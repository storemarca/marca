@extends('layouts.user')

@section('title', 'تفاصيل طلب الإرجاع')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">تفاصيل طلب الإرجاع</h1>
            <p class="text-gray-600 mt-2">طلب إرجاع رقم #{{ $return->id }}</p>
        </div>
        <div>
            <a href="{{ route('user.returns.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                العودة للقائمة
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800 p-4">معلومات الطلب</h2>
            </div>
            <div class="p-4">
                <p class="text-gray-600 mb-2">رقم طلب الإرجاع: <span class="font-semibold text-gray-800">#{{ $return->id }}</span></p>
                <p class="text-gray-600 mb-2">رقم الطلب الأصلي: <span class="font-semibold text-gray-800">#{{ $return->order->id }}</span></p>
                <p class="text-gray-600 mb-2">تاريخ الطلب: <span class="font-semibold text-gray-800">{{ $return->created_at->format('Y-m-d') }}</span></p>
                <p class="text-gray-600 mb-2">الوقت: <span class="font-semibold text-gray-800">{{ $return->created_at->format('h:i A') }}</span></p>
                <p class="text-gray-600 mb-2">عدد المنتجات: <span class="font-semibold text-gray-800">{{ $return->items->count() }}</span></p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800 p-4">حالة الطلب</h2>
            </div>
            <div class="p-4">
                <div class="mb-4">
                    <p class="text-gray-600 mb-2">الحالة الحالية:</p>
                    @switch($return->status)
                        @case('pending')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                قيد الانتظار
                            </span>
                            @break
                        @case('approved')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                تمت الموافقة
                            </span>
                            @break
                        @case('rejected')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                مرفوض
                            </span>
                            @break
                        @case('completed')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                مكتمل
                            </span>
                            @break
                        @case('cancelled')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                ملغي
                            </span>
                            @break
                        @default
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ $return->status }}
                            </span>
                    @endswitch
                </div>
                
                <div class="mb-4">
                    <p class="text-gray-600 mb-2">طريقة الإرجاع:</p>
                    <p class="font-semibold text-gray-800">
                        @switch($return->return_method)
                            @case('refund')
                                استرداد المبلغ
                                @break
                            @case('exchange')
                                استبدال المنتج
                                @break
                            @case('store_credit')
                                رصيد في المتجر
                                @break
                            @default
                                {{ $return->return_method }}
                        @endswitch
                    </p>
                </div>
                
                @if($return->status === 'pending')
                    <div class="mt-6">
                        <form action="{{ route('user.returns.cancel', $return) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" onclick="return confirm('هل أنت متأكد من رغبتك في إلغاء طلب الإرجاع؟')">
                                إلغاء طلب الإرجاع
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800 p-4">معلومات إضافية</h2>
            </div>
            <div class="p-4">
                <div class="mb-4">
                    <p class="text-gray-600 mb-1">سبب الإرجاع:</p>
                    <p class="text-gray-800">{{ $return->reason }}</p>
                </div>
                
                @if($return->notes)
                    <div class="mb-4">
                        <p class="text-gray-600 mb-1">ملاحظات إضافية:</p>
                        <p class="text-gray-800">{{ $return->notes }}</p>
                    </div>
                @endif
                
                @if($return->admin_notes && in_array($return->status, ['approved', 'rejected', 'completed']))
                    <div class="mb-4">
                        <p class="text-gray-600 mb-1">ملاحظات الإدارة:</p>
                        <p class="text-gray-800">{{ $return->admin_notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 p-4">المنتجات المرتجعة</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المنتج</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">السعر</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الكمية</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">سبب الإرجاع</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($return->items as $item)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($item->product->main_image)
                                        <img src="{{ asset('storage/' . $item->product->main_image) }}" alt="{{ $item->product->name }}" class="h-10 w-10 object-cover rounded-md">
                                    @else
                                        <div class="h-10 w-10 bg-gray-200 rounded-md flex items-center justify-center">
                                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="mr-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                        @if($item->orderItem && $item->orderItem->options)
                                            <div class="text-sm text-gray-500">
                                                @foreach($item->orderItem->options as $key => $value)
                                                    {{ $key }}: {{ $value }}@if(!$loop->last), @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $item->orderItem->unit_price }} {{ $return->order->currency }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $item->quantity }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @switch($item->condition)
                                        @case('new')
                                            جديد (غير مستخدم)
                                            @break
                                        @case('like_new')
                                            شبه جديد (تم فتحه فقط)
                                            @break
                                        @case('used')
                                            مستعمل
                                            @break
                                        @case('damaged')
                                            تالف
                                            @break
                                        @default
                                            {{ $item->condition }}
                                    @endswitch
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $item->reason }}</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 