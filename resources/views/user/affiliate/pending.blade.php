@extends('layouts.user')

@section('title', 'طلب الانضمام قيد المراجعة')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h1 class="h4 mb-0">طلب الانضمام لبرنامج المسوقين بالعمولة</h1>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-hourglass-half fa-5x text-warning"></i>
                    </div>
                    <h2 class="h4 mb-3">طلبك قيد المراجعة</h2>
                    <p class="lead">شكراً لاهتمامك بالانضمام إلى برنامج المسوقين بالعمولة. طلبك قيد المراجعة حالياً من قبل فريقنا.</p>
                    <p>سيتم إعلامك عبر البريد الإلكتروني بمجرد مراجعة طلبك.</p>
                    
                    <div class="alert alert-info mt-4">
                        <h5>معلومات الطلب:</h5>
                        <p><strong>تاريخ التقديم:</strong> {{ $affiliate->created_at->format('Y-m-d') }}</p>
                        <p><strong>الحالة:</strong> <span class="badge bg-warning">{{ $affiliate->status_text }}</span></p>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('user.home') }}" class="btn btn-primary">العودة إلى الصفحة الرئيسية</a>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h2 class="h5 mb-0">الأسئلة الشائعة</h2>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading1">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="false" aria-controls="faqCollapse1">
                                    كم من الوقت تستغرق مراجعة الطلب؟
                                </button>
                            </h2>
                            <div id="faqCollapse1" class="accordion-collapse collapse" aria-labelledby="faqHeading1" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    عادةً ما تستغرق مراجعة الطلب من 1 إلى 3 أيام عمل. نحن نراجع جميع الطلبات بعناية للتأكد من أن جميع المسوقين بالعمولة يلتزمون بمعاييرنا.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                                    هل يمكنني تعديل معلومات طلبي؟
                                </button>
                            </h2>
                            <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    لا يمكن تعديل الطلب بعد تقديمه. إذا كنت ترغب في تغيير أي معلومات، يرجى الانتظار حتى يتم رفض الطلب ثم التقديم مرة أخرى، أو التواصل مع خدمة العملاء.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                                    ماذا يحدث إذا تم رفض طلبي؟
                                </button>
                            </h2>
                            <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    إذا تم رفض طلبك، سيتم إعلامك بسبب الرفض. يمكنك معالجة المشكلة والتقديم مرة أخرى بعد 14 يومًا من تاريخ الرفض.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqHeading4">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                                    كيف سيتم إعلامي بقرار الموافقة أو الرفض؟
                                </button>
                            </h2>
                            <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    سيتم إرسال إشعار إلى بريدك الإلكتروني المسجل لدينا بمجرد اتخاذ قرار بشأن طلبك. يمكنك أيضًا التحقق من حالة طلبك من خلال حسابك على الموقع.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 