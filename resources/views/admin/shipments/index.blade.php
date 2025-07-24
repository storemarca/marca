@extends('layouts.admin')

@section('title', 'إدارة الشحنات')

@section('content')
<div class="container-fluid fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة الشحنات</h1>
        <a href="{{ route('admin.shipments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> إنشاء شحنة جديدة
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card primary">
                <div class="stat-icon primary">
                    <i class="fas fa-shipping-fast fa-lg"></i>
                </div>
                <div class="stat-value">{{ $shipments->total() }}</div>
                <div class="stat-label">إجمالي الشحنات</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card success">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle fa-lg"></i>
                </div>
                <div class="stat-value">{{ $shipments->where('status', 'delivered')->count() }}</div>
                <div class="stat-label">تم التسليم</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card warning">
                <div class="stat-icon warning">
                    <i class="fas fa-clock fa-lg"></i>
                </div>
                <div class="stat-value">{{ $shipments->whereIn('status', ['processing', 'in_transit', 'pending'])->count() }}</div>
                <div class="stat-label">قيد المعالجة</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card danger">
                <div class="stat-icon danger">
                    <i class="fas fa-exclamation-triangle fa-lg"></i>
                </div>
                <div class="stat-value">{{ $shipments->where('status', 'failed')->count() }}</div>
                <div class="stat-label">فشل التسليم</div>
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
                <form action="{{ route('admin.shipments.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search" class="form-label">بحث</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control" placeholder="رقم التتبع، رقم الطلب...">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status" class="form-label">حالة الشحنة</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">جميع الحالات</option>
                                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                                    <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>في الطريق</option>
                                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>تم التسليم</option>
                                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فشل التسليم</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="shipping_company_id" class="form-label">شركة الشحن</label>
                                <select name="shipping_company_id" id="shipping_company_id" class="form-select">
                                    <option value="">جميع الشركات</option>
                                    @foreach($shippingCompanies as $company)
                                        <option value="{{ $company->id }}" {{ request('shipping_company_id') == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
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
                                    <a href="{{ route('admin.shipments.index') }}" class="btn btn-outline-secondary">
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
    
    <!-- Shipments Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">قائمة الشحنات</h5>
            <span class="badge bg-primary">{{ $shipments->total() }} شحنة</span>
        </div>
        
        <div class="card-body p-0">
            @if($shipments->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h4 class="empty-state-title">لا توجد شحنات متاحة</h4>
                    <p class="empty-state-description">لم يتم العثور على أي شحنات تطابق معايير البحث</p>
                    <a href="{{ route('admin.shipments.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> إنشاء شحنة جديدة
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>رقم التتبع</th>
                                <th>رقم الطلب</th>
                                <th>شركة الشحن</th>
                                <th>حالة الشحنة</th>
                                <th>تاريخ التسليم المتوقع</th>
                                <th>تاريخ الإنشاء</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($shipments as $shipment)
                                <tr>
                                    <td>
                                        @if($shipment->tracking_number)
                                            <span class="fw-medium font-monospace">{{ $shipment->tracking_number }}</span>
                                        @else
                                            <span class="text-muted fst-italic">غير متوفر</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $shipment->order_id) }}" class="text-primary fw-medium text-decoration-none">
                                            {{ $shipment->order->order_number ?? 'غير متوفر' }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($shipment->shippingCompany && $shipment->shippingCompany->logo)
                                                <img src="{{ asset('storage/' . $shipment->shippingCompany->logo) }}" alt="{{ $shipment->shippingCompany->name }}" class="me-2" style="height: 20px;">
                                            @endif
                                            {{ $shipment->shippingCompany->name ?? 'غير متوفر' }}
                                        </div>
                                    </td>
                                    <td>
                                        @switch($shipment->status)
                                            @case('pending')
                                                <span class="badge badge-pending">
                                                    <span class="badge-dot"></span> قيد الانتظار
                                                </span>
                                                @break
                                            @case('processing')
                                                <span class="badge badge-processing">
                                                    <span class="badge-dot"></span> قيد المعالجة
                                                </span>
                                                @break
                                            @case('in_transit')
                                                <span class="badge badge-shipped">
                                                    <span class="badge-dot"></span> في الطريق
                                                </span>
                                                @break
                                            @case('out_for_delivery')
                                                <span class="badge badge-shipped">
                                                    <span class="badge-dot"></span> خارج للتسليم
                                                </span>
                                                @break
                                            @case('delivered')
                                                <span class="badge badge-delivered">
                                                    <span class="badge-dot"></span> تم التسليم
                                                </span>
                                                @break
                                            @case('failed')
                                                <span class="badge badge-cancelled">
                                                    <span class="badge-dot"></span> فشل التسليم
                                                </span>
                                                @break
                                            @case('returned')
                                                <span class="badge badge-cancelled">
                                                    <span class="badge-dot"></span> تم الإرجاع
                                                </span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">
                                                    {{ $shipment->status }}
                                                </span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($shipment->expected_delivery_date)
                                            {{ \Carbon\Carbon::parse($shipment->expected_delivery_date)->format('Y-m-d') }}
                                        @else
                                            <span class="text-muted fst-italic">غير محدد</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $shipment->created_at->format('Y-m-d') }}</div>
                                        <small class="text-muted">{{ $shipment->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.shipments.show', $shipment->id) }}" class="action-btn action-btn-info" data-bs-toggle="tooltip" title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.shipments.edit', $shipment->id) }}" class="action-btn action-btn-primary" data-bs-toggle="tooltip" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" onclick="confirmDelete('{{ $shipment->id }}')" class="action-btn action-btn-danger" data-bs-toggle="tooltip" title="حذف">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                        <form id="delete-form-{{ $shipment->id }}" action="{{ route('admin.shipments.destroy', $shipment->id) }}" method="POST" class="d-none">
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
                        {{ $shipments->appends(request()->query())->links() }}
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
        if (confirm('هل أنت متأكد من رغبتك في حذف هذه الشحنة؟')) {
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