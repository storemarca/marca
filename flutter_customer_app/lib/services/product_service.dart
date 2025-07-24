import 'package:flutter/foundation.dart';
import 'api_service.dart';
import '../models/product.dart';
import '../models/category.dart' as app_models;

class ProductService extends ChangeNotifier {
  final ApiService _apiService = ApiService();
  
  List<Product> _products = [];
  List<app_models.Category> _categories = [];
  Product? _selectedProduct;
  bool _isLoading = false;
  String? _error;
  int _currentPage = 1;
  int _totalPages = 1;
  
  // Getters
  List<Product> get products => _products;
  List<app_models.Category> get categories => _categories;
  Product? get selectedProduct => _selectedProduct;
  bool get isLoading => _isLoading;
  String? get error => _error;
  bool get hasMorePages => _currentPage < _totalPages;
  
  // Get products with pagination
  Future<bool> getProducts({
    int? categoryId,
    String? search,
    String sortBy = 'created_at',
    String sortDirection = 'desc',
    int page = 1,
    bool refresh = false,
  }) async {
    if (refresh) {
      _products = [];
      _currentPage = 1;
    } else {
      _currentPage = page;
    }
    
    _isLoading = true;
    _error = null;
    notifyListeners();
    
    try {
      // Build query parameters
      final Map<String, dynamic> queryParams = {
        'page': _currentPage.toString(),
        'sort_by': sortBy,
        'sort_direction': sortDirection,
      };
      
      if (categoryId != null) {
        queryParams['category_id'] = categoryId.toString();
      }
      
      if (search != null && search.isNotEmpty) {
        queryParams['search'] = search;
      }
      
      // Convert query parameters to string
      final queryString = queryParams.entries
          .map((e) => '${e.key}=${e.value}')
          .join('&');
      
      final response = await _apiService.get('products?$queryString', requiresAuth: false);
      
      _isLoading = false;
      
      if (response['success'] == true) {
        final data = response['data'];
        
        // Parse pagination data
        _currentPage = data['current_page'] ?? 1;
        _totalPages = data['last_page'] ?? 1;
        
        // Parse products
        final List<dynamic> productsJson = data['data'] ?? [];
        final List<Product> newProducts = productsJson
            .map((json) => Product.fromJson(json))
            .toList();
        
        if (refresh || page == 1) {
          _products = newProducts;
        } else {
          _products.addAll(newProducts);
        }
        
        notifyListeners();
        return true;
      } else {
        _error = response['message'] ?? 'Failed to load products';
        notifyListeners();
        return false;
      }
    } catch (e) {
      _isLoading = false;
      _error = 'Failed to load products: $e';
      notifyListeners();
      return false;
    }
  }
  
  // Load more products
  Future<bool> loadMoreProducts() async {
    if (!hasMorePages || _isLoading) return false;
    return await getProducts(page: _currentPage + 1);
  }
  
  // Get product details
  Future<bool> getProductDetails(String slug) async {
    _isLoading = true;
    _error = null;
    notifyListeners();
    
    try {
      final response = await _apiService.get('products/$slug', requiresAuth: false);
      
      _isLoading = false;
      
      if (response['success'] == true) {
        final data = response['data'];
        _selectedProduct = Product.fromJson(data['product']);
        
        // Parse related products
        final List<dynamic> relatedJson = data['related_products'] ?? [];
        final List<Product> relatedProducts = relatedJson
            .map((json) => Product.fromJson(json))
            .toList();
        
        _selectedProduct!.relatedProducts = relatedProducts;
        
        notifyListeners();
        return true;
      } else {
        _error = response['message'] ?? 'Failed to load product details';
        notifyListeners();
        return false;
      }
    } catch (e) {
      _isLoading = false;
      _error = 'Failed to load product details: $e';
      notifyListeners();
      return false;
    }
  }
  
  // Get product by ID
  Future<Product?> getProductById(String id) async {
    try {
      final response = await _apiService.get('products/id/$id');
      
      if (response['success'] == true) {
        final data = response['data'];
        return Product.fromJson(data);
      } else {
        _error = response['message'] ?? 'Failed to load product';
        notifyListeners();
        return null;
      }
    } catch (e) {
      _error = 'Failed to load product: $e';
      notifyListeners();
      return null;
    }
  }
  
  // Get categories
  Future<bool> getCategories() async {
    _isLoading = true;
    _error = null;
    notifyListeners();
    
    try {
      final response = await _apiService.get('categories', requiresAuth: false);
      
      _isLoading = false;
      
      if (response['success'] == true) {
        final List<dynamic> categoriesJson = response['data'] ?? [];
        _categories = categoriesJson
            .map((json) => app_models.Category.fromJson(json))
            .toList();
        
        notifyListeners();
        return true;
      } else {
        _error = response['message'] ?? 'Failed to load categories';
        notifyListeners();
        return false;
      }
    } catch (e) {
      _isLoading = false;
      _error = 'Failed to load categories: $e';
      notifyListeners();
      return false;
    }
  }
  
  // Get category details
  Future<List<Product>> getCategoryProducts(String slug) async {
    _isLoading = true;
    _error = null;
    notifyListeners();
    
    try {
      final response = await _apiService.get('categories/$slug', requiresAuth: false);
      
      _isLoading = false;
      
      if (response['success'] == true) {
        final data = response['data'];
        
        // Parse products
        final List<dynamic> productsJson = data['products'] ?? [];
        final List<Product> categoryProducts = productsJson
            .map((json) => Product.fromJson(json))
            .toList();
        
        notifyListeners();
        return categoryProducts;
      } else {
        _error = response['message'] ?? 'Failed to load category products';
        notifyListeners();
        return [];
      }
    } catch (e) {
      _isLoading = false;
      _error = 'Failed to load category products: $e';
      notifyListeners();
      return [];
    }
  }
  
  // Reset error
  void resetError() {
    _error = null;
    notifyListeners();
  }
} 