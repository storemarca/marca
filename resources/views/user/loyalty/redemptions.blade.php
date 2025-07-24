@extends('layouts.user')

@section('title', 'سجل استبدال المكافآت')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">سجل استبدال المكافآت</h1>
            <p class="text-gray-600 mt-2">تتبع جميع عمليات استبدال النقاط بالمكافآت</p>
        </div>
        <div>
            <a href="{{ route('loyalty.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                العودة للوحة الولاء
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

    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="border-b border-gray-200 p-4">
            <h2 class="text-lg font-semibold text-gray-800">تصفية النتائج</h2>
        </div>
        <div class="p-4">
            <form action="{{ route('loyalty.redemptions') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                    <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">جميع الحالات</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                    </select>
                </div>
                
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">الترتيب حسب</label>
                    <select id="sort" name="sort" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="created_at" {{ (request('sort') === 'created_at' || !request('sort')) ? 'selected' : '' }}>التاريخ (الأحدث أولاً)</option>
                        <option value="created_at_asc" {{ request('sort') === 'created_at_asc' ? 'selected' : '' }}>التاريخ (الأقدم أولاً)</option>
                        <option value="points" {{ request('sort') === 'points' ? 'selected' : '' }}>النقاط (تنازلي)</option>
                        <option value="points_asc" {{ request('sort') === 'points_asc' ? 'selected' : '' }}>النقاط (تصاعدي)</option>
                    </select>
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
            <h2 class="text-lg font-semibold text-gray-800 p-4">سجل الاستبدالات</h2>
        </div>
        
        @if($redemptions->isEmpty())
            <div class="p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">لا توجد استبدالات</h3>
                <p class="mt-1 text-gray-500">لم تقم باستبدال أي مكافآت بعد.</p>
                <div class="mt-6">
                    <a href="{{ route('loyalty.rewards') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        استعرض المكافآت المتاحة
                    </a>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المكافأة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النقاط</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">كود الاستبدال</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($redemptions as $redemption)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @if($redemption->reward->image)
                                            <img src="{{ asset('storage/' . $redemption->reward->image) }}" alt="{{ $redemption->reward->name }}" class="h-10 w-10 object-cover rounded-md">
                                        @else
                                            <div class="h-10 w-10 bg-gray-200 rounded-md flex items-center justify-center">
                                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m-8-4l8 4m8 0l-8 4m8-4v10a1 1 0 01-1 1h-14a1 1 0 01-1-1v-10"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="mr-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $redemption->reward->name }}</div>
                                            <div class="text-sm text-gray-500">
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
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $redemption->created_at->format('Y-m-d') }}</div>
                                    <div class="text-sm text-gray-500">{{ $redemption->created_at->format('h:i A') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $redemption->points_spent }} نقطة</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
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
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($redemption->code)
                                        <div class="text-sm font-mono bg-gray-100 p-1 rounded">{{ $redemption->code }}</div>
                                    @else
                                        <div class="text-sm text-gray-500">-</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-left">
                                    <a href="{{ route('loyalty.redemptions.show', $redemption) }}" class="text-indigo-600 hover:text-indigo-900 ml-4">عرض</a>
                                    
                                    @if($redemption->status === 'pending')
                                        <form action="{{ route('loyalty.redemptions.cancel', $redemption) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('هل أنت متأكد من رغبتك في إلغاء هذا الاستبدال؟ سيتم إعادة النقاط إلى رصيدك.')">إلغاء</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $redemptions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection 