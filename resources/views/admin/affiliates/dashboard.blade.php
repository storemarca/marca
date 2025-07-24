@extends('layouts.admin')

@section('title', 'لوحة تحكم المسوقين بالعمولة')

@section('styles')
<style>
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
        margin-bottom: 20px;
    }
    
    .stat-card {
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">لوحة تحكم المسوقين بالعمولة</h1>
        <div>
            <a href="{{ route('admin.affiliates.withdrawal-requests') }}" class="btn btn-warning mr-2">
                <i class="fas fa-money-check-alt"></i> طلبات السحب
            </a>
            <a href="{{ route('admin.affiliates.index') }}" class="btn btn-primary">
                <i class="fas fa-users"></i> إدارة المسوقين
            </a>
        </div>
    </div>

    <!-- الإحصائيات -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">إجمالي المسوقين</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAffiliates }}</div>
                            <div class="mt-2 text-xs text-muted">
                                <span class="text-warning mr-2">
                                    <i class="fas fa-user-clock"></i> {{ $pendingAffiliates }} بانتظار الموافقة
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-primary opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">المسوقين النشطين</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeAffiliates }}</div>
                            <div class="mt-2 text-xs text-muted">
                                <span class="text-success mr-2">
                                    <i class="fas fa-percentage"></i> {{ $totalAffiliates > 0 ? round($activeAffiliates / $totalAffiliates * 100) : 0 }}% من إجمالي المسوقين
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-success opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">إجمالي العمولات</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalEarnings, 2) }} ر.س</div>
                            <div class="mt-2 text-xs text-muted">
                                <span class="text-info mr-2">
                                    <i class="fas fa-chart-line"></i> معدل التحويل: {{ $conversionRate }}%
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-info opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">طلبات السحب المعلقة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($pendingWithdrawals, 2) }} ر.س</div>
                            <div class="mt-2 text-xs text-muted">
                                <span class="text-success mr-2">
                                    <i class="fas fa-check-circle"></i> مدفوع: {{ number_format($paidWithdrawals, 2) }} ر.س
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-check-alt fa-2x text-warning opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الرسوم البيانية -->
    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">الأرباح الشهرية</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="earningsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">توزيع الإحالات</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="referralsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- أفضل المسوقين -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">أفضل المسوقين</h6>
                    <span class="badge bg-success">{{ $topAffiliates->count() }} مسوقين</span>
                </div>
                <div class="card-body">
                    @if($topAffiliates->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>المستخدم</th>
                                        <th>الرصيد</th>
                                        <th>إجمالي الأرباح</th>
                                        <th>نسبة العمولة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topAffiliates as $affiliate)
                                        <tr>
                                            <td>
                                                <div>{{ $affiliate->user->name }}</div>
                                                <small class="text-muted">{{ $affiliate->user->email }}</small>
                                            </td>
                                            <td>{{ number_format($affiliate->balance, 2) }} ر.س</td>
                                            <td>{{ number_format($affiliate->lifetime_earnings, 2) }} ر.س</td>
                                            <td>{{ $affiliate->commission_rate }}%</td>
                                            <td>
                                                <a href="{{ route('admin.affiliates.show', $affiliate) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">لا توجد بيانات متاحة</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- أفضل الروابط التسويقية -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">أفضل الروابط التسويقية</h6>
                    <span class="badge bg-info">{{ $topLinks->count() }} روابط</span>
                </div>
                <div class="card-body">
                    @if($topLinks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>الاسم</th>
                                        <th>المسوق</th>
                                        <th>النقرات</th>
                                        <th>التحويلات</th>
                                        <th>معدل التحويل</th>
                                        <th>الأرباح</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topLinks as $link)
                                        <tr>
                                            <td>{{ $link->name }}</td>
                                            <td>{{ $link->affiliate->user->name }}</td>
                                            <td>{{ $link->clicks }}</td>
                                            <td>{{ $link->conversions }}</td>
                                            <td>{{ $link->conversion_rate }}%</td>
                                            <td>{{ number_format($link->earnings, 2) }} ر.س</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">لا توجد بيانات متاحة</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- آخر المعاملات -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">آخر معاملات العمولة</h6>
                    <a href="#" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                </div>
                <div class="card-body">
                    @if($recentTransactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>المسوق</th>
                                        <th>النوع</th>
                                        <th>المبلغ</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <a href="{{ route('admin.affiliates.show', $transaction->affiliate) }}">
                                                    {{ $transaction->affiliate->user->name }}
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge {{ $transaction->type == 'earned' ? 'bg-success' : ($transaction->type == 'paid' ? 'bg-primary' : ($transaction->type == 'refunded' ? 'bg-danger' : 'bg-info')) }}">
                                                    {{ $transaction->type_text }}
                                                </span>
                                            </td>
                                            <td class="{{ $transaction->amount >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $transaction->formatted_amount }}
                                            </td>
                                            <td>
                                                <span class="badge {{ $transaction->status == 'completed' ? 'bg-success' : ($transaction->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                                    {{ $transaction->status_text }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">لا توجد بيانات متاحة</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- آخر طلبات السحب -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">آخر طلبات السحب</h6>
                    <a href="{{ route('admin.affiliates.withdrawal-requests') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                </div>
                <div class="card-body">
                    @if($recentWithdrawals->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>المسوق</th>
                                        <th>المبلغ</th>
                                        <th>طريقة الدفع</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentWithdrawals as $withdrawal)
                                        <tr>
                                            <td>{{ $withdrawal->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <a href="{{ route('admin.affiliates.show', $withdrawal->affiliate) }}">
                                                    {{ $withdrawal->affiliate->user->name }}
                                                </a>
                                            </td>
                                            <td>{{ number_format($withdrawal->amount, 2) }} ر.س</td>
                                            <td>{{ $withdrawal->payment_method }}</td>
                                            <td>
                                                <span class="badge {{ 
                                                    $withdrawal->status == 'paid' ? 'bg-success' : 
                                                    ($withdrawal->status == 'pending' ? 'bg-warning' : 
                                                    ($withdrawal->status == 'approved' ? 'bg-info' : 'bg-danger')) 
                                                }}">
                                                    {{ $withdrawal->status_text }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($withdrawal->status == 'pending')
                                                    <div class="btn-group">
                                                        <a href="{{ route('admin.affiliates.withdrawal-requests') }}" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">لا توجد بيانات متاحة</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- نصائح وتحليلات -->
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">تحليلات وتوصيات</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-primary h-100">
                                <div class="card-body">
                                    <h5 class="card-title">زيادة نسبة التحويل</h5>
                                    <p>معدل التحويل الحالي هو {{ $conversionRate }}%. يمكن تحسين هذا المعدل من خلال:</p>
                                    <ul>
                                        <li>تقديم مواد تسويقية أفضل للمسوقين</li>
                                        <li>تحسين صفحات هبوط المنتجات</li>
                                        <li>تقديم عروض خاصة للعملاء القادمين من روابط المسوقين</li>
                                    </ul>
                                    <button class="btn btn-sm btn-outline-primary mt-2" data-toggle="modal" data-target="#conversionModal">
                                        <i class="fas fa-chart-line mr-1"></i> تحليل مفصل
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-success h-100">
                                <div class="card-body">
                                    <h5 class="card-title">تحفيز المسوقين النشطين</h5>
                                    <p>لديك {{ $activeAffiliates }} مسوق نشط. يمكن زيادة نشاطهم من خلال:</p>
                                    <ul>
                                        <li>تقديم مكافآت للمسوقين الأكثر نشاطاً</li>
                                        <li>زيادة نسب العمولة للمسوقين ذوي الأداء العالي</li>
                                        <li>إرسال تحديثات منتظمة حول المنتجات الجديدة والعروض</li>
                                    </ul>
                                    <button class="btn btn-sm btn-outline-success mt-2" data-toggle="modal" data-target="#affiliatesModal">
                                        <i class="fas fa-users mr-1"></i> خطة التحفيز
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-info h-100">
                                <div class="card-body">
                                    <h5 class="card-title">توسيع شبكة المسوقين</h5>
                                    <p>لزيادة عدد المسوقين بالعمولة، يمكنك:</p>
                                    <ul>
                                        <li>الترويج لبرنامج المسوقين بالعمولة على وسائل التواصل الاجتماعي</li>
                                        <li>التواصل مع المؤثرين في مجال عملك</li>
                                        <li>تبسيط عملية التسجيل والموافقة على طلبات الانضمام</li>
                                    </ul>
                                    <button class="btn btn-sm btn-outline-info mt-2" data-toggle="modal" data-target="#growthModal">
                                        <i class="fas fa-expand-arrows-alt mr-1"></i> خطة النمو
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // رسم بياني للأرباح الشهرية
    const earningsCtx = document.getElementById('earningsChart').getContext('2d');
    new Chart(earningsCtx, {
        type: 'line',
        data: {
            labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
            datasets: [{
                label: 'الأرباح الشهرية',
                data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], // استبدل هذه القيم بالقيم الفعلية من قاعدة البيانات
                backgroundColor: 'rgba(78, 115, 223, 0.2)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointBorderColor: '#fff',
                pointHoverRadius: 5,
                pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                pointHitRadius: 10,
                pointBorderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 12
                    }
                },
                y: {
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value) {
                            return value + ' ر.س';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyColor: "#858796",
                    titleColor: '#6e707e',
                    titleMarginBottom: 10,
                    titleFont: {
                        size: 14
                    },
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    padding: 15,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' ر.س';
                        }
                    }
                }
            }
        }
    });
    
    // رسم بياني لتوزيع الإحالات
    const referralsCtx = document.getElementById('referralsChart').getContext('2d');
    new Chart(referralsCtx, {
        type: 'doughnut',
        data: {
            labels: ['محولة', 'معلقة', 'منتهية الصلاحية'],
            datasets: [{
                data: [60, 30, 10], // استبدل هذه القيم بالقيم الفعلية من قاعدة البيانات
                backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b'],
                hoverBackgroundColor: ['#17a673', '#dda20a', '#c72c2c'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            }
        }
    });
</script>
@endsection 