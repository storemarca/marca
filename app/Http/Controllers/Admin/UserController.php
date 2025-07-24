<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * عرض قائمة المستخدمين
     */
    public function index(Request $request)
    {
        $query = User::query();
        
        // البحث
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // التصفية حسب الدور
        if ($request->has('role') && $request->role) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }
        
        // التصفية حسب التاريخ
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // الترتيب
        $sortField = $request->sort_by ?? 'created_at';
        $sortDirection = $request->sort_direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);
        
        $users = $query->with('roles')->paginate(15);
        $roles = Role::all();
        
        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * عرض نموذج إنشاء مستخدم جديد
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * حفظ مستخدم جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);
        
        DB::beginTransaction();
        
        try {
            $user = new User();
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->password = Hash::make($validated['password']);
            $user->save();
            
            // إسناد الأدوار
            $roles = Role::whereIn('id', $validated['roles'])->get();
            $user->syncRoles($roles);
            
            DB::commit();
            
            return redirect()->route('admin.users.index')
                ->with('success', 'تم إنشاء المستخدم بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating user: ' . $e->getMessage());
            
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء المستخدم: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل مستخدم محدد
     */
    public function show(string $id)
    {
        $user = User::with('roles.permissions')->findOrFail($id);
        
        // الحصول على صلاحيات المستخدم
        $permissions = $user->getAllPermissions()->pluck('name');
        
        return view('admin.users.show', compact('user', 'permissions'));
    }

    /**
     * عرض نموذج تعديل مستخدم
     */
    public function edit(string $id)
    {
        $user = User::with('roles')->findOrFail($id);
        $roles = Role::all();
        
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * تحديث بيانات المستخدم
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);
        
        DB::beginTransaction();
        
        try {
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            
            if ($request->filled('password')) {
                $user->password = Hash::make($validated['password']);
            }
            
            $user->save();
            
            // إسناد الأدوار
            $roles = Role::whereIn('id', $validated['roles'])->get();
            $user->syncRoles($roles);
            
            DB::commit();
            
            return redirect()->route('admin.users.index')
                ->with('success', 'تم تحديث بيانات المستخدم بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating user: ' . $e->getMessage());
            
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث بيانات المستخدم: ' . $e->getMessage());
        }
    }

    /**
     * حذف مستخدم
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        
        // منع حذف المستخدم الحالي
        if ($user->id === auth()->id()) {
            return back()->with('error', 'لا يمكنك حذف حسابك الحالي');
        }
        
        // منع حذف المستخدم الرئيسي (مدير النظام)
        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            return back()->with('error', 'لا يمكن حذف المستخدم الرئيسي الوحيد');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }

    /**
     * عرض الملف الشخصي للمستخدم المسجل دخوله حاليًا
     */
    public function profile()
    {
        $user = auth()->user();
        return view('admin.users.profile', compact('user'));
    }
    
    /**
     * تحديث الملف الشخصي للمستخدم المسجل دخوله حاليًا
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }
        
        $user->save();
        
        return redirect()->route('admin.profile')
            ->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }
} 