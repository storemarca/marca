@extends('layouts.user')

@section('title', __('track_order'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ __('track_order') }}</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <div class="mb-4">
                        <p>{{ __('track_order_description') }}</p>
                    </div>
                    
                    <form action="{{ route('orders.track.submit') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="order_number" class="form-label">{{ __('order_number') }}</label>
                            <input type="text" class="form-control @error('order_number') is-invalid @enderror" 
                                id="order_number" name="order_number" value="{{ old('order_number') }}" required>
                            @error('order_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('email') }}</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                id="email" name="email" value="{{ old('email') }}" required>
                            <div class="form-text">{{ __('email_used_for_order') }}</div>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> {{ __('track_order') }}
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-4 text-center">
                        <p>{{ __('have_account') }} <a href="{{ route('login') }}">{{ __('login') }}</a> {{ __('to_view_all_orders') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 