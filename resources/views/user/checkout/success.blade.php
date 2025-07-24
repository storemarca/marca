@extends('layouts.user')

@section('title', 'تم إتمام الطلب بنجاح')

@section('content')
<div class="container mx-auto px-4 py-5">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded shadow p-6 mb-6">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">شكراً لطلبك، {{ explode(' ', $order->shipping_name)[0] }}</h1>
                    <p class="text-gray-600">تم تأكيد طلبك برقم <span class="font-semibold">{{ $order->order_number }}</span></p>
                </div>
            </div>

            <div class="border-b border-gray-200 pb-4 mb-4">
                <h2 class="text-lg font-bold mb-3">تفاصيل الطلب</h2>
                <div class="flex flex-wrap gap-4 text-sm">
                    <div class="bg-gray-50 rounded p-3 flex-1 min-w-[200px]">
                        <p class="text-gray-600 mb-1">تاريخ الطلب:</p>
                        <p class="font-semibold">{{ $order->created_at->format('d/m/Y') }}</p>
                    </div>
                    <div class="bg-gray-50 rounded p-3 flex-1 min-w-[200px]">
                        <p class="text-gray-600 mb-1">طريقة الدفع:</p>
                        <p class="font-semibold">{{ $order->payment_method_text }}</p>
                    </div>
                    <div class="bg-gray-50 rounded p-3 flex-1 min-w-[200px]">
                        <p class="text-gray-600 mb-1">حالة الطلب:</p>
                        <p class="font-semibold text-green-600">{{ $order->status_text }}</p>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-lg font-bold">منتجات الطلب</h2>
                    @if(auth()->check())
                        <a href="{{ route('user.orders.show', $order->id) }}" class="text-sm text-blue-600 hover:text-orange-500 hover:underline">
                            عرض تفاصيل كاملة للطلب
                        </a>
                    @endif
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex">
                                <div class="w-16 h-16 flex-shrink-0">
                                    <img src="{{ $item->product->image_url ?? 'https://via.placeholder.com/70x70' }}" alt="{{ $item->product_name }}" class="w-full h-full object-contain">
                                </div>
                                <div class="mr-3 flex-1">
                                    <div class="font-medium">{{ $item->product_name }}</div>
                                    <div class="text-sm text-gray-600 flex justify-between mt-1">
                                        <span>{{ $item->quantity }} × {{ number_format($item->unit_price, 2) }} {{ $order->currency_symbol }}</span>
                                        <span class="font-semibold">{{ number_format($item->total, 2) }} {{ $order->currency_symbol }}</span>
                                    </div>
                                    @if($item->options)
                                        <div class="text-gray-500 text-xs mt-1">
                                            @foreach($item->options as $key => $value)
                                                {{ $key }}: {{ $value }}@if(!$loop->last), @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="space-y-1 text-sm">
                            <div class="flex justify-between">
                                <span>المجموع الفرعي:</span>
                                <span>{{ number_format($order->subtotal, 2) }} {{ $order->currency_symbol }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>الشحن:</span>
                                @if($order->shipping_amount > 0)
                                    <span>{{ number_format($order->shipping_amount, 2) }} {{ $order->currency_symbol }}</span>
                                @else
                                    <span class="text-green-600">مجاني</span>
                                @endif
                            </div>
                            @if($order->tax_amount > 0)
                                <div class="flex justify-between">
                                    <span>الضريبة:</span>
                                    <span>{{ number_format($order->tax_amount, 2) }} {{ $order->currency_symbol }}</span>
                                </div>
                            @endif
                            @if($order->discount_amount > 0)
                                <div class="flex justify-between text-green-600">
                                    <span>الخصم:</span>
                                    <span>-{{ number_format($order->discount_amount, 2) }} {{ $order->currency_symbol }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between font-bold text-lg pt-2">
                                <span>الإجمالي:</span>
                                <span>{{ number_format($order->total_amount, 2) }} {{ $order->currency_symbol }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-lg font-bold mb-3">عنوان الشحن</h2>
                    <div class="bg-gray-50 rounded p-4">
                        <p class="font-medium">{{ $order->shipping_name }}</p>
                        <p>{{ $order->shipping_address_line1 }}</p>
                        @if($order->shipping_address_line2)
                            <p>{{ $order->shipping_address_line2 }}</p>
                        @endif
                        <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
                        <p>{{ $order->shipping_country }}</p>
                        <p>{{ $order->shipping_phone }}</p>
                    </div>
                </div>

                <div>
                    <h2 class="text-lg font-bold mb-3">معلومات التوصيل</h2>
                    <div class="bg-gray-50 rounded p-4">
                        <div class="flex items-center text-green-700 mb-2">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">الموعد المتوقع للتوصيل:</span>
                        </div>
                        <p class="font-bold text-xl mb-3">{{ now()->addDays(3)->format('d M Y') }} - {{ now()->addDays(5)->format('d M Y') }}</p>
                        <p class="text-sm text-gray-600">سيتم إرسال تحديثات حالة الطلب إلى بريدك الإلكتروني: {{ $order->customer_email }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center">
            <div class="mb-6">
                <a href="{{ route('user.products.index') }}" class="bg-yellow-400 hover:bg-yellow-500 py-2 px-6 rounded font-medium inline-block transition-colors duration-200">
                    متابعة التسوق
                </a>
            </div>
            
            @if(!auth()->check())
                <div class="bg-blue-50 rounded shadow p-6">
                    <h3 class="font-bold text-lg mb-2">إنشاء حساب لمتابعة طلباتك</h3>
                    <p class="text-gray-600 mb-4">قم بإنشاء حساب للاحتفاظ بسجل طلباتك وتسهيل عمليات الشراء المستقبلية</p>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white py-2 px-6 rounded hover:bg-blue-700 transition duration-200 inline-block">
                        إنشاء حساب
                    </a>
                    <div class="mt-4 text-sm text-gray-600">
                        تم إرسال تفاصيل الطلب إلى: {{ $order->customer_email }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Product Recommendations -->
        <div class="mt-8">
            <h2 class="text-xl font-bold mb-4">قد يعجبك أيضاً</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @for($i = 1; $i <= 4; $i++)
                    <div class="bg-white p-4 rounded shadow">
                        <img src="https://via.placeholder.com/200x200" alt="Product {{ $i }}" class="w-full h-48 object-contain mb-3">
                        <a href="#" class="text-sm text-blue-600 hover:text-orange-500 hover:underline line-clamp-2">منتج مقترح {{ $i }}</a>
                        <div class="text-yellow-500 text-sm my-1">★★★★☆</div>
                        <div class="font-bold">199.00 {{ $order->currency_symbol }}</div>
                        <div class="text-xs text-green-600 mb-3">متوفر</div>
                        <button class="w-full bg-yellow-400 hover:bg-yellow-500 py-1 px-2 rounded-sm text-sm transition-colors duration-200">
                            أضف للسلة
                        </button>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>
@endsection 