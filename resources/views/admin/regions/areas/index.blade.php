@extends('layouts.admin')

@section('title', 'إدارة المناطق')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 mb-4">إدارة المناطق</h1>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-map me-1"></i>
                قائمة المناطق
            </div>
            <a href="{{ route('admin.regions.areas.create') }}" class="btn btn-sm btn-light">
                <i class="fas fa-plus"></i> إضافة منطقة جديدة
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            
            <!-- فلتر البحث -->
            <div class="card mb-3">
                <div class="card-body">
                    <form action="{{ route('admin.regions.areas.index') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="governorate_id" class="form-label">تصفية حسب المحافظة</label>
                            <select name="governorate_id" id="governorate_id" class="form-select">
                                <option value="">-- جميع المحافظات --</option>
                                @foreach($districts->pluck('governorate')->unique('id') as $governorate)
                                    <option value="{{ $governorate->id }}" {{ request('governorate_id') == $governorate->id ? 'selected' : '' }}>
                                        {{ $governorate->name }} ({{ $governorate->country->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="district_id" class="form-label">تصفية حسب المركز</label>
                            <select name="district_id" id="district_id" class="form-select">
                                <option value="">-- جميع المراكز --</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}" {{ request('district_id') == $district->id ? 'selected' : '' }}
                                        data-governorate="{{ $district->governorate_id }}">
                                        {{ $district->name }} ({{ $district->governorate->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i> تصفية
                            </button>
                            <a href="{{ route('admin.regions.areas.index') }}" class="btn btn-secondary ms-2">
                                <i class="fas fa-redo me-1"></i> إعادة ضبط
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>الاسم بالعربية</th>
                            <th>المركز</th>
                            <th>المحافظة</th>
                            <th>الكود</th>
                            <th>تكلفة الشحن الإضافية</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($areas as $area)
                            <tr>
                                <td>{{ $area->id }}</td>
                                <td>{{ $area->name }}</td>
                                <td>{{ $area->name_ar }}</td>
                                <td>{{ $area->district->name }}</td>
                                <td>{{ $area->district->governorate->name }}</td>
                                <td>{{ $area->code }}</td>
                                <td>{{ number_format($area->additional_shipping_cost, 2) }} {{ $area->district->governorate->country->currency_symbol }}</td>
                                <td>
                                    <span class="badge bg-{{ $area->is_active ? 'success' : 'danger' }}">
                                        {{ $area->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.regions.areas.edit', $area->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> تعديل
                                        </a>
                                        <form action="{{ route('admin.regions.areas.destroy', $area->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه المنطقة؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> حذف
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">لا توجد مناطق</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $areas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // تصفية المراكز حسب المحافظة المختارة
    document.getElementById('governorate_id').addEventListener('change', function() {
        const governorateId = this.value;
        const districtSelect = document.getElementById('district_id');
        
        // إظهار جميع المراكز إذا لم يتم اختيار محافظة
        if (!governorateId) {
            Array.from(districtSelect.options).forEach(option => {
                option.style.display = option.value ? 'block' : 'block';
            });
            return;
        }
        
        // إخفاء المراكز التي لا تنتمي للمحافظة المختارة
        Array.from(districtSelect.options).forEach(option => {
            if (!option.value) return; // تخطي الخيار الافتراضي
            
            const optionGovernorateId = option.getAttribute('data-governorate');
            option.style.display = (optionGovernorateId === governorateId) ? 'block' : 'none';
        });
        
        // إعادة تعيين قيمة المركز إذا كان المركز المحدد حاليًا لا ينتمي للمحافظة المختارة
        const selectedDistrict = districtSelect.options[districtSelect.selectedIndex];
        if (selectedDistrict && selectedDistrict.value && selectedDistrict.getAttribute('data-governorate') !== governorateId) {
            districtSelect.value = '';
        }
    });
    
    // تشغيل الفلتر عند تحميل الصفحة إذا كان هناك محافظة محددة
    document.addEventListener('DOMContentLoaded', function() {
        const governorateSelect = document.getElementById('governorate_id');
        if (governorateSelect.value) {
            governorateSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection 