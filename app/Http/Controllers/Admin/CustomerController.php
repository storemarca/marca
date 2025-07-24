<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Address;
use App\Models\Country;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    /**
     * عرض قائمة العملاء
     */
    public function index(Request $request)
    {
        $query = Customer::query();
        
        // البحث
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // التصفية حسب الحالة
        if ($request->has('status') && $request->status) {
            $query->where('is_active', $request->status === 'active' ? 1 : 0);
        }
        
        // التصفية حسب البلد
        if ($request->has('country_id') && $request->country_id) {
            $query->whereHas('addresses', function($q) use ($request) {
                $q->where('country_id', $request->country_id);
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
        
        $customers = $query->withCount('orders')->paginate(15);
        $countries = Country::all();
        
        return view('admin.customers.index', compact('customers', 'countries'));
    }

    /**
     * عرض تفاصيل عميل محدد
     */
    public function show(string $id)
    {
        $customer = Customer::with(['addresses.country', 'orders'])->findOrFail($id);
        
        // إحصائيات العميل
        $totalSpent = $customer->orders()->where('payment_status', 'paid')->sum('total_amount');
        $orderCount = $customer->orders->count();
        $lastOrderDate = $customer->orders->max('created_at');
        
        // Get countries for address modal
        $countries = Country::all();
        
        return view('admin.customers.show', compact('customer', 'totalSpent', 'orderCount', 'lastOrderDate', 'countries'));
    }

    /**
     * عرض نموذج إنشاء عميل جديد
     */
    public function create()
    {
        $countries = Country::all();
        return view('admin.customers.create', compact('countries'));
    }

    /**
     * حفظ عميل جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'required|string|max:20',
            'is_active' => 'sometimes|boolean',
            'notes' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country_id' => 'nullable|exists:countries,id',
            'is_default_address' => 'sometimes|boolean'
        ]);
        
        DB::beginTransaction();
        
        try {
            // إنشاء العميل
            $customer = new Customer();
            $customer->name = $validated['name'];
            $customer->email = $validated['email'];
            $customer->phone = $validated['phone'];
            $customer->is_active = $request->has('is_active');
            $customer->notes = $validated['notes'] ?? null;
            $customer->save();
            
            // إنشاء العنوان إذا تم توفير البيانات
            if ($request->filled('address')) {
                $address = new Address();
                $address->customer_id = $customer->id;
                $address->address = $validated['address'];
                $address->city = $validated['city'] ?? null;
                $address->state = $validated['state'] ?? null;
                $address->postal_code = $validated['postal_code'] ?? null;
                $address->country_id = $validated['country_id'] ?? null;
                $address->is_default = $request->has('is_default_address');
                $address->save();
            }
            
            DB::commit();
            
            return redirect()->route('admin.customers.show', $customer->id)
                ->with('success', 'تم إنشاء العميل بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating customer: ' . $e->getMessage());
            
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء العميل: ' . $e->getMessage());
        }
    }

    /**
     * عرض نموذج تعديل عميل
     */
    public function edit(string $id)
    {
        $customer = Customer::with('addresses.country')->findOrFail($id);
        $countries = Country::all();
        
        return view('admin.customers.edit', compact('customer', 'countries'));
    }

    /**
     * تحديث بيانات العميل
     */
    public function update(Request $request, string $id)
    {
        $customer = Customer::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'phone' => 'required|string|max:20',
            'is_active' => 'sometimes|boolean',
            'notes' => 'nullable|string',
        ]);
        
        $customer->name = $validated['name'];
        $customer->email = $validated['email'];
        $customer->phone = $validated['phone'];
        $customer->is_active = $request->has('is_active');
        $customer->notes = $validated['notes'] ?? null;
        $customer->save();
        
        return redirect()->route('admin.customers.show', $customer->id)
            ->with('success', 'تم تحديث بيانات العميل بنجاح');
    }

    /**
     * تغيير حالة العميل (نشط/غير نشط)
     */
    public function toggleStatus(string $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->is_active = !$customer->is_active;
        $customer->save();
        
        return redirect()->back()->with('success', 'تم تغيير حالة العميل بنجاح');
    }

    /**
     * إضافة عنوان جديد للعميل
     */
    public function addAddress(Request $request, string $id)
    {
        $customer = Customer::findOrFail($id);
        
        $validated = $request->validate([
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country_id' => 'required|exists:countries,id',
            'is_default' => 'sometimes|boolean'
        ]);
        
        // إذا كان العنوان الافتراضي، قم بإلغاء تعيين العناوين الأخرى كافتراضية
        if ($request->has('is_default')) {
            $customer->addresses()->update(['is_default' => false]);
        }
        
        $address = new Address();
        $address->customer_id = $customer->id;
        $address->address = $validated['address'];
        $address->city = $validated['city'];
        $address->state = $validated['state'] ?? null;
        $address->postal_code = $validated['postal_code'] ?? null;
        $address->country_id = $validated['country_id'];
        $address->is_default = $request->has('is_default');
        $address->save();
        
        return redirect()->route('admin.customers.show', $customer->id)
            ->with('success', 'تم إضافة العنوان بنجاح');
    }

    /**
     * حذف عنوان العميل
     */
    public function deleteAddress(string $customerId, string $addressId)
    {
        $address = Address::where('customer_id', $customerId)
            ->where('id', $addressId)
            ->firstOrFail();
        
        $address->delete();
        
        return redirect()->route('admin.customers.show', $customerId)
            ->with('success', 'تم حذف العنوان بنجاح');
    }
} 