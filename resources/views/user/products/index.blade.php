@extends('layouts.user')

@section('title', __('products'))

@section('content')
<div class="bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Debug information removed -->
        
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Left Sidebar - Filters -->
            <div class="w-full md:w-1/4 lg:w-1/5">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6 border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                    <div class="px-4 py-5 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            {{ __('filter_products') }}
                        </h3>
                    </div>
                    <div class="p-5">
                        <form action="{{ route('user.products.index') }}" method="GET" id="filter-form">
                            <!-- Categories -->
                            <div class="mb-6">
                                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    {{ __('categories') }}
                                </h4>
                                <div class="space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                                    <div class="flex items-center hover:bg-blue-50 p-1 rounded-md transition-colors">
                                        <input id="all-categories" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                            {{ !request()->has('categories') ? 'checked' : '' }}>
                                        <label for="all-categories" class="mr-2 rtl:ml-2 rtl:mr-0 block text-sm text-gray-700 cursor-pointer select-none">
                                            {{ __('all_categories') }}
                                        </label>
                                    </div>
                                    @foreach($categories as $category)
                                    <div class="flex items-center hover:bg-blue-50 p-1 rounded-md transition-colors">
                                        <input id="cat-{{ $category->id }}" name="categories[]" type="checkbox" 
                                            value="{{ $category->id }}" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded category-checkbox"
                                            @if(request()->has('categories') && in_array($category->id, request('categories'))) checked @endif>
                                        <label for="cat-{{ $category->id }}" class="mr-2 rtl:ml-2 rtl:mr-0 block text-sm text-gray-700 cursor-pointer select-none">
                                            {{ $category->name }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-4 mb-6">
                                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ __('price_range') }}
                                </h4>
                                <div class="flex items-center space-x-4 rtl:space-x-reverse mb-3">
                                    <div class="w-1/2">
                                        <label for="min_price" class="block text-xs text-gray-500 mb-1">{{ __('from') }}</label>
                                        <input type="number" id="min_price" name="min_price" placeholder="{{ __('from') }}" value="{{ request('min_price') }}"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </div>
                                    <span class="text-gray-500">-</span>
                                    <div class="w-1/2">
                                        <label for="max_price" class="block text-xs text-gray-500 mb-1">{{ __('to') }}</label>
                                        <input type="number" id="max_price" name="max_price" placeholder="{{ __('to') }}" value="{{ request('max_price') }}"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="h-2 bg-gray-200 rounded-full">
                                        <div class="h-2 bg-blue-500 rounded-full" style="width: 60%"></div>
                                </div>
                            </div>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-4 mb-6">
                                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ __('availability') }}
                                </h4>
                                <div class="flex items-center hover:bg-blue-50 p-1 rounded-md transition-colors">
                                    <input id="in-stock" name="in_stock" type="checkbox" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        {{ request('in_stock') ? 'checked' : '' }}>
                                    <label for="in-stock" class="mr-2 rtl:ml-2 rtl:mr-0 block text-sm text-gray-700 cursor-pointer select-none">
                                        {{ __('in_stock') }}
                                    </label>
                                </div>
                            </div>
                            
                            <!-- إضافة فلتر التقييم -->
                            <div class="border-t border-gray-200 pt-4 mb-6">
                                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                    </svg>
                                    {{ __('rating') }}
                                </h4>
                                <div class="space-y-2">
                                    @for($i = 5; $i >= 1; $i--)
                                        <div class="flex items-center hover:bg-blue-50 p-1 rounded-md transition-colors">
                                            <input id="rating-{{ $i }}" name="rating" type="radio" value="{{ $i }}" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                                {{ request('rating') == $i ? 'checked' : '' }}>
                                            <label for="rating-{{ $i }}" class="mr-2 rtl:ml-2 rtl:mr-0 block text-sm text-gray-700 cursor-pointer select-none flex items-center">
                                                @for($j = 1; $j <= 5; $j++)
                                                    @if($j <= $i)
                                                        <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                    @endif
                                                @endfor
                                                <span class="ml-1">{{ __('and_up') }}</span>
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                            
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                                {{ __('apply_filter') }}
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Related Categories -->
                @if($categories->count() > 0)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                    <div class="px-4 py-5 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            {{ __('related_categories') }}
                        </h3>
                    </div>
                    <div class="p-5">
                        <ul class="divide-y divide-gray-200">
                            @foreach($categories->take(5) as $category)
                            <li class="py-2 hover:bg-blue-50 px-2 rounded-md transition-colors">
                                <a href="{{ route('user.products.index', ['category_id' => $category->id]) }}" class="text-blue-600 hover:text-blue-700 font-medium flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                                    </svg>
                                    {{ $category->name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Main Content - Products -->
            <div class="w-full md:w-3/4 lg:w-4/5">
                <!-- Category Quick Filter -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6 border border-gray-100">
                    <div class="p-3 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                        <h3 class="text-md font-bold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            {{ __('browse_by_category') }}
                        </h3>
                    </div>
                    <div class="p-3 overflow-x-auto">
                        <div class="flex space-x-2 rtl:space-x-reverse">
                            <a href="{{ route('user.products.index') }}" 
                               class="whitespace-nowrap px-4 py-2 rounded-full text-sm {{ !request('category_id') ? 'bg-blue-600 text-white font-medium' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors duration-200">
                                {{ __('all_categories') }}
                            </a>
                            
                            @foreach($categories->take(10) as $category)
                                <a href="{{ route('user.products.index', ['category_id' => $category->id]) }}" 
                                   class="whitespace-nowrap px-4 py-2 rounded-full text-sm {{ request('category_id') == $category->id ? 'bg-blue-600 text-white font-medium' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors duration-200">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Results Header -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6 border border-gray-100">
                    <div class="p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-gradient-to-r from-white to-blue-50">
                        <div>
                            <h1 class="text-xl font-bold text-gray-900 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                {{ __('search_results') }}
                            </h1>
                            <p class="mt-1 text-sm text-gray-500 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                {{ __('showing') }} {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} {{ __('of') }} {{ $products->total() ?? 0 }} {{ __('products') }}
                            </p>
                        </div>
                        <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row items-center">
                            <span class="text-sm text-gray-700 mr-2 rtl:ml-2 rtl:mr-0 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"></path>
                                </svg>
                                {{ __('sort_by') }}:
                            </span>
                            <form action="{{ route('user.products.index') }}" method="GET" id="sort-form" class="flex">
                                @foreach(request()->except('sort', 'direction') as $key => $value)
                                    @if(is_array($value))
                                        @foreach($value as $item)
                                            <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                                        @endforeach
                                    @else
                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                    @endif
                                @endforeach
                                
                                <div class="flex rounded-md shadow-sm">
                                    <select name="sort" onchange="document.getElementById('sort-form').submit()" 
                                        class="rounded-l-md rtl:rounded-r-md rtl:rounded-l-none border-r-0 rtl:border-l-0 rtl:border-r border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <option value="created_at" @if(request('sort', 'created_at') == 'created_at') selected @endif>{{ __('newest') }}</option>
                                        <option value="price" @if(request('sort') == 'price') selected @endif>{{ __('price') }}</option>
                                        <option value="name" @if(request('sort') == 'name') selected @endif>{{ __('name') }}</option>
                                    </select>
                                    
                                    <select name="direction" onchange="document.getElementById('sort-form').submit()" 
                                        class="rounded-r-md rtl:rounded-l-md rtl:rounded-r-none border-l-0 rtl:border-r-0 rtl:border-l border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <option value="desc" @if(request('direction', 'desc') == 'desc') selected @endif>{{ __('descending') }}</option>
                                        <option value="asc" @if(request('direction') == 'asc') selected @endif>{{ __('ascending') }}</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Quick Filter Buttons -->
                    <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 flex flex-wrap gap-2">
                        <a href="{{ route('user.products.index') }}" 
                           class="inline-flex items-center px-3 py-1 rounded-full text-sm {{ !request('quick_filter') ? 'bg-blue-100 text-blue-800 font-medium' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            {{ __('all_products') }}
                        </a>
                        <a href="{{ route('user.products.index', ['quick_filter' => 'new', 'sort' => 'created_at', 'direction' => 'desc']) }}" 
                           class="inline-flex items-center px-3 py-1 rounded-full text-sm {{ request('quick_filter') == 'new' ? 'bg-blue-100 text-blue-800 font-medium' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ __('new_arrivals') }}
                        </a>
                        <a href="{{ route('user.products.index', ['quick_filter' => 'popular', 'sort' => 'views', 'direction' => 'desc']) }}" 
                           class="inline-flex items-center px-3 py-1 rounded-full text-sm {{ request('quick_filter') == 'popular' ? 'bg-blue-100 text-blue-800 font-medium' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            {{ __('popular') }}
                        </a>
                        <a href="{{ route('user.products.index', ['quick_filter' => 'sale', 'has_discount' => 1]) }}" 
                           class="inline-flex items-center px-3 py-1 rounded-full text-sm {{ request('quick_filter') == 'sale' ? 'bg-blue-100 text-blue-800 font-medium' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ __('on_sale') }}
                        </a>
                        <a href="{{ route('user.products.index', ['quick_filter' => 'instock', 'in_stock' => 1]) }}" 
                           class="inline-flex items-center px-3 py-1 rounded-full text-sm {{ request('quick_filter') == 'instock' ? 'bg-blue-100 text-blue-800 font-medium' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('in_stock') }}
                        </a>
                    </div>
                </div>
                
                <!-- Products Grid -->
                @if($products->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($products as $product)
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col h-full border border-gray-100 group">
                                <!-- Product Image -->
                                <a href="{{ route('user.products.show', $product->slug) }}" class="block relative overflow-hidden">
                                    <div class="relative h-52 overflow-hidden">
                                    <img src="{{ $product->image_url }}" 
                                            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500" 
                                            alt="{{ $product->name }}" loading="lazy"
                                            onerror="this.onerror=null; this.src='{{ asset('images/product-placeholder.svg') }}';">
                                        
                                        <!-- Status Badge -->
                                        @php
                                            $currentCountry = current_country();
                                            $isInStock = $product->isInStockInCountry($currentCountry->id);
                                        @endphp
                                        
                                        @if($isInStock)
                                            <span class="absolute top-2 right-2 inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 shadow">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                {{ __('in_stock') }}
                                            </span>
                                        @else
                                            <span class="absolute top-2 right-2 inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800 shadow">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                {{ __('out_of_stock') }}
                                            </span>
                                        @endif
                                        
                                        <!-- New Badge -->
                                        @if($product->created_at >= now()->subDays(7))
                                            <span class="absolute top-2 left-2 inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 shadow">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                {{ __('new') }}
                                            </span>
                                        @endif
                                        
                                        <!-- Sale Badge -->
                                        @php
                                            $productPrice = $product->prices->where('country_id', $currentCountry->id)->first();
                                            $hasDiscount = $productPrice && isset($productPrice->sale_price) && $productPrice->sale_price > 0 && $productPrice->sale_price < $productPrice->price;
                                        @endphp
                                        
                                        @if($hasDiscount)
                                            <span class="absolute bottom-2 left-2 inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800 shadow">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ __('sale') }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Quick Add to Cart Button (Overlay) -->
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                                        <form action="{{ route('cart.add') }}" method="POST" class="inline-block transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-full shadow-lg transform hover:scale-105 transition-all duration-300 flex items-center">
                                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                                {{ __('add_to_cart') }}
                                            </button>
                                        </form>
                                    </div>
                                </a>
                                
                                <!-- Product Details -->
                                <div class="p-5 flex flex-col flex-grow">
                                    <!-- Category -->
                                    <div class="mb-2">
                                        <a href="{{ route('user.products.index', ['category_id' => $product->category_id]) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                            {{ $product->category->name ?? __('uncategorized') }}
                                        </a>
                                    </div>
                                    
                                    <!-- Product Name (Arabic & English) -->
                                    <h3 class="text-lg font-bold mb-2 text-gray-900 hover:text-blue-600 transition-colors line-clamp-2">
                                        <a href="{{ route('user.products.show', $product->slug) }}" class="hover:text-blue-600">
                                            {{ $product->name }}
                                            @if(app()->getLocale() == 'ar' && isset($product->attributes['name_en']))
                                                <span class="block text-sm text-gray-500 mt-1 font-normal">{{ $product->attributes['name_en'] }}</span>
                                            @elseif(app()->getLocale() == 'en' && isset($product->attributes['name_ar']))
                                                <span class="block text-sm text-gray-500 mt-1 font-normal">{{ $product->attributes['name_ar'] }}</span>
                                            @endif
                                        </a>
                                    </h3>
                                    
                                    <!-- Star Rating -->
                                    <div class="flex items-center mb-3">
                                        @php
                                            // Placeholder for actual rating logic
                                            $rating = 4; // This should come from your actual rating data
                                        @endphp
                                        
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $rating)
                                                <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @endif
                                        @endfor
                                        
                                        <span class="text-xs text-gray-500 ml-1 rtl:mr-1 rtl:ml-0">({{ rand(5, 50) }})</span>
                                    </div>
                                    
                                    <!-- Price -->
                                    <div class="mt-auto">
                                        @php
                                            $currentCountry = current_country();
                                            $productPrice = $product->prices->where('country_id', $currentCountry->id)->first();
                                        @endphp
                                        
                                        @if($productPrice)
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    @if($hasDiscount)
                                                        <span class="text-sm text-gray-500 line-through">{{ number_format($productPrice->price, 2) }} {{ $currentCountry->currency_symbol }}</span>
                                                        <span class="text-xl font-bold text-red-600 block">
                                                            {{ number_format($productPrice->sale_price, 2) }} 
                                                            <span class="text-sm">{{ $currentCountry->currency_symbol }}</span>
                                                        </span>
                                                    @else
                                                        <span class="text-xl font-bold text-blue-600">
                                                            {{ number_format($productPrice->price, 2) }} 
                                                            <span class="text-sm">{{ $currentCountry->currency_symbol }}</span>
                                                </span>
                                                    @endif
                                                </div>
                                                
                                                <!-- Country Flag -->
                                                <span class="inline-flex items-center">
                                                    <img src="{{ asset('images/flags/' . strtolower($currentCountry->code) . '.svg') }}" 
                                                        alt="{{ $currentCountry->name }}" 
                                                        class="h-4 w-auto rounded-sm shadow-sm" 
                                                        onerror="this.src='{{ asset('images/flags/placeholder.svg') }}'">
                                                    </span>
                                            </div>
                                        @else
                                            <span class="text-lg font-bold text-gray-500">{{ __('price_not_available') }}</span>
                                        @endif
                                    </div>
                                    
                                    <!-- Add to Cart Button -->
                                    <form action="{{ route('cart.add') }}" method="POST" class="mt-4">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md shadow-sm flex items-center justify-center transition-colors duration-300">
                                            <svg class="w-5 h-5 mr-2 rtl:ml-2 rtl:mr-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            {{ __('add_to_cart') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-8">
                        <div class="bg-white rounded-lg shadow-md p-4 border border-gray-100">
                            <div class="flex items-center justify-between flex-wrap gap-4">
                                <div class="text-sm text-gray-700">
                                    {{ __('showing') }} 
                                    <span class="font-medium">{{ $products->firstItem() ?? 0 }}</span> 
                                    {{ __('to') }} 
                                    <span class="font-medium">{{ $products->lastItem() ?? 0 }}</span> 
                                    {{ __('of') }} 
                                    <span class="font-medium">{{ $products->total() ?? 0 }}</span> 
                                    {{ __('results') }}
                                </div>
                                <div>
                        {{ $products->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden p-8 text-center border border-gray-100">
                        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-4 text-xl font-medium text-gray-900">{{ __('no_products_found') }}</h3>
                        <p class="mt-2 text-gray-500">{{ __('try_different_filters') }}</p>
                        <div class="mt-6">
                            <a href="{{ route('user.products.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                {{ __('clear_filters') }}
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle "All Categories" checkbox
        const allCategoriesCheckbox = document.getElementById('all-categories');
        const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
        
        if (allCategoriesCheckbox) {
            allCategoriesCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    categoryCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                }
            });
            
            categoryCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        allCategoriesCheckbox.checked = false;
                    } else if ([...categoryCheckboxes].every(cb => !cb.checked)) {
                        allCategoriesCheckbox.checked = true;
                    }
                });
            });
        }
        
        // Auto-submit filters when changed
        const autoSubmitElements = document.querySelectorAll('#filter-form select, #filter-form input[type="checkbox"], #filter-form input[type="radio"]');
        autoSubmitElements.forEach(element => {
            element.addEventListener('change', function() {
                // Show loading overlay
                const loadingOverlay = document.createElement('div');
                loadingOverlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
                loadingOverlay.innerHTML = `
                    <div class="bg-white p-4 rounded-lg shadow-lg flex items-center">
                        <svg class="animate-spin h-6 w-6 text-blue-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-gray-700 font-medium">{{ __('loading') }}...</span>
                    </div>
                `;
                document.body.appendChild(loadingOverlay);
                
                // Submit the form
                document.getElementById('filter-form').submit();
            });
        });
        
        // Price range slider functionality
        const minPriceInput = document.getElementById('min_price');
        const maxPriceInput = document.getElementById('max_price');
        const priceRangeIndicator = document.querySelector('.bg-blue-500.rounded-full');
        
        function updatePriceRangeIndicator() {
            if (minPriceInput && maxPriceInput && priceRangeIndicator) {
                const min = parseFloat(minPriceInput.value) || 0;
                const max = parseFloat(maxPriceInput.value) || 1000;
                
                // Calculate percentage width and position
                const minPercent = (min / 1000) * 100;
                const maxPercent = (max / 1000) * 100;
                const width = maxPercent - minPercent;
                
                // Update the indicator
                priceRangeIndicator.style.width = `${width}%`;
                priceRangeIndicator.style.marginLeft = `${minPercent}%`;
            }
        }
        
        if (minPriceInput && maxPriceInput) {
            minPriceInput.addEventListener('input', updatePriceRangeIndicator);
            maxPriceInput.addEventListener('input', updatePriceRangeIndicator);
            updatePriceRangeIndicator(); // Initialize on page load
        }
        
        // Add 'custom-scrollbar' class to scrollable containers
        const scrollContainers = document.querySelectorAll('.overflow-x-auto, .overflow-y-auto');
        scrollContainers.forEach(container => {
            container.classList.add('custom-scrollbar');
        });
    });
</script>
@endpush 

@push('styles')
<style>
    /* تعريف أسلوب الاقتصاص للنصوص الطويلة */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* تعريف أسلوب شريط التمرير المخصص */
    .custom-scrollbar::-webkit-scrollbar {
        height: 6px;
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
    
    /* تأثيرات حركية للبطاقات */
    .product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .product-card:hover {
        transform: translateY(-5px);
    }
</style>
@endpush 