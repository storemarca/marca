@extends('layouts.user')

@section('title', 'التقديم لبرنامج المسوقين بالعمولة')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h1 class="h4 mb-0">التقديم لبرنامج المسوقين بالعمولة</h1>
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

                    <div class="alert alert-info mb-4">
                        <h5>كيف يعمل برنامج المسوقين بالعمولة؟</h5>
                        <p>برنامج المسوقين بالعمولة يتيح لك كسب عمولات عن طريق الترويج لمنتجاتنا. عندما يشتري شخص ما من خلال رابط الإحالة الخاص بك، ستحصل على عمولة بنسبة محددة من قيمة الطلب.</p>
                        <hr>
                        <h5>مميزات البرنامج:</h5>
                        <ul>
                            <li>عمولة تصل إلى 15% من قيمة الطلبات</li>
                            <li>روابط تسويقية سهلة الإنشاء والمشاركة</li>
                            <li>لوحة تحكم متكاملة لتتبع الأرباح والإحصائيات</li>
                            <li>مواد تسويقية جاهزة للاستخدام</li>
                            <li>دفعات منتظمة للأرباح</li>
                        </ul>
                    </div>

                    <form method="POST" action="{{ route('affiliate.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="website" class="form-label">موقع الويب (اختياري)</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website') }}" placeholder="https://www.example.com">
                            <div class="form-text">إذا كان لديك موقع ويب تخطط لاستخدامه للترويج لمنتجاتنا</div>
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="social_media" class="form-label">حسابات التواصل الاجتماعي (اختياري)</label>
                            <input type="text" class="form-control @error('social_media') is-invalid @enderror" id="social_media" name="social_media" value="{{ old('social_media') }}" placeholder="instagram: @username, facebook: facebook.com/username">
                            <div class="form-text">أدخل حسابات التواصل الاجتماعي التي تخطط لاستخدامها للترويج</div>
                            @error('social_media')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="marketing_methods" class="form-label">طرق التسويق <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('marketing_methods') is-invalid @enderror" id="marketing_methods" name="marketing_methods" rows="4" required>{{ old('marketing_methods') }}</textarea>
                            <div class="form-text">اشرح كيف تخطط للترويج لمنتجاتنا (مثال: وسائل التواصل الاجتماعي، التسويق عبر البريد الإلكتروني، المدونات، إلخ)</div>
                            @error('marketing_methods')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="payment_method" class="form-label">طريقة الدفع المفضلة <span class="text-danger">*</span></label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                                <option value="" selected disabled>اختر طريقة الدفع</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                                <option value="paypal" {{ old('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                <option value="western_union" {{ old('payment_method') == 'western_union' ? 'selected' : '' }}>Western Union</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="payment_details" class="form-label">تفاصيل الدفع <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('payment_details') is-invalid @enderror" id="payment_details" name="payment_details" rows="3" required>{{ old('payment_details') }}</textarea>
                            <div class="form-text">أدخل تفاصيل الدفع الخاصة بك (مثال: رقم الحساب البنكي، بريد PayPal، إلخ)</div>
                            @error('payment_details')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">أوافق على <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">شروط وأحكام</a> برنامج المسوقين بالعمولة</label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">تقديم الطلب</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Terms and Conditions -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">شروط وأحكام برنامج المسوقين بالعمولة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5>1. الأهلية</h5>
                <p>للمشاركة في برنامج المسوقين بالعمولة، يجب أن تكون:</p>
                <ul>
                    <li>عمرك 18 عامًا على الأقل</li>
                    <li>لديك حساب نشط على موقعنا</li>
                    <li>قادر على استلام المدفوعات بإحدى طرق الدفع المتاحة</li>
                </ul>

                <h5>2. العمولات</h5>
                <p>ستحصل على عمولة بنسبة محددة من قيمة الطلبات التي تتم من خلال رابط الإحالة الخاص بك. تختلف نسبة العمولة حسب تقييمنا لأدائك وحجم المبيعات.</p>

                <h5>3. الدفعات</h5>
                <p>يتم دفع العمولات شهريًا بشرط أن يصل رصيدك إلى الحد الأدنى للسحب (100 ر.س). يمكنك طلب سحب أرباحك في أي وقت من خلال لوحة التحكم الخاصة بك.</p>

                <h5>4. قواعد التسويق</h5>
                <p>يجب الالتزام بالقواعد التالية عند الترويج لمنتجاتنا:</p>
                <ul>
                    <li>عدم استخدام أساليب تسويقية مضللة أو غير أخلاقية</li>
                    <li>عدم إرسال رسائل بريد إلكتروني غير مرغوب فيها (سبام)</li>
                    <li>عدم استخدام محتوى يسيء إلى سمعة الموقع أو العلامة التجارية</li>
                    <li>عدم استخدام الإعلانات المدفوعة التي تستهدف اسم العلامة التجارية مباشرة</li>
                </ul>

                <h5>5. إنهاء الاشتراك</h5>
                <p>نحتفظ بالحق في إنهاء اشتراكك في برنامج المسوقين بالعمولة في أي وقت إذا خالفت أيًا من هذه الشروط والأحكام.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>
@endsection 