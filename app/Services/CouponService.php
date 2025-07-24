<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CouponService
{
    /**
     * تطبيق كوبون على سلة أو طلب
     */
    public function applyCoupon(string $code, float $subtotal, User $user, $cartItems = null): array
    {
        $coupon = Coupon::where('code', $code)->first();
        if (!$coupon) {
            return [
                'success' => false,
                'message' => 'الكوبون غير موجود',
            ];
        }
        if (!$coupon->isValidForUser($user)) {
            return [
                'success' => false,
                'message' => 'الكوبون غير صالح لهذا المستخدم',
            ];
        }
        $discount = $coupon->calculateDiscount($subtotal);
        return [
            'success' => true,
            'coupon' => $coupon,
            'discount' => $discount,
            'message' => 'تم تطبيق الكوبون بنجاح',
        ];
    }

    /**
     * إنشاء كوبون جديد
     */
    public function createCoupon(array $data): Coupon
    {
        return DB::transaction(function () use ($data) {
            $coupon = Coupon::create($data);
            // علاقات إضافية (categories, products, users)
            if (!empty($data['category_ids'])) {
                $coupon->categories()->sync($data['category_ids']);
            }
            if (!empty($data['product_ids'])) {
                $coupon->products()->sync($data['product_ids']);
            }
            if (!empty($data['user_ids'])) {
                $coupon->users()->sync($data['user_ids']);
            }
            return $coupon;
        });
    }

    /**
     * تحديث كوبون
     */
    public function updateCoupon(Coupon $coupon, array $data): Coupon
    {
        return DB::transaction(function () use ($coupon, $data) {
            $coupon->update($data);
            if (isset($data['category_ids'])) {
                $coupon->categories()->sync($data['category_ids']);
            }
            if (isset($data['product_ids'])) {
                $coupon->products()->sync($data['product_ids']);
            }
            if (isset($data['user_ids'])) {
                $coupon->users()->sync($data['user_ids']);
            }
            return $coupon;
        });
    }

    /**
     * تسجيل استخدام الكوبون
     */
    public function recordCouponUsage(Coupon $coupon, Order $order): void
    {
        $user = $order->customer->user ?? null;
        if ($user) {
            $coupon->incrementUsage($user);
        } else {
            $coupon->increment('usage_count');
        }
    }
}
