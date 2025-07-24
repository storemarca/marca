@extends('layouts.admin')

@section('title', 'تفاصيل المنتج')
@section('page-title', 'تفاصيل المنتج: ' . $product->name)

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">المنتجات</a></li>
<li class="breadcrumb-item active">{{ $product->name }}</li>
@endsection

@section('actions')
<div class="btn-group">
    <a href="{{ route('admin.products.edit', $product->slug) }}" class="btn btn-primary">
        <i class="fas fa-edit me-1"></i> تعديل
    </a>
    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="visually-hidden">المزيد</span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        <li>
            <a class="dropdown-item" href="{{ route('admin.products.stock', $product->slug) }}">
                <i class="fas fa-boxes me-2 text-primary"></i> إدارة المخزون
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('admin.products.prices', $product->slug) }}">
                <i class="fas fa-tag me-2 text-success"></i> إدارة الأسعار
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('admin.reports.stock-movements', ['product_id' => $product->id]) }}">
                <i class="fas fa-history me-2 text-info"></i> سجل حركات المخزون
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item" href="{{ route('user.products.show', $product->slug) }}" target="_blank">
                <i class="fas fa-external-link-alt me-2 text-primary"></i> عرض في الموقع
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); if(confirm('هل أنت متأكد من حذف هذا المنتج؟')) document.getElementById('delete-product').submit();">
                <i class="fas fa-trash me-2 text-danger"></i> حذف المنتج
            </a>
            <form id="delete-product" action="{{ route('admin.products.destroy', $product->slug) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        </li>
    </ul>
</div>
@endsection

@section('content')
    <!-- حالة المنتج -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center">
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
        <div>
                        <span class="badge {{ $product->status == 'active' ? 'bg-success' : 'bg-danger' }} fs-6 px-3 py-2">
                {{ $product->status == 'active' ? 'نشط' : 'غير نشط' }}
            </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- معلومات المنتج -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2 text-primary"></i>
                        معلومات المنتج
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-1">اسم المنتج</h6>
                            <div class="fs-5">{{ $product->name }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-1">رمز المنتج (SKU)</h6>
                            <div class="fs-5">{{ $product->sku }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-1">الفئة</h6>
                            <div class="fs-5">{{ $product->category->name ?? 'غير محدد' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-1">الوزن</h6>
                            <div class="fs-5">{{ $product->weight }} كجم</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-1">تكلفة المنتج</h6>
                            <div class="fs-5">{{ number_format($product->cost, 2) }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-1">تاريخ الإضافة</h6>
                            <div class="fs-5">{{ $product->created_at->format('Y-m-d H:i') }}</div>
                        </div>
                        </div>
                    
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">الوصف</h6>
                        <div class="border rounded p-3 bg-light">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- صور المنتج -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-images me-2 text-primary"></i>
                        صور المنتج
                    </h5>
                </div>
                <div class="card-body">
                    @if($product->images && is_array($product->images) && count($product->images) > 0)
                        <div id="product-images-carousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @foreach($product->images as $index => $image)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <img src="{{ asset('storage/' . $image) }}" class="d-block w-100 rounded" alt="{{ $product->name }}" style="height: 250px; object-fit: contain;">
                                        @if($index === 0)
                                            <div class="position-absolute top-0 end-0 m-2">
                                                <span class="badge bg-success">الصورة الرئيسية</span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            @if(count($product->images) > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#product-images-carousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">السابق</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#product-images-carousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">التالي</span>
                                </button>
                            @endif
                        </div>
                        
                        <div class="row mt-3">
                            @foreach($product->images as $index => $image)
                                <div class="col-3 mb-2">
                                    <img src="{{ asset('storage/' . $image) }}" class="img-thumbnail cursor-pointer" alt="{{ $product->name }}" data-bs-target="#product-images-carousel" data-bs-slide-to="{{ $index }}" style="height: 60px; object-fit: cover;">
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-image fa-4x text-muted mb-3"></i>
                            <p class="text-muted">لا توجد صور لهذا المنتج</p>
                        </div>
                    @endif
                    </div>
                </div>
            </div>
            
            <!-- الأسعار حسب البلد -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tag me-2 text-primary"></i>
                        الأسعار حسب البلد
                    </h5>
                    <a href="{{ route('admin.products.prices', $product->slug) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit me-1"></i> تعديل الأسعار
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>البلد</th>
                                    <th>السعر</th>
                                    <th>متاح للبيع</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($product->prices as $price)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="me-2">{{ $price->country->name ?? 'غير محدد' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ number_format($price->price, 2) }}</span>
                                            <span class="text-muted">{{ $price->country->currency_symbol ?? '' }}</span>
                                        </td>
                                        <td>
                                            @if($price->is_active)
                                                <span class="badge bg-success">نعم</span>
                                            @else
                                                <span class="badge bg-danger">لا</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            لم يتم تحديد أي بلد لهذا المنتج
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
            
            <!-- المخزون -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-boxes me-2 text-primary"></i>
                        المخزون
                    </h5>
                    <a href="{{ route('admin.products.stock', $product->slug) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit me-1"></i> تعديل المخزون
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>المخزن</th>
                                    <th>الكمية المتاحة</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($product->stocks as $stock)
                                    <tr>
                                        <td>{{ $stock->warehouse->name ?? 'غير محدد' }}</td>
                                        <td>
                                            <span class="fw-bold">{{ number_format($stock->quantity) }}</span>
                                        </td>
                                        <td>
                                            @if($stock->quantity <= 0)
                                                <span class="badge bg-danger">نفذ من المخزون</span>
                                            @elseif($stock->quantity <= 5)
                                                <span class="badge bg-warning">منخفض المخزون</span>
                                            @else
                                                <span class="badge bg-success">متوفر</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            لا يوجد مخزون لهذا المنتج
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- حركات المخزون الأخيرة -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2 text-primary"></i>
                        آخر حركات المخزون
                    </h5>
                    <a href="{{ route('admin.reports.stock-movements', ['product_id' => $product->id]) }}" class="btn btn-sm btn-outline-primary">
                        عرض الكل
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>المخزن</th>
                                    <th>نوع الحركة</th>
                                    <th>الكمية</th>
                                    <th>الرصيد بعد</th>
                                    <th>المرجع</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $stockMovements = \App\Models\StockMovement::where('product_id', $product->id)
                                        ->with('warehouse')
                                        ->orderBy('created_at', 'desc')
                                        ->limit(5)
                                        ->get();
                                @endphp
                                
                                @forelse($stockMovements as $movement)
                                    <tr>
                                        <td>
                                            <div>{{ $movement->created_at->format('Y-m-d') }}</div>
                                            <small class="text-muted">{{ $movement->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td>{{ $movement->warehouse->name ?? 'غير محدد' }}</td>
                                        <td>
                                            @if($movement->type == 'in')
                                                <span class="badge bg-success">وارد</span>
                                            @elseif($movement->type == 'out')
                                                <span class="badge bg-danger">صادر</span>
                                            @elseif($movement->type == 'adjustment')
                                                <span class="badge bg-warning">تعديل</span>
                                            @elseif($movement->type == 'transfer')
                                                <span class="badge bg-info">نقل</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $movement->type }}</span>
                                        @endif
                                        </td>
                                        <td>
                                            <span class="{{ $movement->type == 'in' || $movement->quantity > 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                                {{ $movement->type == 'in' || $movement->quantity > 0 ? '+' : '' }}{{ number_format($movement->quantity) }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($movement->balance_after) }}</td>
                                        <td>
                                            @if($movement->reference_type == 'order')
                                                <a href="{{ route('admin.orders.show', $movement->reference_id) }}" class="badge bg-primary text-decoration-none">
                                                    <i class="fas fa-shopping-cart me-1"></i> طلب #{{ $movement->reference_id }}
                                                </a>
                                            @elseif($movement->reference_type == 'purchase')
                                                <a href="{{ route('admin.purchase-orders.show', $movement->reference_id) }}" class="badge bg-info text-decoration-none">
                                                    <i class="fas fa-truck-loading me-1"></i> شراء #{{ $movement->reference_id }}
                                                </a>
                                            @elseif($movement->reference_type == 'adjustment')
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-edit me-1"></i> تعديل مخزون
                                                </span>
                                            @elseif($movement->reference_type == 'transfer')
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-exchange-alt me-1"></i> نقل مخزون
                                                </span>
                        @else
                                                <span class="badge bg-secondary">{{ $movement->reference_type ?? 'غير محدد' }}</span>
                        @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            لا توجد حركات مخزون لهذا المنتج
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

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush 