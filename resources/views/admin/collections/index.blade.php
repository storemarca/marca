@extends('layouts.admin')

@section('title', 'تقرير التحصيلات')

@section('content')
<div class="container">
    <h1 class="mb-4">تقرير التحصيلات</h1>

    <div class="mb-3">
        <strong>إجمالي التحصيلات:</strong> {{ number_format($totalCollections, 2) }} ر.س
    </div>

    @if($orders->isEmpty())
        <div class="alert alert-warning">
            لا توجد بيانات مطابقة.
        </div>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>رقم الطلب</th>
                    <th>العميل</th>
                    <th>المبلغ</th>
                    <th>طريقة الدفع</th>
                    <th>تاريخ الإنشاء</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->customer->name ?? 'غير معروف' }}</td>
                        <td>{{ number_format($order->total_amount, 2) }} ر.س</td>
                        <td>{{ $order->payment_method }}</td>
                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
