import 'dart:convert';
import 'package:flutter/foundation.dart';
import '../models/address.dart';
import 'api_service.dart';

class AddressService extends ChangeNotifier {
  final ApiService _apiService = ApiService();
  List<Address> _addresses = [];
  bool _isLoading = false;
  String? _error;

  List<Address> get addresses => _addresses;
  bool get isLoading => _isLoading;
  String? get error => _error;

  Future<List<Address>> getAddresses() async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.get('/addresses');
      
      if (response.statusCode == 200) {
        final List<dynamic> data = json.decode(response.body)['data'];
        _addresses = data.map((json) => Address.fromJson(json)).toList();
        _isLoading = false;
        notifyListeners();
        return _addresses;
      } else {
        throw Exception('Failed to load addresses: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  Future<Address> getAddressById(String id) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.get('/addresses/$id');
      
      if (response.statusCode == 200) {
        final data = json.decode(response.body)['data'];
        final address = Address.fromJson(data);
        _isLoading = false;
        notifyListeners();
        return address;
      } else {
        throw Exception('Failed to load address: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  Future<Address> addAddress(Address address) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.post(
        '/addresses',
        address.toJson(),
      );
      
      if (response.statusCode == 201) {
        final data = json.decode(response.body)['data'];
        final newAddress = Address.fromJson(data);
        
        _addresses.add(newAddress);
        
        // If this is the default address, update other addresses
        if (newAddress.isDefault) {
          _updateDefaultAddresses(newAddress.id);
        }
        
        _isLoading = false;
        notifyListeners();
        return newAddress;
      } else {
        throw Exception('Failed to add address: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  Future<Address> updateAddress(Address address) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.put(
        '/addresses/${address.id}',
        address.toJson(),
      );
      
      if (response.statusCode == 200) {
        final data = json.decode(response.body)['data'];
        final updatedAddress = Address.fromJson(data);
        
        final index = _addresses.indexWhere((a) => a.id == address.id);
        if (index != -1) {
          _addresses[index] = updatedAddress;
        }
        
        // If this is the default address, update other addresses
        if (updatedAddress.isDefault) {
          _updateDefaultAddresses(updatedAddress.id);
        }
        
        _isLoading = false;
        notifyListeners();
        return updatedAddress;
      } else {
        throw Exception('Failed to update address: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  Future<void> deleteAddress(String id) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.delete('/addresses/$id');
      
      if (response.statusCode == 200) {
        _addresses.removeWhere((a) => a.id == id);
        _isLoading = false;
        notifyListeners();
      } else {
        throw Exception('Failed to delete address: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  Future<void> setDefaultAddress(String id) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.post(
        '/addresses/$id/default',
        {},
      );
      
      if (response.statusCode == 200) {
        _updateDefaultAddresses(id);
        _isLoading = false;
        notifyListeners();
      } else {
        throw Exception('Failed to set default address: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      rethrow;
    }
  }

  void _updateDefaultAddresses(String defaultId) {
    for (int i = 0; i < _addresses.length; i++) {
      if (_addresses[i].id == defaultId) {
        if (!_addresses[i].isDefault) {
          _addresses[i] = _addresses[i].copyWith(isDefault: true);
        }
      } else if (_addresses[i].isDefault) {
        _addresses[i] = _addresses[i].copyWith(isDefault: false);
      }
    }
  }
} 