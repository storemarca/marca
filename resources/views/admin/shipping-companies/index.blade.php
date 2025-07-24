@extends('layouts.admin')

@section('title', 'إدارة شركات الشحن')
@section('header', 'إدارة شركات الشحن')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">قائمة شركات الشحن</h2>
            <p class="text-gray-500">إدارة شركات الشحن وإعدادات التكامل مع API</p>
        </div>
        <a href="{{ route('admin.shipping-companies.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300">
            <i class="fas fa-plus ml-2"></i>
            إضافة شركة شحن جديدة
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 mb-6 rounded-md">
            <div class="flex">
                <div class="py-1"><i class="fas fa-check-circle text-green-500"></i></div>
                <div class="ml-3">
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 mb-6 rounded-md">
            <div class="flex">
                <div class="py-1"><i class="fas fa-times-circle text-red-500"></i></div>
                <div class="ml-3">
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الشعار
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الاسم
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الكود
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            تكامل API
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الحالة
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الإجراءات
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($shippingCompanies as $company)
                        <tr class="hover:bg-gray-50 transition-all duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($company->logo)
                                    <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}" class="h-10 w-auto">
                                @else
                                    <div class="h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                        <i class="fas fa-truck text-gray-500"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $company->name }}</div>
                                @if($company->website)
                                    <div class="text-sm text-gray-500">
                                        <a href="{{ $company->website }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $company->website }}
                                        </a>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $company->code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($company->has_api_integration)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle ml-1"></i> متوفر
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        <i class="fas fa-times-circle ml-1"></i> غير متوفر
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($company->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        نشط
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        غير نشط
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-3 space-x-reverse">
                                    <a href="{{ route('admin.shipping-companies.show', $company) }}" class="text-indigo-600 hover:text-indigo-900" title="عرض التفاصيل">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.shipping-companies.edit', $company) }}" class="text-yellow-600 hover:text-yellow-900" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($company->has_api_integration)
                                        <button type="button" 
                                            onclick="testApi({{ $company->id }})" 
                                            class="text-blue-600 hover:text-blue-900" 
                                            title="اختبار API">
                                            <i class="fas fa-vial"></i>
                                        </button>
                                    @endif
                                    <form action="{{ route('admin.shipping-companies.destroy', $company) }}" method="POST" class="inline-block" onsubmit="return confirm('هل أنت متأكد من حذف هذه الشركة؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                لا توجد شركات شحن مضافة حتى الآن
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- نافذة منبثقة لعرض نتائج اختبار API -->
    <div id="api-test-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">نتائج اختبار API</h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 max-h-96 overflow-y-auto">
                <div id="api-test-loading" class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-indigo-600 text-3xl mb-4"></i>
                    <p>جاري اختبار الاتصال بـ API...</p>
                </div>
                <div id="api-test-results" class="hidden">
                    <div id="api-test-success" class="hidden">
                        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 mb-4 rounded-md">
                            <div class="flex">
                                <div class="py-1"><i class="fas fa-check-circle text-green-500"></i></div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium">تم الاتصال بنجاح!</p>
                                    <p class="text-sm" id="api-success-message"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="api-test-error" class="hidden">
                        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 mb-4 rounded-md">
                            <div class="flex">
                                <div class="py-1"><i class="fas fa-times-circle text-red-500"></i></div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium">فشل الاتصال!</p>
                                    <p class="text-sm" id="api-error-message"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h4 class="font-medium text-gray-700 mb-2">تفاصيل الاستجابة:</h4>
                        <pre id="api-response-details" class="bg-gray-100 p-4 rounded-md text-xs overflow-x-auto max-h-60 text-gray-800 font-mono"></pre>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 text-right">
                <button type="button" onclick="closeModal()" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    إغلاق
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function testApi(companyId) {
        // عرض النافذة المنبثقة
        document.getElementById('api-test-modal').classList.remove('hidden');
        document.getElementById('api-test-loading').classList.remove('hidden');
        document.getElementById('api-test-results').classList.add('hidden');
        
        // إرسال طلب اختبار API
        fetch(`{{ url('admin/shipping-companies') }}/${companyId}/test-api`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            // إخفاء التحميل وعرض النتائج
            document.getElementById('api-test-loading').classList.add('hidden');
            document.getElementById('api-test-results').classList.remove('hidden');
            
            // عرض النتائج بناءً على نجاح أو فشل الاختبار
            if (data.success) {
                document.getElementById('api-test-success').classList.remove('hidden');
                document.getElementById('api-test-error').classList.add('hidden');
                document.getElementById('api-success-message').textContent = data.message;
            } else {
                document.getElementById('api-test-success').classList.add('hidden');
                document.getElementById('api-test-error').classList.remove('hidden');
                document.getElementById('api-error-message').textContent = data.message;
            }
            
            // عرض تفاصيل الاستجابة
            document.getElementById('api-response-details').textContent = JSON.stringify(data, null, 2);
        })
        .catch(error => {
            // إخفاء التحميل وعرض رسالة الخطأ
            document.getElementById('api-test-loading').classList.add('hidden');
            document.getElementById('api-test-results').classList.remove('hidden');
            document.getElementById('api-test-success').classList.add('hidden');
            document.getElementById('api-test-error').classList.remove('hidden');
            document.getElementById('api-error-message').textContent = 'حدث خطأ أثناء الاتصال بالخادم';
            document.getElementById('api-response-details').textContent = error.toString();
        });
    }
    
    function closeModal() {
        document.getElementById('api-test-modal').classList.add('hidden');
    }
</script>
@endpush 