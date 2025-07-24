@extends('layouts.admin')

@section('title', __('warehouses'))
@section('page-title', __('warehouses_management'))

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 d-flex align-items-center">
            <i class="fas fa-warehouse me-2 text-primary"></i>
            {{ __('warehouses_list') }}
        </h5>
        <a href="{{ route('admin.warehouses.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> {{ __('add_warehouse') }}
        </a>
    </div>
    
    <div class="card-body">
        <!-- فلاتر البحث -->
        <div class="mb-4">
            <form action="{{ route('admin.warehouses.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="{{ __('search_by_name') }}" value="{{ request('search') }}">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <select name="country_id" class="form-select">
                        <option value="">{{ __('all_countries') }}</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">{{ __('all_statuses') }}</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('active') }}</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('inactive') }}</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i> {{ __('filter') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- جدول المستودعات -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('id') }}</th>
                        <th>{{ __('name') }}</th>
                        <th>{{ __('country') }}</th>
                        <th>{{ __('manager') }}</th>
                        <th>{{ __('contact') }}</th>
                        <th>{{ __('status') }}</th>
                        <th>{{ __('actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($warehouses as $warehouse)
                        <tr>
                            <td>{{ $warehouse->id }}</td>
                            <td>
                                <a href="{{ route('admin.warehouses.show', $warehouse) }}" class="fw-bold text-decoration-none">
                                    {{ $warehouse->name }}
                                </a>
                            </td>
                            <td>{{ $warehouse->country->name }}</td>
                            <td>{{ $warehouse->manager_name ?: '-' }}</td>
                            <td>
                                @if($warehouse->phone)
                                    <div><i class="fas fa-phone-alt me-1 text-muted"></i> {{ $warehouse->phone }}</div>
                                @endif
                                @if($warehouse->email)
                                    <div><i class="fas fa-envelope me-1 text-muted"></i> {{ $warehouse->email }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $warehouse->is_active ? 'success' : 'danger' }}">
                                    {{ $warehouse->is_active ? __('active') : __('inactive') }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.warehouses.show', $warehouse) }}" class="btn btn-info" title="{{ __('view') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.warehouses.edit', $warehouse) }}" class="btn btn-primary" title="{{ __('edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-{{ $warehouse->is_active ? 'warning' : 'success' }}" 
                                        data-bs-toggle="modal" data-bs-target="#statusModal{{ $warehouse->id }}" 
                                        title="{{ $warehouse->is_active ? __('deactivate') : __('activate') }}">
                                        <i class="fas fa-{{ $warehouse->is_active ? 'ban' : 'check' }}"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger" 
                                        data-bs-toggle="modal" data-bs-target="#deleteModal{{ $warehouse->id }}" 
                                        title="{{ __('delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <!-- Modal تغيير الحالة -->
                                <div class="modal fade" id="statusModal{{ $warehouse->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ $warehouse->is_active ? __('deactivate_warehouse') : __('activate_warehouse') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>{{ $warehouse->is_active ? __('confirm_deactivate_warehouse') : __('confirm_activate_warehouse') }}</p>
                                                <p class="fw-bold">{{ $warehouse->name }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('cancel') }}</button>
                                                <form action="{{ route('admin.warehouses.toggle-status', $warehouse) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-{{ $warehouse->is_active ? 'warning' : 'success' }}">
                                                        {{ $warehouse->is_active ? __('deactivate') : __('activate') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Modal الحذف -->
                                <div class="modal fade" id="deleteModal{{ $warehouse->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('delete_warehouse') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>{{ __('confirm_delete_warehouse') }}</p>
                                                <p class="fw-bold">{{ $warehouse->name }}</p>
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    {{ __('warehouse_delete_warning') }}
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('cancel') }}</button>
                                                <form action="{{ route('admin.warehouses.destroy', $warehouse) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">{{ __('delete') }}</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-warehouse fa-3x text-muted mb-3"></i>
                                    <h5>{{ __('no_warehouses_found') }}</h5>
                                    <p class="text-muted">{{ __('no_warehouses_found_description') }}</p>
                                    <a href="{{ route('admin.warehouses.create') }}" class="btn btn-primary mt-2">
                                        <i class="fas fa-plus me-1"></i> {{ __('add_first_warehouse') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- ترقيم الصفحات -->
        <div class="mt-4">
            {{ $warehouses->appends(request()->all())->links() }}
        </div>
    </div>
</div>
@endsection 