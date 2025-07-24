@extends('layouts.user')

@section('title', 'سلة التسوق')

@section('content')
    <div class="container mx-auto px-4 py-5">
        <h1 class="text-2xl font-bold mb-4">سلة التسوق</h1>
        
        @if(session('cart') && count(session('cart')) > 0)
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Cart Items -->
                <div class="lg:w-3/4">
                    <div class="bg-white rounded shadow mb-4">
                        <div class="p-4 border-b border-gray-200 bg-gray-50">
                            <h2 class="text-xl font-semibold">المنتجات ({{ count(session('cart')) }})</h2>
                        </div>
                        
                        <div class="divide-y divide-gray-200">
                            @foreach(session('cart') as $id => $item)
                                <div class="p-4 flex flex-col sm:flex-row">
                                    <!-- Product Image -->
                                    <div class="sm:w-32 h-32 mb-4 sm:mb-0 flex-shrink-0">
                                        <img src="{{ asset('storage/' . $item['image_url']) }}" 
                                             alt="{{ $item['name'] }}" 
                                             class="w-full h-full object-contain rounded border border-gray-200">
                                    </div>
                                    
                                    <!-- Product Details -->
                                    <div class="sm:mr-4 flex-1">
                                        <div class="flex flex-col sm:flex-row justify-between">
                                            <div>
                                                <h3 class="font-semibold text-lg mb-1 text-blue-600 hover:text-orange-500 hover:underline">
                                                    <a href="{{ route('user.products.show', $id) }}">{{ $item['name'] }}</a>
                                                </h3>
                                                <p class="text-sm text-green-600 mb-2">متوفر</p>
                                                <p class="text-gray-600 text-sm mb-2">
                                                    @if(isset($item['attributes']))
                                                        @foreach($item['attributes'] as $key => $value)
                                                            <span>{{ $key }}: {{ $value }}</span>
                                                            @if(!$loop->last), @endif
                                                        @endforeach
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="text-left mt-2 sm:mt-0">
                                                <span class="font-semibold text-lg">{{ number_format($item['price'], 2) }} {{ $item['currency_symbol'] }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mt-4">
                                            <!-- Quantity -->
                                            <div class="flex items-center mb-4 sm:mb-0">
                                                <form action="{{ route('cart.update') }}" method="POST" class="flex items-center">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="id" value="{{ $id }}">
                                                    <span class="mr-2">الكمية:</span>
                                                    <div class="inline-block relative">
                                                        <select name="quantity" class="border border-gray-300 rounded py-1 pl-8 pr-2 bg-white hover:border-gray-400 focus:outline-none focus:border-blue-500 appearance-none text-sm quantity-select" data-id="{{ $id }}">
                                                            @for($i = 1; $i <= min($item['max_quantity'] ?? 10, 10); $i++)
                                                                <option value="{{ $i }}" {{ $item['quantity'] == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                            @endfor
                                                        </select>
                                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center px-2 text-gray-700">
                                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            
                                            <!-- Actions -->
                                            <div class="flex items-center">
                                                <span class="text-gray-700 mx-4">
                                                    المجموع: <span class="font-semibold">{{ number_format($item['price'] * $item['quantity'], 2) }} {{ $item['currency_symbol'] }}</span>
                                                </span>
                                                <div class="border-r border-gray-300 h-6 mx-2"></div>
                                                <form action="{{ route('cart.remove', $id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                                                        حذف
                                                    </button>
                                                </form>
                                                <div class="border-r border-gray-300 h-6 mx-2"></div>
                                                <a href="#" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                                                    حفظ للمشتريات القادمة
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="bg-white p-4 rounded shadow flex justify-between items-center">
                        <div class="text-lg">
                            المجموع ({{ count(session('cart')) }} منتجات): <span class="font-bold text-lg">{{ number_format(session('cart_totals.total') ?? 0, 2) }} {{ session('cart_totals.currency_symbol') ?? 'ر.س' }}</span>
                        </div>
                        
                        <form action="{{ route('cart.update') }}" method="POST" id="update-cart-form">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bg-gray-200 text-gray-700 py-2 px-4 rounded hover:bg-gray-300 transition duration-200">
                                تحديث السلة
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="lg:w-1/4">
                    <div class="bg-white rounded shadow overflow-hidden sticky top-4">
                        <div class="p-4">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-green-800 text-sm">
                                        مؤهل للشحن المجاني
                                    </span>
                                </div>
                            </div>

                            <!-- تفاصيل المجموع -->
                            <div class="border-b border-gray-200 pb-3 mb-3">
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-gray-600">المجموع الفرعي:</span>
                                    <span>{{ number_format(session('cart_totals.subtotal_without_tax') ?? 0, 2) }} {{ session('cart_totals.currency_symbol') ?? 'ر.س' }}</span>
                                </div>
                                
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-gray-600">{{ setting('tax_name', 'ضريبة القيمة المضافة') }} ({{ number_format(session('cart_totals.tax_percentage') ?? 15) }}%):</span>
                                    <span>{{ number_format(session('cart_totals.tax') ?? 0, 2) }} {{ session('cart_totals.currency_symbol') ?? 'ر.س' }}</span>
                                </div>
                                
                                @if(session('cart_totals.discount') > 0)
                                <div class="flex justify-between text-sm mb-2 text-green-600">
                                    <span>الخصم:</span>
                                    <span>-{{ number_format(session('cart_totals.discount') ?? 0, 2) }} {{ session('cart_totals.currency_symbol') ?? 'ر.س' }}</span>
                                </div>
                                @endif
                            </div>
                            
                            <div class="text-xl font-bold mb-3 flex justify-between">
                                <span>المجموع:</span>
                                <span>{{ number_format(session('cart_totals.total') ?? 0, 2) }} {{ session('cart_totals.currency_symbol') ?? 'ر.س' }}</span>
                            </div>
                            
                            <!-- Coupon Code -->
                            <div class="mb-4">
                                <form action="{{ route('cart.apply-discount') }}" method="POST" class="flex">
                                    @csrf
                                    <input type="text" name="code" placeholder="كود الخصم" 
                                        class="flex-1 rounded-r-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 text-sm">
                                    <button type="submit" class="bg-gray-200 text-gray-700 py-1 px-3 rounded-l text-sm hover:bg-gray-300 transition duration-200">
                                        تطبيق
                                    </button>
                                </form>
                                
                                @if(session('coupon_applied'))
                                    <div class="mt-2 text-green-600 text-xs">
                                        تم تطبيق كود الخصم: {{ session('coupon_code') }}
                                    </div>
                                @endif
                                
                                @if(session('coupon_error'))
                                    <div class="mt-2 text-red-600 text-xs">
                                        {{ session('coupon_error') }}
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Checkout Button -->
                            <a href="{{ route('checkout.index') }}" class="block w-full bg-yellow-400 hover:bg-yellow-500 text-center py-2 px-4 rounded-md mb-3 font-medium transition-colors duration-200">
                                الدفع الآن
                            </a>
                            
                            <div class="text-xs text-gray-600 mb-4">
                                بالضغط على "الدفع الآن"، فإنك توافق على 
                                <a href="#" class="text-blue-600 hover:underline">شروط الخدمة</a> و
                                <a href="#" class="text-blue-600 hover:underline">سياسة الخصوصية</a>
                            </div>
                            
                            <!-- Payment Methods -->
                            <div class="flex justify-center border-t border-gray-200 pt-4">
                                <div class="flex space-x-2 space-x-reverse">
                                    <img src="{{ asset('images/payment/visa.svg') }}" alt="Visa" class="h-6">
                                    <img src="{{ asset('images/payment/inpay.svg') }}" alt="InstaPay" class="h-6">
                                    <img src="{{ asset('images/payment/vcash.svg') }}" alt="VCash" class="h-6">
                                    <img src="{{ asset('images/payment/apple-pay.svg') }}" alt="Apple Pay" class="h-6">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recommended Products -->
                    <div class="mt-6 bg-white rounded shadow p-4">
                        <h3 class="font-bold mb-3">قد يعجبك أيضاً</h3>
                        <div class="space-y-4">
                            <div class="flex">
                                <div class="w-16 h-16 flex-shrink-0">
                                    <img src="{{ asset('storage/products/jmrkcKNTUrdmsdFubOv0wlBqEFAHfyiLQDdO4gSQ.jpg') }}" alt="مايكرويف" class="w-full h-full object-contain rounded border border-gray-200" onerror="this.src='{{ asset('images/product-placeholder.jpg') }}'">
                                </div>
                                <div class="mr-3">
                                    <a href="{{ route('user.products.show', 'maykroyf') }}" class="text-sm text-blue-600 hover:text-orange-500 hover:underline">مايكرويف</a>
                                    <div class="text-yellow-500 text-xs">★★★★☆</div>
                                    <div class="font-bold text-sm">199.00 {{ session('cart_totals.currency_symbol') ?? 'ر.س' }}</div>
                                </div>
                            </div>
                            <div class="flex">
                                <div class="w-16 h-16 flex-shrink-0">
                                    <img src="{{ asset('storage/products/Ll9ddAC261W4mz8XWn0U6mijWOyeOnxXpvu53LEe.jpg') }}" alt="مايكرويف توشيبا" class="w-full h-full object-contain rounded border border-gray-200" onerror="this.src='{{ asset('images/product-placeholder.jpg') }}'">
                                </div>
                                <div class="mr-3">
                                    <a href="{{ route('user.products.show', 'maykroyf-toshyba') }}" class="text-sm text-blue-600 hover:text-orange-500 hover:underline">مايكرويف توشيبا</a>
                                    <div class="text-yellow-500 text-xs">★★★★★</div>
                                    <div class="font-bold text-sm">249.00 {{ session('cart_totals.currency_symbol') ?? 'ر.س' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <svg class="h-20 w-20 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h2 class="text-2xl font-bold text-gray-700 mb-4">سلة التسوق فارغة</h2>
                <p class="text-gray-500 mb-6">لم تقم بإضافة أي منتجات إلى سلة التسوق بعد.</p>
                <a href="{{ route('user.products.index') }}" class="bg-yellow-400 hover:bg-yellow-500 text-center py-2 px-6 rounded-md font-medium transition-colors duration-200">
                    تصفح المنتجات
                </a>
            </div>

            <!-- Recommended Products -->
            <div class="mt-8">
                <h2 class="text-2xl font-bold mb-4">منتجات قد تعجبك</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    <div class="bg-white p-4 rounded shadow">
                        <img src="{{ asset('storage/products/jmrkcKNTUrdmsdFubOv0wlBqEFAHfyiLQDdO4gSQ.jpg') }}" alt="مايكرويف" class="w-full h-48 object-contain mb-3 rounded border border-gray-200" onerror="this.src='{{ asset('images/product-placeholder.svg') }}'">
                        <a href="{{ route('user.products.show', 'maykroyf') }}" class="text-sm text-blue-600 hover:text-orange-500 hover:underline line-clamp-2">مايكرويف</a>
                        <div class="text-yellow-500 text-sm my-1">★★★★☆</div>
                        <div class="font-bold">199.00 ر.س</div>
                        <div class="text-xs text-green-600 mb-3">متوفر</div>
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="4">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 py-1 px-2 rounded-sm text-sm transition-colors duration-200">
                                أضف للسلة
                            </button>
                        </form>
                    </div>
                    
                    <div class="bg-white p-4 rounded shadow">
                        <img src="{{ asset('storage/products/Ll9ddAC261W4mz8XWn0U6mijWOyeOnxXpvu53LEe.jpg') }}" alt="مايكرويف توشيبا" class="w-full h-48 object-contain mb-3 rounded border border-gray-200" onerror="this.src='{{ asset('images/product-placeholder.svg') }}'">
                        <a href="{{ route('user.products.show', 'maykroyf-toshyba') }}" class="text-sm text-blue-600 hover:text-orange-500 hover:underline line-clamp-2">مايكرويف توشيبا</a>
                        <div class="text-yellow-500 text-sm my-1">★★★★★</div>
                        <div class="font-bold">249.00 ر.س</div>
                        <div class="text-xs text-green-600 mb-3">متوفر</div>
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="7">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 py-1 px-2 rounded-sm text-sm transition-colors duration-200">
                                أضف للسلة
                            </button>
                        </form>
                    </div>
                    
                    <div class="bg-white p-4 rounded shadow">
                        <img src="{{ asset('storage/products/aMMpFj2APlU2bQr7FwQB80AG0ZcpbMQNUOm8RkKZ.jpg') }}" alt="مروحة" class="w-full h-48 object-contain mb-3 rounded border border-gray-200" onerror="this.src='{{ asset('images/product-placeholder.svg') }}'">
                        <a href="{{ route('user.products.show', 'mroh') }}" class="text-sm text-blue-600 hover:text-orange-500 hover:underline line-clamp-2">مروحة</a>
                        <div class="text-yellow-500 text-sm my-1">★★★☆☆</div>
                        <div class="font-bold">99.00 ر.س</div>
                        <div class="text-xs text-green-600 mb-3">متوفر</div>
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="8">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 py-1 px-2 rounded-sm text-sm transition-colors duration-200">
                                أضف للسلة
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
<style>
    /* تحسين مظهر بطاقات المنتجات */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* تأثيرات حركية للبطاقات */
    .shadow {
        transition: all 0.3s ease;
    }
    
    .shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    /* تحسين مظهر الصور */
    .object-contain {
        background-color: #fff;
    }
    
    /* تحسين مظهر الأزرار */
    button[type="submit"] {
        transition: all 0.2s ease;
    }
    
    button[type="submit"]:hover {
        transform: translateY(-2px);
    }
    
    /* تحسين مظهر السلة */
    .sticky {
        position: sticky;
        top: 20px;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تحديث الكمية تلقائيًا عند التغيير
        const quantitySelects = document.querySelectorAll('.quantity-select');
        
        quantitySelects.forEach(select => {
            select.addEventListener('change', function() {
                // إظهار مؤشر التحميل
                const loadingOverlay = document.createElement('div');
                loadingOverlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
                loadingOverlay.innerHTML = `
                    <div class="bg-white p-4 rounded-lg shadow-lg flex items-center">
                        <svg class="animate-spin h-6 w-6 text-blue-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-gray-700 font-medium">جاري التحديث...</span>
                    </div>
                `;
                document.body.appendChild(loadingOverlay);
                
                // تقديم النموذج
                this.closest('form').submit();
            });
        });
        
        // تحميل الصور بشكل متأخر
        const lazyImages = document.querySelectorAll('img');
        
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const image = entry.target;
                        image.src = image.dataset.src || image.src;
                        imageObserver.unobserve(image);
                    }
                });
            });
            
            lazyImages.forEach(img => {
                imageObserver.observe(img);
            });
        }
    });
</script>
@endpush 