@extends('layouts.admin')

@section('title', 'الملف الشخصي')
@section('page-title', 'الملف الشخصي')

@section('breadcrumbs')
    <li class="breadcrumb-item active">الملف الشخصي</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">معلومات المستخدم</h5>
                </div>
                <div class="card-body text-center">
                    <div class="avatar-circle mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;">
                        <span class="avatar-initials">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                    <h5 class="mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    
                    <div class="d-flex justify-content-center mb-2">
                        @foreach($user->roles as $role)
                            <span class="badge bg-info me-1">{{ $role->name }}</span>
                        @endforeach
                    </div>
                    
                    <hr>
                    
                    <div class="text-start">
                        <p class="mb-1"><strong>تاريخ الإنضمام:</strong> {{ $user->created_at->format('Y-m-d') }}</p>
                        <p class="mb-0"><strong>آخر تحديث:</strong> {{ $user->updated_at->format('Y-m-d') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">تعديل الملف الشخصي</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <form action="{{ route('admin.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">الاسم</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <hr>
                        <h6 class="mb-3">تغيير كلمة المرور</h6>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور الجديدة</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                            <div class="form-text">اترك هذا الحقل فارغًا إذا كنت لا ترغب في تغيير كلمة المرور.</div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .avatar-circle {
        width: 50px;
        height: 50px;
        background-color: #3498db;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
    }
    
    .avatar-initials {
        font-size: 1.5rem;
    }
</style>
@endsection 