@extends('layouts.user')

@section('title', 'تفاصيل المكافأة - ' . $reward->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ $reward->name }}</h1>
            <p class="text-gray-600 mt-2">تفاصيل المكافأة</p>
        </div>
        <div>
            <a href="{{ route('loyalty.rewards') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                العودة للمكافآت
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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="mb-8">
                        @if($reward->image)
                            <img src="{{ asset('storage/' . $reward->image) }}" alt="{{ $reward->name }}" class="w-full h-64 object-cover rounded-lg">
                        @else
                            <div class="w-full h-64 bg-gray-200 flex items-center justify-center rounded-lg">
                                <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m-8-4l8 4m8 0l-8 4m8-4v10a1 1 0 01-1 1h-14a1 1 0 01-1-1v-10"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">وصف المكافأة</h2>
                        <div class="prose max-w-none text-gray-600">
                            {!! nl2br(e($reward->description)) !!}
                        </div>
                    </div>

                    @if($reward->terms)
                        <div class="mb-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-4">الشروط والأحكام</h2>
                            <div class="prose max-w-none text-gray-600">
                                {!! nl2br(e($reward->terms)) !!}
                            </div>
                        </div>
                    @endif

                    @if($reward->how_to_use)
                        <div class="mb-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-4">كيفية الاستخدام</h2>
                            <div class="prose max-w-none text-gray-600">
                                {!! nl2br(e($reward->how_to_use)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="md:col-span-1">
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="bg-indigo-50 p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-2">تفاصيل المكافأة</h2>
                </div>
                <div class="p-6">
                    <div class="mb-4 pb-4 border-b border-gray-200">
                        <p class="text-gray-600 mb-1">النقاط المطلوبة:</p>
                        <p class="text-2xl font-bold text-indigo-600">{{ $reward->points_required }} نقطة</p>
                    </div>

                    <div class="mb-4 pb-4 border-b border-gray-200">
                        <p class="text-gray-600 mb-1">رصيدك الحالي:</p>
                        <p class="text-xl font-semibold {{ $pointsBalance >= $reward->points_required ? 'text-green-600' : 'text-red-600' }}">
                            {{ $pointsBalance }} نقطة
                        </p>
                        @if($pointsBalance < $reward->points_required)
                            <p class="text-sm text-red-600 mt-1">
                                تحتاج {{ $reward->points_required - $pointsBalance }} نقطة إضافية
                            </p>
                        @endif
                    </div>

                    @if($reward->stock_quantity !== null)
                        <div class="mb-4 pb-4 border-b border-gray-200">
                            <p class="text-gray-600 mb-1">الكمية المتاحة:</p>
                            <p class="text-lg font-semibold {{ $reward->stock_quantity < 10 ? 'text-red-600' : 'text-gray-800' }}">
                                {{ $reward->stock_quantity }}
                            </p>
                        </div>
                    @endif

                    @if($reward->expiry_date)
                        <div class="mb-4 pb-4 border-b border-gray-200">
                            <p class="text-gray-600 mb-1">متاح حتى:</p>
                            <p class="text-lg font-semibold text-gray-800">
                                {{ \Carbon\Carbon::parse($reward->expiry_date)->format('Y-m-d') }}
                            </p>
                        </div>
                    @endif

                    <div class="mb-4">
                        <p class="text-gray-600 mb-1">نوع المكافأة:</p>
                        <p class="text-lg font-semibold text-gray-800">
                            @switch($reward->type)
                                @case('discount')
                                    خصم
                                    @break
                                @case('product')
                                    منتج مجاني
                                    @break
                                @case('shipping')
                                    شحن مجاني
                                    @break
                                @case('gift_card')
                                    بطاقة هدية
                                    @break
                                @default
                                    {{ $reward->type }}
                            @endswitch
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    @if($canRedeem)
                        <form action="{{ route('loyalty.rewards.redeem', $reward) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline">
                                استبدال المكافأة
                            </button>
                            <p class="text-sm text-gray-500 mt-2 text-center">
                                سيتم خصم {{ $reward->points_required }} نقطة من رصيدك
                            </p>
                        </form>
                    @else
                        <button disabled class="w-full bg-gray-300 text-gray-500 font-bold py-3 px-4 rounded cursor-not-allowed">
                            غير متاح للاستبدال
                        </button>
                        @if($pointsBalance < $reward->points_required)
                            <p class="text-sm text-red-600 mt-2 text-center">
                                نقاط غير كافية
                            </p>
                        @elseif($reward->stock_quantity !== null && $reward->stock_quantity <= 0)
                            <p class="text-sm text-red-600 mt-2 text-center">
                                نفدت الكمية
                            </p>
                        @elseif($reward->expiry_date && \Carbon\Carbon::parse($reward->expiry_date)->isPast())
                            <p class="text-sm text-red-600 mt-2 text-center">
                                انتهت صلاحية المكافأة
                            </p>
                        @else
                            <p class="text-sm text-red-600 mt-2 text-center">
                                المكافأة غير متاحة حالياً
                            </p>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 