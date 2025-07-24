@extends('layouts.user')

@section('title', __('إنشاء حساب جديد'))

@section('content')
<div class="bg-gray-50 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-yellow-600 py-4">
            <h2 class="text-center text-2xl font-extrabold text-white">
                {{ __('إنشاء حساب جديد') }}
            </h2>
        </div>

        <div class="p-8">
            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <!-- الاسم -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        {{ __('الاسم') }}
                    </label>
                    <div class="mt-1">
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- البريد الإلكتروني -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        {{ __('البريد الإلكتروني') }}
                    </label>
                    <div class="mt-1">
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- كلمة المرور -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        {{ __('كلمة المرور') }}
                    </label>
                    <div class="mt-1">
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- تأكيد كلمة المرور -->
                <div>
                    <label for="password-confirm" class="block text-sm font-medium text-gray-700">
                        {{ __('تأكيد كلمة المرور') }}
                    </label>
                    <div class="mt-1">
                        <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password"
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                    </div>
                </div>

                <!-- زر التسجيل -->
                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        {{ __('تسجيل') }}
                    </button>
                </div>
            </form>

            <!-- تسجيل الدخول -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    {{ __('لديك حساب بالفعل؟') }}
                    <a href="{{ route('login') }}" class="font-medium text-yellow-600 hover:text-yellow-500">
                        {{ __('تسجيل الدخول') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection 