@extends('layouts.admin')

@section('title', safe_trans('dashboard'))
@section('page-title', safe_trans('dashboard'))

@php
// إضافة حل مؤقت لمشكلة htmlspecialchars
function safe_htmlspecialchars($value) {
    if (is_array($value)) {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
    return $value;
}
@endphp

@section('actions')
<div class="btn-group">
    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-calendar-alt me-1"></i> {{ safe_trans('period') }}
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="#">{{ safe_trans('today') }}</a></li>
        <li><a class="dropdown-item" href="#">{{ safe_trans('this_week') }}</a></li>
        <li><a class="dropdown-item active" href="#">{{ safe_trans('this_month') }}</a></li>
        <li><a class="dropdown-item" href="#">{{ safe_trans('this_year') }}</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="#">{{ safe_trans('all_time') }}</a></li>
    </ul>
</div>
@endsection

@section('content')
<div class="row mb-4">
    <!-- المبيعات -->
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="stat-card h-100">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-primary-light rounded-circle me-3">
                    <i class="fas fa-shopping-cart fa-lg text-primary"></i>
                </div>
                <div>
                    <h6 class="stat-label mb-1">{{ safe_trans('total_orders') }}</h6>
                    <h2 class="stat-value mb-0">{{ number_format($stats['total_orders']) }}</h2>
                    <div class="small text-success mt-1 d-flex align-items-center">
                        <i class="fas fa-caret-up me-1"></i> 
                        <span>0% {{ safe_trans('since_last_month') }}</span>
                    </div>
                </div>
            </div>
            <hr class="my-3 opacity-25">
            <a href="{{ route('admin.orders.index') }}" class="text-decoration-none stretched-link d-flex align-items-center">
                <small class="text-primary">{{ safe_trans('view_details') }}</small>
                <i class="fas fa-arrow-right ms-1 small text-primary"></i>
            </a>
        </div>
    </div>
    
    <!-- المبيعات -->
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="stat-card h-100">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-success-light rounded-circle me-3">
                    <i class="fas fa-dollar-sign fa-lg text-success"></i>
                </div>
                <div>
                    <h6 class="stat-label mb-1">{{ safe_trans('total_sales') }}</h6>
                    <h2 class="stat-value mb-0">{{ number_format($stats['total_sales'], 2) }} {{ html_safe(safe_trans('currency_symbol')) }}</h2>
                    <div class="small text-success mt-1 d-flex align-items-center">
                        <i class="fas fa-caret-up me-1"></i>
                        <span>0% {{ safe_trans('since_last_month') }}</span>
                    </div>
                </div>
            </div>
            <hr class="my-3 opacity-25">
            <a href="{{ route('admin.reports.sales') }}" class="text-decoration-none stretched-link d-flex align-items-center">
                <small class="text-primary">{{ safe_trans('view_details') }}</small>
                <i class="fas fa-arrow-right ms-1 small text-primary"></i>
            </a>
        </div>
    </div>
    
    <!-- المنتجات -->
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="stat-card h-100">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-warning-light rounded-circle me-3">
                    <i class="fas fa-box fa-lg text-warning"></i>
                </div>
                <div>
                    <h6 class="stat-label mb-1">{{ safe_trans('total_products') }}</h6>
                    <h2 class="stat-value mb-0">{{ number_format($stats['total_products']) }}</h2>
                    <div class="small text-success mt-1 d-flex align-items-center">
                        <i class="fas fa-plus me-1"></i>
                        <span>{{ safe_trans('active_products') }}</span>
                    </div>
                </div>
            </div>
            <hr class="my-3 opacity-25">
            <a href="{{ route('admin.products.index') }}" class="text-decoration-none stretched-link d-flex align-items-center">
                <small class="text-primary">{{ safe_trans('view_details') }}</small>
                <i class="fas fa-arrow-right ms-1 small text-primary"></i>
            </a>
        </div>
    </div>
    
    <!-- التحصيلات -->
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="stat-card h-100">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-danger-light rounded-circle me-3">
                    <i class="fas fa-wallet fa-lg text-danger"></i>
                </div>
                <div>
                    <h6 class="stat-label mb-1">{{ safe_trans('pending_collections') }}</h6>
                    <h2 class="stat-value mb-0">{{ number_format($stats['pending_collections']) }}</h2>
                    <div class="small text-danger mt-1 d-flex align-items-center">
                        <i class="fas fa-exclamation-circle me-1"></i>
                        <span>{{ safe_trans('awaiting_payments') }}</span>
                    </div>
                </div>
            </div>
            <hr class="my-3 opacity-25">
            <a href="{{ route('admin.collections.index') }}" class="text-decoration-none stretched-link d-flex align-items-center">
                <small class="text-primary">{{ safe_trans('view_details') }}</small>
                <i class="fas fa-arrow-right ms-1 small text-primary"></i>
            </a>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-8 mb-4">
        <!-- رسم بياني للمبيعات -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{ safe_trans('sales_overview') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="salesChartDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ safe_trans('this_month') }}
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="salesChartDropdown">
                        <li><a class="dropdown-item" href="#">{{ safe_trans('this_week') }}</a></li>
                        <li><a class="dropdown-item active" href="#">{{ safe_trans('this_month') }}</a></li>
                        <li><a class="dropdown-item" href="#">{{ safe_trans('this_year') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="300"></canvas>
            </div>
        </div>

        <!-- أحدث الطلبات -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-shopping-cart me-2 text-primary"></i>
                    {{ safe_trans('latest_orders') }}
                </h5>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary">{{ safe_trans('view_all') }}</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ safe_trans('order_number') }}</th>
                            <th>{{ safe_trans('customer') }}</th>
                            <th>{{ safe_trans('amount') }}</th>
                            <th>{{ safe_trans('status') }}</th>
                            <th>{{ safe_trans('date') }}</th>
                            <th>{{ safe_trans('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($latestOrders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->customer->name ?? safe_trans('guest') }}</td>
                                <td>{{ number_format($order->total_amount, 2) }} {{ html_safe($order->currency_symbol) }}</td>
                                <td>
                                    @if($order->status == 'pending')
                                        <span class="badge bg-warning">{{ safe_trans('pending') }}</span>
                                    @elseif($order->status == 'processing')
                                        <span class="badge bg-info">{{ safe_trans('processing') }}</span>
                                    @elseif($order->status == 'shipped')
                                        <span class="badge bg-primary">{{ safe_trans('shipped') }}</span>
                                    @elseif($order->status == 'delivered')
                                        <span class="badge bg-success">{{ safe_trans('delivered') }}</span>
                                    @elseif($order->status == 'cancelled')
                                        <span class="badge bg-danger">{{ safe_trans('cancelled') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $order->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-shopping-cart fa-3x mb-3 text-muted opacity-25"></i>
                                        <p class="text-muted mb-0">{{ safe_trans('no_orders_yet') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- إحصائيات سريعة -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-chart-pie me-2 text-primary"></i>
                    {{ safe_trans('quick_stats') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="progress-stats mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            {{ safe_trans('completed_orders') }}
                        </span>
                        <span class="badge bg-success">0%</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 4px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 0%; border-radius: 4px;"></div>
                    </div>
                </div>

                <div class="progress-stats mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="d-flex align-items-center">
                            <i class="fas fa-clock text-warning me-2"></i>
                            {{ safe_trans('pending_orders') }}
                        </span>
                        <span class="badge bg-warning">0%</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 4px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 0%; border-radius: 4px;"></div>
                    </div>
                </div>

                <div class="progress-stats mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="d-flex align-items-center">
                            <i class="fas fa-times-circle text-danger me-2"></i>
                            {{ safe_trans('cancelled_orders') }}
                        </span>
                        <span class="badge bg-danger">0%</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 4px;">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 0%; border-radius: 4px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- منتجات منخفضة المخزون -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                    {{ safe_trans('low_stock_products') }}
                </h5>
                <a href="{{ route('admin.reports.inventory') }}" class="btn btn-sm btn-primary">{{ safe_trans('view_all') }}</a>
            </div>
            <div class="list-group list-group-flush">
                @forelse ($lowStockProducts as $stock)
                    <a href="{{ route('admin.products.show', $stock->product->id) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">{{ $stock->product->name }}</h6>
                            <small class="text-danger fw-bold">{{ $stock->quantity }} {{ safe_trans('items_left') }}</small>
                        </div>
                        <p class="mb-1">{{ safe_trans('sku') }}: {{ $stock->product->sku }}</p>
                        <small>{{ safe_trans('warehouse') }}: {{ $stock->warehouse->name }}</small>
                    </a>
                @empty
                    <div class="empty-state text-center py-5">
                        <i class="fas fa-box fa-3x mb-3 text-muted opacity-25"></i>
                        <p class="text-muted mb-0">{{ safe_trans('no_low_stock_products') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
        
        <!-- أحدث الشحنات -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-truck me-2 text-primary"></i>
                    {{ safe_trans('latest_shipments') }}
                </h5>
                <a href="{{ route('admin.shipments.index') }}" class="btn btn-sm btn-primary">{{ safe_trans('view_all') }}</a>
            </div>
            <div class="list-group list-group-flush">
                @forelse ($latestShipments as $shipment)
                    <a href="{{ route('admin.shipments.show', $shipment->id) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">{{ $shipment->tracking_number }}</h6>
                            <small>{{ $shipment->created_at->format('Y-m-d') }}</small>
                        </div>
                        <p class="mb-1">{{ safe_trans('order') }}: #{{ $shipment->order->order_number ?? 'N/A' }}</p>
                        <small>
                            @if($shipment->status == 'pending')
                                <span class="text-warning">{{ safe_trans('pending') }}</span>
                            @elseif($shipment->status == 'shipped')
                                <span class="text-primary">{{ safe_trans('shipped') }}</span>
                            @elseif($shipment->status == 'delivered')
                                <span class="text-success">{{ safe_trans('delivered') }}</span>
                            @else
                                <span class="text-secondary">{{ $shipment->status }}</span>
                            @endif
                        </small>
                    </a>
                @empty
                    <div class="empty-state text-center py-5">
                        <i class="fas fa-truck fa-3x mb-3 text-muted opacity-25"></i>
                        <p class="text-muted mb-0">{{ safe_trans('no_shipments_yet') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
        
        <!-- التحصيلات المعلقة -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-money-bill-wave me-2 text-primary"></i>
                    {{ safe_trans('pending_collections') }}
                </h5>
                <a href="{{ route('admin.collections.index') }}" class="btn btn-sm btn-primary">{{ safe_trans('view_all') }}</a>
            </div>
            <div class="list-group list-group-flush">
                @forelse ($pendingCollections as $collection)
                    <a href="{{ route('admin.collections.show', $collection->id) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">{{ $collection->reference_number ?? safe_trans('collection') . ' #' . $collection->id }}</h6>
                            <small class="text-danger fw-bold">{{ number_format($collection->amount, 2) }} {{ html_safe($collection->currency_symbol ?? safe_trans('currency_symbol')) }}</small>
                        </div>
                        <p class="mb-1">{{ safe_trans('shipment') }}: {{ $collection->shipment->tracking_number ?? 'N/A' }}</p>
                        <small>{{ $collection->created_at->format('Y-m-d') }}</small>
                    </a>
                @empty
                    <div class="empty-state text-center py-5">
                        <i class="fas fa-money-bill-wave fa-3x mb-3 text-muted opacity-25"></i>
                        <p class="text-muted mb-0">{{ safe_trans('no_pending_collections') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection 

@push('styles')
<style>
    .bg-primary-light {
        background-color: rgba(13, 110, 253, 0.1);
    }
    .bg-success-light {
        background-color: rgba(25, 135, 84, 0.1);
    }
    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.1);
    }
    .bg-danger-light {
        background-color: rgba(220, 53, 69, 0.1);
    }
    
    .text-primary {
        color: #0d6efd !important;
    }
    .text-success {
        color: #198754 !important;
    }
    .text-warning {
        color: #ffc107 !important;
    }
    .text-danger {
        color: #dc3545 !important;
    }
    
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
    }
    
    .stat-card {
        position: relative;
        padding: 1.5rem;
        border-radius: 0.5rem;
        background-color: #fff;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
    
    .stat-card:hover .stat-icon {
        transform: scale(1.1);
    }
    
    .stat-card:before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: rgba(0, 0, 0, 0.02);
        border-radius: 50%;
        transform: translate(30%, -30%);
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    /* RTL Support for cards */
    html[dir="rtl"] .me-3 {
        margin-right: 0 !important;
        margin-left: 1rem !important;
    }
    html[dir="rtl"] .ms-1 {
        margin-left: 0 !important;
        margin-right: 0.25rem !important;
    }
    
    .list-group-item {
        border-left: none;
        border-right: none;
        padding: 1rem;
    }
    
    .progress {
        overflow: hidden;
        background-color: #f0f0f0;
    }
    
    .card-header .card-title {
        font-size: 1rem;
        font-weight: 600;
    }
    
    .card-header .card-title i {
        opacity: 0.8;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // رسم بياني للمبيعات
        const salesChartCtx = document.getElementById('salesChart').getContext('2d');
        
        // تعيين اتجاه النص بناءً على لغة الواجهة
        const isRTL = document.documentElement.dir === 'rtl';
        Chart.defaults.font.family = 'Cairo, sans-serif';
        
        const salesChart = new Chart(salesChartCtx, {
            type: 'line',
            data: {
                labels: ['1', '5', '10', '15', '20', '25', '30'],
                datasets: [{
                    label: '{{ safe_trans("sales") }}',
                    data: [0, 0, 0, 0, 0, 0, 0],
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }, {
                    label: '{{ safe_trans("orders") }}',
                    data: [0, 0, 0, 0, 0, 0, 0],
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: isRTL ? 'right' : 'left',
                        align: 'start',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 15
                        }
                    },
                    tooltip: {
                        rtl: isRTL,
                        textDirection: isRTL ? 'rtl' : 'ltr',
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(0, 0, 0, 0.1)',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: true,
                        boxWidth: 10,
                        boxHeight: 10,
                        usePointStyle: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false,
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                size: 12
                            },
                            color: '#6c757d'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            },
                            color: '#6c757d'
                        }
                    }
                }
            }
        });
        
        // إضافة تأثيرات حركة للبطاقات
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach((card, index) => {
            // تأخير ظهور البطاقات بشكل متتالي
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100 * index);
        });
    });
</script>
@endpush 