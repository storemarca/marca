<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingCompany;
use App\Models\Warehouse;
use App\Models\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShipmentController extends Controller
{
    /**
     * Mostrar listado de envíos
     */
    public function index(Request $request)
    {
        $query = Shipment::with(['order', 'warehouse', 'shippingCompany', 'items']);
        
        // Filtrado por estado
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filtrado por número de pedido
        if ($request->has('order_number') && $request->order_number) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->order_number . '%');
            });
        }
        
        // Filtrado por empresa de envío
        if ($request->has('shipping_company_id') && $request->shipping_company_id) {
            $query->where('shipping_company_id', $request->shipping_company_id);
        }
        
        $shipments = $query->latest()->paginate(15);
        $shippingCompanies = ShippingCompany::where('is_active', true)->get();
        
        return view('admin.shipments.index', compact('shipments', 'shippingCompanies'));
    }
    
    /**
     * Mostrar formulario para crear envío
     */
    public function create(Request $request)
    {
        $order = null;
        if ($request->has('order_id')) {
            $order = Order::with(['items', 'customer', 'shippingCompany'])->findOrFail($request->order_id);
        }
        
        $warehouses = Warehouse::where('is_active', true)->get();
        $shippingCompanies = ShippingCompany::where('is_active', true)->get();
        
        return view('admin.shipments.create', compact('order', 'warehouses', 'shippingCompanies'));
    }
    
    /**
     * Guardar nuevo envío
     */
    public function store(Request $request)
    {
        // Validación
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'shipping_company_id' => 'required|exists:shipping_companies,id',
            'items' => 'required|array',
            'items.*.order_item_id' => 'required|exists:order_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'is_cod' => 'sometimes|boolean',
            'cod_amount' => 'required_if:is_cod,1|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Crear el envío
            $shipment = new Shipment([
                'order_id' => $request->order_id,
                'warehouse_id' => $request->warehouse_id,
                'shipping_company_id' => $request->shipping_company_id,
                'status' => 'pending',
                'is_cod' => $request->has('is_cod'),
                'cod_amount' => $request->is_cod ? $request->cod_amount : 0,
                'notes' => $request->notes,
            ]);
            
            $shipment->save();
            
            // Crear los items del envío
            foreach ($request->items as $item) {
                if (isset($item['quantity']) && $item['quantity'] > 0) {
                    ShipmentItem::create([
                        'shipment_id' => $shipment->id,
                        'order_item_id' => $item['order_item_id'],
                        'product_id' => OrderItem::find($item['order_item_id'])->product_id,
                        'quantity' => $item['quantity'],
                    ]);
                }
            }
            
            // Update the order with shipping company information
            $order = Order::find($request->order_id);
            $order->shipping_company_id = $request->shipping_company_id;
            $order->save();
            
            // Si el envío es COD, crear una colección pendiente
            if ($shipment->is_cod) {
                Collection::create([
                    'shipment_id' => $shipment->id,
                    'amount' => $shipment->cod_amount,
                    'status' => 'pending',
                ]);
            }
            
            // Si se seleccionó una empresa de transporte con API, intentar crear el envío
            if ($request->create_in_shipping_company) {
                $this->createShipmentWithApi($shipment);
            }
            
            DB::commit();
            
            return redirect()
                ->route('admin.shipments.show', $shipment)
                ->with('success', 'Envío creado con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear envío: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear el envío: ' . $e->getMessage());
        }
    }
    
    /**
     * Ver detalles del envío
     */
    public function show(Shipment $shipment)
    {
        $shipment->load(['order', 'warehouse', 'shippingCompany', 'items.orderItem', 'items.product', 'collection']);
        return view('admin.shipments.show', compact('shipment'));
    }
    
    /**
     * Mostrar formulario de edición
     */
    public function edit(Shipment $shipment)
    {
        $shipment->load(['order', 'warehouse', 'shippingCompany', 'items.orderItem', 'items.product']);
        $shippingCompanies = ShippingCompany::where('is_active', true)->get();
        
        return view('admin.shipments.edit', compact('shipment', 'shippingCompanies'));
    }
    
    /**
     * Actualizar envío
     */
    public function update(Request $request, Shipment $shipment)
    {
        // Validación
        $validated = $request->validate([
            'shipping_company_id' => 'required|exists:shipping_companies,id',
            'tracking_number' => 'nullable|string|max:100',
            'status' => 'required|string|in:pending,processing,shipped,in_transit,out_for_delivery,delivered,failed,returned',
            'shipping_cost' => 'nullable|numeric|min:0',
            'is_cod' => 'sometimes|boolean',
            'cod_amount' => 'required_if:is_cod,1|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Actualizar datos básicos
            $shipment->shipping_company_id = $request->shipping_company_id;
            $shipment->tracking_number = $request->tracking_number;
            $shipment->status = $request->status;
            $shipment->shipping_cost = $request->shipping_cost ?? 0;
            $shipment->is_cod = $request->has('is_cod');
            $shipment->cod_amount = $request->is_cod ? $request->cod_amount : 0;
            $shipment->notes = $request->notes;
            
            // Si cambia a "shipped"
            if ($request->status === 'shipped' && !$shipment->shipped_at) {
                $shipment->shipped_at = now();
            }
            
            // Si cambia a "delivered"
            if ($request->status === 'delivered' && !$shipment->delivered_at) {
                $shipment->delivered_at = now();
            }
            
            $shipment->save();
            
            // Update the order with shipping company information
            $order = $shipment->order;
            $order->shipping_company_id = $request->shipping_company_id;
            if ($request->tracking_number) {
                $order->tracking_number = $request->tracking_number;
            }
            $order->save();
            
            // Actualizar la colección asociada si es COD
            if ($shipment->is_cod) {
                $collection = $shipment->collection;
                
                if (!$collection) {
                    Collection::create([
                        'shipment_id' => $shipment->id,
                        'amount' => $shipment->cod_amount,
                        'status' => 'pending',
                    ]);
                } else {
                    $collection->amount = $shipment->cod_amount;
                    $collection->save();
                }
            } else {
                // Si ya no es COD pero había una colección, eliminarla o marcarla como cancelada
                if ($shipment->collection) {
                    if ($shipment->collection->status === 'pending') {
                        $shipment->collection->delete();
                    } else {
                        $shipment->collection->status = 'disputed';
                        $shipment->collection->notes = 'Envío marcado como no COD';
                        $shipment->collection->save();
                    }
                }
            }
            
            DB::commit();
            
            return redirect()
                ->route('admin.shipments.show', $shipment)
                ->with('success', 'Envío actualizado con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar envío: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el envío: ' . $e->getMessage());
        }
    }
    
    /**
     * تحديث حالة الشحنة
     */
    public function updateStatus(Request $request, Shipment $shipment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,in_transit,out_for_delivery,delivered,failed,returned',
            'notes' => 'nullable|string',
            'description' => 'nullable|string',
        ]);
        
        // تحديث الحالة باستخدام الدالة الجديدة
        $shipment->updateStatus(
            $validated['status'],
            $validated['description'] ?? null,
            ['notes' => $validated['notes'] ?? null]
        );
        
        return redirect()->route('admin.shipments.show', $shipment->id)
            ->with('success', 'تم تحديث حالة الشحنة بنجاح');
    }
    
    /**
     * عرض تاريخ تتبع الشحنة
     */
    public function trackingHistory(Shipment $shipment)
    {
        return view('admin.shipments.tracking-history', compact('shipment'));
    }
    
    /**
     * Actualizar número de seguimiento
     */
    public function updateTracking(Request $request, Shipment $shipment)
    {
        $request->validate([
            'tracking_number' => 'required|string|max:100',
        ]);
        
        $shipment->tracking_number = $request->tracking_number;
        
        // Generar URL de seguimiento si la empresa tiene una plantilla
        if ($shipment->shippingCompany && $shipment->shippingCompany->tracking_url_template) {
            $shipment->tracking_url = str_replace(
                '{tracking_number}', 
                $shipment->tracking_number, 
                $shipment->shippingCompany->tracking_url_template
            );
        }
        
        $shipment->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Número de seguimiento actualizado',
            'tracking_url' => $shipment->tracking_url
        ]);
    }
    
    /**
     * Crear envío con API de transportista
     */
    public function createShipmentWithApi(Shipment $shipment)
    {
        if (!$shipment->shippingCompany || !$shipment->shippingCompany->has_api_integration) {
            return [
                'success' => false,
                'message' => 'Esta empresa de transporte no tiene integración API',
            ];
        }
        
        // Cargar datos necesarios
        $shipment->load(['order.customer', 'warehouse', 'items.product']);
        
        try {
            // Preparar los datos para la API
            $shipmentData = $this->prepareShipmentDataForApi($shipment);
            
            // Llamar al método de la empresa de transporte
            $result = $shipment->shippingCompany->createShipmentViaApi($shipmentData);
            
            if ($result['success']) {
                // Actualizar el envío con los datos recibidos
                $shipment->tracking_number = $result['data']['tracking_number'];
                $shipment->tracking_url = $result['data']['tracking_url'];
                $shipment->status = 'processing';
                $shipment->save();
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Error al crear envío con API: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al crear envío con API: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * Preparar datos del envío para API
     */
    private function prepareShipmentDataForApi(Shipment $shipment)
    {
        // Calcular el peso total
        $totalWeight = 0;
        $totalLength = 0;
        $totalWidth = 0;
        $totalHeight = 0;
        $quantity = 0;
        $description = '';
        
        foreach ($shipment->items as $item) {
            $product = $item->product;
            $totalWeight += $product->weight * $item->quantity;
            $totalLength = max($totalLength, $product->length);
            $totalWidth += $product->width;
            $totalHeight = max($totalHeight, $product->height);
            $quantity += $item->quantity;
            $description .= $product->name . ' x ' . $item->quantity . ', ';
        }
        
        $description = rtrim($description, ', ');
        if (strlen($description) > 100) {
            $description = substr($description, 0, 97) . '...';
        }
        
        // Determinar el país de código según el país de envío
        $countryMap = [
            'Saudi Arabia' => 'SA',
            'Egypt' => 'EG',
        ];
        
        $order = $shipment->order;
        $warehouse = $shipment->warehouse;
        
        // Preparar datos generales
        $shipmentData = [
            'order_number' => $order->order_number,
            'weight' => $totalWeight,
            'length' => $totalLength,
            'width' => $totalWidth,
            'height' => $totalHeight,
            'quantity' => $quantity,
            'description' => $description,
            'is_cod' => $shipment->is_cod,
            'cod_amount' => $shipment->cod_amount,
            'currency' => $order->currency,
            
            // Remitente (almacén)
            'sender_name' => $warehouse->name,
            'sender_company' => config('app.name'),
            'sender_phone' => $warehouse->phone,
            'sender_email' => $warehouse->email,
            'sender_address_line1' => $warehouse->address_line1,
            'sender_address_line2' => $warehouse->address_line2,
            'sender_city' => $warehouse->city,
            'sender_state' => $warehouse->state,
            'sender_postal_code' => $warehouse->postal_code,
            'sender_country' => $warehouse->country->name,
            'sender_country_code' => $warehouse->country->code,
            
            // Destinatario (cliente)
            'receiver_name' => $order->shipping_name,
            'receiver_phone' => $order->shipping_phone,
            'receiver_email' => $order->customer_email,
            'receiver_address_line1' => $order->shipping_address_line1,
            'receiver_address_line2' => $order->shipping_address_line2,
            'receiver_city' => $order->shipping_city,
            'receiver_state' => $order->shipping_state,
            'receiver_postal_code' => $order->shipping_postal_code,
            'receiver_country' => $order->shipping_country,
            'receiver_country_code' => $countryMap[$order->shipping_country] ?? 'SA',
            
            // Para Zajil
            'shipment_value' => $order->total_amount,
            'notes' => $shipment->notes,
        ];
        
        // Si la empresa es Zajil, añadir los IDs específicos
        if ($shipment->shippingCompany->code == 'zajil') {
            // Estos son valores de ejemplo - en una implementación real estos deberían venir de una tabla de mapeo
            $zajilCityMap = [
                'Riyadh' => 1,
                'Jeddah' => 2,
                'Dammam' => 3,
                'Cairo' => 101,
                'Alexandria' => 102,
                // Añadir más ciudades según sea necesario
            ];
            
            $zajilRegionMap = [
                'Riyadh' => 1,
                'Makkah' => 2,
                'Eastern Province' => 3,
                'Cairo' => 101,
                'Alexandria' => 102,
                // Añadir más regiones según sea necesario
            ];
            
            $shipmentData['receiver_city_id'] = $zajilCityMap[$order->shipping_city] ?? 1;
            $shipmentData['receiver_region_id'] = $zajilRegionMap[$order->shipping_state] ?? 1;
            $shipmentData['sender_city_id'] = $zajilCityMap[$warehouse->city] ?? 1;
        }
        
        return $shipmentData;
    }
    
    /**
     * Obtener actualización de seguimiento desde la API
     */
    public function refreshTracking(Shipment $shipment)
    {
        if (!$shipment->shippingCompany || !$shipment->shippingCompany->has_api_integration || !$shipment->tracking_number) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede obtener la información de seguimiento'
            ]);
        }
        
        try {
            $result = $shipment->shippingCompany->trackShipmentViaApi($shipment->tracking_number);
            
            if ($result['success']) {
                // Actualizar el estado si es necesario basado en los datos de seguimiento
                $this->updateShipmentStatusFromTracking($shipment, $result['data']);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Información de seguimiento actualizada',
                    'data' => $result['data']
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener seguimiento: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener seguimiento: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Actualizar el estado del envío basado en datos de seguimiento
     */
    private function updateShipmentStatusFromTracking(Shipment $shipment, $trackingData)
    {
        // La lógica dependerá del formato de respuesta de cada transportista
        // Ejemplo para Aramex:
        if ($shipment->shippingCompany->code === 'aramex') {
            if (!empty($trackingData[0]['Value'])) {
                $latestUpdate = end($trackingData[0]['Value']);
                $status = $latestUpdate['UpdateDescription'] ?? '';
                
                // Mapear estado de Aramex a nuestro sistema
                $statusMap = [
                    'Shipment Created' => 'processing',
                    'Shipment Picked Up' => 'processing',
                    'Shipment In Transit' => 'in_transit',
                    'Shipment Out For Delivery' => 'out_for_delivery',
                    'Shipment Delivered' => 'delivered',
                    'Shipment Returned' => 'returned',
                    'Shipment Delivery Failed' => 'failed',
                ];
                
                foreach ($statusMap as $aramexStatus => $ourStatus) {
                    if (strpos($status, $aramexStatus) !== false) {
                        $shipment->status = $ourStatus;
                        
                        if ($ourStatus === 'delivered') {
                            $shipment->delivered_at = now();
                        }
                        
                        $shipment->save();
                        break;
                    }
                }
            }
        }
        
        // Ejemplo para Zajil:
        else if ($shipment->shippingCompany->code === 'zajil') {
            if (isset($trackingData['status'])) {
                $status = $trackingData['status'];
                
                // Mapear estado de Zajil a nuestro sistema
                $statusMap = [
                    'Created' => 'processing',
                    'Picked Up' => 'processing',
                    'In Transit' => 'in_transit',
                    'Out For Delivery' => 'out_for_delivery',
                    'Delivered' => 'delivered',
                    'Returned' => 'returned',
                    'Failed Delivery Attempt' => 'failed',
                ];
                
                foreach ($statusMap as $zajilStatus => $ourStatus) {
                    if ($status === $zajilStatus) {
                        $shipment->status = $ourStatus;
                        
                        if ($ourStatus === 'delivered') {
                            $shipment->delivered_at = now();
                        }
                        
                        $shipment->save();
                        break;
                    }
                }
            }
        }
    }
}
