@extends('layouts.admin')

@section('title', 'عرض تفاصيل الدور')
@section('page-title', 'عرض تفاصيل الدور')

@section('actions')
    @if(!in_array($role->name, ['admin', 'customer']))
        <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-warning">
            <i class="fas fa-edit ml-1"></i> تعديل الدور
        </a>
    @endif
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4">
            <!-- بطاقة معلومات الدور -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">معلومات الدور</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-lg {{ $role->name === 'admin' ? 'bg-danger' : 'bg-info' }} text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <i class="fas {{ $role->name === 'admin' ? 'fa-user-shield' : 'fa-user-tag' }} fa-2x"></i>
                        </div>
                        <h5 class="mb-1">{{ $role->name }}</h5>
                        @if($role->name === 'admin')
                            <span class="badge bg-danger">دور المدير الرئيسي</span>
                        @elseif($role->name === 'customer')
                            <span class="badge bg-info">دور العميل</span>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="mb-2">عدد المستخدمين</h6>
                        <p class="mb-0">{{ $role->users->count() }} مستخدم</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="mb-2">عدد الصلاحيات</h6>
                        <p class="mb-0">{{ $role->permissions->count() }} صلاحية</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="mb-2">تاريخ الإنشاء</h6>
                        <p class="mb-0">{{ $role->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>
            
            <!-- بطاقة المستخدمين -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">المستخدمين</h5>
                    <span class="badge bg-primary">{{ $role->users->count() }}</span>
                </div>
                <div class="card-body p-0">
                    @if($role->users->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($role->users->take(10) as $user)
                                <a href="{{ route('admin.users.show', $user->id) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light rounded-circle text-center me-2" style="width: 40px; height: 40px; line-height: 40px;">
                                            <span class="font-weight-bold">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $user->name }}</h6>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                            
                            @if($role->users->count() > 10)
                                <div class="text-center py-2">
                                    <a href="{{ route('admin.users.index', ['role' => $role->name]) }}" class="btn btn-sm btn-outline-primary">
                                        عرض جميع المستخدمين ({{ $role->users->count() }})
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5>لا يوجد مستخدمين</h5>
                            <p class="text-muted">لا يوجد مستخدمين مرتبطين بهذا الدور</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <!-- بطاقة الصلاحيات -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">الصلاحيات</h5>
                </div>
                <div class="card-body">
                    @if($role->name === 'admin')
                        <div class="alert alert-danger mb-4">
                            <i class="fas fa-shield-alt me-2"></i>
                            <strong>تنبيه:</strong> دور المدير الرئيسي يملك جميع الصلاحيات في النظام ولا يمكن تعديلها.
                        </div>
                    @endif
                    
                    @if($role->permissions->count() > 0 || $role->name === 'admin')
                        <div class="row">
                            <!-- المستخدمين -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-users me-1"></i> إدارة المستخدمين
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if($role->name === 'admin')
                                            <div class="d-flex flex-wrap gap-2">
                                                <span class="badge bg-success">عرض المستخدمين</span>
                                                <span class="badge bg-success">إضافة مستخدمين</span>
                                                <span class="badge bg-success">تعديل المستخدمين</span>
                                                <span class="badge bg-success">حذف المستخدمين</span>
                                            </div>
                                        @else
                                            <div class="d-flex flex-wrap gap-2">
                                                @if($role->hasPermissionTo('users.view'))
                                                    <span class="badge bg-success">عرض المستخدمين</span>
                                                @else
                                                    <span class="badge bg-secondary">عرض المستخدمين</span>
                                                @endif
                                                
                                                @if($role->hasPermissionTo('users.create'))
                                                    <span class="badge bg-success">إضافة مستخدمين</span>
                                                @else
                                                    <span class="badge bg-secondary">إضافة مستخدمين</span>
                                                @endif
                                                
                                                @if($role->hasPermissionTo('users.edit'))
                                                    <span class="badge bg-success">تعديل المستخدمين</span>
                                                @else
                                                    <span class="badge bg-secondary">تعديل المستخدمين</span>
                                                @endif
                                                
                                                @if($role->hasPermissionTo('users.delete'))
                                                    <span class="badge bg-success">حذف المستخدمين</span>
                                                @else
                                                    <span class="badge bg-secondary">حذف المستخدمين</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- الأدوار -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-user-tag me-1"></i> إدارة الأدوار
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if($role->name === 'admin')
                                            <div class="d-flex flex-wrap gap-2">
                                                <span class="badge bg-success">عرض الأدوار</span>
                                                <span class="badge bg-success">إضافة أدوار</span>
                                                <span class="badge bg-success">تعديل الأدوار</span>
                                                <span class="badge bg-success">حذف الأدوار</span>
                                            </div>
                                        @else
                                            <div class="d-flex flex-wrap gap-2">
                                                @if($role->hasPermissionTo('roles.view'))
                                                    <span class="badge bg-success">عرض الأدوار</span>
                                                @else
                                                    <span class="badge bg-secondary">عرض الأدوار</span>
                                                @endif
                                                
                                                @if($role->hasPermissionTo('roles.create'))
                                                    <span class="badge bg-success">إضافة أدوار</span>
                                                @else
                                                    <span class="badge bg-secondary">إضافة أدوار</span>
                                                @endif
                                                
                                                @if($role->hasPermissionTo('roles.edit'))
                                                    <span class="badge bg-success">تعديل الأدوار</span>
                                                @else
                                                    <span class="badge bg-secondary">تعديل الأدوار</span>
                                                @endif
                                                
                                                @if($role->hasPermissionTo('roles.delete'))
                                                    <span class="badge bg-success">حذف الأدوار</span>
                                                @else
                                                    <span class="badge bg-secondary">حذف الأدوار</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- المنتجات -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-box me-1"></i> إدارة المنتجات
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if($role->name === 'admin')
                                            <div class="d-flex flex-wrap gap-2">
                                                <span class="badge bg-success">عرض المنتجات</span>
                                                <span class="badge bg-success">إضافة منتجات</span>
                                                <span class="badge bg-success">تعديل المنتجات</span>
                                                <span class="badge bg-success">حذف المنتجات</span>
                                            </div>
                                        @else
                                            <div class="d-flex flex-wrap gap-2">
                                                @if($role->hasPermissionTo('products.view'))
                                                    <span class="badge bg-success">عرض المنتجات</span>
                                                @else
                                                    <span class="badge bg-secondary">عرض المنتجات</span>
                                                @endif
                                                
                                                @if($role->hasPermissionTo('products.create'))
                                                    <span class="badge bg-success">إضافة منتجات</span>
                                                @else
                                                    <span class="badge bg-secondary">إضافة منتجات</span>
                                                @endif
                                                
                                                @if($role->hasPermissionTo('products.edit'))
                                                    <span class="badge bg-success">تعديل المنتجات</span>
                                                @else
                                                    <span class="badge bg-secondary">تعديل المنتجات</span>
                                                @endif
                                                
                                                @if($role->hasPermissionTo('products.delete'))
                                                    <span class="badge bg-success">حذف المنتجات</span>
                                                @else
                                                    <span class="badge bg-secondary">حذف المنتجات</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- الطلبات -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-shopping-cart me-1"></i> إدارة الطلبات
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if($role->name === 'admin')
                                            <div class="d-flex flex-wrap gap-2">
                                                <span class="badge bg-success">عرض الطلبات</span>
                                                <span class="badge bg-success">إضافة طلبات</span>
                                                <span class="badge bg-success">تعديل الطلبات</span>
                                                <span class="badge bg-success">حذف الطلبات</span>
                                            </div>
                                        @else
                                            <div class="d-flex flex-wrap gap-2">
                                                @if($role->hasPermissionTo('orders.view'))
                                                    <span class="badge bg-success">عرض الطلبات</span>
                                                @else
                                                    <span class="badge bg-secondary">عرض الطلبات</span>
                                                @endif
                                                
                                                @if($role->hasPermissionTo('orders.create'))
                                                    <span class="badge bg-success">إضافة طلبات</span>
                                                @else
                                                    <span class="badge bg-secondary">إضافة طلبات</span>
                                                @endif
                                                
                                                @if($role->hasPermissionTo('orders.edit'))
                                                    <span class="badge bg-success">تعديل الطلبات</span>
                                                @else
                                                    <span class="badge bg-secondary">تعديل الطلبات</span>
                                                @endif
                                                
                                                @if($role->hasPermissionTo('orders.delete'))
                                                    <span class="badge bg-success">حذف الطلبات</span>
                                                @else
                                                    <span class="badge bg-secondary">حذف الطلبات</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- الإعدادات والتقارير -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-cog me-1"></i> الإعدادات والتقارير
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if($role->name === 'admin')
                                            <div class="d-flex flex-wrap gap-2">
                                                <span class="badge bg-success">عرض الإعدادات</span>
                                                <span class="badge bg-success">تعديل الإعدادات</span>
                                                <span class="badge bg-success">تقارير المبيعات</span>
                                                <span class="badge bg-success">تقارير المخزون</span>
                                                <span class="badge bg-success">تقارير العملاء</span>
                                            </div>
                                        @else
                                            <div class="d-flex flex-wrap gap-2">
                                                @if($role->hasPermissionTo('settings.view'))
                                                    <span class="badge bg-success">عرض الإعدادات</span>
                                                @else
                                                    <span class="badge bg-secondary">عرض الإعدادات</span>
                                                @endif
                                                
                                                @if($role->hasPermissionTo('settings.edit'))
                                                    <span class="badge bg-success">تعديل الإعدادات</span>
                                                @else
                                                    <span class="badge bg-secondary">تعديل الإعدادات</span>
                                                @endif
                                                
                                                @if($role->hasPermissionTo('reports.sales'))
                                                    <span class="badge bg-success">تقارير المبيعات</span>
                                                @else
                                                    <span class="badge bg-secondary">تقارير المبيعات</span>
                                                @endif
                                                
                                                @if($role->hasPermissionTo('reports.inventory'))
                                                    <span class="badge bg-success">تقارير المخزون</span>
                                                @else
                                                    <span class="badge bg-secondary">تقارير المخزون</span>
                                                @endif
                                                
                                                @if($role->hasPermissionTo('reports.customers'))
                                                    <span class="badge bg-success">تقارير العملاء</span>
                                                @else
                                                    <span class="badge bg-secondary">تقارير العملاء</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-key fa-3x text-muted mb-3"></i>
                            <h5>لا توجد صلاحيات</h5>
                            <p class="text-muted">لم يتم إسناد أي صلاحيات لهذا الدور بعد</p>
                            
                            @if(!in_array($role->name, ['admin', 'customer']))
                                <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-primary mt-2">
                                    <i class="fas fa-edit ml-1"></i> تعديل الصلاحيات
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection 