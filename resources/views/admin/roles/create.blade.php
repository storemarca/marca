@extends('layouts.admin')

@section('title', 'إضافة دور جديد')
@section('page-title', 'إضافة دور جديد')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">بيانات الدور الجديد</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label">اسم الدور <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    <small class="form-text text-muted">يجب أن يكون الاسم فريدًا ويفضل استخدام الحروف الصغيرة بدون مسافات (مثال: editor, sales_manager)</small>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label class="form-label d-block">الصلاحيات <span class="text-danger">*</span></label>
                    
                    <div class="border rounded p-3">
                        <div class="row">
                            <!-- المستخدمين -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <div class="form-check">
                                            <input class="form-check-input permission-group" type="checkbox" id="users_all" data-group="users">
                                            <label class="form-check-label fw-bold" for="users_all">
                                                <i class="fas fa-users me-1"></i> إدارة المستخدمين
                                            </label>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-item" type="checkbox" id="users_view" name="permissions[]" value="users.view" data-group="users" {{ in_array('users.view', old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="users_view">عرض المستخدمين</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-item" type="checkbox" id="users_create" name="permissions[]" value="users.create" data-group="users" {{ in_array('users.create', old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="users_create">إضافة مستخدمين</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-item" type="checkbox" id="users_edit" name="permissions[]" value="users.edit" data-group="users" {{ in_array('users.edit', old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="users_edit">تعديل المستخدمين</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input permission-item" type="checkbox" id="users_delete" name="permissions[]" value="users.delete" data-group="users" {{ in_array('users.delete', old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="users_delete">حذف المستخدمين</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- الأدوار -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <div class="form-check">
                                            <input class="form-check-input permission-group" type="checkbox" id="roles_all" data-group="roles">
                                            <label class="form-check-label fw-bold" for="roles_all">
                                                <i class="fas fa-user-tag me-1"></i> إدارة الأدوار
                                            </label>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-item" type="checkbox" id="roles_view" name="permissions[]" value="roles.view" data-group="roles" {{ in_array('roles.view', old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="roles_view">عرض الأدوار</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-item" type="checkbox" id="roles_create" name="permissions[]" value="roles.create" data-group="roles" {{ in_array('roles.create', old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="roles_create">إضافة أدوار</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-item" type="checkbox" id="roles_edit" name="permissions[]" value="roles.edit" data-group="roles" {{ in_array('roles.edit', old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="roles_edit">تعديل الأدوار</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input permission-item" type="checkbox" id="roles_delete" name="permissions[]" value="roles.delete" data-group="roles" {{ in_array('roles.delete', old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="roles_delete">حذف الأدوار</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- المنتجات -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <div class="form-check">
                                            <input class="form-check-input permission-group" type="checkbox" id="products_all" data-group="products">
                                            <label class="form-check-label fw-bold" for="products_all">
                                                <i class="fas fa-box me-1"></i> إدارة المنتجات
                                            </label>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-item" type="checkbox" id="products_view" name="permissions[]" value="products.view" data-group="products" {{ in_array('products.view', old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="products_view">عرض المنتجات</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-item" type="checkbox" id="products_create" name="permissions[]" value="products.create" data-group="products" {{ in_array('products.create', old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="products_create">إضافة منتجات</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-item" type="checkbox" id="products_edit" name="permissions[]" value="products.edit" data-group="products" {{ in_array('products.edit', old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="products_edit">تعديل المنتجات</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input permission-item" type="checkbox" id="products_delete" name="permissions[]" value="products.delete" data-group="products" {{ in_array('products.delete', old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="products_delete">حذف المنتجات</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- الطلبات -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <div class="form-check">
                                            <input class="form-check-input permission-group" type="checkbox" id="orders_all" data-group="orders">
                                            <label class="form-check-label fw-bold" for="orders_all">
                                                <i class="fas fa-shopping-cart me-1"></i> إدارة الطلبات
                                            </label>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-item" type="checkbox" id="orders_view" name="permissions[]" value="orders.view" data-group="orders" {{ in_array('orders.view', old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="orders_view">عرض الطلبات</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-item" type="checkbox" id="orders_create" name="permissions[]" value="orders.create" data-group="orders" {{ in_array('orders.create', old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="orders_create">إضافة طلبات</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-item" type="checkbox" id="orders_edit" name="permissions[]" value="orders.edit" data-group="orders" {{ in_array('orders.edit', old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="orders_edit">تعديل الطلبات</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input permission-item" type="checkbox" id="orders_delete" name="permissions[]" value="orders.delete" data-group="orders" {{ in_array('orders.delete', old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="orders_delete">حذف الطلبات</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- الإعدادات -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <div class="form-check">
                                            <input class="form-check-input permission-group" type="checkbox" id="settings_all" data-group="settings">
                                            <label class="form-check-label fw-bold" for="settings_all">
                                                <i class="fas fa-cog me-1"></i> إدارة الإعدادات
                                            </label>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-item" type="checkbox" id="settings_view" name="permissions[]" value="settings.view" data-group="settings" {{ in_array('settings.view', old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="settings_view">عرض الإعدادات</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-item" type="checkbox" id="settings_edit" name="permissions[]" value="settings.edit" data-group="settings" {{ in_array('settings.edit', old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="settings_edit">تعديل الإعدادات</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- التقارير -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <div class="form-check">
                                            <input class="form-check-input permission-group" type="checkbox" id="reports_all" data-group="reports">
                                            <label class="form-check-label fw-bold" for="reports_all">
                                                <i class="fas fa-chart-bar me-1"></i> التقارير
                                            </label>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-item" type="checkbox" id="reports_sales" name="permissions[]" value="reports.sales" data-group="reports" {{ in_array('reports.sales', old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="reports_sales">تقارير المبيعات</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-item" type="checkbox" id="reports_inventory" name="permissions[]" value="reports.inventory" data-group="reports" {{ in_array('reports.inventory', old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="reports_inventory">تقارير المخزون</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-item" type="checkbox" id="reports_customers" name="permissions[]" value="reports.customers" data-group="reports" {{ in_array('reports.customers', old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="reports_customers">تقارير العملاء</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @error('permissions')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">إضافة الدور</button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // تحديد/إلغاء تحديد جميع الصلاحيات في المجموعة
        $('.permission-group').change(function() {
            const group = $(this).data('group');
            const isChecked = $(this).prop('checked');
            
            $(`.permission-item[data-group="${group}"]`).prop('checked', isChecked);
        });
        
        // تحديث حالة checkbox المجموعة بناءً على حالة الصلاحيات الفردية
        $('.permission-item').change(function() {
            const group = $(this).data('group');
            const totalItems = $(`.permission-item[data-group="${group}"]`).length;
            const checkedItems = $(`.permission-item[data-group="${group}"]:checked`).length;
            
            $(`#${group}_all`).prop('checked', totalItems === checkedItems);
        });
        
        // تطبيق الحالة الأولية للمجموعات
        $('.permission-group').each(function() {
            const group = $(this).data('group');
            const totalItems = $(`.permission-item[data-group="${group}"]`).length;
            const checkedItems = $(`.permission-item[data-group="${group}"]:checked`).length;
            
            $(this).prop('checked', totalItems === checkedItems && totalItems > 0);
        });
    });
</script>
@endsection 