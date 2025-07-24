import 'dart:convert';
import 'package:flutter/foundation.dart';
import '../models/cart_item.dart';
import '../models/order.dart';
import 'api_service.dart';

class OrderService extends ChangeNotifier {
  final ApiService _apiService = ApiService();
  List<Order> _orders = [];
  bool _isLoading = false;
  String? _error;

  List<Order> get orders => _orders;
  bool get isLoading => _isLoading;
  String? get error => _error;

  Future<String?> createOrder({
    required String addressId,
    required String shippingMethodId,
    required String shippingZoneId,
    required String paymentMethod,
    required bool cashOnDelivery,
    required List<CartItem> items,
    required double subtotal,
    required double shippingCost,
    required double total,
  }) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final orderItems = items.map((item) => {
        'product_id': item.product.id,
        'quantity': item.quantity,
        'price': item.product.price,
        'subtotal': item.product.price * item.quantity,
      }).toList();

      final payload = {
        'address_id': addressId,
        'shipping_method_id': shippingMethodId,
        'shipping_zone_id': shippingZoneId,
        'payment_method': paymentMethod,
        'cash_on_delivery': cashOnDelivery,
        'items': orderItems,
        'subtotal': subtotal,
        'shipping_cost': shippingCost,
        'total': total,
      };

      final response = await _apiService.post('/orders', payload);
      
      if (response.statusCode == 201) {
        final data = json.decode(response.body)['data'];
        _isLoading = false;
        notifyListeners();
        return data['id'];
      } else {
        throw Exception('Failed to create order: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  Future<List<Order>> getOrders() async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.get('/orders');
      
      if (response.statusCode == 200) {
        final List<dynamic> data = json.decode(response.body)['data'];
        _orders = data.map((json) => Order.fromJson(json)).toList();
        _isLoading = false;
        notifyListeners();
        return _orders;
      } else {
        throw Exception('Failed to load orders: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  Future<Order> getOrderById(String orderId) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.get('/orders/$orderId');
      
      if (response.statusCode == 200) {
        final data = json.decode(response.body)['data'];
        final order = Order.fromJson(data);
        _isLoading = false;
        notifyListeners();
        return order;
      } else {
        throw Exception('Failed to load order: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  Future<Order> trackOrder(String trackingNumber) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.get('/orders/track/$trackingNumber');
      
      if (response.statusCode == 200) {
        final data = json.decode(response.body)['data'];
        final order = Order.fromJson(data);
        _isLoading = false;
        notifyListeners();
        return order;
      } else if (response.statusCode == 404) {
        throw Exception('Order not found with this tracking number');
      } else {
        throw Exception('Failed to track order: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  Future<Order> cancelOrder(String orderId) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.post('/orders/$orderId/cancel', {});
      
      if (response.statusCode == 200) {
        final data = json.decode(response.body)['data'];
        final order = Order.fromJson(data);
        
        // Update the order in the list
        final index = _orders.indexWhere((o) => o.id == orderId);
        if (index != -1) {
          _orders[index] = order;
        }
        
        _isLoading = false;
        notifyListeners();
        return order;
      } else {
        throw Exception('Failed to cancel order: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  Future<bool> requestReturn(String orderId, List<Map<String, dynamic>> items, String reason) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.post(
        '/orders/$orderId/return',
        {
          'items': items,
          'reason': reason,
        },
      );
      
      if (response.statusCode == 200) {
        _isLoading = false;
        notifyListeners();
        return true;
      } else {
        throw Exception('Failed to request return: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  Future<List<Map<String, dynamic>>> getReturns() async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.get('/returns');
      
      if (response.statusCode == 200) {
        final List<dynamic> data = json.decode(response.body)['data'];
        _isLoading = false;
        notifyListeners();
        return List<Map<String, dynamic>>.from(data);
      } else {
        throw Exception('Failed to load returns: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  Future<Map<String, dynamic>> getReturnById(String returnId) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.get('/returns/$returnId');
      
      if (response.statusCode == 200) {
        final data = json.decode(response.body)['data'];
        _isLoading = false;
        notifyListeners();
        return data;
      } else {
        throw Exception('Failed to load return: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  Future<Order> reorder(String orderId) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.post('/orders/$orderId/reorder', {});
      
      if (response.statusCode == 200) {
        final data = json.decode(response.body)['data'];
        final order = Order.fromJson(data);
        _isLoading = false;
        notifyListeners();
        return order;
      } else {
        throw Exception('Failed to reorder: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }
} 