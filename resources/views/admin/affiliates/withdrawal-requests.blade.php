@extends('layouts.admin')

@section('title', 'إدارة طلبات سحب الأرباح')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة طلبات سحب الأرباح</h1>
        <div>
            <a href="{{ route('admin.affiliates.index') }}" class="btn btn-outline-primary btn-sm me-2">
                <i class="fas fa-users"></i> المسوقين بالعمولة
            </a>
            <a href="{{ route('admin.affiliates.dashboard') }}" class="btn btn-outline-info btn-sm">
                <i class="fas fa-chart-line"></i> لوحة التحكم
            </a>
        </div>
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

    <!-- ملخص طلبات السحب -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">إجمالي الطلبات</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalRequests }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">طلبات قيد المراجعة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingRequests }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">المبالغ المدفوعة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($paidAmount, 2) }} ر.س</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">المبالغ المعلقة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($pendingAmount, 2) }} ر.س</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-check-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- فلتر البحث -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">بحث وتصفية</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.affiliates.withdrawal-requests') }}" method="GET" class="row">
                <div class="col-md-3 mb-3">
                    <label for="affiliate_id">المسوق بالعمولة</label>
                    <select class="form-control" id="affiliate_id" name="affiliate_id">
                        <option value="">الكل</option>
                        @foreach($affiliates as $affiliate)
                            <option value="{{ $affiliate->id }}" {{ request('affiliate_id') == $affiliate->id ? 'selected' : '' }}>
                                {{ $affiliate->user->name }} ({{ $affiliate->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="status">الحالة</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">الكل</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>تمت الموافقة</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>تم الدفع</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="payment_method">طريقة الدفع</label>
                    <select class="form-control" id="payment_method" name="payment_method">
                        <option value="">الكل</option>
                        <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                        <option value="paypal" {{ request('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                        <option value="western_union" {{ request('payment_method') == 'western_union' ? 'selected' : '' }}>Western Union</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="date_from">من تاريخ</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="date_to">إلى تاريخ</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-1 mb-3">
                    <label for="per_page">العدد</label>
                    <select class="form-control" id="per_page" name="per_page">
                        <option value="15" {{ request('per_page') == 15 || !request('per_page') ? 'selected' : '' }}>15</option>
                        <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">تصفية</button>
                    <a href="{{ route('admin.affiliates.withdrawal-requests') }}" class="btn btn-secondary">إعادة تعيين</a>
                </div>
            </form>
        </div>
    </div>

    <!-- جدول طلبات السحب -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">طلبات سحب الأرباح</h6>
            <div>
                <a href="{{ route('admin.affiliates.export-withdrawal-requests') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel"></i> تصدير إلى Excel
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="withdrawalRequestsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>المعرف</th>
                            <th>المسوق</th>
                            <th>المبلغ</th>
                            <th>طريقة الدفع</th>
                            <th>تاريخ الطلب</th>
                            <th>الحالة</th>
                            <th>تاريخ الدفع</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($withdrawalRequests as $request)
                            <tr>
                                <td>{{ $request->id }}</td>
                                <td>
                                    <div>{{ $request->affiliate->user->name }}</div>
                                    <small>{{ $request->affiliate->user->email }}</small>
                                </td>
                                <td>{{ number_format($request->amount, 2) }} ر.س</td>
                                <td>{{ $request->payment_method }}</td>
                                <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <span class="badge {{ 
                                        $request->status == 'paid' ? 'bg-success' : 
                                        ($request->status == 'pending' ? 'bg-warning' : 
                                        ($request->status == 'approved' ? 'bg-info' : 'bg-danger')) 
                                    }}">
                                        {{ $request->status_text }}
                                    </span>
                                </td>
                                <td>
                                    @if($request->paid_at)
                                        {{ $request->paid_at->format('Y-m-d') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal{{ $request->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($request->status == 'pending')
                                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $request->id }}">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @elseif($request->status == 'approved')
                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#markPaidModal{{ $request->id }}">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </button>
                                        @endif
                                    </div>

                                    <!-- View Modal -->
                                    <div class="modal fade" id="viewModal{{ $request->id }}" tabindex="-1" aria-labelledby="viewModalLabel{{ $request->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="viewModalLabel{{ $request->id }}">تفاصيل طلب السحب #{{ $request->id }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6>معلومات الطلب</h6>
                                                            <table class="table table-bordered">
                                                                <tr>
                                                                    <th>المعرف</th>
                                                                    <td>{{ $request->id }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>المبلغ</th>
                                                                    <td>{{ number_format($request->amount, 2) }} ر.س</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>طريقة الدفع</th>
                                                                    <td>{{ $request->payment_method }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>تاريخ الطلب</th>
                                                                    <td>{{ $request->created_at->format('Y-m-d H:i:s') }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>الحالة</th>
                                                                    <td>
                                                                        <span class="badge {{ 
                                                                            $request->status == 'paid' ? 'bg-success' : 
                                                                            ($request->status == 'pending' ? 'bg-warning' : 
                                                                            ($request->status == 'approved' ? 'bg-info' : 'bg-danger')) 
                                                                        }}">
                                                                            {{ $request->status_text }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                                @if($request->paid_at)
                                                                    <tr>
                                                                        <th>تاريخ الدفع</th>
                                                                        <td>{{ $request->paid_at->format('Y-m-d H:i:s') }}</td>
                                                                    </tr>
                                                                @endif
                                                                @if($request->notes)
                                                                    <tr>
                                                                        <th>ملاحظات المسوق</th>
                                                                        <td>{{ $request->notes }}</td>
                                                                    </tr>
                                                                @endif
                                                                @if($request->admin_notes)
                                                                    <tr>
                                                                        <th>ملاحظات الإدارة</th>
                                                                        <td>{{ $request->admin_notes }}</td>
                                                                    </tr>
                                                                @endif
                                                            </table>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6>معلومات المسوق</h6>
                                                            <table class="table table-bordered">
                                                                <tr>
                                                                    <th>الاسم</th>
                                                                    <td>{{ $request->affiliate->user->name }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>البريد الإلكتروني</th>
                                                                    <td>{{ $request->affiliate->user->email }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>رمز المسوق</th>
                                                                    <td>{{ $request->affiliate->code }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>الرصيد الحالي</th>
                                                                    <td>{{ number_format($request->affiliate->balance, 2) }} ر.س</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>إجمالي الأرباح</th>
                                                                    <td>{{ number_format($request->affiliate->lifetime_earnings, 2) }} ر.س</td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-md-12">
                                                            <h6>تفاصيل الدفع</h6>
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    {{ $request->payment_details }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                                    @if($request->status == 'pending')
                                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $request->id }}">
                                                            <i class="fas fa-check me-1"></i> موافقة
                                                        </button>
                                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}">
                                                            <i class="fas fa-times me-1"></i> رفض
                                                        </button>
                                                    @elseif($request->status == 'approved')
                                                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#markPaidModal{{ $request->id }}">
                                                            <i class="fas fa-money-bill-wave me-1"></i> تأكيد الدفع
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Approve Modal -->
                                    <div class="modal fade" id="approveModal{{ $request->id }}" tabindex="-1" aria-labelledby="approveModalLabel{{ $request->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.affiliates.approve-withdrawal', $request) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="approveModalLabel{{ $request->id }}">الموافقة على طلب السحب</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>هل أنت متأكد من الموافقة على طلب سحب المبلغ {{ number_format($request->amount, 2) }} ر.س للمسوق {{ $request->affiliate->user->name }}؟</p>
                                                        <div class="mb-3">
                                                            <label for="admin_notes" class="form-label">ملاحظات (اختياري)</label>
                                                            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                        <button type="submit" class="btn btn-success">موافقة</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Reject Modal -->
                                    <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $request->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.affiliates.reject-withdrawal', $request) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="rejectModalLabel{{ $request->id }}">رفض طلب السحب</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>هل أنت متأكد من رفض طلب سحب المبلغ {{ number_format($request->amount, 2) }} ر.س للمسوق {{ $request->affiliate->user->name }}؟</p>
                                                        <div class="mb-3">
                                                            <label for="admin_notes" class="form-label">سبب الرفض <span class="text-danger">*</span></label>
                                                            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                        <button type="submit" class="btn btn-danger">رفض</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Mark Paid Modal -->
                                    <div class="modal fade" id="markPaidModal{{ $request->id }}" tabindex="-1" aria-labelledby="markPaidModalLabel{{ $request->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.affiliates.mark-paid-withdrawal', $request) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="markPaidModalLabel{{ $request->id }}">تأكيد دفع المبلغ</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>هل أنت متأكد من تأكيد دفع المبلغ {{ number_format($request->amount, 2) }} ر.س للمسوق {{ $request->affiliate->user->name }}؟</p>
                                                        <div class="mb-3">
                                                            <label for="transaction_id" class="form-label">رقم العملية (اختياري)</label>
                                                            <input type="text" class="form-control" id="transaction_id" name="transaction_id">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="admin_notes" class="form-label">ملاحظات (اختياري)</label>
                                                            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                        <button type="submit" class="btn btn-info">تأكيد الدفع</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $withdrawalRequests->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 