@extends('layouts.admin')

@section('title', 'Add Payment Gateway')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add Payment Gateway</h1>
        <a href="{{ route('admin.payment-gateways.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Gateway Details</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.payment-gateways.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="code">Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required>
                            <small class="form-text text-muted">Unique identifier for the gateway (e.g., stripe, paypal, cash)</small>
                            @error('code')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="logo">Logo</label>
                            <input type="file" class="form-control-file @error('logo') is-invalid @enderror" id="logo" name="logo">
                            <small class="form-text text-muted">Recommended size: 200x100px</small>
                            @error('logo')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-check mt-4">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                        
                        <div class="form-check mt-2">
                            <input type="checkbox" class="form-check-input" id="is_default" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_default">Set as Default Gateway</label>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fee_percentage">Fee Percentage (%) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" max="100" class="form-control @error('fee_percentage') is-invalid @enderror" id="fee_percentage" name="fee_percentage" value="{{ old('fee_percentage', 0) }}" required>
                            @error('fee_percentage')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fee_fixed">Fixed Fee <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control @error('fee_fixed') is-invalid @enderror" id="fee_fixed" name="fee_fixed" value="{{ old('fee_fixed', 0) }}" required>
                            @error('fee_fixed')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Gateway Configuration</h6>
                    </div>
                    <div class="card-body">
                        <div id="config-fields">
                            <!-- Dynamic config fields will be added here -->
                            <div class="row config-row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Config Key</label>
                                        <input type="text" class="form-control" name="config_keys[]" placeholder="e.g. api_key">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Config Value</label>
                                        <input type="text" class="form-control" name="config_values[]" placeholder="Value">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-danger btn-block remove-config">Remove</button>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-success mt-2" id="add-config">Add Config Field</button>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Gateway</button>
                    <a href="{{ route('admin.payment-gateways.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Add new config field
        $('#add-config').click(function() {
            const newRow = `
                <div class="row config-row mt-2">
                    <div class="col-md-5">
                        <div class="form-group">
                            <input type="text" class="form-control" name="config_keys[]" placeholder="e.g. api_key">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <input type="text" class="form-control" name="config_values[]" placeholder="Value">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-block remove-config">Remove</button>
                    </div>
                </div>
            `;
            $('#config-fields').append(newRow);
        });
        
        // Remove config field
        $(document).on('click', '.remove-config', function() {
            $(this).closest('.config-row').remove();
        });
        
        // Process form submission to combine config keys and values
        $('form').submit(function() {
            const keys = $('input[name="config_keys[]"]').map(function() {
                return $(this).val();
            }).get();
            
            const values = $('input[name="config_values[]"]').map(function() {
                return $(this).val();
            }).get();
            
            const config = {};
            
            for (let i = 0; i < keys.length; i++) {
                if (keys[i]) {
                    config[keys[i]] = values[i];
                }
            }
            
            $('<input>').attr({
                type: 'hidden',
                name: 'config',
                value: JSON.stringify(config)
            }).appendTo('form');
            
            return true;
        });
    });
</script>
@endsection 