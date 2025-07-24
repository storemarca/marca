@extends('layouts.admin')

@section('title', 'تفاصيل العميل')
@section('header', 'تفاصيل العميل: ' . $customer->name)

@section('content')
    <!-- Customer Header Section -->
    <div class="mb-6 bg-white rounded-lg shadow-lg p-6 transform transition-all duration-300 hover:shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="flex items-center">
                <div class="h-16 w-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center transform transition-transform duration-500 hover:scale-110">
                    <span class="text-white font-semibold text-2xl">{{ substr($customer->name, 0, 1) }}</span>
                </div>
                <div class="mr-4">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $customer->name }}</h1>
                    <p class="text-sm text-gray-500">
                        {{ $customer->is_active ? 'عميل نشط' : 'عميل غير نشط' }} &bull; تم التسجيل {{ $customer->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
            <div class="mt-4 md:mt-0 flex flex-wrap gap-2">
                <a href="{{ route('admin.customers.edit', $customer->id) }}" class="bg-gradient-to-r from-yellow-400 to-yellow-500 hover:from-yellow-500 hover:to-yellow-600 text-white py-2 px-4 rounded-lg flex items-center transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-1">
                    <i class="fas fa-edit ml-1"></i> تعديل
                </a>
                <form action="{{ route('admin.customers.toggle-status', $customer->id) }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" class="{{ $customer->is_active ? 'bg-gradient-to-r from-red-400 to-red-500 hover:from-red-500 hover:to-red-600' : 'bg-gradient-to-r from-green-400 to-green-500 hover:from-green-500 hover:to-green-600' }} text-white py-2 px-4 rounded-lg flex items-center transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-1">
                        <i class="fas {{ $customer->is_active ? 'fa-user-slash' : 'fa-user-check' }} ml-1"></i>
                        {{ $customer->is_active ? 'تعطيل الحساب' : 'تفعيل الحساب' }}
                    </button>
                </form>
                <a href="{{ route('admin.customers.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 px-4 rounded-lg flex items-center transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-1">
                    <i class="fas fa-arrow-right ml-1"></i> العودة للقائمة
                </a>
            </div>
        </div>
    </div>

    <!-- إحصائيات العميل -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-lg p-6 border-r-4 border-blue-500 transform transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
            <div class="flex items-center">
                <div class="bg-gradient-to-r from-blue-400 to-blue-500 text-white p-4 rounded-full">
                    <i class="fas fa-shopping-cart text-xl"></i>
                </div>
                <div class="mr-4">
                    <h2 class="text-gray-600 text-sm">عدد الطلبات</h2>
                    <p class="text-2xl font-bold animate-pulse">{{ $orderCount }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6 border-r-4 border-green-500 transform transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
            <div class="flex items-center">
                <div class="bg-gradient-to-r from-green-400 to-green-500 text-white p-4 rounded-full">
                    <i class="fas fa-dollar-sign text-xl"></i>
                </div>
                <div class="mr-4">
                    <h2 class="text-gray-600 text-sm">إجمالي المشتريات</h2>
                    <p class="text-2xl font-bold animate-pulse">{{ number_format($totalSpent, 2) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6 border-r-4 border-purple-500 transform transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
            <div class="flex items-center">
                <div class="bg-gradient-to-r from-purple-400 to-purple-500 text-white p-4 rounded-full">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </div>
                <div class="mr-4">
                    <h2 class="text-gray-600 text-sm">آخر طلب</h2>
                    <p class="text-xl font-bold animate-pulse">{{ $lastOrderDate ? \Carbon\Carbon::parse($lastOrderDate)->format('Y-m-d') : 'لا يوجد' }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- معلومات العميل والعناوين -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- بطاقة معلومات العميل -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition-all duration-300 hover:shadow-xl">
                <div class="p-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b">
                    <h3 class="font-medium text-gray-900 flex items-center">
                        <i class="fas fa-user-circle text-blue-500 ml-2"></i>
                        معلومات العميل
                    </h3>
                </div>
                <div class="p-6">
                    <div class="mb-4 transform transition-all duration-300 hover:translate-x-2">
                        <h4 class="text-sm font-medium text-gray-500 mb-1">البريد الإلكتروني</h4>
                        <p class="text-gray-900 flex items-center p-2 bg-blue-50 rounded-md">
                            <i class="fas fa-envelope text-blue-500 ml-2"></i>
                            {{ $customer->email }}
                        </p>
                    </div>
                    
                    <div class="mb-4 transform transition-all duration-300 hover:translate-x-2">
                        <h4 class="text-sm font-medium text-gray-500 mb-1">رقم الهاتف</h4>
                        <p class="text-gray-900 flex items-center p-2 bg-green-50 rounded-md">
                            <i class="fas fa-phone text-green-500 ml-2"></i>
                            {{ $customer->phone }}
                        </p>
                    </div>
                    
                    <div class="mb-4 transform transition-all duration-300 hover:translate-x-2">
                        <h4 class="text-sm font-medium text-gray-500 mb-1">حالة الحساب</h4>
                        <p>
                            @if($customer->is_active)
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-gradient-to-r from-green-100 to-green-200 text-green-800 shadow-sm">
                                    <span class="w-2 h-2 bg-green-400 rounded-full ml-1 animate-pulse"></span> نشط
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-gradient-to-r from-red-100 to-red-200 text-red-800 shadow-sm">
                                    <span class="w-2 h-2 bg-red-400 rounded-full ml-1 animate-pulse"></span> غير نشط
                                </span>
                            @endif
                        </p>
                    </div>
                    
                    <div class="mb-4 transform transition-all duration-300 hover:translate-x-2">
                        <h4 class="text-sm font-medium text-gray-500 mb-1">تاريخ التسجيل</h4>
                        <p class="text-gray-900 flex items-center p-2 bg-purple-50 rounded-md">
                            <i class="fas fa-calendar text-purple-500 ml-2"></i>
                            {{ $customer->created_at->format('Y-m-d H:i') }}
                        </p>
                    </div>
                    
                    @if($customer->notes)
                        <div class="mb-4 transform transition-all duration-300 hover:translate-x-2">
                            <h4 class="text-sm font-medium text-gray-500 mb-1">ملاحظات</h4>
                            <p class="text-gray-900 bg-yellow-50 p-3 rounded-md border-r-2 border-yellow-300">
                                {{ $customer->notes }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- بطاقة عناوين العميل -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition-all duration-300 hover:shadow-xl">
                <div class="p-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b flex justify-between items-center">
                    <h3 class="font-medium text-gray-900 flex items-center">
                        <i class="fas fa-map-marker-alt text-red-500 ml-2"></i>
                        عناوين العميل
                    </h3>
                    <button type="button" onclick="openAddressModal()" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-plus ml-1"></i> إضافة عنوان
                    </button>
                </div>
                <div class="p-6">
                    @if($customer->addresses->isEmpty())
                        <div class="text-center py-6">
                            <div class="inline-flex rounded-full bg-yellow-100 p-3 mb-4 animate-bounce">
                                <div class="rounded-full bg-yellow-200 p-2">
                                    <i class="fas fa-map-marker-alt text-yellow-600 text-xl"></i>
                                </div>
                            </div>
                            <h3 class="text-md font-medium text-gray-900 mb-1">لا توجد عناوين مسجلة</h3>
                            <p class="text-gray-500 mb-4">لم يقم العميل بإضافة أي عناوين بعد</p>
                            <button type="button" onclick="openAddressModal()" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-600 transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-plus ml-1"></i> إضافة عنوان جديد
                            </button>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($customer->addresses as $address)
                                <div class="border rounded-lg p-4 {{ $address->is_default ? 'border-blue-300 bg-gradient-to-r from-blue-50 to-blue-100' : 'border-gray-200 hover:border-gray-300' }} transition-all duration-300 transform hover:scale-105 hover:shadow-md">
                                    @if($address->is_default)
                                        <div class="mb-2">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 shadow-sm">
                                                <i class="fas fa-check-circle ml-1"></i> العنوان الافتراضي
                                            </span>
                                        </div>
                                    @endif
                                    <p class="text-gray-900 mb-1 flex items-center">
                                        <i class="fas fa-home text-gray-500 ml-2"></i>
                                        {{ $address->address }}
                                    </p>
                                    <p class="text-gray-600 text-sm mb-2 flex items-center">
                                        <i class="fas fa-map-pin text-gray-400 ml-2"></i>
                                        {{ $address->city }}{{ $address->state ? '، ' . $address->state : '' }}
                                        {{ $address->postal_code ? ' - ' . $address->postal_code : '' }}
                                    </p>
                                    <p class="text-gray-600 text-sm mb-3 flex items-center">
                                        <i class="fas fa-globe text-gray-400 ml-2"></i>
                                        {{ $address->country->name ?? '' }}
                                    </p>
                                    <div class="flex justify-end">
                                        <form action="{{ route('admin.customers.delete-address', [$customer->id, $address->id]) }}" method="POST" class="inline-block" onsubmit="return confirm('هل أنت متأكد من حذف هذا العنوان؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm bg-red-50 hover:bg-red-100 px-3 py-1 rounded-full transition-colors duration-300">
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
        </div>
    </div>
    
    <!-- طلبات العميل -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6 transform transition-all duration-300 hover:shadow-xl">
        <div class="p-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b">
            <h3 class="font-medium text-gray-900 flex items-center">
                <i class="fas fa-shopping-cart text-indigo-500 ml-2"></i>
                طلبات العميل
            </h3>
        </div>
        
        @if($customer->orders->isEmpty())
            <div class="text-center py-12">
                <div class="inline-flex rounded-full bg-gray-100 p-3 mb-4 animate-bounce">
                    <div class="rounded-full bg-gray-200 p-2">
                        <i class="fas fa-shopping-cart text-gray-500 text-xl"></i>
                    </div>
                </div>
                <h3 class="text-md font-medium text-gray-900 mb-1">لا توجد طلبات</h3>
                <p class="text-gray-500">لم يقم العميل بإجراء أي طلبات بعد</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                رقم الطلب
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                التاريخ
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                المبلغ
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                حالة الطلب
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                حالة الدفع
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                الإجراءات
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($customer->orders as $order)
                            <tr class="hover:bg-gray-50 transition-all duration-300 transform hover:scale-[1.01]">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">#{{ $order->order_number }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $order->created_at->format('Y-m-d') }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ number_format($order->grand_total, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($order->status)
                                        @case('pending')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 shadow-sm">
                                                <span class="w-2 h-2 bg-yellow-400 rounded-full mr-1 animate-pulse"></span> قيد الانتظار
                                            </span>
                                            @break
                                        @case('processing')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 shadow-sm">
                                                <span class="w-2 h-2 bg-blue-400 rounded-full mr-1 animate-pulse"></span> قيد المعالجة
                                            </span>
                                            @break
                                        @case('shipped')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-purple-100 to-purple-200 text-purple-800 shadow-sm">
                                                <span class="w-2 h-2 bg-purple-400 rounded-full mr-1 animate-pulse"></span> تم الشحن
                                            </span>
                                            @break
                                        @case('delivered')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-green-100 to-green-200 text-green-800 shadow-sm">
                                                <span class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></span> تم التسليم
                                            </span>
                                            @break
                                        @case('cancelled')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-red-100 to-red-200 text-red-800 shadow-sm">
                                                <span class="w-2 h-2 bg-red-400 rounded-full mr-1 animate-pulse"></span> ملغي
                                            </span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 shadow-sm">
                                                <span class="w-2 h-2 bg-gray-400 rounded-full mr-1"></span> {{ $order->status }}
                                            </span>
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($order->payment_status)
                                        @case('pending')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 shadow-sm">
                                                <span class="w-2 h-2 bg-yellow-400 rounded-full mr-1 animate-pulse"></span> قيد الانتظار
                                            </span>
                                            @break
                                        @case('paid')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-green-100 to-green-200 text-green-800 shadow-sm">
                                                <span class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></span> مدفوع
                                            </span>
                                            @break
                                        @case('failed')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-red-100 to-red-200 text-red-800 shadow-sm">
                                                <span class="w-2 h-2 bg-red-400 rounded-full mr-1 animate-pulse"></span> فشل الدفع
                                            </span>
                                            @break
                                        @case('refunded')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 shadow-sm">
                                                <span class="w-2 h-2 bg-gray-400 rounded-full mr-1 animate-pulse"></span> مسترجع
                                            </span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 shadow-sm">
                                                <span class="w-2 h-2 bg-gray-400 rounded-full mr-1"></span> {{ $order->payment_status }}
                                            </span>
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                    <div class="flex items-center space-x-3 space-x-reverse">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 p-2 rounded-full hover:bg-blue-100 transition-all duration-300 transform hover:scale-110" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.orders.invoice', $order->id) }}" class="text-green-600 hover:text-green-900 bg-green-50 p-2 rounded-full hover:bg-green-100 transition-all duration-300 transform hover:scale-110" title="عرض الفاتورة">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    
    <!-- Modal إضافة عنوان -->
    <div id="addressModal" class="fixed inset-0 z-10 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full animate__animated animate__fadeInUp">
                <form action="{{ route('admin.customers.add-address', $customer->id) }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-map-marker-alt text-red-500 ml-2"></i>
                            إضافة عنوان جديد
                        </h3>
                        
                        <div class="mb-4 transform transition-all duration-300 hover:translate-x-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">العنوان</label>
                            <input type="text" name="address" id="address" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 hover:shadow">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="transform transition-all duration-300 hover:translate-x-2">
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">المدينة</label>
                                <input type="text" name="city" id="city" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 hover:shadow">
                            </div>
                            <div class="transform transition-all duration-300 hover:translate-x-2">
                                <label for="state" class="block text-sm font-medium text-gray-700 mb-1">المنطقة/المحافظة</label>
                                <input type="text" name="state" id="state" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 hover:shadow">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="transform transition-all duration-300 hover:translate-x-2">
                                <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">الرمز البريدي</label>
                                <input type="text" name="postal_code" id="postal_code" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 hover:shadow">
                            </div>
                            <div class="transform transition-all duration-300 hover:translate-x-2">
                                <label for="country_id" class="block text-sm font-medium text-gray-700 mb-1">البلد</label>
                                <select name="country_id" id="country_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 hover:shadow">
                                    <option value="">اختر البلد</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="flex items-center transform transition-all duration-300 hover:translate-x-2">
                            <input type="checkbox" name="is_default" id="is_default" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 ml-2">
                            <label for="is_default" class="text-sm text-gray-700">تعيين كعنوان افتراضي</label>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-base font-medium text-white hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-plus ml-1"></i> إضافة العنوان
                        </button>
                        <button type="button" onclick="closeAddressModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-times ml-1"></i> إلغاء
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
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