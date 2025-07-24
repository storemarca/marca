@extends('layouts.admin')

@section('title', 'سجل حركات المخزون')
@section('page-title', 'سجل حركات المخزون')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.reports.inventory') }}">تقارير المخزون</a></li>
<li class="breadcrumb-item active">سجل حركات المخزون</li>
@endsection

@section('content')
    <!-- فلاتر البحث -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2 text-primary"></i>
                تصفية حركات المخزون
            </h5>
            <div>
                <a href="{{ route('admin.reports.stock-movements') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-times me-1"></i> إعادة تعيين الفلاتر
                </a>
            </div>
        </div>
        <div class="card-body">
            <form id="filter-form" action="{{ route('admin.reports.stock-movements') }}" method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3">
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
                    
                    <div class="col-md-3 mb-3">
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
                    
                    <div class="col-md-3 mb-3">
                        <div class="form-group">
                            <label for="operation" class="form-label">نوع الحركة</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-exchange-alt"></i></span>
                                <select name="operation" id="operation" class="form-select auto-submit">
                                    <option value="">جميع الأنواع</option>
                                    <option value="add" {{ request('operation') == 'add' ? 'selected' : '' }}>إضافة</option>
                                    <option value="subtract" {{ request('operation') == 'subtract' ? 'selected' : '' }}>خصم</option>
                                    <option value="set" {{ request('operation') == 'set' ? 'selected' : '' }}>تعديل</option>
                                    <option value="transfer" {{ request('operation') == 'transfer' ? 'selected' : '' }}>نقل</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="form-group">
                            <label for="date_range" class="form-label">الفترة الزمنية</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <select name="date_range" id="date_range" class="form-select auto-submit">
                                    <option value="">كل الفترات</option>
                                    <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>اليوم</option>
                                    <option value="yesterday" {{ request('date_range') == 'yesterday' ? 'selected' : '' }}>الأمس</option>
                                    <option value="this_week" {{ request('date_range') == 'this_week' ? 'selected' : '' }}>هذا الأسبوع</option>
                                    <option value="last_week" {{ request('date_range') == 'last_week' ? 'selected' : '' }}>الأسبوع الماضي</option>
                                    <option value="this_month" {{ request('date_range') == 'this_month' ? 'selected' : '' }}>هذا الشهر</option>
                                    <option value="last_month" {{ request('date_range') == 'last_month' ? 'selected' : '' }}>الشهر الماضي</option>
                                    <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>فترة مخصصة</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-2" id="custom-date-range" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                    <div class="col-md-3 mb-3">
                        <div class="form-group">
                            <label for="start_date" class="form-label">من تاريخ</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <input type="date" name="start_date" id="start_date" class="form-control auto-submit" value="{{ request('start_date') }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="form-group">
                            <label for="end_date" class="form-label">إلى تاريخ</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <input type="date" name="end_date" id="end_date" class="form-control auto-submit" value="{{ request('end_date') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- جدول حركات المخزون -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-history me-2 text-primary"></i>
                سجل حركات المخزون
            </h5>
            <span class="badge bg-primary">عدد الحركات: {{ $movements->total() }}</span>
        </div>
        
        <div class="card-body">
            @if($movements->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-history fa-3x mb-3 text-muted opacity-25"></i>
                    <h4>لا توجد بيانات متاحة</h4>
                    <p class="text-muted mb-0">لم يتم العثور على أي حركات مخزون تطابق معايير البحث</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th scope="col">التاريخ</th>
                                <th scope="col">المنتج</th>
                                <th scope="col">المخزن</th>
                                <th scope="col">نوع الحركة</th>
                                <th scope="col">الكمية قبل</th>
                                <th scope="col">التغيير</th>
                                <th scope="col">الكمية بعد</th>
                                <th scope="col">المستخدم</th>
                                <th scope="col">السبب</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($movements as $movement)
                                <tr>
                                    <td>
                                        <div>{{ $movement->created_at->format('Y-m-d') }}</div>
                                        <small class="text-muted">{{ $movement->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($movement->product && $movement->product->main_image)
                                                <img class="rounded-3 me-3" src="{{ asset('storage/' . $movement->product->main_image) }}" alt="{{ $movement->product->name }}" width="40" height="40" style="object-fit: cover;">
                                            @else
                                                <div class="rounded-3 me-3 bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-box text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $movement->product->name ?? 'غير متوفر' }}</h6>
                                                <small class="text-muted">{{ $movement->product->sku ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $movement->warehouse->name ?? 'غير متوفر' }}
                                    </td>
                                    <td>
                                        @if($movement->operation == 'add')
                                            <span class="badge bg-success">إضافة</span>
                                        @elseif($movement->operation == 'subtract')
                                            <span class="badge bg-danger">خصم</span>
                                        @elseif($movement->operation == 'set')
                                            <span class="badge bg-warning">تعديل</span>
                                        @elseif($movement->operation == 'transfer')
                                            <span class="badge bg-info">نقل</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $movement->operation }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ number_format($movement->old_quantity) }}
                                    </td>
                                    <td>
                                        <span class="{{ $movement->quantity_change > 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                            {{ $movement->quantity_change > 0 ? '+' : '' }}{{ number_format($movement->quantity_change) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ number_format($movement->new_quantity) }}
                                    </td>
                                    <td>
                                        @if($movement->user)
                                            <span class="badge bg-info">
                                                <i class="fas fa-user me-1"></i> {{ $movement->user->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">النظام</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $movement->reason ?? '-' }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        
        @if($movements->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-center">
                    {{ $movements->withQueryString()->links() }}
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
        
        // إظهار/إخفاء حقول التاريخ المخصص
        const dateRangeSelect = document.getElementById('date_range');
        const customDateRange = document.getElementById('custom-date-range');
        
        if (dateRangeSelect) {
            dateRangeSelect.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customDateRange.style.display = '';
                } else {
                    customDateRange.style.display = 'none';
                }
            });
        }
        
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