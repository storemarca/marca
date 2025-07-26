<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\Order;
use Carbon\Carbon;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Collection::with(['order', 'order.customer']);

        // تطبيق الفلاتر
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

        // الترتيب
        $sortField = $request->sort ?? 'created_at';
        $sortDirection = $request->direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        $collections = $query->paginate(20);

        // إحصائيات
        $totalCollections = Collection::count();
        $pendingCollections = Collection::where('status', 'pending')->count();
        $collectedAmount = Collection::where('status', 'collected')->sum('amount');
        $pendingAmount = Collection::where('status', 'pending')->sum('amount');


        // Count unique orders with collections
        $ordersCount = Collection::distinct('order_id')->count('order_id');

        return view('admin.collections.index', compact(
            'collections',
            'totalCollections',
            'ordersCount',
            'pendingCollections',
            'collectedAmount',
            'pendingAmount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $orders = Order::where('payment_method', 'cash_on_delivery')
            ->where('status', 'delivered')
            ->whereDoesntHave('collection')
            ->with('customer')
            ->get();

        return view('admin.collections.create', compact('orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0',
            'collection_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $order = Order::findOrFail($validated['order_id']);

        $collection = new Collection();
        $collection->order_id = $validated['order_id'];
        $collection->amount = $validated['amount'];
        $collection->collection_date = Carbon::parse($validated['collection_date']);
        $collection->notes = $validated['notes'];
        $collection->status = 'pending';
        $collection->save();

        return redirect()->route('admin.collections.index')
            ->with('success', 'تم إنشاء التحصيل بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Collection $collection)
    {
        $collection->load(['order', 'order.customer', 'order.items.product']);

        return view('admin.collections.show', compact('collection'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Collection $collection)
    {
        $collection->load(['order', 'order.customer']);

        return view('admin.collections.edit', compact('collection'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Collection $collection)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'collection_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $collection->amount = $validated['amount'];
        $collection->collection_date = Carbon::parse($validated['collection_date']);
        $collection->notes = $validated['notes'];
        $collection->save();

        return redirect()->route('admin.collections.show', $collection)
            ->with('success', 'تم تحديث التحصيل بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Collection $collection)
    {
        $collection->delete();

        return redirect()->route('admin.collections.index')
            ->with('success', 'تم حذف التحصيل بنجاح.');
    }

    /**
     * Mark a collection as collected.
     */
    public function markCollected(Request $request, Collection $collection)
    {
        $validated = $request->validate([
            'collected_by' => 'required|string|max:255',
            'collection_notes' => 'nullable|string|max:500',
        ]);

        $collection->status = 'collected';
        $collection->collected_by = $validated['collected_by'];
        $collection->collection_notes = $validated['collection_notes'];
        $collection->collected_at = now();
        $collection->save();

        return redirect()->route('admin.collections.show', $collection)
            ->with('success', 'تم تحديث التحصيل كمحصل بنجاح.');
    }

    /**
     * Mark a collection as settled.
     */
    public function markSettled(Request $request, Collection $collection)
    {
        $validated = $request->validate([
            'settlement_reference' => 'required|string|max:255',
            'settlement_notes' => 'nullable|string|max:500',
        ]);

        $collection->status = 'settled';
        $collection->settlement_reference = $validated['settlement_reference'];
        $collection->settlement_notes = $validated['settlement_notes'];
        $collection->settled_at = now();
        $collection->save();

        return redirect()->route('admin.collections.show', $collection)
            ->with('success', 'تم تحديث التحصيل كمسوى بنجاح.');
    }

    /**
     * Apply date range filter to query.
     */
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
