@extends('layouts.admin')

@section('title', 'إدارة المسوقين بالعمولة')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة المسوقين بالعمولة</h1>
        <a href="{{ route('admin.affiliates.dashboard') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-chart-line"></i> لوحة التحكم
        </a>
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

    <!-- الإحصائيات -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">إجمالي المسوقين</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAffiliates }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingAffiliates }}</div>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">المسوقين النشطين</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeAffiliates }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">إجمالي العمولات</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalEarnings, 2) }} ر.س</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
            <form action="{{ route('admin.affiliates.index') }}" method="GET" class="row">
                <div class="col-md-4 mb-3">
                    <label for="search">بحث</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="اسم المستخدم، البريد الإلكتروني، الرمز">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="status">الحالة</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">الكل</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>مفعل</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>معلق</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="sort">ترتيب حسب</label>
                    <select class="form-control" id="sort" name="sort">
                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>تاريخ التسجيل</option>
                        <option value="balance" {{ request('sort') == 'balance' ? 'selected' : '' }}>الرصيد</option>
                        <option value="lifetime_earnings" {{ request('sort') == 'lifetime_earnings' ? 'selected' : '' }}>إجمالي الأرباح</option>
                        <option value="commission_rate" {{ request('sort') == 'commission_rate' ? 'selected' : '' }}>نسبة العمولة</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="direction">الاتجاه</label>
                    <select class="form-control" id="direction" name="direction">
                        <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>تنازلي</option>
                        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>تصاعدي</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">تصفية</button>
                    <a href="{{ route('admin.affiliates.index') }}" class="btn btn-secondary">إعادة تعيين</a>
                    <a href="{{ route('admin.affiliates.withdrawal-requests') }}" class="btn btn-info float-right">
                        <i class="fas fa-money-check-alt"></i> طلبات السحب 
                        <span class="badge badge-light">{{ $pendingWithdrawals > 0 ? number_format($pendingWithdrawals, 2) . ' ر.س' : '0' }}</span>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- جدول المسوقين -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">قائمة المسوقين بالعمولة</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="affiliatesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>المعرف</th>
                            <th>المستخدم</th>
                            <th>رمز الإحالة</th>
                            <th>الحالة</th>
                            <th>نسبة العمولة</th>
                            <th>الرصيد</th>
                            <th>إجمالي الأرباح</th>
                            <th>تاريخ التسجيل</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($affiliates as $affiliate)
                            <tr>
                                <td>{{ $affiliate->id }}</td>
                                <td>
                                    <div>{{ $affiliate->user->name }}</div>
                                    <small>{{ $affiliate->user->email }}</small>
                                </td>
                                <td><code>{{ $affiliate->code }}</code></td>
                                <td>
                                    <span class="badge {{ 
                                        $affiliate->status == 'approved' ? 'bg-success' : 
                                        ($affiliate->status == 'pending' ? 'bg-warning' : 
                                        ($affiliate->status == 'rejected' ? 'bg-danger' : 'bg-secondary')) 
                                    }}">
                                        {{ $affiliate->status_text }}
                                    </span>
                                </td>
                                <td>{{ $affiliate->commission_rate }}%</td>
                                <td>{{ number_format($affiliate->balance, 2) }} ر.س</td>
                                <td>{{ number_format($affiliate->lifetime_earnings, 2) }} ر.س</td>
                                <td>{{ $affiliate->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.affiliates.show', $affiliate) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($affiliate->status == 'pending')
                                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $affiliate->id }}">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $affiliate->id }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @elseif($affiliate->status == 'approved')
                                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#suspendModal{{ $affiliate->id }}">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#commissionModal{{ $affiliate->id }}">
                                                <i class="fas fa-percentage"></i>
                                            </button>
                                        @elseif($affiliate->status == 'rejected' || $affiliate->status == 'suspended')
                                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $affiliate->id }}">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Approve Modal -->
                                    <div class="modal fade" id="approveModal{{ $affiliate->id }}" tabindex="-1" aria-labelledby="approveModalLabel{{ $affiliate->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="approveModalLabel{{ $affiliate->id }}">تأكيد الموافقة</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    هل أنت متأكد من الموافقة على طلب المسوق بالعمولة <strong>{{ $affiliate->user->name }}</strong>؟
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                    <form action="{{ route('admin.affiliates.approve', $affiliate) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success">موافقة</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Reject Modal -->
                                    <div class="modal fade" id="rejectModal{{ $affiliate->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $affiliate->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="rejectModalLabel{{ $affiliate->id }}">رفض الطلب</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.affiliates.reject', $affiliate) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="rejection_reason" class="form-label">سبب الرفض</label>
                                                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
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

                                    <!-- Suspend Modal -->
                                    <div class="modal fade" id="suspendModal{{ $affiliate->id }}" tabindex="-1" aria-labelledby="suspendModalLabel{{ $affiliate->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="suspendModalLabel{{ $affiliate->id }}">تعليق الحساب</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.affiliates.suspend', $affiliate) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="rejection_reason" class="form-label">سبب التعليق</label>
                                                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                        <button type="submit" class="btn btn-warning">تعليق</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Commission Modal -->
                                    <div class="modal fade" id="commissionModal{{ $affiliate->id }}" tabindex="-1" aria-labelledby="commissionModalLabel{{ $affiliate->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="commissionModalLabel{{ $affiliate->id }}">تعديل نسبة العمولة</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.affiliates.update-commission-rate', $affiliate) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="commission_rate" class="form-label">نسبة العمولة (%)</label>
                                                            <input type="number" class="form-control" id="commission_rate" name="commission_rate" value="{{ $affiliate->commission_rate }}" min="0" max="100" step="0.01" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                        <button type="submit" class="btn btn-primary">حفظ</button>
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
                {{ $affiliates->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 