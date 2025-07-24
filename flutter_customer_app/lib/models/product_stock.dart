import 'warehouse.dart';

class ProductStock {
  final int id;
  final int productId;
  final int warehouseId;
  final Warehouse? warehouse;
  final int quantity;
  final double costPrice;
  final DateTime createdAt;
  final DateTime updatedAt;

  ProductStock({
    required this.id,
    required this.productId,
    required this.warehouseId,
    this.warehouse,
    required this.quantity,
    required this.costPrice,
    required this.createdAt,
    required this.updatedAt,
  });

  factory ProductStock.fromJson(Map<String, dynamic> json) {
    // Parse warehouse
    Warehouse? parseWarehouse(dynamic warehouseData) {
      if (warehouseData == null) return null;
      
      if (warehouseData is Map<String, dynamic>) {
        return Warehouse.fromJson(warehouseData);
      }
      
      return null;
    }

    return ProductStock(
      id: json['id'],
      productId: json['product_id'],
      warehouseId: json['warehouse_id'],
      warehouse: parseWarehouse(json['warehouse']),
      quantity: json['quantity'] ?? 0,
      costPrice: (json['cost_price'] ?? 0).toDouble(),
      createdAt: json['created_at'] != null 
          ? DateTime.parse(json['created_at']) 
          : DateTime.now(),
      updatedAt: json['updated_at'] != null 
          ? DateTime.parse(json['updated_at']) 
          : DateTime.now(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'product_id': productId,
      'warehouse_id': warehouseId,
      'warehouse': warehouse?.toJson(),
      'quantity': quantity,
      'cost_price': costPrice,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }
} 