@extends('layouts.admin')

@section('title', __('collections.title'))

@section('content')
<div class="container">
    <h1 class="mb-4">{{ __('collections.title') }}</h1>

    {{-- الإحصائيات --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5>{{ __('collections.total_collections') }}</h5>
                    <p>{{ $totalCollections }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5>{{ __('collections.pending_collections') }}</h5>
                    <p>{{ $pendingCollections }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5>{{ __('collections.collected_amount') }}</h5>
                    <p>{{ number_format($collectedAmount, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5>{{ __('collections.pending_amount') }}</h5>
                    <p>{{ number_format($pendingAmount, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- الفلاتر --}}
    <form method="GET" class="mb-4 row g-3">
        <div class="col-md-2">
            <select name="status" class="form-control">
                <option value="">{{ __('collections.all_statuses') }}</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                    {{ __('collections.status_pending') }}
                </option>
                <option value="collected" {{ request('status') === 'collected' ? 'selected' : '' }}>
                    {{ __('collections.status_collected') }}
                </option>
                <option value="settled" {{ request('status') === 'settled' ? 'selected' : '' }}>
                    {{ __('collections.status_settled') }}
                </option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="date_range" class="form-control">
                <option value="">{{ __('collections.all_dates') }}</option>
                <option value="today" {{ request('date_range') === 'today' ? 'selected' : '' }}>{{ __('collections.today') }}</option>
                <option value="yesterday" {{ request('date_range') === 'yesterday' ? 'selected' : '' }}>{{ __('collections.yesterday') }}</option>
                <option value="this_week" {{ request('date_range') === 'this_week' ? 'selected' : '' }}>{{ __('collections.this_week') }}</option>
                <option value="last_week" {{ request('date_range') === 'last_week' ? 'selected' : '' }}>{{ __('collections.last_week') }}</option>
                <option value="this_month" {{ request('date_range') === 'this_month' ? 'selected' : '' }}>{{ __('collections.this_month') }}</option>
                <option value="last_month" {{ request('date_range') === 'last_month' ? 'selected' : '' }}>{{ __('collections.last_month') }}</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="col-md-2">
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">{{ __('collections.filter') }}</button>
        </div>
    </form>

    {{-- جدول التحصيلات --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>{{ __('collections.id') }}</th>
                    <th>{{ __('collections.amount') }}</th>
                    <th>{{ __('collections.status') }}</th>
                    <th>{{ __('collections.customer') }}</th>
                    <th>{{ __('collections.collector') }}</th>
                    <th>{{ __('collections.created_at') }}</th>
                    <th>{{ __('collections.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($collections as $collection)
                    <tr>
                        <td>#{{ $collection->id }}</td>
                        <td>{{ number_format($collection->amount, 2) }}</td>
                        <td>{{ __('collections.status_' . $collection->status) }}</td>
                        <td>
                            {{ optional($collection->shipment->order->customer)->name }}
                        </td>
                        <td>{{ optional($collection->collector)->name ?? '-' }}</td>
                        <td>{{ $collection->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.collections.show', $collection) }}" class="btn btn-sm btn-info">
                                {{ __('collections.view') }}
                            </a>
                            <a href="{{ route('admin.collections.edit', $collection) }}" class="btn btn-sm btn-warning">
                                {{ __('collections.edit') }}
                            </a>
                            @if($collection->status === 'pending')
                                <form action="{{ route('admin.collections.markCollected', $collection) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('{{ __('collections.confirm_collect') }}')">
                                        {{ __('collections.mark_collected') }}
                                    </button>
                                </form>
                            @endif
                            @if($collection->status === 'collected')
                                <form action="{{ route('admin.collections.markSettled', $collection) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-dark" onclick="return confirm('{{ __('collections.confirm_settle') }}')">
                                        {{ __('collections.mark_settled') }}
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">{{ __('collections.no_collections_found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- روابط الصفحات --}}
    <div class="mt-4">
        {{ $collections->withQueryString()->links() }}
    </div>
</div>
@endsection
