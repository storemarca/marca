@extends('layouts.admin')

@section('title', 'إدارة طرق الشحن')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">طرق الشحن</h1>
        <a href="{{ route('admin.shipping-methods.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة طريقة شحن جديدة
        </a>
    </div>

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

    <!-- Debug info -->
    <div class="alert alert-info">
        عدد طرق الشحن: {{ $shippingMethods->count() }}
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">قائمة طرق الشحن</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>الاسم</th>
                            <th>الكود</th>
                            <th>التكلفة الأساسية</th>
                            <th>الدول المتاحة</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shippingMethods as $method)
                            <tr>
                                <td>{{ $method->name }}</td>
                                <td>{{ $method->code }}</td>
                                <td>{{ number_format($method->base_cost, 2) }}</td>
                                <td>
                                    @if($method->countries->count() > 0)
                                        {{ $method->countries->count() }} دولة
                                    @else
                                        <span class="text-muted">جميع الدول</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $method->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $method->is_active ? 'مفعل' : 'غير مفعل' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.shipping-methods.show', $method) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.shipping-methods.edit', $method) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.shipping-methods.destroy', $method) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الطريقة؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">لا توجد طرق شحن مضافة</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // No DataTables script needed as it's removed.
    });
</script>
@endsection 