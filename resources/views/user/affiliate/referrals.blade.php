@extends('layouts.user')

@section('title', 'إدارة الإحالات')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">إدارة الإحالات</h1>
        <a href="{{ route('affiliate.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-right ml-1"></i> العودة للوحة التحكم
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">ملخص الإحالات</h5>
                    <div class="row mt-4">
                        <div class="col-6 text-center border-end">
                            <h3 class="text-primary mb-0">{{ $totalReferrals }}</h3>
                            <p class="text-muted">إجمالي الإحالات</p>
                        </div>
                        <div class="col-6 text-center">
                            <h3 class="text-success mb-0">{{ $convertedReferrals }}</h3>
                            <p class="text-muted">إحالات محولة</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6 text-center border-end">
                            <h3 class="text-warning mb-0">{{ $pendingReferrals }}</h3>
                            <p class="text-muted">إحالات معلقة</p>
                        </div>
                        <div class="col-6 text-center">
                            <h3 class="text-danger mb-0">{{ $expiredReferrals }}</h3>
                            <p class="text-muted">إحالات منتهية</p>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <h5>معدل التحويل</h5>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $conversionRate }}%;" aria-valuenow="{{ $conversionRate }}" aria-valuemin="0" aria-valuemax="100">{{ $conversionRate }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">رابط الإحالة الخاص بك</h5>
                </div>
                <div class="card-body">
                    <p>شارك هذا الرابط مع أصدقائك وعائلتك ومتابعيك للحصول على عمولة عن كل عملية شراء تتم من خلاله.</p>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" value="{{ route('affiliate.refer', ['code' => $affiliate->code]) }}" id="referralLink" readonly>
                        <button class="btn btn-primary" type="button" onclick="copyReferralLink()">
                            <i class="fas fa-copy"></i> نسخ الرابط
                        </button>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <p class="mb-2">مشاركة عبر:</p>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('affiliate.refer', ['code' => $affiliate->code])) }}" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                <i class="fab fa-facebook-f"></i> فيسبوك
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('affiliate.refer', ['code' => $affiliate->code])) }}&text={{ urlencode('تسوق معي في ' . config('app.name') . ' واستمتع بتجربة تسوق رائعة!') }}" target="_blank" class="btn btn-sm btn-outline-info me-2">
                                <i class="fab fa-twitter"></i> تويتر
                            </a>
                            <a href="https://api.whatsapp.com/send?text={{ urlencode('تسوق معي في ' . config('app.name') . ' واستمتع بتجربة تسوق رائعة! ' . route('affiliate.refer', ['code' => $affiliate->code])) }}" target="_blank" class="btn btn-sm btn-outline-success me-2">
                                <i class="fab fa-whatsapp"></i> واتساب
                            </a>
                            <a href="mailto:?subject={{ urlencode('دعوة للتسوق في ' . config('app.name')) }}&body={{ urlencode('مرحباً،\n\nأدعوك للتسوق في ' . config('app.name') . ' والاستمتاع بتجربة تسوق رائعة!\n\n' . route('affiliate.refer', ['code' => $affiliate->code])) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-envelope"></i> بريد إلكتروني
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">قائمة الإحالات</h5>
            <div>
                <form action="{{ route('affiliate.referrals') }}" method="GET" class="d-flex">
                    <select name="status" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                        <option value="">جميع الحالات</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                        <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>محولة</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منتهية</option>
                    </select>
                    <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>الأحدث أولاً</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>الأقدم أولاً</option>
                    </select>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            @if($referrals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>التاريخ</th>
                                <th>المستخدم</th>
                                <th>الحالة</th>
                                <th>تاريخ التحويل</th>
                                <th>مبلغ العمولة</th>
                                <th>تفاصيل</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($referrals as $referral)
                                <tr>
                                    <td>{{ $referral->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @if($referral->referred_user)
                                            {{ $referral->referred_user->name }}
                                            <br>
                                            <small class="text-muted">{{ $referral->referred_user->email }}</small>
                                        @else
                                            <span class="text-muted">غير مسجل</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $referral->isConverted() ? 'bg-success' : ($referral->isExpired() ? 'bg-danger' : 'bg-warning') }}">
                                            {{ $referral->status_text }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($referral->converted_at)
                                            {{ $referral->converted_at->format('Y-m-d H:i') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($referral->commission_amount > 0)
                                            <span class="text-success">{{ number_format($referral->commission_amount, 2) }} ر.س</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#referralDetails{{ $referral->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <!-- Modal -->
                                        <div class="modal fade" id="referralDetails{{ $referral->id }}" tabindex="-1" aria-labelledby="referralDetailsLabel{{ $referral->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="referralDetailsLabel{{ $referral->id }}">تفاصيل الإحالة</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <strong>معرف الإحالة:</strong> #{{ $referral->id }}
                                                        </div>
                                                        <div class="mb-3">
                                                            <strong>تاريخ الإنشاء:</strong> {{ $referral->created_at->format('Y-m-d H:i:s') }}
                                                        </div>
                                                        <div class="mb-3">
                                                            <strong>الحالة:</strong>
                                                            <span class="badge {{ $referral->isConverted() ? 'bg-success' : ($referral->isExpired() ? 'bg-danger' : 'bg-warning') }}">
                                                                {{ $referral->status_text }}
                                                            </span>
                                                        </div>
                                                        @if($referral->referred_user)
                                                            <div class="mb-3">
                                                                <strong>المستخدم المُحال:</strong> {{ $referral->referred_user->name }}
                                                            </div>
                                                            <div class="mb-3">
                                                                <strong>البريد الإلكتروني:</strong> {{ $referral->referred_user->email }}
                                                            </div>
                                                            <div class="mb-3">
                                                                <strong>تاريخ التسجيل:</strong> {{ $referral->referred_user->created_at->format('Y-m-d H:i:s') }}
                                                            </div>
                                                        @endif
                                                        @if($referral->isConverted())
                                                            <div class="mb-3">
                                                                <strong>تاريخ التحويل:</strong> {{ $referral->converted_at->format('Y-m-d H:i:s') }}
                                                            </div>
                                                            <div class="mb-3">
                                                                <strong>رقم الطلب:</strong>
                                                                @if($referral->order)
                                                                    <a href="{{ route('user.orders.show', $referral->order) }}" target="_blank">
                                                                        #{{ $referral->order->order_number }}
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted">غير متاح</span>
                                                                @endif
                                                            </div>
                                                            <div class="mb-3">
                                                                <strong>مبلغ العمولة:</strong> <span class="text-success">{{ number_format($referral->commission_amount, 2) }} ر.س</span>
                                                            </div>
                                                        @elseif($referral->isExpired())
                                                            <div class="mb-3">
                                                                <strong>تاريخ الانتهاء:</strong> {{ $referral->expires_at->format('Y-m-d H:i:s') }}
                                                            </div>
                                                        @else
                                                            <div class="mb-3">
                                                                <strong>تاريخ الانتهاء:</strong> {{ $referral->expires_at->format('Y-m-d H:i:s') }}
                                                            </div>
                                                            <div class="alert alert-info">
                                                                <i class="fas fa-info-circle me-2"></i> هذه الإحالة لا تزال نشطة. سيتم تحويلها عند قيام المستخدم المُحال بإتمام عملية شراء.
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $referrals->appends(request()->query())->links() }}
                </div>
            @else
                <div class="p-4 text-center">
                    <div class="mb-3">
                        <i class="fas fa-users fa-3x text-muted"></i>
                    </div>
                    <h5>لا توجد إحالات بعد</h5>
                    <p class="text-muted">شارك رابط الإحالة الخاص بك مع الأصدقاء والعائلة والمتابعين للبدء في كسب العمولات.</p>
                </div>
            @endif
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">نصائح لزيادة الإحالات</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-bullhorn fa-2x text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5>استخدم وسائل التواصل الاجتماعي</h5>
                            <p>شارك رابط الإحالة الخاص بك على منصات التواصل الاجتماعي مثل فيسبوك وتويتر وإنستغرام للوصول إلى جمهور أوسع.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-envelope fa-2x text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5>أرسل رسائل شخصية</h5>
                            <p>أرسل رسائل شخصية إلى أصدقائك وعائلتك لدعوتهم للتسوق عبر رابط الإحالة الخاص بك. الرسائل الشخصية أكثر فعالية من المنشورات العامة.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-lightbulb fa-2x text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5>اشرح الفوائد</h5>
                            <p>اشرح للأشخاص الذين تحيلهم فوائد التسوق من {{ config('app.name') }} مثل جودة المنتجات والأسعار التنافسية وخدمة العملاء الممتازة.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function copyReferralLink() {
        const copyText = document.getElementById("referralLink");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
        
        // Show copied message
        const button = document.querySelector('button[onclick="copyReferralLink()"]');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i> تم النسخ';
        
        setTimeout(function() {
            button.innerHTML = originalText;
        }, 2000);
    }
</script>
@endsection 