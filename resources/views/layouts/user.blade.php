<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', setting('site_description'))">
    <meta name="keywords" content="@yield('meta_keywords', 'ecommerce, online store, shop')">
    <meta property="og:title" content="@yield('title', setting('site_name'))">
    <meta property="og:description" content="@yield('meta_description', setting('site_description'))">
    <meta property="og:image" content="@yield('meta_image', asset('images/logo.png'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">

    <title>@yield('title', setting('site_name'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/basic-styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/css/theme-custom.css') }}">
    
    @stack('styles')

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // تكوين Tailwind CSS بناءً على إعدادات الثيم
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '{{ setting('primary_color', '#eab308') }}',
                        secondary: '{{ setting('secondary_color', '#1f2937') }}',
                        accent: '{{ setting('accent_color', '#ef4444') }}',
                    }
                }
            }
        }
    </script>
    @stack('scripts-header')
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900 min-h-screen flex flex-col {{ setting('enable_dark_mode') ? 'dark-mode-support' : '' }}">
    <!-- Header -->
    <!-- Country Info Bar -->
    <div class="bg-yellow-50 text-yellow-800 py-1">
        <div class="container mx-auto px-4 flex items-center justify-center">
            @php
                $currentCountry = current_country();
            @endphp
            <div class="flex items-center text-sm">
                <img src="{{ asset('images/flags/' . strtolower($currentCountry->code) . '.svg') }}" 
                     alt="{{ $currentCountry->name }}" 
                     class="w-4 h-4 rounded-full object-cover border border-gray-200 me-1" 
                     onerror="this.src='{{ asset('images/flags/placeholder.svg') }}'">
                <span>{{ __('general.currently_shopping_in') }} <strong>{{ $currentCountry->name }}</strong></span>
                <button id="change-country-btn" class="text-yellow-600 hover:text-yellow-800 underline ms-2 text-xs">
                    {{ __('general.change_country') }}
                </button>
            </div>
        </div>
    </div>
    
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center">
                        @if(setting('site_logo'))
                            <img src="{{ asset('storage/' . setting('site_logo')) }}" alt="{{ setting('site_name') }}" class="h-10">
                        @else
                            <span class="text-xl font-bold text-yellow-600">{{ setting('site_name') }}</span>
                        @endif
                    </a>
                </div>

                <!-- Navigation - Desktop -->
                <nav class="hidden md:flex space-x-8 rtl:space-x-reverse">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-yellow-600 px-3 py-2 rounded-md text-sm font-medium">{{ __('general.home') }}</a>
                    <a href="{{ route('user.products.index') }}" class="text-gray-700 hover:text-yellow-600 px-3 py-2 rounded-md text-sm font-medium">{{ __('general.products') }}</a>
                    @auth
                        <a href="{{ route('user.orders.index') }}" class="text-gray-700 hover:text-yellow-600 px-3 py-2 rounded-md text-sm font-medium">{{ __('general.my_orders') }}</a>
                        <a href="{{ route('user.account.index') }}" class="text-gray-700 hover:text-yellow-600 px-3 py-2 rounded-md text-sm font-medium">{{ __('general.my_account') }}</a>
                    @endauth
                </nav>

                <!-- Right Side -->
                <div class="flex items-center space-x-4 rtl:space-x-reverse">
                    <!-- Country Switcher -->
                    <div class="relative">
                        <button id="country-dropdown" class="flex items-center text-gray-700 hover:text-yellow-600">
                            @php
                                $currentCountry = current_country();
                            @endphp
                            <div class="flex items-center">
                                <img src="{{ asset('images/flags/' . strtolower($currentCountry->code) . '.svg') }}" 
                                     alt="{{ $currentCountry->name }}" 
                                     class="w-5 h-5 rounded-full object-cover border border-gray-200 me-1" 
                                     onerror="this.src='{{ asset('images/flags/placeholder.svg') }}'">
                                <span class="text-sm font-medium">{{ $currentCountry->name }}</span>
                                <svg class="w-4 h-4 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </button>
                        <div id="country-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            @php
                                $countries = \App\Models\Country::where('is_active', true)->get();
                            @endphp
                            @foreach($countries as $country)
                                <a href="{{ route('country.switch', $country->id) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <img src="{{ asset('images/flags/' . strtolower($country->code) . '.svg') }}" 
                                         alt="{{ $country->name }}" 
                                         class="w-5 h-5 rounded-full object-cover border border-gray-200 me-2" 
                                         onerror="this.src='{{ asset('images/flags/placeholder.svg') }}'">
                                    <span>{{ $country->name }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Language Switcher -->
                    <div class="relative">
                        <button id="language-dropdown" class="flex items-center text-gray-700 hover:text-yellow-600">
                            <span class="text-sm">{{ app()->getLocale() == 'ar' ? 'العربية' : 'English' }}</span>
                            <svg class="w-4 h-4 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="language-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            <a href="{{ route('language.switch', ['locale' => 'ar']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">العربية</a>
                            <a href="{{ route('language.switch', ['locale' => 'en']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">English</a>
                        </div>
                    </div>

                    <!-- Cart -->
                    <a href="{{ route('cart.index') }}" class="relative text-gray-700 hover:text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        @if(session()->has('cart') && count(session()->get('cart')) > 0)
                            <span class="absolute top-0 right-0 -mt-1 -mr-1 bg-yellow-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-xs">{{ count(session()->get('cart')) }}</span>
                        @endif
                    </a>

                    <!-- User Menu -->
                    @auth
                        <div class="relative">
                            <button id="user-dropdown" class="flex items-center text-gray-700 hover:text-yellow-600">
                                <span class="text-sm font-medium hidden md:block">{{ Auth::user()->name }}</span>
                                <svg class="w-6 h-6 md:ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </button>
                            <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <a href="{{ route('user.account.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('user.my_account') }}</a>
                                <a href="{{ route('user.orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('user.my_orders') }}</a>
                                
                                <!-- Affiliate Link -->
                                @if(Auth::user()->hasAffiliate())
                                    <a href="{{ route('affiliate.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">المسوق بالعمولة</a>
                                @else
                                    <a href="{{ route('affiliate.apply') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">انضم كمسوق بالعمولة</a>
                                @endif
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('user.logout') }}</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-yellow-600 px-3 py-2 rounded-md text-sm font-medium">{{ __('user.login') }}</a>
                        <a href="{{ route('register') }}" class="hidden md:inline-flex text-white bg-yellow-600 hover:bg-yellow-700 px-4 py-2 rounded-md text-sm font-medium">{{ __('user.register') }}</a>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-button" class="md:hidden text-gray-700 hover:text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <div class="flex flex-col space-y-2">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:bg-gray-100 px-3 py-2 rounded-md text-base font-medium">{{ __('general.home') }}</a>
                    <a href="{{ route('user.products.index') }}" class="text-gray-700 hover:bg-gray-100 px-3 py-2 rounded-md text-base font-medium">{{ __('general.products') }}</a>
                    @auth
                        <a href="{{ route('user.orders.index') }}" class="text-gray-700 hover:bg-gray-100 px-3 py-2 rounded-md text-base font-medium">{{ __('general.my_orders') }}</a>
                        <a href="{{ route('user.account.index') }}" class="text-gray-700 hover:bg-gray-100 px-3 py-2 rounded-md text-base font-medium">{{ __('general.my_account') }}</a>
                    @else
                        <a href="{{ route('register') }}" class="text-gray-700 hover:bg-gray-100 px-3 py-2 rounded-md text-base font-medium">{{ __('user.register') }}</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Affiliate Banner -->
    @auth
        @if(!Auth::user()->hasAffiliate())
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-2 px-4">
            <div class="container mx-auto flex flex-wrap items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-money-bill-wave mr-2"></i>
                    <span class="font-medium">كن مسوقًا بالعمولة واربح المال عند ترويج منتجاتنا!</span>
                </div>
                <a href="{{ route('affiliate.apply') }}" class="mt-2 md:mt-0 bg-white text-indigo-700 hover:bg-indigo-100 px-4 py-1 rounded-full text-sm font-medium transition-colors duration-200">
                    انضم الآن
                </a>
            </div>
        </div>
        @endif
    @endauth

    <!-- Main Content -->
    <main class="flex-grow">
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">{{ setting('site_name') }}</h3>
                    <p class="text-gray-300">{{ setting('site_description') }}</p>
                    <div class="mt-4 flex space-x-4 rtl:space-x-reverse">
                        <a href="#" class="text-gray-300 hover:text-white">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"/></svg>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/></svg>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">{{ __('general.quick_links') }}</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-gray-300 hover:text-white">{{ __('general.home') }}</a></li>
                        <li><a href="{{ route('user.products.index') }}" class="text-gray-300 hover:text-white">{{ __('general.products') }}</a></li>
                        <li><a href="{{ route('orders.track') }}" class="text-gray-300 hover:text-white">{{ __('general.track_order') }}</a></li>
                        @guest
                            <li><a href="{{ route('login') }}" class="text-gray-300 hover:text-white">{{ __('user.login') }}</a></li>
                            <li><a href="{{ route('register') }}" class="text-gray-300 hover:text-white">{{ __('user.register') }}</a></li>
                        @endguest
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">{{ __('general.contact_us') }}</h3>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mt-0.5 me-2 rtl:ml-2 rtl:mr-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="text-gray-300">{{ setting('site_address') }}</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mt-0.5 me-2 rtl:ml-2 rtl:mr-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span class="text-gray-300">{{ setting('site_email') }}</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mt-0.5 me-2 rtl:ml-2 rtl:mr-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span class="text-gray-300">{{ setting('site_phone') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400 text-sm">
                <p>&copy; {{ date('Y') }} {{ setting('site_name') }}. {{ __('general.all_rights_reserved') }}</p>
            </div>
        </div>
    </footer>

    @if(setting('enable_dark_mode'))
    <div class="dark-mode-toggle" id="dark-mode-toggle">
        <svg id="dark-mode-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="hidden">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
        <svg id="light-mode-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
    </div>
    @endif

    <!-- Scripts -->
    <script>
        // تحسين كود JavaScript لمنع مشكلة التحميل المستمر
        document.addEventListener('DOMContentLoaded', function() {
            // تحميل الصور مسبقاً لتجنب مشكلة التحميل المستمر
            const preloadFlags = () => {
                const countries = ['sa', 'ae', 'eg', 'us'];
                countries.forEach(code => {
                    const img = new Image();
                    img.src = `{{ asset('images/flags/') }}/${code}.svg`;
                });
                
                // تحميل الصورة الافتراضية
                const placeholder = new Image();
                placeholder.src = `{{ asset('images/flags/placeholder.svg') }}`;
            };
            
            // تنفيذ التحميل المسبق
            preloadFlags();
            
            // التحقق من وضع العرض المحفوظ
            @if(setting('enable_dark_mode'))
            const darkModeToggle = document.getElementById('dark-mode-toggle');
            const darkModeIcon = document.getElementById('dark-mode-icon');
            const lightModeIcon = document.getElementById('light-mode-icon');
            const body = document.body;
            
            // التحقق من الوضع المحفوظ في localStorage
            const isDarkMode = localStorage.getItem('dark-mode') === 'true';
            
            // تطبيق الوضع المحفوظ
            if (isDarkMode) {
                body.classList.add('dark-mode');
                darkModeIcon.classList.remove('hidden');
                lightModeIcon.classList.add('hidden');
            }
            
            // تبديل الوضع عند النقر على الزر
            if (darkModeToggle) {
                darkModeToggle.addEventListener('click', function() {
                    body.classList.toggle('dark-mode');
                    const isNowDark = body.classList.contains('dark-mode');
                    
                    // حفظ الإعداد في localStorage
                    localStorage.setItem('dark-mode', isNowDark);
                    
                    // تبديل الأيقونات
                    if (isNowDark) {
                        darkModeIcon.classList.remove('hidden');
                        lightModeIcon.classList.add('hidden');
                    } else {
                        darkModeIcon.classList.add('hidden');
                        lightModeIcon.classList.remove('hidden');
                    }
                });
            }
            @endif
            
            // Language dropdown
            const languageDropdown = document.getElementById('language-dropdown');
            const languageMenu = document.getElementById('language-menu');
            
            if (languageDropdown && languageMenu) {
                languageDropdown.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    languageMenu.classList.toggle('hidden');
                });
            }
            
            // Country dropdown
            const countryDropdown = document.getElementById('country-dropdown');
            const countryMenu = document.getElementById('country-menu');
            
            if (countryDropdown && countryMenu) {
                countryDropdown.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    countryMenu.classList.toggle('hidden');
                });
            }
            
            // Change country button
            const changeCountryBtn = document.getElementById('change-country-btn');
            
            if (changeCountryBtn && countryMenu && countryDropdown) {
                changeCountryBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    // Scroll to the top of the page
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    
                    // Highlight the country dropdown with a pulse animation
                    countryDropdown.classList.add('animate-pulse');
                    setTimeout(function() {
                        countryDropdown.classList.remove('animate-pulse');
                        // Show the country menu after a short delay
                        countryMenu.classList.remove('hidden');
                    }, 800);
                });
            }
            
            // User dropdown
            const userDropdown = document.getElementById('user-dropdown');
            const userMenu = document.getElementById('user-menu');
            
            if (userDropdown && userMenu) {
                userDropdown.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    userMenu.classList.toggle('hidden');
                });
            }
            
            // Mobile menu
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    mobileMenu.classList.toggle('hidden');
                });
            }
            
            // Close dropdowns when clicking outside
            document.addEventListener('click', function(event) {
                if (languageDropdown && languageMenu && !languageDropdown.contains(event.target)) {
                    languageMenu.classList.add('hidden');
                }
                
                if (countryDropdown && countryMenu && !countryDropdown.contains(event.target) && 
                    (!changeCountryBtn || !changeCountryBtn.contains(event.target))) {
                    countryMenu.classList.add('hidden');
                }
                
                if (userDropdown && userMenu && !userDropdown.contains(event.target)) {
                    userMenu.classList.add('hidden');
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html> 