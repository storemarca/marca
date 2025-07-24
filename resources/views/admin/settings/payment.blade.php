@extends('layouts.admin')

@section('title', 'إعدادات الدفع')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            @include('admin.settings.partials.sidebar')
        </div>
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">إعدادات الدفع</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.payment.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <h6 class="fw-bold">الإعدادات العامة للدفع</h6>
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="currency" class="form-label">العملة الافتراضية</label>
                                        <select name="currency" id="currency" class="form-select">
                                            <option value="SAR" {{ ($settings['currency'] ?? '') == 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                                            <option value="USD" {{ ($settings['currency'] ?? '') == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                                            <option value="EUR" {{ ($settings['currency'] ?? '') == 'EUR' ? 'selected' : '' }}>يورو (EUR)</option>
                                            <option value="AED" {{ ($settings['currency'] ?? '') == 'AED' ? 'selected' : '' }}>درهم إماراتي (AED)</option>
                                            <option value="EGP" {{ ($settings['currency'] ?? '') == 'EGP' ? 'selected' : '' }}>جنيه مصري (EGP)</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="currency_symbol" class="form-label">رمز العملة</label>
                                        <input type="text" class="form-control" id="currency_symbol" name="currency_symbol" value="{{ $settings['currency_symbol'] ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="fw-bold">إعدادات الضريبة</h6>
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tax_percentage" class="form-label">نسبة الضريبة (%)</label>
                                        <input type="number" step="0.01" class="form-control" id="tax_percentage" name="tax_percentage" value="{{ $settings['tax_percentage'] ?? '15' }}">
                                        <div class="form-text">أدخل نسبة الضريبة المطبقة (مثال: 15 لضريبة القيمة المضافة 15%)</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tax_included" class="form-label">الضريبة مشمولة في الأسعار</label>
                                        <select name="tax_included" id="tax_included" class="form-select">
                                            <option value="1" {{ ($settings['tax_included'] ?? '') == '1' ? 'selected' : '' }}>نعم - الأسعار تشمل الضريبة</option>
                                            <option value="0" {{ ($settings['tax_included'] ?? '') == '0' ? 'selected' : '' }}>لا - الضريبة تضاف للسعر</option>
                                        </select>
                                        <div class="form-text">إذا كانت "نعم"، فإن الأسعار المعروضة تشمل الضريبة. إذا كانت "لا"، فستتم إضافة الضريبة عند الدفع.</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tax_name" class="form-label">اسم الضريبة</label>
                                        <input type="text" class="form-control" id="tax_name" name="tax_name" value="{{ $settings['tax_name'] ?? 'ضريبة القيمة المضافة' }}">
                                        <div class="form-text">الاسم الذي سيظهر للعملاء (مثال: ضريبة القيمة المضافة، VAT)</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tax_number" class="form-label">الرقم الضريبي للشركة</label>
                                        <input type="text" class="form-control" id="tax_number" name="tax_number" value="{{ $settings['tax_number'] ?? '' }}">
                                        <div class="form-text">سيظهر هذا الرقم في الفواتير الضريبية</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="show_tax_in_product" name="show_tax_in_product" value="1" {{ ($settings['show_tax_in_product'] ?? '0') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="show_tax_in_product">
                                                عرض الضريبة في صفحة المنتج
                                            </label>
                                        </div>
                                        <div class="form-text">إذا تم تفعيل هذا الخيار، سيتم عرض قيمة الضريبة بشكل منفصل في صفحة المنتج</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="enable_tax_exempt" name="enable_tax_exempt" value="1" {{ ($settings['enable_tax_exempt'] ?? '0') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enable_tax_exempt">
                                                تفعيل الإعفاء الضريبي
                                            </label>
                                        </div>
                                        <div class="form-text">إذا تم تفعيل هذا الخيار، يمكن تعيين بعض العملاء كمعفيين من الضريبة</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="fw-bold">الحسابات البنكية</h6>
                            <hr>
                            
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                أدخل معلومات الحسابات البنكية التي سيتم استخدامها لاستلام المدفوعات من العملاء
                            </div>
                            
                            <div class="mb-3">
                                <label for="bank_transfer_instructions" class="form-label">تعليمات التحويل البنكي</label>
                                <textarea class="form-control" id="bank_transfer_instructions" name="bank_transfer_instructions" rows="3">{{ $settings['bank_transfer_instructions'] ?? 'يرجى تحويل المبلغ إلى أحد الحسابات البنكية المذكورة أدناه. بعد إتمام التحويل، يرجى إرسال صورة من إيصال التحويل مع رقم الطلب.' }}</textarea>
                                <div class="form-text">هذه التعليمات ستظهر للعملاء عند اختيار الدفع عن طريق التحويل البنكي</div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">الحساب البنكي الأول</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="bank_name_1" class="form-label">اسم البنك</label>
                                                <input type="text" class="form-control" id="bank_name_1" name="bank_name_1" value="{{ $settings['bank_name_1'] ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="bank_account_name_1" class="form-label">اسم صاحب الحساب</label>
                                                <input type="text" class="form-control" id="bank_account_name_1" name="bank_account_name_1" value="{{ $settings['bank_account_name_1'] ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="bank_account_number_1" class="form-label">رقم الحساب</label>
                                                <input type="text" class="form-control" id="bank_account_number_1" name="bank_account_number_1" value="{{ $settings['bank_account_number_1'] ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="bank_iban_1" class="form-label">رقم الآيبان (IBAN)</label>
                                                <input type="text" class="form-control" id="bank_iban_1" name="bank_iban_1" value="{{ $settings['bank_iban_1'] ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="bank_swift_1" class="form-label">رمز السويفت (SWIFT/BIC)</label>
                                                <input type="text" class="form-control" id="bank_swift_1" name="bank_swift_1" value="{{ $settings['bank_swift_1'] ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="bank_currency_1" class="form-label">عملة الحساب</label>
                                                <select class="form-select" id="bank_currency_1" name="bank_currency_1">
                                                    <option value="SAR" {{ ($settings['bank_currency_1'] ?? '') == 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                                                    <option value="USD" {{ ($settings['bank_currency_1'] ?? '') == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                                                    <option value="EUR" {{ ($settings['bank_currency_1'] ?? '') == 'EUR' ? 'selected' : '' }}>يورو (EUR)</option>
                                                    <option value="AED" {{ ($settings['bank_currency_1'] ?? '') == 'AED' ? 'selected' : '' }}>درهم إماراتي (AED)</option>
                                                    <option value="EGP" {{ ($settings['bank_currency_1'] ?? '') == 'EGP' ? 'selected' : '' }}>جنيه مصري (EGP)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">الحساب البنكي الثاني</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="bank_name_2" class="form-label">اسم البنك</label>
                                                <input type="text" class="form-control" id="bank_name_2" name="bank_name_2" value="{{ $settings['bank_name_2'] ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="bank_account_name_2" class="form-label">اسم صاحب الحساب</label>
                                                <input type="text" class="form-control" id="bank_account_name_2" name="bank_account_name_2" value="{{ $settings['bank_account_name_2'] ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="bank_account_number_2" class="form-label">رقم الحساب</label>
                                                <input type="text" class="form-control" id="bank_account_number_2" name="bank_account_number_2" value="{{ $settings['bank_account_number_2'] ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="bank_iban_2" class="form-label">رقم الآيبان (IBAN)</label>
                                                <input type="text" class="form-control" id="bank_iban_2" name="bank_iban_2" value="{{ $settings['bank_iban_2'] ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="bank_swift_2" class="form-label">رمز السويفت (SWIFT/BIC)</label>
                                                <input type="text" class="form-control" id="bank_swift_2" name="bank_swift_2" value="{{ $settings['bank_swift_2'] ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="bank_currency_2" class="form-label">عملة الحساب</label>
                                                <select class="form-select" id="bank_currency_2" name="bank_currency_2">
                                                    <option value="SAR" {{ ($settings['bank_currency_2'] ?? '') == 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                                                    <option value="USD" {{ ($settings['bank_currency_2'] ?? '') == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                                                    <option value="EUR" {{ ($settings['bank_currency_2'] ?? '') == 'EUR' ? 'selected' : '' }}>يورو (EUR)</option>
                                                    <option value="AED" {{ ($settings['bank_currency_2'] ?? '') == 'AED' ? 'selected' : '' }}>درهم إماراتي (AED)</option>
                                                    <option value="EGP" {{ ($settings['bank_currency_2'] ?? '') == 'EGP' ? 'selected' : '' }}>جنيه مصري (EGP)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="fw-bold">محافظ الدفع الإلكترونية</h6>
                            <hr>
                            
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                أدخل معلومات محافظ الدفع الإلكترونية التي سيتم استخدامها لاستلام المدفوعات من العملاء
                            </div>
                            
                            <div class="mb-3">
                                <label for="mobile_wallet_instructions" class="form-label">تعليمات الدفع بمحافظ الهاتف المحمول</label>
                                <textarea class="form-control" id="mobile_wallet_instructions" name="mobile_wallet_instructions" rows="3">{{ $settings['mobile_wallet_instructions'] ?? 'يرجى تحويل المبلغ إلى أحد أرقام المحافظ الإلكترونية المذكورة أدناه. بعد إتمام التحويل، يرجى إرسال صورة من إيصال التحويل مع رقم الطلب.' }}</textarea>
                                <div class="form-text">هذه التعليمات ستظهر للعملاء عند اختيار الدفع عن طريق محافظ الهاتف المحمول</div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">فودافون كاش</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="vodafone_cash_enabled" name="vodafone_cash_enabled" value="1" {{ ($settings['vodafone_cash_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="vodafone_cash_enabled">
                                                        تفعيل فودافون كاش
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="vodafone_cash_number" class="form-label">رقم الهاتف</label>
                                                <input type="text" class="form-control" id="vodafone_cash_number" name="vodafone_cash_number" value="{{ $settings['vodafone_cash_number'] ?? '' }}" placeholder="مثال: 01000000000">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="vodafone_cash_name" class="form-label">اسم صاحب المحفظة</label>
                                                <input type="text" class="form-control" id="vodafone_cash_name" name="vodafone_cash_name" value="{{ $settings['vodafone_cash_name'] ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">اتصالات كاش</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="etisalat_cash_enabled" name="etisalat_cash_enabled" value="1" {{ ($settings['etisalat_cash_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="etisalat_cash_enabled">
                                                        تفعيل اتصالات كاش
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="etisalat_cash_number" class="form-label">رقم الهاتف</label>
                                                <input type="text" class="form-control" id="etisalat_cash_number" name="etisalat_cash_number" value="{{ $settings['etisalat_cash_number'] ?? '' }}" placeholder="مثال: 01100000000">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="etisalat_cash_name" class="form-label">اسم صاحب المحفظة</label>
                                                <input type="text" class="form-control" id="etisalat_cash_name" name="etisalat_cash_name" value="{{ $settings['etisalat_cash_name'] ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">أورانج كاش</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="orange_cash_enabled" name="orange_cash_enabled" value="1" {{ ($settings['orange_cash_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="orange_cash_enabled">
                                                        تفعيل أورانج كاش
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="orange_cash_number" class="form-label">رقم الهاتف</label>
                                                <input type="text" class="form-control" id="orange_cash_number" name="orange_cash_number" value="{{ $settings['orange_cash_number'] ?? '' }}" placeholder="مثال: 01200000000">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="orange_cash_name" class="form-label">اسم صاحب المحفظة</label>
                                                <input type="text" class="form-control" id="orange_cash_name" name="orange_cash_name" value="{{ $settings['orange_cash_name'] ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">وي كاش</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="we_cash_enabled" name="we_cash_enabled" value="1" {{ ($settings['we_cash_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="we_cash_enabled">
                                                        تفعيل وي كاش
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="we_cash_number" class="form-label">رقم الهاتف</label>
                                                <input type="text" class="form-control" id="we_cash_number" name="we_cash_number" value="{{ $settings['we_cash_number'] ?? '' }}" placeholder="مثال: 01500000000">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="we_cash_name" class="form-label">اسم صاحب المحفظة</label>
                                                <input type="text" class="form-control" id="we_cash_name" name="we_cash_name" value="{{ $settings['we_cash_name'] ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">انستا باي</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="instapay_enabled" name="instapay_enabled" value="1" {{ ($settings['instapay_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="instapay_enabled">
                                                        تفعيل انستا باي
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="instapay_number" class="form-label">رقم الهاتف أو المعرف</label>
                                                <input type="text" class="form-control" id="instapay_number" name="instapay_number" value="{{ $settings['instapay_number'] ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="instapay_name" class="form-label">اسم صاحب الحساب</label>
                                                <input type="text" class="form-control" id="instapay_name" name="instapay_name" value="{{ $settings['instapay_name'] ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="fw-bold">بوابات الدفع</h6>
                            <hr>
                            
                            <!-- طرق الدفع المتاحة -->
                            <div class="mb-3">
                                <label class="form-label">طرق الدفع المتاحة</label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="payment_cod" name="payment_methods[]" value="cod" {{ in_array('cod', is_array($settings['payment_methods'] ?? []) ? $settings['payment_methods'] : []) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payment_cod">
                                        الدفع عند الاستلام
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="payment_bank_transfer" name="payment_methods[]" value="bank_transfer" {{ in_array('bank_transfer', is_array($settings['payment_methods'] ?? []) ? $settings['payment_methods'] : []) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payment_bank_transfer">
                                        التحويل البنكي
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input payment-gateway-toggle" type="checkbox" id="payment_paypal" name="payment_methods[]" value="paypal" {{ in_array('paypal', is_array($settings['payment_methods'] ?? []) ? $settings['payment_methods'] : []) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payment_paypal">
                                        باي بال (PayPal)
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input payment-gateway-toggle" type="checkbox" id="payment_stripe" name="payment_methods[]" value="stripe" {{ in_array('stripe', is_array($settings['payment_methods'] ?? []) ? $settings['payment_methods'] : []) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payment_stripe">
                                        سترايب (Stripe)
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input payment-gateway-toggle" type="checkbox" id="payment_myfatoorah" name="payment_methods[]" value="myfatoorah" {{ in_array('myfatoorah', is_array($settings['payment_methods'] ?? []) ? $settings['payment_methods'] : []) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payment_myfatoorah">
                                        ماي فاتورة (MyFatoorah)
                                    </label>
                                </div>
                            </div>
                            
                            <!-- إعدادات باي بال -->
                            <div id="paypal_settings" class="payment-gateway-settings mb-4 {{ in_array('paypal', is_array($settings['payment_methods'] ?? []) ? $settings['payment_methods'] : []) ? '' : 'd-none' }}">
                                <h6 class="fw-bold">إعدادات باي بال</h6>
                                <hr>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="paypal_mode" class="form-label">وضع باي بال</label>
                                            <select name="paypal_mode" id="paypal_mode" class="form-select">
                                                <option value="sandbox" {{ ($settings['paypal_mode'] ?? '') == 'sandbox' ? 'selected' : '' }}>تجريبي (Sandbox)</option>
                                                <option value="live" {{ ($settings['paypal_mode'] ?? '') == 'live' ? 'selected' : '' }}>مباشر (Live)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="paypal_client_id" class="form-label">معرف العميل (Client ID)</label>
                                            <input type="text" class="form-control" id="paypal_client_id" name="paypal_client_id" value="{{ $settings['paypal_client_id'] ?? '' }}">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="paypal_secret" class="form-label">المفتاح السري (Secret)</label>
                                            <input type="password" class="form-control" id="paypal_secret" name="paypal_secret" value="{{ $settings['paypal_secret'] ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- إعدادات سترايب -->
                            <div id="stripe_settings" class="payment-gateway-settings mb-4 {{ in_array('stripe', is_array($settings['payment_methods'] ?? []) ? $settings['payment_methods'] : []) ? '' : 'd-none' }}">
                                <h6 class="fw-bold">إعدادات سترايب</h6>
                                <hr>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="stripe_publishable_key" class="form-label">المفتاح العام (Publishable Key)</label>
                                            <input type="text" class="form-control" id="stripe_publishable_key" name="stripe_publishable_key" value="{{ $settings['stripe_publishable_key'] ?? '' }}">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="stripe_secret_key" class="form-label">المفتاح السري (Secret Key)</label>
                                            <input type="password" class="form-control" id="stripe_secret_key" name="stripe_secret_key" value="{{ $settings['stripe_secret_key'] ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- إعدادات ماي فاتورة -->
                            <div id="myfatoorah_settings" class="payment-gateway-settings mb-4 {{ in_array('myfatoorah', is_array($settings['payment_methods'] ?? []) ? $settings['payment_methods'] : []) ? '' : 'd-none' }}">
                                <h6 class="fw-bold">إعدادات ماي فاتورة</h6>
                                <hr>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="myfatoorah_mode" class="form-label">وضع ماي فاتورة</label>
                                            <select name="myfatoorah_mode" id="myfatoorah_mode" class="form-select">
                                                <option value="test" {{ ($settings['myfatoorah_mode'] ?? '') == 'test' ? 'selected' : '' }}>تجريبي (Test)</option>
                                                <option value="live" {{ ($settings['myfatoorah_mode'] ?? '') == 'live' ? 'selected' : '' }}>مباشر (Live)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="myfatoorah_api_key" class="form-label">مفتاح API</label>
                                            <input type="password" class="form-control" id="myfatoorah_api_key" name="myfatoorah_api_key" value="{{ $settings['myfatoorah_api_key'] ?? '' }}">
                                        </div>
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

@section('scripts')
<script>
    $(document).ready(function() {
        // عرض/إخفاء إعدادات بوابات الدفع عند تحديد/إلغاء تحديد الخيارات
        $('.payment-gateway-toggle').change(function() {
            const gatewayId = $(this).attr('id').replace('payment_', '');
            if ($(this).is(':checked')) {
                $(`#${gatewayId}_settings`).removeClass('d-none');
            } else {
                $(`#${gatewayId}_settings`).addClass('d-none');
            }
        });
    });
</script>
@endsection 