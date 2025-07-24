<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class NewAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء حساب مسؤول جديد
        $admin = User::create([
            'name' => 'مدير النظام',
            'email' => 'superadmin@marca.com',
            'password' => Hash::make('admin123456'),
            'phone' => '+201234567890',
        ]);
        
        // إسناد دور المسؤول
        $admin->assignRole('admin');
        
        // إنشاء حساب مدير مبيعات
        $salesManager = User::create([
            'name' => 'مدير المبيعات',
            'email' => 'sales@marca.com',
            'password' => Hash::make('sales123456'),
            'phone' => '+201234567891',
        ]);
        
        // إسناد دور المسؤول (يمكن تعديله لدور آخر إذا كان موجوداً)
        $salesManager->assignRole('admin');
    }
} 