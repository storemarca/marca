@extends('layouts.user')

@section('title', 'حساب المسوق بالعمولة معلق')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">برنامج المسوقين بالعمولة</h1>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">حساب المسوق بالعمولة معلق</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-ban text-warning fa-5x mb-3"></i>
                        <h4 class="mb-3">تم تعليق حسابك في برنامج المسوقين بالعمولة</h4>
                    </div>

                    <div class="alert alert-warning">
                        <h5 class="alert-heading">سبب التعليق:</h5>
                        <p>{{ $affiliate->rejection_reason ?: 'لم يتم تحديد سبب للتعليق.' }}</p>
                    </div>

                    <div class="mt-4">
                        <p>يمكنك التواصل مع فريق الدعم للاستفسار عن سبب تعليق حسابك أو للحصول على مزيد من المعلومات حول كيفية إعادة تفعيل حسابك.</p>
                        
                        <div class="text-center mt-4">
                            <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> العودة للوحة التحكم
                            </a>
                            <a href="{{ route('user.contact') }}" class="btn btn-primary">
                                <i class="fas fa-envelope"></i> التواصل مع الدعم
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 