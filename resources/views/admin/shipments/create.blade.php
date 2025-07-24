@extends('layouts.admin')

@section('title', 'إنشاء شحنة جديدة')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">إنشاء شحنة جديدة</h1>
    
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.shipments.index') }}">الشحنات</a></li>
        <li class="breadcrumb-item active">إنشاء شحنة</li>
    </ol>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-shipping-fast me-1"></i>
            بيانات الشحنة
        </div>
        <div class="card-body">
            <form action="{{ route('admin.shipments.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h5 class="mb-3">معلومات الطلب</h5>
                        
                        @if($order)
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">رقم الطلب:</label>
                                <p>{{ $order->order_number }}</p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">العميل:</label>
                                <p>{{ $order->customer->name }}</p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">عنوان الشحن:</label>
                                <p>
                                    {{ $order->shipping_name }}<br>
                                    {{ $order->shipping_address_line1 }}<br>
                                    @if($order->shipping_address_line2)
                                        {{ $order->shipping_address_line2 }}<br>
                                    @endif
                                    {{ $order->shipping_city }}, {{ $order->shipping_state }}<br>
                                    {{ $order->shipping_postal_code }}<br>
                                    {{ $order->shipping_country }}
                                </p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">هاتف:</label>
                                <p>{{ $order->shipping_phone }}</p>
                            </div>
                        @else
                            <div class="mb-3">
                                <label for="order_id" class="form-label">اختر الطلب</label>
                                <select id="order_id" name="order_id" class="form-select @error('order_id') is-invalid @enderror" required>
                                    <option value="">-- اختر الطلب --</option>
                                    <!-- هنا يجب وضع كود لعرض الطلبات المتاحة -->
                                </select>
                                @error('order_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    </div>
                    
                    <div class="col-md-6">
                        <h5 class="mb-3">معلومات الشحن</h5>
                        
                        <div class="mb-3">
                            <label for="warehouse_id" class="form-label">المخزن</label>
                            <select id="warehouse_id" name="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror" required>
                                <option value="">-- اختر المخزن --</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }} ({{ $warehouse->city }}, {{ $warehouse->country->name }})</option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="shipping_company_id" class="form-label">شركة الشحن</label>
                            <select id="shipping_company_id" name="shipping_company_id" class="form-select @error('shipping_company_id') is-invalid @enderror" required>
                                <option value="">-- اختر شركة الشحن --</option>
                                @foreach($shippingCompanies as $company)
                                    <option value="{{ $company->id }}" data-has-api="{{ $company->has_api_integration ? 'true' : 'false' }}">
                                        {{ $company->name }} {{ $company->has_api_integration ? '(يدعم الربط التلقائي)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('shipping_company_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3" id="api_integration_section" style="display: none;">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="create_in_shipping_company" name="create_in_shipping_company" value="1">
                                <label class="form-check-label" for="create_in_shipping_company">
                                    إنشاء الشحنة تلقائياً في نظام شركة الشحن
                                </label>
                                <small class="form-text text-muted d-block">
                                    سيتم إنشاء الشحنة تلقائياً في نظام شركة الشحن والحصول على رقم التتبع.
                                </small>
                            </div>
                        </div>
                        
                        <div class="mb-3" id="manual_tracking_section">
                            <label for="tracking_number" class="form-label">رقم التتبع</label>
                            <input type="text" id="tracking_number" name="tracking_number" class="form-control @error('tracking_number') is-invalid @enderror">
                            @error('tracking_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                في حالة عدم إنشاء الشحنة تلقائياً، يمكنك إضافة رقم التتبع يدوياً.
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_cod" name="is_cod" value="1">
                                <label class="form-check-label" for="is_cod">
                                    الدفع عند الاستلام (COD)
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3" id="cod_amount_section" style="display: none;">
                            <label for="cod_amount" class="form-label">مبلغ التحصيل</label>
                            <div class="input-group">
                                <input type="number" step="0.01" id="cod_amount" name="cod_amount" class="form-control @error('cod_amount') is-invalid @enderror">
                                <span class="input-group-text">{{ $order ? $order->currency : 'SAR' }}</span>
                            </div>
                            @error('cod_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">ملاحظات</label>
                            <textarea id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"></textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                @if($order && $order->items)
                    <h5 class="mb-3">المنتجات المطلوب شحنها</h5>
                    
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>المنتج</th>
                                    <th>SKU</th>
                                    <th>الكمية المطلوبة</th>
                                    <th>الكمية المراد شحنها</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>{{ $item->product_name }}</td>
                                        <td>{{ $item->sku }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>
                                            <input type="number" 
                                                   name="items[{{ $item->id }}][quantity]" 
                                                   class="form-control shipment-item-quantity" 
                                                   min="0" 
                                                   max="{{ $item->quantity }}" 
                                                   value="{{ $item->quantity }}" 
                                                   required>
                                            <input type="hidden" 
                                                   name="items[{{ $item->id }}][order_item_id]" 
                                                   value="{{ $item->id }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.shipments.index') }}" class="btn btn-secondary me-2">إلغاء</a>
                    <button type="submit" class="btn btn-primary">إنشاء الشحنة</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mostrar/ocultar sección de integración API
        const shippingCompanySelect = document.getElementById('shipping_company_id');
        const apiIntegrationSection = document.getElementById('api_integration_section');
        const manualTrackingSection = document.getElementById('manual_tracking_section');
        
        shippingCompanySelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const hasApi = selectedOption.getAttribute('data-has-api') === 'true';
            
            apiIntegrationSection.style.display = hasApi ? 'block' : 'none';
            
            // Si se selecciona crear con API, ocultar el campo manual de seguimiento
            const createInShippingCompany = document.getElementById('create_in_shipping_company');
            if (hasApi && createInShippingCompany.checked) {
                manualTrackingSection.style.display = 'none';
            } else {
                manualTrackingSection.style.display = 'block';
            }
        });
        
        // Manejar cambio en checkbox de crear en API
        const createInShippingCompany = document.getElementById('create_in_shipping_company');
        createInShippingCompany.addEventListener('change', function() {
            if (this.checked) {
                manualTrackingSection.style.display = 'none';
            } else {
                manualTrackingSection.style.display = 'block';
            }
        });
        
        // Mostrar/ocultar sección de monto COD
        const isCodCheckbox = document.getElementById('is_cod');
        const codAmountSection = document.getElementById('cod_amount_section');
        
        isCodCheckbox.addEventListener('change', function() {
            codAmountSection.style.display = this.checked ? 'block' : 'none';
        });
        
        // Si hay un orden preseleccionado, establecer el valor del monto COD
        @if($order)
            document.getElementById('cod_amount').value = '{{ $order->total_amount }}';
        @endif
    });
</script>
@endpush 