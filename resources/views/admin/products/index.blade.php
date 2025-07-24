@extends('layouts.admin')

@section('title', trans('products'))
@section('page-title', trans('products_management'))

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 d-flex align-items-center">
            <i class="fas fa-box me-2 text-primary"></i>
            {{ trans('products_list') }}
        </h5>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> {{ trans('add_product') }}
        </a>
    </div>
    
    <div class="card-body">
        <!-- فلاتر البحث -->
        <div class="mb-4">
            <form action="{{ route('admin.products.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ trans('search') }}..." 
                            class="form-control">
                    </div>
                </div>
                
                <div class="col-md-2">
                    <select name="category_id" class="form-select">
                        <option value="">{{ trans('all_categories') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <select name="country_id" class="form-select">
                        <option value="">{{ trans('all_countries') }}</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">{{ trans('all_statuses') }}</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ trans('active') }}</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ trans('inactive') }}</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <div class="d-flex">
                        <button type="submit" class="btn btn-outline-primary me-2">
                            <i class="fas fa-filter me-1"></i> {{ trans('filter') }}
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-1"></i> {{ trans('reset') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- جدول المنتجات -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col" width="80">{{ trans('image') }}</th>
                        <th scope="col">{{ trans('product') }}</th>
                        <th scope="col">{{ trans('sku') }}</th>
                        <th scope="col">{{ trans('category') }}</th>
                        <th scope="col">{{ trans('countries') }}</th>
                        <th scope="col">{{ trans('status') }}</th>
                        <th scope="col" class="text-end">{{ trans('actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>
                                @if ($product->main_image)
                                    <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-image text-secondary"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-medium">{{ $product->name }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $product->sku }}</span>
                            </td>
                            <td>
                                @if($product->category)
                                    <span class="badge bg-info text-white">{{ $product->category->name }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ trans('uncategorized') }}</span>
                                @endif
                            </td>
                            <td>
                                @if(count($product->countries) > 0)
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($product->countries->take(3) as $country)
                                            <span class="badge bg-primary">{{ $country->name }}</span>
                                        @endforeach
                                        @if(count($product->countries) > 3)
                                            <span class="badge bg-secondary">+{{ count($product->countries) - 3 }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="badge bg-warning text-dark">{{ trans('no_countries') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($product->status == 'active')
                                    <span class="badge bg-success">{{ trans('active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ trans('inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.products.show', $product->slug) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="{{ trans('view') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product->slug) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="{{ trans('edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product->slug) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger delete-btn" data-bs-toggle="tooltip" title="{{ trans('delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">{{ trans('no_products_available') }}</h5>
                                    <p class="text-muted">{{ trans('add_your_first_product') }}</p>
                                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary mt-2">
                                        <i class="fas fa-plus me-1"></i> {{ trans('add_product') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- ترقيم الصفحات -->
        <div class="d-flex justify-content-center mt-4">
            {{ $products->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th {
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .badge {
        font-weight: 500;
    }
    
    .delete-btn:hover {
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تفعيل tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                delay: { show: 500, hide: 100 }
            });
        });
        
        // تأكيد الحذف
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            const deleteBtn = form.querySelector('.delete-btn');
            deleteBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (confirm("{{ trans('confirm_delete_product') }}")) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush 