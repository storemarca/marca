@extends('layouts.admin')

@section('title', 'إضافة عميل جديد')
@section('header', 'إضافة عميل جديد')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.customers.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-300">
            <i class="fas fa-arrow-right ml-1"></i> العودة للقائمة
        </a>
    </div>
    
    <form action="{{ route('admin.customers.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- بيانات العميل -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-4 bg-gray-50 border-b">
                        <h3 class="font-medium text-gray-900">بيانات العميل</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">الاسم الكامل <span class="text-red-600">*</span></label>
                                <input type="text" name="name" id="name" required value="{{ old('name') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني <span class="text-red-600">*</span></label>
                                <input type="email" name="email" id="email" required value="{{ old('email') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">رقم الهاتف <span class="text-red-600">*</span></label>
                                <input type="text" name="phone" id="phone" required value="{{ old('phone') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="flex items-center mt-6">
                                <input type="checkbox" name="is_active" id="is_active" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 ml-2">
                                <label for="is_active" class="text-sm text-gray-700">حساب نشط</label>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                            <textarea name="notes" id="notes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- عنوان العميل (اختياري) -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-4 bg-gray-50 border-b">
                        <h3 class="font-medium text-gray-900">العنوان (اختياري)</h3>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">العنوان</label>
                            <input type="text" name="address" id="address" value="{{ old('address') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">المدينة</label>
                            <input type="text" name="city" id="city" value="{{ old('city') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="state" class="block text-sm font-medium text-gray-700 mb-1">المنطقة/المحافظة</label>
                            <input type="text" name="state" id="state" value="{{ old('state') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('state')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">الرمز البريدي</label>
                            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('postal_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="country_id" class="block text-sm font-medium text-gray-700 mb-1">البلد</label>
                            <select name="country_id" id="country_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">اختر البلد</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" name="is_default_address" id="is_default_address" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 ml-2">
                            <label for="is_default_address" class="text-sm text-gray-700">تعيين كعنوان افتراضي</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-300">
                <i class="fas fa-save ml-1"></i> حفظ العميل
            </button>
            <a href="{{ route('admin.customers.index') }}" class="mr-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-300">
                إلغاء
            </a>
        </div>
    </form>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush 