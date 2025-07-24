@extends('layouts.admin')

@section('title', 'تفاصيل شركة الشحن')
@section('header', 'تفاصيل شركة الشحن')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.shipping-companies.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-300">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة إلى قائمة شركات الشحن
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- بطاقة المعلومات الأساسية -->
        <div class="col-span-2 bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-100 to-blue-100">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">معلومات شركة الشحن</h3>
                    <div>
                        <a href="{{ route('admin.shipping-companies.edit', $shippingCompany) }}" class="inline-flex items-center px-3 py-1 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white hover:bg-indigo-700">
                            <i class="fas fa-edit ml-1"></i> تعديل
                        </a>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="flex items-center mb-6">
                    @if($shippingCompany->logo)
                        <img src="{{ asset('storage/' . $shippingCompany->logo) }}" alt="{{ $shippingCompany->name }}" class="h-16 w-auto object-contain">
                    @else
                        <div class="h-16 w-16 bg-gray-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-truck text-gray-500 text-2xl"></i>
                        </div>
                    @endif
                    <div class="mr-4">
                        <h2 class="text-xl font-bold text-gray-800">{{ $shippingCompany->name }}</h2>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $shippingCompany->code }}
                        </span>
                        <span class="px-2 mr-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $shippingCompany->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $shippingCompany->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                </div>

                @if($shippingCompany->description)
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">الوصف:</h4>
                        <p class="text-gray-600">{{ $shippingCompany->description }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($shippingCompany->website)
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-1">الموقع الإلكتروني:</h4>
                            <a href="{{ $shippingCompany->website }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                {{ $shippingCompany->website }}
                            </a>
                        </div>
                    @endif

                    @if($shippingCompany->tracking_url_template)
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-1">قالب رابط التتبع:</h4>
                            <p class="text-gray-600">{{ $shippingCompany->tracking_url_template }}</p>
                        </div>
                    @endif
                </div>

                <hr class="my-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($shippingCompany->contact_person)
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-1">جهة الاتصال:</h4>
                            <p class="text-gray-600">{{ $shippingCompany->contact_person }}</p>
                        </div>
                    @endif

                    @if($shippingCompany->contact_email)
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني:</h4>
                            <a href="mailto:{{ $shippingCompany->contact_email }}" class="text-indigo-600 hover:text-indigo-900">
                                {{ $shippingCompany->contact_email }}
                            </a>
                        </div>
                    @endif

                    @if($shippingCompany->contact_phone)
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-1">رقم الهاتف:</h4>
                            <p class="text-gray-600">{{ $shippingCompany->contact_phone }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- بطاقة الإحصائيات والإجراءات -->
        <div class="col-span-1">
            <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-100 to-blue-100">
                    <h3 class="text-lg font-medium text-gray-900">الإحصائيات</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm font-medium text-gray-700">عدد الشحنات:</span>
                        <span class="text-lg font-bold text-indigo-600">{{ $shipmentsCount }}</span>
                    </div>
                </div>
            </div>

            @if($shippingCompany->has_api_integration)
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-100 to-blue-100">
                        <h3 class="text-lg font-medium text-gray-900">تكامل API</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-600 mb-4">هذه الشركة لديها تكامل مع API للإنشاء التلقائي للشحنات وتتبعها.</p>
                        <button id="test-api-btn" data-company-id="{{ $shippingCompany->id }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300">
                            <i class="fas fa-vial ml-2"></i>
                            اختبار الاتصال بـ API
                        </button>
                        <div id="api-test-result" class="mt-4 hidden"></div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
@if($shippingCompany->has_api_integration)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const testApiBtn = document.getElementById('test-api-btn');
    const apiTestResult = document.getElementById('api-test-result');
    
    testApiBtn.addEventListener('click', function() {
        const companyId = this.getAttribute('data-company-id');
        
        // تغيير حالة الزر
        testApiBtn.disabled = true;
        testApiBtn.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري الاختبار...';
        
        // إرسال طلب اختبار API
        fetch(`/admin/shipping-companies/${companyId}/test-api`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            // عرض النتيجة
            apiTestResult.classList.remove('hidden');
            
            if (data.success) {
                apiTestResult.innerHTML = `
                    <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 rounded-md">
                        <div class="flex">
                            <div class="py-1"><i class="fas fa-check-circle text-green-500"></i></div>
                            <div class="mr-3">
                                <p class="text-sm font-medium">تم الاتصال بنجاح!</p>
                                <p class="text-xs mt-1">${data.message || 'تم إنشاء شحنة اختبارية بنجاح.'}</p>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                apiTestResult.innerHTML = `
                    <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 rounded-md">
                        <div class="flex">
                            <div class="py-1"><i class="fas fa-times-circle text-red-500"></i></div>
                            <div class="mr-3">
                                <p class="text-sm font-medium">فشل الاتصال</p>
                                <p class="text-xs mt-1">${data.message || 'حدث خطأ أثناء الاتصال بـ API.'}</p>
                            </div>
                        </div>
                    </div>
                `;
            }
        })
        .catch(error => {
            apiTestResult.classList.remove('hidden');
            apiTestResult.innerHTML = `
                <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 rounded-md">
                    <div class="flex">
                        <div class="py-1"><i class="fas fa-times-circle text-red-500"></i></div>
                        <div class="mr-3">
                            <p class="text-sm font-medium">حدث خطأ</p>
                            <p class="text-xs mt-1">فشل الاتصال بالخادم. يرجى المحاولة مرة أخرى.</p>
                        </div>
                    </div>
                </div>
            `;
        })
        .finally(() => {
            // إعادة الزر إلى حالته الطبيعية
            testApiBtn.disabled = false;
            testApiBtn.innerHTML = '<i class="fas fa-vial ml-2"></i> اختبار الاتصال بـ API';
        });
    });
});
</script>
@endif
@endpush 