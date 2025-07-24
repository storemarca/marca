@extends('layouts.admin')

@section('title', 'فاتورة الطلب')
@section('header', 'فاتورة الطلب: ' . $order->order_number)

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center space-x-4 space-x-reverse">
            <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-arrow-right ml-2"></i> العودة للطلب
            </a>
            <a href="{{ route('admin.orders.downloadInvoice', $order->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-download ml-2"></i> تحميل PDF
            </a>
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <i class="fas fa-print ml-2"></i> طباعة
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden print:shadow-none" id="invoice-print">
        <!-- رأس الفاتورة -->
        <div class="p-6 border-b">
            <div class="flex flex-col md:flex-row md:justify-between md:items-start">
                <div class="mb-6 md:mb-0">
                    <h1 class="text-2xl font-bold text-gray-900 mb-1">فاتورة</h1>
                    <p class="text-gray-600">رقم الفاتورة: INV-{{ $order->order_number }}</p>
                    <p class="text-gray-600">تاريخ الطلب: {{ $order->created_at->format('Y-m-d') }}</p>
                    <p class="text-gray-600">رقم الطلب: {{ $order->order_number }}</p>
                </div>
                <div class="text-left">
                    <div class="text-2xl font-bold text-gray-900 mb-1">{{ config('app.name') }}</div>
                    <p class="text-gray-600">info@marca.com</p>
                    <p class="text-gray-600">+966 12 345 6789</p>
                </div>
            </div>
        </div>
        
        <!-- معلومات العميل والشحن -->
        <div class="p-6 border-b">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">معلومات العميل</h3>
                    <p class="font-semibold">{{ $order->shipping_name }}</p>
                    <p class="text-gray-600">{{ $order->shipping_email }}</p>
                    <p class="text-gray-600">{{ $order->shipping_phone }}</p>
                    <p class="text-gray-600">{{ $order->formattedShippingAddress }}</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">عنوان الشحن</h3>
                    <p class="text-gray-600">{{ $order->shipping_address }}</p>
                    <p class="text-gray-600">{{ $order->shipping_city }}, {{ $order->shipping_country }}</p>
                    @if($order->shipping_zip)
                        <p class="text-gray-600">{{ $order->shipping_zip }}</p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- المنتجات -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            المنتج
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            السعر
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الكمية
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الإجمالي
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $item->product_name }}</div>
                                <div class="text-xs text-gray-500">{{ $item->product_sku ?? 'SKU غير متوفر' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($item->unit_price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($item->unit_price * $item->quantity, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-left text-sm font-medium text-gray-500">
                            المجموع الفرعي
                        </td>
                        <td class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                            {{ number_format($order->subtotal, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-left text-sm font-medium text-gray-500">
                            الشحن
                        </td>
                        <td class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                            {{ number_format($order->shipping_cost, 2) }}
                        </td>
                    </tr>
                    @if($order->discount > 0)
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-left text-sm font-medium text-gray-500">
                                الخصم
                            </td>
                            <td class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                                - {{ number_format($order->discount, 2) }}
                            </td>
                        </tr>
                    @endif
                    @if($order->tax > 0)
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-left text-sm font-medium text-gray-500">
                                {{ setting('tax_name', 'ضريبة القيمة المضافة') }} ({{ setting('tax_percentage', 15) }}%)
                            </td>
                            <td class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                                {{ number_format($order->tax, 2) }}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-left text-sm font-bold text-gray-900">
                            الإجمالي
                        </td>
                        <td class="px-6 py-3 text-right text-sm font-bold text-gray-900">
                            {{ number_format($order->grand_total, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <!-- معلومات الدفع والشحن -->
        <div class="p-6 border-t">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">معلومات الدفع</h3>
                    <p class="text-gray-600">طريقة الدفع: {{ $order->payment_method }}</p>
                    <p class="text-gray-600">حالة الدفع: 
                        @switch($order->payment_status)
                            @case('pending')
                                قيد الانتظار
                                @break
                            @case('paid')
                                تم الدفع
                                @break
                            @case('failed')
                                فشل الدفع
                                @break
                            @case('refunded')
                                تم الاسترجاع
                                @break
                            @default
                                {{ $order->payment_status }}
                        @endswitch
                    </p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">معلومات الشحن</h3>
                    <p class="text-gray-600">طريقة الشحن: {{ $order->shipping_method ?? 'التوصيل القياسي' }}</p>
                    @if($order->tracking_number)
                        <p class="text-gray-600">رقم التتبع: {{ $order->tracking_number }}</p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- تذييل الفاتورة -->
        <div class="p-6 border-t text-center">
            <p class="text-gray-600 mb-1">شكراً لطلبك من {{ config('app.name') }}</p>
            <p class="text-gray-500 text-sm">إذا كان لديك أي استفسارات، يرجى التواصل معنا على info@marca.com</p>
            @if(setting('tax_number'))
                <p class="text-gray-500 text-sm mt-2">{{ setting('tax_name', 'ضريبة القيمة المضافة') }} - الرقم الضريبي: {{ setting('tax_number') }}</p>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #invoice-print, #invoice-print * {
                visibility: visible;
            }
            #invoice-print {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .print\:hidden {
                display: none;
            }
            .print\:shadow-none {
                box-shadow: none !important;
            }
        }
    </style>
@endpush 