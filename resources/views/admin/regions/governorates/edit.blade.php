@extends('layouts.admin')

@section('title', 'تعديل محافظة')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 mb-4">تعديل محافظة</h1>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-edit me-1"></i>
            تعديل محافظة: {{ $governorate->name }}
        </div>
        <div class="card-body">
            <form action="{{ route('admin.regions.governorates.update', $governorate->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="country_id" class="form-label">الدولة <span class="text-danger">*</span></label>
                            <select name="country_id" id="country_id" class="form-select @error('country_id') is-invalid @enderror" required>
                                <option value="">-- اختر الدولة --</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country_id', $governorate->country_id) == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }} ({{ $country->name_ar }})
                                    </option>
                                @endforeach
                            </select>
                            @error('country_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="code" class="form-label">كود المحافظة</label>
                            <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $governorate->code) }}">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">مثال: CAI لمحافظة القاهرة</small>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">الاسم (بالإنجليزية) <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $governorate->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="name_ar" class="form-label">الاسم (بالعربية)</label>
                            <input type="text" name="name_ar" id="name_ar" class="form-control @error('name_ar') is-invalid @enderror" value="{{ old('name_ar', $governorate->name_ar) }}">
                            @error('name_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="shipping_cost" class="form-label">تكلفة الشحن <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="shipping_cost" id="shipping_cost" class="form-control @error('shipping_cost') is-invalid @enderror" value="{{ old('shipping_cost', $governorate->shipping_cost) }}" step="0.01" min="0" required>
                                <span class="input-group-text currency-symbol">{{ $governorate->country->currency_symbol ?? 'ر.س' }}</span>
                            </div>
                            @error('shipping_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label d-block">الحالة</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $governorate->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">نشط</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> حفظ التغييرات
                    </button>
                    <a href="{{ route('admin.regions.governorates.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-times me-1"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // تحديث رمز العملة عند تغيير الدولة
    document.getElementById('country_id').addEventListener('change', function() {
        const countryId = this.value;
        if (!countryId) return;
        
        // الحصول على معلومات الدولة
        fetch(`/api/countries/${countryId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const country = data.data;
                    document.querySelector('.currency-symbol').textContent = country.currency_symbol || 'ر.س';
                }
            })
            .catch(error => console.error('Error:', error));
    });
</script>
@endsection 