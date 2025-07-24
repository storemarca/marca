@extends('layouts.admin')

@section('title', 'تقرير المشتريات')
@section('header', 'تقرير المشتريات')

@section('content')
    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6 border-r-4 border-blue-500 transition-transform duration-300 transform hover:scale-105">
            <div class="flex items-center">
                <div class="bg-blue-100 text-blue-600 p-3 rounded-full">
                    <i class="fas fa-shopping-basket text-xl"></i>
                </div>
                <div class="mr-4">
                    <h2 class="text-gray-600 text-sm">إجمالي المشتريات</h2>
                    <p class="text-2xl font-bold">{{ number_format($totalPurchases, 2) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-r-4 border-yellow-500 transition-transform duration-300 transform hover:scale-105">
            <div class="flex items-center">
                <div class="bg-yellow-100 text-yellow-600 p-3 rounded-full">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="mr-4">
                    <h2 class="text-gray-600 text-sm">مشتريات قيد الانتظار</h2>
                    <p class="text-2xl font-bold">{{ number_format($pendingPurchases, 2) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-r-4 border-green-500 transition-transform duration-300 transform hover:scale-105">
            <div class="flex items-center">
                <div class="bg-green-100 text-green-600 p-3 rounded-full">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="mr-4">
                    <h2 class="text-gray-600 text-sm">مشتريات مستلمة</h2>
                    <p class="text-2xl font-bold">{{ number_format($receivedPurchases, 2) }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- فلاتر البحث -->
    <div class="mb-6">
        <form action="{{ route('admin.reports.purchases') }}" method="GET" class="bg-white rounded-lg shadow-md p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-filter text-gray-400"></i>
                        </div>
                        <select name="status" id="status" class="w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">جميع الحالات</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="ordered" {{ request('status') == 'ordered' ? 'selected' : '' }}>تم الطلب</option>
                            <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>تم الاستلام</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">المورد</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <select name="supplier_id" id="supplier_id" class="w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">جميع الموردين</option>
                            @foreach(App\Models\Supplier::all() as $supplier)
                                <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">من تاريخ</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-calendar text-gray-400"></i>
                        </div>
                        <input type="date" name="date_from" id="date_from" class="w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ request('date_from') }}">
                    </div>
                </div>
                
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">إلى تاريخ</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-calendar text-gray-400"></i>
                        </div>
                        <input type="date" name="date_to" id="date_to" class="w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ request('date_to') }}">
                    </div>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 ml-2 transition-colors duration-300">
                        <i class="fas fa-search ml-1"></i> تصفية
                    </button>
                    <a href="{{ route('admin.reports.purchases') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-300">
                        <i class="fas fa-times ml-1"></i> إعادة تعيين
                    </a>
                </div>
            </div>
        </form>
    </div>
    
    <!-- جدول المشتريات -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 border-b bg-gray-50 flex flex-col md:flex-row justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">قائمة المشتريات</h3>
            <div class="mt-2 md:mt-0 text-sm text-gray-500">
                عدد المشتريات: <span class="font-semibold">{{ $purchaseOrders->total() }}</span>
            </div>
        </div>
        
        @if($purchaseOrders->isEmpty())
            <div class="p-8 text-center">
                <div class="inline-flex rounded-full bg-yellow-100 p-4 mb-4">
                    <div class="rounded-full bg-yellow-200 p-4">
                        <i class="fas fa-shopping-basket text-yellow-600 text-3xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">لا توجد بيانات متاحة</h3>
                <p class="text-gray-500 mb-4">لم يتم العثور على أي مشتريات تطابق معايير البحث</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                رقم الطلب
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                المورد
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                التاريخ
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                المبلغ
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
                        @foreach ($purchaseOrders as $purchaseOrder)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">#{{ $purchaseOrder->order_number }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $purchaseOrder->supplier->name ?? 'غير متوفر' }}</div>
                                    <div class="text-xs text-gray-500">{{ $purchaseOrder->supplier->email ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $purchaseOrder->created_at->format('Y-m-d') }}</div>
                                    <div class="text-xs text-gray-500">{{ $purchaseOrder->created_at->format('h:i A') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ number_format($purchaseOrder->total_amount, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($purchaseOrder->status)
                                        @case('pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <span class="w-2 h-2 bg-yellow-400 rounded-full mr-1"></span> قيد الانتظار
                                            </span>
                                            @break
                                        @case('ordered')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <span class="w-2 h-2 bg-blue-400 rounded-full mr-1"></span> تم الطلب
                                            </span>
                                            @break
                                        @case('received')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <span class="w-2 h-2 bg-green-400 rounded-full mr-1"></span> تم الاستلام
                                            </span>
                                            @break
                                        @case('cancelled')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <span class="w-2 h-2 bg-red-400 rounded-full mr-1"></span> ملغي
                                            </span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <span class="w-2 h-2 bg-gray-400 rounded-full mr-1"></span> {{ $purchaseOrder->status }}
                                            </span>
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                    <a href="{{ route('admin.purchase-orders.show', $purchaseOrder->id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 p-1.5 rounded-full hover:bg-blue-100 transition-colors duration-200" title="عرض الطلب">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4">
                {{ $purchaseOrders->links() }}
            </div>
        @endif
    </div>
@endsection 