<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\ProductStock;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ImportService
{
    /**
     * استيراد المنتجات من ملف CSV
     *
     * @param string $filePath مسار ملف CSV
     * @param bool $updateExisting تحديث المنتجات الموجودة إذا كانت القيمة true
     * @param int|null $defaultCategoryId معرف الفئة الافتراضية للمنتجات الجديدة
     * @param int|null $defaultWarehouseId معرف المستودع الافتراضي للمخزون
     * @return array نتائج الاستيراد
     */
    public function importProductsFromCsv(string $filePath, bool $updateExisting = true, ?int $defaultCategoryId = null, ?int $defaultWarehouseId = null): array
    {
        // التحقق من وجود الملف
        if (!Storage::exists($filePath)) {
            throw new \Exception('Import file not found: ' . $filePath);
        }
        
        // فتح الملف للقراءة
        $file = fopen(Storage::path($filePath), 'r');
        
        // قراءة رأس الملف
        $headers = fgetcsv($file);
        
        // تحويل رؤوس الأعمدة إلى أحرف صغيرة وإزالة المسافات
        $headers = array_map(function($header) {
            return strtolower(str_replace(' ', '_', trim($header)));
        }, $headers);
        
        // تهيئة متغيرات النتائج
        $results = [
            'total' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => [],
        ];
        
        // الحصول على المستودع الافتراضي إذا لم يتم تحديده
        if (!$defaultWarehouseId) {
            $defaultWarehouse = Warehouse::where('is_default', true)->first();
            $defaultWarehouseId = $defaultWarehouse ? $defaultWarehouse->id : null;
        }
        
        // قراءة بيانات المنتجات
        DB::beginTransaction();
        
        try {
            while (($row = fgetcsv($file)) !== false) {
                $results['total']++;
                
                // تحويل الصف إلى مصفوفة ترابطية
                $data = [];
                foreach ($headers as $index => $header) {
                    if (isset($row[$index])) {
                        $data[$header] = trim($row[$index]);
                    }
                }
                
                // التحقق من وجود البيانات الأساسية
                if (empty($data['sku']) || empty($data['name'])) {
                    $results['skipped']++;
                    $results['errors'][] = "Row {$results['total']}: Missing required fields (SKU or Name)";
                    continue;
                }
                
                // البحث عن المنتج بواسطة SKU
                $product = Product::where('sku', $data['sku'])->first();
                
                // إذا كان المنتج موجودًا وكان التحديث غير مسموح به
                if ($product && !$updateExisting) {
                    $results['skipped']++;
                    continue;
                }
                
                // تحضير بيانات المنتج
                $productData = [
                    'sku' => $data['sku'],
                    'name' => $data['name'],
                    'description' => $data['description'] ?? null,
                    'price' => $data['price'] ?? 0,
                    'cost_price' => $data['cost_price'] ?? 0,
                    'status' => $data['status'] ?? 'active',
                ];
                
                // التحقق من الفئة
                if (!empty($data['category'])) {
                    // البحث عن الفئة بالاسم أو إنشاء فئة جديدة
                    $category = Category::firstOrCreate(
                        ['name' => $data['category']],
                        ['slug' => Str::slug($data['category'])]
                    );
                    $productData['category_id'] = $category->id;
                } elseif ($defaultCategoryId) {
                    $productData['category_id'] = $defaultCategoryId;
                }
                
                // إنشاء أو تحديث المنتج
                if ($product) {
                    $product->update($productData);
                    $results['updated']++;
                } else {
                    $product = Product::create($productData);
                    $results['created']++;
                }
                
                // تحديث المخزون إذا تم تحديد المستودع الافتراضي وكانت كمية المخزون متوفرة
                if ($defaultWarehouseId && isset($data['stock_quantity'])) {
                    $stockData = [
                        'product_id' => $product->id,
                        'warehouse_id' => $defaultWarehouseId,
                        'quantity' => (int) $data['stock_quantity'],
                        'low_stock_threshold' => $data['low_stock_threshold'] ?? 5,
                    ];
                    
                    // البحث عن سجل المخزون أو إنشاء سجل جديد
                    $stock = ProductStock::updateOrCreate(
                        ['product_id' => $product->id, 'warehouse_id' => $defaultWarehouseId],
                        $stockData
                    );
                }
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error importing products from CSV: ' . $e->getMessage());
            throw $e;
        } finally {
            fclose($file);
        }
        
        return $results;
    }
    
    /**
     * استيراد العملاء من ملف CSV
     *
     * @param string $filePath مسار ملف CSV
     * @param bool $updateExisting تحديث العملاء الموجودين إذا كانت القيمة true
     * @return array نتائج الاستيراد
     */
    public function importCustomersFromCsv(string $filePath, bool $updateExisting = true): array
    {
        // التحقق من وجود الملف
        if (!Storage::exists($filePath)) {
            throw new \Exception('Import file not found: ' . $filePath);
        }
        
        // فتح الملف للقراءة
        $file = fopen(Storage::path($filePath), 'r');
        
        // قراءة رأس الملف
        $headers = fgetcsv($file);
        
        // تحويل رؤوس الأعمدة إلى أحرف صغيرة وإزالة المسافات
        $headers = array_map(function($header) {
            return strtolower(str_replace(' ', '_', trim($header)));
        }, $headers);
        
        // تهيئة متغيرات النتائج
        $results = [
            'total' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => [],
        ];
        
        // قراءة بيانات العملاء
        DB::beginTransaction();
        
        try {
            while (($row = fgetcsv($file)) !== false) {
                $results['total']++;
                
                // تحويل الصف إلى مصفوفة ترابطية
                $data = [];
                foreach ($headers as $index => $header) {
                    if (isset($row[$index])) {
                        $data[$header] = trim($row[$index]);
                    }
                }
                
                // التحقق من وجود البيانات الأساسية
                if (empty($data['email']) || empty($data['name'])) {
                    $results['skipped']++;
                    $results['errors'][] = "Row {$results['total']}: Missing required fields (Email or Name)";
                    continue;
                }
                
                // التحقق من صحة البريد الإلكتروني
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $results['skipped']++;
                    $results['errors'][] = "Row {$results['total']}: Invalid email format";
                    continue;
                }
                
                // البحث عن العميل بواسطة البريد الإلكتروني
                $customer = Customer::where('email', $data['email'])->first();
                
                // إذا كان العميل موجودًا وكان التحديث غير مسموح به
                if ($customer && !$updateExisting) {
                    $results['skipped']++;
                    continue;
                }
                
                // تحضير بيانات العميل
                $customerData = [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'country' => $data['country'] ?? null,
                    'city' => $data['city'] ?? null,
                    'address' => $data['address'] ?? null,
                    'postal_code' => $data['postal_code'] ?? null,
                ];
                
                // إنشاء أو تحديث العميل
                if ($customer) {
                    $customer->update($customerData);
                    $results['updated']++;
                } else {
                    // إنشاء كلمة مرور عشوائية للعميل الجديد
                    $customerData['password'] = bcrypt(Str::random(10));
                    $customer = Customer::create($customerData);
                    $results['created']++;
                }
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error importing customers from CSV: ' . $e->getMessage());
            throw $e;
        } finally {
            fclose($file);
        }
        
        return $results;
    }
    
    /**
     * استيراد المخزون من ملف CSV
     *
     * @param string $filePath مسار ملف CSV
     * @param int|null $warehouseId معرف المستودع
     * @return array نتائج الاستيراد
     */
    public function importInventoryFromCsv(string $filePath, ?int $warehouseId = null): array
    {
        // التحقق من وجود الملف
        if (!Storage::exists($filePath)) {
            throw new \Exception('Import file not found: ' . $filePath);
        }
        
        // فتح الملف للقراءة
        $file = fopen(Storage::path($filePath), 'r');
        
        // قراءة رأس الملف
        $headers = fgetcsv($file);
        
        // تحويل رؤوس الأعمدة إلى أحرف صغيرة وإزالة المسافات
        $headers = array_map(function($header) {
            return strtolower(str_replace(' ', '_', trim($header)));
        }, $headers);
        
        // تهيئة متغيرات النتائج
        $results = [
            'total' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => [],
        ];
        
        // الحصول على المستودع إذا لم يتم تحديده
        if (!$warehouseId) {
            $defaultWarehouse = Warehouse::where('is_default', true)->first();
            if (!$defaultWarehouse) {
                throw new \Exception('No default warehouse found. Please specify a warehouse ID.');
            }
            $warehouseId = $defaultWarehouse->id;
        }
        
        // الحصول على خدمة المخزون
        $inventoryService = app(InventoryService::class);
        
        // قراءة بيانات المخزون
        DB::beginTransaction();
        
        try {
            while (($row = fgetcsv($file)) !== false) {
                $results['total']++;
                
                // تحويل الصف إلى مصفوفة ترابطية
                $data = [];
                foreach ($headers as $index => $header) {
                    if (isset($row[$index])) {
                        $data[$header] = trim($row[$index]);
                    }
                }
                
                // التحقق من وجود البيانات الأساسية
                if (empty($data['sku']) || !isset($data['quantity'])) {
                    $results['skipped']++;
                    $results['errors'][] = "Row {$results['total']}: Missing required fields (SKU or Quantity)";
                    continue;
                }
                
                // البحث عن المنتج بواسطة SKU
                $product = Product::where('sku', $data['sku'])->first();
                
                if (!$product) {
                    $results['skipped']++;
                    $results['errors'][] = "Row {$results['total']}: Product with SKU '{$data['sku']}' not found";
                    continue;
                }
                
                // تحديث المخزون
                $quantity = (int) $data['quantity'];
                $lowStockThreshold = isset($data['low_stock_threshold']) ? (int) $data['low_stock_threshold'] : 5;
                
                // تحديد نوع العملية
                $operation = isset($data['operation']) ? strtolower($data['operation']) : 'set';
                $reason = $data['reason'] ?? 'Inventory import';
                
                // تنفيذ عملية تحديث المخزون
                $inventoryService->updateStock(
                    $product->id,
                    $warehouseId,
                    $quantity,
                    $operation,
                    $reason
                );
                
                // تحديث عتبة المخزون المنخفض إذا تم تحديدها
                if (isset($data['low_stock_threshold'])) {
                    $stock = ProductStock::where('product_id', $product->id)
                        ->where('warehouse_id', $warehouseId)
                        ->first();
                    
                    if ($stock) {
                        $stock->update(['low_stock_threshold' => $lowStockThreshold]);
                    }
                }
                
                $results['updated']++;
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error importing inventory from CSV: ' . $e->getMessage());
            throw $e;
        } finally {
            fclose($file);
        }
        
        return $results;
    }
    
    /**
     * استيراد الفئات من ملف CSV
     *
     * @param string $filePath مسار ملف CSV
     * @param bool $updateExisting تحديث الفئات الموجودة إذا كانت القيمة true
     * @return array نتائج الاستيراد
     */
    public function importCategoriesFromCsv(string $filePath, bool $updateExisting = true): array
    {
        // التحقق من وجود الملف
        if (!Storage::exists($filePath)) {
            throw new \Exception('Import file not found: ' . $filePath);
        }
        
        // فتح الملف للقراءة
        $file = fopen(Storage::path($filePath), 'r');
        
        // قراءة رأس الملف
        $headers = fgetcsv($file);
        
        // تحويل رؤوس الأعمدة إلى أحرف صغيرة وإزالة المسافات
        $headers = array_map(function($header) {
            return strtolower(str_replace(' ', '_', trim($header)));
        }, $headers);
        
        // تهيئة متغيرات النتائج
        $results = [
            'total' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => [],
        ];
        
        // قراءة بيانات الفئات
        DB::beginTransaction();
        
        try {
            while (($row = fgetcsv($file)) !== false) {
                $results['total']++;
                
                // تحويل الصف إلى مصفوفة ترابطية
                $data = [];
                foreach ($headers as $index => $header) {
                    if (isset($row[$index])) {
                        $data[$header] = trim($row[$index]);
                    }
                }
                
                // التحقق من وجود البيانات الأساسية
                if (empty($data['name'])) {
                    $results['skipped']++;
                    $results['errors'][] = "Row {$results['total']}: Missing required field (Name)";
                    continue;
                }
                
                // البحث عن الفئة بواسطة الاسم
                $category = Category::where('name', $data['name'])->first();
                
                // إذا كانت الفئة موجودة وكان التحديث غير مسموح به
                if ($category && !$updateExisting) {
                    $results['skipped']++;
                    continue;
                }
                
                // تحضير بيانات الفئة
                $categoryData = [
                    'name' => $data['name'],
                    'description' => $data['description'] ?? null,
                    'slug' => $data['slug'] ?? Str::slug($data['name']),
                    'status' => $data['status'] ?? 'active',
                ];
                
                // التحقق من الفئة الأب
                if (!empty($data['parent_name'])) {
                    // البحث عن الفئة الأب بالاسم
                    $parentCategory = Category::where('name', $data['parent_name'])->first();
                    
                    if ($parentCategory) {
                        $categoryData['parent_id'] = $parentCategory->id;
                    } else {
                        // إنشاء الفئة الأب إذا لم تكن موجودة
                        $newParent = Category::create([
                            'name' => $data['parent_name'],
                            'slug' => Str::slug($data['parent_name']),
                            'status' => 'active',
                        ]);
                        $categoryData['parent_id'] = $newParent->id;
                    }
                }
                
                // إنشاء أو تحديث الفئة
                if ($category) {
                    $category->update($categoryData);
                    $results['updated']++;
                } else {
                    Category::create($categoryData);
                    $results['created']++;
                }
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error importing categories from CSV: ' . $e->getMessage());
            throw $e;
        } finally {
            fclose($file);
        }
        
        return $results;
    }
    
    /**
     * التحقق من صحة ملف الاستيراد
     *
     * @param string $filePath مسار الملف
     * @param array $requiredHeaders الرؤوس المطلوبة
     * @return array نتائج التحقق
     */
    public function validateImportFile(string $filePath, array $requiredHeaders): array
    {
        // التحقق من وجود الملف
        if (!Storage::exists($filePath)) {
            return [
                'valid' => false,
                'errors' => ['Import file not found: ' . $filePath],
            ];
        }
        
        // فتح الملف للقراءة
        $file = fopen(Storage::path($filePath), 'r');
        
        // قراءة رأس الملف
        $headers = fgetcsv($file);
        
        // تحويل رؤوس الأعمدة إلى أحرف صغيرة وإزالة المسافات
        $headers = array_map(function($header) {
            return strtolower(str_replace(' ', '_', trim($header)));
        }, $headers);
        
        // التحقق من وجود الرؤوس المطلوبة
        $missingHeaders = [];
        foreach ($requiredHeaders as $requiredHeader) {
            if (!in_array(strtolower($requiredHeader), $headers)) {
                $missingHeaders[] = $requiredHeader;
            }
        }
        
        // إغلاق الملف
        fclose($file);
        
        // إرجاع نتائج التحقق
        if (count($missingHeaders) > 0) {
            return [
                'valid' => false,
                'errors' => ['Missing required headers: ' . implode(', ', $missingHeaders)],
            ];
        }
        
        return [
            'valid' => true,
            'headers' => $headers,
        ];
    }
    
    /**
     * الحصول على نموذج ملف CSV للاستيراد
     *
     * @param string $type نوع النموذج (products, customers, inventory, categories)
     * @return string مسار ملف النموذج
     */
    public function getImportTemplate(string $type): string
    {
        // تحديد رؤوس الملف حسب النوع
        $headers = [];
        $filename = '';
        
        switch ($type) {
            case 'products':
                $headers = [
                    'SKU', 'Name', 'Description', 'Price', 'Cost Price',
                    'Category', 'Stock Quantity', 'Low Stock Threshold', 'Status'
                ];
                $filename = 'product_import_template.csv';
                break;
                
            case 'customers':
                $headers = [
                    'Name', 'Email', 'Phone', 'Country', 'City',
                    'Address', 'Postal Code'
                ];
                $filename = 'customer_import_template.csv';
                break;
                
            case 'inventory':
                $headers = [
                    'SKU', 'Quantity', 'Operation', 'Low Stock Threshold', 'Reason'
                ];
                $filename = 'inventory_import_template.csv';
                break;
                
            case 'categories':
                $headers = [
                    'Name', 'Description', 'Slug', 'Parent Name', 'Status'
                ];
                $filename = 'category_import_template.csv';
                break;
                
            default:
                throw new \Exception('Invalid template type: ' . $type);
        }
        
        // إنشاء مسار الملف
        $filePath = 'templates/' . $filename;
        $fullPath = Storage::path($filePath);
        
        // التأكد من وجود المجلد
        $directory = dirname($fullPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // فتح الملف للكتابة
        $file = fopen($fullPath, 'w');
        
        // كتابة رأس الملف
        fputcsv($file, $headers);
        
        // إضافة صف مثال حسب النوع
        switch ($type) {
            case 'products':
                fputcsv($file, [
                    'PRD001', 'Sample Product', 'This is a sample product description',
                    '99.99', '50.00', 'Electronics', '100', '10', 'active'
                ]);
                break;
                
            case 'customers':
                fputcsv($file, [
                    'John Doe', 'john.doe@example.com', '+1234567890',
                    'United States', 'New York', '123 Main St', '10001'
                ]);
                break;
                
            case 'inventory':
                fputcsv($file, [
                    'PRD001', '50', 'add', '10', 'Stock adjustment'
                ]);
                break;
                
            case 'categories':
                fputcsv($file, [
                    'Electronics', 'Electronic products', 'electronics',
                    'Products', 'active'
                ]);
                break;
        }
        
        // إغلاق الملف
        fclose($file);
        
        // إرجاع مسار الملف
        return $filePath;
    }
    
    /**
     * معالجة ملف مرفوع للاستيراد
     *
     * @param \Illuminate\Http\UploadedFile $file الملف المرفوع
     * @return string مسار الملف المخزن
     */
    public function processUploadedFile($file): string
    {
        // التحقق من نوع الملف
        $extension = $file->getClientOriginalExtension();
        if (!in_array(strtolower($extension), ['csv', 'xlsx', 'xls'])) {
            throw new \Exception('Invalid file type. Only CSV and Excel files are allowed.');
        }
        
        // إنشاء اسم الملف
        $filename = 'import_' . date('Y-m-d_His') . '.' . $extension;
        
        // تخزين الملف
        $path = $file->storeAs('imports', $filename);
        
        return $path;
    }
    
    /**
     * تحويل ملف Excel إلى CSV
     *
     * @param string $excelFilePath مسار ملف Excel
     * @return string مسار ملف CSV
     */
    public function convertExcelToCsv(string $excelFilePath): string
    {
        // التحقق من وجود الملف
        if (!Storage::exists($excelFilePath)) {
            throw new \Exception('Excel file not found: ' . $excelFilePath);
        }
        
        // إنشاء اسم ملف CSV
        $csvFilename = pathinfo($excelFilePath, PATHINFO_FILENAME) . '.csv';
        $csvFilePath = 'imports/' . $csvFilename;
        
        // قراءة ملف Excel وتحويله إلى CSV
        $reader = \Excel::toArray([], Storage::path($excelFilePath));
        
        // الحصول على البيانات من الورقة الأولى
        $data = $reader[0] ?? [];
        
        if (empty($data)) {
            throw new \Exception('Excel file is empty or could not be read.');
        }
        
        // فتح ملف CSV للكتابة
        $file = fopen(Storage::path($csvFilePath), 'w');
        
        // كتابة البيانات إلى ملف CSV
        foreach ($data as $row) {
            fputcsv($file, $row);
        }
        
        // إغلاق الملف
        fclose($file);
        
        return $csvFilePath;
    }
}