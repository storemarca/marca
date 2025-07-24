<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ShipmentController;
use App\Http\Controllers\Admin\ShippingCompanyController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\PaymentGatewayController;
use App\Http\Controllers\Admin\ReturnController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\LoyaltyTierController;
use App\Http\Controllers\Admin\LoyaltyRewardController;
use App\Http\Controllers\Admin\AffiliateController;
use App\Http\Controllers\Admin\ShippingMethodController;
use App\Http\Controllers\Admin\GovernorateController;
use App\Http\Controllers\Admin\DistrictController;
use App\Http\Controllers\Admin\AreaController;

// مسارات لوحة الإدارة
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // لوحة التحكم
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // صفحة حالات الطلبات
    Route::get('/sales', function () {
        return view('admin.sales');
    })->name('sales');
    
        // المنتجات
    Route::resource('products', ProductController::class);
    Route::get('products/{product}/stock', [ProductController::class, 'stock'])->name('products.stock');
    Route::post('products/{product}/stock', [ProductController::class, 'updateStock'])->name('products.stock.update');
    Route::get('products/{product}/prices', [ProductController::class, 'prices'])->name('products.prices');
    Route::post('products/{product}/prices', [ProductController::class, 'updatePrices'])->name('products.prices.update');
    Route::post('products/generate-sku', [ProductController::class, 'generateSku'])->name('products.generate-sku');

    // الأقسام
    Route::resource('categories', CategoryController::class);
    
    // المستودعات
    Route::resource('warehouses', WarehouseController::class);
    Route::post('warehouses/{warehouse}/toggle-status', [WarehouseController::class, 'toggleStatus'])->name('warehouses.toggle-status');
    
    // البلدان
    Route::resource('countries', CountryController::class);
    Route::post('countries/{country}/toggle-status', [CountryController::class, 'toggleStatus'])->name('countries.toggle-status');
    
    // المناطق الجغرافية
    Route::group(['prefix' => 'regions', 'as' => 'regions.'], function () {
        // المحافظات
        Route::resource('governorates', GovernorateController::class);
        
        // المراكز
        Route::resource('districts', DistrictController::class);
        
        // المناطق
        Route::resource('areas', AreaController::class);
    });
    
    // الطلبات
    Route::resource('orders', OrderController::class);
    Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('orders/{order}/invoice/pdf', [OrderController::class, 'invoicePdf'])->name('orders.invoice.pdf');
    Route::get('orders/{order}/download-invoice', [OrderController::class, 'downloadInvoice'])->name('orders.downloadInvoice');
    Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('orders/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('orders.payment-status');
    Route::post('orders/{order}/create-shipment', [OrderController::class, 'createShipment'])->name('orders.createShipment');
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancelOrder'])->name('orders.cancelOrder');
    
    // الشحنات
    Route::resource('shipments', ShipmentController::class);
    Route::post('shipments/{shipment}/status', [ShipmentController::class, 'updateStatus'])->name('shipments.status');
    Route::post('shipments/{shipment}/tracking', [ShipmentController::class, 'updateTracking'])->name('shipments.tracking');
    
    // Nuevas rutas para integración con empresas de envío
    Route::post('shipments/{shipment}/create-with-api', [ShipmentController::class, 'createShipmentWithApi'])->name('shipments.create-with-api');
    Route::get('shipments/{shipment}/refresh-tracking', [ShipmentController::class, 'refreshTracking'])->name('shipments.refresh-tracking');
    Route::get('shipments/{shipment}/tracking-history', [ShipmentController::class, 'trackingHistory'])->name('shipments.tracking-history');
    
    // شركات الشحن
    Route::resource('shipping-companies', ShippingCompanyController::class);
    Route::post('shipping-companies/{shippingCompany}/test-api', [ShippingCompanyController::class, 'testApi'])->name('shipping-companies.test-api');

    // Shipping methods
    Route::resource('shipping-methods', ShippingMethodController::class);

    // Test route
    Route::get('test-shipping-methods', [App\Http\Controllers\Admin\TestController::class, 'testShippingMethods'])->name('test-shipping-methods');
    
    // الموردين
    Route::resource('suppliers', SupplierController::class);
    
    // طلبات الشراء
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::post('purchase-orders/{purchaseOrder}/status', [PurchaseOrderController::class, 'updateStatus'])->name('purchase-orders.status');
    Route::get('purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'showReceive'])->name('purchase-orders.receive');
    Route::post('purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'processReceive'])->name('purchase-orders.process-receive');
    
    // التحصيلات
    Route::resource('collections', CollectionController::class);
    Route::post('collections/{collection}/mark-collected', [CollectionController::class, 'markCollected'])->name('collections.mark-collected');
    Route::post('collections/{collection}/mark-settled', [CollectionController::class, 'markSettled'])->name('collections.mark-settled');
    
    // العملاء
    Route::resource('customers', CustomerController::class);
    Route::post('customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
    Route::post('customers/{customer}/addresses', [CustomerController::class, 'addAddress'])->name('customers.add-address');
    Route::delete('customers/{customer}/addresses/{address}', [CustomerController::class, 'deleteAddress'])->name('customers.delete-address');
    
    // التقارير
Route::get('reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
Route::get('reports/collections', [ReportController::class, 'collections'])->name('reports.collections');
Route::get('reports/purchases', [ReportController::class, 'purchases'])->name('reports.purchases');
Route::get('reports/stock-movements', [ReportController::class, 'stockMovements'])->name('reports.stock-movements');

    
    // المستخدمون والصلاحيات
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    
    // الملف الشخصي
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    
    // الإعدادات
    Route::prefix('settings')->name('settings.')->group(function () {
        // الإعدادات العامة
        Route::get('/general', [SettingController::class, 'general'])->name('general');
        Route::post('/general', [SettingController::class, 'saveGeneral'])->name('general.save');
        Route::put('/general', [SettingController::class, 'saveGeneral'])->name('general.update');
        
        // إعدادات الصفحة الرئيسية
        Route::get('/homepage', [SettingController::class, 'homepage'])->name('homepage');
        Route::post('/homepage', [SettingController::class, 'saveHomepage'])->name('homepage.save');
        Route::put('/homepage', [SettingController::class, 'saveHomepage'])->name('homepage.update');
        
        // إعدادات الثيمات
        Route::get('/theme', [SettingController::class, 'theme'])->name('theme');
        Route::post('/theme', [SettingController::class, 'saveTheme'])->name('theme.save');
        Route::put('/theme', [SettingController::class, 'saveTheme'])->name('theme.update');
        
        // إعدادات البريد الإلكتروني
        Route::get('/mail', [SettingController::class, 'mail'])->name('mail');
        Route::post('/mail', [SettingController::class, 'saveMail'])->name('mail.save');
        Route::put('/mail', [SettingController::class, 'saveMail'])->name('mail.update');
        
        // إعدادات الشحن
        Route::get('/shipping', [SettingController::class, 'shipping'])->name('shipping');
        Route::post('/shipping', [SettingController::class, 'saveShipping'])->name('shipping.save');
        Route::put('/shipping', [SettingController::class, 'saveShipping'])->name('shipping.update');
        
        // إعدادات الدفع
        Route::get('/payment', [SettingController::class, 'payment'])->name('payment');
        Route::post('/payment', [SettingController::class, 'savePayment'])->name('payment.save');
        Route::put('/payment', [SettingController::class, 'savePayment'])->name('payment.update');
    });

    // Payment Gateways
    Route::resource('payment-gateways', PaymentGatewayController::class);
    Route::post('payment-gateways/{paymentGateway}/toggle-active', [PaymentGatewayController::class, 'toggleActive'])->name('payment-gateways.toggle-active');

    // Returns Management
    Route::get('returns', [ReturnController::class, 'index'])->name('returns.index');
    Route::get('returns/{return}', [ReturnController::class, 'show'])->name('returns.show');
    Route::post('returns/{return}/status', [ReturnController::class, 'updateStatus'])->name('returns.update-status');
    Route::get('returns/export', [ReturnController::class, 'export'])->name('returns.export');

    // Reviews Management
    Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::get('reviews/{review}', [ReviewController::class, 'show'])->name('reviews.show');
    Route::post('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('reviews/{review}/unapprove', [ReviewController::class, 'unapprove'])->name('reviews.unapprove');
    Route::post('reviews/{review}/toggle-featured', [ReviewController::class, 'toggleFeatured'])->name('reviews.toggle-featured');
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('reviews/images/{image}/approve', [ReviewController::class, 'approveImage'])->name('reviews.images.approve');
    Route::post('reviews/images/{image}/unapprove', [ReviewController::class, 'unapproveImage'])->name('reviews.images.unapprove');
    Route::delete('reviews/images/{image}', [ReviewController::class, 'destroyImage'])->name('reviews.images.destroy');
    Route::get('reviews/export', [ReviewController::class, 'export'])->name('reviews.export');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('notifications', [NotificationController::class, 'destroyAll'])->name('notifications.destroy-all');
    Route::get('notifications/count', [NotificationController::class, 'getUnreadCount'])->name('notifications.count');
    Route::get('notifications/latest', [NotificationController::class, 'getLatest'])->name('notifications.latest');

    // Coupons
    Route::resource('coupons', CouponController::class);
    Route::post('coupons/{coupon}/toggle-active', [CouponController::class, 'toggleActive'])->name('coupons.toggle-active');
    Route::get('coupons-export', [CouponController::class, 'export'])->name('coupons.export');

    // Loyalty System
    Route::group(['prefix' => 'loyalty', 'as' => 'loyalty.'], function () {
        // Loyalty Tiers
        Route::resource('tiers', LoyaltyTierController::class);
        Route::post('tiers/{tier}/toggle-active', [LoyaltyTierController::class, 'toggleActive'])->name('tiers.toggle-active');
        
        // Loyalty Rewards
        Route::resource('rewards', LoyaltyRewardController::class);
        Route::post('rewards/{reward}/toggle-active', [LoyaltyRewardController::class, 'toggleActive'])->name('rewards.toggle-active');
        Route::post('rewards/{reward}/update-stock', [LoyaltyRewardController::class, 'updateStock'])->name('rewards.update-stock');
    });

    // نظام المسوقين بالعمولة
    Route::group(['prefix' => 'affiliates', 'as' => 'affiliates.'], function () {
        Route::get('/', [AffiliateController::class, 'index'])->name('index');
        Route::get('/dashboard', [AffiliateController::class, 'dashboard'])->name('dashboard');
        
        // طلبات السحب
        Route::get('/withdrawal-requests', [AffiliateController::class, 'withdrawalRequests'])->name('withdrawal-requests');
        Route::post('/withdrawal-requests/{withdrawalRequest}/approve', [AffiliateController::class, 'approveWithdrawal'])->name('approve-withdrawal');
        Route::post('/withdrawal-requests/{withdrawalRequest}/reject', [AffiliateController::class, 'rejectWithdrawal'])->name('reject-withdrawal');
        Route::post('/withdrawal-requests/{withdrawalRequest}/mark-paid', [AffiliateController::class, 'markWithdrawalAsPaid'])->name('mark-withdrawal-paid');
        Route::get('/export-withdrawal-requests', [AffiliateController::class, 'exportWithdrawalRequests'])->name('export-withdrawal-requests');
        
        Route::get('/{affiliate}', [AffiliateController::class, 'show'])->name('show');
        Route::post('/{affiliate}/approve', [AffiliateController::class, 'approve'])->name('approve');
        Route::post('/{affiliate}/reject', [AffiliateController::class, 'reject'])->name('reject');
        Route::post('/{affiliate}/suspend', [AffiliateController::class, 'suspend'])->name('suspend');
        Route::post('/{affiliate}/commission-rate', [AffiliateController::class, 'updateCommissionRate'])->name('update-commission-rate');
    });
}); 