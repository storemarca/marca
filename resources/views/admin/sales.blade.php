@extends('layouts.admin')

@section('title', 'المبيعات')
@section('page-title', 'إدارة المبيعات')

@section('content')
<div class="container-fluid fade-in">
    <div class="row g-4 mb-4">
        <!-- إحصائيات حالات الطلبات -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">حالات الطلبات</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>الحالة</th>
                                    <th>الوصف</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <span class="badge bg-warning">قيد الانتظار</span>
                                    </td>
                                    <td>
                                        الطلبات التي تم إنشاؤها ولكن لم تتم معالجتها بعد
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="badge bg-info">قيد المعالجة</span>
                                    </td>
                                    <td>
                                        الطلبات التي بدأت معالجتها وتجهيزها
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">تم الشحن</span>
                                    </td>
                                    <td>
                                        الطلبات التي تم شحنها وإرسالها للعملاء
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="badge bg-info">في الطريق</span>
                                    </td>
                                    <td>
                                        الطلبات التي في طريقها للتسليم
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="badge bg-success">تم التسليم</span>
                                    </td>
                                    <td>
                                        الطلبات التي تم تسليمها للعملاء بنجاح
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="badge bg-danger">ملغي</span>
                                    </td>
                                    <td>
                                        الطلبات التي تم إلغاؤها
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- إحصائيات حالات الدفع -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">حالات الدفع</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>الحالة</th>
                                    <th>الوصف</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <span class="badge bg-warning">قيد الانتظار</span>
                                    </td>
                                    <td>
                                        الدفعات التي لم تتم معالجتها بعد
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="badge bg-success">مدفوع</span>
                                    </td>
                                    <td>
                                        الدفعات التي تمت معالجتها بنجاح
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="badge bg-danger">فشل الدفع</span>
                                    </td>
                                    <td>
                                        الدفعات التي فشلت لأي سبب من الأسباب
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="badge bg-info">تم الاسترجاع</span>
                                    </td>
                                    <td>
                                        الدفعات التي تم استرجاعها للعميل
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="badge bg-info">تم الاسترجاع جزئياً</span>
                                    </td>
                                    <td>
                                        الدفعات التي تم استرجاع جزء منها للعميل
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 