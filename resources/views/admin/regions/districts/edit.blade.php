@extends('layouts.admin')

@section('title', 'تعديل مركز')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 mb-4">تعديل مركز</h1>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-edit me-1"></i>
            تعديل مركز: {{ $district->name }}
        </div>
        <div class="card-body">
            <form action="{{ route('admin.regions.districts.update', $district->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="governorate_id" class="form-label">المحافظة <span class="text-danger">*</span></label>
                            <select name="governorate_id" id="governorate_id" class="form-select @error('governorate_id') is-invalid @enderror" required>
                                <option value="">-- اختر المحافظة --</option>
                                @foreach($governorates as $governorate)
                                    <option value="{{ $governorate->id }}" {{ old('governorate_id', $district->governorate_id) == $governorate->id ? 'selected' : '' }}
                                        data-country="{{ $governorate->country->id }}" 
                                        data-currency="{{ $governorate->country->currency_symbol }}">
                                        {{ $governorate->name }} ({{ $governorate->country->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('governorate_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="code" class="form-label">كود المركز</label>
                            <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $district->code) }}">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">مثال: GIZA-DOK لمركز الدقي بمحافظة الجيزة</small>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">الاسم (بالإنجليزية) <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $district->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="name_ar" class="form-label">الاسم (بالعربية)</label>
                            <input type="text" name="name_ar" id="name_ar" class="form-control @error('name_ar') is-invalid @enderror" value="{{ old('name_ar', $district->name_ar) }}">
                            @error('name_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="additional_shipping_cost" class="form-label">تكلفة الشحن الإضافية <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="additional_shipping_cost" id="additional_shipping_cost" class="form-control @error('additional_shipping_cost') is-invalid @enderror" value="{{ old('additional_shipping_cost', $district->additional_shipping_cost) }}" step="0.01" min="0" required>
                                <span class="input-group-text currency-symbol">{{ $district->governorate->country->currency_symbol ?? 'ر.س' }}</span>
                            </div>
                            <small class="text-muted">هذه التكلفة تضاف إلى تكلفة شحن المحافظة</small>
                            @error('additional_shipping_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label d-block">الحالة</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $district->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">نشط</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> حفظ التغييرات
                    </button>
                    <a href="{{ route('admin.regions.districts.index') }}" class="btn btn-secondary me-2">
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
    // تحديث رمز العملة عند تغيير المحافظة
    document.getElementById('governorate_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (!selectedOption.value) return;
        
        const currencySymbol = selectedOption.getAttribute('data-currency');
        if (currencySymbol) {
            document.querySelector('.currency-symbol').textContent = currencySymbol;
        }
    });
</script>
@endsection 