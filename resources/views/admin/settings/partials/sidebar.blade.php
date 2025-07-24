<div class="card">
    <div class="card-header">
        <h5 class="mb-0">الإعدادات</h5>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            <a href="{{ route('admin.settings.general') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.general') ? 'active' : '' }}">
                <i class="fas fa-cog me-2"></i> الإعدادات العامة
            </a>
            <a href="{{ route('admin.settings.homepage') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.homepage') ? 'active' : '' }}">
                <i class="fas fa-home me-2"></i> إعدادات الصفحة الرئيسية
            </a>
            <a href="{{ route('admin.settings.theme') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.theme') ? 'active' : '' }}">
                <i class="fas fa-paint-brush me-2"></i> إعدادات الثيمات والألوان
            </a>
            <a href="{{ route('admin.settings.mail') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.mail') ? 'active' : '' }}">
                <i class="fas fa-envelope me-2"></i> إعدادات البريد الإلكتروني
            </a>
            <a href="{{ route('admin.settings.shipping') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.shipping') ? 'active' : '' }}">
                <i class="fas fa-shipping-fast me-2"></i> إعدادات الشحن
            </a>
            <a href="{{ route('admin.settings.payment') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.payment') ? 'active' : '' }}">
                <i class="fas fa-credit-card me-2"></i> إعدادات الدفع
            </a>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">إدارة النظام</h5>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-user-shield me-2"></i> إدارة المستخدمين
            </a>
            <a href="{{ route('admin.roles.index') }}" class="list-group-item list-group-item-action">
                <i class="fas fa-user-lock me-2"></i> الأدوار والصلاحيات
            </a>
        </div>
    </div>
</div> 