@extends('layouts.user')

@section('title', __('order_details'))

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 fw-bold text-primary">{{ __('order_details') }}</h1>
                
                @if(!isset($isGuestTracking))
                <div>
                    <a href="{{ route('user.orders.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i> {{ __('back_to_orders') }}
                    </a>
                </div>
                @else
                <div>
                    <a href="{{ route('orders.track') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i> {{ __('back_to_tracking') }}
                    </a>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Order Summary Header Card -->
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm order-header-card">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center text-md-start mb-3 mb-md-0">
                            <div class="order-number-badge">
                                <span class="small text-muted d-block">{{ __('order_number') }}</span>
                                <h2 class="mb-0 fw-bold">#{{ $order->order_number }}</h2>
                            </div>
                        </div>
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <div class="order-date">
                                <span class="small text-muted d-block">{{ __('order_date') }}</span>
                                <h5 class="mb-0"><i class="far fa-calendar-alt me-2 text-primary"></i>{{ $order->created_at->format('Y-m-d') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <div class="order-status">
                                <span class="small text-muted d-block">{{ __('status') }}</span>
                                <span class="badge status-badge fs-6 {{ $order->status == 'delivered' ? 'bg-success' : ($order->status == 'cancelled' ? 'bg-danger' : 'bg-warning') }}">
                                    <i class="fas {{ $order->status == 'delivered' ? 'fa-check-circle' : ($order->status == 'cancelled' ? 'fa-times-circle' : 'fa-clock') }} me-1"></i>
                                    {{ $order->status_text }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-3 text-center text-md-end">
                            <div class="order-total">
                                <span class="small text-muted d-block">{{ __('total_amount') }}</span>
                                <h4 class="mb-0 fw-bold text-primary">{{ $order->currency_symbol }} {{ number_format($order->total_amount, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <!-- Order Tracking Timeline Card -->
            <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-route me-2"></i>{{ __('order_tracking') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="order-timeline p-4">
                        @php
                            $progressPercentage = 0;
                            switch($order->status) {
                                case 'pending':
                                    $progressPercentage = 25;
                                    break;
                                case 'processing':
                                    $progressPercentage = 50;
                                    break;
                                case 'shipped':
                                    $progressPercentage = 75;
                                    break;
                                case 'delivered':
                                    $progressPercentage = 100;
                                    break;
                                case 'cancelled':
                                    $progressPercentage = 0;
                                    break;
                                default:
                                    $progressPercentage = 25;
                            }
                        @endphp
                        
                        <div class="progress-container">
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progressPercentage }}%" aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            
                            <div class="timeline-points d-flex justify-content-between mt-2">
                                <div class="timeline-point {{ $order->status != 'cancelled' ? 'active' : '' }}">
                                    <div class="point-icon">
                                        <i class="fas fa-clipboard-check"></i>
                                    </div>
                                    <div class="point-label">{{ __('pending') }}</div>
                                    @if($order->created_at)
                                        <div class="point-date">{{ $order->created_at->format('d M') }}</div>
                                    @endif
                                </div>
                                
                                <div class="timeline-point {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'active' : '' }}">
                                    <div class="point-icon">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div class="point-label">{{ __('processing') }}</div>
                                </div>
                                
                                <div class="timeline-point {{ in_array($order->status, ['shipped', 'delivered']) ? 'active' : '' }}">
                                    <div class="point-icon">
                                        <i class="fas fa-shipping-fast"></i>
                                    </div>
                                    <div class="point-label">{{ __('shipped') }}</div>
                                    @if($order->shipped_at)
                                        <div class="point-date">{{ \Carbon\Carbon::parse($order->shipped_at)->format('d M') }}</div>
                                    @endif
                                </div>
                                
                                <div class="timeline-point {{ $order->status == 'delivered' ? 'active' : '' }}">
                                    <div class="point-icon">
                                        <i class="fas fa-home"></i>
                                    </div>
                                    <div class="point-label">{{ __('delivered') }}</div>
                                    @if($order->delivered_at)
                                        <div class="point-date">{{ \Carbon\Carbon::parse($order->delivered_at)->format('d M') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Details Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>{{ __('order_details') }}</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-card h-100">
                                <div class="info-card-header">
                                    <i class="fas fa-file-invoice text-primary"></i>
                            <h6>{{ __('order_info') }}</h6>
                                </div>
                                <div class="info-card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <span class="text-muted">{{ __('order_date') }}:</span>
                                            <span class="fw-medium">{{ $order->created_at->format('Y-m-d H:i') }}</span>
                                </li>
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <span class="text-muted">{{ __('payment_status') }}:</span>
                                    <span class="badge {{ $order->payment_status == 'paid' ? 'bg-success' : 'bg-warning' }}">
                                        {{ $order->payment_status }}
                                    </span>
                                </li>
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <span class="text-muted">{{ __('payment_method') }}:</span>
                                            <span class="fw-medium">{{ $order->payment_method_text }}</span>
                                        </li>
                                        @if($order->tracking_number)
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <span class="text-muted">{{ __('tracking_number') }}:</span>
                                            <span class="fw-medium">{{ $order->tracking_number }}</span>
                                        </li>
                                        @endif
                            </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-card h-100">
                                <div class="info-card-header">
                                    <i class="fas fa-map-marker-alt text-primary"></i>
                            <h6>{{ __('shipping_address') }}</h6>
                                </div>
                                <div class="info-card-body">
                                    <div class="address-card">
                                        <div class="name fw-bold mb-2">{{ $order->shipping_name }}</div>
                                        <div class="address mb-1">
                                            {{ $order->shipping_address_line1 }}
                                @if($order->shipping_address_line2)
                                                <br>{{ $order->shipping_address_line2 }}
                                @endif
                                        </div>
                                        <div class="city-state mb-1">
                                            {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}
                                        </div>
                                        <div class="country">{{ $order->shipping_country }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        </div>
                    </div>
                    
            <!-- Order Items Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-box-open me-2"></i>{{ __('ordered_items') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover order-items-table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('product') }}</th>
                                    <th>{{ __('price') }}</th>
                                    <th>{{ __('quantity') }}</th>
                                    <th class="text-end">{{ __('subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="product-image">
                                            @if($item->product && $item->product->image)
                                                    <img src="{{ asset($item->product->image) }}" alt="{{ $item->product_name }}" class="img-thumbnail">
                                            @else
                                                    <img src="{{ asset('images/product-placeholder.svg') }}" alt="{{ $item->product_name }}" class="img-thumbnail">
                                            @endif
                                            </div>
                                            <div class="product-info ms-3">
                                                <div class="product-name fw-bold">{{ $item->product_name }}</div>
                                                @if($item->options)
                                                    <div class="product-options small text-muted">
                                                        @foreach(json_decode($item->options) as $key => $value)
                                                            <span>{{ $key }}: {{ $value }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $order->currency_symbol }} {{ number_format($item->unit_price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-end">{{ $order->currency_symbol }} {{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="order-summary p-4 bg-light">
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                                <table class="table table-sm order-totals-table">
                                <tr>
                                    <td>{{ __('subtotal') }}:</td>
                                        <td class="text-end">{{ $order->currency_symbol }} {{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('shipping') }}:</td>
                                        <td class="text-end">{{ $order->currency_symbol }} {{ number_format($order->shipping_amount, 2) }}</td>
                                </tr>
                                @if($order->tax_amount > 0)
                                <tr>
                                    <td>{{ __('tax') }}:</td>
                                        <td class="text-end">{{ $order->currency_symbol }} {{ number_format($order->tax_amount, 2) }}</td>
                                </tr>
                                @endif
                                @if($order->discount_amount > 0)
                                    <tr class="discount-row">
                                    <td>{{ __('discount') }}:</td>
                                        <td class="text-end text-danger">-{{ $order->currency_symbol }} {{ number_format($order->discount_amount, 2) }}</td>
                                </tr>
                                @endif
                                    <tr class="total-row">
                                        <td class="fw-bold">{{ __('total') }}:</td>
                                        <td class="text-end fw-bold fs-5">{{ $order->currency_symbol }} {{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            @if($order->shipments->count() > 0)
                <!-- Shipment Information Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-gradient-info text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-shipping-fast me-2"></i>{{ __('shipment_info') }}</h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach($order->shipments as $shipment)
                            <div class="shipment-item p-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="shipment-header d-flex align-items-center mb-3">
                                    <div class="shipment-badge me-3">
                                        <span class="badge rounded-pill bg-secondary">{{ $loop->iteration }}</span>
                                    </div>
                                    <h6 class="mb-0">{{ __('shipment') }}</h6>
                                </div>
                                
                                <ul class="list-group list-group-flush shipment-details mb-3">
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span><i class="fas fa-check-circle me-2 text-info"></i>{{ __('status') }}</span>
                                        <span class="badge {{ $shipment->status == 'delivered' ? 'bg-success' : 'bg-info' }}">
                                            {{ $shipment->status }}
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span><i class="fas fa-truck me-2 text-info"></i>{{ __('shipping_company') }}</span>
                                        <span>{{ $shipment->shippingCompany->name }}</span>
                                    </li>
                                    @if($shipment->tracking_number)
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="fas fa-barcode me-2 text-info"></i>{{ __('tracking_number') }}</span>
                                            <span class="badge bg-light text-dark">{{ $shipment->tracking_number }}</span>
                                        </li>
                                    @endif
                                    @if($shipment->shipped_at)
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="fas fa-calendar-check me-2 text-info"></i>{{ __('shipped_date') }}</span>
                                            <span>{{ $shipment->shipped_at->format('Y-m-d') }}</span>
                                        </li>
                                    @endif
                                    @if($shipment->delivered_at)
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="fas fa-calendar-check me-2 text-success"></i>{{ __('delivered_date') }}</span>
                                            <span>{{ $shipment->delivered_at->format('Y-m-d') }}</span>
                                        </li>
                                    @endif
                                </ul>
                                
                                @if($shipment->tracking_url)
                                    <a href="{{ $shipment->tracking_url }}" target="_blank" class="btn btn-primary w-100">
                                        <i class="fas fa-external-link-alt me-1"></i> {{ __('track_shipment') }}
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            @if(!isset($isGuestTracking) && $order->isReturnable())
                <!-- Actions Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-gradient-secondary text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>{{ __('actions') }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-grid gap-3">
                            <a href="{{ route('user.returns.create', ['order_id' => $order->id]) }}" class="btn btn-danger">
                                <i class="fas fa-undo me-2"></i> {{ __('request_return') }}
                            </a>
                            <a href="{{ route('orders.track') }}?order_number={{ $order->order_number }}&order_token={{ $order->token }}" class="btn btn-info text-white">
                                <i class="fas fa-truck me-2"></i> {{ __('track_order') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif
            
            @if($order->notes)
                <!-- Order Notes Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-gradient-light py-3">
                        <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>{{ __('order_notes') }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="order-notes">
                            <i class="fas fa-quote-left text-muted opacity-25 fa-2x float-start me-2 mt-1"></i>
                            <p class="mb-0">{{ $order->notes }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* General Styles */
    body {
        background-color: #f8f9fa;
    }
    
    .card {
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    }
    
    .bg-gradient-info {
        background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%);
    }
    
    .bg-gradient-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
    }
    
    .bg-gradient-light {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    /* Order Header Card */
    .order-header-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-top: 4px solid #0d6efd;
    }
    
    .status-badge {
        padding: 8px 15px;
        border-radius: 50px;
        font-weight: 500;
    }
    
    /* Order Timeline */
    .order-timeline {
        position: relative;
    }
    
    .progress-container {
        padding: 20px 10px;
    }
    
    .timeline-points {
        margin-top: 30px;
    }
    
    .timeline-point {
        position: relative;
        text-align: center;
        width: 25%;
    }
    
    .timeline-point .point-icon {
        width: 40px;
        height: 40px;
        background-color: #e9ecef;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        border: 2px solid #ced4da;
        color: #6c757d;
        position: absolute;
        top: -50px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1;
        transition: all 0.3s ease;
    }
    
    .timeline-point.active .point-icon {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
        box-shadow: 0 0 0 5px rgba(40, 167, 69, 0.2);
    }
    
    .timeline-point .point-label {
        font-weight: 500;
        font-size: 0.9rem;
        color: #6c757d;
    }
    
    .timeline-point.active .point-label {
        color: #212529;
        font-weight: 600;
    }
    
    .timeline-point .point-date {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    /* Info Cards */
    .info-card {
        background-color: #fff;
        border-radius: 10px;
        overflow: hidden;
        height: 100%;
    }
    
    .info-card-header {
        padding: 15px 0;
        border-bottom: 1px solid #e9ecef;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }
    
    .info-card-header i {
        margin-right: 10px;
        font-size: 1.2rem;
    }
    
    .info-card-header h6 {
        margin-bottom: 0;
        font-weight: 600;
    }
    
    .info-card-body {
        padding: 0;
    }
    
    /* Address Card */
    .address-card {
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 8px;
    }
    
    /* Order Items Table */
    .order-items-table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
    }
    
    .order-items-table td {
        vertical-align: middle;
        padding: 1rem;
    }
    
    .product-image {
        width: 70px;
        height: 70px;
        overflow: hidden;
        border-radius: 8px;
        flex-shrink: 0;
    }
    
    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border: none;
    }
    
    .product-name {
        font-size: 1rem;
        margin-bottom: 5px;
    }
    
    .product-options span {
        display: inline-block;
        background-color: #f8f9fa;
        padding: 2px 8px;
        border-radius: 4px;
        margin-right: 5px;
        margin-bottom: 5px;
    }
    
    /* Order Summary */
    .order-totals-table td {
        padding: 10px 0;
        border-top: 1px solid #dee2e6;
    }
    
    .order-totals-table .discount-row {
        color: #dc3545;
    }
    
    .order-totals-table .total-row {
        border-top: 2px solid #212529;
    }
    
    /* Shipment Details */
    .shipment-item {
        transition: all 0.3s ease;
    }
    
    .shipment-item:hover {
        background-color: #f8f9fa;
    }
    
    .shipment-badge .badge {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .shipment-details .list-group-item {
        background-color: transparent;
        padding-top: 12px;
        padding-bottom: 12px;
        border-color: #e9ecef;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 767.98px) {
        .timeline-point .point-icon {
            width: 30px;
            height: 30px;
            font-size: 0.8rem;
        }
        
        .timeline-point .point-label {
            font-size: 0.8rem;
        }
        
        .product-image {
            width: 50px;
            height: 50px;
        }
        
        .product-name {
            font-size: 0.9rem;
        }
    }
</style>
@endpush
