<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إعادة تعيين ذاكرة التخزين المؤقت للأدوار والصلاحيات
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // إنشاء الصلاحيات
        $permissions = [
            // صلاحيات المستخدمين
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            
            // صلاحيات المنتجات
            'view products',
            'create products',
            'edit products',
            'delete products',
            
            // صلاحيات الطلبات
            'view orders',
            'manage orders',
            
            // صلاحيات الشحنات
            'view shipments',
            'create shipments',
            'edit shipments',
            'delete shipments',
            
            // صلاحيات الموردين
            'view suppliers',
            'create suppliers',
            'edit suppliers',
            'delete suppliers',
            
            // صلاحيات أوامر الشراء
            'view purchase-orders',
            'create purchase-orders',
            'edit purchase-orders',
            'delete purchase-orders',
            
            // صلاحيات التحصيلات
            'view collections',
            'manage collections',
            
            // صلاحيات التقارير
            'view reports',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // إنشاء الأدوار وإسناد الصلاحيات
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());
        
        $warehouseManagerRole = Role::create(['name' => 'warehouse_manager']);
        $warehouseManagerRole->givePermissionTo([
            'view products',
            'view orders',
            'view shipments',
            'create shipments',
            'edit shipments',
            'view suppliers',
            'view purchase-orders',
        ]);
        
        $financialManagerRole = Role::create(['name' => 'financial_manager']);
        $financialManagerRole->givePermissionTo([
            'view orders',
            'view collections',
            'manage collections',
            'view reports',
        ]);
        
        $customerRole = Role::create(['name' => 'customer']);
    }
} 