@extends('layouts.admin')

@section('title', 'Payment Gateways')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Payment Gateways</h1>
        <a href="{{ route('admin.payment-gateways.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add New Gateway
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Manage Payment Gateways</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Status</th>
                            <th>Default</th>
                            <th>Fee</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paymentGateways as $gateway)
                            <tr>
                                <td>
                                    @if ($gateway->logo)
                                        <img src="{{ asset('storage/' . $gateway->logo) }}" alt="{{ $gateway->name }}" height="30">
                                    @else
                                        <span class="badge badge-secondary">No Logo</span>
                                    @endif
                                </td>
                                <td>{{ $gateway->name }}</td>
                                <td>{{ $gateway->code }}</td>
                                <td>
                                    <form action="{{ route('admin.payment-gateways.toggle-active', $gateway) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $gateway->is_active ? 'btn-success' : 'btn-secondary' }}">
                                            {{ $gateway->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    @if ($gateway->is_default)
                                        <span class="badge badge-primary">Default</span>
                                    @else
                                        <span class="badge badge-light">No</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $gateway->fee_percentage }}% + {{ number_format($gateway->fee_fixed, 2) }}
                                </td>
                                <td>
                                    <a href="{{ route('admin.payment-gateways.show', $gateway) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.payment-gateways.edit', $gateway) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.payment-gateways.destroy', $gateway) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this gateway?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No payment gateways found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 