@extends('layouts.admin')

@section('title', __('warehouse_details'))
@section('page-title', __('warehouse_details'))

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- بطاقة معلومات المستودع -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-warehouse me-2 text-primary"></i>
                    {{ $warehouse->name }}
                </h5>
                <span class="badge bg-{{ $warehouse->is_active ? 'success' : 'danger' }}">
                    {{ $warehouse->is_active ? __('active') : __('inactive') }}
                </span>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-muted mb-2">{{ __('basic_information') }}</h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">{{ __('id') }}:</span>
                            <span class="fw-medium">{{ $warehouse->id }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">{{ __('country') }}:</span>
                            <span class="fw-medium">{{ $warehouse->country->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">{{ __('manager_name') }}:</span>
                            <span class="fw-medium">{{ $warehouse->manager_name ?: '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">{{ __('created_at') }}:</span>
                            <span class="fw-medium">{{ $warehouse->created_at->format('Y-m-d') }}</span>
                        </li>
                    </ul>
                </div>
                
                <div class="mb-4">
                    <h6 class="text-muted mb-2">{{ __('contact_information') }}</h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                            <div class="text-muted mb-1">{{ __('phone') }}:</div>
                            <div class="fw-medium">{{ $warehouse->phone ?: '-' }}</div>
                        </li>
                        <li class="list-group-item px-0">
                            <div class="text-muted mb-1">{{ __('email') }}:</div>
                            <div class="fw-medium">{{ $warehouse->email ?: '-' }}</div>
                        </li>
                        <li class="list-group-item px-0">
                            <div class="text-muted mb-1">{{ __('address') }}:</div>
                            <div class="fw-medium">{{ $warehouse->address ?: '-' }}</div>
                        </li>
                    </ul>
                </div>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.warehouses.edit', $warehouse) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> {{ __('edit_warehouse') }}
                    </a>
                    <a href="{{ route('admin.warehouses.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> {{ __('back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
        
        <!-- بطاقة إحصائيات المخزون -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('inventory_statistics') }}</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="card bg-light">
                            <div class="card-body text-center py-3">
                                <h3 class="mb-1">{{ $stocks->count() }}</h3>
                                <div class="text-muted small">{{ __('products') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card bg-light">
                            <div class="card-body text-center py-3">
                                <h3 class="mb-1">{{ $stocks->sum('quantity') }}</h3>
                                <div class="text-muted small">{{ __('total_items') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body text-center py-3">
                                <h3 class="mb-1">{{ number_format($totalValue, 2) }}</h3>
                                <div class="text-muted small">{{ __('total_value') }} ({{ __('currency_symbol') }})</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- بطاقة المخزون -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{ __('warehouse_inventory') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('product') }}</th>
                                <th>{{ __('sku') }}</th>
                                <th class="text-center">{{ __('quantity') }}</th>
                                <th class="text-end">{{ __('unit_cost') }}</th>
                                <th class="text-end">{{ __('total_cost') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stocks as $stock)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.products.show', $stock->product) }}" class="fw-bold text-decoration-none">
                                            {{ $stock->product->name }}
                                        </a>
                                    </td>
                                    <td>{{ $stock->product->sku }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $stock->quantity > 5 ? 'success' : ($stock->quantity > 0 ? 'warning' : 'danger') }} rounded-pill">
                                            {{ $stock->quantity }}
                                        </span>
                                    </td>
                                    <td class="text-end">{{ number_format($stock->product->cost, 2) }}</td>
                                    <td class="text-end">{{ number_format($stock->quantity * $stock->product->cost, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                            <h5>{{ __('no_products_in_warehouse') }}</h5>
                                            <p class="text-muted">{{ __('no_products_in_warehouse_description') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 