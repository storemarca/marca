@extends('layouts.admin')

@section('title', 'إدارة المراكز')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 mb-4">إدارة المراكز</h1>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-map-pin me-1"></i>
                قائمة المراكز
            </div>
            <a href="{{ route('admin.regions.districts.create') }}" class="btn btn-sm btn-light">
                <i class="fas fa-plus"></i> إضافة مركز جديد
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
                    <form action="{{ route('admin.regions.districts.index') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="governorate_id" class="form-label">تصفية حسب المحافظة</label>
                            <select name="governorate_id" id="governorate_id" class="form-select">
                                <option value="">-- جميع المحافظات --</option>
                                @foreach($governorates as $governorate)
                                    <option value="{{ $governorate->id }}" {{ request('governorate_id') == $governorate->id ? 'selected' : '' }}>
                                        {{ $governorate->name }} ({{ $governorate->country->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i> تصفية
                            </button>
                            <a href="{{ route('admin.regions.districts.index') }}" class="btn btn-secondary ms-2">
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
                            <th>المحافظة</th>
                            <th>الكود</th>
                            <th>تكلفة الشحن الإضافية</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($districts as $district)
                            <tr>
                                <td>{{ $district->id }}</td>
                                <td>{{ $district->name }}</td>
                                <td>{{ $district->name_ar }}</td>
                                <td>{{ $district->governorate->name }} ({{ $district->governorate->country->name }})</td>
                                <td>{{ $district->code }}</td>
                                <td>{{ number_format($district->additional_shipping_cost, 2) }} {{ $district->governorate->country->currency_symbol }}</td>
                                <td>
                                    <span class="badge bg-{{ $district->is_active ? 'success' : 'danger' }}">
                                        {{ $district->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.regions.districts.edit', $district->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> تعديل
                                        </a>
                                        <form action="{{ route('admin.regions.districts.destroy', $district->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المركز؟');">
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
                                <td colspan="8" class="text-center">لا توجد مراكز</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $districts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 