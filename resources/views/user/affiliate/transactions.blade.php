@extends('layouts.user')

@section('title', 'معاملات العمولة')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">معاملات العمولة</h1>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">ملخص الأرباح</h5>
                    <div class="mb-3">
                        <strong>الرصيد الحالي:</strong> 
                        <span class="text-success h4">{{ number_format($affiliate->balance, 2) }} ر.س</span>
                    </div>
                    <div class="mb-3">
                        <strong>إجمالي الأرباح:</strong> {{ number_format($affiliate->lifetime_earnings, 2) }} ر.س
                    </div>
                    @if($affiliate->isApproved() && $affiliate->balance >= 100)
                        <a href="{{ route('affiliate.withdrawal-form') }}" class="btn btn-primary">طلب سحب الأرباح</a>
                    @elseif($affiliate->isApproved())
                        <div class="alert alert-info">
                            يجب أن يكون لديك رصيد 100 ر.س على الأقل لطلب السحب.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">تصفية المعاملات</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('affiliate.transactions') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="type" class="form-label">نوع المعاملة</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">الكل</option>
                                <option value="earned" {{ request('type') == 'earned' ? 'selected' : '' }}>مكتسبة</option>
                                <option value="paid" {{ request('type') == 'paid' ? 'selected' : '' }}>مدفوعة</option>
                                <option value="refunded" {{ request('type') == 'refunded' ? 'selected' : '' }}>مستردة</option>
                                <option value="adjusted" {{ request('type') == 'adjusted' ? 'selected' : '' }}>معدلة</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="date_from" class="form-label">من تاريخ</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="date_to" class="form-label">إلى تاريخ</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">تصفية</button>
                            <a href="{{ route('affiliate.transactions') }}" class="btn btn-secondary">إعادة تعيين</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">سجل المعاملات</h5>
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
                                        <th>الرصيد بعد</th>
                                        <th>الحالة</th>
                                        <th>ملاحظات</th>
                                        <th>مرتبط بـ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <span class="badge {{ $transaction->type == 'earned' ? 'bg-success' : ($transaction->type == 'paid' ? 'bg-primary' : ($transaction->type == 'refunded' ? 'bg-danger' : 'bg-info')) }}">
                                                    {{ $transaction->type_text }}
                                                </span>
                                            </td>
                                            <td class="{{ $transaction->amount >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $transaction->formatted_amount }}
                                            </td>
                                            <td>{{ number_format($transaction->balance_after, 2) }} ر.س</td>
                                            <td>
                                                <span class="badge {{ $transaction->status == 'completed' ? 'bg-success' : ($transaction->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                                    {{ $transaction->status_text }}
                                                </span>
                                            </td>
                                            <td>{{ $transaction->notes ?? '-' }}</td>
                                            <td>
                                                @if($transaction->order)
                                                    <a href="{{ route('user.orders.show', $transaction->order) }}" class="btn btn-sm btn-outline-primary">
                                                        الطلب #{{ $transaction->order->order_number }}
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $transactions->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            لا توجد معاملات تطابق معايير البحث.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">معلومات مهمة</h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="infoAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                    كيف يتم احتساب العمولات؟
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#infoAccordion">
                                <div class="accordion-body">
                                    <p>يتم احتساب العمولات كنسبة مئوية من قيمة الطلب الإجمالية. نسبة العمولة الخاصة بك هي {{ $affiliate->commission_rate }}%.</p>
                                    <p>مثال: إذا تم شراء منتج بقيمة 1000 ر.س من خلال رابط الإحالة الخاص بك، فستحصل على {{ $affiliate->commission_rate * 10 }} ر.س كعمولة.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    متى يتم إضافة العمولات إلى رصيدي؟
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#infoAccordion">
                                <div class="accordion-body">
                                    <p>يتم إضافة العمولات إلى رصيدك فور اكتمال الطلب وتأكيد الدفع. في حالة استرداد الطلب، سيتم خصم العمولة من رصيدك.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    كيف يمكنني سحب أرباحي؟
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#infoAccordion">
                                <div class="accordion-body">
                                    <p>يمكنك طلب سحب أرباحك عندما يصل رصيدك إلى 100 ر.س على الأقل. يتم معالجة طلبات السحب خلال 3-5 أيام عمل.</p>
                                    <p>لطلب سحب الأرباح، انتقل إلى <a href="{{ route('affiliate.withdrawal-form') }}">صفحة طلب السحب</a>.</p>
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