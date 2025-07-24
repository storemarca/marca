@extends('layouts.admin')

@section('title', 'عرض بيانات المستخدم')
@section('page-title', 'عرض بيانات المستخدم')

@section('actions')
    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning">
        <i class="fas fa-edit ml-1"></i> تعديل البيانات
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4">
            <!-- بطاقة معلومات المستخدم -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">معلومات المستخدم</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-lg bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <span class="font-weight-bold fs-1">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                        <h5 class="mb-1">{{ $user->name }}</h5>
                        <p class="text-muted">
                            @foreach($user->roles as $role)
                                <span class="badge bg-info me-1">{{ $role->name }}</span>
                            @endforeach
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="mb-2">البريد الإلكتروني</h6>
                        <p class="mb-0">{{ $user->email }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="mb-2">تاريخ التسجيل</h6>
                        <p class="mb-0">{{ $user->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="mb-2">آخر تحديث</h6>
                        <p class="mb-0">{{ $user->updated_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>
            
            <!-- بطاقة الأدوار والصلاحيات -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">الأدوار والصلاحيات</h5>
                </div>
                <div class="card-body">
                    <h6 class="mb-3">الأدوار المسندة</h6>
                    <ul class="list-group mb-4">
                        @forelse($user->roles as $role)
                            <li class="list-group-item d-flex align-items-center">
                                <div class="avatar-sm {{ $role->name === 'admin' ? 'bg-danger' : 'bg-info' }} text-white rounded-circle text-center me-2" style="width: 30px; height: 30px; line-height: 30px;">
                                    <i class="fas {{ $role->name === 'admin' ? 'fa-user-shield' : 'fa-user-tag' }}"></i>
                                </div>
                                <div>
                                    <strong>{{ $role->name }}</strong>
                                    @if($role->name === 'admin')
                                        <div class="text-muted small">دور المدير الرئيسي</div>
                                    @elseif($role->name === 'customer')
                                        <div class="text-muted small">دور العميل</div>
                                    @endif
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted">
                                لا توجد أدوار مسندة
                            </li>
                        @endforelse
                    </ul>
                    
                    <h6 class="mb-3">الصلاحيات</h6>
                    @if($user->hasRole('admin'))
                        <div class="alert alert-danger mb-0">
                            <i class="fas fa-shield-alt me-2"></i>
                            هذا المستخدم لديه دور المدير ويملك <strong>جميع الصلاحيات</strong> في النظام.
                        </div>
                    @else
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @forelse($permissions as $permission)
                                <span class="badge bg-info">{{ $permission }}</span>
                            @empty
                                <p class="text-muted">لا توجد صلاحيات مسندة</p>
                            @endforelse
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <!-- بطاقة نشاط المستخدم -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">نشاط المستخدم</h5>
                    <div class="nav nav-tabs card-header-tabs">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#orders-tab">الطلبات</button>
                        @if(!$user->hasRole('customer'))
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#actions-tab">الإجراءات</button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- تبويب الطلبات -->
                        <div class="tab-pane fade show active" id="orders-tab">
                            @if($user->hasRole('customer') && isset($orders) && count($orders) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>رقم الطلب</th>
                                                <th>التاريخ</th>
                                                <th>الحالة</th>
                                                <th>المبلغ</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($orders as $order)
                                                <tr>
                                                    <td>#{{ $order->id }}</td>
                                                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }}">
                                                            {{ $order->status }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $order->total }} ريال</td>
                                                    <td>
                                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                    <h5>لا توجد طلبات</h5>
                                    <p class="text-muted">لم يقم هذا المستخدم بإجراء أي طلبات حتى الآن</p>
                                </div>
                            @endif
                        </div>
                        
                        <!-- تبويب الإجراءات -->
                        @if(!$user->hasRole('customer'))
                            <div class="tab-pane fade" id="actions-tab">
                                <div class="text-center py-4">
                                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                    <h5>سجل الإجراءات</h5>
                                    <p class="text-muted">سيتم تطوير هذه الميزة قريباً</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 