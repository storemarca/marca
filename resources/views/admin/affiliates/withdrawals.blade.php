@extends('layouts.admin')

@section('title', 'إدارة طلبات السحب للمسوقين بالعمولة')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة طلبات السحب للمسوقين بالعمولة</h1>
        <div>
            <a href="{{ route('admin.affiliates.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-users"></i> قائمة المسوقين
            </a>
            <a href="{{ route('admin.affiliates.dashboard') }}" class="btn btn-sm btn-primary">
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

    <!-- إحصائيات سريعة -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">طلبات قيد المراجعة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $withdrawalRequests->where('status', 'pending')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">إجمالي المبالغ المعلقة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($withdrawalRequests->where('status', 'pending')->sum('amount'), 2) }} ر.س
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">طلبات تم دفعها</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $withdrawalRequests->where('status', 'paid')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">طلبات مرفوضة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $withdrawalRequests->where('status', 'rejected')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
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
                <div class="col-md-4 mb-3">
                    <label for="status">الحالة</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">الكل</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="search">بحث</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="اسم المسوق أو البريد الإلكتروني" value="{{ request('search') }}">
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">بحث</button>
                    <a href="{{ route('admin.affiliates.withdrawal-requests') }}" class="btn btn-secondary">إعادة ضبط</a>
                </div>
            </form>
        </div>
    </div>

    <!-- جدول طلبات السحب -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">طلبات السحب</h6>
        </div>
        <div class="card-body">
            @if($withdrawalRequests->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered" id="withdrawalsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>رقم الطلب</th>
                            <th>المسوق</th>
                            <th>المبلغ</th>
                            <th>طريقة الدفع</th>
                            <th>تاريخ الطلب</th>
                            <th>الحالة</th>
                            <th>تاريخ المعالجة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($withdrawalRequests as $withdrawal)
                        <tr>
                            <td>{{ $withdrawal->id }}</td>
                            <td>
                                <a href="{{ route('admin.affiliates.show', $withdrawal->affiliate) }}">
                                    {{ $withdrawal->affiliate->user->name }}
                                </a>
                            </td>
                            <td>{{ number_format($withdrawal->amount, 2) }} ر.س</td>
                            <td>{{ $withdrawal->payment_method }}</td>
                            <td>{{ $withdrawal->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                @if($withdrawal->status == 'pending')
                                    <span class="badge badge-warning">{{ $withdrawal->status_text }}</span>
                                @elseif($withdrawal->status == 'approved')
                                    <span class="badge badge-info">{{ $withdrawal->status_text }}</span>
                                @elseif($withdrawal->status == 'rejected')
                                    <span class="badge badge-danger">{{ $withdrawal->status_text }}</span>
                                @elseif($withdrawal->status == 'paid')
                                    <span class="badge badge-success">{{ $withdrawal->status_text }}</span>
                                @endif
                            </td>
                            <td>{{ $withdrawal->processed_at ? $withdrawal->processed_at->format('Y-m-d H:i') : '-' }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#detailsModal{{ $withdrawal->id }}">
                                    <i class="fas fa-info-circle"></i> التفاصيل
                                </button>
                                
                                @if($withdrawal->status == 'pending')
                                <form action="{{ route('admin.affiliates.approve-withdrawal', $withdrawal) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i> الموافقة
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal{{ $withdrawal->id }}">
                                    <i class="fas fa-times"></i> الرفض
                                </button>
                                @endif
                                
                                @if($withdrawal->status == 'approved')
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#markPaidModal{{ $withdrawal->id }}">
                                    <i class="fas fa-money-bill"></i> تحديد كمدفوع
                                </button>
                                @endif

                                <!-- Modal: Details -->
                                <div class="modal fade" id="detailsModal{{ $withdrawal->id }}" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel{{ $withdrawal->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="detailsModalLabel{{ $withdrawal->id }}">تفاصيل طلب السحب #{{ $withdrawal->id }}</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6>معلومات المسوق</h6>
                                                        <table class="table table-bordered">
                                                            <tr>
                                                                <th>الاسم</th>
                                                                <td>{{ $withdrawal->affiliate->user->name }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>البريد الإلكتروني</th>
                                                                <td>{{ $withdrawal->affiliate->user->email }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>رمز المسوق</th>
                                                                <td>{{ $withdrawal->affiliate->code }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>الرصيد الحالي</th>
                                                                <td>{{ number_format($withdrawal->affiliate->balance, 2) }} ر.س</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6>تفاصيل طلب السحب</h6>
                                                        <table class="table table-bordered">
                                                            <tr>
                                                                <th>المبلغ</th>
                                                                <td>{{ number_format($withdrawal->amount, 2) }} ر.س</td>
                                                            </tr>
                                                            <tr>
                                                                <th>طريقة الدفع</th>
                                                                <td>{{ $withdrawal->payment_method }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>تاريخ الطلب</th>
                                                                <td>{{ $withdrawal->created_at->format('Y-m-d H:i') }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>الحالة</th>
                                                                <td>{{ $withdrawal->status_text }}</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                                
                                                <div class="mt-4">
                                                    <h6>تفاصيل الدفع المقدمة من المسوق</h6>
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <pre>{{ $withdrawal->payment_details }}</pre>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                @if($withdrawal->rejection_reason)
                                                <div class="mt-4">
                                                    <h6>سبب الرفض</h6>
                                                    <div class="alert alert-danger">
                                                        {{ $withdrawal->rejection_reason }}
                                                    </div>
                                                </div>
                                                @endif
                                                
                                                @if($withdrawal->transaction_reference)
                                                <div class="mt-4">
                                                    <h6>مرجع المعاملة</h6>
                                                    <div class="alert alert-info">
                                                        {{ $withdrawal->transaction_reference }}
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal: Reject Withdrawal -->
                                <div class="modal fade" id="rejectModal{{ $withdrawal->id }}" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel{{ $withdrawal->id }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.affiliates.reject-withdrawal', $withdrawal) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="rejectModalLabel{{ $withdrawal->id }}">رفض طلب السحب</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="rejection_reason">سبب الرفض</label>
                                                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                                    <button type="submit" class="btn btn-danger">رفض الطلب</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal: Mark as Paid -->
                                <div class="modal fade" id="markPaidModal{{ $withdrawal->id }}" tabindex="-1" role="dialog" aria-labelledby="markPaidModalLabel{{ $withdrawal->id }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.affiliates.mark-withdrawal-paid', $withdrawal) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="markPaidModalLabel{{ $withdrawal->id }}">تحديد طلب السحب كمدفوع</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="transaction_reference">رقم مرجع المعاملة</label>
                                                        <input type="text" class="form-control" id="transaction_reference" name="transaction_reference" required>
                                                        <small class="form-text text-muted">أدخل رقم مرجع المعاملة أو أي معلومات تتعلق بعملية الدفع.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                                    <button type="submit" class="btn btn-primary">تحديد كمدفوع</button>
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
            @else
            <div class="alert alert-info">لا توجد طلبات سحب متطابقة مع معايير البحث.</div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#withdrawalsTable').DataTable({
            "paging": false,
            "searching": false,
            "ordering": true,
            "info": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Arabic.json"
            }
        });
    });
</script>
@endpush
@endsection 