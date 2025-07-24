import 'package:intl/intl.dart';
import 'address.dart';
import 'product.dart';

class Order {
  final String id;
  final String orderNumber;
  final String status;
  final DateTime createdAt;
  final List<OrderItem> items;
  final Address shippingAddress;
  final Address? billingAddress;
  final double subtotal;
  final double shippingCost;
  final double discount;
  final double total;
  final String paymentMethod;
  final String paymentStatus;
  final Shipment? shipment;
  final List<StatusEvent> statusHistory;

  Order({
    required this.id,
    required this.orderNumber,
    required this.status,
    required this.createdAt,
    required this.items,
    required this.shippingAddress,
    this.billingAddress,
    required this.subtotal,
    required this.shippingCost,
    required this.discount,
    required this.total,
    required this.paymentMethod,
    required this.paymentStatus,
    this.shipment,
    required this.statusHistory,
  });

  String get formattedSubtotal => _formatCurrency(subtotal);
  String get formattedShippingCost => _formatCurrency(shippingCost);
  String get formattedDiscount => _formatCurrency(discount);
  String get formattedTotal => _formatCurrency(total);

  String _formatCurrency(double amount) {
    final formatter = NumberFormat.currency(
      symbol: 'SAR ',
      decimalDigits: 2,
    );
    return formatter.format(amount);
  }

  factory Order.fromJson(Map<String, dynamic> json) {
    return Order(
      id: json['id'],
      orderNumber: json['order_number'],
      status: json['status'],
      createdAt: DateTime.parse(json['created_at']),
      items: (json['items'] as List)
          .map((item) => OrderItem.fromJson(item))
          .toList(),
      shippingAddress: Address.fromJson(json['shipping_address']),
      billingAddress: json['billing_address'] != null
          ? Address.fromJson(json['billing_address'])
          : null,
      subtotal: json['subtotal'].toDouble(),
      shippingCost: json['shipping_cost'].toDouble(),
      discount: json['discount'].toDouble(),
      total: json['total'].toDouble(),
      paymentMethod: json['payment_method'],
      paymentStatus: json['payment_status'],
      shipment: json['shipment'] != null
          ? Shipment.fromJson(json['shipment'])
          : null,
      statusHistory: (json['status_history'] as List)
          .map((event) => StatusEvent.fromJson(event))
          .toList(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'order_number': orderNumber,
      'status': status,
      'created_at': createdAt.toIso8601String(),
      'items': items.map((item) => item.toJson()).toList(),
      'shipping_address': shippingAddress.toJson(),
      'billing_address': billingAddress?.toJson(),
      'subtotal': subtotal,
      'shipping_cost': shippingCost,
      'discount': discount,
      'total': total,
      'payment_method': paymentMethod,
      'payment_status': paymentStatus,
      'shipment': shipment?.toJson(),
      'status_history': statusHistory.map((event) => event.toJson()).toList(),
    };
  }
}

class OrderItem {
  final String id;
  final Product product;
  final int quantity;
  final double price;
  final double total;

  OrderItem({
    required this.id,
    required this.product,
    required this.quantity,
    required this.price,
    required this.total,
  });

  String get formattedPrice {
    final formatter = NumberFormat.currency(
      symbol: 'SAR ',
      decimalDigits: 2,
    );
    return formatter.format(price);
  }

  String get formattedTotal {
    final formatter = NumberFormat.currency(
      symbol: 'SAR ',
      decimalDigits: 2,
    );
    return formatter.format(total);
  }

  factory OrderItem.fromJson(Map<String, dynamic> json) {
    return OrderItem(
      id: json['id'],
      product: Product.fromJson(json['product']),
      quantity: json['quantity'],
      price: json['price'].toDouble(),
      total: json['total'].toDouble(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'product': product.toJson(),
      'quantity': quantity,
      'price': price,
      'total': total,
    };
  }
}

class Shipment {
  final String id;
  final String trackingNumber;
  final String shippingCompany;
  final String status;
  final DateTime? shippedAt;
  final DateTime? deliveredAt;
  final DateTime? estimatedDeliveryDate;

  Shipment({
    required this.id,
    required this.trackingNumber,
    required this.shippingCompany,
    required this.status,
    this.shippedAt,
    this.deliveredAt,
    this.estimatedDeliveryDate,
  });

  factory Shipment.fromJson(Map<String, dynamic> json) {
    return Shipment(
      id: json['id'],
      trackingNumber: json['tracking_number'],
      shippingCompany: json['shipping_company'],
      status: json['status'],
      shippedAt: json['shipped_at'] != null
          ? DateTime.parse(json['shipped_at'])
          : null,
      deliveredAt: json['delivered_at'] != null
          ? DateTime.parse(json['delivered_at'])
          : null,
      estimatedDeliveryDate: json['estimated_delivery_date'] != null
          ? DateTime.parse(json['estimated_delivery_date'])
          : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'tracking_number': trackingNumber,
      'shipping_company': shippingCompany,
      'status': status,
      'shipped_at': shippedAt?.toIso8601String(),
      'delivered_at': deliveredAt?.toIso8601String(),
      'estimated_delivery_date': estimatedDeliveryDate?.toIso8601String(),
    };
  }
}

class StatusEvent {
  final String status;
  final String description;
  final DateTime timestamp;

  StatusEvent({
    required this.status,
    required this.description,
    required this.timestamp,
  });

  factory StatusEvent.fromJson(Map<String, dynamic> json) {
    return StatusEvent(
      status: json['status'],
      description: json['description'],
      timestamp: DateTime.parse(json['timestamp']),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'status': status,
      'description': description,
      'timestamp': timestamp.toIso8601String(),
    };
  }
} 