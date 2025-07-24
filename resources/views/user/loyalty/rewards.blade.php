@extends('layouts.user')

@section('title', 'مكافآت الولاء')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">مكافآت الولاء</h1>
            <p class="text-gray-600 mt-2">استبدل نقاطك بمكافآت حصرية</p>
        </div>
        <div>
            <a href="{{ route('loyalty.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                العودة للوحة الولاء
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="p-4 bg-indigo-50 border-b border-indigo-100">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex items-center">
                    <div class="bg-indigo-100 rounded-full p-3">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <h2 class="text-lg font-semibold text-gray-800">رصيد نقاطك الحالي</h2>
                        <p class="text-2xl font-bold text-indigo-600">{{ $pointsBalance }} نقطة</p>
                    </div>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('loyalty.transactions') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                        عرض سجل المعاملات
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="border-b border-gray-200 p-4">
            <h2 class="text-lg font-semibold text-gray-800">تصفية المكافآت</h2>
        </div>
        <div class="p-4">
            <form action="{{ route('loyalty.rewards') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">نوع المكافأة</label>
                    <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">جميع المكافآت</option>
                        <option value="discount" {{ request('type') === 'discount' ? 'selected' : '' }}>خصم</option>
                        <option value="product" {{ request('type') === 'product' ? 'selected' : '' }}>منتج مجاني</option>
                        <option value="shipping" {{ request('type') === 'shipping' ? 'selected' : '' }}>شحن مجاني</option>
                        <option value="gift_card" {{ request('type') === 'gift_card' ? 'selected' : '' }}>بطاقة هدية</option>
                        <option value="other" {{ request('type') === 'other' ? 'selected' : '' }}>أخرى</option>
                    </select>
                </div>
                
                <div>
                    <label for="affordable" class="block text-sm font-medium text-gray-700 mb-1">إمكانية الاستبدال</label>
                    <select id="affordable" name="affordable" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">جميع المكافآت</option>
                        <option value="true" {{ request('affordable') === 'true' ? 'selected' : '' }}>المكافآت المتاحة فقط</option>
                    </select>
                </div>
                
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">الترتيب حسب</label>
                    <select id="sort" name="sort" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="points_required" {{ (request('sort') === 'points_required' || !request('sort')) ? 'selected' : '' }}>النقاط المطلوبة (تصاعدي)</option>
                        <option value="points_required_desc" {{ request('sort') === 'points_required_desc' ? 'selected' : '' }}>النقاط المطلوبة (تنازلي)</option>
                        <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>الأحدث</option>
                        <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>الاسم</option>
                    </select>
                    <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
                </div>
                
                <div class="md:col-span-3">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        تصفية
                    </button>
                    <a href="{{ route('loyalty.rewards') }}" class="mr-2 text-gray-600 hover:text-gray-800">
                        إعادة تعيين
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if($rewards->isEmpty())
        <div class="bg-white rounded-lg shadow-md overflow-hidden p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m-8-4l8 4m8 0l-8 4m8-4v10a1 1 0 01-1 1h-14a1 1 0 01-1-1v-10"></path>
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">لا توجد مكافآت متاحة</h3>
            <p class="mt-1 text-gray-500">لم يتم العثور على مكافآت تطابق معايير البحث.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($rewards as $reward)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    @if($reward->image)
                        <img src="{{ asset('storage/' . $reward->image) }}" alt="{{ $reward->name }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m-8-4l8 4m8 0l-8 4m8-4v10a1 1 0 01-1 1h-14a1 1 0 01-1-1v-10"></path>
                            </svg>
                        </div>
                    @endif
                    
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-900">{{ $reward->name }}</h3>
                            <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                {{ $reward->points_required }} نقطة
                            </span>
                        </div>
                        
                        <p class="text-gray-600 mb-4">{{ Str::limit($reward->description, 100) }}</p>
                        
                        @if($reward->stock_quantity !== null)
                            <div class="mb-4">
                                <p class="text-sm text-gray-500">
                                    الكمية المتاحة: 
                                    <span class="{{ $reward->stock_quantity < 10 ? 'text-red-600' : 'text-gray-700' }}">
                                        {{ $reward->stock_quantity }}
                                    </span>
                                </p>
                            </div>
                        @endif
                        
                        <div class="flex justify-between items-center">
                            <a href="{{ route('loyalty.rewards.show', $reward) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                                عرض التفاصيل
                            </a>
                            
                            @if($pointsBalance >= $reward->points_required && $reward->isAvailable())
                                <a href="{{ route('loyalty.rewards.show', $reward) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    استبدال
                                </a>
                            @else
                                <button disabled class="bg-gray-300 text-gray-500 font-bold py-2 px-4 rounded cursor-not-allowed">
                                    غير متاح
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-8">
            {{ $rewards->links() }}
        </div>
    @endif
</div>
@endsection 