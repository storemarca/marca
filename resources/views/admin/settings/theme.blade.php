@extends('layouts.admin')

@section('title', 'إعدادات الثيمات والألوان')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.1/spectrum.min.css">
<style>
    .color-preview {
        width: 30px;
        height: 30px;
        border-radius: 4px;
        display: inline-block;
        margin-right: 10px;
        border: 1px solid #ddd;
    }
    
    .theme-preview {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .theme-preview-header {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 15px;
    }
    
    .theme-preview-content {
        padding: 15px;
    }
    
    .theme-preview-footer {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
        margin-top: 15px;
    }
    
    .theme-preview-button {
        padding: 8px 16px;
        border-radius: var(--button-radius, 0.375rem);
        background-color: var(--primary-color, #eab308);
        color: white;
        display: inline-block;
        margin-right: 10px;
    }
    
    .theme-preview-button-secondary {
        padding: 8px 16px;
        border-radius: var(--button-radius, 0.375rem);
        background-color: var(--secondary-color, #1f2937);
        color: white;
        display: inline-block;
        margin-right: 10px;
    }
    
    .theme-preview-button-accent {
        padding: 8px 16px;
        border-radius: var(--button-radius, 0.375rem);
        background-color: var(--accent-color, #ef4444);
        color: white;
        display: inline-block;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">إعدادات الثيمات والألوان</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
        <li class="breadcrumb-item active">إعدادات الثيمات والألوان</li>
    </ol>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-paint-brush me-1"></i>
                    إعدادات الثيمات والألوان
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
                    
                    <form action="{{ route('admin.settings.theme.save') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="border-bottom pb-2">الألوان الرئيسية</h4>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="primary_color" class="form-label">اللون الرئيسي</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <div class="color-preview" id="primary-color-preview" style="background-color: {{ $settings['primary_color'] ?? '#eab308' }}"></div>
                                        </span>
                                        <input type="text" class="form-control color-picker" id="primary_color" name="primary_color" value="{{ $settings['primary_color'] ?? '#eab308' }}" required>
                                    </div>
                                    <small class="form-text text-muted">يستخدم للأزرار والعناصر الرئيسية</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="secondary_color" class="form-label">اللون الثانوي</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <div class="color-preview" id="secondary-color-preview" style="background-color: {{ $settings['secondary_color'] ?? '#1f2937' }}"></div>
                                        </span>
                                        <input type="text" class="form-control color-picker" id="secondary_color" name="secondary_color" value="{{ $settings['secondary_color'] ?? '#1f2937' }}" required>
                                    </div>
                                    <small class="form-text text-muted">يستخدم للعناوين والنصوص</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="accent_color" class="form-label">لون التمييز</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <div class="color-preview" id="accent-color-preview" style="background-color: {{ $settings['accent_color'] ?? '#ef4444' }}"></div>
                                        </span>
                                        <input type="text" class="form-control color-picker" id="accent_color" name="accent_color" value="{{ $settings['accent_color'] ?? '#ef4444' }}" required>
                                    </div>
                                    <small class="form-text text-muted">يستخدم للتنبيهات والعناصر المميزة</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="border-bottom pb-2">أنماط العناصر</h4>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="header_style" class="form-label">نمط الهيدر</label>
                                    <select class="form-select" id="header_style" name="header_style">
                                        <option value="default" {{ ($settings['header_style'] ?? 'default') == 'default' ? 'selected' : '' }}>افتراضي</option>
                                        <option value="centered" {{ ($settings['header_style'] ?? '') == 'centered' ? 'selected' : '' }}>متوسط</option>
                                        <option value="minimal" {{ ($settings['header_style'] ?? '') == 'minimal' ? 'selected' : '' }}>بسيط</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="footer_style" class="form-label">نمط الفوتر</label>
                                    <select class="form-select" id="footer_style" name="footer_style">
                                        <option value="default" {{ ($settings['footer_style'] ?? 'default') == 'default' ? 'selected' : '' }}>افتراضي</option>
                                        <option value="simple" {{ ($settings['footer_style'] ?? '') == 'simple' ? 'selected' : '' }}>بسيط</option>
                                        <option value="detailed" {{ ($settings['footer_style'] ?? '') == 'detailed' ? 'selected' : '' }}>مفصل</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="button_style" class="form-label">نمط الأزرار</label>
                                    <select class="form-select" id="button_style" name="button_style">
                                        <option value="rounded" {{ ($settings['button_style'] ?? 'rounded') == 'rounded' ? 'selected' : '' }}>مستدير</option>
                                        <option value="square" {{ ($settings['button_style'] ?? '') == 'square' ? 'selected' : '' }}>مربع</option>
                                        <option value="pill" {{ ($settings['button_style'] ?? '') == 'pill' ? 'selected' : '' }}>كبسولة</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="border-bottom pb-2">خيارات إضافية</h4>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="enable_dark_mode" name="enable_dark_mode" value="1" {{ ($settings['enable_dark_mode'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enable_dark_mode">تفعيل الوضع الليلي</label>
                                    <div class="form-text">يتيح للمستخدمين التبديل بين الوضع الفاتح والوضع الداكن</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="rtl_support" name="rtl_support" value="1" {{ ($settings['rtl_support'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="rtl_support">دعم اللغات من اليمين لليسار</label>
                                    <div class="form-text">تفعيل دعم اللغات التي تكتب من اليمين لليسار مثل العربية</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="border-bottom pb-2">CSS مخصص</h4>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="custom_css" class="form-label">CSS مخصص</label>
                                    <textarea class="form-control" id="custom_css" name="custom_css" rows="6">{{ $settings['custom_css'] ?? '' }}</textarea>
                                    <div class="form-text">أضف أكواد CSS مخصصة لتخصيص مظهر الموقع بشكل أكبر</div>
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
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-eye me-1"></i>
                    معاينة الثيم
                </div>
                <div class="card-body">
                    <div class="theme-preview">
                        <div class="theme-preview-header">
                            <h5>معاينة الهيدر</h5>
                            <div class="mt-2">
                                <span class="theme-preview-button">زر رئيسي</span>
                            </div>
                        </div>
                        
                        <div class="theme-preview-content">
                            <h4 style="color: var(--secondary-color, #1f2937);">عنوان المحتوى</h4>
                            <p>هذا نص تجريبي لمعاينة شكل النصوص والألوان في الثيم المختار.</p>
                            
                            <div class="mt-3">
                                <span class="theme-preview-button">زر رئيسي</span>
                                <span class="theme-preview-button-secondary">زر ثانوي</span>
                                <span class="theme-preview-button-accent">زر مميز</span>
                            </div>
                        </div>
                        
                        <div class="theme-preview-footer">
                            <h5>معاينة الفوتر</h5>
                            <p class="small">حقوق النشر © {{ date('Y') }} {{ setting('site_name') }}</p>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i>
                        هذه معاينة بسيطة للألوان والأنماط التي اخترتها. قد يختلف المظهر النهائي قليلاً.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.1/spectrum.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تهيئة منتقي الألوان
        $(".color-picker").spectrum({
            showInput: true,
            preferredFormat: "hex",
            showPalette: true,
            palette: [
                ["#eab308", "#1f2937", "#ef4444", "#22c55e", "#3b82f6"],
                ["#f59e0b", "#374151", "#dc2626", "#16a34a", "#2563eb"],
                ["#d97706", "#4b5563", "#b91c1c", "#15803d", "#1d4ed8"]
            ],
            change: function(color) {
                updatePreview(this.id, color.toHexString());
            }
        });
        
        // تحديث المعاينة عند تغيير القيم
        function updatePreview(id, color) {
            if (id === 'primary_color') {
                document.getElementById('primary-color-preview').style.backgroundColor = color;
                document.documentElement.style.setProperty('--primary-color', color);
            } else if (id === 'secondary_color') {
                document.getElementById('secondary-color-preview').style.backgroundColor = color;
                document.documentElement.style.setProperty('--secondary-color', color);
            } else if (id === 'accent_color') {
                document.getElementById('accent-color-preview').style.backgroundColor = color;
                document.documentElement.style.setProperty('--accent-color', color);
            }
        }
        
        // تحديث نمط الأزرار
        document.getElementById('button_style').addEventListener('change', function() {
            let buttonRadius = '0.375rem'; // default
            if (this.value === 'square') {
                buttonRadius = '0';
            } else if (this.value === 'pill') {
                buttonRadius = '9999px';
            }
            document.documentElement.style.setProperty('--button-radius', buttonRadius);
        });
        
        // تهيئة المعاينة
        document.documentElement.style.setProperty('--primary-color', '{{ $settings['primary_color'] ?? '#eab308' }}');
        document.documentElement.style.setProperty('--secondary-color', '{{ $settings['secondary_color'] ?? '#1f2937' }}');
        document.documentElement.style.setProperty('--accent-color', '{{ $settings['accent_color'] ?? '#ef4444' }}');
        
        // تعيين نمط الأزرار
        let buttonStyle = '{{ $settings['button_style'] ?? 'rounded' }}';
        let buttonRadius = '0.375rem'; // default
        if (buttonStyle === 'square') {
            buttonRadius = '0';
        } else if (buttonStyle === 'pill') {
            buttonRadius = '9999px';
        }
        document.documentElement.style.setProperty('--button-radius', buttonRadius);
    });
</script>
@endsection 