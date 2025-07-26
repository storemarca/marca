@extends('layouts.admin')

@section('title', 'إدارة التحصيلات')

@section('content')
<div class="container-fluid fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 d-flex align-items-center">
            <i class="fas fa-money-bill-wave text-primary me-3"></i>
            إدارة التحصيلات
        </h1>
        
        <div>
            <a href="{{ route('admin.collections.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> إضافة تحصيل
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card primary">
                <div class="stat-icon primary">
                    <i class="fas fa-money-bill-wave fa-lg"></i>
                </div>
                <div class="stat-value">{{ $totalCollections }}</div>
                <div class="stat-label">إجمالي التحصيلات</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card warning">
                <div class="stat-icon warning">
                    <i class="fas fa-clock fa-lg"></i>
                </div>
                <div class="stat-value">{{ $pendingCollections }}</div>
                <div class="stat-label">قيد الانتظار</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card success">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle fa-lg"></i>
                </div>
                <div class="stat-value">{{ number_format($collectedAmount, 2) }}</div>
                <div class="stat-label">مبلغ محصل</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card info">
                <div class="stat-icon info">
                    <i class="fas fa-hourglass-half fa-lg"></i>
                </div>
                <div class="stat-value">{{ number_format($pendingAmount, 2) }}</div>
                <div class="stat-label">مبلغ معلق</div>
            </div>
        </div>
    </div>

    <!-- Collections Table -->
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                قائمة التحصيلات
                <span class="badge bg-primary rounded-pill">{{ $collections->total() }} تحصيل</span>
            </h5>
        </div>
        
        <div class="card-body">
            @if($collections->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">لا توجد تحصيلات</h5>
                    <p class="text-muted">لم يتم إنشاء أي تحصيلات بعد</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>رقم التحصيل</th>
                                <th>رقم الطلب</th>
                                <th>العميل</th>
                                <th>المبلغ</th>
                                <th>الحالة</th>
                                <th>تاريخ الإنشاء</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($collections as $collection)
                                <tr>
                                    <td>
                                        <span class="fw-bold">#{{ $collection->id }}</span>
                                    </td>
                                    <td>
                                        @if($collection->order)
                                            <a href="{{ route('admin.orders.show', $collection->order->id) }}" class="text-decoration-none">
                                                #{{ $collection->order->order_number ?? $collection->order->id }}
                                            </a>
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($collection->order && $collection->order->customer)
                                            {{ $collection->order->customer->name }}
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">{{ number_format($collection->amount, 2) }} ر.س</span>
                                    </td>
                                    <td>
                                        @if($collection->status == 'pending')
                                            <span class="badge bg-warning">قيد الانتظار</span>
                                        @elseif($collection->status == 'collected')
                                            <span class="badge bg-success">محصل</span>
                                        @elseif($collection->status == 'settled')
                                            <span class="badge bg-info">مسدد</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $collection->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $collection->created_at->format('Y-m-d H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.collections.show', $collection->id) }}" class="btn btn-outline-primary" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.collections.edit', $collection->id) }}" class="btn btn-outline-warning" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($collection->status == 'pending')
                                                <form action="{{ route('admin.collections.mark-collected', $collection->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success" title="تحديد كمحصل" onclick="return confirm('هل أنت متأكد؟')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $collections->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
