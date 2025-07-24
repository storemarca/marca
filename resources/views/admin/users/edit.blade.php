@extends('layouts.admin')

@section('title', 'تعديل بيانات المستخدم')
@section('page-title', 'تعديل بيانات المستخدم')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">تعديل بيانات المستخدم: {{ $user->name }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">كلمة المرور الجديدة</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                        <small class="form-text text-muted">اترك هذا الحقل فارغًا إذا كنت لا ترغب في تغيير كلمة المرور</small>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">تأكيد كلمة المرور الجديدة</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label d-block">الأدوار والصلاحيات <span class="text-danger">*</span></label>
                    
                    <div class="border rounded p-3">
                        <div class="mb-3">
                            <label class="form-label">اختر الأدوار</label>
                            <div class="row">
                                @foreach($roles as $role)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input role-checkbox" type="checkbox" id="role_{{ $role->id }}" name="roles[]" value="{{ $role->id }}" 
                                                {{ in_array($role->id, old('roles', $userRoles)) ? 'checked' : '' }}
                                                {{ $user->id === auth()->id() && $role->name === 'admin' ? 'disabled' : '' }}>
                                            <label class="form-check-label" for="role_{{ $role->id }}">
                                                {{ $role->name }}
                                                @if($role->name === 'admin')
                                                    <span class="badge bg-danger">مدير النظام</span>
                                                @elseif($role->name === 'customer')
                                                    <span class="badge bg-info">عميل</span>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        @if($user->id === auth()->id())
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>تنبيه:</strong> لا يمكنك إزالة دور المدير من حسابك الشخصي.
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>ملاحظة:</strong> دور "مدير النظام" يمنح جميع الصلاحيات تلقائياً.
                            </div>
                        @endif
                    </div>
                    
                    @error('roles')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="send_password_notification" name="send_password_notification" value="1" {{ old('send_password_notification') ? 'checked' : '' }}>
                        <label class="form-check-label" for="send_password_notification">
                            إرسال إشعار بتغيير كلمة المرور إلى البريد الإلكتروني للمستخدم
                        </label>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // عند اختيار دور المدير، يتم تحديد جميع الأدوار الأخرى كغير متاحة
        $('#role_1').change(function() {
            if($(this).is(':checked')) {
                $('.role-checkbox').not(this).prop('checked', false).prop('disabled', true);
            } else {
                $('.role-checkbox').prop('disabled', false);
            }
        });
        
        // تطبيق الحالة الأولية
        if($('#role_1').is(':checked')) {
            $('.role-checkbox').not('#role_1').prop('checked', false).prop('disabled', true);
        }
    });
</script>
@endsection 