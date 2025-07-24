@extends('layouts.admin')

@section('title', 'تقرير المبيعات')

@section('content')
    <div class="container-fluid px-0">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>تقرير المبيعات
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            يعرض هذا التقرير تحليلاً مفصلاً للمبيعات في الفترة المحددة، بما في ذلك إجمالي المبيعات، وعدد الطلبات، والمنتجات الأكثر مبيعاً.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- إحصائيات سريعة -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm border-0 sales-stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="stat-icon bg-success-subtle text-success me-3">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">إجمالي المبيعات</h6>
                                <h2 class="mb-0">{{ number_format($totalSales, 2) }}</h2>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center small text-muted">
                                <span>الفترة</span>
                                <span class="fw-medium">{{ $startDate->format('Y-m-d') }} - {{ $endDate->format('Y-m-d') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm border-0 sales-stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="stat-icon bg-primary-subtle text-primary me-3">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">عدد الطلبات</h6>
                                <h2 class="mb-0">{{ number_format($totalOrders) }}</h2>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center small text-muted">
                                <span>الفترة</span>
                                <span class="fw-medium">{{ $startDate->format('Y-m-d') }} - {{ $endDate->format('Y-m-d') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm border-0 sales-stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="stat-icon bg-info-subtle text-info me-3">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">متوسط قيمة الطلب</h6>
                                <h2 class="mb-0">{{ number_format($averageOrderValue, 2) }}</h2>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center small text-muted">
                                <span>الفترة</span>
                                <span class="fw-medium">{{ $startDate->format('Y-m-d') }} - {{ $endDate->format('Y-m-d') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- فلاتر البحث -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-filter me-2"></i>تصفية النتائج
                        </h6>
                        <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="collapse show" id="filterCollapse">
                        <div class="card-body">
                            <form action="{{ route('admin.reports.sales') }}" method="GET">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="date_from" class="form-label">من تاريخ</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from', $startDate->format('Y-m-d')) }}">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <label for="date_to" class="form-label">إلى تاريخ</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to', $endDate->format('Y-m-d')) }}">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <label for="country_id" class="form-label">البلد</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                            <select class="form-select" id="country_id" name="country_id">
                                                <option value="">جميع البلدان</option>
                                                @foreach($countries as $country)
                                                    <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                                                        {{ $country->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3 mb-3 d-flex align-items-end">
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-search me-1"></i> تصفية
                                            </button>
                                            <a href="{{ route('admin.reports.sales') }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-times me-1"></i> إعادة تعيين
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- المنتجات الأكثر مبيعاً -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-star me-2"></i>المنتجات الأكثر مبيعاً
                        </h6>
                        <span class="badge bg-primary">أفضل 10 منتجات</span>
                    </div>
                    
                    <div class="card-body p-0">
                        @if($topProducts->isEmpty())
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <div class="empty-state-icon bg-warning-subtle text-warning mx-auto">
                                        <i class="fas fa-box"></i>
                                    </div>
                                </div>
                                <h5>لا توجد بيانات متاحة</h5>
                                <p class="text-muted">لم يتم العثور على أي مبيعات في الفترة المحددة</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0">المنتج</th>
                                            <th class="border-0">الكمية المباعة</th>
                                            <th class="border-0">إجمالي المبيعات</th>
                                            <th class="border-0">نسبة المبيعات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($topProducts as $product)
                                            <tr>
                                                <td>
                                                    <div class="fw-medium">{{ $product->product_name }}</div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary-subtle text-primary rounded-pill">{{ number_format($product->total_quantity) }}</span>
                                                </td>
                                                <td>
                                                    <div class="fw-medium">{{ number_format($product->total_sales, 2) }}</div>
                                                </td>
                                                <td>
                                                    @php
                                                        $percentage = $totalSales > 0 ? round(($product->total_sales / $totalSales) * 100, 1) : 0;
                                                    @endphp
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%"></div>
                                                        </div>
                                                        <span class="small">{{ $percentage }}%</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- المبيعات حسب التاريخ والبلد -->
        <div class="row mb-4">
            <div class="col-md-6 mb-4 mb-md-0">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-calendar-alt me-2"></i>المبيعات حسب التاريخ
                        </h6>
                    </div>
                    
                    <div class="card-body p-0">
                        @if($salesByDate->isEmpty())
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <div class="empty-state-icon bg-info-subtle text-info mx-auto">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                </div>
                                <h5>لا توجد بيانات متاحة</h5>
                                <p class="text-muted">لم يتم العثور على أي مبيعات في الفترة المحددة</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0">التاريخ</th>
                                            <th class="border-0">عدد الطلبات</th>
                                            <th class="border-0">إجمالي المبيعات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($salesByDate as $date => $data)
                                            <tr>
                                                <td>
                                                    <div class="fw-medium">{{ $date }}</div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary-subtle text-primary rounded-pill">{{ number_format($data['count']) }}</span>
                                                </td>
                                                <td>
                                                    <div class="fw-medium">{{ number_format($data['total'], 2) }}</div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-globe me-2"></i>المبيعات حسب البلد
                        </h6>
                    </div>
                    
                    <div class="card-body p-0">
                        @if($salesByCountry->isEmpty())
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <div class="empty-state-icon bg-info-subtle text-info mx-auto">
                                        <i class="fas fa-globe"></i>
                                    </div>
                                </div>
                                <h5>لا توجد بيانات متاحة</h5>
                                <p class="text-muted">لم يتم العثور على أي مبيعات في الفترة المحددة</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0">البلد</th>
                                            <th class="border-0">عدد الطلبات</th>
                                            <th class="border-0">إجمالي المبيعات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($salesByCountry as $country => $data)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="country-flag me-2">
                                                            <i class="fas fa-flag text-primary"></i>
                                                        </div>
                                                        <div class="fw-medium">{{ $country }}</div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary-subtle text-primary rounded-pill">{{ number_format($data['count']) }}</span>
                                                </td>
                                                <td>
                                                    <div class="fw-medium">{{ number_format($data['total'], 2) }}</div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .sales-stat-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .sales-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 1.5rem;
    }
    
    .empty-state-icon {
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 2rem;
    }
    
    .country-flag {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection 