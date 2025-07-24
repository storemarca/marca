@extends('layouts.admin')

@section('title', 'تعديل الشحنة')
@section('header', 'تعديل الشحنة: ' . $shipment->tracking_number)

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.shipments.show', $shipment->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="fas fa-arrow-right ml-2"></i> العودة لتفاصيل الشحنة
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('admin.shipments.update', $shipment->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- معلومات الطلب (للعرض فقط) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الطلب</label>
                    <div class="mt-1 p-3 bg-gray-100 rounded-md">
                        <p class="text-sm text-gray-900">رقم الطلب: {{ $shipment->order->order_number ?? 'غير متوفر' }}</p>
                        <p class="text-sm text-gray-900 mt-1">العميل: {{ $shipment->order->shipping_name ?? 'غير متوفر' }}</p>
                        <p class="text-sm text-gray-900 mt-1">المبلغ: {{ number_format($shipment->order->grand_total ?? 0, 2) }}</p>
                    </div>
                </div>
                
                <!-- شركة الشحن -->
                <div>
                    <label for="shipping_company_id" class="block text-sm font-medium text-gray-700 mb-1">شركة الشحن <span class="text-red-600">*</span></label>
                    <select name="shipping_company_id" id="shipping_company_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">اختر شركة الشحن</option>
                        @foreach($shippingCompanies as $company)
                            <option value="{{ $company->id }}" {{ old('shipping_company_id', $shipment->shipping_company_id) == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('shipping_company_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- رقم التتبع -->
                <div>
                    <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-1">رقم التتبع <span class="text-red-600">*</span></label>
                    <input type="text" name="tracking_number" id="tracking_number" value="{{ old('tracking_number', $shipment->tracking_number) }}" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('tracking_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- تاريخ التسليم المتوقع -->
                <div>
                    <label for="expected_delivery_date" class="block text-sm font-medium text-gray-700 mb-1">تاريخ التسليم المتوقع <span class="text-red-600">*</span></label>
                    <input type="date" name="expected_delivery_date" id="expected_delivery_date" 
                        value="{{ old('expected_delivery_date', $shipment->expected_delivery_date ? \Carbon\Carbon::parse($shipment->expected_delivery_date)->format('Y-m-d') : '') }}" 
                        required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('expected_delivery_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- ملاحظات -->
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                <textarea name="notes" id="notes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('notes', $shipment->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex items-center justify-end">
                <a href="{{ route('admin.shipments.show', $shipment->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 ml-2">
                    إلغاء
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    تحديث الشحنة
                </button>
            </div>
        </form>
    </div>
    
    <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="mr-3">
                <p class="text-sm text-yellow-700">
                    <strong>ملاحظة:</strong> لتحديث حالة الشحنة، يرجى استخدام خيار "تحديث حالة الشحنة" في صفحة تفاصيل الشحنة.
                </p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush 