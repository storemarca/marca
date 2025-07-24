@extends('layouts.user')

@section('title', 'الإشعارات')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">الإشعارات</h1>
        <div>
            @if($notifications->count() > 0)
                <form action="{{ route('user.notifications.read-all') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-check-double me-1"></i> تحديد الكل كمقروء
                    </button>
                </form>
                <form action="{{ route('user.notifications.destroy-all') }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف جميع الإشعارات؟')">
                        <i class="fas fa-trash me-1"></i> حذف الكل
                    </button>
                </form>
            @endif
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

    <div class="card shadow-sm">
        @if($notifications->count() > 0)
            <div class="list-group list-group-flush">
                @foreach($notifications as $notification)
                    <div class="list-group-item list-group-item-action p-4 {{ $notification->read_at ? '' : 'bg-light' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="notification-icon me-3">
                                @switch($notification->data['type'] ?? 'default')
                                    @case('order')
                                        <div class="icon-circle bg-primary text-white">
                                            <i class="fas fa-shopping-bag"></i>
                                        </div>
                                        @break
                                    @case('shipment')
                                        <div class="icon-circle bg-info text-white">
                                            <i class="fas fa-truck"></i>
                                        </div>
                                        @break
                                    @case('return')
                                        <div class="icon-circle bg-warning text-white">
                                            <i class="fas fa-exchange-alt"></i>
                                        </div>
                                        @break
                                    @case('review')
                                        <div class="icon-circle bg-success text-white">
                                            <i class="fas fa-star"></i>
                                        </div>
                                        @break
                                    @case('loyalty')
                                        <div class="icon-circle bg-purple text-white">
                                            <i class="fas fa-award"></i>
                                        </div>
                                        @break
                                    @default
                                        <div class="icon-circle bg-secondary text-white">
                                            <i class="fas fa-bell"></i>
                                        </div>
                                @endswitch
                            </div>
                            
                            <div class="flex-grow-1">
                                <h6 class="mb-1 {{ $notification->read_at ? '' : 'fw-bold' }}">
                                    {{ $notification->data['title'] ?? 'إشعار' }}
                                </h6>
                                <p class="mb-2">{{ $notification->data['message'] ?? '' }}</p>
                                <div class="d-flex align-items-center text-muted small">
                                    <span><i class="fas fa-clock me-1"></i> {{ $notification->created_at->diffForHumans() }}</span>
                                    
                                    @if(isset($notification->data['link']))
                                        <a href="{{ $notification->data['link'] }}" class="ms-3">
                                            <i class="fas fa-external-link-alt me-1"></i> عرض التفاصيل
                                        </a>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="ms-3 d-flex">
                                @if(!$notification->read_at)
                                    <form action="{{ route('user.notifications.read', $notification->id) }}" method="POST" class="me-2">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="تحديد كمقروء">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                <form action="{{ route('user.notifications.destroy', $notification->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف" onclick="return confirm('هل أنت متأكد من حذف هذا الإشعار؟')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="p-3">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-bell-slash fa-4x text-muted"></i>
                </div>
                <h5>لا توجد إشعارات</h5>
                <p class="text-muted">ستظهر هنا الإشعارات المتعلقة بطلباتك ونشاطاتك على المنصة.</p>
            </div>
        @endif
    </div>
</div>

<style>
    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .bg-purple {
        background-color: #6f42c1;
    }
</style>
@endsection 