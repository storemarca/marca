import 'package:flutter/foundation.dart';
import 'api_service.dart';
import '../models/user.dart';

class AuthService extends ChangeNotifier {
  final ApiService _apiService = ApiService();
  User? _currentUser;
  bool _isLoading = false;
  String? _error;

  User? get currentUser => _currentUser;
  bool get isLoading => _isLoading;
  String? get error => _error;
  
  // Check if user is authenticated
  Future<bool> isAuthenticated() async {
    return await _apiService.isAuthenticated();
  }
  
  // Register a new user
  Future<bool> register({
    required String name,
    required String email,
    required String password,
    required String passwordConfirmation,
    String? phone,
  }) async {
    _isLoading = true;
    _error = null;
    notifyListeners();
    
    try {
      final response = await _apiService.post('register', {
        'name': name,
        'email': email,
        'password': password,
        'password_confirmation': passwordConfirmation,
        'phone': phone,
      }, requiresAuth: false);
      
      _isLoading = false;
      
      if (response['success'] == true) {
        // Save auth token
        await _apiService.saveAuthToken(response['data']['token']);
        
        // Set current user
        _currentUser = User.fromJson(response['data']['user']);
        
        notifyListeners();
        return true;
      } else {
        _error = response['message'] ?? 'Registration failed';
        notifyListeners();
        return false;
      }
    } catch (e) {
      _isLoading = false;
      _error = 'Registration failed: $e';
      notifyListeners();
      return false;
    }
  }
  
  // Login user
  Future<bool> login({required String email, required String password}) async {
    _isLoading = true;
    _error = null;
    notifyListeners();
    
    try {
      final response = await _apiService.post('login', {
        'email': email,
        'password': password,
      }, requiresAuth: false);
      
      _isLoading = false;
      
      if (response['success'] == true) {
        // Save auth token
        await _apiService.saveAuthToken(response['data']['token']);
        
        // Set current user
        _currentUser = User.fromJson(response['data']['user']);
        
        notifyListeners();
        return true;
      } else {
        _error = response['message'] ?? 'Login failed';
        notifyListeners();
        return false;
      }
    } catch (e) {
      _isLoading = false;
      _error = 'Login failed: $e';
      notifyListeners();
      return false;
    }
  }
  
  // Logout user
  Future<bool> logout() async {
    _isLoading = true;
    notifyListeners();
    
    try {
      final response = await _apiService.post('logout', {});
      
      _isLoading = false;
      
      if (response['success'] == true) {
        // Clear auth token
        await _apiService.clearAuthToken();
        
        // Clear current user
        _currentUser = null;
        
        notifyListeners();
        return true;
      } else {
        _error = response['message'] ?? 'Logout failed';
        notifyListeners();
        return false;
      }
    } catch (e) {
      _isLoading = false;
      _error = 'Logout failed: $e';
      notifyListeners();
      
      // Force logout even if API call fails
      await _apiService.clearAuthToken();
      _currentUser = null;
      notifyListeners();
      
      return true;
    }
  }
  
  // Get user profile
  Future<bool> getUserProfile() async {
    _isLoading = true;
    _error = null;
    notifyListeners();
    
    try {
      final response = await _apiService.get('user');
      
      _isLoading = false;
      
      if (response['data'] != null) {
        _currentUser = User.fromJson(response['data']);
        notifyListeners();
        return true;
      } else {
        _error = response['message'] ?? 'Failed to get user profile';
        notifyListeners();
        return false;
      }
    } catch (e) {
      _isLoading = false;
      _error = 'Failed to get user profile: $e';
      notifyListeners();
      return false;
    }
  }
  
  // Update user profile
  Future<bool> updateProfile({
    required String name,
    required String email,
    String? phone,
  }) async {
    _isLoading = true;
    _error = null;
    notifyListeners();
    
    try {
      final response = await _apiService.put('user', {
        'name': name,
        'email': email,
        'phone': phone,
      });
      
      _isLoading = false;
      
      if (response['data'] != null) {
        _currentUser = User.fromJson(response['data']);
        notifyListeners();
        return true;
      } else {
        _error = response['message'] ?? 'Failed to update profile';
        notifyListeners();
        return false;
      }
    } catch (e) {
      _isLoading = false;
      _error = 'Failed to update profile: $e';
      notifyListeners();
      return false;
    }
  }
  
  // Reset error
  void resetError() {
    _error = null;
    notifyListeners();
  }
} 