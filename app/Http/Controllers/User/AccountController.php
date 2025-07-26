<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Address;
use App\Models\Country;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    /**
     * عرض لوحة تحكم المستخدم
     */
    public function index()
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // إحصائيات للمستخدم
        $ordersCount = Order::where('customer_id', $user->id)->count() ?? 0;
        $addressesCount = Address::where('customer_id', $user->id)->count();
        
        // آخر الطلبات
        $recentOrders = Order::where('customer_id', $user->id)
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
        
        return view('user.account.index', compact('user', 'ordersCount', 'addressesCount', 'recentOrders'));
    }
    
    /**
     * عرض نموذج تعديل بيانات المستخدم
     */
    public function edit()
    {
        $user = auth()->user();
        
        return view('user.account.edit', compact('user'));
    }
    
    /**
     * تحديث بيانات المستخدم
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'current_password' => 'nullable|required_with:password|string',
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);
        
        // التحقق من كلمة المرور الحالية إذا كان المستخدم يريد تغييرها
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة']);
            }
        }
        
        // تحديث البيانات
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->phone = $validatedData['phone'] ?? $user->phone;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($validatedData['password']);
        }
        
        $user->save();
        
        return redirect()->route('user.account.edit')->with('success', 'تم تحديث بياناتك بنجاح');
    }
    
    /**
     * عرض قائمة عناوين المستخدم
     */
    public function addresses()
    {
        $addresses = Address::where('customer_id', auth()->id())->get();
        $countries = Country::all();
        
        return view('user.account.addresses', compact('addresses', 'countries'));
    }
    
    /**
     * إضافة عنوان جديد
     */
    public function storeAddress(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country_id' => 'required|exists:countries,id',
            'is_default' => 'boolean',
        ]);
        
        // إذا كان العنوان الافتراضي، قم بإلغاء تعيين العناوين الأخرى كافتراضية
        if ($request->has('is_default') && $request->is_default) {
            Address::where('customer_id', auth()->id())
                  ->update(['is_default' => false]);
        }
        
        // إنشاء العنوان الجديد
        $address = new Address($validatedData);
        $address->customer_id = auth()->id();
        $address->save();
        
        return redirect()->route('user.account.addresses')->with('success', 'تم إضافة العنوان بنجاح');
    }
    
    /**
     * تحديث عنوان
     */
    public function updateAddress(Request $request, $id)
    {
        $address = Address::where('customer_id', auth()->id())
                         ->findOrFail($id);
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country_id' => 'required|exists:countries,id',
            'is_default' => 'boolean',
        ]);
        
        // إذا كان العنوان الافتراضي، قم بإلغاء تعيين العناوين الأخرى كافتراضية
        if ($request->has('is_default') && $request->is_default) {
            Address::where('customer_id', auth()->id())
                  ->where('id', '!=', $id)
                  ->update(['is_default' => false]);
        }
        
        // تحديث العنوان
        $address->update($validatedData);
        
        return redirect()->route('user.account.addresses')->with('success', 'تم تحديث العنوان بنجاح');
    }
    
    /**
     * حذف عنوان
     */
    public function destroyAddress($id)
    {
        $address = Address::where('customer_id', auth()->id())
                         ->findOrFail($id);
        
        // إذا كان العنوان الافتراضي، لا يمكن حذفه
        if ($address->is_default) {
            return redirect()->route('user.account.addresses')->with('error', 'لا يمكن حذف العنوان الافتراضي');
        }
        
        $address->delete();
        
        return redirect()->route('user.account.addresses')->with('success', 'تم حذف العنوان بنجاح');
    }
}
