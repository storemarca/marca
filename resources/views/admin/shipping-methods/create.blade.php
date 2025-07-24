@extends('layouts.admin')

@section('title', 'إضافة طريقة شحن جديدة')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إضافة طريقة شحن جديدة</h1>
        <a href="{{ route('admin.shipping-methods.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> العودة للقائمة
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">معلومات طريقة الشحن</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.shipping-methods.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">الاسم <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="code" class="form-label">الكود <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required>
                            <small class="form-text text-muted">يجب أن يكون الكود فريدًا (مثال: standard, express)</small>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="base_cost" class="form-label">التكلفة الأساسية <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" class="form-control @error('base_cost') is-invalid @enderror" id="base_cost" name="base_cost" value="{{ old('base_cost', 0) }}" required>
                                <span class="input-group-text">ريال</span>
                            </div>
                            <small class="form-text text-muted">التكلفة الافتراضية للشحن إذا لم يتم تحديد تكلفة للدولة</small>
                            @error('base_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="is_active" class="form-label">الحالة</label>
                            <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active">
                                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>مفعل</option>
                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>غير مفعل</option>
                            </select>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">الوصف</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="weight_based" name="weight_based" value="1" {{ old('weight_based') ? 'checked' : '' }}>
                                <label class="form-check-label" for="weight_based">
                                    حساب التكلفة حسب الوزن
                                </label>
                            </div>
                            <small class="form-text text-muted">تفعيل حساب تكلفة الشحن بناءً على وزن المنتجات</small>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="cost_per_kg" class="form-label">تكلفة الكيلوجرام</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" class="form-control @error('cost_per_kg') is-invalid @enderror" id="cost_per_kg" name="cost_per_kg" value="{{ old('cost_per_kg', 0) }}">
                                <span class="input-group-text">ريال</span>
                            </div>
                            @error('cost_per_kg')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="free_shipping_threshold" class="form-label">حد الشحن المجاني</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" class="form-control @error('free_shipping_threshold') is-invalid @enderror" id="free_shipping_threshold" name="free_shipping_threshold" value="{{ old('free_shipping_threshold', 0) }}">
                                <span class="input-group-text">ريال</span>
                            </div>
                            <small class="form-text text-muted">الحد الأدنى للطلب للحصول على شحن مجاني (0 لتعطيل هذه الميزة)</small>
                            @error('free_shipping_threshold')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <h5 class="mb-3">أسعار الشحن حسب الدولة</h5>
                <p class="text-muted mb-3">حدد تكلفة الشحن لكل دولة. إذا لم يتم تحديد دول، سيتم استخدام التكلفة الأساسية لجميع الدول.</p>
                
                <div class="table-responsive mb-3">
                    <table class="table table-bordered" id="countriesTable">
                        <thead>
                            <tr>
                                <th>الدولة</th>
                                <th>تكلفة الشحن</th>
                                <th>متاح</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- سيتم إضافة الصفوف ديناميكيًا -->
                        </tbody>
                    </table>
                </div>
                
                <div class="mb-3">
                    <button type="button" class="btn btn-success" id="addCountryBtn">
                        <i class="fas fa-plus"></i> إضافة دولة
                    </button>
                </div>
                
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">حفظ طريقة الشحن</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal إضافة دولة -->
<div class="modal fade" id="addCountryModal" tabindex="-1" aria-labelledby="addCountryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCountryModalLabel">إضافة دولة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="country_id" class="form-label">الدولة <span class="text-danger">*</span></label>
                    <select class="form-select" id="country_id" required>
                        <option value="">-- اختر الدولة --</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" data-name="{{ $country->name }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="country_cost" class="form-label">تكلفة الشحن <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" step="0.01" min="0" class="form-control" id="country_cost" value="0" required>
                        <span class="input-group-text">ريال</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="country_is_available" checked>
                        <label class="form-check-label" for="country_is_available">
                            متاح للشحن
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" id="saveCountryBtn">إضافة</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let countryRowCounter = 0;
        const addedCountries = new Set();
        
        // إظهار/إخفاء حقل تكلفة الكيلوجرام بناءً على خيار الوزن
        function toggleWeightBasedFields() {
            if ($('#weight_based').is(':checked')) {
                $('#cost_per_kg').prop('disabled', false);
            } else {
                $('#cost_per_kg').prop('disabled', true).val(0);
            }
        }
        
        // تشغيل الدالة عند تحميل الصفحة
        toggleWeightBasedFields();
        
        // تشغيل الدالة عند تغيير خيار الوزن
        $('#weight_based').change(toggleWeightBasedFields);
        
        // إضافة دولة جديدة
        $('#addCountryBtn').click(function() {
            $('#addCountryModal').modal('show');
        });
        
        // حفظ الدولة
        $('#saveCountryBtn').click(function() {
            const countryId = $('#country_id').val();
            const countryName = $('#country_id option:selected').data('name');
            const cost = $('#country_cost').val();
            const isAvailable = $('#country_is_available').is(':checked');
            
            if (!countryId || !countryName) {
                alert('يرجى اختيار دولة');
                return;
            }
            
            if (addedCountries.has(countryId)) {
                alert('هذه الدولة مضافة بالفعل');
                return;
            }
            
            addedCountries.add(countryId);
            
            const newRow = `
                <tr id="country-row-${countryRowCounter}">
                    <td>
                        ${countryName}
                        <input type="hidden" name="countries[${countryRowCounter}][country_id]" value="${countryId}">
                    </td>
                    <td>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" class="form-control" name="countries[${countryRowCounter}][cost]" value="${cost}" required>
                            <span class="input-group-text">ريال</span>
                        </div>
                    </td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="countries[${countryRowCounter}][is_available]" value="1" ${isAvailable ? 'checked' : ''}>
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-country" data-country-id="${countryId}" data-row-id="${countryRowCounter}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            
            $('#countriesTable tbody').append(newRow);
            countryRowCounter++;
            
            // إعادة تعيين النموذج وإغلاق النافذة
            $('#country_id').val('');
            $('#country_cost').val(0);
            $('#country_is_available').prop('checked', true);
            $('#addCountryModal').modal('hide');
        });
        
        // حذف دولة
        $(document).on('click', '.remove-country', function() {
            const countryId = $(this).data('country-id');
            const rowId = $(this).data('row-id');
            
            addedCountries.delete(countryId);
            $(`#country-row-${rowId}`).remove();
        });
    });
</script>
@endsection 