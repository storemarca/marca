@extends('layouts.admin')

@section('title', trans('add_product'))
@section('page-title', trans('add_product'))

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 d-flex align-items-center">
            <i class="fas fa-box-open me-2 text-primary"></i>
            {{ trans('add_product') }}
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="product-form">
            @csrf
            
            <!-- Nav tabs -->
            <ul class="nav nav-tabs mb-3" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab" aria-controls="basic" aria-selected="true">
                        <i class="fas fa-info-circle me-1"></i> {{ trans('basic_information') ?? 'المعلومات الأساسية' }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="prices-tab" data-bs-toggle="tab" data-bs-target="#prices" type="button" role="tab" aria-controls="prices" aria-selected="false">
                        <i class="fas fa-tag me-1"></i> {{ trans('prices') ?? 'الأسعار' }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button" role="tab" aria-controls="inventory" aria-selected="false">
                        <i class="fas fa-warehouse me-1"></i> {{ trans('inventory') ?? 'المخزون' }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="attributes-tab" data-bs-toggle="tab" data-bs-target="#attributes" type="button" role="tab" aria-controls="attributes" aria-selected="false">
                        <i class="fas fa-list-ul me-1"></i> {{ trans('attributes') ?? 'الخصائص' }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="media-tab" data-bs-toggle="tab" data-bs-target="#media" type="button" role="tab" aria-controls="media" aria-selected="false">
                        <i class="fas fa-images me-1"></i> {{ trans('media') ?? 'الوسائط' }}
                    </button>
                </li>
            </ul>
            
            <!-- Tab content -->
            <div class="tab-content">
                <!-- المعلومات الأساسية -->
                <div class="tab-pane fade show active" id="basic" role="tabpanel" aria-labelledby="basic-tab">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <!-- اسم المنتج -->
                                    <div class="mb-3">
                                        <label for="name" class="form-label">{{ trans('product_name') ?? 'اسم المنتج' }} <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                            class="form-control @error('name') is-invalid @enderror">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- رمز المنتج (SKU) -->
                                    <div class="mb-3">
                                        <label for="sku" class="form-label">{{ trans('sku') ?? 'رمز المنتج' }}</label>
                                        <div class="input-group">
                                            <input type="text" name="sku" id="sku" value="{{ old('sku') }}"
                                                class="form-control @error('sku') is-invalid @enderror" placeholder="سيتم توليده تلقائيًا إذا تركته فارغًا">
                                            <button type="button" class="btn btn-outline-secondary" id="generate-sku">
                                                <i class="fas fa-sync-alt"></i> توليد
                                            </button>
                                        </div>
                                        <small class="text-muted">اتركه فارغًا ليتم توليده تلقائيًا أو اضغط على زر التوليد</small>
                                        @error('sku')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- الفئة -->
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">{{ trans('category') ?? 'الفئة' }} <span class="text-danger">*</span></label>
                                        <select name="category_id" id="category_id" required
                                            class="form-select @error('category_id') is-invalid @enderror">
                                            <option value="">{{ trans('select_category') ?? 'اختر الفئة' }}</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <!-- تكلفة المنتج -->
                                        <div class="col-md-6">
                                            <label for="cost" class="form-label">{{ trans('cost') ?? 'التكلفة' }} <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" name="cost" id="cost" value="{{ old('cost') }}" step="0.01" min="0" required
                                                    class="form-control @error('cost') is-invalid @enderror">
                                                <span class="input-group-text">{{ trans('currency_symbol') ?? '$' }}</span>
                                            </div>
                                            @error('cost')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- عدد القطع -->
                                        <div class="col-md-6">
                                            <label for="pieces_count" class="form-label">{{ trans('pieces_count') ?? 'عدد القطع' }}</label>
                                            <input type="number" name="pieces_count" id="pieces_count" value="{{ old('pieces_count', 1) }}" min="1" step="1"
                                                class="form-control @error('pieces_count') is-invalid @enderror">
                                            @error('pieces_count')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- الوزن -->
                                        <div class="col-md-6">
                                            <label for="weight" class="form-label">{{ trans('weight') ?? 'الوزن' }} <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" name="weight" id="weight" value="{{ old('weight') }}" step="0.01" min="0" required
                                                    class="form-control @error('weight') is-invalid @enderror">
                                                <span class="input-group-text">{{ trans('kg') ?? 'كجم' }}</span>
                                            </div>
                                            @error('weight')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- الحالة -->
                                        <div class="col-md-6">
                                            <label for="status" class="form-label">{{ trans('status') ?? 'الحالة' }} <span class="text-danger">*</span></label>
                                            <select name="status" id="status" required
                                                class="form-select @error('status') is-invalid @enderror">
                                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>{{ trans('active') ?? 'نشط' }}</option>
                                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>{{ trans('inactive') ?? 'غير نشط' }}</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- الوصف -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <label for="description" class="form-label">{{ trans('description') ?? 'الوصف' }} <span class="text-danger">*</span></label>
                                    <textarea name="description" id="description" rows="4" required
                                        class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- الأسعار -->
                <div class="tab-pane fade" id="prices" role="tabpanel" aria-labelledby="prices-tab">
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach($countries as $country)
                                    <div class="col-md-4">
                                        <div class="card h-100 border">
                                            <div class="card-body">
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" name="countries[]" id="country_{{ $country->id }}" value="{{ $country->id }}" 
                                                        {{ (is_array(old('countries')) && in_array($country->id, old('countries'))) ? 'checked' : '' }}
                                                        class="form-check-input">
                                                    <label for="country_{{ $country->id }}" class="form-check-label fw-bold">{{ $country->name }}</label>
                                                </div>
                                                <div class="input-group mb-2">
                                                    <span class="input-group-text">{{ trans('price') ?? 'السعر' }}</span>
                                                    <input type="number" name="price[{{ $country->id }}]" value="{{ old('price.' . $country->id) }}" 
                                                        step="0.01" min="0" placeholder="0.00"
                                                        class="form-control @error('price.' . $country->id) is-invalid @enderror">
                                                    <span class="input-group-text">{{ $country->currency_symbol }}</span>
                                                </div>
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ trans('sale_price') ?? 'سعر العرض' }}</span>
                                                    <input type="number" name="sale_price[{{ $country->id }}]" value="{{ old('sale_price.' . $country->id) }}" 
                                                        step="0.01" min="0" placeholder="0.00"
                                                        class="form-control @error('sale_price.' . $country->id) is-invalid @enderror">
                                                    <span class="input-group-text">{{ $country->currency_symbol }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('countries')
                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                            @enderror
                            @error('price.*')
                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- المخزون -->
                <div class="tab-pane fade" id="inventory" role="tabpanel" aria-labelledby="inventory-tab">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ trans('inventory') ?? 'المخزون' }}</h6>
                            <button type="button" id="add-stock" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-plus me-1"></i> {{ trans('add_warehouse') ?? 'إضافة مستودع' }}
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="stock-container">
                                <div class="stock-item mb-3 pb-3 border-bottom">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">{{ trans('warehouse') ?? 'المستودع' }}</label>
                                            <select name="stocks[0][warehouse_id]" class="form-select">
                                                <option value="">{{ trans('select_warehouse') ?? 'اختر المستودع' }}</option>
                                                @foreach($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex">
                                                <div class="flex-grow-1">
                                                    <label class="form-label">{{ trans('quantity') ?? 'الكمية' }}</label>
                                                    <input type="number" name="stocks[0][quantity]" min="0" value="0"
                                                        class="form-control">
                                                </div>
                                                <div class="ms-2 d-flex align-items-end">
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-stock mb-1">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- الخصائص -->
                <div class="tab-pane fade" id="attributes" role="tabpanel" aria-labelledby="attributes-tab">
                    <div class="row">
                        <!-- الألوان -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">{{ trans('colors') ?? 'الألوان المتاحة' }}</h6>
                                    <button type="button" id="add-custom-color" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-plus me-1"></i> {{ trans('add_custom_color') ?? 'إضافة لون مخصص' }}
                                    </button>
                                </div>
                                <div class="card-body">
                                    <!-- الألوان الشائعة -->
                                    <div class="mb-4">
                                        <h6 class="mb-3">{{ trans('common_colors') ?? 'الألوان الشائعة' }}</h6>
                                        <div class="row g-3">
                                            <div class="col-md-4 col-6">
                                                <div class="form-check color-checkbox">
                                                    <input class="form-check-input" type="checkbox" id="color-red" name="common_colors[]" value="red|#FF0000|أحمر">
                                                    <label class="form-check-label d-flex align-items-center" for="color-red">
                                                        <span class="color-swatch me-2" style="background-color: #FF0000;"></span>
                                                        أحمر
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <div class="form-check color-checkbox">
                                                    <input class="form-check-input" type="checkbox" id="color-blue" name="common_colors[]" value="blue|#0000FF|أزرق">
                                                    <label class="form-check-label d-flex align-items-center" for="color-blue">
                                                        <span class="color-swatch me-2" style="background-color: #0000FF;"></span>
                                                        أزرق
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <div class="form-check color-checkbox">
                                                    <input class="form-check-input" type="checkbox" id="color-green" name="common_colors[]" value="green|#008000|أخضر">
                                                    <label class="form-check-label d-flex align-items-center" for="color-green">
                                                        <span class="color-swatch me-2" style="background-color: #008000;"></span>
                                                        أخضر
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <div class="form-check color-checkbox">
                                                    <input class="form-check-input" type="checkbox" id="color-black" name="common_colors[]" value="black|#000000|أسود">
                                                    <label class="form-check-label d-flex align-items-center" for="color-black">
                                                        <span class="color-swatch me-2" style="background-color: #000000;"></span>
                                                        أسود
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <div class="form-check color-checkbox">
                                                    <input class="form-check-input" type="checkbox" id="color-white" name="common_colors[]" value="white|#FFFFFF|أبيض">
                                                    <label class="form-check-label d-flex align-items-center" for="color-white">
                                                        <span class="color-swatch me-2" style="background-color: #FFFFFF; border: 1px solid #ddd;"></span>
                                                        أبيض
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-6">
                                                <div class="form-check color-checkbox">
                                                    <input class="form-check-input" type="checkbox" id="color-yellow" name="common_colors[]" value="yellow|#FFFF00|أصفر">
                                                    <label class="form-check-label d-flex align-items-center" for="color-yellow">
                                                        <span class="color-swatch me-2" style="background-color: #FFFF00;"></span>
                                                        أصفر
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- الألوان المخصصة -->
                                    <div>
                                        <h6 class="mb-3">{{ trans('custom_colors') ?? 'ألوان مخصصة' }}</h6>
                                        <div id="custom-colors-container">
                                            <!-- سيتم إضافة الألوان المخصصة هنا بواسطة JavaScript -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- المقاسات -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">{{ trans('sizes') ?? 'المقاسات المتاحة' }}</h6>
                                    <button type="button" id="add-custom-size" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-plus me-1"></i> {{ trans('add_custom_size') ?? 'إضافة مقاس مخصص' }}
                                    </button>
                                </div>
                                <div class="card-body">
                                    <!-- المقاسات الشائعة -->
                                    <div class="mb-4">
                                        <h6 class="mb-3">{{ trans('common_sizes') ?? 'المقاسات الشائعة' }}</h6>
                                        
                                        <!-- مقاسات الملابس -->
                                        <div class="mb-3">
                                            <p class="text-muted mb-2">{{ trans('clothing_sizes') ?? 'مقاسات الملابس' }}:</p>
                                            <div class="row g-2">
                                                <div class="col-auto">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="size-xs" name="common_sizes[]" value="XS">
                                                        <label class="form-check-label" for="size-xs">XS</label>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="size-s" name="common_sizes[]" value="S">
                                                        <label class="form-check-label" for="size-s">S</label>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="size-m" name="common_sizes[]" value="M">
                                                        <label class="form-check-label" for="size-m">M</label>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="size-l" name="common_sizes[]" value="L">
                                                        <label class="form-check-label" for="size-l">L</label>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="size-xl" name="common_sizes[]" value="XL">
                                                        <label class="form-check-label" for="size-xl">XL</label>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="size-xxl" name="common_sizes[]" value="XXL">
                                                        <label class="form-check-label" for="size-xxl">XXL</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- المقاسات المخصصة -->
                                    <div>
                                        <h6 class="mb-3">{{ trans('custom_sizes') ?? 'مقاسات مخصصة' }}</h6>
                                        <div id="custom-sizes-container">
                                            <!-- سيتم إضافة المقاسات المخصصة هنا بواسطة JavaScript -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- الفيديوهات -->
                        <div class="col-12">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">{{ trans('videos') ?? 'فيديوهات المنتج' }}</h6>
                                    <button type="button" id="add-video" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-plus me-1"></i> {{ trans('add_video') ?? 'إضافة فيديو' }}
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div id="videos-container">
                                        <div class="video-item mb-3 pb-3 border-bottom">
                                            <div class="row g-3">
                                                <div class="col-md-5">
                                                    <label class="form-label">{{ trans('video_title') ?? 'عنوان الفيديو' }}</label>
                                                    <input type="text" name="videos[0][title]" class="form-control" placeholder="عنوان الفيديو">
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label">{{ trans('video_url') ?? 'رابط الفيديو' }}</label>
                                                    <input type="url" name="videos[0][url]" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="d-flex h-100 align-items-end">
                                                        <button type="button" class="btn btn-outline-danger btn-sm remove-video mb-1">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- الوسائط -->
                <div class="tab-pane fade" id="media" role="tabpanel" aria-labelledby="media-tab">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="images" class="form-label">{{ trans('upload_images') ?? 'تحميل الصور' }}</label>
                                <input id="images" name="images[]" type="file" class="form-control mb-3" multiple accept="image/*">
                                
                                <!-- معاينة الصور -->
                                <div id="image-preview" class="mt-3 row g-2"></div>
                                
                                <!-- رسائل الخطأ -->
                                @error('images')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                @error('images.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- زر الحفظ -->
            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="fas fa-save me-1"></i> {{ trans('save_product') ?? 'حفظ المنتج' }}
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-lg px-5 ms-2">
                    <i class="fas fa-times me-1"></i> {{ trans('cancel') ?? 'إلغاء' }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .dropzone-container {
        border: 2px dashed #ddd;
        border-radius: 5px;
        padding: 25px;
        text-align: center;
        background: #f8f9fa;
        transition: all 0.3s;
    }
    
    .dropzone-container:hover {
        border-color: #adb5bd;
        background: #f1f3f5;
    }
    
    .dropzone-container i {
        font-size: 2rem;
        color: #6c757d;
        margin-bottom: 10px;
    }
    
    .color-swatch {
        display: inline-block;
        width: 20px;
        height: 20px;
        border-radius: 4px;
        vertical-align: middle;
        border: 1px solid rgba(0,0,0,0.1);
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .color-checkbox {
        margin-bottom: 10px;
    }
    
    .color-checkbox .form-check-label {
        cursor: pointer;
        display: flex;
        align-items: center;
    }
    
    .form-check-input:checked + .form-check-label .color-swatch {
        box-shadow: 0 0 0 2px #0d6efd;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تفعيل Select2 للقوائم المنسدلة
        $('.select2').select2();
        
        // تحديث حالة حقل السعر المخفض عند تغيير السعر الأساسي
        $('input[name^="price"]').on('change', function() {
            const countryId = $(this).attr('name').match(/\[(.*?)\]/)[1];
            const salePrice = $(`input[name="sale_price[${countryId}]"]`);
            const maxPrice = parseFloat($(this).val());
            
            if (salePrice.val() && parseFloat(salePrice.val()) >= maxPrice) {
                salePrice.val('');
            }
            
            salePrice.attr('max', maxPrice);
        });
        
        // زر توليد SKU
        $('#generate-sku').on('click', function() {
            const categoryId = $('#category_id').val();
            if (!categoryId) {
                alert('يرجى اختيار الفئة أولاً لتوليد رمز المنتج (SKU)');
                return;
            }
            
            // عرض مؤشر التحميل
            $(this).html('<i class="fas fa-spinner fa-spin"></i>');
            
            // الحصول على SKU من الخادم
            $.ajax({
                url: "{{ route('admin.products.generate-sku') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    category_id: categoryId
                },
                success: function(response) {
                    if (response.success) {
                        $('#sku').val(response.sku);
                    } else {
                        alert('حدث خطأ أثناء توليد رمز المنتج (SKU)');
                    }
                },
                error: function() {
                    alert('حدث خطأ أثناء توليد رمز المنتج (SKU)');
                },
                complete: function() {
                    $('#generate-sku').html('<i class="fas fa-sync-alt"></i> توليد');
                }
            });
        });
        
        // توليد SKU تلقائيًا عند تغيير الفئة إذا كان حقل SKU فارغًا
        $('#category_id').on('change', function() {
            if ($('#sku').val() === '') {
                $('#generate-sku').click();
            }
        });
        
        // تفعيل محرر النصوص
        if (typeof CKEDITOR !== 'undefined') {
            CKEDITOR.replace('description');
        }
        
        // إضافة لون مخصص
        $('#add-custom-color').on('click', function() {
            const colorIndex = $('.custom-color-row').length;
            const template = `
                <div class="row g-2 mb-2 custom-color-row">
                    <div class="col-5">
                        <input type="text" name="custom_colors[${colorIndex}][name]" class="form-control" placeholder="اسم اللون">
                    </div>
                    <div class="col-5">
                        <input type="color" name="custom_colors[${colorIndex}][code]" class="form-control form-control-color" value="#ffffff">
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-danger btn-sm remove-custom-color">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
            
            $('#custom-colors-container').append(template);
        });
        
        // حذف لون مخصص
        $(document).on('click', '.remove-custom-color', function() {
            $(this).closest('.custom-color-row').remove();
        });
        
        // إضافة مقاس مخصص
        $('#add-custom-size').on('click', function() {
            const sizeIndex = $('.custom-size-row').length;
            const template = `
                <div class="row g-2 mb-2 custom-size-row">
                    <div class="col-10">
                        <input type="text" name="custom_sizes[${sizeIndex}][name]" class="form-control" placeholder="اسم المقاس">
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-danger btn-sm remove-custom-size">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
            
            $('#custom-sizes-container').append(template);
        });
        
        // حذف مقاس مخصص
        $(document).on('click', '.remove-custom-size', function() {
            $(this).closest('.custom-size-row').remove();
        });
    });
</script>
@endpush 