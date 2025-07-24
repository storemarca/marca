<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\Address;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
     public function run(): void
    {/*
        // الحصول على الدول
        $saudiArabia = Country::where('code', 'SA')->first();
        $egypt = Country::where('code', 'EG')->first();
        $uae = Country::where('code', 'AE')->first();

        // إنشاء المستخدم الأول
        $user1 = User::create([
            'name' => 'أحمد محمد',
            'email' => 'ahmed@example.com',
            'password' => Hash::make('password123'),
            'phone' => '+966501234567',
        ]);

        // إنشاء بيانات العميل للمستخدم الأول
        $customer1 = Customer::create([
            'user_id' => $user1->getKey(),
            'default_country_id' => $saudiArabia->getKey(),
            'birth_date' => '1990-01-15',
            'gender' => 'male',
            'first_name' => 'أحمد',
            'last_name' => 'محمد',
            'email' => 'ahmed@example.com',
            'phone' => '+966501234567',
            'is_active' => true,
        ]);

        // إضافة عنوان للمستخدم الأول
        Address::create([
            'user_id' => $user1->getKey(),
            'country_id' => $saudiArabia->getKey(),
            'name' => 'المنزل',
            'address_line1' => 'حي النزهة',
            'address_line2' => 'شارع الملك فهد',
            'city' => 'الرياض',
            'postal_code' => '12345',
            'phone' => '+966501234567',
            'is_default' => true,
        ]);

        // إنشاء المستخدم الثاني
        $user2 = User::create([
            'name' => 'فاطمة علي',
            'email' => 'fatima@example.com',
            'password' => Hash::make('password123'),
            'phone' => '+201001234567',
        ]);

        // إنشاء بيانات العميل للمستخدم الثاني
        $customer2 = Customer::create([
            'user_id' => $user2->getKey(),
            'default_country_id' => $egypt->getKey(),
            'birth_date' => '1995-05-20',
            'gender' => 'female',
            'first_name' => 'فاطمة',
            'last_name' => 'علي',
            'email' => 'fatima@example.com',
            'phone' => '+201001234567',
            'is_active' => true,
        ]);

        // إضافة عنوان للمستخدم الثاني
        Address::create([
            'user_id' => $user2->getKey(),
            'country_id' => $egypt->getKey(),
            'name' => 'المنزل',
            'address_line1' => 'المعادي',
            'address_line2' => 'شارع 9',
            'city' => 'القاهرة',
            'postal_code' => '11435',
            'phone' => '+201001234567',
            'is_default' => true,
        ]);

        // إنشاء المستخدم الثالث
        $user3 = User::create([
            'name' => 'خالد عبدالله',
            'email' => 'khalid@example.com',
            'password' => Hash::make('password123'),
            'phone' => '+971501234567',
        ]);

        // إنشاء بيانات العميل للمستخدم الثالث
        $customer3 = Customer::create([
            'user_id' => $user3->getKey(),
            'default_country_id' => $uae->getKey(),
            'birth_date' => '1988-11-10',
            'gender' => 'male',
            'first_name' => 'خالد',
            'last_name' => 'عبدالله',
            'email' => 'khalid@example.com',
            'phone' => '+971501234567',
            'is_active' => true,
        ]);

        // إضافة عنوان للمستخدم الثالث
        Address::create([
            'user_id' => $user3->getKey(),
            'country_id' => $uae->getKey(),
            'name' => 'المنزل',
            'address_line1' => 'شارع الشيخ زايد',
            'address_line2' => 'برج خليفة',
            'city' => 'دبي',
            'postal_code' => '12345',
            'phone' => '+971501234567',
            'is_default' => true,
        ]);*/
    }

} 