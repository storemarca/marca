@extends('layouts.admin')

@section('title', 'إدارة أسعار المنتج')
@section('page-title', 'إدارة أسعار المنتج: ' . $product->name)

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">المنتجات</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.products.show', $product->id) }}">{{ $product->name }}</a></li>
<li class="breadcrumb-item active">إدارة الأسعار</li>
@endsection

@section('content')
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tag me-2 text-primary"></i>
                        إدارة أسعار المنتج
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                @if($product->images && is_array($product->images) && count($product->images) > 0)
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
                        <div class="col-md-6">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>تكلفة المنتج:</strong> {{ number_format($product->cost, 2) }}
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.products.prices.update', $product->id) }}" method="POST">
                        @csrf
                        
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>البلد</th>
                                        <th width="200">السعر</th>
                                        <th width="200">سعر العرض</th>
                                        <th width="100">متاح للبيع</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($countries as $country)
                                        @php
                                            $price = $product->prices->where('country_id', $country->id)->first();
                                            $currentPrice = $price ? $price->price : 0;
                                            $currentSalePrice = $price ? $price->sale_price : null;
                                            $isActive = $price ? $price->is_active : true;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $country->name }}</div>
                                                <div class="text-muted small">{{ $country->currency_code ?? '' }} {{ $country->currency_symbol ? '('.$country->currency_symbol.')' : '' }}</div>
                                                <input type="hidden" name="prices[{{ $loop->index }}][country_id]" value="{{ $country->id }}">
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="number" name="prices[{{ $loop->index }}][price]" class="form-control" value="{{ $currentPrice }}" min="0" step="0.01" required>
                                                    <span class="input-group-text">{{ $country->currency_symbol ?? '' }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="number" name="prices[{{ $loop->index }}][sale_price]" class="form-control" value="{{ $currentSalePrice }}" min="0" step="0.01">
                                                    <span class="input-group-text">{{ $country->currency_symbol ?? '' }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input" type="checkbox" name="prices[{{ $loop->index }}][is_active]" value="1" {{ $isActive ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            ملاحظة: يجب تعيين سعر للمنتج في كل بلد ترغب في بيعه فيه. إذا كان السعر 0، فلن يكون المنتج متاحًا للبيع في هذا البلد.
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