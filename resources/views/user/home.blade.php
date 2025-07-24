@extends('layouts.user')

@section('title', 'الرئيسية')

@section('content')
    <!-- Hero Section -->
    <div class="relative bg-gray-900 overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="relative z-10 pb-8 bg-gray-900 sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                <svg class="hidden lg:block absolute right-0 inset-y-0 h-full w-48 text-gray-900 transform translate-x-1/2" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                    <polygon points="50,0 100,0 50,100 0,100" />
                </svg>

                <div class="pt-10 sm:pt-16 lg:pt-8 lg:pb-14 lg:overflow-hidden">
                    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div class="lg:grid lg:grid-cols-2 lg:gap-8">
                            <div class="mx-auto max-w-md px-4 sm:max-w-2xl sm:px-6 sm:text-center lg:px-0 lg:text-right lg:flex lg:items-center">
                                <div class="lg:py-24">
                                    <h1 class="mt-4 text-4xl tracking-tight font-extrabold text-white sm:mt-5 sm:text-6xl lg:mt-6 xl:text-6xl">
                                        <span class="block">{{ setting('home_banner_title', 'تسوق بثقة') }}</span>
                                        <span class="block text-yellow-500">{{ __('مع أفضل المنتجات') }}</span>
                                    </h1>
                                    <p class="mt-3 text-base text-gray-300 sm:mt-5 sm:text-xl lg:text-lg xl:text-xl">
                                        {{ setting('home_banner_subtitle', 'اكتشف مجموعة واسعة من المنتجات عالية الجودة بأسعار تنافسية. تسوق الآن واستمتع بتجربة تسوق فريدة.') }}
                                    </p>
                                    <div class="mt-10 sm:mt-12">
                                        <div class="sm:flex sm:justify-center lg:justify-start">
                                            <div class="rounded-md shadow">
                                                <a href="{{ route('user.products.index') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 md:py-4 md:text-lg md:px-10">
                                                    {{ setting('home_banner_button_text', 'تسوق الآن') }}
                                                </a>
                                            </div>
                                            <div class="mt-3 sm:mt-0 sm:mr-3">
                                                <a href="{{ route('register') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-yellow-600 bg-white hover:bg-gray-50 md:py-4 md:text-lg md:px-10">
                                                    {{ __('إنشاء حساب') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-12 -mb-16 sm:-mb-48 lg:m-0 lg:relative">
                                <div class="mx-auto max-w-md px-4 sm:max-w-2xl sm:px-6 lg:max-w-none lg:px-0">
                                    @if(setting('home_banner_image'))
                                        <img class="w-full lg:absolute lg:inset-y-0 lg:left-0 lg:h-full lg:w-auto lg:max-w-none" 
                                             src="{{ asset('storage/' . setting('home_banner_image')) }}" 
                                             alt="{{ setting('home_banner_title') }}">
                                    @else
                                        <!-- استخدام عنصر بديل إذا لم تكن الصورة متاحة -->
                                        <div class="w-full lg:absolute lg:inset-y-0 lg:left-0 lg:h-full lg:w-auto lg:max-w-none bg-yellow-600 rounded-lg flex items-center justify-center">
                                            <span class="text-white text-4xl font-bold p-10">{{ setting('site_name') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Categories -->
    <div class="bg-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h2 class="text-base text-yellow-600 font-semibold tracking-wide uppercase">{{ __('التصنيفات') }}</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    {{ setting('home_categories_title', 'تصفح حسب الفئات') }}
                </p>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
                    {{ setting('home_categories_subtitle', 'اكتشف منتجاتنا المصنفة بعناية لتسهيل تجربة التسوق الخاصة بك') }}
                </p>
            </div>

            <div class="mt-10">
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($categories->take(4) as $category)
                        <div class="group relative bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300">
                            <div class="aspect-w-3 aspect-h-2 bg-gray-200 group-hover:opacity-75">
                                <img src="{{ $category->image ?? asset('images/category-placeholder.jpg') }}" 
                                     alt="{{ $category->name }}" 
                                     class="w-full h-48 object-cover">
                            </div>
                            <div class="p-4 text-center">
                                <h3 class="text-lg font-medium text-gray-900">
                                    <a href="{{ route('user.products.index', ['category_id' => $category->id]) }}">
                                        <span aria-hidden="true" class="absolute inset-0"></span>
                                        {{ $category->name }}
                                    </a>
                                </h3>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Products -->
    <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-extrabold tracking-tight text-gray-900">{{ setting('home_featured_title', 'المنتجات المميزة') }}</h2>
                <a href="{{ route('user.products.index', ['featured' => 1]) }}" class="text-yellow-600 hover:text-yellow-700 font-medium">
                    {{ __('عرض الكل') }} <span aria-hidden="true">&rarr;</span>
                </a>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-x-4 gap-y-10 sm:gap-x-6 md:grid-cols-3 lg:grid-cols-4 xl:gap-x-8">
                @foreach($featuredProducts as $product)
                    <div class="product-card group relative bg-white">
                        <!-- Badge para productos destacados -->
                        <div class="product-card-badge product-card-badge-featured">
                            {{ __('مميز') }}
                        </div>
                        
                        <!-- Imagen del producto con overlay en hover -->
                        <div class="product-card-image">
                            <img src="{{ $product->image_url }}" 
                                 alt="{{ $product->name }}">
                                 
                            <!-- Overlay con botón de añadir al carrito -->
                            <div class="product-card-overlay">
                                <form action="{{ route('cart.add') }}" method="POST" class="inline-block">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="product-card-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <span class="mr-1">{{ __('أضف للسلة') }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Información del producto -->
                        <div class="product-card-content">
                            <!-- Categoría -->
                            <div class="text-xs text-gray-500 mb-1">
                                {{ $product->category->name ?? '' }}
                            </div>
                            
                            <!-- Nombre del producto -->
                            <h3 class="product-card-title">
                                <a href="{{ route('user.products.show', $product->slug) }}">
                                    {{ $product->name }}
                                </a>
                            </h3>
                            
                            <!-- Valoraciones -->
                            <div class="flex items-center mb-2">
                                <div class="flex items-center text-yellow-500">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= ($product->rating ?? 0))
                                            <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20">
                                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                            </svg>
                                        @else
                                            <svg class="h-4 w-4 fill-current text-gray-300" viewBox="0 0 20 20">
                                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                            </svg>
                                        @endif
                                    @endfor
                                </div>
                                <p class="mr-2 text-xs text-gray-500">({{ $product->reviews_count ?? 0 }})</p>
                            </div>
                            
                            <!-- Línea divisoria -->
                            <div class="border-t border-gray-100 my-2"></div>
                            
                            <!-- Precio y disponibilidad -->
                            <div class="flex justify-between items-center mt-3">
                                <div>
                                    @php
                                        $currentCountry = current_country();
                                        $productPrice = $product->getPriceForCountry($currentCountry->id);
                                    @endphp
                                    @if($productPrice)
                                        <span class="product-card-price">
                                            {{ number_format($productPrice->price, 2) }} {{ $currentCountry->currency_symbol }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500">
                                            {{ __('price_not_available') }}
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Estado del stock -->
                                <div>
                                    @if ($product->isInStock())
                                        <span class="product-card-stock-badge product-card-stock-badge-in">
                                            <svg class="inline-block h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            {{ __('متوفر') }}
                                        </span>
                                    @else
                                        <span class="product-card-stock-badge product-card-stock-badge-out">
                                            <svg class="inline-block h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            {{ __('غير متوفر') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- New Arrivals -->
    <div class="bg-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-extrabold tracking-tight text-gray-900">{{ setting('home_new_title', 'وصل حديثاً') }}</h2>
                <a href="{{ route('user.products.index', ['sort' => 'created_at', 'direction' => 'desc']) }}" class="text-yellow-600 hover:text-yellow-700 font-medium">
                    {{ __('عرض الكل') }} <span aria-hidden="true">&rarr;</span>
                </a>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-x-4 gap-y-10 sm:gap-x-6 md:grid-cols-3 lg:grid-cols-4 xl:gap-x-8">
                @foreach($newProducts as $product)
                    <div class="product-card group relative bg-white">
                        <!-- Badge para productos nuevos -->
                        <div class="product-card-badge product-card-badge-new">
                            {{ __('جديد') }}
                        </div>
                        
                        <!-- Imagen del producto con overlay en hover -->
                        <div class="product-card-image">
                            <img src="{{ $product->image_url }}" 
                                 alt="{{ $product->name }}">
                                 
                            <!-- Overlay con botón de añadir al carrito -->
                            <div class="product-card-overlay">
                                <form action="{{ route('cart.add') }}" method="POST" class="inline-block">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="product-card-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <span class="mr-1">{{ __('أضف للسلة') }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Información del producto -->
                        <div class="product-card-content">
                            <!-- Categoría -->
                            <div class="text-xs text-gray-500 mb-1">
                                {{ $product->category->name ?? '' }}
                            </div>
                            
                            <!-- Nombre del producto -->
                            <h3 class="product-card-title">
                                <a href="{{ route('user.products.show', $product->slug) }}">
                                    {{ $product->name }}
                                </a>
                            </h3>
                            
                            <!-- Valoraciones -->
                            <div class="flex items-center mb-2">
                                <div class="flex items-center text-yellow-500">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= ($product->rating ?? 0))
                                            <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20">
                                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                            </svg>
                                        @else
                                            <svg class="h-4 w-4 fill-current text-gray-300" viewBox="0 0 20 20">
                                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                            </svg>
                                        @endif
                                    @endfor
                                </div>
                                <p class="mr-2 text-xs text-gray-500">({{ $product->reviews_count ?? 0 }})</p>
                            </div>
                            
                            <!-- Línea divisoria -->
                            <div class="border-t border-gray-100 my-2"></div>
                            
                            <!-- Precio y disponibilidad -->
                            <div class="flex justify-between items-center mt-3">
                                <div>
                                    @php
                                        $currentCountry = current_country();
                                        $productPrice = $product->getPriceForCountry($currentCountry->id);
                                    @endphp
                                    @if($productPrice)
                                        <span class="product-card-price">
                                            {{ number_format($productPrice->price, 2) }} {{ $currentCountry->currency_symbol }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500">
                                            {{ __('price_not_available') }}
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Estado del stock -->
                                <div>
                                    @if ($product->isInStock())
                                        <span class="product-card-stock-badge product-card-stock-badge-in">
                                            <svg class="inline-block h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            {{ __('متوفر') }}
                                        </span>
                                    @else
                                        <span class="product-card-stock-badge product-card-stock-badge-out">
                                            <svg class="inline-block h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            {{ __('غير متوفر') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Banner -->
    <div class="bg-yellow-600">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
            <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                <span class="block">{{ __('جاهز للتسوق؟') }}</span>
                <span class="block text-yellow-200">{{ __('سجل الآن واحصل على شحن مجاني') }}</span>
            </h2>
            <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
                <div class="inline-flex rounded-md shadow">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-yellow-600 bg-white hover:bg-yellow-50">
                        {{ __('سجل الآن') }}
                    </a>
                </div>
                <div class="mr-4 inline-flex rounded-md shadow">
                    <a href="{{ route('user.products.index') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-yellow-700 hover:bg-yellow-800">
                        {{ __('تسوق الآن') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .aspect-w-3 {
        position: relative;
        padding-bottom: 66.666667%;
    }
    .aspect-w-4 {
        position: relative;
        padding-bottom: 75%;
    }
    .aspect-h-2, .aspect-h-3 {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
    }
    
    /* Estilos adicionales para las tarjetas de productos */
    .product-card-badge {
        position: absolute;
        z-index: 10;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    
    .product-card-badge-featured {
        background-color: #f59e0b;
        color: white;
        top: 0.5rem;
        right: 0.5rem;
    }
    
    .product-card-badge-new {
        background-color: #3b82f6;
        color: white;
        top: 0.5rem;
        left: 0.5rem;
    }
    
    .product-card-badge-sale {
        background-color: #ef4444;
        color: white;
        top: 0.5rem;
        left: 0.5rem;
    }
    
    .product-card {
        transition: all 0.3s ease;
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .product-card-image {
        position: relative;
        overflow: hidden;
        height: 200px;
    }
    
    .product-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .product-card:hover .product-card-image img {
        transform: scale(1.05);
    }
    
    .product-card-overlay {
        position: absolute;
        inset: 0;
        background-color: rgba(0, 0, 0, 0);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: all 0.3s ease;
    }
    
    .product-card:hover .product-card-overlay {
        background-color: rgba(0, 0, 0, 0.2);
        opacity: 1;
    }
    
    .product-card-content {
        padding: 1rem;
    }
    
    .product-card-title {
        font-size: 1rem;
        font-weight: 500;
        color: #1f2937;
        margin-bottom: 0.5rem;
        transition: color 0.2s ease;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .product-card:hover .product-card-title {
        color: #d97706;
    }
    
    .product-card-price {
        font-size: 1.125rem;
        font-weight: 700;
        color: #d97706;
    }
    
    .product-card-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 1rem;
        background-color: #d97706;
        color: white;
        border-radius: 9999px;
        font-weight: 500;
        transition: all 0.2s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .product-card-button:hover {
        background-color: #b45309;
        transform: scale(1.05);
    }
    
    .product-card-stock-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.125rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .product-card-stock-badge-in {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .product-card-stock-badge-out {
        background-color: #fee2e2;
        color: #b91c1c;
    }
</style>
@endpush 