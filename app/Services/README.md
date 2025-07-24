# خدمات النظام (System Services)

هذا الدليل يوضح الخدمات المتاحة في النظام وكيفية استخدامها.

## قائمة الخدمات

1. [خدمة الطلبات (OrderService)](#خدمة-الطلبات-orderservice)
2. [خدمة المدفوعات (PaymentService)](#خدمة-المدفوعات-paymentservice)
3. [خدمة الكوبونات (CouponService)](#خدمة-الكوبونات-couponservice)
4. [خدمة الشحن (ShippingService)](#خدمة-الشحن-shippingservice)
5. [خدمة الإرجاع (ReturnService)](#خدمة-الإرجاع-returnservice)
6. [خدمة المخزون (InventoryService)](#خدمة-المخزون-inventoryservice)
7. [خدمة الإشعارات (NotificationService)](#خدمة-الإشعارات-notificationservice)
8. [خدمة التقارير (ReportService)](#خدمة-التقارير-reportservice)
9. [خدمة التحليلات (AnalyticsService)](#خدمة-التحليلات-analyticsservice)
10. [خدمة الإعدادات (SettingService)](#خدمة-الإعدادات-settingservice)

## خدمة الطلبات (OrderService)

تتعامل هذه الخدمة مع إنشاء وإدارة الطلبات في النظام.

### الوظائف الرئيسية

- `createOrder`: إنشاء طلب جديد
- `processOrderPayment`: معالجة دفع الطلب
- `updateOrderStatus`: تحديث حالة الطلب
- `checkAndUpdateStock`: التحقق من المخزون وتحديثه
- `getCustomerOrders`: الحصول على طلبات العميل
- `getOrderDetails`: الحصول على تفاصيل الطلب
- `cancelOrder`: إلغاء الطلب

### مثال استخدام

```php
$orderService = app(App\Services\OrderService::class);

// إنشاء طلب جديد
$orderData = [
    'customer_id' => 1,
    'products' => [
        ['id' => 1, 'quantity' => 2],
        ['id' => 3, 'quantity' => 1],
    ],
    'shipping_address_id' => 5,
    'billing_address_id' => 5,
    'shipping_method_id' => 2,
    'payment_method' => 'stripe',
    'coupon_code' => 'SUMMER10',
];

$order = $orderService->createOrder($orderData);
```

## خدمة المدفوعات (PaymentService)

تتعامل هذه الخدمة مع معالجة المدفوعات من خلال بوابات الدفع المختلفة.

### الوظائف الرئيسية

- `processPayment`: معالجة الدفع
- `getGatewayHandler`: الحصول على معالج بوابة الدفع
- `createGatewayHandler`: إنشاء معالج بوابة الدفع
- `getAvailableGateways`: الحصول على بوابات الدفع المتاحة
- `processRefund`: معالجة استرداد المبلغ

### مثال استخدام

```php
$paymentService = app(App\Services\PaymentService::class);

// معالجة الدفع
$paymentData = [
    'amount' => 150.75,
    'currency' => 'USD',
    'gateway' => 'stripe',
    'order_id' => 1001,
    'card_token' => 'tok_visa',
];

$transaction = $paymentService->processPayment($paymentData);
```

## خدمة الكوبونات (CouponService)

تتعامل هذه الخدمة مع إدارة الكوبونات والخصومات.

### الوظائف الرئيسية

- `applyCoupon`: تطبيق كوبون على طلب
- `calculateDiscount`: حساب قيمة الخصم
- `isCouponApplicableToItems`: التحقق من إمكانية تطبيق الكوبون على العناصر
- `recordCouponUsage`: تسجيل استخدام الكوبون
- `createCoupon`: إنشاء كوبون جديد
- `updateCoupon`: تحديث كوبون
- `getValidCoupons`: الحصول على الكوبونات الصالحة

### مثال استخدام

```php
$couponService = app(App\Services\CouponService::class);

// تطبيق كوبون على طلب
$order = Order::find(1001);
$couponCode = 'SUMMER10';

$result = $couponService->applyCoupon($order, $couponCode);
```

## خدمة الشحن (ShippingService)

تتعامل هذه الخدمة مع إدارة عمليات الشحن والتوصيل.

### الوظائف الرئيسية

- `createShipment`: إنشاء شحنة
- `updateShipmentStatus`: تحديث حالة الشحنة
- `calculateShippingCost`: حساب تكلفة الشحن
- `getAvailableShippingMethods`: الحصول على طرق الشحن المتاحة
- `isOrderFullyShipped`: التحقق مما إذا كان الطلب قد تم شحنه بالكامل
- `trackShipment`: تتبع الشحنة

### مثال استخدام

```php
$shippingService = app(App\Services\ShippingService::class);

// إنشاء شحنة
$shipmentData = [
    'order_id' => 1001,
    'items' => [
        ['order_item_id' => 1, 'quantity' => 2],
        ['order_item_id' => 2, 'quantity' => 1],
    ],
    'shipping_company_id' => 3,
    'tracking_number' => 'TRK123456789',
];

$shipment = $shippingService->createShipment($shipmentData);
```

## خدمة الإرجاع (ReturnService)

تتعامل هذه الخدمة مع إدارة طلبات إرجاع المنتجات.

### الوظائف الرئيسية

- `createReturnRequest`: إنشاء طلب إرجاع
- `updateReturnStatus`: تحديث حالة طلب الإرجاع
- `processReturnCompletion`: معالجة اكتمال الإرجاع
- `returnItemToInventory`: إعادة العنصر إلى المخزون

### مثال استخدام

```php
$returnService = app(App\Services\ReturnService::class);

// إنشاء طلب إرجاع
$returnData = [
    'order_id' => 1001,
    'reason' => 'المنتج تالف',
    'notes' => 'العبوة مفتوحة والمنتج به خدوش',
    'return_method' => 'refund',
    'items' => [
        ['order_item_id' => 1, 'quantity' => 1, 'condition' => 'damaged', 'reason' => 'المنتج تالف'],
    ],
];

$returnRequest = $returnService->createReturnRequest($returnData);
```

## خدمة المخزون (InventoryService)

تتعامل هذه الخدمة مع إدارة المخزون والمستودعات.

### الوظائف الرئيسية

- `updateStock`: تحديث كمية المخزون
- `reserveStock`: حجز مخزون لطلب
- `releaseReservedStock`: تحرير المخزون المحجوز
- `hasAvailableStock`: التحقق من توفر المخزون
- `processOrderStock`: معالجة مخزون الطلب
- `restoreOrderStock`: استعادة مخزون الطلب الملغى
- `transferStock`: نقل المخزون بين المستودعات
- `getLowStockProducts`: الحصول على المنتجات ذات المخزون المنخفض

### مثال استخدام

```php
$inventoryService = app(App\Services\InventoryService::class);

// تحديث كمية المخزون
$productId = 101;
$warehouseId = 1;
$quantity = 50;

$result = $inventoryService->updateStock(
    $productId,
    $warehouseId,
    $quantity,
    'add',
    'استلام بضاعة جديدة'
);
```

## خدمة الإشعارات (NotificationService)

تتعامل هذه الخدمة مع إرسال وإدارة الإشعارات للمستخدمين والعملاء.

### الوظائف الرئيسية

- `sendOrderCreatedNotification`: إرسال إشعار بإنشاء طلب
- `sendOrderStatusChangedNotification`: إرسال إشعار بتغيير حالة الطلب
- `sendPaymentReceivedNotification`: إرسال إشعار باستلام الدفع
- `sendShipmentCreatedNotification`: إرسال إشعار بإنشاء شحنة
- `sendReturnRequestCreatedNotification`: إرسال إشعار بإنشاء طلب إرجاع
- `sendLowStockNotification`: إرسال إشعار بانخفاض المخزون

### مثال استخدام

```php
$notificationService = app(App\Services\NotificationService::class);

// إرسال إشعار بإنشاء طلب
$order = Order::find(1001);
$notificationService->sendOrderCreatedNotification($order);
```

## خدمة التقارير (ReportService)

تتعامل هذه الخدمة مع إنشاء وإدارة التقارير المختلفة في النظام.

### الوظائف الرئيسية

- `getSalesReport`: الحصول على تقرير المبيعات
- `getProductSalesReport`: الحصول على تقرير مبيعات المنتجات
- `getCategorySalesReport`: الحصول على تقرير مبيعات الفئات
- `getCustomerSalesReport`: الحصول على تقرير مبيعات العملاء
- `getInventoryReport`: الحصول على تقرير المخزون
- `getLowStockReport`: الحصول على تقرير المخزون المنخفض
- `getReturnReport`: الحصول على تقرير الإرجاع
- `getDashboardStats`: الحصول على إحصائيات لوحة التحكم

### مثال استخدام

```php
$reportService = app(App\Services\ReportService::class);

// الحصول على تقرير المبيعات
$startDate = '2023-01-01';
$endDate = '2023-12-31';
$groupBy = 'month';

$salesReport = $reportService->getSalesReport($startDate, $endDate, $groupBy);
```

## خدمة التحليلات (AnalyticsService)

تتعامل هذه الخدمة مع تحليل البيانات وتتبع سلوك المستخدمين.

### الوظائف الرئيسية

- `logProductView`: تسجيل مشاهدة منتج
- `logSearchQuery`: تسجيل استعلام بحث
- `getMostViewedProducts`: الحصول على أكثر المنتجات مشاهدة
- `getBestSellingProducts`: الحصول على أكثر المنتجات مبيعاً
- `getPopularSearchQueries`: الحصول على أكثر عمليات البحث شيوعاً
- `getSalesStatsByPeriod`: الحصول على إحصائيات المبيعات حسب الفترة
- `getConversionRate`: الحصول على معدل التحويل

### مثال استخدام

```php
$analyticsService = app(App\Services\AnalyticsService::class);

// تسجيل مشاهدة منتج
$productId = 101;
$userId = auth()->id();
$analyticsService->logProductView($productId, $userId);

// الحصول على أكثر المنتجات مبيعاً
$bestSellers = $analyticsService->getBestSellingProducts(10, 30); // أفضل 10 منتجات في آخر 30 يوم
```

## خدمة الإعدادات (SettingService)

تتعامل هذه الخدمة مع إدارة إعدادات النظام.

### الوظائف الرئيسية

- `getAllSettings`: الحصول على جميع الإعدادات
- `getSetting`: الحصول على إعداد محدد
- `updateSetting`: تحديث إعداد
- `updateSettings`: تحديث عدة إعدادات
- `deleteSetting`: حذف إعداد
- `uploadSettingFile`: رفع ملف للإعدادات
- `getStoreSettings`: الحصول على إعدادات المتجر
- `getPaymentSettings`: الحصول على إعدادات الدفع
- `getShippingSettings`: الحصول على إعدادات الشحن

### مثال استخدام

```php
$settingService = app(App\Services\SettingService::class);

// الحصول على إعداد محدد
$storeName = $settingService->getSetting('store_name', 'My Store');

// تحديث إعداد
$settingService->updateSetting('store_name', 'New Store Name');

// الحصول على إعدادات المتجر
$storeSettings = $settingService->getStoreSettings();
```

## كيفية إضافة خدمة جديدة

1. قم بإنشاء ملف PHP جديد في مجلد `app/Services`
2. قم بتعريف الفئة (class) الخاصة بالخدمة
3. قم بتسجيل الخدمة في مزود الخدمة (Service Provider) إذا لزم الأمر

### مثال لإنشاء خدمة جديدة

```php
<?php

namespace App\Services;

class NewService
{
    public function doSomething()
    {
        // قم بتنفيذ العملية المطلوبة
        return 'تم تنفيذ العملية بنجاح';
    }
}
```

### تسجيل الخدمة في مزود الخدمة

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\NewService;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(NewService::class, function ($app) {
            return new NewService();
        });
    }
}
```

## ملاحظات هامة

- تأكد من استخدام الحقن التبعي (Dependency Injection) لتحسين قابلية الاختبار
- استخدم واجهات (Interfaces) لتحقيق مبدأ الاعتماد على التجريد
- قم بتوثيق الخدمات الجديدة في هذا الملف
- استخدم المعاملات (Transactions) عند التعامل مع قاعدة البيانات لضمان تكامل البيانات