import 'package:intl/intl.dart';
import 'product.dart';

class CartItem {
  final String id;
  final Product product;
  int quantity;
  final double price;
  final String? offerId;
  final bool hasLimitedOffer;
  final bool hasFreeShipping;

  CartItem({
    required this.id,
    required this.product,
    required this.quantity,
    required this.price,
    this.offerId,
    this.hasLimitedOffer = false,
    this.hasFreeShipping = false,
  });

  double get total => price * quantity;

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

  CartItem copyWith({
    String? id,
    Product? product,
    int? quantity,
    double? price,
    String? offerId,
    bool? hasLimitedOffer,
    bool? hasFreeShipping,
  }) {
    return CartItem(
      id: id ?? this.id,
      product: product ?? this.product,
      quantity: quantity ?? this.quantity,
      price: price ?? this.price,
      offerId: offerId ?? this.offerId,
      hasLimitedOffer: hasLimitedOffer ?? this.hasLimitedOffer,
      hasFreeShipping: hasFreeShipping ?? this.hasFreeShipping,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'product_id': product.id,
      'quantity': quantity,
      'price': price,
      'offer_id': offerId,
      'has_limited_offer': hasLimitedOffer,
      'has_free_shipping': hasFreeShipping,
    };
  }

  factory CartItem.fromJson(Map<String, dynamic> json, Product product) {
    return CartItem(
      id: json['id'],
      product: product,
      quantity: json['quantity'],
      price: json['price'].toDouble(),
      offerId: json['offer_id'],
      hasLimitedOffer: json['has_limited_offer'] ?? false,
      hasFreeShipping: json['has_free_shipping'] ?? false,
    );
  }
} 