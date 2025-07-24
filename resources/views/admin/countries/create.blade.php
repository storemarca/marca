@extends('layouts.admin')

@section('title', __('add_country'))
@section('page-title', __('add_country'))

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 d-flex align-items-center">
            <i class="fas fa-globe me-2 text-primary"></i>
            {{ __('add_country') }}
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.countries.store') }}" method="POST" id="country-form">
            @csrf
            
            <div class="row g-4">
                <div class="col-md-8">
                    <!-- بطاقة المعلومات الأساسية -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('basic_information') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- اسم البلد -->
                                <div class="col-md-6">
                                    <label for="name" class="form-label">{{ __('country_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                        class="form-control @error('name') is-invalid @enderror">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- رمز البلد -->
                                <div class="col-md-6">
                                    <label for="code" class="form-label">{{ __('country_code') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="code" id="code" value="{{ old('code') }}" required
                                        placeholder="{{ __('enter_country_code') }}" maxlength="2"
                                        class="form-control @error('code') is-invalid @enderror">
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- الحالة -->
                                <div class="col-md-6">
                                    <label for="is_active" class="form-label">{{ __('status') }}</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                            {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">{{ __('active') }}</label>
                                    </div>
                                    @error('is_active')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- بطاقة معلومات العملة -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('currency_information') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- رمز العملة -->
                                <div class="col-md-6">
                                    <label for="currency_code" class="form-label">{{ __('currency_code') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="currency_code" id="currency_code" value="{{ old('currency_code') }}" required
                                        placeholder="{{ __('enter_currency_code') }}" maxlength="3"
                                        class="form-control @error('currency_code') is-invalid @enderror">
                                    @error('currency_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- رمز العملة (الرمز) -->
                                <div class="col-md-6">
                                    <label for="currency_symbol" class="form-label">{{ __('currency_symbol') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="currency_symbol" id="currency_symbol" value="{{ old('currency_symbol') }}" required
                                        placeholder="{{ __('enter_currency_symbol') }}" maxlength="10"
                                        class="form-control @error('currency_symbol') is-invalid @enderror">
                                    @error('currency_symbol')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- نسبة الضريبة -->
                                <div class="col-md-6">
                                    <label for="tax_rate" class="form-label">{{ __('tax_rate') }} (%)</label>
                                    <input type="number" name="tax_rate" id="tax_rate" value="{{ old('tax_rate', '0') }}" 
                                        min="0" max="100" step="0.01"
                                        class="form-control @error('tax_rate') is-invalid @enderror">
                                    @error('tax_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <!-- بطاقة الإجراءات -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('actions') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> {{ __('save_country') }}
                                </button>
                                <a href="{{ route('admin.countries.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> {{ __('cancel') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- بطاقة المساعدة -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('help') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info mb-0">
                                <h6><i class="fas fa-info-circle me-2"></i>{{ __('information') }}</h6>
                                <ul class="mb-0 ps-3">
                                    <li>{{ __('country_code') }}: ISO 3166-1 alpha-2 (مثال: SA, US)</li>
                                    <li>{{ __('currency_code') }}: ISO 4217 (مثال: SAR, USD)</li>
                                    <li>{{ __('currency_symbol') }}: رمز العملة (مثال: ر.س, $)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تحويل رمز البلد إلى حروف كبيرة
        const codeInput = document.getElementById('code');
        codeInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
        
        // تحويل رمز العملة إلى حروف كبيرة
        const currencyCodeInput = document.getElementById('currency_code');
        currencyCodeInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    });
</script>
@endpush 