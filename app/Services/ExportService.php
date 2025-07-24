<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\ReturnRequest;
use App\Models\ProductStock;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ExportService
{
    /**
     * تصدير الطلبات إلى ملف CSV
     *
     * @param array $filters معايير التصفية للطلبات
     * @param string|null $filename اسم الملف المراد إنشاؤه
     * @return string مسار الملف المصدر
     */
    public function exportOrdersToCsv(array $filters = [], ?string $filename = null): string
    {
        try {
            // إنشاء استعلام الطلبات مع تطبيق المرشحات
            $query = Order::query();
            
            // تطبيق المرشحات
            if (isset($filters['start_date'])) {
                $query->where('created_at', '>=', $filters['start_date']);
            }
            
            if (isset($filters['end_date'])) {
                $query->where('created_at', '<=', $filters['end_date']);
            }
            
            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }
            
            if (isset($filters['customer_id'])) {
                $query->where('customer_id', $filters['customer_id']);
            }
            
            // الحصول على الطلبات
            $orders = $query->with(['customer', 'items.product'])->get();
            
            // إنشاء اسم الملف إذا لم يتم تحديده
            if (!$filename) {
                $filename = 'orders_export_' . date('Y-m-d_His') . '.csv';
            }
            
            // إنشاء مسار الملف
            $filePath = 'exports/' . $filename;
            $fullPath = Storage::path($filePath);
            
            // التأكد من وجود المجلد
            $directory = dirname($fullPath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // فتح الملف للكتابة
            $file = fopen($fullPath, 'w');
            
            // كتابة رأس الملف
            fputcsv($file, [
                'Order ID',
                'Order Number',
                'Customer',
                'Email',
                'Date',
                'Status',
                'Total',
                'Payment Method',
                'Shipping Method'
            ]);
            
            // كتابة بيانات الطلبات
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->order_number,
                    $order->customer ? $order->customer->name : 'N/A',
                    $order->customer ? $order->customer->email : 'N/A',
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->status,
                    $order->total,
                    $order->payment_method,
                    $order->shipping_method
                ]);
            }
            
            // إغلاق الملف
            fclose($file);
            
            // إرجاع مسار الملف
            return $filePath;
        } catch (\Exception $e) {
            Log::error('Error exporting orders to CSV: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * تصدير المنتجات إلى ملف CSV
     *
     * @param array $filters معايير التصفية للمنتجات
     * @param string|null $filename اسم الملف المراد إنشاؤه
     * @return string مسار الملف المصدر
     */
    public function exportProductsToCsv(array $filters = [], ?string $filename = null): string
    {
        try {
            // إنشاء استعلام المنتجات مع تطبيق المرشحات
            $query = Product::query();
            
            // تطبيق المرشحات
            if (isset($filters['category_id'])) {
                $query->where('category_id', $filters['category_id']);
            }
            
            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }
            
            // الحصول على المنتجات
            $products = $query->with(['category', 'stocks'])->get();
            
            // إنشاء اسم الملف إذا لم يتم تحديده
            if (!$filename) {
                $filename = 'products_export_' . date('Y-m-d_His') . '.csv';
            }
            
            // إنشاء مسار الملف
            $filePath = 'exports/' . $filename;
            $fullPath = Storage::path($filePath);
            
            // التأكد من وجود المجلد
            $directory = dirname($fullPath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // فتح الملف للكتابة
            $file = fopen($fullPath, 'w');
            
            // كتابة رأس الملف
            fputcsv($file, [
                'Product ID',
                'SKU',
                'Name',
                'Category',
                'Price',
                'Cost Price',
                'Stock Quantity',
                'Status',
                'Created At'
            ]);
            
            // كتابة بيانات المنتجات
            foreach ($products as $product) {
                // حساب إجمالي المخزون من جميع المستودعات
                $totalStock = $product->stocks->sum('quantity');
                
                fputcsv($file, [
                    $product->id,
                    $product->sku,
                    $product->name,
                    $product->category ? $product->category->name : 'N/A',
                    $product->price,
                    $product->cost_price,
                    $totalStock,
                    $product->status,
                    $product->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            // إغلاق الملف
            fclose($file);
            
            // إرجاع مسار الملف
            return $filePath;
        } catch (\Exception $e) {
            Log::error('Error exporting products to CSV: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * تصدير العملاء إلى ملف CSV
     *
     * @param array $filters معايير التصفية للعملاء
     * @param string|null $filename اسم الملف المراد إنشاؤه
     * @return string مسار الملف المصدر
     */
    public function exportCustomersToCsv(array $filters = [], ?string $filename = null): string
    {
        try {
            // إنشاء استعلام العملاء مع تطبيق المرشحات
            $query = Customer::query();
            
            // تطبيق المرشحات
            if (isset($filters['country'])) {
                $query->where('country', $filters['country']);
            }
            
            if (isset($filters['registration_date_start'])) {
                $query->where('created_at', '>=', $filters['registration_date_start']);
            }
            
            if (isset($filters['registration_date_end'])) {
                $query->where('created_at', '<=', $filters['registration_date_end']);
            }
            
            // الحصول على العملاء
            $customers = $query->withCount('orders')->get();
            
            // إنشاء اسم الملف إذا لم يتم تحديده
            if (!$filename) {
                $filename = 'customers_export_' . date('Y-m-d_His') . '.csv';
            }
            
            // إنشاء مسار الملف
            $filePath = 'exports/' . $filename;
            $fullPath = Storage::path($filePath);
            
            // التأكد من وجود المجلد
            $directory = dirname($fullPath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // فتح الملف للكتابة
            $file = fopen($fullPath, 'w');
            
            // كتابة رأس الملف
            fputcsv($file, [
                'Customer ID',
                'Name',
                'Email',
                'Phone',
                'Country',
                'City',
                'Total Orders',
                'Registration Date'
            ]);
            
            // كتابة بيانات العملاء
            foreach ($customers as $customer) {
                fputcsv($file, [
                    $customer->id,
                    $customer->name,
                    $customer->email,
                    $customer->phone,
                    $customer->country,
                    $customer->city,
                    $customer->orders_count,
                    $customer->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            // إغلاق الملف
            fclose($file);
            
            // إرجاع مسار الملف
            return $filePath;
        } catch (\Exception $e) {
            Log::error('Error exporting customers to CSV: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * تصدير طلبات الإرجاع إلى ملف CSV
     *
     * @param array $filters معايير التصفية لطلبات الإرجاع
     * @param string|null $filename اسم الملف المراد إنشاؤه
     * @return string مسار الملف المصدر
     */
    public function exportReturnRequestsToCsv(array $filters = [], ?string $filename = null): string
    {
        try {
            // إنشاء استعلام طلبات الإرجاع مع تطبيق المرشحات
            $query = ReturnRequest::query();
            
            // تطبيق المرشحات
            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }
            
            if (isset($filters['start_date'])) {
                $query->where('created_at', '>=', $filters['start_date']);
            }
            
            if (isset($filters['end_date'])) {
                $query->where('created_at', '<=', $filters['end_date']);
            }
            
            // الحصول على طلبات الإرجاع
            $returns = $query->with(['order', 'customer', 'items.product'])->get();
            
            // إنشاء اسم الملف إذا لم يتم تحديده
            if (!$filename) {
                $filename = 'returns_export_' . date('Y-m-d_His') . '.csv';
            }
            
            // إنشاء مسار الملف
            $filePath = 'exports/' . $filename;
            $fullPath = Storage::path($filePath);
            
            // التأكد من وجود المجلد
            $directory = dirname($fullPath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // فتح الملف للكتابة
            $file = fopen($fullPath, 'w');
            
            // كتابة رأس الملف
            fputcsv($file, [
                'Return ID',
                'Return Number',
                'Order Number',
                'Customer',
                'Status',
                'Total Amount',
                'Return Method',
                'Created Date',
                'Processed Date'
            ]);
            
            // كتابة بيانات طلبات الإرجاع
            foreach ($returns as $return) {
                fputcsv($file, [
                    $return->id,
                    $return->return_number,
                    $return->order ? $return->order->order_number : 'N/A',
                    $return->customer ? $return->customer->name : 'N/A',
                    $return->status,
                    $return->total_amount,
                    $return->return_method,
                    $return->created_at->format('Y-m-d H:i:s'),
                    $return->processed_at ? $return->processed_at->format('Y-m-d H:i:s') : 'N/A'
                ]);
            }
            
            // إغلاق الملف
            fclose($file);
            
            // إرجاع مسار الملف
            return $filePath;
        } catch (\Exception $e) {
            Log::error('Error exporting return requests to CSV: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * تصدير تقرير المخزون إلى ملف CSV
     *
     * @param array $filters معايير التصفية للمخزون
     * @param string|null $filename اسم الملف المراد إنشاؤه
     * @return string مسار الملف المصدر
     */
    public function exportInventoryReportToCsv(array $filters = [], ?string $filename = null): string
    {
        try {
            // إنشاء استعلام المخزون مع تطبيق المرشحات
            $query = ProductStock::query();
            
            // تطبيق المرشحات
            if (isset($filters['warehouse_id'])) {
                $query->where('warehouse_id', $filters['warehouse_id']);
            }
            
            if (isset($filters['low_stock_only']) && $filters['low_stock_only']) {
                $query->whereRaw('quantity <= low_stock_threshold');
            }
            
            // الحصول على بيانات المخزون
            $stocks = $query->with(['product', 'warehouse'])->get();
            
            // إنشاء اسم الملف إذا لم يتم تحديده
            if (!$filename) {
                $filename = 'inventory_report_' . date('Y-m-d_His') . '.csv';
            }
            
            // إنشاء مسار الملف
            $filePath = 'exports/' . $filename;
            $fullPath = Storage::path($filePath);
            
            // التأكد من وجود المجلد
            $directory = dirname($fullPath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // فتح الملف للكتابة
            $file = fopen($fullPath, 'w');
            
            // كتابة رأس الملف
            fputcsv($file, [
                'Product ID',
                'SKU',
                'Product Name',
                'Warehouse',
                'Quantity',
                'Reserved Quantity',
                'Available Quantity',
                'Low Stock Threshold',
                'Status'
            ]);
            
            // كتابة بيانات المخزون
            foreach ($stocks as $stock) {
                $availableQuantity = $stock->quantity - $stock->reserved_quantity;
                $status = $availableQuantity <= $stock->low_stock_threshold ? 'Low Stock' : 'In Stock';
                
                fputcsv($file, [
                    $stock->product_id,
                    $stock->product ? $stock->product->sku : 'N/A',
                    $stock->product ? $stock->product->name : 'N/A',
                    $stock->warehouse ? $stock->warehouse->name : 'N/A',
                    $stock->quantity,
                    $stock->reserved_quantity,
                    $availableQuantity,
                    $stock->low_stock_threshold,
                    $status
                ]);
            }
            
            // إغلاق الملف
            fclose($file);
            
            // إرجاع مسار الملف
            return $filePath;
        } catch (\Exception $e) {
            Log::error('Error exporting inventory report to CSV: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * تصدير البيانات إلى ملف PDF
     *
     * @param string $view اسم ملف العرض
     * @param array $data البيانات المراد تصديرها
     * @param string|null $filename اسم الملف المراد إنشاؤه
     * @return string مسار الملف المصدر
     */
    public function exportToPdf(string $view, array $data, ?string $filename = null): string
    {
        try {
            // إنشاء اسم الملف إذا لم يتم تحديده
            if (!$filename) {
                $filename = 'export_' . date('Y-m-d_His') . '.pdf';
            }
            
            // إنشاء مسار الملف
            $filePath = 'exports/' . $filename;
            
            // إنشاء ملف PDF باستخدام مكتبة dompdf
            $pdf = \PDF::loadView($view, $data);
            
            // حفظ الملف
            Storage::put($filePath, $pdf->output());
            
            // إرجاع مسار الملف
            return $filePath;
        } catch (\Exception $e) {
            Log::error('Error exporting to PDF: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * تصدير البيانات إلى ملف Excel
     *
     * @param string $exportClass اسم فئة التصدير
     * @param array $data البيانات المراد تصديرها
     * @param string|null $filename اسم الملف المراد إنشاؤه
     * @return string مسار الملف المصدر
     */
    public function exportToExcel(string $exportClass, array $data, ?string $filename = null): string
    {
        try {
            // إنشاء اسم الملف إذا لم يتم تحديده
            if (!$filename) {
                $filename = 'export_' . date('Y-m-d_His') . '.xlsx';
            }
            
            // إنشاء مسار الملف
            $filePath = 'exports/' . $filename;
            
            // إنشاء كائن التصدير
            $export = new $exportClass($data);
            
            // تصدير البيانات إلى ملف Excel
            \Excel::store($export, $filePath);
            
            // إرجاع مسار الملف
            return $filePath;
        } catch (\Exception $e) {
            Log::error('Error exporting to Excel: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * الحصول على رابط تنزيل الملف المصدر
     *
     * @param string $filePath مسار الملف المصدر
     * @param int $expiresInMinutes مدة صلاحية الرابط بالدقائق
     * @return string رابط التنزيل
     */
    public function getDownloadLink(string $filePath, int $expiresInMinutes = 60): string
    {
        try {
            // التحقق من وجود الملف
            if (!Storage::exists($filePath)) {
                throw new \Exception('Export file not found: ' . $filePath);
            }
            
            // إنشاء رابط تنزيل مؤقت
            $url = Storage::temporaryUrl(
                $filePath,
                now()->addMinutes($expiresInMinutes)
            );
            
            return $url;
        } catch (\Exception $e) {
            Log::error('Error generating download link: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * حذف ملف مصدر
     *
     * @param string $filePath مسار الملف المصدر
     * @return bool نتيجة العملية
     */
    public function deleteExportFile(string $filePath): bool
    {
        try {
            // التحقق من وجود الملف
            if (!Storage::exists($filePath)) {
                return false;
            }
            
            // حذف الملف
            return Storage::delete($filePath);
        } catch (\Exception $e) {
            Log::error('Error deleting export file: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * تنظيف ملفات التصدير القديمة
     *
     * @param int $olderThanDays عدد الأيام للملفات القديمة
     * @return int عدد الملفات التي تم حذفها
     */
    public function cleanupOldExportFiles(int $olderThanDays = 7): int
    {
        try {
            $deletedCount = 0;
            $cutoffDate = now()->subDays($olderThanDays);
            
            // الحصول على قائمة ملفات التصدير
            $files = Storage::files('exports');
            
            foreach ($files as $file) {
                // الحصول على تاريخ آخر تعديل للملف
                $lastModified = Storage::lastModified($file);
                
                // التحقق مما إذا كان الملف أقدم من التاريخ المحدد
                if ($lastModified < $cutoffDate->timestamp) {
                    // حذف الملف
                    if (Storage::delete($file)) {
                        $deletedCount++;
                    }
                }
            }
            
            return $deletedCount;
        } catch (\Exception $e) {
            Log::error('Error cleaning up old export files: ' . $e->getMessage());
            return 0;
        }
    }
}