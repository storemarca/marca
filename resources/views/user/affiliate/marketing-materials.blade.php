@extends('layouts.user')

@section('title', 'المواد التسويقية')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">المواد التسويقية</h1>
        <a href="{{ route('affiliate.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-right ml-1"></i> العودة للوحة التحكم
        </a>
    </div>

    <div class="alert alert-info mb-4">
        <div class="d-flex">
            <div class="me-3">
                <i class="fas fa-info-circle fa-2x"></i>
            </div>
            <div>
                <h5 class="alert-heading">استخدم هذه المواد التسويقية لزيادة مبيعاتك!</h5>
                <p>يمكنك استخدام هذه البانرات والصور في موقعك الإلكتروني أو حساباتك على وسائل التواصل الاجتماعي. تأكد من ربطها بالرابط التسويقي الخاص بك للحصول على العمولة.</p>
            </div>
        </div>
    </div>

    <!-- البانرات -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">البانرات الإعلانية</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <img src="{{ asset('images/marketing/banner1.jpg') }}" alt="بانر إعلاني" class="img-fluid mb-3 rounded">
                            <h6>بانر عام للموقع</h6>
                            <p class="text-muted small">قياس: 728×90 بيكسل</p>
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-sm btn-primary copy-code" data-code="<a href='{{ route('affiliate.track', ['code' => $affiliate->code]) }}'><img src='{{ asset('images/marketing/banner1.jpg') }}' alt='{{ config('app.name') }}' /></a>">
                                    <i class="fas fa-code me-1"></i> نسخ الكود
                                </button>
                                <a href="{{ asset('images/marketing/banner1.jpg') }}" download class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-download me-1"></i> تحميل
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <img src="{{ asset('images/marketing/banner2.jpg') }}" alt="بانر إعلاني" class="img-fluid mb-3 rounded">
                            <h6>بانر العروض الخاصة</h6>
                            <p class="text-muted small">قياس: 300×250 بيكسل</p>
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-sm btn-primary copy-code" data-code="<a href='{{ route('affiliate.track', ['code' => $affiliate->code]) }}'><img src='{{ asset('images/marketing/banner2.jpg') }}' alt='{{ config('app.name') }}' /></a>">
                                    <i class="fas fa-code me-1"></i> نسخ الكود
                                </button>
                                <a href="{{ asset('images/marketing/banner2.jpg') }}" download class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-download me-1"></i> تحميل
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <img src="{{ asset('images/marketing/banner3.jpg') }}" alt="بانر إعلاني" class="img-fluid mb-3 rounded">
                            <h6>بانر المنتجات الجديدة</h6>
                            <p class="text-muted small">قياس: 300×600 بيكسل</p>
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-sm btn-primary copy-code" data-code="<a href='{{ route('affiliate.track', ['code' => $affiliate->code]) }}'><img src='{{ asset('images/marketing/banner3.jpg') }}' alt='{{ config('app.name') }}' /></a>">
                                    <i class="fas fa-code me-1"></i> نسخ الكود
                                </button>
                                <a href="{{ asset('images/marketing/banner3.jpg') }}" download class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-download me-1"></i> تحميل
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الشعارات -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">الشعارات والأيقونات</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <img src="{{ asset('images/marketing/logo-dark.png') }}" alt="شعار الموقع" class="img-fluid mb-3" style="max-height: 80px;">
                            <h6>الشعار الأساسي</h6>
                            <div class="d-flex justify-content-between mt-3">
                                <button class="btn btn-sm btn-primary copy-code" data-code="<a href='{{ route('affiliate.track', ['code' => $affiliate->code]) }}'><img src='{{ asset('images/marketing/logo-dark.png') }}' alt='{{ config('app.name') }}' /></a>">
                                    <i class="fas fa-code me-1"></i> نسخ الكود
                                </button>
                                <a href="{{ asset('images/marketing/logo-dark.png') }}" download class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-download me-1"></i> تحميل
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <img src="{{ asset('images/marketing/logo-light.png') }}" alt="شعار الموقع" class="img-fluid mb-3 bg-dark p-2" style="max-height: 80px;">
                            <h6>الشعار (خلفية داكنة)</h6>
                            <div class="d-flex justify-content-between mt-3">
                                <button class="btn btn-sm btn-primary copy-code" data-code="<a href='{{ route('affiliate.track', ['code' => $affiliate->code]) }}'><img src='{{ asset('images/marketing/logo-light.png') }}' alt='{{ config('app.name') }}' /></a>">
                                    <i class="fas fa-code me-1"></i> نسخ الكود
                                </button>
                                <a href="{{ asset('images/marketing/logo-light.png') }}" download class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-download me-1"></i> تحميل
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <img src="{{ asset('images/marketing/icon.png') }}" alt="أيقونة الموقع" class="img-fluid mb-3" style="max-height: 80px;">
                            <h6>أيقونة الموقع</h6>
                            <div class="d-flex justify-content-between mt-3">
                                <button class="btn btn-sm btn-primary copy-code" data-code="<a href='{{ route('affiliate.track', ['code' => $affiliate->code]) }}'><img src='{{ asset('images/marketing/icon.png') }}' alt='{{ config('app.name') }}' /></a>">
                                    <i class="fas fa-code me-1"></i> نسخ الكود
                                </button>
                                <a href="{{ asset('images/marketing/icon.png') }}" download class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-download me-1"></i> تحميل
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <img src="{{ asset('images/marketing/badge.png') }}" alt="شارة الموقع" class="img-fluid mb-3" style="max-height: 80px;">
                            <h6>شارة "شريك معتمد"</h6>
                            <div class="d-flex justify-content-between mt-3">
                                <button class="btn btn-sm btn-primary copy-code" data-code="<a href='{{ route('affiliate.track', ['code' => $affiliate->code]) }}'><img src='{{ asset('images/marketing/badge.png') }}' alt='شريك معتمد - {{ config('app.name') }}' /></a>">
                                    <i class="fas fa-code me-1"></i> نسخ الكود
                                </button>
                                <a href="{{ asset('images/marketing/badge.png') }}" download class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-download me-1"></i> تحميل
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- نماذج الوصف -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">نماذج الوصف التسويقي</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">وصف عام للموقع</h6>
                        </div>
                        <div class="card-body">
                            <p>{{ config('app.name') }} هو وجهتك المثالية للتسوق الإلكتروني، حيث يقدم مجموعة واسعة من المنتجات عالية الجودة بأسعار تنافسية. استمتع بتجربة تسوق سلسة وآمنة مع خدمة توصيل سريعة وخيارات دفع متعددة.</p>
                            <button class="btn btn-sm btn-primary copy-text" data-text="{{ config('app.name') }} هو وجهتك المثالية للتسوق الإلكتروني، حيث يقدم مجموعة واسعة من المنتجات عالية الجودة بأسعار تنافسية. استمتع بتجربة تسوق سلسة وآمنة مع خدمة توصيل سريعة وخيارات دفع متعددة. استخدم الرابط التالي للتسوق: {{ route('affiliate.track', ['code' => $affiliate->code]) }}">
                                <i class="fas fa-copy me-1"></i> نسخ النص
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">نص ترويجي للعروض</h6>
                        </div>
                        <div class="card-body">
                            <p>عروض حصرية ولفترة محدودة! استفد من خصومات تصل إلى 50% على تشكيلة واسعة من المنتجات في {{ config('app.name') }}. لا تفوت هذه الفرصة الرائعة للحصول على منتجاتك المفضلة بأسعار مخفضة.</p>
                            <button class="btn btn-sm btn-primary copy-text" data-text="عروض حصرية ولفترة محدودة! استفد من خصومات تصل إلى 50% على تشكيلة واسعة من المنتجات في {{ config('app.name') }}. لا تفوت هذه الفرصة الرائعة للحصول على منتجاتك المفضلة بأسعار مخفضة. تسوق الآن: {{ route('affiliate.track', ['code' => $affiliate->code]) }}">
                                <i class="fas fa-copy me-1"></i> نسخ النص
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">نص ترويجي للمنتجات الجديدة</h6>
                        </div>
                        <div class="card-body">
                            <p>اكتشف أحدث المنتجات التي وصلت للتو إلى {{ config('app.name') }}! منتجات عصرية ومبتكرة تلبي احتياجاتك وتضيف لمسة من التميز إلى حياتك. كن أول من يحصل على هذه المنتجات الحصرية.</p>
                            <button class="btn btn-sm btn-primary copy-text" data-text="اكتشف أحدث المنتجات التي وصلت للتو إلى {{ config('app.name') }}! منتجات عصرية ومبتكرة تلبي احتياجاتك وتضيف لمسة من التميز إلى حياتك. كن أول من يحصل على هذه المنتجات الحصرية. اكتشف الجديد: {{ route('affiliate.track', ['code' => $affiliate->code]) }}">
                                <i class="fas fa-copy me-1"></i> نسخ النص
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">نص ترويجي لمواسم التخفيضات</h6>
                        </div>
                        <div class="card-body">
                            <p>موسم التخفيضات الكبرى قد بدأ في {{ config('app.name') }}! استمتع بخصومات هائلة على جميع الأقسام. تسوق الآن واحصل على أفضل العروض قبل نفاد الكمية. شحن مجاني للطلبات فوق 200 ريال.</p>
                            <button class="btn btn-sm btn-primary copy-text" data-text="موسم التخفيضات الكبرى قد بدأ في {{ config('app.name') }}! استمتع بخصومات هائلة على جميع الأقسام. تسوق الآن واحصل على أفضل العروض قبل نفاد الكمية. شحن مجاني للطلبات فوق 200 ريال. تسوق الآن: {{ route('affiliate.track', ['code' => $affiliate->code]) }}">
                                <i class="fas fa-copy me-1"></i> نسخ النص
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- نصائح تسويقية -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">نصائح لتحسين أداء التسويق بالعمولة</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-primary">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <i class="fas fa-bullseye fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title text-center">استهدف الجمهور المناسب</h5>
                            <p class="card-text">ركز على الجمهور المهتم بالمنتجات التي تروج لها. كلما كان الجمهور أكثر استهدافاً، زادت نسبة التحويل ومعدل النقر.</p>
                            <ul class="mt-3">
                                <li>حدد اهتمامات جمهورك المستهدف</li>
                                <li>استخدم المحتوى المناسب لهذا الجمهور</li>
                                <li>اختر المنصات التي يتواجد فيها جمهورك</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-success">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <i class="fas fa-edit fa-3x text-success"></i>
                            </div>
                            <h5 class="card-title text-center">أنشئ محتوى قيّم</h5>
                            <p class="card-text">قدم محتوى مفيد وجذاب يشرح فوائد المنتجات ومميزاتها. المراجعات الصادقة والشرح التفصيلي يزيد من ثقة المستخدمين.</p>
                            <ul class="mt-3">
                                <li>اكتب مراجعات تفصيلية للمنتجات</li>
                                <li>شارك تجارب حقيقية مع المنتجات</li>
                                <li>قدم مقارنات بين المنتجات المختلفة</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-info">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <i class="fas fa-chart-line fa-3x text-info"></i>
                            </div>
                            <h5 class="card-title text-center">تتبع وتحسين الأداء</h5>
                            <p class="card-text">راقب أداء روابطك التسويقية باستمرار وحلل البيانات لمعرفة ما يعمل بشكل جيد وما يحتاج إلى تحسين.</p>
                            <ul class="mt-3">
                                <li>تتبع معدلات النقر والتحويل</li>
                                <li>جرب أساليب تسويقية مختلفة</li>
                                <li>حسّن استراتيجيتك بناءً على النتائج</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // نسخ الكود HTML
        document.querySelectorAll('.copy-code').forEach(button => {
            button.addEventListener('click', function() {
                const code = this.getAttribute('data-code');
                copyToClipboard(code);
                showToast('تم نسخ الكود بنجاح');
                this.innerHTML = '<i class="fas fa-check me-1"></i> تم النسخ';
                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-code me-1"></i> نسخ الكود';
                }, 2000);
            });
        });

        // نسخ النص
        document.querySelectorAll('.copy-text').forEach(button => {
            button.addEventListener('click', function() {
                const text = this.getAttribute('data-text');
                copyToClipboard(text);
                showToast('تم نسخ النص بنجاح');
                this.innerHTML = '<i class="fas fa-check me-1"></i> تم النسخ';
                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-copy me-1"></i> نسخ النص';
                }, 2000);
            });
        });

        // وظيفة النسخ إلى الحافظة
        function copyToClipboard(text) {
            const el = document.createElement('textarea');
            el.value = text;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
        }

        // إظهار رسالة تأكيد النسخ
        function showToast(message) {
            // يمكن استبدال هذا بمكتبة toast إذا كانت متاحة
            const toast = document.createElement('div');
            toast.className = 'position-fixed bottom-0 end-0 p-3';
            toast.style.zIndex = '5000';
            toast.innerHTML = `
                <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="me-auto">إشعار</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 3000);
        }
    });
</script>
@endsection 