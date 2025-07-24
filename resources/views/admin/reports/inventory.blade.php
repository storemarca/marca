@extends('layouts.admin')

@section('title', 'تقرير المخزون')
@section('page-title', 'تقرير المخزون')

@section('breadcrumbs')
<li class="breadcrumb-item active">تقارير المخزون</li>
@endsection

@section('content')
    <!-- إحصائيات سريعة -->
    <div class="row mb-4">
        <div class="col-md-4 mb-4">
            <div class="stat-card primary h-100">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-primary-light rounded-circle me-3">
                        <i class="fas fa-box-open fa-lg text-primary"></i>
                    </div>
                    <div>
                        <h6 class="stat-label mb-1">إجمالي المخزون</h6>
                        <h2 class="stat-value mb-0">{{ number_format($totalStock) }}</h2>
                        <div class="small text-success mt-1 d-flex align-items-center">
                            <span>إجمالي المنتجات: {{ $stocks->total() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="stat-card warning h-100">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-warning-light rounded-circle me-3">
                        <i class="fas fa-exclamation-triangle fa-lg text-warning"></i>
                    </div>
                    <div>
                        <h6 class="stat-label mb-1">منتجات منخفضة المخزون</h6>
                        <h2 class="stat-value mb-0">{{ number_format($lowStockCount) }}</h2>
                        <div class="small mt-1 d-flex align-items-center">
                            <span>نسبة من الإجمالي: {{ $stocks->total() > 0 ? number_format(($lowStockCount / $stocks->total()) * 100, 1) : 0 }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="stat-card danger h-100">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-danger-light rounded-circle me-3">
                        <i class="fas fa-times fa-lg text-danger"></i>
                    </div>
                    <div>
                        <h6 class="stat-label mb-1">منتجات نفذت من المخزون</h6>
                        <h2 class="stat-value mb-0">{{ number_format($outOfStockCount) }}</h2>
                        <div class="small mt-1 d-flex align-items-center">
                            <span>نسبة من الإجمالي: {{ $stocks->total() > 0 ? number_format(($outOfStockCount / $stocks->total()) * 100, 1) : 0 }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- فلاتر البحث -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2 text-primary"></i>
                تصفية المخزون
            </h5>
            <div>
                <a href="{{ route('admin.reports.inventory') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-times me-1"></i> إعادة تعيين الفلاتر
                </a>
            </div>
        </div>
        <div class="card-body">
            <form id="filter-form" action="{{ route('admin.reports.inventory') }}" method="GET">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="form-group">
                            <label for="product_id" class="form-label">المنتج</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-box"></i></span>
                                <select name="product_id" id="product_id" class="form-select auto-submit">
                                    <option value="">جميع المنتجات</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="form-group">
                            <label for="warehouse_id" class="form-label">المخزن</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-warehouse"></i></span>
                                <select name="warehouse_id" id="warehouse_id" class="form-select auto-submit">
                                    <option value="">جميع المخازن</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="form-group">
                            <label for="quantity_filter" class="form-label">تصفية حسب الكمية</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-filter"></i></span>
                                <select name="quantity_filter" id="quantity_filter" class="form-select auto-submit">
                                    <option value="">الكل</option>
                                    <option value="low" {{ request('quantity_filter') == 'low' ? 'selected' : '' }}>منخفض المخزون (≤ 5)</option>
                                    <option value="out" {{ request('quantity_filter') == 'out' ? 'selected' : '' }}>نفذ من المخزون (0)</option>
                                    <option value="available" {{ request('quantity_filter') == 'available' ? 'selected' : '' }}>متوفر (> 0)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- جدول المخزون -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-boxes me-2 text-primary"></i>
                حالة المخزون
            </h5>
            <span class="badge bg-primary">عدد المنتجات: {{ $stocks->total() }}</span>
        </div>
        
        <div class="card-body">
            @if($stocks->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-box-open fa-3x mb-3 text-muted opacity-25"></i>
                    <h4>لا توجد بيانات متاحة</h4>
                    <p class="text-muted mb-0">لم يتم العثور على أي منتجات تطابق معايير البحث</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th scope="col">المنتج</th>
                                <th scope="col">SKU</th>
                                <th scope="col">المخزن</th>
                                <th scope="col">الكمية المتاحة</th>
                                <th scope="col">الحالة</th>
                                <th scope="col">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stocks as $stock)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($stock->product && $stock->product->main_image)
                                                <img class="rounded-3 me-3" src="{{ asset('storage/' . $stock->product->main_image) }}" alt="{{ $stock->product->name }}" width="40" height="40" style="object-fit: cover;">
                                            @else
                                                <div class="rounded-3 me-3 bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-box text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $stock->product->name ?? 'غير متوفر' }}</h6>
                                                <small class="text-muted">{{ $stock->product->category->name ?? 'بدون تصنيف' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $stock->product->sku ?? 'غير متوفر' }}</span>
                                    </td>
                                    <td>
                                        <div>{{ $stock->warehouse->name ?? 'غير متوفر' }}</div>
                                        <small class="text-muted">{{ $stock->warehouse->address ?? '' }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="fw-bold">{{ number_format($stock->quantity) }}</span>
                                            @if($stock->quantity <= 5 && $stock->quantity > 0)
                                                <span class="ms-2 text-warning" title="منخفض المخزون">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                </span>
                                            @elseif($stock->quantity <= 0)
                                                <span class="ms-2 text-danger" title="نفذ من المخزون">
                                                    <i class="fas fa-times-circle"></i>
                                                </span>
                                            @endif
                                        </div>
                                        
                                        @if($stock->quantity > 0)
                                            <div class="progress mt-2" style="height: 5px;">
                                                @php
                                                    $percentage = min(100, ($stock->quantity / 20) * 100);
                                                    $barClass = $stock->quantity <= 5 ? 'bg-warning' : 'bg-success';
                                                @endphp
                                                <div class="progress-bar {{ $barClass }}" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($stock->quantity <= 0)
                                            <span class="badge bg-danger">نفذ من المخزون</span>
                                        @elseif($stock->quantity <= 5)
                                            <span class="badge bg-warning">منخفض المخزون</span>
                                        @else
                                            <span class="badge bg-success">متوفر</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.products.show', $stock->product->slug) }}" class="btn btn-sm btn-outline-primary" title="عرض المنتج">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.products.stock', $stock->product->slug) }}" class="btn btn-sm btn-outline-success" title="تعديل المخزون">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.reports.stock-movements', ['product_id' => $stock->product_id]) }}">
                                                        <i class="fas fa-history me-2 text-primary"></i>
                                                        سجل حركة المخزون
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.products.edit', $stock->product->slug) }}">
                                                        <i class="fas fa-pencil-alt me-2 text-success"></i>
                                                        تعديل المنتج
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); if(confirm('هل أنت متأكد من حذف هذا المنتج؟')) document.getElementById('delete-product-{{ $stock->product_id }}').submit();">
                                                        <i class="fas fa-trash-alt me-2 text-danger"></i>
                                                        حذف المنتج
                                                    </a>
                                                    <form id="delete-product-{{ $stock->product_id }}" action="{{ route('admin.products.destroy', $stock->product->slug) }}" method="POST" class="d-none">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        
        @if($stocks->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-center">
                    {{ $stocks->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تفعيل التصفية التلقائية عند تغيير أي عنصر من الفلتر
        const autoSubmitElements = document.querySelectorAll('.auto-submit');
        autoSubmitElements.forEach(element => {
            element.addEventListener('change', function() {
                // إظهار مؤشر التحميل
                const loadingOverlay = document.createElement('div');
                loadingOverlay.classList.add('position-fixed', 'top-0', 'start-0', 'w-100', 'h-100', 'd-flex', 'justify-content-center', 'align-items-center');
                loadingOverlay.style.backgroundColor = 'rgba(0, 0, 0, 0.2)';
                loadingOverlay.style.zIndex = '9999';
                loadingOverlay.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">جاري التحميل...</span></div>';
                document.body.appendChild(loadingOverlay);
                
                // تقديم النموذج
                document.getElementById('filter-form').submit();
            });
        });
        
        // تفعيل التنسيق المحسن للقوائم المنسدلة
        const selects = document.querySelectorAll('.form-select');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                this.classList.add('border-primary');
            });
        });
    });
</script>
@endsection 