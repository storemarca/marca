<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\Order;
use Carbon\Carbon;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        $query = Collection::with(['shipment.order.customer', 'collector']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_range')) {
            $this->applyDateRangeFilter($query, $request->date_range);
        }

        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', Carbon::parse($request->start_date)->startOfDay());
        }

        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay());
        }

        $sortField = $request->sort ?? 'created_at';
        $sortDirection = $request->direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        $collections = $query->paginate(20);

        $totalCollections = Collection::count();
        $pendingCollections = Collection::where('status', 'pending')->count();
        $collectedAmount = Collection::where('status', 'collected')->sum('amount');
        $pendingAmount = Collection::where('status', 'pending')->sum('amount');

        $orders = collect();
        if ($collections->isNotEmpty()) {
            $orders = $collections->map(function($collection) {
                return $collection->shipment->order_id ?? null;
            })->filter()->unique();
        }
        
        // Count of unique orders
        $ordersCount = $orders->count();

        return view('admin.collections.index', compact(
            'collections',
            'totalCollections',
            'orders',
            'ordersCount',
            'pendingCollections',
            'collectedAmount',
            'pendingAmount'
        ));
    }

    public function create()
    {
        $orders = Order::where('payment_method', 'cash_on_delivery')
            ->where('status', 'delivered')
            ->whereHas('shipments', function($query) {
                $query->where('is_cod', true)
                      ->whereDoesntHave('collection');
            })
            ->with('customer', 'shipments')
            ->get();

        return view('admin.collections.create', compact('orders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipment_id' => 'required|exists:shipments,id',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $shipment = \App\Models\Shipment::findOrFail($validated['shipment_id']);

        if ($shipment->hasCollection()) {
            return redirect()->back()
                ->withErrors(['shipment_id' => __('collections.shipment_already_has_collection')])
                ->withInput();
        }

        $collection = new Collection();
        $collection->shipment_id = $validated['shipment_id'];
        $collection->amount = $validated['amount'];
        $collection->notes = $validated['notes'];
        $collection->status = 'pending';
        $collection->save();

        return redirect()->route('admin.collections.index')
            ->with('success', __('collections.created_successfully'));
    }

    public function show(Collection $collection)
    {
        $collection->load(['shipment.order.customer', 'shipment.order.items.product', 'collector']);

        return view('admin.collections.show', compact('collection'));
    }

    public function edit(Collection $collection)
    {
        $collection->load(['shipment.order.customer', 'collector']);

        return view('admin.collections.edit', compact('collection'));
    }

    public function update(Request $request, Collection $collection)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $collection->amount = $validated['amount'];
        $collection->notes = $validated['notes'];
        $collection->save();

        return redirect()->route('admin.collections.show', $collection)
            ->with('success', __('collections.updated_successfully'));
    }

    public function destroy(Collection $collection)
    {
        $collection->delete();

        return redirect()->route('admin.collections.index')
            ->with('success', __('collections.deleted_successfully'));
    }

    public function markCollected(Request $request, Collection $collection)
    {
        $validated = $request->validate([
            'collected_by' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:500',
            'receipt_number' => 'nullable|string|max:255',
        ]);

        $collection->status = 'collected';
        $collection->collected_by = $validated['collected_by'];
        $collection->notes = $validated['notes'];
        $collection->receipt_number = $validated['receipt_number'] ?? null;
        $collection->collected_at = now();
        $collection->save();

        return redirect()->route('admin.collections.show', $collection)
            ->with('success', __('collections.marked_as_collected'));
    }

    public function markSettled(Request $request, Collection $collection)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $collection->status = 'settled';
        $collection->notes = $validated['notes'];
        $collection->settled_at = now();
        $collection->save();

        return redirect()->route('admin.collections.show', $collection)
            ->with('success', __('collections.marked_as_settled'));
    }

    protected function applyDateRangeFilter($query, $range)
    {
        switch ($range) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'yesterday':
                $query->whereDate('created_at', Carbon::yesterday());
                break;
            case 'this_week':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'last_week':
                $query->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereMonth('created_at', Carbon::now()->month)
                      ->whereYear('created_at', Carbon::now()->year);
                break;
            case 'last_month':
                $query->whereMonth('created_at', Carbon::now()->subMonth()->month)
                      ->whereYear('created_at', Carbon::now()->subMonth()->year);
                break;
        }
    }
}
