import 'dart:convert';
import 'package:flutter/foundation.dart';
import '../models/cart_item.dart';
import '../models/limited_offer.dart';
import '../models/product.dart';
import 'api_service.dart';
import 'limited_offer_service.dart';
import 'shipping_service.dart';

class CartService extends ChangeNotifier {
  final ApiService _apiService = ApiService();
  List<CartItem> _items = [];
  bool _isLoading = false;
  String? _error;
  double _tax = 0.0;
  double _discount = 0.0;
  String _currencySymbol = '\$'; // Default currency symbol

  List<CartItem> get items => _items;
  bool get isLoading => _isLoading;
  String? get error => _error;
  String get currencySymbol => _currencySymbol;
  double get tax => _tax;
  double get discount => _discount;
  double get total => subtotal + tax - discount;

  int get itemCount => _items.fold(0, (sum, item) => sum + item.quantity);

  double get subtotal => _items.fold(0, (sum, item) => sum + item.total);

  // Reset error state
  void resetError() {
    _error = null;
    notifyListeners();
  }

  // Method to fetch cart data from the API
  Future<void> getCart() async {
    await getCartItems();
    await _fetchCartTotals();
  }

  // Method to update item quantity
  Future<void> updateQuantity(String itemId, int quantity) async {
    await updateCartItemQuantity(itemId, quantity);
  }

  // Private method to fetch cart totals (tax, discount, etc.)
  Future<void> _fetchCartTotals() async {
    try {
      final response = await _apiService.get('/cart/totals');

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        _tax = (data['tax'] as num).toDouble();
        _discount = (data['discount'] as num).toDouble();
        _currencySymbol = data['currency_symbol'] ?? '\$';
        notifyListeners();
      }
    } catch (e) {
      _error = 'Failed to fetch cart totals: $e';
      notifyListeners();
    }
  }

  Future<List<CartItem>> getCartItems() async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.get('/cart');

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        final List<dynamic> itemsData = data['items'];

        // We need to fetch product details for each cart item
        final List<CartItem> cartItems = [];
        for (var itemData in itemsData) {
          final productResponse =
              await _apiService.get('/products/${itemData['product_id']}');
          if (productResponse.statusCode == 200) {
            final productData = json.decode(productResponse.body)['data'];
            final product = Product.fromJson(productData);
            cartItems.add(CartItem.fromJson(itemData, product));
          }
        }

        _items = cartItems;
        _isLoading = false;
        notifyListeners();
        return _items;
      } else {
        throw Exception('Failed to load cart items: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  Future<bool> addToCart({
    required Product product,
    required int quantity,
    LimitedOffer? offer,
    bool hasFreeShipping = false,
  }) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      // Check if we're adding a product with limited offer
      final price = offer != null
          ? offer.offerPrice
          : (product.getCurrentPrice()?.price ?? 0.0);
      final offerId = offer?.id;
      final hasLimitedOffer = offer != null;

      final data = {
        'product_id': product.id,
        'quantity': quantity,
        'price': price,
        if (offerId != null) 'offer_id': offerId,
        'has_limited_offer': hasLimitedOffer,
        'has_free_shipping': hasFreeShipping,
      };

      final response = await _apiService.post('/cart', data);

      if (response.statusCode == 200 || response.statusCode == 201) {
        // If it's a limited offer, reserve the quantity
        if (offer != null) {
          final limitedOfferService = LimitedOfferService();
          await limitedOfferService.reserveOfferQuantity(offer.id, quantity);
        }

        // Refresh cart items
        await getCartItems();
        return true;
      } else {
        throw Exception('Failed to add item to cart: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  Future<void> updateCartItemQuantity(String itemId, int quantity) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      // Find the current item to get its current quantity
      final currentItem = _items.firstWhere((item) => item.id == itemId);
      final quantityDifference = quantity - currentItem.quantity;

      final response = await _apiService.put(
        '/cart/$itemId',
        {'quantity': quantity},
      );

      if (response.statusCode == 200) {
        // If it's a limited offer, update the reserved quantity
        if (currentItem.hasLimitedOffer && currentItem.offerId != null) {
          final limitedOfferService = LimitedOfferService();
          if (quantityDifference > 0) {
            // Reserve additional quantity
            await limitedOfferService.reserveOfferQuantity(
              currentItem.offerId!,
              quantityDifference,
            );
          } else if (quantityDifference < 0) {
            // Release some quantity
            await limitedOfferService.releaseOfferQuantity(
              currentItem.offerId!,
              -quantityDifference,
            );
          }
        }

        // Update local state
        final index = _items.indexWhere((item) => item.id == itemId);
        if (index != -1) {
          _items[index] = _items[index].copyWith(quantity: quantity);
          notifyListeners();
        }

        _isLoading = false;
      } else {
        throw Exception('Failed to update cart item: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  Future<void> removeFromCart(String itemId) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      // Find the current item to get its quantity
      final currentItem = _items.firstWhere((item) => item.id == itemId);

      final response = await _apiService.delete('/cart/$itemId');

      if (response.statusCode == 200) {
        // If it's a limited offer, release the quantity
        if (currentItem.hasLimitedOffer && currentItem.offerId != null) {
          final limitedOfferService = LimitedOfferService();
          await limitedOfferService.releaseOfferQuantity(
            currentItem.offerId!,
            currentItem.quantity,
          );
        }

        // Update local state
        _items.removeWhere((item) => item.id == itemId);
        _isLoading = false;
        notifyListeners();
      } else {
        throw Exception(
            'Failed to remove item from cart: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  Future<void> clearCart() async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      // For each limited offer item, release the quantity
      for (var item in _items) {
        if (item.hasLimitedOffer && item.offerId != null) {
          final limitedOfferService = LimitedOfferService();
          await limitedOfferService.releaseOfferQuantity(
            item.offerId!,
            item.quantity,
          );
        }
      }

      final response = await _apiService.delete('/cart');

      if (response.statusCode == 200) {
        _items = [];
        _isLoading = false;
        notifyListeners();
      } else {
        throw Exception('Failed to clear cart: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  // Calculate shipping cost based on the selected zone, method, and cart items
  double calculateShippingCost({
    required String zoneId,
    required String methodId,
    required ShippingService shippingService,
  }) {
    // Check if all products have free shipping
    final allProductsHaveFreeShipping =
        _items.every((item) => item.hasFreeShipping);
    if (allProductsHaveFreeShipping) {
      return 0.0;
    }

    // Calculate total weight
    final double totalWeight = _items.fold(
        0, (sum, item) => sum + (item.product.weight * item.quantity));

    // Get product IDs for checking free shipping eligibility
    final List<String> productIds =
        _items.map((item) => item.product.id.toString()).toList();

    return shippingService.calculateShippingCost(
      zoneId: zoneId,
      methodId: methodId,
      orderValue: subtotal,
      orderWeight: totalWeight,
      productIds: productIds,
    );
  }
}
