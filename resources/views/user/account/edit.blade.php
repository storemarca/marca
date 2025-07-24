@extends('layouts.user')

@section('title', __('edit_account'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">{{ __('edit_account') }}</h1>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6">
                <form action="{{ route('user.account.update') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('name') }}</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('email') }}</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">{{ __('phone') }}</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" 
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="border-t border-gray-200 pt-6 mt-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">{{ __('change_password') }}</h2>
                        <p class="text-sm text-gray-500 mb-4">{{ __('leave_blank_if_no_change') }}</p>

                        <div class="mb-6">
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('current_password') }}</label>
                            <input type="password" name="current_password" id="current_password" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm">
                            @error('current_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('new_password') }}</label>
                            <input type="password" name="password" id="password" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">{{ __('confirm_password') }}</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <a href="{{ route('user.account.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 me-4">
                            {{ __('cancel') }}
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            {{ __('save_changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 