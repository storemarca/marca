@extends('layouts.admin')

@section('title', 'إعدادات الشحن')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            @include('admin.settings.partials.sidebar')
        </div>
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">إعدادات الشحن</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.shipping.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <h6 class="fw-bold">الإعدادات العامة للشحن</h6>
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="shipping_enabled" class="form-label">تفعيل الشحن</label>
                                        <select name="shipping_enabled" id="shipping_enabled" class="form-select">
                                            <option value="1" {{ ($settings['shipping_enabled'] ?? '') == '1' ? 'selected' : '' }}>مفعل</option>
                                            <option value="0" {{ ($settings['shipping_enabled'] ?? '') == '0' ? 'selected' : '' }}>معطل</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="default_shipping_company" class="form-label">شركة الشحن الافتراضية</label>
                                        <select name="default_shipping_company" id="default_shipping_company" class="form-select">
                                            <option value="">-- اختر شركة الشحن --</option>
                                            @foreach($shippingCompanies ?? [] as $company)
                                                <option value="{{ $company->id }}" {{ ($settings['default_shipping_company'] ?? '') == $company->id ? 'selected' : '' }}>
                                                    {{ $company->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="free_shipping_min_amount" class="form-label">الحد الأدنى للشحن المجاني</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control" id="free_shipping_min_amount" name="free_shipping_min_amount" value="{{ $settings['free_shipping_min_amount'] ?? '' }}">
                                            <span class="input-group-text">ريال</span>
                                        </div>
                                        <small class="form-text text-muted">اترك فارغاً لتعطيل الشحن المجاني</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="default_shipping_cost" class="form-label">تكلفة الشحن الافتراضية</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control" id="default_shipping_cost" name="default_shipping_cost" value="{{ $settings['default_shipping_cost'] ?? '' }}">
                                            <span class="input-group-text">ريال</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="fw-bold">خيارات التتبع</h6>
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="enable_tracking" class="form-label">تفعيل تتبع الشحنات</label>
                                        <select name="enable_tracking" id="enable_tracking" class="form-select">
                                            <option value="1" {{ ($settings['enable_tracking'] ?? '') == '1' ? 'selected' : '' }}>مفعل</option>
                                            <option value="0" {{ ($settings['enable_tracking'] ?? '') == '0' ? 'selected' : '' }}>معطل</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tracking_page_enabled" class="form-label">صفحة تتبع الشحنات</label>
                                        <select name="tracking_page_enabled" id="tracking_page_enabled" class="form-select">
                                            <option value="1" {{ ($settings['tracking_page_enabled'] ?? '') == '1' ? 'selected' : '' }}>مفعلة</option>
                                            <option value="0" {{ ($settings['tracking_page_enabled'] ?? '') == '0' ? 'selected' : '' }}>معطلة</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="fw-bold">المستودعات</h6>
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="default_warehouse" class="form-label">المستودع الافتراضي</label>
                                        <select name="default_warehouse" id="default_warehouse" class="form-select">
                                            <option value="">-- اختر المستودع --</option>
                                            @foreach($warehouses ?? [] as $warehouse)
                                                <option value="{{ $warehouse->id }}" {{ ($settings['default_warehouse'] ?? '') == $warehouse->id ? 'selected' : '' }}>
                                                    {{ $warehouse->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 