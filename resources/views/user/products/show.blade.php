@extends('layouts.user')

@section('title', $seoTitle)
@section('meta_description', $seoDescription)
@section('meta_keywords', $seoKeywords)
@section('meta_image', $product->main_image ? asset($product->main_image) : '')

@push('styles')
<style>
    /* دعم نسبة العرض إلى الارتفاع للفيديوهات */
    .aspect-w-16 {
        position: relative;
        padding-bottom: 56.25%; /* نسبة 16:9 */
        height: 0;
    }
    .aspect-w-16 iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <nav class="flex mb-5" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 rtl:space-x-reverse">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-yellow-600">
                    <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                    </svg>
                    {{ __('home') }}
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <a href="{{ route('user.products.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-yellow-600 md:ms-2">{{ __('products') }}</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <a href="{{ route('user.products.index', ['category' => $product->category_id]) }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-yellow-600 md:ms-2">{{ $product->category->name }}</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">{{ $product->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-6">
            <!-- Product Images -->
            <div class="product-images">
                @if(!empty($product->images))
                    <div class="main-image mb-4">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-auto rounded-lg" id="main-product-image" onerror="this.onerror=null; this.src='{{ asset('images/product-placeholder.svg') }}';">
                    </div>
                    @if(count($product->images) > 1)
                        <div class="thumbnails grid grid-cols-5 gap-2">
                            @foreach($product->images as $index => $image)
                                <img src="{{ asset($image) }}" alt="{{ $product->name }} - {{ __('image') }} {{ $index + 1 }}" 
                                    class="w-full h-20 object-cover rounded cursor-pointer thumbnail-image" 
                                    onclick="document.getElementById('main-product-image').src = this.src"
                                    onerror="this.onerror=null; this.src='{{ asset('images/product-placeholder.svg') }}';">
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="main-image mb-4">
                        <img src="{{ asset('images/product-placeholder.svg') }}" alt="{{ $product->name }}" class="w-full h-auto rounded-lg">
                    </div>
                @endif
            </div>

            <!-- Product Info -->
            <div class="product-info">
                <h1 class="text-2xl font-bold mb-2">{{ $product->name }}</h1>
                
                <div class="mb-4">
                    <span class="text-gray-600">{{ __('sku') }}: {{ $product->sku }}</span>
                </div>
                
                @if($product->short_description)
                    <div class="text-gray-700 mb-4">
                        {{ $product->short_description }}
                    </div>
                @endif
                
                <div class="mb-6">
                    @if($productPrice)
                        <div class="text-2xl font-bold text-yellow-600">
                            {{ number_format($productPrice->price, 2) }} {{ $productPrice->currency_symbol ?? '' }}
                        </div>
                        @if($productPrice->sale_price > 0 && $productPrice->sale_price < $productPrice->price && $productPrice->isSaleActive())
                            <div class="text-gray-500 line-through">
                                {{ number_format($productPrice->price, 2) }} {{ $productPrice->currency_symbol ?? '' }}
                            </div>
                            <div class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded mt-1 inline-block">
                                {{ __('save') }} {{ number_format(100 - ($productPrice->sale_price / $productPrice->price * 100), 0) }}%
                            </div>
                        @endif
                        
                        @if(setting('show_tax_in_product', 0) == 1)
                            <div class="text-gray-500 text-sm mt-2">
                                @if(setting('tax_included', 0) == 1)
                                    {{ setting('tax_name', 'ضريبة القيمة المضافة') }} ({{ setting('tax_percentage', 15) }}%) مشمولة في السعر
                                @else
                                    {{ setting('tax_name', 'ضريبة القيمة المضافة') }} ({{ setting('tax_percentage', 15) }}%) تضاف عند الدفع
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
                
                <!-- Stock Status -->
                <div class="mb-6">
                    @php
                        $country = current_country();
                        $countryStock = $product->getAvailableStockForCountry($country->id);
                    @endphp
                    
                    @if($countryStock > 0)
                        <div class="text-green-600 flex items-center">
                            <svg class="w-5 h-5 me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            {{ __('in_stock') }} ({{ $countryStock }} {{ __('available') }})
                        </div>
                    @else
                        <div class="text-red-600 flex items-center">
                            <svg class="w-5 h-5 me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            {{ __('out_of_stock') }}
                        </div>
                    @endif
                </div>
                
                <!-- Add to Cart Form -->
                @if($countryStock > 0)
                    <form action="{{ route('cart.add') }}" method="POST" class="mb-6">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        
                        <div class="flex items-center mb-4">
                            <label for="quantity" class="block text-sm font-medium text-gray-700 me-4">{{ __('quantity') }}:</label>
                            <div class="custom-number-input">
                                <div class="flex flex-row h-10 w-32 rounded-lg relative bg-transparent mt-1">
                                    <button type="button" data-action="decrement" class="bg-gray-200 text-gray-600 hover:text-gray-700 hover:bg-gray-300 h-full w-10 rounded-l cursor-pointer">
                                        <span class="m-auto text-xl font-thin">−</span>
                                    </button>
                                    <input type="number" name="quantity" id="quantity" class="outline-none focus:outline-none text-center w-full bg-gray-100 font-semibold text-md hover:text-black focus:text-black md:text-base cursor-default flex items-center text-gray-700" min="1" max="{{ $countryStock }}" value="1">
                                    <button type="button" data-action="increment" class="bg-gray-200 text-gray-600 hover:text-gray-700 hover:bg-gray-300 h-full w-10 rounded-r cursor-pointer">
                                        <span class="m-auto text-xl font-thin">+</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg w-full flex items-center justify-center">
                            <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            {{ __('add_to_cart') }}
                        </button>
                    </form>
                @endif
                
                <!-- Product Categories and Tags -->
                <div class="mb-6">
                    <div class="flex items-center">
                        <span class="text-gray-600 me-2">{{ __('category') }}:</span>
                        <a href="{{ route('user.products.index', ['category' => $product->category_id]) }}" class="text-yellow-600 hover:underline">
                            {{ $product->category->name }}
                        </a>
                    </div>
                </div>
                
                <!-- Social Sharing -->
                <div>
                    <p class="text-gray-600 mb-2">{{ __('share') }}:</p>
                    <div class="flex space-x-4 rtl:space-x-reverse">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                            </svg>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($product->name) }}&url={{ urlencode(url()->current()) }}" target="_blank" class="text-blue-400 hover:text-blue-600">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/>
                            </svg>
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($product->name . ' ' . url()->current()) }}" target="_blank" class="text-green-500 hover:text-green-700">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Product Description and Details -->
        <div class="p-6 border-t">
            <div class="mb-8">
                <h2 class="text-xl font-bold mb-4">{{ __('product_description') }}</h2>
                <div class="prose max-w-none text-gray-700">
                    {!! $product->description !!}
                </div>
            </div>
            
            @if($product->attributes && count($product->attributes) > 0)
                <div class="mb-8">
                    <h2 class="text-xl font-bold mb-4">{{ __('specifications') }}</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <tbody>
                                @foreach($product->attributes as $key => $value)
                                    <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                                        <td class="py-2 px-4 border-b border-gray-200 font-medium">{{ $key }}</td>
                                        <td class="py-2 px-4 border-b border-gray-200">{{ $value }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            
            <!-- ألوان المنتج -->
            @if($product->colors && count($product->colors) > 0)
                <div class="mb-8">
                    <h2 class="text-xl font-bold mb-4">{{ __('colors') ?? 'الألوان المتاحة' }}</h2>
                    <div class="flex flex-wrap gap-3">
                        @foreach($product->colors as $color)
                            <div class="flex items-center">
                                <div class="w-6 h-6 rounded-full mr-2" style="background-color: {{ $color['code'] ?? '#000000' }}"></div>
                                <span class="text-gray-700">{{ $color['name'] ?? '' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <!-- مقاسات المنتج -->
            @if($product->sizes && count($product->sizes) > 0)
                <div class="mb-8">
                    <h2 class="text-xl font-bold mb-4">{{ __('sizes') ?? 'المقاسات المتاحة' }}</h2>
                    <div class="flex flex-wrap gap-3">
                        @foreach($product->sizes as $size)
                            <div class="border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-100 transition-colors">
                                {{ $size['name'] ?? '' }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <!-- فيديوهات المنتج -->
            @if($product->videos && count($product->videos) > 0)
                <div class="mb-8">
                    <h2 class="text-xl font-bold mb-4">{{ __('videos') ?? 'فيديوهات المنتج' }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($product->videos as $video)
                            <div class="bg-gray-50 rounded-lg overflow-hidden shadow-sm">
                                <div class="aspect-w-16 aspect-h-9">
                                    @php
                                        // تحويل رابط YouTube إلى رابط Embed
                                        $videoUrl = $video['url'] ?? '';
                                        $embedUrl = '';
                                        
                                        if (strpos($videoUrl, 'youtube.com') !== false) {
                                            // استخراج معرف الفيديو من الرابط
                                            preg_match('/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $videoUrl, $matches);
                                            if (isset($matches[1])) {
                                                $embedUrl = 'https://www.youtube.com/embed/' . $matches[1];
                                            }
                                        } elseif (strpos($videoUrl, 'vimeo.com') !== false) {
                                            // استخراج معرف الفيديو من رابط Vimeo
                                            preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|video\/|)(\d+)(?:[a-zA-Z0-9_-]+)?/', $videoUrl, $matches);
                                            if (isset($matches[1])) {
                                                $embedUrl = 'https://player.vimeo.com/video/' . $matches[1];
                                            }
                                        }
                                    @endphp
                                    
                                    @if($embedUrl)
                                        <iframe src="{{ $embedUrl }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full"></iframe>
                                    @else
                                        <div class="flex items-center justify-center h-full bg-gray-200 text-gray-500">
                                            رابط الفيديو غير صالح
                                        </div>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <h3 class="font-medium text-gray-800">{{ $video['title'] ?? 'فيديو المنتج' }}</h3>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="mt-12">
            <h2 class="text-2xl font-bold mb-6">{{ __('related_products') }}</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $relatedProduct)
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                        <a href="{{ route('user.products.show', $relatedProduct->slug) }}" class="block">
                            <img src="{{ $relatedProduct->image_url }}" 
                                alt="{{ $relatedProduct->name }}" 
                                class="w-full h-48 object-cover"
                                onerror="this.onerror=null; this.src='{{ asset('images/product-placeholder.svg') }}';">
                        </a>
                        <div class="p-4">
                            <a href="{{ route('user.products.show', $relatedProduct->slug) }}" class="block">
                                <h3 class="text-lg font-semibold mb-2 hover:text-yellow-600">{{ $relatedProduct->name }}</h3>
                            </a>
                            @if($relatedProduct->prices->isNotEmpty())
                                <p class="text-yellow-600 font-bold">
                                    @php
                                        $currentCountry = current_country();
                                        $relatedProductPrice = $relatedProduct->prices->where('country_id', $currentCountry->id)->first();
                                    @endphp
                                    @if($relatedProductPrice)
                                        {{ number_format($relatedProductPrice->price, 2) }} 
                                        {{ $currentCountry->currency_symbol }}
                                    @else
                                        {{ __('price_not_available') }}
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Quantity increment/decrement
        const decrementButtons = document.querySelectorAll('[data-action="decrement"]');
        const incrementButtons = document.querySelectorAll('[data-action="increment"]');
        const quantityInputs = document.querySelectorAll('input[name="quantity"]');
        
        decrementButtons.forEach((button, index) => {
            button.addEventListener('click', function() {
                const input = quantityInputs[index];
                const currentValue = parseInt(input.value);
                if (currentValue > parseInt(input.min)) {
                    input.value = currentValue - 1;
                }
            });
        });
        
        incrementButtons.forEach((button, index) => {
            button.addEventListener('click', function() {
                const input = quantityInputs[index];
                const currentValue = parseInt(input.value);
                if (currentValue < parseInt(input.max)) {
                    input.value = currentValue + 1;
                }
            });
        });
        
        // Image gallery
        const thumbnailImages = document.querySelectorAll('.thumbnail-image');
        const mainImage = document.getElementById('main-product-image');
        
        if (thumbnailImages.length > 0 && mainImage) {
            thumbnailImages.forEach(img => {
                img.addEventListener('click', function() {
                    mainImage.src = this.src;
                });
            });
        }
    });
</script>

<!-- Structured Data for SEO -->
<script type="application/ld+json">
@php
$jsonData = [
    "@context" => "https://schema.org/",
    "@type" => "Product",
    "name" => $product->name,
    "image" => !empty($product->images) ? array_map(function($img) { return asset($img); }, $product->images) : [asset('images/placeholder.jpg')],
    "description" => $seoDescription,
    "sku" => $product->sku,
    "mpn" => $product->sku,
    "brand" => [
        "@type" => "Brand",
        "name" => setting('site_name')
    ]
];

if ($productPrice) {
    $jsonData["offers"] = [
        "@type" => "Offer",
        "url" => url()->current(),
        "priceCurrency" => $productPrice->currency_code ?? 'SAR',
        "price" => $productPrice->price,
        "priceValidUntil" => now()->addMonths(6)->format('Y-m-d'),
        "availability" => $countryStock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock'
    ];
}

echo json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
@endphp
</script>
@endpush 