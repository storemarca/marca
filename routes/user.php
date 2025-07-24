<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\ProductController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\AccountController;
use App\Http\Controllers\User\ReturnController;
use App\Http\Controllers\User\ReviewController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\LoyaltyController;
use App\Http\Controllers\User\AffiliateController;

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for the user side of the application.
|
*/

// المنتجات
Route::get('/products', [ProductController::class, 'index'])->name('user.products.index');
// إضافة مسار للوصول إلى المنتج عن طريق المعرف (يجب أن يكون قبل مسار slug)
Route::get('/products/id/{id}', [ProductController::class, 'showById'])->name('user.products.show.by.id');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('user.products.show');

// سلة التسوق
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/discount', [CartController::class, 'applyDiscount'])->name('cart.apply-discount');
Route::delete('/cart/discount', [CartController::class, 'removeDiscount'])->name('cart.remove-discount');

// الدفع
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');

// تتبع الطلب للزوار
Route::get('/track-order', [OrderController::class, 'track'])->name('orders.track');
Route::post('/track-order', [OrderController::class, 'trackOrder'])->name('orders.track.submit');

// مسارات المستخدم المصادق عليها
Route::middleware('auth')->group(function () {
    // الطلبات
    Route::get('/orders', [OrderController::class, 'index'])->name('user.orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('user.orders.show');
    
    // المرتجعات
    Route::get('/returns', [ReturnController::class, 'index'])->name('user.returns.index');
    Route::get('/returns/create', [ReturnController::class, 'create'])->name('user.returns.create');
    Route::post('/returns', [ReturnController::class, 'store'])->name('user.returns.store');
    Route::get('/returns/{return}', [ReturnController::class, 'show'])->name('user.returns.show');
    Route::post('/returns/{return}/cancel', [ReturnController::class, 'cancel'])->name('user.returns.cancel');
    
    // تقييمات المنتجات
    Route::get('/reviews/create', [ReviewController::class, 'create'])->name('user.reviews.create');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('user.reviews.store');
    Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('user.reviews.edit');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('user.reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('user.reviews.destroy');
    Route::delete('/reviews/{review}/images', [ReviewController::class, 'deleteImage'])->name('user.reviews.delete-image');
    Route::post('/reviews/{review}/vote', [ReviewController::class, 'vote'])->name('user.reviews.vote');
    Route::delete('/reviews/{review}/vote', [ReviewController::class, 'removeVote'])->name('user.reviews.remove-vote');
    
    // الحساب
    Route::get('/account', [AccountController::class, 'index'])->name('user.account.index');
    Route::get('/account/edit', [AccountController::class, 'edit'])->name('user.account.edit');
    Route::patch('/account/update', [AccountController::class, 'update'])->name('user.account.update');
    Route::get('/account/addresses', [AccountController::class, 'addresses'])->name('user.account.addresses');
    Route::post('/account/addresses', [AccountController::class, 'storeAddress'])->name('user.account.addresses.store');
    Route::patch('/account/addresses/{address}', [AccountController::class, 'updateAddress'])->name('user.account.addresses.update');
    Route::delete('/account/addresses/{address}', [AccountController::class, 'destroyAddress'])->name('user.account.addresses.destroy');
    
    // الإشعارات
    Route::get('/notifications', [NotificationController::class, 'index'])->name('user.notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('user.notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('user.notifications.read-all');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('user.notifications.destroy');
    Route::delete('/notifications', [NotificationController::class, 'destroyAll'])->name('user.notifications.destroy-all');
    Route::get('/notifications/count', [NotificationController::class, 'getUnreadCount'])->name('user.notifications.count');
    Route::get('/notifications/latest', [NotificationController::class, 'getLatest'])->name('user.notifications.latest');
    
    // نظام الولاء
    Route::group(['prefix' => 'loyalty', 'as' => 'loyalty.'], function () {
        Route::get('/', [LoyaltyController::class, 'index'])->name('index');
        Route::get('/transactions', [LoyaltyController::class, 'transactions'])->name('transactions');
        Route::get('/rewards', [LoyaltyController::class, 'rewards'])->name('rewards');
        Route::get('/rewards/{reward}', [LoyaltyController::class, 'showReward'])->name('rewards.show');
        Route::post('/rewards/{reward}/redeem', [LoyaltyController::class, 'redeemReward'])->name('rewards.redeem');
        Route::get('/redemptions', [LoyaltyController::class, 'redemptions'])->name('redemptions');
        Route::get('/redemptions/{redemption}', [LoyaltyController::class, 'showRedemption'])->name('redemptions.show');
        Route::post('/redemptions/{redemption}/cancel', [LoyaltyController::class, 'cancelRedemption'])->name('redemptions.cancel');
    });

    // نظام المسوقين بالعمولة
    Route::group(['prefix' => 'affiliate', 'as' => 'affiliate.'], function () {
        Route::get('/', [AffiliateController::class, 'index'])->name('index');
        Route::get('/apply', [AffiliateController::class, 'apply'])->name('apply');
        Route::post('/apply', [AffiliateController::class, 'store'])->name('store');
        Route::get('/pending', [AffiliateController::class, 'pending'])->name('pending');
        Route::get('/rejected', [AffiliateController::class, 'rejected'])->name('rejected');
        Route::get('/suspended', [AffiliateController::class, 'suspended'])->name('suspended');
        
        // الروابط التسويقية
        Route::get('/links', [AffiliateController::class, 'links'])->name('links');
        Route::post('/links', [AffiliateController::class, 'createLink'])->name('create-link');
        
        // المعاملات والإحالات
        Route::get('/transactions', [AffiliateController::class, 'transactions'])->name('transactions');
        Route::get('/referrals', [AffiliateController::class, 'referrals'])->name('referrals');
        
        // طلبات السحب
        Route::get('/withdrawals', [AffiliateController::class, 'withdrawals'])->name('withdrawals');
        Route::get('/withdrawals/create', [AffiliateController::class, 'showWithdrawalForm'])->name('withdrawal-form');
        Route::post('/withdrawals', [AffiliateController::class, 'requestWithdrawal'])->name('request-withdrawal');
        
        // المواد التسويقية
        Route::get('/marketing-materials', [AffiliateController::class, 'marketingMaterials'])->name('marketing-materials');
    });
}); 