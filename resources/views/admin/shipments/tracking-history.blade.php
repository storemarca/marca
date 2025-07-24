@extends('layouts.admin')

@section('title', 'تاريخ تتبع الشحنة')
@section('header', 'تاريخ تتبع الشحنة #' . $shipment->id)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.shipments.show', $shipment->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-300">
        <i class="fas fa-arrow-right ml-2"></i>
        العودة إلى تفاصيل الشحنة
    </a>
</div>

<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">معلومات الشحنة</h3>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $shipment->status_color }}">
                <i class="{{ $shipment->status_icon }} ml-1"></i>
                {{ $shipment->status_text }}
            </span>
        </div>
    </div>
    
    <div class="p-6 border-b border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">رقم الطلب</h4>
                <p class="text-base font-medium">
                    <a href="{{ route('admin.orders.show', $shipment->order_id) }}" class="text-blue-600 hover:text-blue-800">
                        {{ $shipment->order->order_number }}
                    </a>
                </p>
            </div>
            
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">رقم التتبع</h4>
                <p class="text-base font-medium">
                    @if($shipment->tracking_number)
                        {{ $shipment->tracking_number }}
                        @if($shipment->tracking_url)
                            <a href="{{ $shipment->tracking_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 mr-2">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        @endif
                    @else
                        <span class="text-gray-400">غير متوفر</span>
                    @endif
                </p>
            </div>
            
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">شركة الشحن</h4>
                <p class="text-base font-medium">
                    {{ $shipment->shippingCompany->name ?? 'غير متوفر' }}
                </p>
            </div>
        </div>
    </div>
    
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <h4 class="text-md font-medium text-gray-800">تاريخ التتبع</h4>
    </div>
    
    <div class="p-6">
        @if($shipment->tracking_history && count($shipment->tracking_history) > 0)
            <div class="relative">
                <!-- خط التتبع العمودي -->
                <div class="absolute top-0 bottom-0 left-6 w-0.5 bg-blue-200 z-0"></div>
                
                <div class="space-y-6 relative z-10">
                    @foreach($shipment->tracking_history as $index => $entry)
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-full flex items-center justify-center {{ $index === 0 ? 'bg-blue-500 text-white' : 'bg-blue-100 text-blue-500' }} shadow-md">
                                    @switch($entry['status'])
                                        @case('pending')
                                            <i class="fas fa-clock"></i>
                                            @break
                                        @case('processing')
                                            <i class="fas fa-cog"></i>
                                            @break
                                        @case('shipped')
                                            <i class="fas fa-truck-loading"></i>
                                            @break
                                        @case('in_transit')
                                            <i class="fas fa-shipping-fast"></i>
                                            @break
                                        @case('out_for_delivery')
                                            <i class="fas fa-truck"></i>
                                            @break
                                        @case('delivered')
                                            <i class="fas fa-check-circle"></i>
                                            @break
                                        @case('failed')
                                            <i class="fas fa-times-circle"></i>
                                            @break
                                        @case('returned')
                                            <i class="fas fa-undo"></i>
                                            @break
                                        @default
                                            <i class="fas fa-circle"></i>
                                    @endswitch
                                </div>
                            </div>
                            <div class="mr-4 bg-white rounded-lg border border-gray-200 shadow-sm p-4 w-full">
                                <div class="flex items-center justify-between mb-2">
                                    <h5 class="text-base font-medium text-gray-900">
                                        @switch($entry['status'])
                                            @case('pending')
                                                قيد الانتظار
                                                @break
                                            @case('processing')
                                                قيد المعالجة
                                                @break
                                            @case('shipped')
                                                تم الشحن
                                                @break
                                            @case('in_transit')
                                                في الطريق
                                                @break
                                            @case('out_for_delivery')
                                                خارج للتسليم
                                                @break
                                            @case('delivered')
                                                تم التسليم
                                                @break
                                            @case('failed')
                                                فشل التسليم
                                                @break
                                            @case('returned')
                                                مرتجع
                                                @break
                                            @default
                                                {{ $entry['status'] }}
                                        @endswitch
                                    </h5>
                                    <span class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($entry['timestamp'])->format('Y-m-d H:i') }}
                                    </span>
                                </div>
                                @if(isset($entry['description']) && $entry['description'])
                                    <p class="text-sm text-gray-600">{{ $entry['description'] }}</p>
                                @endif
                                
                                @if(isset($entry['details']) && !empty($entry['details']))
                                    <div class="mt-2 pt-2 border-t border-gray-100">
                                        @foreach($entry['details'] as $key => $value)
                                            @if($value && $key != 'notes')
                                                <div class="flex justify-between text-xs">
                                                    <span class="text-gray-500">{{ $key }}:</span>
                                                    <span class="text-gray-800">{{ $value }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                        
                                        @if(isset($entry['details']['notes']) && $entry['details']['notes'])
                                            <div class="mt-2 text-xs text-gray-600 bg-gray-50 p-2 rounded">
                                                <span class="font-medium">ملاحظات:</span> {{ $entry['details']['notes'] }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-yellow-50 border-r-4 border-yellow-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-yellow-400"></i>
                    </div>
                    <div class="mr-3">
                        <p class="text-sm text-yellow-700">
                            لا يوجد سجل تتبع متاح لهذه الشحنة.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@if($shipment->shippingCompany && $shipment->shippingCompany->has_api_integration && $shipment->tracking_number)
<div class="mt-6">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
            <h3 class="text-lg font-medium text-gray-900">تحديث معلومات التتبع</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.shipments.refresh-tracking', $shipment->id) }}" method="POST" class="flex justify-center">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-sync-alt ml-2"></i>
                    تحديث معلومات التتبع من API
                </button>
            </form>
        </div>
    </div>
</div>
@endif
@endsection 