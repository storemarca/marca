@extends('layouts.admin')

@section('title', 'تعديل بيانات العميل')
@section('header', 'تعديل بيانات العميل: ' . $customer->name)

@section('content')
    <div class="mb-6 flex flex-wrap gap-2">
        <a href="{{ route('admin.customers.show', $customer->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-300">
            <i class="fas fa-arrow-right ml-1"></i> العودة للتفاصيل
        </a>
        <a href="{{ route('admin.customers.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-300">
            <i class="fas fa-list ml-1"></i> قائمة العملاء
        </a>
    </div>
    
    <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="p-4 bg-gray-50 border-b">
                <h3 class="font-medium text-gray-900">بيانات العميل</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">الاسم الكامل <span class="text-red-600">*</span></label>
                        <input type="text" name="name" id="name" required value="{{ old('name', $customer->name) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني <span class="text-red-600">*</span></label>
                        <input type="email" name="email" id="email" required value="{{ old('email', $customer->email) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">رقم الهاتف <span class="text-red-600">*</span></label>
                        <input type="text" name="phone" id="phone" required value="{{ old('phone', $customer->phone) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex items-center mt-6">
                        <input type="checkbox" name="is_active" id="is_active" {{ $customer->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 ml-2">
                        <label for="is_active" class="text-sm text-gray-700">حساب نشط</label>
                    </div>
                </div>
                
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                    <textarea name="notes" id="notes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('notes', $customer->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- عناوين العميل -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                <h3 class="font-medium text-gray-900">عناوين العميل</h3>
                <button type="button" onclick="openAddressModal()" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-plus ml-1"></i> إضافة عنوان
                </button>
            </div>
            <div class="p-6">
                @if($customer->addresses->isEmpty())
                    <div class="text-center py-6">
                        <div class="inline-flex rounded-full bg-yellow-100 p-3 mb-4">
                            <div class="rounded-full bg-yellow-200 p-2">
                                <i class="fas fa-map-marker-alt text-yellow-600 text-xl"></i>
                            </div>
                        </div>
                        <h3 class="text-md font-medium text-gray-900 mb-1">لا توجد عناوين مسجلة</h3>
                        <p class="text-gray-500 mb-4">لم يقم العميل بإضافة أي عناوين بعد</p>
                        <button type="button" onclick="openAddressModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-600 transition">
                            <i class="fas fa-plus ml-1"></i> إضافة عنوان جديد
                        </button>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($customer->addresses as $address)
                            <div class="border rounded-lg p-4 {{ $address->is_default ? 'border-blue-300 bg-blue-50' : 'border-gray-200' }}">
                                @if($address->is_default)
                                    <div class="mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            العنوان الافتراضي
                                        </span>
                                    </div>
                                @endif
                                <p class="text-gray-900 mb-1">{{ $address->address }}</p>
                                <p class="text-gray-600 text-sm mb-2">
                                    {{ $address->city }}{{ $address->state ? '، ' . $address->state : '' }}
                                    {{ $address->postal_code ? ' - ' . $address->postal_code : '' }}
                                </p>
                                <p class="text-gray-600 text-sm mb-3">{{ $address->country->name ?? '' }}</p>
                                <div class="flex justify-end">
                                    <form action="{{ route('admin.customers.delete-address', [$customer->id, $address->id]) }}" method="POST" class="inline-block" onsubmit="return confirm('هل أنت متأكد من حذف هذا العنوان؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm">
                                            <i class="fas fa-trash ml-1"></i> حذف
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        
        <div class="mt-6 flex justify-end">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-300">
                <i class="fas fa-save ml-1"></i> حفظ التغييرات
            </button>
            <a href="{{ route('admin.customers.show', $customer->id) }}" class="mr-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-300">
                إلغاء
            </a>
        </div>
    </form>
    
    <!-- Modal إضافة عنوان -->
    <div id="addressModal" class="fixed inset-0 z-10 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('admin.customers.add-address', $customer->id) }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">إضافة عنوان جديد</h3>
                        
                        <div class="mb-4">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">العنوان <span class="text-red-600">*</span></label>
                            <input type="text" name="address" id="address" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">المدينة <span class="text-red-600">*</span></label>
                                <input type="text" name="city" id="city" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="state" class="block text-sm font-medium text-gray-700 mb-1">المنطقة/المحافظة</label>
                                <input type="text" name="state" id="state" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">الرمز البريدي</label>
                                <input type="text" name="postal_code" id="postal_code" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="country_id" class="block text-sm font-medium text-gray-700 mb-1">البلد <span class="text-red-600">*</span></label>
                                <select name="country_id" id="country_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">اختر البلد</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" name="is_default" id="is_default" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 ml-2">
                            <label for="is_default" class="text-sm text-gray-700">تعيين كعنوان افتراضي</label>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            إضافة العنوان
                        </button>
                        <button type="button" onclick="closeAddressModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            إلغاء
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush

@push('scripts')
<script>
    function openAddressModal() {
        document.getElementById('addressModal').classList.remove('hidden');
    }
    
    function closeAddressModal() {
        document.getElementById('addressModal').classList.add('hidden');
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('addressModal');
        if (event.target == modal) {
            closeAddressModal();
        }
    }
</script>
@endpush 