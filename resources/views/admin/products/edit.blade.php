@extends('layouts.admin')

@section('title', 'تعديل المنتج')
@section('header', 'تعديل المنتج: ' . $product->name)

@section('content')
    <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-100">
        <!-- بطاقة معلومات المنتج الأساسية -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    بيانات المنتج الأساسية
                </h2>
                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">{{ $product->sku }}</span>
            </div>
            
            <!-- شريط التقدم -->
            <div class="w-full bg-gray-200 rounded-full h-2.5 mb-6">
                <div class="bg-blue-600 h-2.5 rounded-full" style="width: 25%"></div>
            </div>
        </div>

        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="product-form">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- اسم المنتج -->
                <div class="form-group">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <svg class="w-4 h-4 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                        </svg>
                        اسم المنتج <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- رمز المنتج (SKU) -->
                <div class="form-group">
                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <svg class="w-4 h-4 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                        </svg>
                        رمز المنتج (SKU) <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    @error('sku')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- الفئة -->
                <div class="form-group">
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <svg class="w-4 h-4 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        الفئة <span class="text-red-600">*</span>
                    </label>
                    <select name="category_id" id="category_id" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="">اختر الفئة</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- تكلفة المنتج -->
                <div class="form-group">
                    <label for="cost" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <svg class="w-4 h-4 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        تكلفة المنتج <span class="text-red-600">*</span>
                    </label>
                    <div class="relative">
                    <input type="number" name="cost" id="cost" value="{{ old('cost', $product->cost) }}" step="0.01" min="0" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors pr-8">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="text-gray-500">$</span>
                        </div>
                    </div>
                    @error('cost')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- الوزن -->
                <div class="form-group">
                    <label for="weight" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <svg class="w-4 h-4 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                        </svg>
                        الوزن (كجم) <span class="text-red-600">*</span>
                    </label>
                    <div class="relative">
                    <input type="number" name="weight" id="weight" value="{{ old('weight', $product->weight) }}" step="0.01" min="0" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors pr-12">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="text-gray-500">كجم</span>
                        </div>
                    </div>
                    @error('weight')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- الحالة -->
                <div class="form-group">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        <svg class="w-4 h-4 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        الحالة <span class="text-red-600">*</span>
                    </label>
                    <select name="status" id="status" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- الوصف -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                    <svg class="w-4 h-4 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                    </svg>
                    وصف المنتج <span class="text-red-600">*</span>
                </label>
                <textarea name="description" id="description" rows="4" required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- البلدان والأسعار -->
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    الأسعار حسب البلد <span class="text-red-600">*</span>
                </h3>
                <div class="bg-blue-50 p-6 rounded-lg border border-blue-100 shadow-inner">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($countries as $country)
                            <div class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100">
                                <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <input type="checkbox" name="countries[]" id="country_{{ $country->id }}" value="{{ $country->id }}" 
                                    {{ (is_array(old('countries')) && in_array($country->id, old('countries'))) || 
                                        ($product->countries->contains($country->id) && !is_array(old('countries'))) ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="country_{{ $country->id }}" class="mr-2 block text-sm font-medium text-gray-700 cursor-pointer select-none">
                                            <div class="flex items-center">
                                                <img src="{{ asset('images/flags/' . strtolower($country->code) . '.svg') }}" 
                                                    alt="{{ $country->name }}" 
                                                    class="h-5 w-auto mr-2 rounded shadow-sm"
                                                    onerror="this.src='{{ asset('images/flags/placeholder.svg') }}'">
                                                {{ $country->name }}
                                            </div>
                                        </label>
                                    </div>
                                    <span class="text-xs font-semibold bg-gray-100 text-gray-700 px-2 py-1 rounded-full">
                                        {{ $country->currency_code }}
                                    </span>
                                </div>
                                <div class="flex flex-col space-y-3">
                                    <div class="flex items-center">
                                        <span class="text-sm text-gray-500 w-24">السعر العادي:</span>
                                        <div class="relative flex-1">
                                        <input type="number" name="price[{{ $country->id }}]" 
                                            value="{{ old('price.' . $country->id, $prices[$country->id] ?? 0) }}" 
                                            step="0.01" min="0" placeholder="0.00"
                                                class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 w-full pr-8">
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                <span class="text-gray-500">{{ $country->currency_symbol }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-sm text-gray-500 w-24">سعر العرض:</span>
                                        <div class="relative flex-1">
                                            <input type="number" name="sale_price[{{ $country->id }}]" 
                                                value="{{ old('sale_price.' . $country->id, $sale_prices[$country->id] ?? 0) }}" 
                                                step="0.01" min="0" placeholder="0.00"
                                                class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 w-full pr-8">
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                <span class="text-gray-500">{{ $country->currency_symbol }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <label class="inline-flex items-center cursor-pointer select-none">
                                            <input type="checkbox" name="is_active[{{ $country->id }}]" value="1" 
                                                {{ old('is_active.' . $country->id, $is_active[$country->id] ?? 0) ? 'checked' : '' }}
                                                class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                            <span class="mr-2 text-sm text-gray-700">نشط في هذا البلد</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('countries')
                        <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('price.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- المخزون -->
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    المخزون
                </h3>
                
                <!-- ألوان المنتج -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                        </svg>
                        ألوان المنتج
                    </h3>
                    <div class="bg-purple-50 p-6 rounded-lg border border-purple-100 shadow-inner">
                        <div id="colors-container" class="space-y-4">
                            @php
                                $colors = old('colors', $product->colors ?? []);
                            @endphp
                            
                            @if(!empty($colors))
                                @foreach($colors as $index => $color)
                                    <div class="color-item bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div class="form-group">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">اسم اللون</label>
                                                <input type="text" name="colors[{{ $index }}][name]" value="{{ $color['name'] ?? '' }}" 
                                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            </div>
                                            <div class="form-group">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">كود اللون</label>
                                                <div class="flex">
                                                    <input type="color" name="colors[{{ $index }}][code]" value="{{ $color['code'] ?? '#000000' }}" 
                                                        class="h-10 w-10 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    <input type="text" name="colors[{{ $index }}][code_text]" value="{{ $color['code'] ?? '#000000' }}" 
                                                        class="mr-2 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                            </div>
                                            <div class="form-group flex items-end">
                                                <button type="button" class="remove-color bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-md transition-colors">
                                                    <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    حذف
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="color-item bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="form-group">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">اسم اللون</label>
                                            <input type="text" name="colors[0][name]" value="" 
                                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                        <div class="form-group">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">كود اللون</label>
                                            <div class="flex">
                                                <input type="color" name="colors[0][code]" value="#000000" 
                                                    class="h-10 w-10 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                <input type="text" name="colors[0][code_text]" value="#000000" 
                                                    class="mr-2 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            </div>
                                        </div>
                                        <div class="form-group flex items-end">
                                            <button type="button" class="remove-color bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-md transition-colors">
                                                <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                حذف
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="mt-4">
                            <button type="button" id="add-color" class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-md transition-colors">
                                <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                إضافة لون جديد
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- مقاسات المنتج -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        مقاسات المنتج
                    </h3>
                    <div class="bg-yellow-50 p-6 rounded-lg border border-yellow-100 shadow-inner">
                        <div id="sizes-container" class="space-y-4">
                            @php
                                $sizes = old('sizes', $product->sizes ?? []);
                            @endphp
                            
                            @if(!empty($sizes))
                                @foreach($sizes as $index => $size)
                                    <div class="size-item bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="form-group">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">المقاس</label>
                                                <input type="text" name="sizes[{{ $index }}][name]" value="{{ $size['name'] ?? '' }}" 
                                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            </div>
                                            <div class="form-group flex items-end">
                                                <button type="button" class="remove-size bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-md transition-colors">
                                                    <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    حذف
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="size-item bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="form-group">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">المقاس</label>
                                            <input type="text" name="sizes[0][name]" value="" 
                                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                        <div class="form-group flex items-end">
                                            <button type="button" class="remove-size bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-md transition-colors">
                                                <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                حذف
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="mt-4">
                            <button type="button" id="add-size" class="bg-yellow-600 hover:bg-yellow-700 text-white py-2 px-4 rounded-md transition-colors">
                                <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                إضافة مقاس جديد
                            </button>
                        </div>
                    </div>
                </div>
            
            <!-- الصور الحالية -->
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    صور المنتج
                </h3>

                </div>
             
            <!-- صور المنتج -->
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    صور المنتج
                </h3>
                <div class="bg-blue-50 p-6 rounded-lg border border-blue-100 shadow-inner">
                    <!-- الصور الحالية -->
                    @if(!empty($product->images))
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">الصور الحالية</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($product->images as $index => $image)
                                    <div class="relative group">
                                        <img src="{{ asset($image) }}" alt="صورة المنتج {{ $index + 1 }}" class="w-full h-40 object-cover rounded-lg shadow-sm">
                                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center rounded-lg">
                                            <button type="button" class="remove-existing-image text-white bg-red-600 hover:bg-red-700 p-2 rounded-full transition-colors" data-image-index="{{ $index }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <input type="hidden" name="existing_images[]" value="{{ $image }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <!-- إضافة صور جديدة -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">إضافة صور جديدة</label>
                        <div class="flex items-center justify-center w-full">
                            <label for="product_images" class="flex flex-col items-center justify-center w-full h-64 border-2 border-blue-300 border-dashed rounded-lg cursor-pointer bg-white hover:bg-blue-50 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-10 h-10 mb-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">اضغط للتحميل</span> أو اسحب وأفلت</p>
                                    <p class="text-xs text-gray-500">PNG, JPG, JPEG أو WEBP (الحد الأقصى: 5 ميجابايت لكل صورة)</p>
                                </div>
                                <input id="product_images" name="product_images[]" type="file" class="hidden" multiple accept="image/png, image/jpeg, image/jpg, image/webp" />
                            </label>
                        </div>
                        <div id="image-preview" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4"></div>
                    </div>
                </div>
            </div>
            
            <!-- فيديوهات المنتج -->
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    فيديوهات المنتج
                </h3>
                <div class="bg-red-50 p-6 rounded-lg border border-red-100 shadow-inner">
                    <div id="videos-container" class="space-y-4">
                        @php
                            $videos = old('videos', $product->videos ?? []);
                        @endphp
                        
                        @if(!empty($videos))
                            @foreach($videos as $index => $video)
                                <div class="video-item bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="form-group">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">عنوان الفيديو</label>
                                            <input type="text" name="videos[{{ $index }}][title]" value="{{ $video['title'] ?? '' }}" 
                                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                        <div class="form-group">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">رابط الفيديو (YouTube أو Vimeo)</label>
                                            <input type="url" name="videos[{{ $index }}][url]" value="{{ $video['url'] ?? '' }}" 
                                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                        <div class="form-group flex items-end">
                                            <button type="button" class="remove-video bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-md transition-colors">
                                                <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                حذف
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="video-item bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">عنوان الفيديو</label>
                                        <input type="text" name="videos[0][title]" value="" 
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">رابط الفيديو (YouTube أو Vimeo)</label>
                                        <input type="url" name="videos[0][url]" value="" 
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div class="form-group flex items-end">
                                        <button type="button" class="remove-video bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-md transition-colors">
                                            <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            حذف
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="mt-4">
                        <button type="button" id="add-video" class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md transition-colors">
                            <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            إضافة فيديو جديد
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-end space-x-3 rtl:space-x-reverse">
                <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 ml-2 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2 rtl:ml-2 rtl:mr-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    إلغاء
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2 rtl:ml-2 rtl:mr-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    تحديث المنتج
                </button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .form-group {
            margin-bottom: 1rem;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c5c5c5;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a0a0a0;
        }
        .aspect-w-1 {
            position: relative;
            padding-bottom: 100%;
        }
        .aspect-w-1 img {
            position: absolute;
            height: 100%;
            width: 100%;
            top: 0;
            left: 0;
            object-fit: cover;
        }
    </style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // معالجة الألوان
        const colorsContainer = document.getElementById('colors-container');
        const addColorBtn = document.getElementById('add-color');
        let colorIndex = document.querySelectorAll('.color-item').length;

        // إضافة لون جديد
        addColorBtn.addEventListener('click', function() {
            const colorItem = document.createElement('div');
            colorItem.className = 'color-item bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100';
            colorItem.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 mb-1">اسم اللون</label>
                        <input type="text" name="colors[${colorIndex}][name]" value="" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 mb-1">كود اللون</label>
                        <div class="flex">
                            <input type="color" name="colors[${colorIndex}][code]" value="#000000" 
                                class="h-10 w-10 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <input type="text" name="colors[${colorIndex}][code_text]" value="#000000" 
                                class="mr-2 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="form-group flex items-end">
                        <button type="button" class="remove-color bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-md transition-colors">
                            <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            حذف
                        </button>
                    </div>
                </div>
            `;
            colorsContainer.appendChild(colorItem);
            colorIndex++;
            
            // ربط حدث تغيير لون بالحقل النصي
            const colorInputs = colorItem.querySelectorAll('input[type="color"]');
            const textInputs = colorItem.querySelectorAll('input[name$="[code_text]"]');
            
            colorInputs.forEach((input, i) => {
                input.addEventListener('input', function() {
                    textInputs[i].value = this.value;
                });
            });
            
            textInputs.forEach((input, i) => {
                input.addEventListener('input', function() {
                    colorInputs[i].value = this.value;
                });
            });
            
            // حدث حذف اللون
            colorItem.querySelector('.remove-color').addEventListener('click', function() {
                colorItem.remove();
            });
        });
        
        // حدث حذف الألوان الموجودة
        document.querySelectorAll('.remove-color').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.color-item').remove();
            });
        });
        
        // ربط حدث تغيير لون بالحقل النصي للألوان الموجودة
        document.querySelectorAll('.color-item').forEach(item => {
            const colorInputs = item.querySelectorAll('input[type="color"]');
            const textInputs = item.querySelectorAll('input[name$="[code_text]"]');
            
            colorInputs.forEach((input, i) => {
                input.addEventListener('input', function() {
                    textInputs[i].value = this.value;
                });
            });
            
            textInputs.forEach((input, i) => {
                input.addEventListener('input', function() {
                    colorInputs[i].value = this.value;
                });
            });
        });
        
        // معالجة المقاسات
        const sizesContainer = document.getElementById('sizes-container');
        const addSizeBtn = document.getElementById('add-size');
        let sizeIndex = document.querySelectorAll('.size-item').length;
        
        // إضافة مقاس جديد
        addSizeBtn.addEventListener('click', function() {
            const sizeItem = document.createElement('div');
            sizeItem.className = 'size-item bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100';
            sizeItem.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 mb-1">المقاس</label>
                        <input type="text" name="sizes[${sizeIndex}][name]" value="" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="form-group flex items-end">
                        <button type="button" class="remove-size bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-md transition-colors">
                            <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            حذف
                        </button>
                    </div>
                </div>
            `;
            sizesContainer.appendChild(sizeItem);
            sizeIndex++;
            
            // حدث حذف المقاس
            sizeItem.querySelector('.remove-size').addEventListener('click', function() {
                sizeItem.remove();
            });
        });
        
        // حدث حذف المقاسات الموجودة
        document.querySelectorAll('.remove-size').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.size-item').remove();
            });
        });
        
        // معالجة الفيديوهات
        const videosContainer = document.getElementById('videos-container');
        const addVideoBtn = document.getElementById('add-video');
        let videoIndex = document.querySelectorAll('.video-item').length;
        
        // إضافة فيديو جديد
        addVideoBtn.addEventListener('click', function() {
            const videoItem = document.createElement('div');
            videoItem.className = 'video-item bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100';
            videoItem.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 mb-1">عنوان الفيديو</label>
                        <input type="text" name="videos[${videoIndex}][title]" value="" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 mb-1">رابط الفيديو (YouTube أو Vimeo)</label>
                        <input type="url" name="videos[${videoIndex}][url]" value="" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="form-group flex items-end">
                        <button type="button" class="remove-video bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-md transition-colors">
                            <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            حذف
                        </button>
                    </div>
                </div>
            `;
            videosContainer.appendChild(videoItem);
            videoIndex++;
            
            // حدث حذف الفيديو
            videoItem.querySelector('.remove-video').addEventListener('click', function() {
                videoItem.remove();
            });
        });
        
        // حدث حذف الفيديوهات الموجودة
        document.querySelectorAll('.remove-video').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.video-item').remove();
            });
        });
        
        // معالجة معاينة الصور
        const imageInput = document.getElementById('product_images');
        const imagePreview = document.getElementById('image-preview');
        
        imageInput.addEventListener('change', function() {
            imagePreview.innerHTML = '';
            
            if (this.files) {
                for (let i = 0; i < this.files.length; i++) {
                    const file = this.files[i];
                    if (!file.type.match('image.*')) continue;
                    
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'relative';
                        div.innerHTML = `
                            <img src="${e.target.result}" alt="معاينة الصورة" class="w-full h-40 object-cover rounded-lg shadow-sm">
                            <div class="absolute top-2 right-2 bg-white bg-opacity-75 rounded-full p-1">
                                <span class="text-xs font-medium text-gray-700">${file.name}</span>
                            </div>
                        `;
                        imagePreview.appendChild(div);
                    };
                    
                    reader.readAsDataURL(file);
                }
            }
        });
        
        // حدث حذف الصور الموجودة
        document.querySelectorAll('.remove-existing-image').forEach(button => {
            button.addEventListener('click', function() {
                const imageIndex = this.getAttribute('data-image-index');
                const imageContainer = this.closest('.relative');
                
                // إضافة حقل مخفي لإخبار الخادم بحذف هذه الصورة
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'images_to_remove[]';
                input.value = imageIndex;
                document.getElementById('product-form').appendChild(input);
                
                // إخفاء الصورة من واجهة المستخدم
                imageContainer.remove();
            });
        });
    });
</script>
@endpush 