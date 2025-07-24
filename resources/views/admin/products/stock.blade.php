@extends('layouts.admin')

@section('title', 'إدارة مخزون المنتج')
@section('page-title', 'إدارة مخزون المنتج: ' . $product->name)

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">المنتجات</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.products.show', $product->id) }}">{{ $product->name }}</a></li>
<li class="breadcrumb-item active">إدارة المخزون</li>
@endsection

@section('content')
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-boxes me-2 text-primary"></i>
                        إدارة مخزون المنتج
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                @if($product->main_image)
                                    <img src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                    <div class="rounded me-3 bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="fas fa-box text-muted fa-2x"></i>
                                    </div>
                                @endif
                                <div>
                                    <h4 class="mb-1">{{ $product->name }}</h4>
                                    <div class="text-muted">{{ $product->sku }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('admin.reports.stock-movements', ['product_id' => $product->id]) }}" class="btn btn-outline-primary">
                                <i class="fas fa-history me-1"></i> سجل حركات المخزون
                            </a>
                        </div>
                    </div>

                    <form action="{{ route('admin.products.stock.update', $product->id) }}" method="POST">
                        @csrf
                        
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>المستودع</th>
                                        <th width="200">الكمية الحالية</th>
                                        <th width="200">الكمية الجديدة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($warehouses as $warehouse)
                                        @php
                                            $stock = $product->stocks->where('warehouse_id', $warehouse->id)->first();
                                            $currentQuantity = $stock ? $stock->quantity : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input warehouse-checkbox" type="checkbox" id="warehouse-{{ $warehouse->id }}" data-warehouse-id="{{ $warehouse->id }}">
                                                    <label class="form-check-label fw-medium" for="warehouse-{{ $warehouse->id }}">
                                                        {{ $warehouse->name }}
                                                        <div class="text-muted small">{{ $warehouse->address }}</div>
                                                    </label>
                                                </div>
                                                <input type="hidden" name="stocks[{{ $loop->index }}][warehouse_id]" value="{{ $warehouse->id }}">
                                            </td>
                                            <td>
                                                <span class="badge {{ $currentQuantity > 5 ? 'bg-success' : ($currentQuantity > 0 ? 'bg-warning' : 'bg-danger') }} fs-6">
                                                    {{ number_format($currentQuantity) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="number" name="stocks[{{ $loop->index }}][quantity]" class="form-control" value="{{ $currentQuantity }}" min="0">
                                                    <span class="input-group-text">وحدة</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            ملاحظة: سيتم تسجيل أي تغيير في الكمية كحركة مخزون جديدة.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 