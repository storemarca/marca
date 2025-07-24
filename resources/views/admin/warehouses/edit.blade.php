@extends('layouts.admin')

@section('title', __('edit_warehouse'))
@section('page-title', __('edit_warehouse'))

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 d-flex align-items-center">
            <i class="fas fa-warehouse me-2 text-primary"></i>
            {{ __('edit_warehouse') }}
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.warehouses.update', $warehouse) }}" method="POST" id="warehouse-form">
            @csrf
            @method('PUT')
            
            <div class="row g-4">
                <div class="col-md-8">
                    <!-- بطاقة المعلومات الأساسية -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('basic_information') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- اسم المستودع -->
                                <div class="col-md-6">
                                    <label for="name" class="form-label">{{ __('warehouse_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $warehouse->name) }}" required
                                        class="form-control @error('name') is-invalid @enderror">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- البلد -->
                                <div class="col-md-6">
                                    <label for="country_id" class="form-label">{{ __('country') }} <span class="text-danger">*</span></label>
                                    <select name="country_id" id="country_id" required
                                        class="form-select @error('country_id') is-invalid @enderror">
                                        <option value="">{{ __('select_country') }}</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}" {{ old('country_id', $warehouse->country_id) == $country->id ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('country_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- اسم المدير -->
                                <div class="col-md-6">
                                    <label for="manager_name" class="form-label">{{ __('manager_name') }}</label>
                                    <input type="text" name="manager_name" id="manager_name" value="{{ old('manager_name', $warehouse->manager_name) }}"
                                        class="form-control @error('manager_name') is-invalid @enderror">
                                    @error('manager_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- رقم الهاتف -->
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">{{ __('phone') }}</label>
                                    <input type="text" name="phone" id="phone" value="{{ old('phone', $warehouse->phone) }}"
                                        class="form-control @error('phone') is-invalid @enderror">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- البريد الإلكتروني -->
                                <div class="col-md-6">
                                    <label for="email" class="form-label">{{ __('email') }}</label>
                                    <input type="email" name="email" id="email" value="{{ old('email', $warehouse->email) }}"
                                        class="form-control @error('email') is-invalid @enderror">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- الحالة -->
                                <div class="col-md-6">
                                    <label for="is_active" class="form-label">{{ __('status') }}</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                            {{ old('is_active', $warehouse->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">{{ __('active') }}</label>
                                    </div>
                                    @error('is_active')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- بطاقة العنوان -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('address') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="address_line1" class="form-label">{{ __('address_line1') }}</label>
                                    <input type="text" name="address_line1" id="address_line1" value="{{ old('address_line1', $warehouse->address_line1) }}"
                                        class="form-control @error('address_line1') is-invalid @enderror">
                                    @error('address_line1')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="address_line2" class="form-label">{{ __('address_line2') }}</label>
                                    <input type="text" name="address_line2" id="address_line2" value="{{ old('address_line2', $warehouse->address_line2) }}"
                                        class="form-control @error('address_line2') is-invalid @enderror">
                                    @error('address_line2')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="city" class="form-label">{{ __('city') }}</label>
                                    <input type="text" name="city" id="city" value="{{ old('city', $warehouse->city) }}"
                                        class="form-control @error('city') is-invalid @enderror">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="state" class="form-label">{{ __('state_province') }}</label>
                                    <input type="text" name="state" id="state" value="{{ old('state', $warehouse->state) }}"
                                        class="form-control @error('state') is-invalid @enderror">
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="postal_code" class="form-label">{{ __('postal_code') }}</label>
                                    <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $warehouse->postal_code) }}"
                                        class="form-control @error('postal_code') is-invalid @enderror">
                                    @error('postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-12">
                                    <label for="address" class="form-label">{{ __('additional_address_info') }}</label>
                                    <textarea name="address" id="address" rows="3"
                                        class="form-control @error('address') is-invalid @enderror">{{ old('address', $warehouse->address) }}</textarea>
                                    @error('address')
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
                                    <i class="fas fa-save me-1"></i> {{ __('update_warehouse') }}
                                </button>
                                <a href="{{ route('admin.warehouses.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> {{ __('cancel') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- بطاقة معلومات المستودع -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">{{ __('warehouse_information') }}</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted">{{ __('id') }}:</span>
                                    <span class="fw-medium">{{ $warehouse->id }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted">{{ __('created_at') }}:</span>
                                    <span class="fw-medium">{{ $warehouse->created_at->format('Y-m-d') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted">{{ __('updated_at') }}:</span>
                                    <span class="fw-medium">{{ $warehouse->updated_at->format('Y-m-d') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection 