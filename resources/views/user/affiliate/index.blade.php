@extends('layouts.user')

@section('title', 'لوحة تحكم المسوق بالعمولة')

@section('styles')
<style>
    .stats-card {
        transition: all 0.3s ease;
    }
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .chart-container {
        position: relative;
        height: 250px;
        width: 100%;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <h1 class="mb-4">لوحة تحكم المسوق بالعمولة</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm h-100 stats-card border-primary border-start border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">الرصيد الحالي</h6>
                            <h2 class="text-primary mb-0">{{ number_format($affiliate->balance, 2) }} ر.س</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-wallet fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    @if($affiliate->isApproved() && $affiliate->balance >= 100)
                        <a href="{{ route('affiliate.withdrawal-form') }}" class="btn btn-sm btn-primary">طلب سحب الأرباح</a>
                    @elseif($affiliate->isApproved())
                        <small class="text-muted">
                            يجب أن يكون رصيدك 100 ر.س على الأقل لطلب السحب
                        </small>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100 stats-card border-success border-start border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">إجمالي الأرباح</h6>
                            <h3 class="text-success mb-0">{{ number_format($affiliate->lifetime_earnings, 2) }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-coins fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="{{ route('affiliate.transactions') }}" class="text-decoration-none">عرض جميع المعاملات <i class="fas fa-arrow-left"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100 stats-card border-info border-start border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">إجمالي النقرات</h6>
                            <h3 class="text-info mb-0">{{ $links->sum('clicks') }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-mouse-pointer fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="{{ route('affiliate.links') }}" class="text-decoration-none">إدارة الروابط <i class="fas fa-arrow-left"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100 stats-card border-warning border-start border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">معدل التحويل</h6>
                            <h3 class="text-warning mb-0">{{ $links->sum('clicks') > 0 ? number_format($links->sum('conversions') / $links->sum('clicks') * 100, 2) : '0.00' }}%</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-chart-line fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="{{ route('affiliate.referrals') }}" class="text-decoration-none">إدارة الإحالات <i class="fas fa-arrow-left"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">معلومات الحساب</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>الحالة:</strong> 
                        <span class="badge {{ $affiliate->isApproved() ? 'bg-success' : ($affiliate->isPending() ? 'bg-warning' : 'bg-danger') }}">
                            {{ $affiliate->status_text }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>رمز الإحالة:</strong> 
                        <div class="input-group mt-1">
                            <input type="text" class="form-control" value="{{ $affiliate->code }}" id="affiliateCode" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ $affiliate->code }}', 'affiliateCode')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <strong>رابط الإحالة:</strong> 
                        <div class="input-group mt-1">
                            <input type="text" class="form-control" value="{{ route('affiliate.referral', $affiliate->code) }}" id="referralLink" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ route('affiliate.referral', $affiliate->code) }}', 'referralLink')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <strong>نسبة العمولة:</strong> {{ $affiliate->commission_rate }}%
                    </div>
                    <div class="mb-3">
                        <strong>تاريخ الانضمام:</strong> {{ $affiliate->approved_at ? $affiliate->approved_at->format('Y-m-d') : 'لم يتم الموافقة بعد' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">الأرباح الشهرية</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="earningsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">آخر المعاملات</h5>
                    <a href="{{ route('affiliate.transactions') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                </div>
                <div class="card-body">
                    @if($transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>النوع</th>
                                        <th>المبلغ</th>
                                        <th>الرصيد</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                                            <td>{{ $transaction->type_text }}</td>
                                            <td class="{{ $transaction->amount >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $transaction->formatted_amount }}
                                            </td>
                                            <td>{{ number_format($transaction->balance_after, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">لا توجد معاملات حتى الآن.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">آخر الإحالات</h5>
                    <a href="{{ route('affiliate.referrals') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                </div>
                <div class="card-body">
                    @if($referrals->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>المستخدم</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($referrals as $referral)
                                        <tr>
                                            <td>{{ $referral->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                @if($referral->referred_user)
                                                    {{ $referral->referred_user->name }}
                                                @else
                                                    <span class="text-muted">غير مسجل</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $referral->isConverted() ? 'bg-success' : ($referral->isExpired() ? 'bg-danger' : 'bg-warning') }}">
                                                    {{ $referral->status_text }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">لا توجد إحالات حتى الآن.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">أداء الروابط التسويقية</h5>
                    <a href="{{ route('affiliate.links') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                </div>
                <div class="card-body">
                    @if($links->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>الاسم</th>
                                        <th>النوع</th>
                                        <th>النقرات</th>
                                        <th>التحويلات</th>
                                        <th>الأرباح</th>
                                        <th>معدل التحويل</th>
                                        <th>الرابط</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($links as $link)
                                        <tr>
                                            <td>{{ $link->name }}</td>
                                            <td>{{ $link->target_type_text }}</td>
                                            <td>{{ $link->clicks }}</td>
                                            <td>{{ $link->conversions }}</td>
                                            <td>{{ number_format($link->earnings, 2) }} ر.س</td>
                                            <td>{{ $link->conversion_rate }}%</td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="text" class="form-control" value="{{ route('affiliate.track', $link->slug) }}" readonly>
                                                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ route('affiliate.track', $link->slug) }}')">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">لا توجد روابط تسويقية حتى الآن.</div>
                        @if($affiliate->isApproved())
                            <a href="{{ route('affiliate.links') }}" class="btn btn-primary">إنشاء رابط تسويقي</a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($pendingWithdrawals->count() > 0)
    <div class="container py-4">
        <div class="alert alert-info">
            <h5>طلبات السحب المعلقة</h5>
            <p>لديك {{ $pendingWithdrawals->count() }} طلب سحب معلق بإجمالي {{ number_format($pendingWithdrawals->sum('amount'), 2) }} ر.س</p>
            <a href="{{ route('affiliate.withdrawals') }}" class="btn btn-sm btn-primary">عرض التفاصيل</a>
        </div>
    </div>
@endif
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // نسخ النص إلى الحافظة
        function copyToClipboard(text, elementId = null) {
            const el = document.createElement('textarea');
            el.value = text;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
            
            if (elementId) {
                const inputElement = document.getElementById(elementId);
                if (inputElement) {
                    inputElement.classList.add('bg-light');
                    setTimeout(() => {
                        inputElement.classList.remove('bg-light');
                    }, 1000);
                }
            }
            
            // إظهار إشعار بنجاح النسخ
            const toast = document.createElement('div');
            toast.className = 'position-fixed top-0 end-0 p-3';
            toast.style.zIndex = '5000';
            toast.innerHTML = `
                <div class="toast show align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-check-circle me-2"></i> تم نسخ النص بنجاح
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 3000);
        }

        window.copyToClipboard = copyToClipboard;

        // رسم الرسم البياني للأرباح الشهرية
        const ctx = document.getElementById('earningsChart').getContext('2d');
        
        // بيانات الأرباح الشهرية - يمكن استبدالها بالبيانات الفعلية من الخادم
        const monthlyEarningsData = {
            labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
            datasets: [{
                label: 'الأرباح الشهرية (ر.س)',
                data: [
                    {{ json_encode([
                        'january' => 0,
                        'february' => 0,
                        'march' => 0,
                        'april' => 0,
                        'may' => 0,
                        'june' => 0,
                        'july' => 0,
                        'august' => 0,
                        'september' => 0,
                        'october' => 0,
                        'november' => 0,
                        'december' => 0,
                    ]) }}
                ],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        };

        new Chart(ctx, {
            type: 'line',
            data: monthlyEarningsData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
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
                        callbacks: {
                            label: function(context) {
                                return context.raw + ' ر.س';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection 