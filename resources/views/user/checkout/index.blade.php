@extends('layouts.user')

@section('title', 'إتمام الطلب')

@section('content')
<div class="container mx-auto px-4 py-5">
    <div class="mb-6">
        <h1 class="text-2xl font-bold mb-2">إتمام الطلب</h1>
        <div class="flex items-center text-sm">
            <div class="flex items-center text-blue-600">
                <span class="w-6 h-6 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">1</span>
                <span class="mr-2">عنوان الشحن</span>
            </div>
            <div class="w-12 h-1 mx-2 bg-gray-300"></div>
            <div class="flex items-center text-gray-500">
                <span class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-bold">2</span>
                <span class="mr-2">طريقة الدفع</span>
            </div>
            <div class="w-12 h-1 mx-2 bg-gray-300"></div>
            <div class="flex items-center text-gray-500">
                <span class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-bold">3</span>
                <span class="mr-2">مراجعة الطلب</span>
            </div>
        </div>
    </div>

    @if(!session()->has('cart') || count(session()->get('cart')) === 0)
        <div class="bg-white rounded shadow p-8 text-center">
            <h2 class="text-xl font-bold text-gray-700 mb-4">سلة التسوق فارغة</h2>
            <p class="text-gray-500 mb-6">لا يمكن متابعة الدفع بدون إضافة منتجات إلى سلة التسوق.</p>
            <a href="{{ route('user.products.index') }}" class="bg-yellow-400 hover:bg-yellow-500 text-center py-2 px-6 rounded-md font-medium transition-colors duration-200">
                تصفح المنتجات
            </a>
        </div>
    @else
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Checkout Form -->
            <div class="lg:w-3/4">
                <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
                    @csrf
                    
                    <!-- Shipping Information -->
                    <div class="bg-white rounded shadow mb-6">
                        <div class="p-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                            <h2 class="text-xl font-semibold">معلومات الشحن</h2>
                            @if(auth()->check())
                                <a href="{{ route('user.account.addresses') }}" class="text-sm text-blue-600 hover:text-orange-500 hover:underline">
                                    إدارة العناوين
                                </a>
                            @endif
                        </div>
                        
                        <div class="p-6">
                            @if(auth()->check() && auth()->user()->addresses->count() > 0)
                                <div class="mb-6">
                                    <h3 class="font-semibold mb-3">اختر عنوان شحن</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach(auth()->user()->addresses as $address)
                                            <div class="border rounded p-4 relative hover:border-yellow-500 transition-colors duration-200">
                                                <input type="radio" name="address_id" id="address-{{ $address->id }}" value="{{ $address->id }}" 
                                                    class="absolute top-4 left-4" @if($loop->first) checked @endif>
                                                <label for="address-{{ $address->id }}" class="block cursor-pointer">
                                                    <div class="font-semibold">{{ $address->name }}</div>
                                                    <div class="text-gray-600 text-sm mt-1">
                                                        {{ $address->address_line1 }}<br>
                                                        @if($address->address_line2)
                                                            {{ $address->address_line2 }}<br>
                                                        @endif
                                                        {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}<br>
                                                        {{ $address->country->name }}<br>
                                                        {{ $address->phone }}
                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="mt-4 flex items-center">
                                        <input type="checkbox" name="use_new_address" id="use_new_address" value="1" 
                                            class="rounded border-gray-300 text-yellow-500 focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                        <label for="use_new_address" class="mr-2 text-sm">استخدام عنوان جديد</label>
                                    </div>
                                </div>
                                
                                <div id="new-address-form" class="hidden">
                            @endif
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-2">
                                    <h3 class="font-semibold mb-3 flex items-center">
                                        <svg class="w-5 h-5 text-yellow-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                        </svg>
                                        أدخل عنوان الشحن
                                    </h3>
                                </div>
                                
                                <div>
                                    <label for="name" class="block mb-1 text-sm font-medium">الاسم الكامل <span class="text-red-600">*</span></label>
                                    <input type="text" name="name" id="name" value="{{ auth()->check() ? auth()->user()->name : old('name') }}" required 
                                        class="w-full rounded border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                </div>
                                
                                <div>
                                    <label for="phone" class="block mb-1 text-sm font-medium">رقم الهاتف <span class="text-red-600">*</span></label>
                                    <input type="tel" name="phone" id="phone" value="{{ auth()->check() ? auth()->user()->phone : old('phone') }}" required 
                                        class="w-full rounded border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                </div>
                                
                                <div class="col-span-2">
                                    <label for="email" class="block mb-1 text-sm font-medium">البريد الإلكتروني <span class="text-red-600">*</span></label>
                                    <input type="email" name="email" id="email" value="{{ auth()->check() ? auth()->user()->email : old('email') }}" required 
                                        class="w-full rounded border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                </div>
                                
                                <div class="col-span-2">
                                    <label for="address_line1" class="block mb-1 text-sm font-medium">العنوان <span class="text-red-600">*</span></label>
                                    <input type="text" name="address_line1" id="address_line1" value="{{ old('address_line1') }}" required 
                                        class="w-full rounded border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                </div>
                                
                                <div class="col-span-2">
                                    <label for="address_line2" class="block mb-1 text-sm font-medium">العنوان (السطر الثاني)</label>
                                    <input type="text" name="address_line2" id="address_line2" value="{{ old('address_line2') }}" 
                                        class="w-full rounded border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                </div>
                                
                                <div>
                                    <label for="city" class="block mb-1 text-sm font-medium">المدينة <span class="text-red-600">*</span></label>
                                    <input type="text" name="city" id="city" value="{{ old('city') }}" required 
                                        class="w-full rounded border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                </div>
                                
                                <div>
                                    <label for="state" class="block mb-1 text-sm font-medium">المنطقة/المحافظة <span class="text-red-600">*</span></label>
                                    <input type="text" name="state" id="state" value="{{ old('state') }}" required 
                                        class="w-full rounded border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                </div>
                                
                                <div>
                                    <label for="postal_code" class="block mb-1 text-sm font-medium">الرمز البريدي <span class="text-red-600">*</span></label>
                                    <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}" required 
                                        class="w-full rounded border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                </div>
                                
                                <div class="mb-4">
                                    <label for="country_id" class="block text-sm font-medium text-gray-700">{{ __('general.country') }}</label>
                                    <select id="country_id" name="country_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                        <option value="">{{ __('general.select_country') }}</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="governorate_id" class="block text-sm font-medium text-gray-700">{{ __('general.governorate') }}</label>
                                    <select id="governorate_id" name="governorate_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                        <option value="">{{ __('general.select_governorate') }}</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="district_id" class="block text-sm font-medium text-gray-700">{{ __('general.district') }}</label>
                                    <select id="district_id" name="district_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                        <option value="">{{ __('general.select_district') }}</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="area_id" class="block text-sm font-medium text-gray-700">{{ __('general.area') }}</label>
                                    <select id="area_id" name="area_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">{{ __('general.select_area') }}</option>
                                    </select>
                                </div>
                                
                                @if(auth()->check())
                                    <div class="col-span-2">
                                        <div class="flex items-center">
                                            <input type="checkbox" name="save_address" id="save_address" value="1" 
                                                class="rounded border-gray-300 text-yellow-500 focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                            <label for="save_address" class="mr-2 text-sm">حفظ هذا العنوان لاستخدامه لاحقاً</label>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-span-2">
                                        <div class="flex items-center">
                                            <input type="checkbox" name="create_account" id="create_account" value="1" 
                                                class="rounded border-gray-300 text-yellow-500 focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                            <label for="create_account" class="mr-2 text-sm">إنشاء حساب لتتبع طلبك وحفظ بياناتك</label>
                                        </div>
                                    </div>
                                    
                                    <div id="create-account-fields" class="col-span-2 hidden">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                            <div>
                                                <label for="password" class="block mb-1 text-sm font-medium">كلمة المرور <span class="text-red-600">*</span></label>
                                                <input type="password" name="password" id="password" 
                                                    class="w-full rounded border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                            </div>
                                            <div>
                                                <label for="password_confirmation" class="block mb-1 text-sm font-medium">تأكيد كلمة المرور <span class="text-red-600">*</span></label>
                                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                                    class="w-full rounded border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            @if(auth()->check() && auth()->user()->addresses->count() > 0)
                                </div>
                            @endif
                            
                            <div class="mt-6">
                                <h3 class="font-semibold mb-3 text-sm">ملاحظات الطلب (اختياري)</h3>
                                <textarea name="notes" rows="3" 
                                    class="w-full rounded border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50"
                                    placeholder="أضف أي ملاحظات خاصة بالطلب هنا">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Shipping Method -->
                    <div class="bg-white rounded shadow mb-6">
                        <div class="p-4 border-b border-gray-200 bg-gray-50">
                            <h2 class="text-xl font-semibold">طريقة الشحن</h2>
                        </div>
                        
                        <div class="p-6">
                            <div class="space-y-3">
                                @foreach($shippingMethods as $method)
                                    <div class="border rounded p-3 flex items-center hover:border-yellow-500 transition-colors duration-200">
                                        <input type="radio" name="shipping_method_id" id="shipping-{{ $method->id }}" value="{{ $method->id }}" 
                                            class="ml-4 text-yellow-500 focus:ring-yellow-200" @if($loop->first) checked @endif>
                                        <label for="shipping-{{ $method->id }}" class="flex-1 cursor-pointer">
                                            <div class="font-semibold">{{ $method->name }}</div>
                                            <div class="text-gray-600 text-sm">{{ $method->description }}</div>
                                        </label>
                                        <div class="text-left">
                                            @if($method->price > 0)
                                                <span class="font-semibold">{{ number_format($method->price, 2) }} {{ session('cart_totals.currency_symbol') ?? 'ر.س' }}</span>
                                            @else
                                                <span class="text-green-600 font-semibold">مجاني</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="bg-white rounded shadow mb-6">
                        <div class="p-4 border-b border-gray-200 bg-gray-50">
                            <h2 class="text-xl font-semibold">طريقة الدفع</h2>
                        </div>
                        
                        <div class="p-6">
                            <div class="space-y-3">
                                <div class="border rounded p-3 hover:border-yellow-500 transition-colors duration-200">
                                    <div class="flex items-center">
                                        <input type="radio" name="payment_method" id="payment-card" value="card" 
                                            class="ml-4 text-yellow-500 focus:ring-yellow-200" checked>
                                        <label for="payment-card" class="flex-1 cursor-pointer">
                                            <div class="font-semibold">بطاقة ائتمان/مدى</div>
                                        </label>
                                        <div class="flex space-x-2 space-x-reverse">
                                            <img src="{{ asset('images/payment/visa.svg') }}" alt="visa" class="h-6">
                                            <img src="{{ asset('images/payment/inpay.svg') }}" alt="instapay" class="h-6">
                                            <img src="{{ asset('images/payment/vcash.svg') }}" alt="vcash" class="h-6">
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 payment-details" id="card-details">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="col-span-2">
                                                <label for="card_number" class="block mb-1 text-sm font-medium">رقم البطاقة <span class="text-red-600">*</span></label>
                                                <input type="text" name="card_number" id="card_number" placeholder="0000 0000 0000 0000" 
                                                    class="w-full rounded border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                            </div>
                                            
                                            <div>
                                                <label for="card_expiry" class="block mb-1 text-sm font-medium">تاريخ الانتهاء <span class="text-red-600">*</span></label>
                                                <input type="text" name="card_expiry" id="card_expiry" placeholder="MM/YY" 
                                                    class="w-full rounded border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                            </div>
                                            
                                            <div>
                                                <label for="card_cvv" class="block mb-1 text-sm font-medium">رمز الأمان (CVV) <span class="text-red-600">*</span></label>
                                                <input type="text" name="card_cvv" id="card_cvv" placeholder="123" 
                                                    class="w-full rounded border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                            </div>
                                            
                                            <div class="col-span-2">
                                                <label for="card_name" class="block mb-1 text-sm font-medium">الاسم على البطاقة <span class="text-red-600">*</span></label>
                                                <input type="text" name="card_name" id="card_name" 
                                                    class="w-full rounded border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="border rounded p-3 hover:border-yellow-500 transition-colors duration-200">
                                    <div class="flex items-center">
                                        <input type="radio" name="payment_method" id="payment-cod" value="cod" 
                                            class="ml-4 text-yellow-500 focus:ring-yellow-200">
                                        <label for="payment-cod" class="cursor-pointer">
                                            <div class="font-semibold">الدفع عند الاستلام</div>
                                            <div class="text-gray-600 text-sm">ادفع نقداً عند استلام الطلب</div>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="border rounded p-3 hover:border-yellow-500 transition-colors duration-200">
                                    <div class="flex items-center">
                                        <input type="radio" name="payment_method" id="payment-wallet" value="wallet" 
                                            class="ml-4 text-yellow-500 focus:ring-yellow-200">
                                        <label for="payment-wallet" class="cursor-pointer">
                                            <div class="font-semibold">محفظة Apple Pay</div>
                                        </label>
                                        <div class="mr-auto">
                                            <img src="{{ asset('images/payment/apple-pay.svg') }}" alt="Apple Pay" class="h-6">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="border rounded p-3 hover:border-yellow-500 transition-colors duration-200">
                                    <div class="flex items-center">
                                        <input type="radio" name="payment_method" id="payment-bank-transfer" value="bank_transfer" 
                                            class="ml-4 text-yellow-500 focus:ring-yellow-200">
                                        <label for="payment-bank-transfer" class="cursor-pointer">
                                            <div class="font-semibold">التحويل البنكي</div>
                                            <div class="text-gray-600 text-sm">قم بالتحويل البنكي إلى أحد حساباتنا</div>
                                        </label>
                                    </div>
                                    
                                    <div class="mt-4 payment-details hidden" id="bank-transfer-details">
                                        <div class="bg-gray-50 p-4 rounded mb-4">
                                            <p class="text-sm text-gray-700 mb-3">{{ setting('bank_transfer_instructions', 'يرجى تحويل المبلغ إلى أحد الحسابات البنكية المذكورة أدناه. بعد إتمام التحويل، يرجى إرسال صورة من إيصال التحويل مع رقم الطلب.') }}</p>
                                        </div>
                                        
                                        @if(setting('bank_name_1') && setting('bank_account_number_1'))
                                            <div class="border rounded p-4 mb-3">
                                                <h4 class="font-semibold mb-2">{{ setting('bank_name_1') }}</h4>
                                                <div class="grid grid-cols-1 gap-2 text-sm">
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600">اسم صاحب الحساب:</span>
                                                        <span class="font-medium">{{ setting('bank_account_name_1') }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600">رقم الحساب:</span>
                                                        <span class="font-medium">{{ setting('bank_account_number_1') }}</span>
                                                    </div>
                                                    @if(setting('bank_iban_1'))
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-600">رقم الآيبان:</span>
                                                            <span class="font-medium">{{ setting('bank_iban_1') }}</span>
                                                        </div>
                                                    @endif
                                                    @if(setting('bank_swift_1'))
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-600">رمز السويفت:</span>
                                                            <span class="font-medium">{{ setting('bank_swift_1') }}</span>
                                                        </div>
                                                    @endif
                                                    @if(setting('bank_currency_1'))
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-600">عملة الحساب:</span>
                                                            <span class="font-medium">{{ setting('bank_currency_1') }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if(setting('bank_name_2') && setting('bank_account_number_2'))
                                            <div class="border rounded p-4">
                                                <h4 class="font-semibold mb-2">{{ setting('bank_name_2') }}</h4>
                                                <div class="grid grid-cols-1 gap-2 text-sm">
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600">اسم صاحب الحساب:</span>
                                                        <span class="font-medium">{{ setting('bank_account_name_2') }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600">رقم الحساب:</span>
                                                        <span class="font-medium">{{ setting('bank_account_number_2') }}</span>
                                                    </div>
                                                    @if(setting('bank_iban_2'))
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-600">رقم الآيبان:</span>
                                                            <span class="font-medium">{{ setting('bank_iban_2') }}</span>
                                                        </div>
                                                    @endif
                                                    @if(setting('bank_swift_2'))
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-600">رمز السويفت:</span>
                                                            <span class="font-medium">{{ setting('bank_swift_2') }}</span>
                                                        </div>
                                                    @endif
                                                    @if(setting('bank_currency_2'))
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-600">عملة الحساب:</span>
                                                            <span class="font-medium">{{ setting('bank_currency_2') }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <div class="mt-4">
                                            <label for="transaction_reference" class="block mb-1 text-sm font-medium">رقم مرجع التحويل (اختياري)</label>
                                            <input type="text" name="transaction_reference" id="transaction_reference" 
                                                class="w-full rounded border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50"
                                                placeholder="أدخل رقم مرجع التحويل إذا كان متاحًا">
                                            <div class="text-xs text-gray-500 mt-1">يمكنك إضافة رقم مرجع التحويل لاحقًا من صفحة تفاصيل الطلب</div>
                                        </div>
                                    </div>
                                </div>
                                
                                @if(
                                    (setting('vodafone_cash_enabled') && setting('vodafone_cash_number')) ||
                                    (setting('etisalat_cash_enabled') && setting('etisalat_cash_number')) ||
                                    (setting('orange_cash_enabled') && setting('orange_cash_number')) ||
                                    (setting('we_cash_enabled') && setting('we_cash_number')) ||
                                    (setting('instapay_enabled') && setting('instapay_number'))
                                )
                                <div class="border rounded p-3 hover:border-yellow-500 transition-colors duration-200">
                                    <div class="flex items-center">
                                        <input type="radio" name="payment_method" id="payment-mobile-wallet" value="mobile_wallet" 
                                            class="ml-4 text-yellow-500 focus:ring-yellow-200">
                                        <label for="payment-mobile-wallet" class="cursor-pointer">
                                            <div class="font-semibold">محافظ الدفع الإلكترونية</div>
                                            <div class="text-gray-600 text-sm">فودافون كاش، اتصالات كاش، أورانج كاش، وي كاش، انستا باي</div>
                                        </label>
                                        <div class="mr-auto flex space-x-2 space-x-reverse">
                                            @if(setting('vodafone_cash_enabled') && setting('vodafone_cash_number'))
                                                <img src="{{ asset('images/payment/vcash.svg') }}" alt="فودافون كاش" class="h-6">
                                            @endif
                                            @if(setting('etisalat_cash_enabled') && setting('etisalat_cash_number'))
                                                <img src="{{ asset('images/payment/visa.svg') }}" alt="اتصالات كاش" class="h-6">
                                            @endif
                                            @if(setting('orange_cash_enabled') && setting('orange_cash_number'))
                                                <img src="{{ asset('images/payment/inpay.svg') }}" alt="أورانج كاش" class="h-6">
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 payment-details hidden" id="mobile-wallet-details">
                                        <div class="bg-gray-50 p-4 rounded mb-4">
                                            <p class="text-sm text-gray-700 mb-3">{{ setting('mobile_wallet_instructions', 'يرجى تحويل المبلغ إلى أحد أرقام المحافظ الإلكترونية المذكورة أدناه. بعد إتمام التحويل، يرجى إرسال صورة من إيصال التحويل مع رقم الطلب.') }}</p>
                                        </div>
                                        
                                        <div class="space-y-3">
                                            @if(setting('vodafone_cash_enabled') && setting('vodafone_cash_number'))
                                                <div class="border rounded p-4 flex items-center">
                                                    <div class="w-12 h-12 flex-shrink-0">
                                                        <img src="{{ asset('images/payment/vcash.svg') }}" alt="فودافون كاش" class="w-full h-full object-contain">
                                                    </div>
                                                    <div class="mr-4">
                                                        <h4 class="font-semibold">فودافون كاش</h4>
                                                        <div class="text-sm">
                                                            <span class="text-gray-600">رقم الهاتف:</span>
                                                            <span class="font-medium">{{ setting('vodafone_cash_number') }}</span>
                                                        </div>
                                                        @if(setting('vodafone_cash_name'))
                                                            <div class="text-sm">
                                                                <span class="text-gray-600">اسم صاحب المحفظة:</span>
                                                                <span class="font-medium">{{ setting('vodafone_cash_name') }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if(setting('etisalat_cash_enabled') && setting('etisalat_cash_number'))
                                                <div class="border rounded p-4 flex items-center">
                                                    <div class="w-12 h-12 flex-shrink-0">
                                                        <img src="{{ asset('images/payment/visa.svg') }}" alt="اتصالات كاش" class="w-full h-full object-contain">
                                                    </div>
                                                    <div class="mr-4">
                                                        <h4 class="font-semibold">اتصالات كاش</h4>
                                                        <div class="text-sm">
                                                            <span class="text-gray-600">رقم الهاتف:</span>
                                                            <span class="font-medium">{{ setting('etisalat_cash_number') }}</span>
                                                        </div>
                                                        @if(setting('etisalat_cash_name'))
                                                            <div class="text-sm">
                                                                <span class="text-gray-600">اسم صاحب المحفظة:</span>
                                                                <span class="font-medium">{{ setting('etisalat_cash_name') }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if(setting('orange_cash_enabled') && setting('orange_cash_number'))
                                                <div class="border rounded p-4 flex items-center">
                                                    <div class="w-12 h-12 flex-shrink-0">
                                                        <img src="{{ asset('images/payment/inpay.svg') }}" alt="أورانج كاش" class="w-full h-full object-contain">
                                                    </div>
                                                    <div class="mr-4">
                                                        <h4 class="font-semibold">أورانج كاش</h4>
                                                        <div class="text-sm">
                                                            <span class="text-gray-600">رقم الهاتف:</span>
                                                            <span class="font-medium">{{ setting('orange_cash_number') }}</span>
                                                        </div>
                                                        @if(setting('orange_cash_name'))
                                                            <div class="text-sm">
                                                                <span class="text-gray-600">اسم صاحب المحفظة:</span>
                                                                <span class="font-medium">{{ setting('orange_cash_name') }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if(setting('we_cash_enabled') && setting('we_cash_number'))
                                                <div class="border rounded p-4 flex items-center">
                                                    <div class="w-12 h-12 flex-shrink-0">
                                                        <img src="{{ asset('images/payment/visa.svg') }}" alt="وي كاش" class="w-full h-full object-contain">
                                                    </div>
                                                    <div class="mr-4">
                                                        <h4 class="font-semibold">وي كاش</h4>
                                                        <div class="text-sm">
                                                            <span class="text-gray-600">رقم الهاتف:</span>
                                                            <span class="font-medium">{{ setting('we_cash_number') }}</span>
                                                        </div>
                                                        @if(setting('we_cash_name'))
                                                            <div class="text-sm">
                                                                <span class="text-gray-600">اسم صاحب المحفظة:</span>
                                                                <span class="font-medium">{{ setting('we_cash_name') }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if(setting('instapay_enabled') && setting('instapay_number'))
                                                <div class="border rounded p-4 flex items-center">
                                                    <div class="w-12 h-12 flex-shrink-0">
                                                        <img src="{{ asset('images/payment/inpay.svg') }}" alt="انستا باي" class="w-full h-full object-contain">
                                                    </div>
                                                    <div class="mr-4">
                                                        <h4 class="font-semibold">انستا باي</h4>
                                                        <div class="text-sm">
                                                            <span class="text-gray-600">رقم الهاتف أو المعرف:</span>
                                                            <span class="font-medium">{{ setting('instapay_number') }}</span>
                                                        </div>
                                                        @if(setting('instapay_name'))
                                                            <div class="text-sm">
                                                                <span class="text-gray-600">اسم صاحب الحساب:</span>
                                                                <span class="font-medium">{{ setting('instapay_name') }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="mt-4">
                                            <label for="wallet_transaction_reference" class="block mb-1 text-sm font-medium">رقم مرجع التحويل (اختياري)</label>
                                            <input type="text" name="wallet_transaction_reference" id="wallet_transaction_reference" 
                                                class="w-full rounded border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50"
                                                placeholder="أدخل رقم مرجع التحويل إذا كان متاحًا">
                                            <div class="text-xs text-gray-500 mt-1">يمكنك إضافة رقم مرجع التحويل لاحقًا من صفحة تفاصيل الطلب</div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Place Order Button -->
                    <div class="bg-white rounded shadow p-6">
                        <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 py-3 px-4 rounded font-medium transition-colors duration-200">
                            إتمام الطلب والدفع
                        </button>
                        <div class="mt-4 text-center text-xs text-gray-600">
                            بالضغط على "إتمام الطلب والدفع"، فإنك توافق على 
                            <a href="#" class="text-blue-600 hover:underline">شروط الخدمة</a> و
                            <a href="#" class="text-blue-600 hover:underline">سياسة الخصوصية</a>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Order Summary -->
            <div class="lg:w-1/4">
                <div class="bg-white rounded shadow overflow-hidden sticky top-4">
                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-xl font-semibold">ملخص الطلب</h2>
                    </div>
                    
                    <div class="p-4">
                        <button type="submit" form="checkout-form" class="w-full bg-yellow-400 hover:bg-yellow-500 py-2 px-4 rounded font-medium mb-4 transition-colors duration-200">
                            إتمام الطلب والدفع
                        </button>
                        
                        <div class="text-xs text-gray-600 mb-4 text-center">
                            بالضغط على "إتمام الطلب والدفع"، فإنك توافق على 
                            <a href="#" class="text-blue-600 hover:underline">شروط الخدمة</a> و
                            <a href="#" class="text-blue-600 hover:underline">سياسة الخصوصية</a>
                        </div>
                        
                        <div class="border-t border-b border-gray-200 py-4 mb-4">
                            <div class="font-bold text-lg mb-3">تفاصيل الطلب</div>
                            
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span>المجموع الفرعي ({{ count(session('cart')) }} منتجات):</span>
                                    <span>{{ number_format(session('cart_totals.subtotal') ?? 0, 2) }} {{ session('cart_totals.currency_symbol') ?? 'ر.س' }}</span>
                                </div>
                                
                                <div class="flex justify-between text-sm">
                                    <span>الشحن:</span>
                                    @if(session('cart_totals.shipping') > 0)
                                        <span>{{ number_format(session('cart_totals.shipping'), 2) }} {{ session('cart_totals.currency_symbol') }}</span>
                                    @else
                                        <span class="text-green-600">مجاني</span>
                                    @endif
                                </div>
                                
                                @if(session('cart_totals.tax') > 0)
                                    <div class="flex justify-between text-sm">
                                        <span>الضريبة ({{ session('cart_totals.tax_rate') }}%):</span>
                                        <span>{{ number_format(session('cart_totals.tax'), 2) }} {{ session('cart_totals.currency_symbol') }}</span>
                                    </div>
                                @endif
                                
                                @if(session('cart_totals.discount') > 0)
                                    <div class="flex justify-between text-green-600 text-sm">
                                        <span>الخصم:</span>
                                        <span>-{{ number_format(session('cart_totals.discount'), 2) }} {{ session('cart_totals.currency_symbol') }}</span>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <div class="flex justify-between font-bold text-lg">
                                    <span>الإجمالي:</span>
                                    <span>{{ number_format(session('cart_totals.total') ?? 0, 2) }} {{ session('cart_totals.currency_symbol') ?? 'ر.س' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Products Summary -->
                        <div>
                            <div class="font-bold text-sm mb-3">المنتجات ({{ count(session('cart')) }})</div>
                            <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                                @foreach(session('cart') as $id => $item)
                                    <div class="flex">
                                        <div class="w-16 h-16 flex-shrink-0">
                                            <img src="{{ $item['image_url'] ?? 'https://via.placeholder.com/70x70' }}" alt="{{ $item['name'] }}" class="w-full h-full object-contain">
                                        </div>
                                        <div class="mr-3 flex-1">
                                            <div class="text-sm text-blue-600">{{ $item['name'] }}</div>
                                            <div class="text-sm">{{ number_format($item['price'], 2) }} {{ $item['currency_symbol'] }}</div>
                                            <div class="text-gray-600 text-xs">الكمية: {{ $item['quantity'] }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle address form based on checkbox
        const useNewAddressCheckbox = document.getElementById('use_new_address');
        const newAddressForm = document.getElementById('new-address-form');
        
        if (useNewAddressCheckbox && newAddressForm) {
        useNewAddressCheckbox.addEventListener('change', function() {
            if (this.checked) {
                newAddressForm.classList.remove('hidden');
            } else {
                newAddressForm.classList.add('hidden');
            }
        });
        }
        
        // Toggle account creation fields
        const createAccountCheckbox = document.getElementById('create_account');
        const accountFields = document.getElementById('create-account-fields');
        
        if (createAccountCheckbox && accountFields) {
            createAccountCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    accountFields.classList.remove('hidden');
                } else {
                    accountFields.classList.add('hidden');
                }
            });
        }
        
        // Toggle payment method details
        const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
        const paymentDetails = document.querySelectorAll('.payment-details');
        
        function showSelectedPaymentDetails() {
            // Hide all payment details first
            paymentDetails.forEach(details => {
                details.classList.add('hidden');
            });
            
            // Show selected payment method details
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
            if (selectedMethod) {
                const detailsId = selectedMethod.value === 'card' ? 'card-details' : 
                                  selectedMethod.value === 'bank_transfer' ? 'bank-transfer-details' : 
                                  selectedMethod.value === 'mobile_wallet' ? 'mobile-wallet-details' : null;
                
                if (detailsId) {
                    const details = document.getElementById(detailsId);
                    if (details) {
                        details.classList.remove('hidden');
                    }
                }
            }
        }
        
        // Add event listeners to payment method radios
        paymentMethods.forEach(method => {
            method.addEventListener('change', showSelectedPaymentDetails);
        });
        
        // Show initial payment details
        showSelectedPaymentDetails();
    });
</script>
@endpush 