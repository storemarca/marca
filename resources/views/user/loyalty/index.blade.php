@extends('layouts.user')

@section('title', 'نظام الولاء')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">نظام الولاء</h1>
        <p class="text-gray-600 mt-2">استمتع بمكافآت حصرية كعضو في برنامج الولاء</p>
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
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6 text-white">
                <h2 class="text-xl font-bold">رصيد النقاط</h2>
                <div class="mt-4">
                    <span class="text-4xl font-bold">{{ $loyaltyPoints ? $loyaltyPoints->points_balance : 0 }}</span>
                    <span class="text-sm ml-1">نقطة</span>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">يمكنك استبدال نقاطك بمكافآت حصرية</p>
                <a href="{{ route('loyalty.rewards') }}" class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    استبدال النقاط
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-teal-500 p-6 text-white">
                <h2 class="text-xl font-bold">مستوى العضوية</h2>
                <div class="mt-4">
                    <span class="text-2xl font-bold">{{ $currentTier ? $currentTier->name : 'عضو جديد' }}</span>
                </div>
            </div>
            <div class="p-6">
                @if($nextTier && $loyaltyPoints)
                    <div class="mb-4">
                        <p class="text-gray-600 mb-2">المستوى التالي: {{ $nextTier->name }}</p>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            @php
                                $progress = 0;
                                if ($currentTier) {
                                    $pointsNeeded = $nextTier->required_points - $currentTier->required_points;
                                    $pointsEarned = $loyaltyPoints->points_balance - $currentTier->required_points;
                                    $progress = min(100, max(0, ($pointsEarned / $pointsNeeded) * 100));
                                }
                            @endphp
                            <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $progress }}%"></div>
                        </div>
                        <p class="text-gray-600 mt-2 text-sm">
                            {{ $loyaltyPoints ? $loyaltyPoints->points_balance : 0 }} / {{ $nextTier->required_points }} نقطة
                            ({{ $nextTier->required_points - ($loyaltyPoints ? $loyaltyPoints->points_balance : 0) }} نقطة متبقية)
                        </p>
                    </div>
                @else
                    <p class="text-gray-600">أنت في أعلى مستوى!</p>
                @endif
                
                <a href="#tier-benefits" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                    عرض مزايا المستويات
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500 to-pink-500 p-6 text-white">
                <h2 class="text-xl font-bold">الاستبدالات الأخيرة</h2>
            </div>
            <div class="p-6">
                @if($redemptions->isEmpty())
                    <p class="text-gray-600">لا توجد استبدالات حتى الآن</p>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach($redemptions as $redemption)
                            <li class="py-3">
                                <div class="flex justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $redemption->reward->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $redemption->created_at->format('Y-m-d') }}</p>
                                    </div>
                                    <p class="text-sm font-medium text-indigo-600">{{ $redemption->reward->points_required }} نقطة</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4">
                        <a href="{{ route('loyalty.redemptions') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                            عرض الكل
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800 p-4">آخر المعاملات</h2>
            </div>
            <div class="p-4">
                @if($transactions->isEmpty())
                    <p class="text-gray-600 p-4 text-center">لا توجد معاملات حتى الآن</p>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach($transactions as $transaction)
                            <li class="py-3">
                                <div class="flex justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $transaction->description }}</p>
                                        <p class="text-sm text-gray-500">{{ $transaction->created_at->format('Y-m-d h:i A') }}</p>
                                    </div>
                                    <p class="text-sm font-medium {{ $transaction->points > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->points > 0 ? '+' : '' }}{{ $transaction->points }} نقطة
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4 text-center">
                        <a href="{{ route('loyalty.transactions') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                            عرض جميع المعاملات
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800 p-4">المكافآت المتاحة</h2>
            </div>
            <div class="p-4">
                @if($rewards->isEmpty())
                    <p class="text-gray-600 p-4 text-center">لا توجد مكافآت متاحة حالياً</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($rewards->take(4) as $reward)
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                @if($reward->image)
                                    <img src="{{ asset('storage/' . $reward->image) }}" alt="{{ $reward->name }}" class="w-full h-32 object-cover">
                                @else
                                    <div class="w-full h-32 bg-gray-200 flex items-center justify-center">
                                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m-8-4l8 4m8 0l-8 4m8-4v10a1 1 0 01-1 1h-14a1 1 0 01-1-1v-10"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div class="p-3">
                                    <h3 class="text-sm font-medium text-gray-900">{{ $reward->name }}</h3>
                                    <p class="text-sm text-indigo-600 font-bold mt-1">{{ $reward->points_required }} نقطة</p>
                                    <div class="mt-2">
                                        <a href="{{ route('loyalty.rewards.show', $reward) }}" class="text-xs text-indigo-600 hover:text-indigo-800">
                                            عرض التفاصيل
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ route('loyalty.rewards') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                            عرض جميع المكافآت
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="tier-benefits" class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 p-4">مستويات العضوية والمزايا</h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المستوى</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النقاط المطلوبة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">معدل كسب النقاط</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المزايا</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($tiers as $tier)
                            <tr class="{{ ($currentTier && $currentTier->id == $tier->id) ? 'bg-indigo-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($currentTier && $currentTier->id == $tier->id)
                                            <span class="mr-2 flex-shrink-0 h-5 w-5 rounded-full bg-indigo-500"></span>
                                        @endif
                                        <div class="text-sm font-medium {{ ($currentTier && $currentTier->id == $tier->id) ? 'text-indigo-700' : 'text-gray-900' }}">
                                            {{ $tier->name }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ number_format($tier->required_points) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $tier->earning_rate }}×</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $tier->benefits }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 p-4">كيفية كسب النقاط</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="mx-auto h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <h3 class="mt-3 text-lg font-medium text-gray-900">المشتريات</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        اكسب نقاط على كل عملية شراء تقوم بها. كلما زاد إنفاقك، زادت النقاط التي تكسبها.
                    </p>
                </div>
                
                <div class="text-center">
                    <div class="mx-auto h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="mt-3 text-lg font-medium text-gray-900">التقييمات</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        اكسب نقاط إضافية عند كتابة تقييمات للمنتجات التي اشتريتها.
                    </p>
                </div>
                
                <div class="text-center">
                    <div class="mx-auto h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a4 4 0 00-4-4H5.45a4 4 0 00-3.91 3.26L1 11l10.44 5.22"></path>
                        </svg>
                    </div>
                    <h3 class="mt-3 text-lg font-medium text-gray-900">الإحالات</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        قم بدعوة أصدقائك للتسجيل واكسب نقاط عندما يقومون بإجراء أول عملية شراء.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 