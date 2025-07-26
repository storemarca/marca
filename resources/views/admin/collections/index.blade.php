@extends('layouts.admin')

@section('title', 'تقرير التحصيلات')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">تقرير التحصيلات</h4>

    <form method="GET" action="{{ route('admin.reports.collections') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="date_range" class="form-label">نطاق التاريخ</label>
            <select name="date_range" id="date_range" class="form-select">
                <option value="">-- اختر --</option>
                <option value="today">اليوم</option>
                <option value="yesterday">أمس</option>
                <option value="this_week">هذا الأسبوع</option>
                <option value="last_week">الأسبوع الماضي</option>
                <option value="this_month">هذا الشهر</option>
                <option value="last_month">الشهر الماضي</option>
                <option value="custom">مخصص</option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="start_date" class="form-label">من تاريخ</label>
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>

        <div class="col-md-3">
            <label for="end_date" class="form-label">إلى تاريخ</label>
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>

        <div class="col-md-3">
            <label for="payment_method" class="form-label">طريقة الدفع</label>
            <select name="payment_method" class="form-select">
                <option value="">-- الكل --</option>
                <option value="cash">نقدًا</option>
                <option value="card">بطاقة</option>
                <option value="bank">تحويل بنكي</option>
            </select>
        </div>

        <div class="col-12 text-end">
            <button type="submit" class="btn btn-primary">تصفية</button>
        </div>
    </form>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h5 class="card-title">إجمالي التحصيلات</h5>
                    <p class="h4 text-success">{{ number_format($totalCollections, 2) }} ﷼</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h5 class="card-title">عدد الطلبات</h5>
                    <p class="h4">{{ $orders->total() }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($orders->isEmpty())
        <div class="alert alert-info text-center">
            لا توجد بيانات مطابقة للفلاتر المحددة.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>العميل</th>
                        <th>المبلغ</th>
                        <th>طريقة الدفع</th>
                        <th>الحالة</th>
                        <th>تاريخ الطلب</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->customer->name ?? '-' }}</td>
                            <td>{{ number_format($order->total_amount, 2) }} ﷼</td>
                            <td>{{ ucfirst($order->payment_method) }}</td>
                            <td>
                                @if($order->status == 'completed')
                                    <span class="badge bg-success">مكتمل</span>
                                @elseif($order->status == 'pending')
                                    <span class="badge bg-warning text-dark">قيد المعالجة</span>
                                @else
                                    <span class="badge bg-secondary">آخر</span>
                                @endif
                            </td>
                            <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $orders->withQueryString()->links() }}
    @endif
</div>
@endsection
