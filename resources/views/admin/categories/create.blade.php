@extends('layouts.admin')

@section('title', trans('add_category'))
@section('page-title', trans('add_category'))

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 d-flex align-items-center">
            <i class="fas fa-tag me-2 text-primary"></i>
            {{ trans('add_category') }}
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" id="category-form">
            @csrf
            
            <div class="row">
                <div class="col-md-8">
                    <!-- بطاقة المعلومات الأساسية -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">{{ trans('basic_information') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- اسم القسم -->
                                <div class="col-md-6">
                                    <label for="name" class="form-label">{{ trans('category_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                        class="form-control @error('name') is-invalid @enderror">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- الرابط -->
                                <div class="col-md-6">
                                    <label for="slug" class="form-label">{{ trans('slug') }}</label>
                                    <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                                        class="form-control @error('slug') is-invalid @enderror"
                                        placeholder="{{ trans('auto_generate_placeholder') }}">
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- القسم الأب -->
                                <div class="col-md-6">
                                    <label for="parent_id" class="form-label">{{ trans('parent_category') }}</label>
                                    <select name="parent_id" id="parent_id"
                                        class="form-select @error('parent_id') is-invalid @enderror">
                                        <option value="">{{ trans('main_category') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('parent_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- ترتيب العرض -->
                                <div class="col-md-6">
                                    <label for="sort_order" class="form-label">{{ trans('sort_order') }}</label>
                                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}"
                                        class="form-control @error('sort_order') is-invalid @enderror">
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- الحالة -->
                                <div class="col-md-6">
                                    <div class="form-check form-switch mt-4">
                                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                            class="form-check-input @error('is_active') is-invalid @enderror">
                                        <label for="is_active" class="form-check-label">{{ trans('active') }}</label>
                                    </div>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- بطاقة الوصف -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">{{ trans('description') }}</h6>
                        </div>
                        <div class="card-body">
                            <textarea name="description" id="description" rows="4"
                                class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- بطاقة بيانات SEO -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ trans('seo_data') }}</h6>
                            <button type="button" class="btn btn-sm btn-link text-primary p-0" data-bs-toggle="collapse" data-bs-target="#seoCollapse">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                        <div class="collapse" id="seoCollapse">
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="meta_title" class="form-label">{{ trans('meta_title') }}</label>
                                        <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}"
                                            class="form-control @error('meta_title') is-invalid @enderror">
                                        @error('meta_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-12">
                                        <label for="meta_description" class="form-label">{{ trans('meta_description') }}</label>
                                        <textarea name="meta_description" id="meta_description" rows="3"
                                            class="form-control @error('meta_description') is-invalid @enderror">{{ old('meta_description') }}</textarea>
                                        @error('meta_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <!-- بطاقة الصورة -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">{{ trans('category_image') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 text-center">
                                <div class="category-image-preview mb-3">
                                    <img id="image-preview" src="{{ asset('images/placeholder-image.png') }}" alt="Category Image Preview" class="img-fluid rounded border">
                                </div>
                                <div class="input-group">
                                    <input type="file" name="image" id="image" accept="image/*"
                                        class="form-control @error('image') is-invalid @enderror">
                                    <label class="input-group-text" for="image">{{ trans('browse') }}</label>
                                </div>
                                <div class="form-text">{{ trans('recommended_image_size') }}</div>
                                @error('image')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- بطاقة الإجراءات -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">{{ trans('actions') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> {{ trans('save_category') }}
                                </button>
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> {{ trans('cancel') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .category-image-preview {
        width: 100%;
        height: 200px;
        overflow: hidden;
        position: relative;
        background-color: #f8f9fa;
        border-radius: 0.25rem;
    }
    
    .category-image-preview img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        transition: all 0.3s ease;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // توليد الرابط تلقائيًا من الاسم
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');
        
        nameInput.addEventListener('input', function() {
            if (!slugInput.value) {
                // تحويل النص إلى slug مناسب (إزالة الأحرف الخاصة واستبدال المسافات بشرطات)
                const slug = this.value
                    .toLowerCase()
                    .replace(/[\s_]+/g, '-')
                    .replace(/[^\w\-]+/g, '')
                    .replace(/\-\-+/g, '-')
                    .replace(/^-+/, '')
                    .replace(/-+$/, '');
                
                slugInput.value = slug;
            }
        });
        
        // معاينة الصورة
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('image-preview');
        
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
        
        // تفعيل توليد عنوان الميتا تلقائيًا
        const metaTitleInput = document.getElementById('meta_title');
        
        nameInput.addEventListener('blur', function() {
            if (!metaTitleInput.value) {
                metaTitleInput.value = this.value;
            }
        });
    });
</script>
@endpush 