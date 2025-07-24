import 'dart:convert';
import 'package:flutter/foundation.dart';
import '../models/shipping_cost.dart';
import 'api_service.dart';

class ShippingService extends ChangeNotifier {
  final ApiService _apiService = ApiService();
  List<ShippingZone> _zones = [];
  List<ShippingMethod> _methods = [];
  List<ShippingRate> _rates = [];
  List<FreeShippingProduct> _freeShippingProducts = [];
  bool _isLoading = false;
  String? _error;

  List<ShippingZone> get zones => _zones;
  List<ShippingMethod> get methods => _methods;
  List<ShippingRate> get rates => _rates;
  List<FreeShippingProduct> get freeShippingProducts => _freeShippingProducts;
  bool get isLoading => _isLoading;
  String? get error => _error;

  Future<void> loadShippingData() async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      await Future.wait([
        _loadShippingZones(),
        _loadShippingMethods(),
        _loadShippingRates(),
        _loadFreeShippingProducts(),
      ]);
      
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<List<ShippingZone>> _loadShippingZones() async {
    try {
      final response = await _apiService.get('/shipping/zones');
      
      if (response.statusCode == 200) {
        final List<dynamic> data = json.decode(response.body)['data'];
        _zones = data.map((json) => ShippingZone.fromJson(json)).toList();
        return _zones;
      } else {
        throw Exception('Failed to load shipping zones: ${response.statusCode}');
      }
    } catch (e) {
      rethrow;
    }
  }

  Future<List<ShippingMethod>> _loadShippingMethods() async {
    try {
      final response = await _apiService.get('/shipping/methods');
      
      if (response.statusCode == 200) {
        final List<dynamic> data = json.decode(response.body)['data'];
        _methods = data.map((json) => ShippingMethod.fromJson(json)).toList();
        return _methods;
      } else {
        throw Exception('Failed to load shipping methods: ${response.statusCode}');
      }
    } catch (e) {
      rethrow;
    }
  }

  Future<List<ShippingRate>> _loadShippingRates() async {
    try {
      final response = await _apiService.get('/shipping/rates');
      
      if (response.statusCode == 200) {
        final List<dynamic> data = json.decode(response.body)['data'];
        _rates = data.map((json) => ShippingRate.fromJson(json)).toList();
        return _rates;
      } else {
        throw Exception('Failed to load shipping rates: ${response.statusCode}');
      }
    } catch (e) {
      rethrow;
    }
  }

  Future<List<FreeShippingProduct>> _loadFreeShippingProducts() async {
    try {
      final response = await _apiService.get('/shipping/free-products');
      
      if (response.statusCode == 200) {
        final List<dynamic> data = json.decode(response.body)['data'];
        _freeShippingProducts = data.map((json) => FreeShippingProduct.fromJson(json)).toList();
        return _freeShippingProducts;
      } else {
        throw Exception('Failed to load free shipping products: ${response.statusCode}');
      }
    } catch (e) {
      rethrow;
    }
  }

  bool isProductEligibleForFreeShipping(String productId) {
    return _freeShippingProducts.any((product) => 
      product.productId == productId && product.isAvailable
    );
  }

  List<ShippingMethod> getAvailableMethodsForZone(String zoneId) {
    final zoneRates = _rates.where((rate) => rate.zoneId == zoneId && rate.isActive).toList();
    final methodIds = zoneRates.map((rate) => rate.methodId).toSet();
    return _methods.where((method) => methodIds.contains(method.id) && method.isActive).toList();
  }

  List<ShippingRate> getApplicableRates(String zoneId, double orderValue) {
    return _rates.where((rate) => 
      rate.zoneId == zoneId && 
      rate.isActive && 
      rate.isApplicable(orderValue)
    ).toList();
  }

  ShippingZone? findZoneByCity(String country, String city) {
    return _zones.firstWhere(
      (zone) => 
        zone.country.toLowerCase() == country.toLowerCase() && 
        zone.cities.any((zoneCity) => zoneCity.toLowerCase() == city.toLowerCase()) &&
        zone.isActive,
      orElse: () => ShippingZone(
        id: '',
        name: '',
        country: '',
        cities: [],
        isActive: false,
      ),
    );
  }

  double calculateShippingCost({
    required String zoneId,
    required String methodId,
    required double orderValue,
    required double orderWeight,
    required List<String> productIds,
  }) {
    // Check if all products have free shipping
    final allProductsHaveFreeShipping = productIds.every(isProductEligibleForFreeShipping);
    if (allProductsHaveFreeShipping) {
      return 0.0;
    }

    // Find applicable rate
    final rate = _rates.firstWhere(
      (rate) => 
        rate.zoneId == zoneId && 
        rate.methodId == methodId && 
        rate.isActive && 
        rate.isApplicable(orderValue),
      orElse: () => ShippingRate(
        id: '',
        zoneId: '',
        zoneName: '',
        methodId: '',
        methodName: '',
        baseCost: 0,
        isActive: false,
      ),
    );

    if (rate.id.isEmpty) {
      return 0.0; // No applicable rate found
    }

    return rate.calculateCost(orderWeight);
  }
} 