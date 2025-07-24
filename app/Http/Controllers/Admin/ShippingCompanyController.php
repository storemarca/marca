<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShippingCompanyController extends Controller
{
    /**
     * عرض قائمة شركات الشحن
     */
    public function index()
    {
        $shippingCompanies = ShippingCompany::orderBy('name')->get();
        
        return view('admin.shipping-companies.index', compact('shippingCompanies'));
    }

    /**
     * عرض نموذج إنشاء شركة شحن جديدة
     */
    public function create()
    {
        return view('admin.shipping-companies.create');
    }

    /**
     * حفظ شركة شحن جديدة
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:shipping_companies',
            'description' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'tracking_url_template' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:1024', // 1MB max
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'has_api_integration' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // معالجة الشعار إذا تم تحميله
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('shipping-companies', 'public');
            $validated['logo'] = $logoPath;
        }

        // تعيين القيم الافتراضية
        $validated['has_api_integration'] = $request->has('has_api_integration');
        $validated['is_active'] = $request->has('is_active');
        
        // إعداد بيانات API إذا كان لديها تكامل API
        if ($validated['has_api_integration']) {
            $apiCredentials = [];
            
            // تحديد نوع API بناءً على الكود
            switch ($validated['code']) {
                case 'aramex':
                    $apiCredentials = [
                        'username' => $request->input('api_username'),
                        'password' => $request->input('api_password'),
                        'account_number' => $request->input('api_account_number'),
                        'account_pin' => $request->input('api_account_pin'),
                        'account_entity' => $request->input('api_account_entity'),
                        'account_country_code' => $request->input('api_account_country_code'),
                        'version' => $request->input('api_version', 'v1'),
                        'api_url' => $request->input('api_url'),
                        'tracking_api_url' => $request->input('api_tracking_url'),
                    ];
                    break;
                    
                case 'zajil':
                    $apiCredentials = [
                        'api_key' => $request->input('api_key'),
                        'api_url' => $request->input('api_url'),
                    ];
                    break;
                    
                case 'smsa':
                    $apiCredentials = [
                        'api_key' => $request->input('api_key'),
                        'api_url' => $request->input('api_url'),
                        'customer_id' => $request->input('api_customer_id'),
                    ];
                    break;
                    
                case 'bosta':
                    $apiCredentials = [
                        'api_key' => $request->input('api_key'),
                        'api_url' => $request->input('api_url', 'https://app.bosta.co/api/v0/deliveries'),
                        'api_base_url' => $request->input('api_base_url', 'https://app.bosta.co/api/v0'),
                    ];
                    break;
                    
                default:
                    $apiCredentials = [
                        'api_key' => $request->input('api_key'),
                        'api_url' => $request->input('api_url'),
                    ];
            }
            
            $validated['api_credentials'] = $apiCredentials;
        }

        ShippingCompany::create($validated);

        return redirect()->route('admin.shipping-companies.index')
            ->with('success', 'تم إنشاء شركة الشحن بنجاح');
    }

    /**
     * عرض تفاصيل شركة شحن
     */
    public function show(ShippingCompany $shippingCompany)
    {
        // تحميل عدد الشحنات المرتبطة بالشركة
        $shipmentsCount = $shippingCompany->shipments()->count();
        
        return view('admin.shipping-companies.show', compact('shippingCompany', 'shipmentsCount'));
    }

    /**
     * عرض نموذج تعديل شركة شحن
     */
    public function edit(ShippingCompany $shippingCompany)
    {
        return view('admin.shipping-companies.edit', compact('shippingCompany'));
    }

    /**
     * تحديث بيانات شركة شحن
     */
    public function update(Request $request, ShippingCompany $shippingCompany)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:shipping_companies,code,' . $shippingCompany->id,
            'description' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'tracking_url_template' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:1024', // 1MB max
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'has_api_integration' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // معالجة الشعار إذا تم تحميله
        if ($request->hasFile('logo')) {
            // حذف الشعار القديم إذا كان موجودًا
            if ($shippingCompany->logo) {
                Storage::disk('public')->delete($shippingCompany->logo);
            }
            
            $logoPath = $request->file('logo')->store('shipping-companies', 'public');
            $validated['logo'] = $logoPath;
        }

        // تعيين القيم الافتراضية
        $validated['has_api_integration'] = $request->has('has_api_integration');
        $validated['is_active'] = $request->has('is_active');
        
        // إعداد بيانات API إذا كان لديها تكامل API
        if ($validated['has_api_integration']) {
            $apiCredentials = [];
            
            // تحديد نوع API بناءً على الكود
            switch ($validated['code']) {
                case 'aramex':
                    $apiCredentials = [
                        'username' => $request->input('api_username'),
                        'password' => $request->input('api_password'),
                        'account_number' => $request->input('api_account_number'),
                        'account_pin' => $request->input('api_account_pin'),
                        'account_entity' => $request->input('api_account_entity'),
                        'account_country_code' => $request->input('api_account_country_code'),
                        'version' => $request->input('api_version', 'v1'),
                        'api_url' => $request->input('api_url'),
                        'tracking_api_url' => $request->input('api_tracking_url'),
                    ];
                    break;
                    
                case 'zajil':
                    $apiCredentials = [
                        'api_key' => $request->input('api_key'),
                        'api_url' => $request->input('api_url'),
                    ];
                    break;
                    
                case 'smsa':
                    $apiCredentials = [
                        'api_key' => $request->input('api_key'),
                        'api_url' => $request->input('api_url'),
                        'customer_id' => $request->input('api_customer_id'),
                    ];
                    break;
                    
                case 'bosta':
                    $apiCredentials = [
                        'api_key' => $request->input('api_key'),
                        'api_url' => $request->input('api_url', 'https://app.bosta.co/api/v0/deliveries'),
                        'api_base_url' => $request->input('api_base_url', 'https://app.bosta.co/api/v0'),
                    ];
                    break;
                    
                default:
                    $apiCredentials = [
                        'api_key' => $request->input('api_key'),
                        'api_url' => $request->input('api_url'),
                    ];
            }
            
            $validated['api_credentials'] = $apiCredentials;
        }

        $shippingCompany->update($validated);

        return redirect()->route('admin.shipping-companies.index')
            ->with('success', 'تم تحديث شركة الشحن بنجاح');
    }

    /**
     * حذف شركة شحن
     */
    public function destroy(ShippingCompany $shippingCompany)
    {
        // التحقق من عدم وجود شحنات مرتبطة
        if ($shippingCompany->shipments()->count() > 0) {
            return redirect()->route('admin.shipping-companies.index')
                ->with('error', 'لا يمكن حذف شركة الشحن لأنها مرتبطة بشحنات');
        }
        
        // حذف الشعار إذا كان موجودًا
        if ($shippingCompany->logo) {
            Storage::disk('public')->delete($shippingCompany->logo);
        }
        
        $shippingCompany->delete();

        return redirect()->route('admin.shipping-companies.index')
            ->with('success', 'تم حذف شركة الشحن بنجاح');
    }
    
    /**
     * اختبار تكامل API لشركة الشحن
     */
    public function testApi(Request $request, ShippingCompany $shippingCompany)
    {
        if (!$shippingCompany->has_api_integration) {
            return response()->json([
                'success' => false,
                'message' => 'هذه الشركة ليس لديها تكامل API'
            ]);
        }
        
        try {
            // بيانات اختبار للشحنة
            $testData = [
                'order_number' => 'TEST-' . Str::random(8),
                'sender_name' => 'Test Sender',
                'sender_phone' => '+966500000000',
                'sender_email' => 'test@example.com',
                'sender_address_line1' => 'Test Address Line 1',
                'sender_city' => 'Riyadh',
                'sender_state' => 'Riyadh',
                'sender_postal_code' => '12345',
                'sender_country_code' => 'SA',
                'receiver_name' => 'Test Receiver',
                'receiver_phone' => '+966500000001',
                'receiver_email' => 'receiver@example.com',
                'receiver_address_line1' => 'Test Receiver Address',
                'receiver_city' => 'Jeddah',
                'receiver_state' => 'Makkah',
                'receiver_postal_code' => '23456',
                'receiver_country_code' => 'SA',
                'weight' => 1,
                'length' => 10,
                'width' => 10,
                'height' => 10,
                'quantity' => 1,
                'description' => 'Test Shipment',
                'is_cod' => false,
                'cod_amount' => 0,
                'currency' => 'SAR',
            ];
            
            // إضافة بيانات خاصة بكل شركة
            switch ($shippingCompany->code) {
                case 'aramex':
                    $testData['sender_company'] = 'Test Company';
                    break;
                    
                case 'zajil':
                    $testData['receiver_city_id'] = 1;
                    $testData['receiver_region_id'] = 1;
                    $testData['sender_city_id'] = 1;
                    $testData['destination_branch_id'] = 181;
                    break;
                    
                case 'smsa':
                    $testData['service_type'] = 'DLV';
                    break;
                    
                case 'bosta':
                    $testData['receiver_city_code'] = 'EG-01'; // Cairo
                    $testData['receiver_zone'] = 'Nasr City';
                    break;
            }
            
            // محاولة إنشاء شحنة اختبارية
            $result = $shippingCompany->createShipmentViaApi($testData);
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء اختبار API: ' . $e->getMessage(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
} 