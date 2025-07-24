import 'package:intl/intl.dart';

class LimitedOffer {
  final String id;
  final String productId;
  final String productName;
  final String? productImage;
  final double originalPrice;
  final double offerPrice;
  final int totalQuantity;
  final int remainingQuantity;
  final DateTime startDate;
  final DateTime endDate;
  final bool isActive;

  LimitedOffer({
    required this.id,
    required this.productId,
    required this.productName,
    this.productImage,
    required this.originalPrice,
    required this.offerPrice,
    required this.totalQuantity,
    required this.remainingQuantity,
    required this.startDate,
    required this.endDate,
    required this.isActive,
  });

  bool get isExpired => DateTime.now().isAfter(endDate);
  bool get isSoldOut => remainingQuantity <= 0;
  bool get isAvailable => isActive && !isExpired && !isSoldOut;

  double get discountPercentage {
    if (originalPrice <= 0) return 0;
    return ((originalPrice - offerPrice) / originalPrice) * 100;
  }

  String get formattedDiscountPercentage {
    return '${discountPercentage.round()}%';
  }

  String get formattedOfferPrice {
    final formatter = NumberFormat.currency(
      symbol: 'SAR ',
      decimalDigits: 2,
    );
    return formatter.format(offerPrice);
  }

  String get formattedOriginalPrice {
    final formatter = NumberFormat.currency(
      symbol: 'SAR ',
      decimalDigits: 2,
    );
    return formatter.format(originalPrice);
  }

  String get formattedStartDate {
    final dateFormat = DateFormat('dd/MM/yyyy');
    return dateFormat.format(startDate);
  }

  String get formattedEndDate {
    final dateFormat = DateFormat('dd/MM/yyyy');
    return dateFormat.format(endDate);
  }

  String get remainingTimeText {
    if (isExpired) return 'انتهى العرض';
    if (isSoldOut) return 'نفدت الكمية';
    
    final now = DateTime.now();
    final difference = endDate.difference(now);
    
    if (difference.inDays > 0) {
      return 'متبقي ${difference.inDays} يوم';
    } else if (difference.inHours > 0) {
      return 'متبقي ${difference.inHours} ساعة';
    } else if (difference.inMinutes > 0) {
      return 'متبقي ${difference.inMinutes} دقيقة';
    } else {
      return 'ينتهي خلال لحظات';
    }
  }

  factory LimitedOffer.fromJson(Map<String, dynamic> json) {
    return LimitedOffer(
      id: json['id'],
      productId: json['product_id'],
      productName: json['product_name'],
      productImage: json['product_image'],
      originalPrice: json['original_price'].toDouble(),
      offerPrice: json['offer_price'].toDouble(),
      totalQuantity: json['total_quantity'],
      remainingQuantity: json['remaining_quantity'],
      startDate: DateTime.parse(json['start_date']),
      endDate: DateTime.parse(json['end_date']),
      isActive: json['is_active'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'product_id': productId,
      'product_name': productName,
      'product_image': productImage,
      'original_price': originalPrice,
      'offer_price': offerPrice,
      'total_quantity': totalQuantity,
      'remaining_quantity': remainingQuantity,
      'start_date': startDate.toIso8601String(),
      'end_date': endDate.toIso8601String(),
      'is_active': isActive,
    };
  }
} 