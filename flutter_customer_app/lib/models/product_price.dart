class ProductPrice {
  final int id;
  final int productId;
  final int countryId;
  final String? countryName;
  final String currencySymbol;
  final double price;
  final double? salePrice;
  final DateTime? salePriceStartDate;
  final DateTime? salePriceEndDate;
  final bool isActive;
  final DateTime createdAt;
  final DateTime updatedAt;

  ProductPrice({
    required this.id,
    required this.productId,
    required this.countryId,
    this.countryName,
    required this.currencySymbol,
    required this.price,
    this.salePrice,
    this.salePriceStartDate,
    this.salePriceEndDate,
    required this.isActive,
    required this.createdAt,
    required this.updatedAt,
  });

  factory ProductPrice.fromJson(Map<String, dynamic> json) {
    return ProductPrice(
      id: json['id'],
      productId: json['product_id'],
      countryId: json['country_id'],
      countryName: json['country_name'],
      currencySymbol: json['currency_symbol'] ?? '\$',
      price: (json['price'] ?? 0).toDouble(),
      salePrice: json['sale_price'] != null ? json['sale_price'].toDouble() : null,
      salePriceStartDate: json['sale_price_start_date'] != null 
          ? DateTime.parse(json['sale_price_start_date']) 
          : null,
      salePriceEndDate: json['sale_price_end_date'] != null 
          ? DateTime.parse(json['sale_price_end_date']) 
          : null,
      isActive: json['is_active'] ?? true,
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
      'country_id': countryId,
      'country_name': countryName,
      'currency_symbol': currencySymbol,
      'price': price,
      'sale_price': salePrice,
      'sale_price_start_date': salePriceStartDate?.toIso8601String(),
      'sale_price_end_date': salePriceEndDate?.toIso8601String(),
      'is_active': isActive,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }
} 