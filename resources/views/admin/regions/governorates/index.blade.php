@extends('layouts.admin')

@section('title', 'إدارة المحافظات')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 mb-4">إدارة المحافظات</h1>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-map-marked-alt me-1"></i>
                قائمة المحافظات
            </div>
            <a href="{{ route('admin.regions.governorates.create') }}" class="btn btn-sm btn-light">
                <i class="fas fa-plus"></i> إضافة محافظة جديدة
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
            
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>الاسم بالعربية</th>
                            <th>الدولة</th>
                            <th>الكود</th>
                            <th>تكلفة الشحن</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($governorates as $governorate)
                            <tr>
                                <td>{{ $governorate->id }}</td>
                                <td>{{ $governorate->name }}</td>
                                <td>{{ $governorate->name_ar }}</td>
                                <td>{{ $governorate->country->name }}</td>
                                <td>{{ $governorate->code }}</td>
                                <td>{{ number_format($governorate->shipping_cost, 2) }} {{ $governorate->country->currency_symbol }}</td>
                                <td>
                                    <span class="badge bg-{{ $governorate->is_active ? 'success' : 'danger' }}">
                                        {{ $governorate->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.regions.governorates.edit', $governorate->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> تعديل
                                        </a>
                                        <form action="{{ route('admin.regions.governorates.destroy', $governorate->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه المحافظة؟');">
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
                                <td colspan="8" class="text-center">لا توجد محافظات</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $governorates->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 