@extends('layouts.admin')

@section('title', __('country_details'))
@section('page-title', __('country_details'))

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- بطاقة معلومات البلد -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-globe me-2 text-primary"></i>
                    {{ $country->name }}
                </h5>
                <span class="badge bg-{{ $country->is_active ? 'success' : 'danger' }}">
                    {{ $country->is_active ? __('active') : __('inactive') }}
                </span>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-muted mb-2">{{ __('basic_information') }}</h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">{{ __('id') }}:</span>
                            <span class="fw-medium">{{ $country->id }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">{{ __('country_code') }}:</span>
                            <span class="fw-medium">{{ $country->code }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">{{ __('created_at') }}:</span>
                            <span class="fw-medium">{{ $country->created_at->format('Y-m-d') }}</span>
                        </li>
                    </ul>
                </div>
                
                <div class="mb-4">
                    <h6 class="text-muted mb-2">{{ __('currency_information') }}</h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">{{ __('currency_code') }}:</span>
                            <span class="fw-medium">{{ $country->currency_code }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">{{ __('currency_symbol') }}:</span>
                            <span class="fw-medium">{{ $country->currency_symbol }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">{{ __('tax_rate') }}:</span>
                            <span class="fw-medium">{{ $country->tax_rate }}%</span>
                        </li>
                    </ul>
                </div>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.countries.edit', $country) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> {{ __('edit_country') }}
                    </a>
                    <a href="{{ route('admin.countries.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> {{ __('back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- بطاقة إحصائيات الاستخدام -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('usage_statistics') }}</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6 col-lg-3">
                        <div class="card bg-light h-100">
                            <div class="card-body text-center py-4">
                                <div class="mb-3">
                                    <i class="fas fa-users fa-2x text-primary"></i>
                                </div>
                                <h3 class="mb-1">{{ $customersCount }}</h3>
                                <div class="text-muted">{{ __('customers_count') }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <div class="card bg-light h-100">
                            <div class="card-body text-center py-4">
                                <div class="mb-3">
                                    <i class="fas fa-map-marker-alt fa-2x text-success"></i>
                                </div>
                                <h3 class="mb-1">{{ $addressesCount }}</h3>
                                <div class="text-muted">{{ __('addresses_count') }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <div class="card bg-light h-100">
                            <div class="card-body text-center py-4">
                                <div class="mb-3">
                                    <i class="fas fa-shopping-cart fa-2x text-info"></i>
                                </div>
                                <h3 class="mb-1">{{ $ordersCount }}</h3>
                                <div class="text-muted">{{ __('orders_count') }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <div class="card bg-light h-100">
                            <div class="card-body text-center py-4">
                                <div class="mb-3">
                                    <i class="fas fa-warehouse fa-2x text-warning"></i>
                                </div>
                                <h3 class="mb-1">{{ $warehousesCount }}</h3>
                                <div class="text-muted">{{ __('warehouses_count') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ __('country_delete_warning') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 