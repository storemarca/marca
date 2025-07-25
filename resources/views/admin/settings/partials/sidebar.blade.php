<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ safe_trans('admin.settings') }}</h5>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            <a href="{{ route('admin.settings.general') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.general') ? 'active' : '' }}">
                <i class="fas fa-cog me-2"></i> {{ safe_trans('admin.general_settings') }}
            </a>
            <a href="{{ route('admin.settings.language') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.language') ? 'active' : '' }}">
                <i class="fas fa-language me-2"></i> {{ safe_trans('admin.language_settings') }}
            </a>
            <a href="{{ route('admin.settings.homepage') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.homepage') ? 'active' : '' }}">
                <i class="fas fa-home me-2"></i> {{ safe_trans('admin.homepage_settings') }}
            </a>
            <a href="{{ route('admin.settings.theme') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.theme') ? 'active' : '' }}">
                <i class="fas fa-paint-brush me-2"></i> {{ safe_trans('admin.theme_settings') }}
            </a>
            <a href="{{ route('admin.settings.mail') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.mail') ? 'active' : '' }}">
                <i class="fas fa-envelope me-2"></i> {{ safe_trans('admin.mail_settings') }}
            </a>
            <a href="{{ route('admin.settings.shipping') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.shipping') ? 'active' : '' }}">
                <i class="fas fa-shipping-fast me-2"></i> {{ safe_trans('admin.shipping_settings') }}
            </a>
            <a href="{{ route('admin.settings.payment') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.payment') ? 'active' : '' }}">
                <i class="fas fa-credit-card me-2"></i> {{ safe_trans('admin.payment_settings') }}
            </a>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">{{ safe_trans('admin.system_management') }}</h5>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-user-shield me-2"></i> {{ safe_trans('admin.user_management') }}
            </a>
            <a href="{{ route('admin.roles.index') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-user-lock me-2"></i> {{ safe_trans('admin.roles_permissions') }}
            </a>
        </div>
    </div>
</div> 