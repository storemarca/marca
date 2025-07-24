import 'dart:convert';
import 'package:flutter/foundation.dart';
import '../models/loyalty_point.dart';
import 'api_service.dart';

class LoyaltyService extends ChangeNotifier {
  final ApiService _apiService = ApiService();
  UserLoyalty? _userLoyalty;
  List<LoyaltyReward> _rewards = [];
  bool _isLoading = false;
  String? _error;

  UserLoyalty? get userLoyalty => _userLoyalty;
  List<LoyaltyReward> get rewards => _rewards;
  bool get isLoading => _isLoading;
  String? get error => _error;

  Future<UserLoyalty?> getUserLoyalty() async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.get('/loyalty/points');
      
      if (response.statusCode == 200) {
        final data = json.decode(response.body)['data'];
        _userLoyalty = UserLoyalty.fromJson(data);
        _isLoading = false;
        notifyListeners();
        return _userLoyalty;
      } else {
        throw Exception('Failed to load loyalty points: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  Future<List<LoyaltyReward>> getRewards() async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.get('/loyalty/rewards');
      
      if (response.statusCode == 200) {
        final List<dynamic> data = json.decode(response.body)['data'];
        _rewards = data.map((json) => LoyaltyReward.fromJson(json)).toList();
        _isLoading = false;
        notifyListeners();
        return _rewards;
      } else {
        throw Exception('Failed to load rewards: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  Future<bool> redeemReward(String rewardId) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.post(
        '/loyalty/rewards/$rewardId/redeem',
        {},
      );
      
      if (response.statusCode == 200) {
        // Update user loyalty points
        await getUserLoyalty();
        _isLoading = false;
        notifyListeners();
        return true;
      } else {
        throw Exception('Failed to redeem reward: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }
} 