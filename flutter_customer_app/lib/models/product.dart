import 'dart:convert';

import 'category.dart';
import 'product_price.dart';
import 'product_stock.dart';

class Product {
  final int id;
  final String name;
  final String slug;
  final String description;
  final String? shortDescription;
  final String sku;
  final String? barcode;
  final int categoryId;
  final Category? category;
  final double cost;
  final double weight;
  final double? width;
  final double? height;
  final double? length;
  final bool isActive;
  final bool isFeatured;
  final List<String> images;
  final String? videoUrl;
  final Map<String, dynamic>? attributes;
  final List<ProductPrice> prices;
  final List<ProductStock> stocks;
  final DateTime createdAt;
  final DateTime updatedAt;
  List<Product> relatedProducts;

  Product({
    required this.id,
    required this.name,
    required this.slug,
    required this.description,
    this.shortDescription,
    required this.sku,
    this.barcode,
    required this.categoryId,
    this.category,
    required this.cost,
    required this.weight,
    this.width,
    this.height,
    this.length,
    required this.isActive,
    required this.isFeatured,
    required this.images,
    this.videoUrl,
    this.attributes,
    required this.prices,
    required this.stocks,
    required this.createdAt,
    required this.updatedAt,
    this.relatedProducts = const [],
  });

  factory Product.fromJson(Map<String, dynamic> json) {
    // Parse images
    List<String> parseImages(dynamic imagesData) {
      if (imagesData == null) return [];

      if (imagesData is List) {
        return imagesData.map((img) => img.toString()).toList();
      } else if (imagesData is String) {
        try {
          final List<dynamic> parsed = jsonDecode(imagesData);
          return parsed.map((img) => img.toString()).toList();
        } catch (_) {
          return [imagesData];
        }
      }

      return [];
    }

    // Parse prices
    List<ProductPrice> parsePrices(dynamic pricesData) {
      if (pricesData == null) return [];

      if (pricesData is List) {
        return pricesData
            .map((price) => ProductPrice.fromJson(price))
            .toList();
      }

      return [];
    }

    // Parse stocks
    List<ProductStock> parseStocks(dynamic stocksData) {
      if (stocksData == null) return [];

      if (stocksData is List) {
        return stocksData
            .map((stock) => ProductStock.fromJson(stock))
            .toList();
      }

      return [];
    }

    // Parse category
    Category? parseCategory(dynamic categoryData) {
      if (categoryData == null) return null;

      if (categoryData is Map<String, dynamic>) {
        return Category.fromJson(categoryData);
      }

      return null;
    }

    return Product(
      id: json['id'],
      name: json['name'],
      slug: json['slug'],
      description: json['description'],
      shortDescription: json['short_description'],
      sku: json['sku'],
      barcode: json['barcode'],
      categoryId: json['category_id'],
      category: parseCategory(json['category']),
      cost: (json['cost'] ?? 0).toDouble(),
      weight: (json['weight'] ?? 0).toDouble(),
      width: json['width'] != null ? json['width'].toDouble() : null,
      height: json['height'] != null ? json['height'].toDouble() : null,
      length: json['length'] != null ? json['length'].toDouble() : null,
      isActive: json['is_active'] ?? true,
      isFeatured: json['is_featured'] ?? false,
      images: parseImages(json['images']),
      videoUrl: json['video_url'],
      attributes: json['attributes'],
      prices: parsePrices(json['prices']),
      stocks: parseStocks(json['stocks']),
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
      'name': name,
      'slug': slug,
      'description': description,
      'short_description': shortDescription,
      'sku': sku,
      'barcode': barcode,
      'category_id': categoryId,
      'category': category?.toJson(),
      'cost': cost,
      'weight': weight,
      'width': width,
      'height': height,
      'length': length,
      'is_active': isActive,
      'is_featured': isFeatured,
      'images': images,
      'video_url': videoUrl,
      'attributes': attributes,
      'prices': prices.map((price) => price.toJson()).toList(),
      'stocks': stocks.map((stock) => stock.toJson()).toList(),
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }

  // Get current price
  ProductPrice? getCurrentPrice() {
    if (prices.isEmpty) return null;
    return prices.first;
  }

  // Get formatted price
  String getFormattedPrice() {
    final price = getCurrentPrice();
    if (price == null) return '0.00';

    if (price.salePrice != null &&
        price.salePriceStartDate != null &&
        price.salePriceEndDate != null) {
      final now = DateTime.now();
      if (now.isAfter(price.salePriceStartDate!) &&
          now.isBefore(price.salePriceEndDate!)) {
        return '${price.currencySymbol}${price.salePrice!.toStringAsFixed(2)}';
      }
    }

    return '${price.currencySymbol}${price.price.toStringAsFixed(2)}';
  }

  // Check if product is on sale
  bool isOnSale() {
    final price = getCurrentPrice();
    if (price == null) return false;

    if (price.salePrice != null &&
        price.salePriceStartDate != null &&
        price.salePriceEndDate != null) {
      final now = DateTime.now();
      return now.isAfter(price.salePriceStartDate!) &&
             now.isBefore(price.salePriceEndDate!);
    }

    return false;
  }

  // Get discount percentage
  int getDiscountPercentage() {
    final price = getCurrentPrice();
    if (price == null || !isOnSale() || price.salePrice == null) return 0;

    final discount = ((price.price - price.salePrice!) / price.price) * 100;
    return discount.round();
  }

  // Get total stock
  int getTotalStock() {
    if (stocks.isEmpty) return 0;
    return stocks.fold(0, (sum, stock) => sum + stock.quantity);
  }

  // Check if product is in stock
  bool isInStock() {
    return getTotalStock() > 0;
  }
}
