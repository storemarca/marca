@extends('layouts.admin')

@section('title', 'إدارة المستخدمين')
@section('page-title', 'إدارة المستخدمين')

@section('actions')
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="fas fa-plus-circle ml-1"></i> إضافة مستخدم جديد
    </a>
@endsection

@section('content')
    <!-- فلاتر البحث -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">فلترة المستخدمين</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">بحث</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="اسم المستخدم أو البريد الإلكتروني...">
                </div>
                <div class="col-md-3">
                    <label for="role" class="form-label">الدور</label>
                    <select class="form-select" id="role" name="role">
                        <option value="">جميع الأدوار</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sort_by" class="form-label">ترتيب حسب</label>
                    <select class="form-select" id="sort_by" name="sort_by">
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>تاريخ التسجيل</option>
                        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>الاسم</option>
                        <option value="email" {{ request('sort_by') == 'email' ? 'selected' : '' }}>البريد الإلكتروني</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="sort_direction" class="form-label">اتجاه الترتيب</label>
                    <select class="form-select" id="sort_direction" name="sort_direction">
                        <option value="desc" {{ request('sort_direction') == 'desc' ? 'selected' : '' }}>تنازلي</option>
                        <option value="asc" {{ request('sort_direction') == 'asc' ? 'selected' : '' }}>تصاعدي</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">تطبيق الفلاتر</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">إعادة تعيين</a>
                </div>
            </form>
        </div>
    </div>

    <!-- جدول المستخدمين -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">قائمة المستخدمين</h5>
            <span class="badge bg-primary">{{ $users->total() }} مستخدم</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>البريد الإلكتروني</th>
                            <th>الأدوار</th>
                            <th>تاريخ التسجيل</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light rounded-circle text-center me-2" style="width: 40px; height: 40px; line-height: 40px;">
                                            <span class="font-weight-bold">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $user->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-info me-1">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-info" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <h5>لا يوجد مستخدمين</h5>
                                        <p class="text-muted">لم يتم العثور على أي مستخدمين مطابقين لمعايير البحث</p>
                                        <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary mt-2">
                                            <i class="fas fa-plus-circle ml-1"></i> إضافة مستخدم جديد
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>
@endsection 