@extends('layouts.user')

@section('title', 'إنشاء طلب إرجاع')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">إنشاء طلب إرجاع</h1>
        <p class="text-gray-600 mt-2">طلب إرجاع للطلب رقم #{{ $order->id }}</p>
    </div>

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">تفاصيل الطلب</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-gray-600">رقم الطلب: <span class="font-semibold text-gray-800">#{{ $order->id }}</span></p>
                    <p class="text-gray-600">تاريخ الطلب: <span class="font-semibold text-gray-800">{{ $order->created_at->format('Y-m-d') }}</span></p>
                    <p class="text-gray-600">حالة الطلب: <span class="font-semibold text-gray-800">{{ $order->status }}</span></p>
                </div>
                <div>
                    <p class="text-gray-600">إجمالي الطلب: <span class="font-semibold text-gray-800">{{ $order->total }} {{ $order->currency }}</span></p>
                    <p class="text-gray-600">طريقة الدفع: <span class="font-semibold text-gray-800">{{ $order->payment_method }}</span></p>
                    <p class="text-gray-600">حالة الدفع: <span class="font-semibold text-gray-800">{{ $order->payment_status }}</span></p>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('user.returns.store') }}" method="POST" class="bg-white rounded-lg shadow-md overflow-hidden">
        @csrf
        <input type="hidden" name="order_id" value="{{ $order->id }}">
        
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">المنتجات المؤهلة للإرجاع</h2>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">اختر المنتجات التي ترغب في إرجاعها:</label>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">اختيار</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المنتج</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">السعر</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الكمية المطلوبة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الكمية المتاحة للإرجاع</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">كمية الإرجاع</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">حالة المنتج</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">سبب الإرجاع</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($order->items as $item)
                                @php
                                    $returnedQuantity = $item->returnItems->sum('quantity');
                                    $availableQuantity = $item->quantity - $returnedQuantity;
                                @endphp
                                
                                @if($availableQuantity > 0)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" name="items[{{ $loop->index }}][selected]" id="item-{{ $item->id }}" class="return-item-checkbox" data-item-id="{{ $loop->index }}">
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                @if($item->product->main_image)
                                                    <img src="{{ asset('storage/' . $item->product->main_image) }}" alt="{{ $item->product->name }}" class="h-10 w-10 object-cover rounded-md">
                                                @else
                                                    <div class="h-10 w-10 bg-gray-200 rounded-md flex items-center justify-center">
                                                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div class="mr-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                                    @if($item->options)
                                                        <div class="text-sm text-gray-500">
                                                            @foreach($item->options as $key => $value)
                                                                {{ $key }}: {{ $value }}@if(!$loop->last), @endif
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <input type="hidden" name="items[{{ $loop->index }}][order_item_id]" value="{{ $item->id }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ __('price') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $item->unit_price }} {{ $order->currency }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $item->quantity }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $availableQuantity }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" name="items[{{ $loop->index }}][quantity]" min="1" max="{{ $availableQuantity }}" value="1" class="mt-1 block w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 return-item-quantity" disabled data-item-id="{{ $loop->index }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <select name="items[{{ $loop->index }}][condition]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 return-item-condition" disabled data-item-id="{{ $loop->index }}">
                                                <option value="new">جديد (غير مستخدم)</option>
                                                <option value="like_new">شبه جديد (تم فتحه فقط)</option>
                                                <option value="used">مستعمل</option>
                                                <option value="damaged">تالف</option>
                                            </select>
                                        </td>
                                        <td class="px-6 py-4">
                                            <input type="text" name="items[{{ $loop->index }}][reason]" placeholder="سبب الإرجاع" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 return-item-reason" disabled data-item-id="{{ $loop->index }}">
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mb-6">
                <label for="return_method" class="block text-gray-700 text-sm font-bold mb-2">طريقة الإرجاع:</label>
                <select name="return_method" id="return_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    <option value="refund">استرداد المبلغ</option>
                    <option value="exchange">استبدال المنتج</option>
                    <option value="store_credit">رصيد في المتجر</option>
                </select>
            </div>
            
            <div class="mb-6">
                <label for="reason" class="block text-gray-700 text-sm font-bold mb-2">سبب الإرجاع العام:</label>
                <textarea name="reason" id="reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required></textarea>
            </div>
            
            <div class="mb-6">
                <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">ملاحظات إضافية (اختياري):</label>
                <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
            </div>
        </div>
        
        <div class="px-6 py-4 bg-gray-50 text-left">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" id="submit-button" disabled>
                إرسال طلب الإرجاع
            </button>
            <a href="{{ route('user.orders.show', $order) }}" class="text-gray-600 hover:text-gray-800 mr-4">إلغاء</a>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.return-item-checkbox');
        const submitButton = document.getElementById('submit-button');
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const itemId = this.getAttribute('data-item-id');
                const quantityInput = document.querySelector(`.return-item-quantity[data-item-id="${itemId}"]`);
                const conditionSelect = document.querySelector(`.return-item-condition[data-item-id="${itemId}"]`);
                const reasonInput = document.querySelector(`.return-item-reason[data-item-id="${itemId}"]`);
                
                if (this.checked) {
                    quantityInput.disabled = false;
                    conditionSelect.disabled = false;
                    reasonInput.disabled = false;
                    reasonInput.required = true;
                } else {
                    quantityInput.disabled = true;
                    conditionSelect.disabled = true;
                    reasonInput.disabled = true;
                    reasonInput.required = false;
                }
                
                // Check if at least one item is selected
                const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
                submitButton.disabled = !anyChecked;
            });
        });
    });
</script>
@endsection 