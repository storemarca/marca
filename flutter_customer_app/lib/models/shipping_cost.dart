import 'package:intl/intl.dart';

class ShippingZone {
  final String id;
  final String name;
  final String country;
  final List<String> cities;
  final bool isActive;

  ShippingZone({
    required this.id,
    required this.name,
    required this.country,
    required this.cities,
    required this.isActive,
  });

  factory ShippingZone.fromJson(Map<String, dynamic> json) {
    return ShippingZone(
      id: json['id'],
      name: json['name'],
      country: json['country'],
      cities: List<String>.from(json['cities']),
      isActive: json['is_active'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'country': country,
      'cities': cities,
      'is_active': isActive,
    };
  }
}

class ShippingMethod {
  final String id;
  final String name;
  final String companyName;
  final String? logo;
  final int estimatedDays;
  final bool isActive;
  final bool isCashOnDeliveryAvailable;
  final double cashOnDeliveryFee;

  ShippingMethod({
    required this.id,
    required this.name,
    required this.companyName,
    this.logo,
    required this.estimatedDays,
    required this.isActive,
    required this.isCashOnDeliveryAvailable,
    required this.cashOnDeliveryFee,
  });

  String get deliveryTimeText {
    if (estimatedDays == 1) {
      return 'يوم واحد';
    } else if (estimatedDays == 2) {
      return 'يومان';
    } else if (estimatedDays >= 3 && estimatedDays <= 10) {
      return '$estimatedDays أيام';
    } else {
      return '$estimatedDays يوم';
    }
  }

  String get formattedCashOnDeliveryFee {
    final formatter = NumberFormat.currency(
      symbol: 'SAR ',
      decimalDigits: 2,
    );
    return formatter.format(cashOnDeliveryFee);
  }

  factory ShippingMethod.fromJson(Map<String, dynamic> json) {
    return ShippingMethod(
      id: json['id'],
      name: json['name'],
      companyName: json['company_name'],
      logo: json['logo'],
      estimatedDays: json['estimated_days'],
      isActive: json['is_active'],
      isCashOnDeliveryAvailable: json['is_cash_on_delivery_available'],
      cashOnDeliveryFee: json['cash_on_delivery_fee'].toDouble(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'company_name': companyName,
      'logo': logo,
      'estimated_days': estimatedDays,
      'is_active': isActive,
      'is_cash_on_delivery_available': isCashOnDeliveryAvailable,
      'cash_on_delivery_fee': cashOnDeliveryFee,
    };
  }
}

class ShippingRate {
  final String id;
  final String zoneId;
  final String zoneName;
  final String methodId;
  final String methodName;
  final double baseCost;
  final double? weightCost;
  final double? minOrderValue;
  final double? maxOrderValue;
  final bool isActive;

  ShippingRate({
    required this.id,
    required this.zoneId,
    required this.zoneName,
    required this.methodId,
    required this.methodName,
    required this.baseCost,
    this.weightCost,
    this.minOrderValue,
    this.maxOrderValue,
    required this.isActive,
  });

  bool isApplicable(double orderValue) {
    if (minOrderValue != null && orderValue < minOrderValue!) {
      return false;
    }
    if (maxOrderValue != null && orderValue > maxOrderValue!) {
      return false;
    }
    return isActive;
  }

  double calculateCost(double orderWeight) {
    if (weightCost == null || orderWeight <= 0) {
      return baseCost;
    }
    return baseCost + (weightCost! * orderWeight);
  }

  String get formattedBaseCost {
    final formatter = NumberFormat.currency(
      symbol: 'SAR ',
      decimalDigits: 2,
    );
    return formatter.format(baseCost);
  }

  factory ShippingRate.fromJson(Map<String, dynamic> json) {
    return ShippingRate(
      id: json['id'],
      zoneId: json['zone_id'],
      zoneName: json['zone_name'],
      methodId: json['method_id'],
      methodName: json['method_name'],
      baseCost: json['base_cost'].toDouble(),
      weightCost: json['weight_cost']?.toDouble(),
      minOrderValue: json['min_order_value']?.toDouble(),
      maxOrderValue: json['max_order_value']?.toDouble(),
      isActive: json['is_active'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'zone_id': zoneId,
      'zone_name': zoneName,
      'method_id': methodId,
      'method_name': methodName,
      'base_cost': baseCost,
      'weight_cost': weightCost,
      'min_order_value': minOrderValue,
      'max_order_value': maxOrderValue,
      'is_active': isActive,
    };
  }
}

class FreeShippingProduct {
  final String id;
  final String productId;
  final String productName;
  final DateTime? startDate;
  final DateTime? endDate;
  final bool isActive;

  FreeShippingProduct({
    required this.id,
    required this.productId,
    required this.productName,
    this.startDate,
    this.endDate,
    required this.isActive,
  });

  bool get isExpired {
    if (endDate == null) return false;
    return DateTime.now().isAfter(endDate!);
  }

  bool get isAvailable {
    if (!isActive) return false;
    if (startDate != null && DateTime.now().isBefore(startDate!)) return false;
    return !isExpired;
  }

  factory FreeShippingProduct.fromJson(Map<String, dynamic> json) {
    return FreeShippingProduct(
      id: json['id'],
      productId: json['product_id'],
      productName: json['product_name'],
      startDate: json['start_date'] != null ? DateTime.parse(json['start_date']) : null,
      endDate: json['end_date'] != null ? DateTime.parse(json['end_date']) : null,
      isActive: json['is_active'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'product_id': productId,
      'product_name': productName,
      'start_date': startDate?.toIso8601String(),
      'end_date': endDate?.toIso8601String(),
      'is_active': isActive,
    };
  }
} 