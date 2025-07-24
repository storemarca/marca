@extends('layouts.admin')

@section('title', 'تفاصيل الشحنة')
@section('header', 'تفاصيل الشحنة: ' . $shipment->tracking_number)

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center space-x-4 space-x-reverse">
            <a href="{{ route('admin.shipments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-arrow-right ml-2"></i> العودة للشحنات
            </a>
            <a href="{{ route('admin.orders.show', $shipment->order_id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-shopping-cart ml-2"></i> عرض الطلب
            </a>
            <a href="{{ route('admin.shipments.edit', $shipment->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                <i class="fas fa-edit ml-2"></i> تعديل
            </a>
        </div>
        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                @switch($shipment->status)
                    @case('processing')
                        bg-blue-100 text-blue-800
                        @break
                    @case('in_transit')
                        bg-yellow-100 text-yellow-800
                        @break
                    @case('delivered')
                        bg-green-100 text-green-800
                        @break
                    @case('failed')
                        bg-red-100 text-red-800
                        @break
                    @default
                        bg-gray-100 text-gray-800
                @endswitch
            ">
                @switch($shipment->status)
                    @case('processing')
                        قيد المعالجة
                        @break
                    @case('in_transit')
                        في الطريق
                        @break
                    @case('delivered')
                        تم التسليم
                        @break
                    @case('failed')
                        فشل التسليم
                        @break
                    @default
                        {{ $shipment->status }}
                @endswitch
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- معلومات الشحنة -->
        <div class="lg:col-span-2 space-y-6">
            <!-- معلومات الشحنة -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900">معلومات الشحنة</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">رقم التتبع</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shipment->tracking_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">شركة الشحن</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shipment->shippingCompany->name ?? 'غير متوفر' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">تاريخ التسليم المتوقع</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($shipment->expected_delivery_date)->format('Y-m-d') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">تاريخ الإنشاء</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shipment->created_at->format('Y-m-d H:i') }}</dd>
                        </div>
                        @if($shipment->notes)
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">ملاحظات</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $shipment->notes }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
            
            <!-- معلومات الطلب -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900">معلومات الطلب</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">رقم الطلب</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="{{ route('admin.orders.show', $shipment->order_id) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $shipment->order->order_number ?? 'غير متوفر' }}
                                </a>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">حالة الطلب</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @switch($shipment->order->status ?? '')
                                    @case('pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            قيد الانتظار
                                        </span>
                                        @break
                                    @case('processing')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            قيد المعالجة
                                        </span>
                                        @break
                                    @case('shipped')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            تم الشحن
                                        </span>
                                        @break
                                    @case('delivered')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            تم التسليم
                                        </span>
                                        @break
                                    @case('cancelled')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            ملغي
                                        </span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $shipment->order->status ?? 'غير متوفر' }}
                                        </span>
                                @endswitch
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">حالة الدفع</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @switch($shipment->order->payment_status ?? '')
                                    @case('pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            قيد الانتظار
                                        </span>
                                        @break
                                    @case('paid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            مدفوع
                                        </span>
                                        @break
                                    @case('failed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            فشل الدفع
                                        </span>
                                        @break
                                    @case('refunded')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            مسترجع
                                        </span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $shipment->order->payment_status ?? 'غير متوفر' }}
                                        </span>
                                @endswitch
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">طريقة الدفع</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shipment->order->payment_method ?? 'غير متوفر' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">إجمالي الطلب</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($shipment->order->grand_total ?? 0, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">تاريخ الطلب</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shipment->order->created_at ? $shipment->order->created_at->format('Y-m-d H:i') : 'غير متوفر' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
            
            <!-- معلومات العميل -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900">معلومات العميل</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">الاسم</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shipment->order->shipping_name ?? 'غير متوفر' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">البريد الإلكتروني</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shipment->order->shipping_email ?? 'غير متوفر' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">رقم الهاتف</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shipment->order->shipping_phone ?? 'غير متوفر' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">البلد</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shipment->order->shipping_country ?? 'غير متوفر' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">المدينة</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shipment->order->shipping_city ?? 'غير متوفر' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">الرمز البريدي</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shipment->order->shipping_zip ?? 'غير متوفر' }}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">العنوان</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shipment->order->shipping_address ?? 'غير متوفر' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
            
            <!-- المنتجات -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900">المنتجات</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    المنتج
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الكمية
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    السعر
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الإجمالي
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($shipment->order->items ?? [] as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0">
                                                @if($item->product && $item->product->main_image)
                                                    <img class="h-10 w-10 rounded object-cover" src="{{ asset('storage/' . $item->product->main_image) }}" alt="{{ $item->product_name }}">
                                                @else
                                                    <div class="h-10 w-10 rounded bg-gray-200 flex items-center justify-center">
                                                        <i class="fas fa-box text-gray-400"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="mr-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $item->product_name }}</div>
                                                <div class="text-xs text-gray-500">{{ $item->product_sku ?? 'SKU غير متوفر' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($item->orderItem->unit_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($item->orderItem->unit_price * $item->quantity, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                        لا توجد منتجات متاحة
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- الإجراءات -->
        <div class="lg:col-span-1 space-y-6">
            <!-- تحديث حالة الشحنة -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900">تحديث حالة الشحنة</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.shipments.update-status', $shipment->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                            <select name="status" id="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="processing" {{ $shipment->status == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                                <option value="in_transit" {{ $shipment->status == 'in_transit' ? 'selected' : '' }}>في الطريق</option>
                                <option value="delivered" {{ $shipment->status == 'delivered' ? 'selected' : '' }}>تم التسليم</option>
                                <option value="failed" {{ $shipment->status == 'failed' ? 'selected' : '' }}>فشل التسليم</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                            <textarea name="notes" id="notes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('notes', $shipment->notes) }}</textarea>
                        </div>
                        <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            تحديث الحالة
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- معلومات التحصيل -->
            @if($shipment->collection)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b">
                        <h3 class="text-lg font-medium text-gray-900">معلومات التحصيل</h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 gap-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">المبلغ</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ number_format($shipment->collection->amount, 2) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">الحالة</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @switch($shipment->collection->status)
                                        @case('pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                قيد الانتظار
                                            </span>
                                            @break
                                        @case('settled')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                تم التسوية
                                            </span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $shipment->collection->status }}
                                            </span>
                                    @endswitch
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">تاريخ التسوية</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $shipment->collection->settled_at ? \Carbon\Carbon::parse($shipment->collection->settled_at)->format('Y-m-d') : 'لم يتم التسوية بعد' }}
                                </dd>
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('admin.collections.show', $shipment->collection->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 w-full justify-center">
                                    عرض تفاصيل التحصيل
                                </a>
                            </div>
                        </dl>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush 