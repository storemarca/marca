@extends('layouts.admin')

@section('title', 'إدارة الأدوار والصلاحيات')
@section('page-title', 'إدارة الأدوار والصلاحيات')

@section('actions')
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
        <i class="fas fa-plus-circle ml-1"></i> إضافة دور جديد
    </a>
@endsection

@section('content')
    <!-- جدول الأدوار -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">قائمة الأدوار</h5>
            <span class="badge bg-primary">{{ count($roles) }} دور</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم الدور</th>
                            <th>عدد المستخدمين</th>
                            <th>الصلاحيات</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td>{{ $role->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm {{ $role->name === 'admin' ? 'bg-danger' : 'bg-info' }} text-white rounded-circle text-center me-2" style="width: 40px; height: 40px; line-height: 40px;">
                                            <i class="fas {{ $role->name === 'admin' ? 'fa-user-shield' : 'fa-user-tag' }}"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $role->name }}</h6>
                                            @if($role->name === 'admin')
                                                <small class="text-muted">دور المدير الرئيسي</small>
                                            @elseif($role->name === 'customer')
                                                <small class="text-muted">دور العميل</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $role->users->count() }} مستخدم</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @if($role->name === 'admin')
                                            <span class="badge bg-danger">جميع الصلاحيات</span>
                                        @else
                                            @foreach($role->permissions->take(3) as $permission)
                                                <span class="badge bg-info">{{ $permission->name }}</span>
                                            @endforeach
                                            
                                            @if($role->permissions->count() > 3)
                                                <span class="badge bg-secondary">+{{ $role->permissions->count() - 3 }}</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.roles.show', $role->id) }}" class="btn btn-sm btn-info" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(!in_array($role->name, ['admin', 'customer']))
                                            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-warning" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟')">
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
                                <td colspan="5" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-user-lock fa-3x text-muted mb-3"></i>
                                        <h5>لا توجد أدوار</h5>
                                        <p class="text-muted">لم يتم إنشاء أي أدوار بعد</p>
                                        <a href="{{ route('admin.roles.create') }}" class="btn btn-sm btn-primary mt-2">
                                            <i class="fas fa-plus-circle ml-1"></i> إضافة دور جديد
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- شرح الأدوار والصلاحيات -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">معلومات عن الأدوار والصلاحيات</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-3"><i class="fas fa-info-circle text-info me-2"></i> ما هي الأدوار؟</h6>
                    <p>الأدوار هي مجموعة من الصلاحيات التي يمكن إسنادها للمستخدمين. يمكن للمستخدم أن يكون له أكثر من دور واحد.</p>
                    <ul class="list-group mt-2">
                        <li class="list-group-item d-flex align-items-center">
                            <div class="avatar-sm bg-danger text-white rounded-circle text-center me-2" style="width: 30px; height: 30px; line-height: 30px;">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div>
                                <strong>مدير النظام (admin)</strong>
                                <div class="text-muted small">يملك جميع الصلاحيات ولا يمكن تعديله أو حذفه</div>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <div class="avatar-sm bg-info text-white rounded-circle text-center me-2" style="width: 30px; height: 30px; line-height: 30px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <strong>العميل (customer)</strong>
                                <div class="text-muted small">دور افتراضي للعملاء المسجلين في الموقع</div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="mb-3"><i class="fas fa-key text-warning me-2"></i> ما هي الصلاحيات؟</h6>
                    <p>الصلاحيات تحدد ما يمكن للمستخدم القيام به في النظام. يمكن إضافة صلاحيات متعددة لكل دور.</p>
                    <div class="alert alert-info">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>نصيحة:</strong> قم بإنشاء أدوار محددة لكل نوع من المستخدمين (مثل: مدير مبيعات، مدير مخزون، محاسب) وإسناد الصلاحيات المناسبة لكل دور.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 