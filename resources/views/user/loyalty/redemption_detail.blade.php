@extends('layouts.user')

@section('title', 'تفاصيل الاستبدال')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">تفاصيل الاستبدال</h1>
            <p class="text-gray-600 mt-2">استبدال مكافأة {{ $redemption->reward->name }}</p>
        </div>
        <div>
            <a href="{{ route('loyalty.redemptions') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                العودة للاستبدالات
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
                <div class="border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800 p-4">معلومات المكافأة</h2>
                </div>
                <div class="p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            @if($redemption->reward->image)
                                <img src="{{ asset('storage/' . $redemption->reward->image) }}" alt="{{ $redemption->reward->name }}" class="h-24 w-24 object-cover rounded-lg">
                            @else
                                <div class="h-24 w-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m-8-4l8 4m8 0l-8 4m8-4v10a1 1 0 01-1 1h-14a1 1 0 01-1-1v-10"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="mr-6">
                            <h3 class="text-xl font-bold text-gray-800">{{ $redemption->reward->name }}</h3>
                            <p class="text-gray-600 mt-2">{{ $redemption->reward->description }}</p>
                            <div class="mt-4">
                                <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                    {{ $redemption->points_spent }} نقطة
                                </span>
                                <span class="mr-2 bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                    @switch($redemption->reward->type)
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
                                            {{ $redemption->reward->type }}
                                    @endswitch
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($redemption->reward->how_to_use)
                        <div class="mt-8">
                            <h4 class="text-lg font-semibold text-gray-800 mb-2">كيفية الاستخدام</h4>
                            <div class="bg-gray-50 p-4 rounded-lg text-gray-700">
                                {!! nl2br(e($redemption->reward->how_to_use)) !!}
                            </div>
                        </div>
                    @endif

                    @if($redemption->reward->terms)
                        <div class="mt-6">
                            <h4 class="text-lg font-semibold text-gray-800 mb-2">الشروط والأحكام</h4>
                            <div class="bg-gray-50 p-4 rounded-lg text-gray-700">
                                {!! nl2br(e($redemption->reward->terms)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="md:col-span-1">
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="bg-indigo-50 p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-2">تفاصيل الاستبدال</h2>
                </div>
                <div class="p-6">
                    <div class="mb-4 pb-4 border-b border-gray-200">
                        <p class="text-gray-600 mb-1">رقم الاستبدال:</p>
                        <p class="text-lg font-semibold text-gray-800">#{{ $redemption->id }}</p>
                    </div>

                    <div class="mb-4 pb-4 border-b border-gray-200">
                        <p class="text-gray-600 mb-1">تاريخ الاستبدال:</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $redemption->created_at->format('Y-m-d') }}</p>
                        <p class="text-sm text-gray-500">{{ $redemption->created_at->format('h:i A') }}</p>
                    </div>

                    <div class="mb-4 pb-4 border-b border-gray-200">
                        <p class="text-gray-600 mb-1">الحالة:</p>
                        <div>
                            @switch($redemption->status)
                                @case('pending')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        قيد الانتظار
                                    </span>
                                    @break
                                @case('processing')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        قيد المعالجة
                                    </span>
                                    @break
                                @case('completed')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        مكتمل
                                    </span>
                                    @break
                                @case('cancelled')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        ملغي
                                    </span>
                                    @break
                                @default
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ $redemption->status }}
                                    </span>
                            @endswitch
                        </div>
                    </div>

                    <div class="mb-4 pb-4 border-b border-gray-200">
                        <p class="text-gray-600 mb-1">النقاط المستخدمة:</p>
                        <p class="text-lg font-semibold text-indigo-600">{{ $redemption->points_spent }} نقطة</p>
                    </div>

                    @if($redemption->code)
                        <div class="mb-4">
                            <p class="text-gray-600 mb-1">كود الاستبدال:</p>
                            <div class="bg-gray-100 p-3 rounded-lg">
                                <p class="text-lg font-mono text-center font-semibold text-gray-800">{{ $redemption->code }}</p>
                            </div>
                        </div>
                    @endif

                    @if($redemption->status === 'pending')
                        <div class="mt-6">
                            <form action="{{ route('loyalty.redemptions.cancel', $redemption) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" onclick="return confirm('هل أنت متأكد من رغبتك في إلغاء هذا الاستبدال؟ سيتم إعادة النقاط إلى رصيدك.')">
                                    إلغاء الاستبدال
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            @if($redemption->notes || $redemption->admin_notes)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800 p-4">ملاحظات</h2>
                    </div>
                    <div class="p-6">
                        @if($redemption->notes)
                            <div class="mb-4">
                                <p class="text-gray-600 mb-1">ملاحظاتك:</p>
                                <p class="text-gray-800">{{ $redemption->notes }}</p>
                            </div>
                        @endif

                        @if($redemption->admin_notes)
                            <div>
                                <p class="text-gray-600 mb-1">ملاحظات الإدارة:</p>
                                <p class="text-gray-800">{{ $redemption->admin_notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 