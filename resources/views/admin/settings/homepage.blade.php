@extends('layouts.admin')

@section('title', 'إعدادات الصفحة الرئيسية')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">إعدادات الصفحة الرئيسية</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
        <li class="breadcrumb-item active">إعدادات الصفحة الرئيسية</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-cog me-1"></i>
            إعدادات الصفحة الرئيسية
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('admin.settings.homepage.save') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row mb-4">
                    <div class="col-12">
                        <h4 class="border-bottom pb-2">قسم البانر الرئيسي</h4>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="home_banner_title" class="form-label">عنوان البانر الرئيسي</label>
                            <input type="text" class="form-control" id="home_banner_title" name="home_banner_title" value="{{ $settings['home_banner_title'] ?? '' }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="home_banner_subtitle" class="form-label">وصف البانر الرئيسي</label>
                            <textarea class="form-control" id="home_banner_subtitle" name="home_banner_subtitle" rows="2" required>{{ $settings['home_banner_subtitle'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="home_banner_button_text" class="form-label">نص زر البانر</label>
                            <input type="text" class="form-control" id="home_banner_button_text" name="home_banner_button_text" value="{{ $settings['home_banner_button_text'] ?? '' }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="home_banner_image" class="form-label">صورة البانر الرئيسي</label>
                            <input type="file" class="form-control" id="home_banner_image" name="home_banner_image">
                            @if(isset($settings['home_banner_image']) && $settings['home_banner_image'])
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $settings['home_banner_image']) }}" alt="Banner Image" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-12">
                        <h4 class="border-bottom pb-2">أقسام المنتجات</h4>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="home_featured_title" class="form-label">عنوان قسم المنتجات المميزة</label>
                            <input type="text" class="form-control" id="home_featured_title" name="home_featured_title" value="{{ $settings['home_featured_title'] ?? '' }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="home_new_title" class="form-label">عنوان قسم المنتجات الجديدة</label>
                            <input type="text" class="form-control" id="home_new_title" name="home_new_title" value="{{ $settings['home_new_title'] ?? '' }}" required>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-12">
                        <h4 class="border-bottom pb-2">قسم التصنيفات</h4>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="home_categories_title" class="form-label">عنوان قسم التصنيفات</label>
                            <input type="text" class="form-control" id="home_categories_title" name="home_categories_title" value="{{ $settings['home_categories_title'] ?? '' }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="home_categories_subtitle" class="form-label">وصف قسم التصنيفات</label>
                            <textarea class="form-control" id="home_categories_subtitle" name="home_categories_subtitle" rows="2" required>{{ $settings['home_categories_subtitle'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">حفظ الإعدادات</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 