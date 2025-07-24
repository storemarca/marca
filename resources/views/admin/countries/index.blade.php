@extends('layouts.admin')

@section('title', __('countries'))
@section('page-title', __('countries_management'))

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 d-flex align-items-center">
            <i class="fas fa-globe me-2 text-primary"></i>
            {{ __('countries_list') }}
        </h5>
        <a href="{{ route('admin.countries.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> {{ __('add_country') }}
        </a>
    </div>
    
    <div class="card-body">
        <!-- فلاتر البحث -->
        <div class="mb-4">
            <form action="{{ route('admin.countries.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="{{ __('search') }}" value="{{ request('search') }}">
                    </div>
                </div>
                
                <div class="col-md-4">
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
        
        <!-- جدول البلدان -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('id') }}</th>
                        <th>{{ __('name') }}</th>
                        <th>{{ __('country_code') }}</th>
                        <th>{{ __('currency_information') }}</th>
                        <th>{{ __('tax_rate') }}</th>
                        <th>{{ __('status') }}</th>
                        <th>{{ __('actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($countries as $country)
                        <tr>
                            <td>{{ $country->id }}</td>
                            <td>
                                <a href="{{ route('admin.countries.show', $country) }}" class="fw-bold text-decoration-none">
                                    {{ $country->name }}
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $country->code }}</span>
                            </td>
                            <td>
                                <div><strong>{{ $country->currency_code }}</strong> ({{ $country->currency_symbol }})</div>
                            </td>
                            <td>{{ $country->tax_rate }}%</td>
                            <td>
                                <span class="badge bg-{{ $country->is_active ? 'success' : 'danger' }}">
                                    {{ $country->is_active ? __('active') : __('inactive') }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.countries.show', $country) }}" class="btn btn-info" title="{{ __('view') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.countries.edit', $country) }}" class="btn btn-primary" title="{{ __('edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-{{ $country->is_active ? 'warning' : 'success' }}" 
                                        data-bs-toggle="modal" data-bs-target="#statusModal{{ $country->id }}" 
                                        title="{{ $country->is_active ? __('deactivate') : __('activate') }}">
                                        <i class="fas fa-{{ $country->is_active ? 'ban' : 'check' }}"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger" 
                                        data-bs-toggle="modal" data-bs-target="#deleteModal{{ $country->id }}" 
                                        title="{{ __('delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <!-- Modal تغيير الحالة -->
                                <div class="modal fade" id="statusModal{{ $country->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ $country->is_active ? __('deactivate_country') : __('activate_country') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>{{ $country->is_active ? __('confirm_deactivate_country') : __('confirm_activate_country') }}</p>
                                                <p class="fw-bold">{{ $country->name }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('cancel') }}</button>
                                                <form action="{{ route('admin.countries.toggle-status', $country) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-{{ $country->is_active ? 'warning' : 'success' }}">
                                                        {{ $country->is_active ? __('deactivate') : __('activate') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Modal الحذف -->
                                <div class="modal fade" id="deleteModal{{ $country->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('delete_country') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>{{ __('confirm_delete_country') }}</p>
                                                <p class="fw-bold">{{ $country->name }}</p>
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    {{ __('country_delete_warning') }}
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('cancel') }}</button>
                                                <form action="{{ route('admin.countries.destroy', $country) }}" method="POST">
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
                                    <i class="fas fa-globe fa-3x text-muted mb-3"></i>
                                    <h5>{{ __('no_countries_found') }}</h5>
                                    <p class="text-muted">{{ __('no_countries_found_description') }}</p>
                                    <a href="{{ route('admin.countries.create') }}" class="btn btn-primary mt-2">
                                        <i class="fas fa-plus me-1"></i> {{ __('add_first_country') }}
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
            {{ $countries->appends(request()->all())->links() }}
        </div>
    </div>
</div>
@endsection 