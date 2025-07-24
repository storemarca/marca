@extends('layouts.user')

@section('title', 'طلبات سحب الأرباح')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">طلبات سحب الأرباح</h1>
        <a href="{{ route('affiliate.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-right ml-1"></i> العودة للوحة التحكم
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">ملخص الرصيد</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h2 class="text-primary mb-0">{{ number_format($affiliate->balance, 2) }} ر.س</h2>
                        <p class="text-muted">الرصيد الحالي</p>
                    </div>
                    <div class="row">
                        <div class="col-6 text-center border-end">
                            <h4 class="text-success mb-0">{{ number_format($affiliate->lifetime_earnings, 2) }}</h4>
                            <p class="text-muted">إجمالي الأرباح</p>
                        </div>
                        <div class="col-6 text-center">
                            <h4 class="text-info mb-0">{{ number_format($totalWithdrawn, 2) }}</h4>
                            <p class="text-muted">إجمالي المسحوبات</p>
                        </div>
                    </div>
                    <hr>
                    @if($affiliate->balance >= 100)
                        <div class="d-grid">
                            <a href="{{ route('affiliate.withdrawal-form') }}" class="btn btn-primary">
                                <i class="fas fa-money-bill-wave me-1"></i> طلب سحب جديد
                            </a>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i> يجب أن يكون لديك رصيد 100 ر.س على الأقل لطلب السحب.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">معلومات الدفع</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">طريقة الدفع المفضلة</label>
                            <p>{{ $affiliate->payment_method }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">حالة الحساب</label>
                            <p>
                                <span class="badge {{ $affiliate->isApproved() ? 'bg-success' : ($affiliate->isPending() ? 'bg-warning' : 'bg-danger') }}">
                                    {{ $affiliate->status_text }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">تفاصيل الدفع</label>
                            <p>{{ $affiliate->payment_details }}</p>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editPaymentDetailsModal">
                            <i class="fas fa-edit me-1"></i> تعديل معلومات الدفع
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">طلبات السحب</h5>
            <div>
                <form action="{{ route('affiliate.withdrawals') }}" method="GET" class="d-flex">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">جميع الحالات</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>تمت الموافقة</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>تم الدفع</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                    </select>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            @if($withdrawals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>التاريخ</th>
                                <th>المبلغ</th>
                                <th>طريقة الدفع</th>
                                <th>الحالة</th>
                                <th>ملاحظات</th>
                                <th>تاريخ الدفع</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($withdrawals as $withdrawal)
                                <tr>
                                    <td>{{ $withdrawal->id }}</td>
                                    <td>{{ $withdrawal->created_at->format('Y-m-d') }}</td>
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
                                        @if($withdrawal->notes)
                                            <button type="button" class="btn btn-sm btn-link" data-bs-toggle="popover" data-bs-trigger="focus" title="ملاحظات" data-bs-content="{{ $withdrawal->notes }}">
                                                عرض الملاحظات
                                            </button>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($withdrawal->paid_at)
                                            {{ $withdrawal->paid_at->format('Y-m-d') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $withdrawals->appends(request()->query())->links() }}
                </div>
            @else
                <div class="p-4 text-center">
                    <div class="mb-3">
                        <i class="fas fa-money-check-alt fa-3x text-muted"></i>
                    </div>
                    <h5>لا توجد طلبات سحب</h5>
                    <p class="text-muted">لم تقم بإنشاء أي طلبات سحب بعد. يمكنك طلب سحب أرباحك عندما يصل رصيدك إلى 100 ر.س على الأقل.</p>
                </div>
            @endif
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">الأسئلة الشائعة</h5>
        </div>
        <div class="card-body">
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            كم من الوقت تستغرق معالجة طلب السحب؟
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            تتم مراجعة طلبات السحب خلال 1-3 أيام عمل. بعد الموافقة على الطلب، يتم تحويل المبلغ خلال 3-5 أيام عمل إضافية، اعتماداً على طريقة الدفع المختارة.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            ما هو الحد الأدنى لطلب السحب؟
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            الحد الأدنى لطلب سحب الأرباح هو 100 ر.س. يجب أن يكون لديك رصيد 100 ر.س على الأقل لتتمكن من إنشاء طلب سحب جديد.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            هل يمكنني تغيير طريقة الدفع الخاصة بي؟
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            نعم، يمكنك تغيير طريقة الدفع وتفاصيل الدفع الخاصة بك في أي وقت من خلال النقر على "تعديل معلومات الدفع" في صفحة طلبات السحب. سيتم استخدام المعلومات الجديدة لطلبات السحب المستقبلية.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            هل يمكنني إلغاء طلب السحب؟
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            يمكنك إلغاء طلب السحب فقط إذا كان لا يزال في حالة "قيد المراجعة". بمجرد الموافقة على الطلب أو رفضه، لا يمكن إلغاؤه. للإلغاء، يرجى التواصل مع فريق الدعم.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal تعديل معلومات الدفع -->
<div class="modal fade" id="editPaymentDetailsModal" tabindex="-1" aria-labelledby="editPaymentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('affiliate.update-payment-details') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="editPaymentDetailsModalLabel">تعديل معلومات الدفع</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">طريقة الدفع المفضلة <span class="text-danger">*</span></label>
                        <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                            <option value="" selected disabled>اختر طريقة الدفع</option>
                            <option value="bank_transfer" {{ $affiliate->payment_method == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                            <option value="paypal" {{ $affiliate->payment_method == 'paypal' ? 'selected' : '' }}>PayPal</option>
                            <option value="western_union" {{ $affiliate->payment_method == 'western_union' ? 'selected' : '' }}>Western Union</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="payment_details" class="form-label">تفاصيل الدفع <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('payment_details') is-invalid @enderror" id="payment_details" name="payment_details" rows="4" required>{{ $affiliate->payment_details }}</textarea>
                        <div class="form-text">أدخل تفاصيل الدفع الخاصة بك (مثال: رقم الحساب البنكي، بريد PayPal، إلخ)</div>
                        @error('payment_details')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تفعيل tooltips
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl)
        });
    });
</script>
@endsection 