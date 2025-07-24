@extends('layouts.admin')

@section('title', 'إعدادات البريد الإلكتروني')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            @include('admin.settings.partials.sidebar')
        </div>
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">إعدادات البريد الإلكتروني</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.mail.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mail_driver" class="form-label">نوع خدمة البريد</label>
                                    <select name="mail_driver" id="mail_driver" class="form-select">
                                        <option value="smtp" {{ $settings['mail_driver'] ?? '' == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                        <option value="mailgun" {{ $settings['mail_driver'] ?? '' == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                        <option value="ses" {{ $settings['mail_driver'] ?? '' == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mail_encryption" class="form-label">نوع التشفير</label>
                                    <select name="mail_encryption" id="mail_encryption" class="form-select">
                                        <option value="tls" {{ $settings['mail_encryption'] ?? '' == 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ $settings['mail_encryption'] ?? '' == 'ssl' ? 'selected' : '' }}>SSL</option>
                                        <option value="" {{ ($settings['mail_encryption'] ?? '') == '' ? 'selected' : '' }}>بدون تشفير</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mail_host" class="form-label">خادم SMTP</label>
                                    <input type="text" class="form-control" id="mail_host" name="mail_host" value="{{ $settings['mail_host'] ?? '' }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mail_port" class="form-label">منفذ SMTP</label>
                                    <input type="text" class="form-control" id="mail_port" name="mail_port" value="{{ $settings['mail_port'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mail_username" class="form-label">اسم المستخدم</label>
                                    <input type="text" class="form-control" id="mail_username" name="mail_username" value="{{ $settings['mail_username'] ?? '' }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mail_password" class="form-label">كلمة المرور</label>
                                    <input type="password" class="form-control" id="mail_password" name="mail_password" value="{{ $settings['mail_password'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mail_from_address" class="form-label">عنوان البريد المرسل</label>
                                    <input type="email" class="form-control" id="mail_from_address" name="mail_from_address" value="{{ $settings['mail_from_address'] ?? '' }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mail_from_name" class="form-label">اسم المرسل</label>
                                    <input type="text" class="form-control" id="mail_from_name" name="mail_from_name" value="{{ $settings['mail_from_name'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                            <button type="button" id="test_mail" class="btn btn-outline-info">اختبار الإعدادات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#test_mail').click(function() {
            // يمكن إضافة كود لاختبار إعدادات البريد هنا
            alert('سيتم تنفيذ هذه الميزة قريباً');
        });
    });
</script>
@endsection 