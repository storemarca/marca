@extends('layouts.admin')

@section('title', 'إدارة الطلبات')

@section('content')
<div class="container-fluid fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 d-flex align-items-center">
            <i class="fas fa-shopping-cart text-primary me-3"></i>
            إدارة الطلبات
        </h1>
        
        <div>
            <a href="{{ route('admin.sales') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-line me-1"></i> إحصائيات المبيعات
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card primary">
                <div class="stat-icon primary">
                    <i class="fas fa-shopping-cart fa-lg"></i>
                </div>
                <div class="stat-value">{{ $orders->total() }}</div>
                <div class="stat-label">إجمالي الطلبات</div>
                <div class="progress mt-2" style="height: 5px">
                    <div class="progress-bar bg-primary" style="width: 100%"></div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card success">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle fa-lg"></i>
                </div>
                <div class="stat-value">{{ $orders->where('status', 'delivered')->count() }}</div>
                <div class="stat-label">تم التسليم</div>
                <div class="progress mt-2" style="height: 5px">
                    <div class="progress-bar bg-success" style="width: {{ ($orders->where('status', 'delivered')->count() / max(1, $orders->total())) * 100 }}%"></div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card warning">
                <div class="stat-icon warning">
                    <i class="fas fa-clock fa-lg"></i>
                </div>
                <div class="stat-value">{{ $orders->whereIn('status', ['processing', 'pending'])->count() }}</div>
                <div class="stat-label">قيد المعالجة</div>
                <div class="progress mt-2" style="height: 5px">
                    <div class="progress-bar bg-warning" style="width: {{ ($orders->whereIn('status', ['processing', 'pending'])->count() / max(1, $orders->total())) * 100 }}%"></div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card danger">
                <div class="stat-icon danger">
                    <i class="fas fa-ban fa-lg"></i>
                </div>
                <div class="stat-value">{{ $orders->where('status', 'cancelled')->count() }}</div>
                <div class="stat-label">ملغية</div>
                <div class="progress mt-2" style="height: 5px">
                    <div class="progress-bar bg-danger" style="width: {{ ($orders->where('status', 'cancelled')->count() / max(1, $orders->total())) * 100 }}%"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Search Form -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="fas fa-search text-primary me-2"></i>
                بحث متقدم
            </h5>
            <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="collapse" data-bs-target="#searchCollapse" aria-expanded="false" aria-controls="searchCollapse">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        <div class="collapse show" id="searchCollapse">
            <div class="card-body">
                <form action="{{ route('admin.orders.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search" class="form-label text-dark fw-medium">بحث</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-search text-primary"></i></span>
                                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control" placeholder="رقم الطلب، اسم العميل...">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status" class="form-label text-dark fw-medium">حالة الطلب</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">جميع الحالات</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ html_safe(__('orders.status.pending')) }}</option>
                                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>{{ html_safe(__('orders.status.processing')) }}</option>
                                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>{{ html_safe(__('orders.status.shipped')) }}</option>
                                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>{{ html_safe(__('orders.status.delivered')) }}</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ html_safe(__('orders.status.cancelled')) }}</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="payment_status" class="form-label text-dark fw-medium">حالة الدفع</label>
                                <select name="payment_status" id="payment_status" class="form-select">
                                    <option value="">جميع الحالات</option>
                                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>{{ html_safe(__('orders.payment_status.pending')) }}</option>
                                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>{{ html_safe(__('orders.payment_status.paid')) }}</option>
                                    <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>{{ html_safe(__('orders.payment_status.failed')) }}</option>
                                    <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>{{ html_safe(__('orders.payment_status.refunded')) }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date_from" class="form-label text-dark fw-medium">من تاريخ</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-calendar-alt text-primary"></i></span>
                                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="form-control">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date_to" class="form-label text-dark fw-medium">إلى تاريخ</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-calendar-alt text-primary"></i></span>
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
                                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
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
    
    <!-- Orders Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="fas fa-list text-primary me-2"></i>
                قائمة الطلبات
            </h5>
            <span class="badge bg-primary rounded-pill">{{ $orders->total() }} طلب</span>
        </div>
        
        <div class="card-body p-0">
            @if($orders->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h4 class="empty-state-title">لا توجد طلبات متاحة</h4>
                    <p class="empty-state-description">لم يتم العثور على أي طلبات تطابق معايير البحث</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>رقم الطلب</th>
                                <th>العميل</th>
                                <th>المبلغ الإجمالي</th>
                                <th>حالة الطلب</th>
                                <th>حالة الدفع</th>
                                <th>تاريخ الطلب</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>
                                        <span class="fw-medium font-monospace text-primary">{{ $order->order_number }}</span>
                                        @if ($order->tracking_number)
                                            <div class="mt-1">
                                                <span class="badge bg-light text-dark border">
                                                    <i class="fas fa-truck text-secondary me-1"></i> {{ $order->tracking_number }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-2">
                                                {{ substr($order->customer ? $order->customer->name : $order->shipping_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $order->customer ? $order->customer->name : $order->shipping_name }}</div>
                                                <div class="small text-muted">{{ $order->customer ? $order->customer->email : $order->shipping_email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ number_format($order->grand_total, 2) }}</div>
                                        <div class="small text-muted">{{ $order->items_count ?? 0 }} منتجات</div>
                                    </td>
                                    <td>
                                        @switch($order->status)
                                            @case('pending')
                                                <span class="badge badge-pending">
                                                    <span class="badge-dot"></span> {{ html_safe(__('orders.status.pending')) }}
                                                </span>
                                                @break
                                            @case('processing')
                                                <span class="badge badge-processing">
                                                    <span class="badge-dot"></span> {{ html_safe(__('orders.status.processing')) }}
                                                </span>
                                                @break
                                            @case('shipped')
                                                <span class="badge badge-shipped">
                                                    <span class="badge-dot"></span> {{ html_safe(__('orders.status.shipped')) }}
                                                </span>
                                                @break
                                            @case('delivered')
                                                <span class="badge badge-delivered">
                                                    <span class="badge-dot"></span> {{ html_safe(__('orders.status.delivered')) }}
                                                </span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge badge-cancelled">
                                                    <span class="badge-dot"></span> {{ html_safe(__('orders.status.cancelled')) }}
                                                </span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">
                                                    {{ html_safe($order->status) }}
                                                </span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @switch($order->payment_status)
                                            @case('pending')
                                                <span class="badge badge-pending">
                                                    <span class="badge-dot"></span> {{ html_safe(__('orders.payment_status.pending')) }}
                                                </span>
                                                @break
                                            @case('paid')
                                                <span class="badge badge-delivered">
                                                    <span class="badge-dot"></span> {{ html_safe(__('orders.payment_status.paid')) }}
                                                </span>
                                                @break
                                            @case('failed')
                                                <span class="badge badge-cancelled">
                                                    <span class="badge-dot"></span> {{ html_safe(__('orders.payment_status.failed')) }}
                                                </span>
                                                @break
                                            @case('refunded')
                                                <span class="badge bg-info">
                                                    <span class="badge-dot"></span> {{ html_safe(__('orders.payment_status.refunded')) }}
                                                </span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">
                                                    {{ html_safe($order->payment_status) }}
                                                </span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $order->created_at->format('Y-m-d') }}</div>
                                        <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="action-btn action-btn-info" data-bs-toggle="tooltip" title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.orders.invoice', $order->id) }}" class="action-btn action-btn-primary" data-bs-toggle="tooltip" title="عرض الفاتورة">
                                                <i class="fas fa-file-invoice"></i>
                                            </a>
                                            <div class="dropdown">
                                                <button class="action-btn action-btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="المزيد من الإجراءات">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.orders.invoice.pdf', $order->id) }}">
                                                            <i class="fas fa-file-pdf me-2 text-danger"></i> تحميل PDF
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.shipments.create', ['order_id' => $order->id]) }}">
                                                            <i class="fas fa-truck me-2 text-primary"></i> إنشاء شحنة
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" onclick="confirmDelete('{{ $order->id }}'); return false;">
                                                            <i class="fas fa-trash-alt me-2"></i> حذف الطلب
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="p-3 border-top">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">تأكيد الحذف</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من حذف هذا الطلب؟ هذا الإجراء لا يمكن التراجع عنه.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">تأكيد الحذف</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function confirmDelete(orderId) {
        document.getElementById('deleteForm').action = `{{ url('admin/orders') }}/${orderId}`;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
    
    // تفعيل tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush 