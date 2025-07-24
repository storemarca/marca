import 'dart:convert';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:http/http.dart' as http;
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class ApiService {
  final String _baseUrl = dotenv.env['API_URL'] ?? 'http://localhost/marca/public/api';
  final FlutterSecureStorage _storage = const FlutterSecureStorage();
  
  // Headers with auth token
  Future<Map<String, String>> _getHeaders({bool requiresAuth = true}) async {
    Map<String, String> headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };
    
    if (requiresAuth) {
      final token = await _storage.read(key: 'auth_token');
      if (token != null) {
        headers['Authorization'] = 'Bearer $token';
      }
    }
    
    return headers;
  }
  
  // GET request
  Future<dynamic> get(String endpoint, {bool requiresAuth = true}) async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/$endpoint'),
        headers: await _getHeaders(requiresAuth: requiresAuth),
      );
      
      return _processResponse(response);
    } catch (e) {
      return {'success': false, 'message': 'Network error: $e'};
    }
  }
  
  // POST request
  Future<dynamic> post(String endpoint, dynamic data, {bool requiresAuth = true}) async {
    try {
      final response = await http.post(
        Uri.parse('$_baseUrl/$endpoint'),
        headers: await _getHeaders(requiresAuth: requiresAuth),
        body: json.encode(data),
      );
      
      return _processResponse(response);
    } catch (e) {
      return {'success': false, 'message': 'Network error: $e'};
    }
  }
  
  // PUT request
  Future<dynamic> put(String endpoint, dynamic data, {bool requiresAuth = true}) async {
    try {
      final response = await http.put(
        Uri.parse('$_baseUrl/$endpoint'),
        headers: await _getHeaders(requiresAuth: requiresAuth),
        body: json.encode(data),
      );
      
      return _processResponse(response);
    } catch (e) {
      return {'success': false, 'message': 'Network error: $e'};
    }
  }
  
  // DELETE request
  Future<dynamic> delete(String endpoint, {bool requiresAuth = true}) async {
    try {
      final response = await http.delete(
        Uri.parse('$_baseUrl/$endpoint'),
        headers: await _getHeaders(requiresAuth: requiresAuth),
      );
      
      return _processResponse(response);
    } catch (e) {
      return {'success': false, 'message': 'Network error: $e'};
    }
  }
  
  // Process HTTP response
  dynamic _processResponse(http.Response response) {
    if (response.statusCode >= 200 && response.statusCode < 300) {
      if (response.body.isNotEmpty) {
        return json.decode(response.body);
      }
      return {'success': true};
    } else if (response.statusCode == 401) {
      // Unauthorized - token expired or invalid
      return {'success': false, 'message': 'Unauthorized', 'status': 401};
    } else if (response.statusCode == 422) {
      // Validation errors
      return json.decode(response.body);
    } else {
      // Other errors
      return {
        'success': false,
        'message': 'Server error: ${response.statusCode}',
        'status': response.statusCode
      };
    }
  }
  
  // Save auth token
  Future<void> saveAuthToken(String token) async {
    await _storage.write(key: 'auth_token', value: token);
  }
  
  // Clear auth token
  Future<void> clearAuthToken() async {
    await _storage.delete(key: 'auth_token');
  }
  
  // Check if user is authenticated
  Future<bool> isAuthenticated() async {
    final token = await _storage.read(key: 'auth_token');
    return token != null;
  }
} 