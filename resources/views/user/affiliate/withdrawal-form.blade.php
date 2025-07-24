@extends('layouts.user')

@section('title', 'طلب سحب الأرباح')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">طلب سحب الأرباح</h1>
        <a href="{{ route('affiliate.withdrawals') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-right ml-1"></i> العودة لطلبات السحب
        </a>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">نموذج طلب سحب الأرباح</h5>
                </div>
                <div class="card-body">
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

                    @if($affiliate->balance < 100)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            عذراً، يجب أن يكون لديك رصيد 100 ر.س على الأقل لطلب السحب. رصيدك الحالي هو {{ number_format($affiliate->balance, 2) }} ر.س.
                        </div>
                    @else
                        <form action="{{ route('affiliate.request-withdrawal') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="amount" class="form-label">المبلغ المراد سحبه <span class="text-danger">*</span></label>
                                    <span class="badge bg-primary">الرصيد المتاح: {{ number_format($affiliate->balance, 2) }} ر.س</span>
                                </div>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" min="100" max="{{ $affiliate->balance }}" step="0.01" value="{{ old('amount', 100) }}" required>
                                    <span class="input-group-text">ر.س</span>
                                </div>
                                <div class="form-text">الحد الأدنى للسحب هو 100 ر.س</div>
                                @error('amount')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="payment_method" class="form-label">طريقة الدفع <span class="text-danger">*</span></label>
                                <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                                    <option value="" selected disabled>اختر طريقة الدفع</option>
                                    <option value="bank_transfer" {{ old('payment_method', $affiliate->payment_method) == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                                    <option value="paypal" {{ old('payment_method', $affiliate->payment_method) == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                    <option value="western_union" {{ old('payment_method', $affiliate->payment_method) == 'western_union' ? 'selected' : '' }}>Western Union</option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="payment_details" class="form-label">تفاصيل الدفع <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('payment_details') is-invalid @enderror" id="payment_details" name="payment_details" rows="4" required>{{ old('payment_details', $affiliate->payment_details) }}</textarea>
                                <div class="form-text">أدخل تفاصيل الدفع الخاصة بك (مثال: رقم الحساب البنكي، بريد PayPal، إلخ)</div>
                                @error('payment_details')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="notes" class="form-label">ملاحظات (اختياري)</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                <div class="form-text">أي ملاحظات إضافية تود إضافتها لطلب السحب</div>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-info">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i class="fas fa-info-circle fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5 class="alert-heading">معلومات هامة</h5>
                                        <p class="mb-0">تتم مراجعة طلبات السحب خلال 1-3 أيام عمل. بعد الموافقة على الطلب، يتم تحويل المبلغ خلال 3-5 أيام عمل إضافية، اعتماداً على طريقة الدفع المختارة.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input @error('terms') is-invalid @enderror" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">أوافق على <a href="#" data-bs-toggle="modal" data-bs-target="#withdrawalTermsModal">شروط وأحكام</a> سحب الأرباح</label>
                                @error('terms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">تقديم طلب السحب</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal للشروط والأحكام -->
<div class="modal fade" id="withdrawalTermsModal" tabindex="-1" aria-labelledby="withdrawalTermsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="withdrawalTermsModalLabel">شروط وأحكام سحب الأرباح</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5>1. طلبات السحب</h5>
                <ul>
                    <li>الحد الأدنى لطلب سحب الأرباح هو 100 ر.س.</li>
                    <li>يمكن تقديم طلب سحب واحد فقط في الشهر.</li>
                    <li>تتم مراجعة طلبات السحب خلال 1-3 أيام عمل.</li>
                    <li>بعد الموافقة على الطلب، يتم تحويل المبلغ خلال 3-5 أيام عمل إضافية.</li>
                </ul>

                <h5>2. الرسوم والضرائب</h5>
                <ul>
                    <li>قد يتم خصم رسوم التحويل من المبلغ المسحوب، اعتماداً على طريقة الدفع المختارة.</li>
                    <li>المسوق بالعمولة مسؤول عن دفع أي ضرائب مستحقة على الأرباح المكتسبة.</li>
                </ul>

                <h5>3. معلومات الدفع</h5>
                <ul>
                    <li>يجب التأكد من صحة معلومات الدفع المقدمة.</li>
                    <li>لن نكون مسؤولين عن أي تأخير أو فشل في الدفع بسبب معلومات دفع غير صحيحة.</li>
                </ul>

                <h5>4. إلغاء الطلب</h5>
                <ul>
                    <li>يمكن إلغاء طلب السحب فقط إذا كان لا يزال في حالة "قيد المراجعة".</li>
                    <li>بمجرد الموافقة على الطلب، لا يمكن إلغاؤه.</li>
                </ul>

                <h5>5. الحسابات المعلقة أو المحظورة</h5>
                <ul>
                    <li>لن يتم معالجة طلبات السحب للحسابات المعلقة أو المحظورة.</li>
                    <li>نحتفظ بالحق في تعليق أو رفض طلبات السحب في حالة الاشتباه في نشاط احتيالي.</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const amountInput = document.getElementById('amount');
        const maxAmount = {{ $affiliate->balance }};
        
        // تحديد الحد الأقصى للمبلغ
        amountInput.addEventListener('change', function() {
            if (this.value > maxAmount) {
                this.value = maxAmount;
            }
            if (this.value < 100) {
                this.value = 100;
            }
        });
        
        // زر سحب كامل الرصيد
        document.getElementById('withdrawAll').addEventListener('click', function() {
            amountInput.value = maxAmount;
        });
    });
</script>
@endsection 