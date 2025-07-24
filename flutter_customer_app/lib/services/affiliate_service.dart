import 'dart:convert';
import 'package:flutter/foundation.dart';
import '../models/affiliate.dart';
import 'api_service.dart';

class AffiliateService extends ChangeNotifier {
  final ApiService _apiService = ApiService();
  List<AffiliateLink> _links = [];
  AffiliateStats? _stats;
  List<WithdrawalRequest> _withdrawals = [];
  bool _isLoading = false;
  String? _error;

  List<AffiliateLink> get links => _links;
  AffiliateStats? get stats => _stats;
  List<WithdrawalRequest> get withdrawals => _withdrawals;
  bool get isLoading => _isLoading;
  String? get error => _error;

  Future<List<AffiliateLink>> getAffiliateLinks() async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.get('/affiliate/links');
      
      if (response.statusCode == 200) {
        final List<dynamic> data = json.decode(response.body)['data'];
        _links = data.map((json) => AffiliateLink.fromJson(json)).toList();
        _isLoading = false;
        notifyListeners();
        return _links;
      } else {
        throw Exception('Failed to load affiliate links: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  Future<AffiliateLink> createAffiliateLink(Map<String, dynamic> data) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.post('/affiliate/links', data);
      
      if (response.statusCode == 201) {
        final linkData = json.decode(response.body)['data'];
        final newLink = AffiliateLink.fromJson(linkData);
        
        _links.add(newLink);
        _isLoading = false;
        notifyListeners();
        return newLink;
      } else {
        throw Exception('Failed to create affiliate link: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  Future<bool> deleteAffiliateLink(String id) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.delete('/affiliate/links/$id');
      
      if (response.statusCode == 200) {
        _links.removeWhere((link) => link.id == id);
        _isLoading = false;
        notifyListeners();
        return true;
      } else {
        throw Exception('Failed to delete affiliate link: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  Future<AffiliateStats?> getAffiliateStats() async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.get('/affiliate/stats');
      
      if (response.statusCode == 200) {
        final data = json.decode(response.body)['data'];
        _stats = AffiliateStats.fromJson(data);
        _isLoading = false;
        notifyListeners();
        return _stats;
      } else {
        throw Exception('Failed to load affiliate stats: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  Future<List<WithdrawalRequest>> getWithdrawals() async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.get('/affiliate/withdrawals');
      
      if (response.statusCode == 200) {
        final List<dynamic> data = json.decode(response.body)['data'];
        _withdrawals = data.map((json) => WithdrawalRequest.fromJson(json)).toList();
        _isLoading = false;
        notifyListeners();
        return _withdrawals;
      } else {
        throw Exception('Failed to load withdrawals: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  Future<WithdrawalRequest> requestWithdrawal(Map<String, dynamic> data) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.post('/affiliate/withdrawals', data);
      
      if (response.statusCode == 201) {
        final withdrawalData = json.decode(response.body)['data'];
        final newWithdrawal = WithdrawalRequest.fromJson(withdrawalData);
        
        _withdrawals.add(newWithdrawal);
        
        // Update available balance
        if (_stats != null) {
          await getAffiliateStats();
        }
        
        _isLoading = false;
        notifyListeners();
        return newWithdrawal;
      } else {
        throw Exception('Failed to request withdrawal: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }
} 