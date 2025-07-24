<?php

namespace App\Services;

use App\Models\Tax;
use App\Models\TaxRate;
use App\Models\TaxClass;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TaxService
{
    /**
     * خدمة التخزين المؤقت
     *
     * @var \App\Services\CacheService
     */
    protected $cacheService;
    
    /**
     * إنشاء مثيل جديد من الخدمة
     *
     * @param \App\Services\CacheService $cacheService
     * @return void
     */
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    
    /**
     * حساب الضريبة لمنتج واحد
     *
     * @param \App\Models\Product $product المنتج
     * @param float $price السعر
     * @param int $quantity الكمية
     * @param string $countryCode رمز الدولة
     * @param string|null $stateCode رمز الولاية/المقاطعة (اختياري)
     * @param bool $includeTax هل السعر يشمل الضريبة بالفعل
     * @return array معلومات الضريبة
     */
    public function calculateProductTax(
        Product $product,
        float $price,
        int $quantity = 1,
        string $countryCode = '',
        ?string $stateCode = null,
        bool $includeTax = false
    ): array {
        // الحصول على فئة الضريبة للمنتج
        $taxClassId = $product->tax_class_id;
        
        // إذا لم يكن للمنتج فئة ضريبة، إرجاع صفر
        if (!$taxClassId) {
            return [
                'tax_amount' => 0,
                'tax_rate' => 0,
                'price_excluding_tax' => $price,
                'price_including_tax' => $price,
                'tax_details' => [],
            ];
        }
        
        // الحصول على معدلات الضريبة المطبقة
        $taxRates = $this->getApplicableTaxRates($taxClassId, $countryCode, $stateCode);
        
        // إذا لم تكن هناك معدلات ضريبة مطبقة، إرجاع صفر
        if ($taxRates->isEmpty()) {
            return [
                'tax_amount' => 0,
                'tax_rate' => 0,
                'price_excluding_tax' => $price,
                'price_including_tax' => $price,
                'tax_details' => [],
            ];
        }
        
        // حساب الضريبة
        $subtotal = $price * $quantity;
        $totalTaxRate = 0;
        $totalTaxAmount = 0;
        $taxDetails = [];
        
        foreach ($taxRates as $taxRate) {
            $rate = $taxRate->rate / 100; // تحويل النسبة المئوية إلى كسر عشري
            $totalTaxRate += $taxRate->rate;
            
            // حساب مبلغ الضريبة بناءً على ما إذا كان السعر يشمل الضريبة بالفعل
            $taxAmount = 0;
            if ($includeTax) {
                // إذا كان السعر يشمل الضريبة، استخراج الضريبة من السعر
                $taxAmount = $subtotal - ($subtotal / (1 + $rate));
            } else {
                // إذا كان السعر لا يشمل الضريبة، إضافة الضريبة إلى السعر
                $taxAmount = $subtotal * $rate;
            }
            
            $totalTaxAmount += $taxAmount;
            
            // إضافة تفاصيل الضريبة
            $taxDetails[] = [
                'name' => $taxRate->name,
                'rate' => $taxRate->rate,
                'amount' => round($taxAmount, 2),
            ];
        }
        
        // حساب الأسعار شاملة وغير شاملة الضريبة
        $priceExcludingTax = $includeTax ? ($price / (1 + ($totalTaxRate / 100))) : $price;
        $priceIncludingTax = $includeTax ? $price : ($price * (1 + ($totalTaxRate / 100)));
        
        return [
            'tax_amount' => round($totalTaxAmount, 2),
            'tax_rate' => $totalTaxRate,
            'price_excluding_tax' => round($priceExcludingTax, 2),
            'price_including_tax' => round($priceIncludingTax, 2),
            'tax_details' => $taxDetails,
        ];
    }
    
    /**
     * حساب الضريبة لسلة التسوق أو الطلب
     *
     * @param array $items عناصر السلة أو الطلب
     * @param string $countryCode رمز الدولة
     * @param string|null $stateCode رمز الولاية/المقاطعة (اختياري)
     * @param bool $includeTax هل الأسعار تشمل الضريبة بالفعل
     * @return array معلومات الضريبة
     */
    public function calculateCartTax(
        array $items,
        string $countryCode = '',
        ?string $stateCode = null,
        bool $includeTax = false
    ): array {
        $totalTaxAmount = 0;
        $taxDetails = [];
        $taxSummary = [];
        
        foreach ($items as $item) {
            // التحقق من وجود المنتج والسعر والكمية
            if (!isset($item['product_id']) || !isset($item['price']) || !isset($item['quantity'])) {
                continue;
            }
            
            // الحصول على المنتج
            $product = Product::find($item['product_id']);
            if (!$product) {
                continue;
            }
            
            // حساب ضريبة المنتج
            $productTax = $this->calculateProductTax(
                $product,
                $item['price'],
                $item['quantity'],
                $countryCode,
                $stateCode,
                $includeTax
            );
            
            $totalTaxAmount += $productTax['tax_amount'];
            
            // تجميع تفاصيل الضريبة حسب اسم الضريبة
            foreach ($productTax['tax_details'] as $taxDetail) {
                $taxName = $taxDetail['name'];
                
                if (!isset($taxSummary[$taxName])) {
                    $taxSummary[$taxName] = [
                        'name' => $taxName,
                        'rate' => $taxDetail['rate'],
                        'amount' => 0,
                    ];
                }
                
                $taxSummary[$taxName]['amount'] += $taxDetail['amount'];
            }
            
            // إضافة تفاصيل ضريبة المنتج
            $taxDetails[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'tax_amount' => $productTax['tax_amount'],
                'tax_details' => $productTax['tax_details'],
            ];
        }
        
        // تحويل مصفوفة التلخيص إلى مصفوفة عددية
        $taxSummaryArray = array_values($taxSummary);
        
        // تقريب المبالغ في ملخص الضريبة
        foreach ($taxSummaryArray as &$summary) {
            $summary['amount'] = round($summary['amount'], 2);
        }
        
        return [
            'total_tax_amount' => round($totalTaxAmount, 2),
            'tax_summary' => $taxSummaryArray,
            'tax_details' => $taxDetails,
        ];
    }
    
    /**
     * حساب الضريبة للطلب
     *
     * @param \App\Models\Order $order الطلب
     * @param bool $recalculate إعادة حساب الضريبة حتى لو كانت محسوبة مسبقًا
     * @return array معلومات الضريبة
     */
    public function calculateOrderTax(Order $order, bool $recalculate = false): array
    {
        // إذا كانت الضريبة محسوبة مسبقًا ولا نحتاج لإعادة الحساب، إرجاع القيمة المحسوبة
        if (!$recalculate && $order->tax_amount > 0) {
            return [
                'total_tax_amount' => $order->tax_amount,
                'tax_summary' => json_decode($order->tax_details, true) ?? [],
                'tax_details' => [],
            ];
        }
        
        // تحويل عناصر الطلب إلى التنسيق المطلوب لحساب الضريبة
        $items = $order->items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'price' => $item->price,
                'quantity' => $item->quantity,
            ];
        })->toArray();
        
        // الحصول على معلومات الدولة والولاية من عنوان الشحن
        $countryCode = $order->shipping_country ?? '';
        $stateCode = $order->shipping_state ?? null;
        
        // حساب الضريبة
        $taxInfo = $this->calculateCartTax($items, $countryCode, $stateCode, false);
        
        // تحديث معلومات الضريبة في الطلب إذا كان مطلوبًا إعادة الحساب
        if ($recalculate) {
            $order->tax_amount = $taxInfo['total_tax_amount'];
            $order->tax_details = json_encode($taxInfo['tax_summary']);
            $order->total = $order->subtotal + $order->shipping_amount + $taxInfo['total_tax_amount'] - $order->discount_amount;
            $order->save();
        }
        
        return $taxInfo;
    }
    
    /**
     * الحصول على معدلات الضريبة المطبقة لفئة ضريبة ودولة وولاية محددة
     *
     * @param int $taxClassId معرف فئة الضريبة
     * @param string $countryCode رمز الدولة
     * @param string|null $stateCode رمز الولاية/المقاطعة (اختياري)
     * @return \Illuminate\Support\Collection مجموعة من معدلات الضريبة
     */
    public function getApplicableTaxRates(int $taxClassId, string $countryCode = '', ?string $stateCode = null): Collection
    {
        // إنشاء مفتاح التخزين المؤقت
        $cacheKey = "tax_rates_{$taxClassId}_{$countryCode}_{$stateCode}";
        
        // محاولة الحصول على معدلات الضريبة من التخزين المؤقت
        return $this->cacheService->remember($cacheKey, 60, function () use ($taxClassId, $countryCode, $stateCode) {
            // البحث عن معدلات الضريبة المطبقة
            $query = TaxRate::where('tax_class_id', $taxClassId)
                ->where('is_active', true);
            
            // تطبيق تصفية الدولة إذا تم تحديدها
            if (!empty($countryCode)) {
                $query->where(function ($q) use ($countryCode) {
                    $q->where('country_code', $countryCode)
                      ->orWhere('country_code', '');
                });
            }
            
            // تطبيق تصفية الولاية إذا تم تحديدها
            if (!empty($stateCode)) {
                $query->where(function ($q) use ($stateCode) {
                    $q->where('state_code', $stateCode)
                      ->orWhere('state_code', '');
                });
            }
            
            // الحصول على معدلات الضريبة وترتيبها حسب الأولوية
            return $query->orderBy('priority', 'asc')->get();
        });
    }
    
    /**
     * إنشاء فئة ضريبة جديدة
     *
     * @param array $data بيانات فئة الضريبة
     * @return \App\Models\TaxClass فئة الضريبة
     */
    public function createTaxClass(array $data): TaxClass
    {
        DB::beginTransaction();
        
        try {
            // إنشاء فئة الضريبة
            $taxClass = TaxClass::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? '',
                'is_active' => $data['is_active'] ?? true,
            ]);
            
            // مسح ذاكرة التخزين المؤقت للضرائب
            $this->clearTaxCache();
            
            DB::commit();
            return $taxClass;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating tax class: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * تحديث فئة ضريبة موجودة
     *
     * @param \App\Models\TaxClass $taxClass فئة الضريبة
     * @param array $data بيانات التحديث
     * @return \App\Models\TaxClass فئة الضريبة المحدثة
     */
    public function updateTaxClass(TaxClass $taxClass, array $data): TaxClass
    {
        DB::beginTransaction();
        
        try {
            // تحديث فئة الضريبة
            $taxClass->update([
                'name' => $data['name'] ?? $taxClass->name,
                'description' => $data['description'] ?? $taxClass->description,
                'is_active' => $data['is_active'] ?? $taxClass->is_active,
            ]);
            
            // مسح ذاكرة التخزين المؤقت للضرائب
            $this->clearTaxCache();
            
            DB::commit();
            return $taxClass;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating tax class: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * إنشاء معدل ضريبة جديد
     *
     * @param array $data بيانات معدل الضريبة
     * @return \App\Models\TaxRate معدل الضريبة
     */
    public function createTaxRate(array $data): TaxRate
    {
        DB::beginTransaction();
        
        try {
            // إنشاء معدل الضريبة
            $taxRate = TaxRate::create([
                'tax_class_id' => $data['tax_class_id'],
                'name' => $data['name'],
                'rate' => $data['rate'],
                'country_code' => $data['country_code'] ?? '',
                'state_code' => $data['state_code'] ?? '',
                'priority' => $data['priority'] ?? 1,
                'is_active' => $data['is_active'] ?? true,
            ]);
            
            // مسح ذاكرة التخزين المؤقت للضرائب
            $this->clearTaxCache();
            
            DB::commit();
            return $taxRate;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating tax rate: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * تحديث معدل ضريبة موجود
     *
     * @param \App\Models\TaxRate $taxRate معدل الضريبة
     * @param array $data بيانات التحديث
     * @return \App\Models\TaxRate معدل الضريبة المحدث
     */
    public function updateTaxRate(TaxRate $taxRate, array $data): TaxRate
    {
        DB::beginTransaction();
        
        try {
            // تحديث معدل الضريبة
            $taxRate->update([
                'tax_class_id' => $data['tax_class_id'] ?? $taxRate->tax_class_id,
                'name' => $data['name'] ?? $taxRate->name,
                'rate' => $data['rate'] ?? $taxRate->rate,
                'country_code' => $data['country_code'] ?? $taxRate->country_code,
                'state_code' => $data['state_code'] ?? $taxRate->state_code,
                'priority' => $data['priority'] ?? $taxRate->priority,
                'is_active' => $data['is_active'] ?? $taxRate->is_active,
            ]);
            
            // مسح ذاكرة التخزين المؤقت للضرائب
            $this->clearTaxCache();
            
            DB::commit();
            return $taxRate;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating tax rate: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * حذف فئة ضريبة
     *
     * @param \App\Models\TaxClass $taxClass فئة الضريبة
     * @return bool نجاح العملية
     */
    public function deleteTaxClass(TaxClass $taxClass): bool
    {
        DB::beginTransaction();
        
        try {
            // التحقق من عدم استخدام فئة الضريبة في أي منتجات
            $productsCount = Product::where('tax_class_id', $taxClass->id)->count();
            if ($productsCount > 0) {
                throw new \Exception("Cannot delete tax class. It is used by {$productsCount} products.");
            }
            
            // حذف معدلات الضريبة المرتبطة بفئة الضريبة
            TaxRate::where('tax_class_id', $taxClass->id)->delete();
            
            // حذف فئة الضريبة
            $taxClass->delete();
            
            // مسح ذاكرة التخزين المؤقت للضرائب
            $this->clearTaxCache();
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting tax class: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * حذف معدل ضريبة
     *
     * @param \App\Models\TaxRate $taxRate معدل الضريبة
     * @return bool نجاح العملية
     */
    public function deleteTaxRate(TaxRate $taxRate): bool
    {
        DB::beginTransaction();
        
        try {
            // حذف معدل الضريبة
            $taxRate->delete();
            
            // مسح ذاكرة التخزين المؤقت للضرائب
            $this->clearTaxCache();
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting tax rate: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * الحصول على جميع فئات الضريبة
     *
     * @param bool $activeOnly الحصول على الفئات النشطة فقط
     * @return \Illuminate\Support\Collection مجموعة من فئات الضريبة
     */
    public function getAllTaxClasses(bool $activeOnly = false): Collection
    {
        // إنشاء مفتاح التخزين المؤقت
        $cacheKey = 'tax_classes_' . ($activeOnly ? 'active' : 'all');
        
        // محاولة الحصول على فئات الضريبة من التخزين المؤقت
        return $this->cacheService->remember($cacheKey, 60, function () use ($activeOnly) {
            $query = TaxClass::query();
            
            if ($activeOnly) {
                $query->where('is_active', true);
            }
            
            return $query->orderBy('name', 'asc')->get();
        });
    }
    
    /**
     * الحصول على جميع معدلات الضريبة لفئة ضريبة محددة
     *
     * @param int $taxClassId معرف فئة الضريبة
     * @param bool $activeOnly الحصول على المعدلات النشطة فقط
     * @return \Illuminate\Support\Collection مجموعة من معدلات الضريبة
     */
    public function getTaxRatesForClass(int $taxClassId, bool $activeOnly = false): Collection
    {
        // إنشاء مفتاح التخزين المؤقت
        $cacheKey = "tax_rates_for_class_{$taxClassId}_" . ($activeOnly ? 'active' : 'all');
        
        // محاولة الحصول على معدلات الضريبة من التخزين المؤقت
        return $this->cacheService->remember($cacheKey, 60, function () use ($taxClassId, $activeOnly) {
            $query = TaxRate::where('tax_class_id', $taxClassId);
            
            if ($activeOnly) {
                $query->where('is_active', true);
            }
            
            return $query->orderBy('priority', 'asc')->get();
        });
    }
    
    /**
     * الحصول على معدلات الضريبة حسب الدولة
     *
     * @param string $countryCode رمز الدولة
     * @param bool $activeOnly الحصول على المعدلات النشطة فقط
     * @return \Illuminate\Support\Collection مجموعة من معدلات الضريبة
     */
    public function getTaxRatesByCountry(string $countryCode, bool $activeOnly = false): Collection
    {
        // إنشاء مفتاح التخزين المؤقت
        $cacheKey = "tax_rates_country_{$countryCode}_" . ($activeOnly ? 'active' : 'all');
        
        // محاولة الحصول على معدلات الضريبة من التخزين المؤقت
        return $this->cacheService->remember($cacheKey, 60, function () use ($countryCode, $activeOnly) {
            $query = TaxRate::where('country_code', $countryCode);
            
            if ($activeOnly) {
                $query->where('is_active', true);
            }
            
            return $query->orderBy('tax_class_id', 'asc')
                ->orderBy('priority', 'asc')
                ->get();
        });
    }
    
    /**
     * تحديث حالة فئة ضريبة (تفعيل/تعطيل)
     *
     * @param \App\Models\TaxClass $taxClass فئة الضريبة
     * @param bool $isActive الحالة الجديدة
     * @return \App\Models\TaxClass فئة الضريبة المحدثة
     */
    public function updateTaxClassStatus(TaxClass $taxClass, bool $isActive): TaxClass
    {
        $taxClass->is_active = $isActive;
        $taxClass->save();
        
        // مسح ذاكرة التخزين المؤقت للضرائب
        $this->clearTaxCache();
        
        return $taxClass;
    }
    
    /**
     * تحديث حالة معدل ضريبة (تفعيل/تعطيل)
     *
     * @param \App\Models\TaxRate $taxRate معدل الضريبة
     * @param bool $isActive الحالة الجديدة
     * @return \App\Models\TaxRate معدل الضريبة المحدث
     */
    public function updateTaxRateStatus(TaxRate $taxRate, bool $isActive): TaxRate
    {
        $taxRate->is_active = $isActive;
        $taxRate->save();
        
        // مسح ذاكرة التخزين المؤقت للضرائب
        $this->clearTaxCache();
        
        return $taxRate;
    }
    
    /**
     * مسح ذاكرة التخزين المؤقت للضرائب
     *
     * @return void
     */
    public function clearTaxCache(): void
    {
        // مسح جميع مفاتيح التخزين المؤقت المتعلقة بالضرائب
        $this->cacheService->forgetByPattern('tax_*');
    }
    
    /**
     * الحصول على إحصائيات الضرائب
     *
     * @param string $startDate تاريخ البداية (Y-m-d)
     * @param string $endDate تاريخ النهاية (Y-m-d)
     * @return array إحصائيات الضرائب
     */
    public function getTaxStatistics(string $startDate, string $endDate): array
    {
        // إنشاء مفتاح التخزين المؤقت
        $cacheKey = "tax_statistics_{$startDate}_{$endDate}";
        
        // محاولة الحصول على الإحصائيات من التخزين المؤقت
        return $this->cacheService->remember($cacheKey, 60, function () use ($startDate, $endDate) {
            // الحصول على إجمالي الضرائب المحصلة في الفترة المحددة
            $totalTaxCollected = Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', '!=', Order::STATUS_CANCELLED)
                ->sum('tax_amount');
            
            // الحصول على عدد الطلبات التي تم تحصيل ضرائب منها
            $ordersWithTax = Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', '!=', Order::STATUS_CANCELLED)
                ->where('tax_amount', '>', 0)
                ->count();
            
            // الحصول على متوسط الضريبة لكل طلب
            $averageTaxPerOrder = $ordersWithTax > 0 ? $totalTaxCollected / $ordersWithTax : 0;
            
            // الحصول على تفاصيل الضرائب حسب الدولة
            $taxByCountry = Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', '!=', Order::STATUS_CANCELLED)
                ->where('tax_amount', '>', 0)
                ->select('shipping_country', DB::raw('SUM(tax_amount) as total_tax'))
                ->groupBy('shipping_country')
                ->orderBy('total_tax', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'country' => $item->shipping_country ?: 'Unknown',
                        'total_tax' => round($item->total_tax, 2),
                    ];
                });
            
            return [
                'total_tax_collected' => round($totalTaxCollected, 2),
                'orders_with_tax' => $ordersWithTax,
                'average_tax_per_order' => round($averageTaxPerOrder, 2),
                'tax_by_country' => $taxByCountry,
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
            ];
        });
    }
}