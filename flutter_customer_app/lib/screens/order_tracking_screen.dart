import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../models/order.dart';
import '../services/order_service.dart';
import '../widgets/loading_indicator.dart';
import '../widgets/order_status_timeline.dart';

class OrderTrackingScreen extends StatefulWidget {
  final String? orderId;
  final String? trackingNumber;

  const OrderTrackingScreen({
    Key? key,
    this.orderId,
    this.trackingNumber,
  }) : super(key: key);

  @override
  State<OrderTrackingScreen> createState() => _OrderTrackingScreenState();
}

class _OrderTrackingScreenState extends State<OrderTrackingScreen> {
  final _trackingController = TextEditingController();
  bool _isLoading = false;
  Order? _order;
  String? _errorMessage;

  @override
  void initState() {
    super.initState();
    if (widget.trackingNumber != null) {
      _trackingController.text = widget.trackingNumber!;
      _trackOrder();
    } else if (widget.orderId != null) {
      _fetchOrderDetails();
    }
  }

  @override
  void dispose() {
    _trackingController.dispose();
    super.dispose();
  }

  Future<void> _fetchOrderDetails() async {
    if (widget.orderId == null) return;
    
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    try {
      final orderService = Provider.of<OrderService>(context, listen: false);
      final order = await orderService.getOrderById(widget.orderId!);
      
      setState(() {
        _order = order;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _errorMessage = 'Failed to load order details: ${e.toString()}';
        _isLoading = false;
      });
    }
  }

  Future<void> _trackOrder() async {
    final trackingNumber = _trackingController.text.trim();
    if (trackingNumber.isEmpty) {
      setState(() {
        _errorMessage = 'Please enter a tracking number';
      });
      return;
    }

    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    try {
      final orderService = Provider.of<OrderService>(context, listen: false);
      final order = await orderService.trackOrder(trackingNumber);
      
      setState(() {
        _order = order;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _errorMessage = 'Failed to track order: ${e.toString()}';
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Track Order'),
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            if (widget.orderId == null) ...[
              TextField(
                controller: _trackingController,
                decoration: InputDecoration(
                  labelText: 'Tracking Number',
                  hintText: 'Enter your order tracking number',
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                  suffixIcon: IconButton(
                    icon: const Icon(Icons.search),
                    onPressed: _trackOrder,
                  ),
                ),
              ),
              const SizedBox(height: 16),
              ElevatedButton(
                onPressed: _trackOrder,
                style: ElevatedButton.styleFrom(
                  padding: const EdgeInsets.symmetric(vertical: 16),
                ),
                child: const Text('Track Order'),
              ),
              const SizedBox(height: 24),
            ],
            
            if (_isLoading) ...[
              const Center(child: LoadingIndicator()),
            ] else if (_errorMessage != null) ...[
              Center(
                child: Column(
                  children: [
                    const Icon(
                      Icons.error_outline,
                      color: Colors.red,
                      size: 48,
                    ),
                    const SizedBox(height: 16),
                    Text(
                      _errorMessage!,
                      textAlign: TextAlign.center,
                      style: const TextStyle(color: Colors.red),
                    ),
                  ],
                ),
              ),
            ] else if (_order != null) ...[
              _buildOrderDetails(),
            ],
          ],
        ),
      ),
    );
  }

  Widget _buildOrderDetails() {
    final dateFormat = DateFormat('MMM dd, yyyy');
    final timeFormat = DateFormat('hh:mm a');
    
    return Expanded(
      child: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Card(
              elevation: 2,
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          'Order #${_order!.orderNumber}',
                          style: const TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 18,
                          ),
                        ),
                        Chip(
                          label: Text(_order!.status),
                          backgroundColor: _getStatusColor(_order!.status),
                          labelStyle: const TextStyle(color: Colors.white),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                    Text(
                      'Placed on: ${dateFormat.format(_order!.createdAt)} at ${timeFormat.format(_order!.createdAt)}',
                      style: TextStyle(
                        color: Colors.grey[600],
                      ),
                    ),
                    const Divider(height: 32),
                    OrderStatusTimeline(
                      status: _order!.status,
                      events: _order!.statusHistory,
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 16),
            Card(
              elevation: 2,
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Shipping Details',
                      style: TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 16,
                      ),
                    ),
                    const SizedBox(height: 8),
                    if (_order!.shipment != null) ...[
                      ListTile(
                        contentPadding: EdgeInsets.zero,
                        leading: const Icon(Icons.local_shipping),
                        title: Text(_order!.shipment!.shippingCompany),
                        subtitle: Text(
                          'Tracking #: ${_order!.shipment!.trackingNumber}',
                        ),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        'Recipient: ${_order!.shippingAddress.name}',
                      ),
                      const SizedBox(height: 4),
                      Text(_order!.shippingAddress.address),
                      const SizedBox(height: 4),
                      Text(
                        '${_order!.shippingAddress.city}, ${_order!.shippingAddress.state} ${_order!.shippingAddress.postalCode}',
                      ),
                      const SizedBox(height: 4),
                      Text(_order!.shippingAddress.country),
                      const SizedBox(height: 4),
                      Text('Phone: ${_order!.shippingAddress.phone}'),
                    ] else ...[
                      const Text('Shipment information not available yet.'),
                    ],
                  ],
                ),
              ),
            ),
            const SizedBox(height: 16),
            Card(
              elevation: 2,
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Order Items',
                      style: TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 16,
                      ),
                    ),
                    const SizedBox(height: 8),
                    ListView.builder(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      itemCount: _order!.items.length,
                      itemBuilder: (context, index) {
                        final item = _order!.items[index];
                        return ListTile(
                          contentPadding: EdgeInsets.zero,
                          leading: ClipRRect(
                            borderRadius: BorderRadius.circular(4),
                            child: Image.network(
                              item.product.thumbnailUrl,
                              width: 50,
                              height: 50,
                              fit: BoxFit.cover,
                              errorBuilder: (_, __, ___) => Container(
                                width: 50,
                                height: 50,
                                color: Colors.grey[300],
                                child: const Icon(Icons.image_not_supported),
                              ),
                            ),
                          ),
                          title: Text(
                            item.product.name,
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                          ),
                          subtitle: Text(
                            '${item.quantity} × ${item.formattedPrice}',
                          ),
                          trailing: Text(
                            item.formattedTotal,
                            style: const TextStyle(fontWeight: FontWeight.bold),
                          ),
                        );
                      },
                    ),
                    const Divider(height: 24),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text('Subtotal'),
                        Text(_order!.formattedSubtotal),
                      ],
                    ),
                    const SizedBox(height: 8),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text('Shipping'),
                        Text(_order!.formattedShippingCost),
                      ],
                    ),
                    if (_order!.discount > 0) ...[
                      const SizedBox(height: 8),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text('Discount'),
                          Text(
                            '-${_order!.formattedDiscount}',
                            style: const TextStyle(color: Colors.green),
                          ),
                        ],
                      ),
                    ],
                    const Divider(height: 24),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text(
                          'Total',
                          style: TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 16,
                          ),
                        ),
                        Text(
                          _order!.formattedTotal,
                          style: const TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 16,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Color _getStatusColor(String status) {
    switch (status.toLowerCase()) {
      case 'pending':
        return Colors.orange;
      case 'processing':
        return Colors.blue;
      case 'shipped':
        return Colors.indigo;
      case 'delivered':
        return Colors.green;
      case 'cancelled':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }
} 