@extends('layouts.admin')

@section('title', 'الإعدادات العامة')
@section('page-title', 'الإعدادات العامة')

@section('content')
    <div class="row">
        <div class="col-lg-3 mb-4">
            @include('admin.settings.partials.sidebar')
        </div>
        
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">الإعدادات العامة للموقع</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.general.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- معلومات الموقع الأساسية -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">معلومات الموقع الأساسية</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="site_name" class="form-label">اسم الموقع</label>
                                    <input type="text" class="form-control @error('site_name') is-invalid @enderror" id="site_name" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? config('app.name')) }}">
                                    @error('site_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="site_email" class="form-label">البريد الإلكتروني للموقع</label>
                                    <input type="email" class="form-control @error('site_email') is-invalid @enderror" id="site_email" name="site_email" value="{{ old('site_email', $settings['site_email'] ?? '') }}">
                                    @error('site_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="site_phone" class="form-label">رقم الهاتف</label>
                                    <input type="text" class="form-control @error('site_phone') is-invalid @enderror" id="site_phone" name="site_phone" value="{{ old('site_phone', $settings['site_phone'] ?? '') }}">
                                    @error('site_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="default_language" class="form-label">اللغة الافتراضية</label>
                                    <select class="form-select @error('default_language') is-invalid @enderror" id="default_language" name="default_language">
                                        <option value="ar" {{ old('default_language', $settings['default_language'] ?? 'ar') == 'ar' ? 'selected' : '' }}>العربية</option>
                                        <option value="en" {{ old('default_language', $settings['default_language'] ?? 'ar') == 'en' ? 'selected' : '' }}>English</option>
                                    </select>
                                    @error('default_language')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-12">
                                    <label for="site_description" class="form-label">وصف الموقع</label>
                                    <textarea class="form-control @error('site_description') is-invalid @enderror" id="site_description" name="site_description" rows="3">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                                    @error('site_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-12">
                                    <label for="site_address" class="form-label">عنوان الشركة</label>
                                    <textarea class="form-control @error('site_address') is-invalid @enderror" id="site_address" name="site_address" rows="2">{{ old('site_address', $settings['site_address'] ?? '') }}</textarea>
                                    @error('site_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <!-- إعدادات العملة -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">إعدادات العملة</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="currency" class="form-label">العملة</label>
                                    <input type="text" class="form-control @error('currency') is-invalid @enderror" id="currency" name="currency" value="{{ old('currency', $settings['currency'] ?? 'SAR') }}" placeholder="مثال: SAR, USD">
                                    @error('currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="currency_symbol" class="form-label">رمز العملة</label>
                                    <input type="text" class="form-control @error('currency_symbol') is-invalid @enderror" id="currency_symbol" name="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? 'ر.س') }}" placeholder="مثال: ر.س, $">
                                    @error('currency_symbol')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <!-- شعار وأيقونة الموقع -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">شعار وأيقونة الموقع</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="site_logo" class="form-label">شعار الموقع</label>
                                    <input type="file" class="form-control @error('site_logo') is-invalid @enderror" id="site_logo" name="site_logo">
                                    @error('site_logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    @if(!empty($settings['site_logo']))
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $settings['site_logo']) }}" alt="شعار الموقع" class="img-thumbnail" style="max-height: 50px;">
                                        </div>
                                    @endif
                                    <small class="form-text text-muted">الحجم المفضل: 200×50 بكسل</small>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="site_favicon" class="form-label">أيقونة الموقع (Favicon)</label>
                                    <input type="file" class="form-control @error('site_favicon') is-invalid @enderror" id="site_favicon" name="site_favicon">
                                    @error('site_favicon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    @if(!empty($settings['site_favicon']))
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $settings['site_favicon']) }}" alt="أيقونة الموقع" class="img-thumbnail" style="max-height: 32px;">
                                        </div>
                                    @endif
                                    <small class="form-text text-muted">الحجم المفضل: 32×32 بكسل</small>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <!-- إعدادات متقدمة -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">إعدادات متقدمة</h6>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1" {{ old('maintenance_mode', $settings['maintenance_mode'] ?? 0) ? 'checked' : '' }}>
                                <label class="form-check-label" for="maintenance_mode">وضع الصيانة</label>
                                <div class="form-text text-muted">عند تفعيل وضع الصيانة، سيتم عرض صفحة الصيانة للزوار، بينما يمكن للمدراء الاستمرار في الوصول إلى لوحة التحكم.</div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <!-- CSS مخصص -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">CSS مخصص</h6>
                            <div class="row">
                                <div class="col-12">
                                    <label for="custom_css" class="form-label">أكواد CSS مخصصة</label>
                                    <textarea class="form-control @error('custom_css') is-invalid @enderror" id="custom_css" name="custom_css" rows="6" placeholder="/* أضف أكواد CSS المخصصة هنا */">{{ old('custom_css', $settings['custom_css'] ?? '') }}</textarea>
                                    <div class="form-text text-muted">أضف أكواد CSS المخصصة التي تريد تطبيقها على لوحة التحكم. سيتم تحميل هذه الأكواد بعد ملف CSS الرئيسي.</div>
                                    @error('custom_css')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> حفظ الإعدادات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 