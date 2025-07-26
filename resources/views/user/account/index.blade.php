@extends('layouts.user')

@section('title', 'حسابي')

@section('page-header', 'حسابي')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Sidebar -->
            <div class="md:w-1/4">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="mr-4">
                                <h2 class="font-semibold">{{ auth()->user()->name }}</h2>
                                <p class="text-gray-600 text-sm">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-0">
                        <nav>
                            <a href="{{ route('user.account.index') }}" class="block py-3 px-4 border-r-4 border-blue-600 bg-blue-50 text-blue-600 font-medium">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                    لوحة التحكم
                                </div>
                            </a>
                            <a href="{{ route('user.orders.index') }}" class="block py-3 px-4 border-r-4 border-transparent hover:bg-gray-50 hover:text-blue-600 hover:border-blue-600">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                    طلباتي
                                </div>
                            </a>
                            <a href="{{ route('user.account.addresses') }}" class="block py-3 px-4 border-r-4 border-transparent hover:bg-gray-50 hover:text-blue-600 hover:border-blue-600">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    عناويني
                                </div>
                            </a>
                            <a href="{{ route('user.account.edit') }}" class="block py-3 px-4 border-r-4 border-transparent hover:bg-gray-50 hover:text-blue-600 hover:border-blue-600">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    إعدادات الحساب
                                </div>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-right block py-3 px-4 border-r-4 border-transparent hover:bg-gray-50 hover:text-red-600 hover:border-red-600">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        تسجيل الخروج
                                    </div>
                                </button>
                            </form>
                        </nav>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="md:w-3/4">
                <!-- Account Overview -->
                <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold">لوحة التحكم</h2>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-blue-50 rounded-lg p-6">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                        </svg>
                                    </div>
                                    <div class="mr-4">
                                        <h3 class="font-semibold text-lg">طلباتي</h3>
                                        <p class="text-gray-600">{{ $ordersCount ?? 0 }} طلب</p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('user.orders.index') }}" class="text-blue-600 hover:underline">عرض جميع الطلبات</a>
                                </div>
                            </div>
                            
                            <div class="bg-green-50 rounded-lg p-6">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="mr-4">
                                        <h3 class="font-semibold text-lg">عناويني</h3>
                                        <p class="text-gray-600">{{ $addressesCount }} عنوان</p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('user.account.addresses') }}" class="text-green-600 hover:underline">إدارة العناوين</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Orders -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold">آخر الطلبات</h2>
                    </div>
                    
                    <div class="p-6">
                        @if($recentOrders->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="py-2 px-4 text-right">رقم الطلب</th>
                                            <th class="py-2 px-4 text-right">التاريخ</th>
                                            <th class="py-2 px-4 text-right">الحالة</th>
                                            <th class="py-2 px-4 text-right">المجموع</th>
                                            <th class="py-2 px-4 text-right">الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($recentOrders as $order)
                                            <tr>
                                                <td class="py-3 px-4">{{ $order->order_number }}</td>
                                                <td class="py-3 px-4">{{ $order->created_at->format('d/m/Y') }}</td>
                                                <td class="py-3 px-4">
                                                    <span class="px-2 py-1 text-xs rounded-full {{ $order->status_color }}">
                                                        {{ $order->status_text }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4">{{ number_format($order->total_amount, 2) }} {{ $order->currency_symbol }}</td>
                                                <td class="py-3 px-4">
                                                    <a href="{{ route('user.orders.show', $order->id) }}" class="text-blue-600 hover:underline">عرض</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-4 text-center">
                                <a href="{{ route('user.orders.index') }}" class="text-blue-600 hover:underline">عرض جميع الطلبات</a>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">لا توجد طلبات بعد</h3>
                                <p class="text-gray-500 mb-6">لم تقم بإنشاء أي طلبات حتى الآن</p>
                                <a href="{{ route('user.products.index') }}" class="bg-blue-600 text-white py-2 px-6 rounded-lg hover:bg-blue-700 transition duration-200">
                                    تصفح المنتجات
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 