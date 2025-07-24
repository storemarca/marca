@extends('layouts.admin')

@section('title', 'إضافة شركة شحن جديدة')
@section('header', 'إضافة شركة شحن جديدة')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.shipping-companies.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-300">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة إلى قائمة شركات الشحن
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-100 to-blue-100">
            <h3 class="text-lg font-medium text-gray-900">معلومات شركة الشحن</h3>
        </div>
        <form action="{{ route('admin.shipping-companies.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- المعلومات الأساسية -->
                <div class="col-span-1 md:col-span-2 mb-4">
                    <h4 class="text-md font-medium text-gray-800 mb-3 pb-2 border-b border-gray-200">المعلومات الأساسية</h4>
                </div>
                
                <!-- الاسم -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">اسم الشركة <span class="text-red-600">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- الكود -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">الكود <span class="text-red-600">*</span></label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">مثال: aramex, zajil, smsa, bosta (يستخدم للتكامل مع API)</p>
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- الوصف -->
                <div class="col-span-1 md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">الوصف</label>
                    <textarea name="description" id="description" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- الموقع الإلكتروني -->
                <div>
                    <label for="website" class="block text-sm font-medium text-gray-700 mb-1">الموقع الإلكتروني</label>
                    <input type="url" name="website" id="website" value="{{ old('website') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('website')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- قالب رابط التتبع -->
                <div>
                    <label for="tracking_url_template" class="block text-sm font-medium text-gray-700 mb-1">قالب رابط التتبع</label>
                    <input type="text" name="tracking_url_template" id="tracking_url_template" value="{{ old('tracking_url_template') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">مثال: https://www.aramex.com/track/results?ShipmentNumber={tracking_number} أو https://bosta.co/tracking-shipment/{tracking_number}</p>
                    @error('tracking_url_template')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- الشعار -->
                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">الشعار</label>
                    <input type="file" name="logo" id="logo" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">الحد الأقصى: 1 ميجابايت، الأنواع المسموحة: JPG, PNG, GIF</p>
                    @error('logo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- الحالة -->
                <div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" {{ old('is_active') ? 'checked' : '' }}>
                        <label for="is_active" class="mr-2 block text-sm text-gray-900">نشط</label>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">شركات الشحن النشطة فقط ستظهر للمستخدمين</p>
                </div>
                
                <!-- معلومات الاتصال -->
                <div class="col-span-1 md:col-span-2 mt-6 mb-4">
                    <h4 class="text-md font-medium text-gray-800 mb-3 pb-2 border-b border-gray-200">معلومات الاتصال</h4>
                </div>
                
                <!-- اسم جهة الاتصال -->
                <div>
                    <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-1">اسم جهة الاتصال</label>
                    <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('contact_person')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- البريد الإلكتروني -->
                <div>
                    <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني</label>
                    <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('contact_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- رقم الهاتف -->
                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-1">رقم الهاتف</label>
                    <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('contact_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- تكامل API -->
                <div class="col-span-1 md:col-span-2 mt-6 mb-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="has_api_integration" id="has_api_integration" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" {{ old('has_api_integration') ? 'checked' : '' }} onchange="toggleApiFields()">
                        <label for="has_api_integration" class="mr-2 block text-sm text-gray-900">تكامل API</label>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">حدد هذا الخيار إذا كانت الشركة تدعم التكامل مع API</p>
                </div>
                
                <!-- حقول API -->
                <div id="api_fields" class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-4 rounded-lg border border-gray-200 {{ old('has_api_integration') ? '' : 'hidden' }}">
                    <div class="col-span-1 md:col-span-2 mb-2">
                        <h4 class="text-md font-medium text-gray-800">إعدادات API</h4>
                        <p class="text-sm text-gray-500">أدخل بيانات الاعتماد الخاصة بـ API</p>
                    </div>
                    
                    <!-- حقول API المشتركة -->
                    <div>
                        <label for="api_url" class="block text-sm font-medium text-gray-700 mb-1">رابط API</label>
                        <input type="text" name="api_url" id="api_url" value="{{ old('api_url') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <div>
                        <label for="api_key" class="block text-sm font-medium text-gray-700 mb-1">مفتاح API</label>
                        <input type="text" name="api_key" id="api_key" value="{{ old('api_key') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <!-- حقول Aramex -->
                    <div class="aramex-fields col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 hidden">
                        <div class="col-span-1 md:col-span-2">
                            <h5 class="text-sm font-medium text-gray-700">إعدادات خاصة بـ Aramex</h5>
                        </div>
                        
                        <div>
                            <label for="api_username" class="block text-sm font-medium text-gray-700 mb-1">اسم المستخدم</label>
                            <input type="text" name="api_username" id="api_username" value="{{ old('api_username') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <div>
                            <label for="api_password" class="block text-sm font-medium text-gray-700 mb-1">كلمة المرور</label>
                            <input type="password" name="api_password" id="api_password" value="{{ old('api_password') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <div>
                            <label for="api_account_number" class="block text-sm font-medium text-gray-700 mb-1">رقم الحساب</label>
                            <input type="text" name="api_account_number" id="api_account_number" value="{{ old('api_account_number') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <div>
                            <label for="api_account_pin" class="block text-sm font-medium text-gray-700 mb-1">رمز الحساب</label>
                            <input type="text" name="api_account_pin" id="api_account_pin" value="{{ old('api_account_pin') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <div>
                            <label for="api_account_entity" class="block text-sm font-medium text-gray-700 mb-1">كيان الحساب</label>
                            <input type="text" name="api_account_entity" id="api_account_entity" value="{{ old('api_account_entity') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <div>
                            <label for="api_account_country_code" class="block text-sm font-medium text-gray-700 mb-1">رمز بلد الحساب</label>
                            <input type="text" name="api_account_country_code" id="api_account_country_code" value="{{ old('api_account_country_code') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <div>
                            <label for="api_version" class="block text-sm font-medium text-gray-700 mb-1">إصدار API</label>
                            <input type="text" name="api_version" id="api_version" value="{{ old('api_version', 'v1') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <div>
                            <label for="api_tracking_url" class="block text-sm font-medium text-gray-700 mb-1">رابط API للتتبع</label>
                            <input type="text" name="api_tracking_url" id="api_tracking_url" value="{{ old('api_tracking_url') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    
                    <!-- حقول SMSA -->
                    <div class="smsa-fields col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 hidden">
                        <div class="col-span-1 md:col-span-2">
                            <h5 class="text-sm font-medium text-gray-700">إعدادات خاصة بـ SMSA</h5>
                        </div>
                        
                        <div>
                            <label for="api_customer_id" class="block text-sm font-medium text-gray-700 mb-1">معرف العميل</label>
                            <input type="text" name="api_customer_id" id="api_customer_id" value="{{ old('api_customer_id') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    
                    <!-- حقول Bosta -->
                    <div class="bosta-fields col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 hidden">
                        <div class="col-span-1 md:col-span-2">
                            <h5 class="text-sm font-medium text-gray-700">إعدادات خاصة بـ Bosta</h5>
                        </div>
                        
                        <div>
                            <label for="api_base_url" class="block text-sm font-medium text-gray-700 mb-1">الرابط الأساسي للـ API</label>
                            <input type="text" name="api_base_url" id="api_base_url" value="{{ old('api_base_url', 'https://app.bosta.co/api/v0') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="mt-1 text-xs text-gray-500">الرابط الأساسي لـ API بوسطة</p>
                        </div>
                        
                        <div>
                            <label for="api_url" class="block text-sm font-medium text-gray-700 mb-1">رابط إنشاء الشحنات</label>
                            <input type="text" name="api_url" id="bosta_api_url" value="{{ old('api_url', 'https://app.bosta.co/api/v0/deliveries') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="mt-1 text-xs text-gray-500">رابط إنشاء الشحنات في بوسطة</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 text-left">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    إضافة شركة الشحن
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    function toggleApiFields() {
        const hasApiIntegration = document.getElementById('has_api_integration').checked;
        const apiFields = document.getElementById('api_fields');
        
        if (hasApiIntegration) {
            apiFields.classList.remove('hidden');
        } else {
            apiFields.classList.add('hidden');
        }
        
        updateApiFields();
    }
    
    function updateApiFields() {
        const code = document.getElementById('code').value.toLowerCase();
        const aramexFields = document.querySelector('.aramex-fields');
        const smsaFields = document.querySelector('.smsa-fields');
        const bostaFields = document.querySelector('.bosta-fields');
        
        // إخفاء جميع الحقول الخاصة
        aramexFields.classList.add('hidden');
        smsaFields.classList.add('hidden');
        bostaFields.classList.add('hidden');
        
        // إظهار الحقول المناسبة بناءً على الكود
        if (code === 'aramex') {
            aramexFields.classList.remove('hidden');
        } else if (code === 'smsa') {
            smsaFields.classList.remove('hidden');
        } else if (code === 'bosta') {
            bostaFields.classList.remove('hidden');
        }
    }
    
    // إضافة مستمع لتغيير الكود
    document.getElementById('code').addEventListener('input', updateApiFields);
    
    // تحديث الحقول عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        toggleApiFields();
    });
</script>
@endpush 