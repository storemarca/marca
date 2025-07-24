@extends('layouts.user')

@section('title', 'خطأ في الإعدادات')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">خطأ في إعدادات النظام</h4>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="mb-3">لا توجد دول نشطة في النظام</h5>
                    <p>لا يمكن عرض المتجر حاليًا بسبب عدم وجود دول نشطة في النظام.</p>
                    <p>يرجى التواصل مع إدارة الموقع لحل هذه المشكلة.</p>
                    
                    @auth('admin')
                        <div class="mt-4">
                            <a href="{{ route('admin.countries.index') }}" class="btn btn-primary">
                                إدارة الدول
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 