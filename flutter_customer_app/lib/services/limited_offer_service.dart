import 'dart:convert';
import 'package:flutter/foundation.dart';
import '../models/limited_offer.dart';
import 'api_service.dart';

class LimitedOfferService extends ChangeNotifier {
  final ApiService _apiService = ApiService();
  List<LimitedOffer> _limitedOffers = [];
  bool _isLoading = false;
  String? _error;

  List<LimitedOffer> get limitedOffers => _limitedOffers;
  bool get isLoading => _isLoading;
  String? get error => _error;

  List<LimitedOffer> get activeOffers {
    return _limitedOffers.where((offer) => offer.isAvailable).toList();
  }

  Future<List<LimitedOffer>> getLimitedOffers() async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.get('/offers/limited');
      
      if (response.statusCode == 200) {
        final List<dynamic> data = json.decode(response.body)['data'];
        _limitedOffers = data.map((json) => LimitedOffer.fromJson(json)).toList();
        _isLoading = false;
        notifyListeners();
        return _limitedOffers;
      } else {
        throw Exception('Failed to load limited offers: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  Future<LimitedOffer?> getLimitedOfferByProductId(String productId) async {
    // First check if we already have this offer loaded
    final existingOffer = _limitedOffers.firstWhere(
      (offer) => offer.productId == productId && offer.isAvailable,
      orElse: () => LimitedOffer(
        id: '',
        productId: '',
        productName: '',
        originalPrice: 0,
        offerPrice: 0,
        totalQuantity: 0,
        remainingQuantity: 0,
        startDate: DateTime.now(),
        endDate: DateTime.now(),
        isActive: false,
      ),
    );

    if (existingOffer.id.isNotEmpty) {
      return existingOffer;
    }

    // If not found or not loaded yet, fetch from API
    try {
      final response = await _apiService.get('/offers/product/$productId');
      
      if (response.statusCode == 200) {
        final data = json.decode(response.body)['data'];
        if (data != null) {
          final offer = LimitedOffer.fromJson(data);
          
          // Update in the list if it exists
          final index = _limitedOffers.indexWhere((o) => o.id == offer.id);
          if (index != -1) {
            _limitedOffers[index] = offer;
          } else {
            _limitedOffers.add(offer);
          }
          
          notifyListeners();
          return offer;
        }
      }
      return null;
    } catch (e) {
      // Just return null if there's no offer for this product
      return null;
    }
  }

  // This method is called when a product with a limited offer is added to cart
  Future<bool> reserveOfferQuantity(String offerId, int quantity) async {
    try {
      final response = await _apiService.post(
        '/offers/$offerId/reserve',
        {'quantity': quantity},
      );
      
      if (response.statusCode == 200) {
        // Update the remaining quantity in our local list
        final data = json.decode(response.body)['data'];
        final updatedOffer = LimitedOffer.fromJson(data);
        
        final index = _limitedOffers.indexWhere((o) => o.id == offerId);
        if (index != -1) {
          _limitedOffers[index] = updatedOffer;
          notifyListeners();
        }
        
        return true;
      }
      return false;
    } catch (e) {
      return false;
    }
  }

  // This method is called when a product with a limited offer is removed from cart
  Future<bool> releaseOfferQuantity(String offerId, int quantity) async {
    try {
      final response = await _apiService.post(
        '/offers/$offerId/release',
        {'quantity': quantity},
      );
      
      if (response.statusCode == 200) {
        // Update the remaining quantity in our local list
        final data = json.decode(response.body)['data'];
        final updatedOffer = LimitedOffer.fromJson(data);
        
        final index = _limitedOffers.indexWhere((o) => o.id == offerId);
        if (index != -1) {
          _limitedOffers[index] = updatedOffer;
          notifyListeners();
        }
        
        return true;
      }
      return false;
    } catch (e) {
      return false;
    }
  }
} 