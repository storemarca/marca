@extends('layouts.admin')

@section('title', 'تفاصيل طريقة الشحن')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">تفاصيل طريقة الشحن</h1>
        <div>
            <a href="{{ route('admin.shipping-methods.edit', $shippingMethod) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> تعديل
            </a>
            <a href="{{ route('admin.shipping-methods.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> العودة للقائمة
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات أساسية</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5 class="font-weight-bold">{{ $shippingMethod->name }}</h5>
                        <span class="badge {{ $shippingMethod->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $shippingMethod->is_active ? 'مفعل' : 'غير مفعل' }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <strong>الكود:</strong> {{ $shippingMethod->code }}
                    </div>

                    <div class="mb-3">
                        <strong>التكلفة الأساسية:</strong> {{ number_format($shippingMethod->base_cost, 2) }} ريال
                    </div>

                    @if($shippingMethod->description)
                        <div class="mb-3">
                            <strong>الوصف:</strong>
                            <p class="mt-1">{{ $shippingMethod->description }}</p>
                        </div>
                    @endif

                    <div class="mb-3">
                        <strong>حساب التكلفة حسب الوزن:</strong>
                        @if($shippingMethod->weight_based)
                            <span class="text-success">مفعل</span>
                            <p class="mt-1">تكلفة الكيلوجرام: {{ number_format($shippingMethod->cost_per_kg, 2) }} ريال</p>
                        @else
                            <span class="text-danger">غير مفعل</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <strong>حد الشحن المجاني:</strong>
                        @if($shippingMethod->free_shipping_threshold > 0)
                            {{ number_format($shippingMethod->free_shipping_threshold, 2) }} ريال
                        @else
                            <span class="text-muted">غير متاح</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">أسعار الشحن حسب الدولة</h6>
                </div>
                <div class="card-body">
                    @if($shippingMethod->countries->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>الدولة</th>
                                        <th>تكلفة الشحن</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($shippingMethod->countries as $country)
                                        <tr>
                                            <td>{{ $country->name }}</td>
                                            <td>{{ number_format($country->pivot->cost, 2) }} ريال</td>
                                            <td>
                                                <span class="badge {{ $country->pivot->is_available ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $country->pivot->is_available ? 'متاح' : 'غير متاح' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            لا توجد أسعار مخصصة للدول. سيتم استخدام التكلفة الأساسية ({{ number_format($shippingMethod->base_cost, 2) }} ريال) لجميع الدول.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">إحصائيات الاستخدام</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        عدد الطلبات
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $shippingMethod->orders->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
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