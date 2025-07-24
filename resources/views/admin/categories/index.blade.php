@extends('layouts.admin')

@section('title', trans('categories'))
@section('page-title', trans('categories_management'))

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 d-flex align-items-center">
            <i class="fas fa-tags me-2 text-primary"></i>
            {{ trans('categories_list') }}
        </h5>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> {{ trans('add_category') }}
        </a>
    </div>
    
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col" width="50">#</th>
                        <th scope="col">{{ trans('name') }}</th>
                        <th scope="col">{{ trans('parent_category') }}</th>
                        <th scope="col" width="100">{{ trans('status') }}</th>
                        <th scope="col" width="100">{{ trans('sort_order') }}</th>
                        <th scope="col" width="150" class="text-center">{{ trans('actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $index => $category)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($category->image)
                                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                            <i class="fas fa-folder text-secondary"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-medium">{{ $category->name }}</div>
                                        @if($category->slug)
                                            <small class="text-muted">{{ $category->slug }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($category->parent)
                                    <span class="badge bg-info">{{ $category->parent->name }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ trans('main_category') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($category->is_active)
                                    <span class="badge bg-success">{{ trans('active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ trans('inactive') }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">{{ $category->sort_order }}</span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.categories.show', $category->id) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="{{ trans('view') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="{{ trans('edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline delete-form">
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
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">{{ trans('no_categories_available') }}</h5>
                                    <p class="text-muted">{{ trans('add_your_first_category') }}</p>
                                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mt-2">
                                        <i class="fas fa-plus me-1"></i> {{ trans('add_category') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(isset($categories) && method_exists($categories, 'links') && $categories->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $categories->links() }}
            </div>
        @endif
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
                
                if (confirm("{{ trans('confirm_delete_category') }}")) {
                    form.submit();
                }
            });
        });
        
        // إخفاء التنبيه بعد 5 ثوانٍ
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    });
</script>
@endpush 