@extends('layouts.admin')

@section('title', 'إدارة العملاء')

@section('content')
<div class="container-fluid fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة العملاء</h1>
        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> إضافة عميل جديد
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card primary">
                <div class="stat-icon primary">
                    <i class="fas fa-users fa-lg"></i>
                </div>
                <div class="stat-value">{{ $customers->total() }}</div>
                <div class="stat-label">إجمالي العملاء</div>
                <div class="mt-3 pt-2 border-top">
                    <div class="d-flex justify-content-between align-items-center small text-muted">
                        <span>عملاء جدد هذا الشهر</span>
                        <span class="fw-medium">{{ \App\Models\Customer::whereMonth('created_at', now()->month)->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="stat-card success">
                <div class="stat-icon success">
                    <i class="fas fa-user-check fa-lg"></i>
                </div>
                <div class="stat-value">{{ \App\Models\Customer::where('is_active', true)->count() }}</div>
                <div class="stat-label">العملاء النشطين</div>
                <div class="mt-3 pt-2 border-top">
                    <div class="d-flex justify-content-between align-items-center small text-muted">
                        <span>نسبة من الإجمالي</span>
                        <span class="fw-medium">
                            @php
                                $total = \App\Models\Customer::count();
                                $active = \App\Models\Customer::where('is_active', true)->count();
                                $percentage = $total > 0 ? round(($active / $total) * 100, 1) : 0;
                            @endphp
                            {{ $percentage }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="stat-card info">
                <div class="stat-icon info">
                    <i class="fas fa-shopping-bag fa-lg"></i>
                </div>
                <div class="stat-value">
                    @php
                        $totalCustomers = \App\Models\Customer::count();
                        $totalOrders = \App\Models\Order::count();
                        $average = $totalCustomers > 0 ? round($totalOrders / $totalCustomers, 1) : 0;
                    @endphp
                    {{ $average }}
                </div>
                <div class="stat-label">متوسط الطلبات لكل عميل</div>
                <div class="mt-3 pt-2 border-top">
                    <div class="d-flex justify-content-between align-items-center small text-muted">
                        <span>إجمالي الطلبات</span>
                        <span class="fw-medium">{{ $totalOrders }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Search Form -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">بحث متقدم</h5>
            <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="collapse" data-bs-target="#searchCollapse" aria-expanded="false" aria-controls="searchCollapse">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        <div class="collapse show" id="searchCollapse">
            <div class="card-body">
                <form action="{{ route('admin.customers.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search" class="form-label">بحث</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control" placeholder="الاسم، البريد الإلكتروني، رقم الهاتف...">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status" class="form-label">حالة العميل</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">جميع الحالات</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="country_id" class="form-label">البلد</label>
                                <select name="country_id" id="country_id" class="form-select">
                                    <option value="">جميع البلدان</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date_from" class="form-label">من تاريخ</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="form-control">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date_to" class="form-label">إلى تاريخ</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="form-control">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-group w-100">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        <i class="fas fa-search me-1"></i> بحث
                                    </button>
                                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-redo"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Customers Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">قائمة العملاء</h5>
            <span class="badge bg-primary">{{ $customers->total() }} عميل</span>
        </div>
        
        <div class="card-body p-0">
            @if($customers->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4 class="empty-state-title">لا يوجد عملاء متاحين</h4>
                    <p class="empty-state-description">لم يتم العثور على أي عملاء يطابقون معايير البحث</p>
                    <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> إضافة عميل جديد
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>الاسم</th>
                                <th>معلومات الاتصال</th>
                                <th>الطلبات</th>
                                <th>الحالة</th>
                                <th>تاريخ التسجيل</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $customer)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-lg bg-primary me-3">
                                                {{ substr($customer->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $customer->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="fas fa-envelope text-muted me-2"></i>
                                                {{ $customer->email }}
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-phone text-muted me-2"></i>
                                                {{ $customer->phone }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-medium">{{ $customer->orders_count }}</span> طلبات
                                    </td>
                                    <td>
                                        @if($customer->is_active)
                                            <span class="badge badge-delivered">
                                                <span class="badge-dot"></span> نشط
                                            </span>
                                        @else
                                            <span class="badge badge-cancelled">
                                                <span class="badge-dot"></span> غير نشط
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $customer->created_at->format('Y-m-d') }}</div>
                                        <small class="text-muted">{{ $customer->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.customers.show', $customer->id) }}" class="action-btn action-btn-info" data-bs-toggle="tooltip" title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.customers.edit', $customer->id) }}" class="action-btn action-btn-primary" data-bs-toggle="tooltip" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.customers.toggle-status', $customer->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="action-btn {{ $customer->is_active ? 'action-btn-warning' : 'action-btn-success' }}" data-bs-toggle="tooltip" title="{{ $customer->is_active ? 'تعطيل الحساب' : 'تفعيل الحساب' }}">
                                                    <i class="fas {{ $customer->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                                </button>
                                            </form>
                                            <button type="button" onclick="confirmDelete('{{ $customer->id }}')" class="action-btn action-btn-danger" data-bs-toggle="tooltip" title="حذف">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                        <form id="delete-form-{{ $customer->id }}" action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        {{ $customers->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function confirmDelete(id) {
        if (confirm('هل أنت متأكد من رغبتك في حذف هذا العميل؟')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endsection 