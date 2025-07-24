@extends('layouts.user')

@section('title', 'سجل معاملات النقاط')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">سجل معاملات النقاط</h1>
            <p class="text-gray-600 mt-2">تتبع جميع عمليات كسب واستبدال النقاط</p>
        </div>
        <div>
            <a href="{{ route('loyalty.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                العودة للوحة الولاء
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="border-b border-gray-200 p-4">
            <h2 class="text-lg font-semibold text-gray-800">تصفية النتائج</h2>
        </div>
        <div class="p-4">
            <form action="{{ route('loyalty.transactions') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">نوع المعاملة</label>
                    <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">جميع المعاملات</option>
                        <option value="earn" {{ request('type') === 'earn' ? 'selected' : '' }}>كسب نقاط</option>
                        <option value="redeem" {{ request('type') === 'redeem' ? 'selected' : '' }}>استبدال نقاط</option>
                        <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>تعديل إداري</option>
                        <option value="expiry" {{ request('type') === 'expiry' ? 'selected' : '' }}>انتهاء صلاحية</option>
                    </select>
                </div>
                
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">من تاريخ</label>
                    <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">إلى تاريخ</label>
                    <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                        تصفية
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 p-4">سجل المعاملات</h2>
        </div>
        
        @if($transactions->isEmpty())
            <div class="p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">لا توجد معاملات</h3>
                <p class="mt-1 text-gray-500">لم يتم العثور على معاملات تطابق معايير البحث.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الوصف</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النقاط</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الرصيد</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transactions as $transaction)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $transaction->created_at->format('Y-m-d') }}</div>
                                    <div class="text-sm text-gray-500">{{ $transaction->created_at->format('h:i A') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $transaction->description }}</div>
                                    @if($transaction->reference_type && $transaction->reference_id)
                                        <div class="text-sm text-gray-500">
                                            @switch($transaction->reference_type)
                                                @case('App\Models\Order')
                                                    الطلب رقم #{{ $transaction->reference_id }}
                                                    @break
                                                @case('App\Models\ProductReview')
                                                    تقييم منتج
                                                    @break
                                                @case('App\Models\RewardRedemption')
                                                    استبدال مكافأة
                                                    @break
                                                @default
                                                    {{ class_basename($transaction->reference_type) }} #{{ $transaction->reference_id }}
                                            @endswitch
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($transaction->type)
                                        @case('earn')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                كسب
                                            </span>
                                            @break
                                        @case('redeem')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                استبدال
                                            </span>
                                            @break
                                        @case('adjustment')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                تعديل
                                            </span>
                                            @break
                                        @case('expiry')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                انتهاء صلاحية
                                            </span>
                                            @break
                                        @default
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ $transaction->type }}
                                            </span>
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm {{ $transaction->points > 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                                        {{ $transaction->points > 0 ? '+' : '' }}{{ $transaction->points }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $transaction->balance_after }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection 