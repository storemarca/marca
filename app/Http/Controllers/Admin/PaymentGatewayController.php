<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PaymentGatewayController extends Controller
{
    /**
     * Display a listing of the payment gateways.
     */
    public function index()
    {
        $paymentGateways = PaymentGateway::all();
        
        return view('admin.payment_gateways.index', compact('paymentGateways'));
    }

    /**
     * Show the form for creating a new payment gateway.
     */
    public function create()
    {
        return view('admin.payment_gateways.create');
    }

    /**
     * Store a newly created payment gateway in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:payment_gateways',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:1024',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'fee_percentage' => 'required|numeric|min:0|max:100',
            'fee_fixed' => 'required|numeric|min:0',
            'config' => 'nullable|array',
        ]);
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('payment_gateways', 'public');
            $validated['logo'] = $path;
        }
        
        // Handle config data
        if (isset($validated['config'])) {
            // Ensure sensitive data is encrypted if needed
            foreach ($validated['config'] as $key => $value) {
                if (in_array($key, ['api_key', 'secret', 'password'])) {
                    $validated['config'][$key] = encrypt($value);
                }
            }
        }
        
        // If this is set as default, unset all other defaults
        if ($request->is_default) {
            PaymentGateway::where('is_default', true)->update(['is_default' => false]);
        }
        
        $paymentGateway = PaymentGateway::create($validated);
        
        return redirect()
            ->route('admin.payment_gateways.index')
            ->with('success', 'Payment gateway created successfully.');
    }

    /**
     * Display the specified payment gateway.
     */
    public function show(PaymentGateway $paymentGateway)
    {
        return view('admin.payment_gateways.show', compact('paymentGateway'));
    }

    /**
     * Show the form for editing the specified payment gateway.
     */
    public function edit(PaymentGateway $paymentGateway)
    {
        // Decrypt sensitive config data for display
        if ($paymentGateway->config) {
            foreach ($paymentGateway->config as $key => $value) {
                if (in_array($key, ['api_key', 'secret', 'password'])) {
                    try {
                        $paymentGateway->config[$key] = decrypt($value);
                    } catch (\Exception $e) {
                        // If decryption fails, leave as is
                    }
                }
            }
        }
        
        return view('admin.payment_gateways.edit', compact('paymentGateway'));
    }

    /**
     * Update the specified payment gateway in storage.
     */
    public function update(Request $request, PaymentGateway $paymentGateway)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('payment_gateways')->ignore($paymentGateway->id),
            ],
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:1024',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'fee_percentage' => 'required|numeric|min:0|max:100',
            'fee_fixed' => 'required|numeric|min:0',
            'config' => 'nullable|array',
        ]);
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($paymentGateway->logo) {
                Storage::disk('public')->delete($paymentGateway->logo);
            }
            
            $path = $request->file('logo')->store('payment_gateways', 'public');
            $validated['logo'] = $path;
        }
        
        // Handle config data
        if (isset($validated['config'])) {
            // Ensure sensitive data is encrypted if needed
            foreach ($validated['config'] as $key => $value) {
                if (in_array($key, ['api_key', 'secret', 'password'])) {
                    $validated['config'][$key] = encrypt($value);
                }
            }
        }
        
        // If this is set as default, unset all other defaults
        if ($request->is_default) {
            PaymentGateway::where('id', '!=', $paymentGateway->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
        
        $paymentGateway->update($validated);
        
        return redirect()
            ->route('admin.payment_gateways.index')
            ->with('success', 'Payment gateway updated successfully.');
    }

    /**
     * Remove the specified payment gateway from storage.
     */
    public function destroy(PaymentGateway $paymentGateway)
    {
        // Delete logo if exists
        if ($paymentGateway->logo) {
            Storage::disk('public')->delete($paymentGateway->logo);
        }
        
        $paymentGateway->delete();
        
        return redirect()
            ->route('admin.payment_gateways.index')
            ->with('success', 'Payment gateway deleted successfully.');
    }
    
    /**
     * Toggle the active status of the payment gateway.
     */
    public function toggleActive(PaymentGateway $paymentGateway)
    {
        $paymentGateway->is_active = !$paymentGateway->is_active;
        $paymentGateway->save();
        
        return redirect()
            ->route('admin.payment_gateways.index')
            ->with('success', 'Payment gateway status updated successfully.');
    }
} 