<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="refresh" content="3600">

    <title>{{ html_safe(__('admin.admin_panel')) }} - {{ config('app.name', 'Marca') }} - @yield('title', html_safe(__('admin.dashboard')))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @if(app()->getLocale() == 'ar')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @endif
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Admin CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin-modern.css') }}">
    
    <!-- Page Specific CSS -->
    @if(request()->routeIs('admin.orders.*'))
    <link rel="stylesheet" href="{{ asset('css/order-details.css') }}">
    @endif
    
    <!-- Custom CSS -->
    @if(!empty($settings['custom_css']))
    <style>
        {!! $settings['custom_css'] !!}
    </style>
    @endif
    
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar admin-navbar sticky-top">
        <div class="container-fluid">
            <!-- Sidebar Toggle Button (Mobile) -->
            <button class="btn btn-link d-lg-none me-2 text-white" id="sidebarToggle" type="button">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Brand -->
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <div class="navbar-brand-logo">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                {{ config('app.name', 'Marca') }} - لوحة التحكم
            </a>
            
            <!-- Navbar Toggler -->
            <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-ellipsis-v"></i>
            </button>
            
            <!-- Navbar Content -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <!-- Right Navbar -->
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <!-- Language Switcher -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-globe me-1"></i>
                            {{ app()->getLocale() == 'ar' ? html_safe(__('arabic')) : html_safe(__('english')) }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                            <li><a class="dropdown-item {{ app()->getLocale() == 'ar' ? 'active' : '' }}" href="{{ route('language.switch', ['locale' => 'ar']) }}">{{ html_safe(__('arabic')) }}</a></li>
                            <li><a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}" href="{{ route('language.switch', ['locale' => 'en']) }}">{{ html_safe(__('english')) }}</a></li>
                        </ul>
                    </li>
                    
                    <!-- Notifications -->
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                0
                                <span class="visually-hidden">unread notifications</span>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationsDropdown" style="width: 300px;">
                            <li>
                                <div class="dropdown-header d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">{{ html_safe(__('admin.notifications')) }}</span>
                                    <a href="#" class="text-muted small">{{ html_safe(__('admin.mark_all_as_read')) }}</a>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <div class="text-center py-3">
                                    <i class="fas fa-bell-slash text-muted mb-2" style="font-size: 1.5rem;"></i>
                                    <p class="text-muted mb-0 small">{{ html_safe(__('admin.no_notifications')) }}</p>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-center small" href="#">{{ html_safe(__('admin.view_all_notifications')) }}</a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- User Menu -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="avatar-circle me-2">
                                <span class="avatar-initials">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                            <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.profile') }}">
                                    <i class="fas fa-user me-2 text-primary"></i>
                                    {{ html_safe(__('admin.profile')) }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.settings.general') }}">
                                    <i class="fas fa-cog me-2 text-primary"></i>
                                    {{ html_safe(__('admin.settings')) }}
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2 text-danger"></i>
                                    {{ html_safe(__('admin.logout')) }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="py-2">
                <!-- Main Navigation -->
                <ul class="nav flex-column">
                    <!-- تعديل العناصر في القائمة الجانبية -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            {{ html_safe(__('admin.dashboard')) }}
                        </a>
                    </li>
                    
                    <div class="sidebar-heading">{{ html_safe(__('admin.catalog')) }}</div>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                            <i class="fas fa-box"></i>
                            {{ html_safe(__('admin.products')) }}
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                            <i class="fas fa-tags"></i>
                            {{ html_safe(__('admin.categories')) }}
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.warehouses.*') ? 'active' : '' }}" href="{{ route('admin.warehouses.index') }}">
                            <i class="fas fa-warehouse"></i>
                            {{ html_safe(__('warehouses')) }}
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.countries.*') ? 'active' : '' }}" href="{{ route('admin.countries.index') }}">
                            <i class="fas fa-globe"></i>
                            {{ html_safe(__('countries')) }}
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.regions.*') ? 'active' : '' }}" href="#" id="regionsDropdown" role="button" data-bs-toggle="collapse" data-bs-target="#regionsSubMenu" aria-expanded="{{ request()->routeIs('admin.regions.*') ? 'true' : 'false' }}">
                            <i class="fas fa-map-marked-alt"></i>
                            المناطق الجغرافية
                        </a>
                        <div class="collapse {{ request()->routeIs('admin.regions.*') ? 'show' : '' }}" id="regionsSubMenu">
                            <ul class="nav flex-column ms-3">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.regions.governorates.*') ? 'active' : '' }}" href="{{ route('admin.regions.governorates.index') }}">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        المحافظات
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.regions.districts.*') ? 'active' : '' }}" href="{{ route('admin.regions.districts.index') }}">
                                        <i class="fas fa-map-pin me-2"></i>
                                        المراكز
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.regions.areas.*') ? 'active' : '' }}" href="{{ route('admin.regions.areas.index') }}">
                                        <i class="fas fa-map me-2"></i>
                                        المناطق
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    
                    <div class="sidebar-heading">{{ html_safe(__('admin.sales')) }}</div>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                            <i class="fas fa-shopping-cart"></i>
                            {{ html_safe(__('admin.orders')) }}
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.sales') ? 'active' : '' }}" href="{{ route('admin.sales') }}">
                            <i class="fas fa-list-alt"></i>
                            {{ html_safe(__('admin.order_statuses')) }}
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">
                            <i class="fas fa-users"></i>
                            {{ html_safe(__('admin.customers')) }}
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.shipments.*') ? 'active' : '' }}" href="{{ route('admin.shipments.index') }}">
                            <i class="fas fa-truck"></i>
                            {{ html_safe(__('admin.shipments')) }}
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.shipping-companies.*') ? 'active' : '' }}" href="{{ route('admin.shipping-companies.index') }}">
                            <i class="fas fa-shipping-fast"></i>
                            شركات الشحن
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.shipping-methods.*') ? 'active' : '' }}" href="{{ route('admin.shipping-methods.index') }}">
                            <i class="fas fa-truck-loading"></i>
                            طرق الشحن
                        </a>
                    </li>
                    
                    <div class="sidebar-heading">{{ html_safe(__('admin.reports')) }}</div>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reports.inventory') ? 'active' : '' }}" href="{{ route('admin.reports.inventory') }}">
                            <i class="fas fa-chart-bar"></i>
                            {{ html_safe(__('admin.reports')) }}
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reports.stock-movements') ? 'active' : '' }}" href="{{ route('admin.reports.stock-movements') }}">
                            <i class="fas fa-history"></i>
                            سجل حركات المخزون
                        </a>
                    </li>
                    
                    <div class="sidebar-heading">{{ html_safe(__('admin.administration')) }}</div>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                            <i class="fas fa-user-shield"></i>
                            {{ html_safe(__('admin.users')) }}
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">
                            <i class="fas fa-user-lock"></i>
                            {{ html_safe(__('admin.roles')) }}
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.general') }}">
                            <i class="fas fa-cog"></i>
                            {{ html_safe(__('admin.settings')) }}
                        </a>
                    </li>
                    
                    <div class="sidebar-heading">التسويق والشركاء</div>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.affiliates.*') ? 'active' : '' }}" href="{{ route('admin.affiliates.index') }}">
                            <i class="fas fa-handshake"></i>
                            التسويق بالعمولة
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.affiliates.withdrawal-requests') ? 'active' : '' }}" href="{{ route('admin.affiliates.withdrawal-requests') }}">
                            <i class="fas fa-money-bill-wave"></i>
                            طلبات السحب
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.affiliates.dashboard') ? 'active' : '' }}" href="{{ route('admin.affiliates.dashboard') }}">
                            <i class="fas fa-chart-line"></i>
                            إحصائيات العمولات
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1>@yield('page-title', html_safe(__('admin.dashboard')))</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ html_safe(__('admin.dashboard')) }}</a></li>
                            @yield('breadcrumbs')
                        </ol>
                    </nav>
                </div>
                <div>
                    @yield('actions')
                </div>
            </div>
            
            <!-- Alerts -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>يوجد أخطاء في البيانات المدخلة:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            <!-- Main Content Area -->
            <div class="content-area">
                @yield('content')
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Admin JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle sidebar on mobile
            document.getElementById('sidebarToggle')?.addEventListener('click', function() {
                document.getElementById('sidebar').classList.toggle('show');
            });
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                const sidebar = document.getElementById('sidebar');
                const sidebarToggle = document.getElementById('sidebarToggle');
                
                if (sidebar && sidebarToggle && window.innerWidth < 992) {
                    if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target) && sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                    }
                }
            });
            
            // إضافة تأثيرات حركية للعناصر
            document.querySelectorAll('.card, .stat-card').forEach(function(element) {
                element.classList.add('fade-in');
            });
            
            // تحسين تجربة المستخدم للنماذج
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    const submitButton = this.querySelector('button[type="submit"]');
                    if (submitButton && !submitButton.disabled) {
                        submitButton.disabled = true;
                        
                        // إضافة أيقونة تحميل
                        const originalText = submitButton.innerHTML;
                        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري التنفيذ...';
                        
                        // إعادة تمكين الزر بعد 5 ثوان في حالة حدوث خطأ
                        setTimeout(() => {
                            if (submitButton.disabled) {
                                submitButton.disabled = false;
                                submitButton.innerHTML = originalText;
                            }
                        }, 5000);
                    }
                });
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html> 