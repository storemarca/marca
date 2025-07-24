<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    /**
     * عرض قائمة الأدوار
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * عرض نموذج إنشاء دور جديد
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy(function($permission) {
            return explode('.', $permission->name)[0]; // تجميع الصلاحيات حسب النوع
        });
        
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * حفظ دور جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);
        
        DB::beginTransaction();
        
        try {
            $role = Role::create(['name' => $validated['name']]);
            
            // إسناد الصلاحيات
            $permissions = Permission::whereIn('id', $validated['permissions'])->get();
            $role->syncPermissions($permissions);
            
            DB::commit();
            
            return redirect()->route('admin.roles.index')
                ->with('success', 'تم إنشاء الدور بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating role: ' . $e->getMessage());
            
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء الدور: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل دور محدد
     */
    public function show(string $id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $users = $role->users()->paginate(10);
        
        return view('admin.roles.show', compact('role', 'users'));
    }

    /**
     * عرض نموذج تعديل دور
     */
    public function edit(string $id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        
        // منع تعديل دور المدير
        if ($role->name === 'admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'لا يمكن تعديل دور المدير الرئيسي');
        }
        
        $permissions = Permission::all()->groupBy(function($permission) {
            return explode('.', $permission->name)[0]; // تجميع الصلاحيات حسب النوع
        });
        
        // الحصول على صلاحيات الدور الحالي
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * تحديث بيانات الدور
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);
        
        // منع تعديل دور المدير
        if ($role->name === 'admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'لا يمكن تعديل دور المدير الرئيسي');
        }
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);
        
        DB::beginTransaction();
        
        try {
            $role->name = $validated['name'];
            $role->save();
            
            // إسناد الصلاحيات
            $permissions = Permission::whereIn('id', $validated['permissions'])->get();
            $role->syncPermissions($permissions);
            
            DB::commit();
            
            return redirect()->route('admin.roles.index')
                ->with('success', 'تم تحديث الدور بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating role: ' . $e->getMessage());
            
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث الدور: ' . $e->getMessage());
        }
    }

    /**
     * حذف دور
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        
        // منع حذف دور المدير أو دور العميل
        if (in_array($role->name, ['admin', 'customer'])) {
            return back()->with('error', 'لا يمكن حذف الأدوار الأساسية للنظام');
        }
        
        // التحقق من وجود مستخدمين مرتبطين بالدور
        if ($role->users()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف الدور لأنه مرتبط بمستخدمين');
        }
        
        $role->delete();
        
        return redirect()->route('admin.roles.index')
            ->with('success', 'تم حذف الدور بنجاح');
    }
} 