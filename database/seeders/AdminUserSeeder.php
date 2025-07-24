<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء حساب المسؤول الرئيسي
        $admin = User::create([
            'name' => 'المسؤول',
            'email' => 'admin@marca.com',
            'password' => Hash::make('admin123'),
        ]);
        
        // إسناد دور المسؤول
        $admin->assignRole('admin');
        
        // إنشاء حساب مدير المخزن
        $warehouseManager = User::create([
            'name' => 'مدير المخزن',
            'email' => 'warehouse@marca.com',
            'password' => Hash::make('warehouse123'),
        ]);
        
        // إسناد دور مدير المخزن
        $warehouseManager->assignRole('warehouse_manager');
        
        // إنشاء حساب المدير المالي
        $financialManager = User::create([
            'name' => 'المدير المالي',
            'email' => 'finance@marca.com',
            'password' => Hash::make('finance123'),

        ]);
        
        // إسناد دور المدير المالي
        $financialManager->assignRole('financial_manager');
    }
} 