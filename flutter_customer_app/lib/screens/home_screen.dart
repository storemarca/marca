import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../services/auth_service.dart';
import '../services/product_service.dart';
import '../widgets/product_card.dart';
import '../widgets/category_chip.dart';
import 'product_detail_screen.dart';
import 'cart_screen.dart';
import 'profile_screen.dart';
import 'auth/login_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  int _currentIndex = 0;
  final _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadData() async {
    final productService = Provider.of<ProductService>(context, listen: false);
    
    // Load products and categories
    await productService.getProducts();
    await productService.getCategories();
  }

  Future<void> _logout() async {
    final authService = Provider.of<AuthService>(context, listen: false);
    
    final success = await authService.logout();
    
    if (!mounted) return;
    
    if (success) {
      // Navigate to login screen
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(builder: (_) => const LoginScreen()),
      );
    } else if (authService.error != null) {
      // Show error message
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(authService.error!)),
      );
      
      // Reset error
      authService.resetError();
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: _currentIndex == 0 
            ? const Text('Home')
            : _currentIndex == 1
                ? const Text('Cart')
                : const Text('Profile'),
        actions: [
          if (_currentIndex == 0)
            IconButton(
              icon: const Icon(Icons.search),
              onPressed: () {
                showSearch(
                  context: context,
                  delegate: ProductSearchDelegate(),
                );
              },
            ),
          if (_currentIndex == 2)
            IconButton(
              icon: const Icon(Icons.logout),
              onPressed: _logout,
            ),
        ],
      ),
      body: _currentIndex == 0
          ? _buildHomeContent()
          : _currentIndex == 1
              ? const CartScreen()
              : const ProfileScreen(),
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _currentIndex,
        onTap: (index) {
          setState(() {
            _currentIndex = index;
          });
        },
        items: const [
          BottomNavigationBarItem(
            icon: Icon(Icons.home_outlined),
            activeIcon: Icon(Icons.home),
            label: 'Home',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.shopping_cart_outlined),
            activeIcon: Icon(Icons.shopping_cart),
            label: 'Cart',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.person_outline),
            activeIcon: Icon(Icons.person),
            label: 'Profile',
          ),
        ],
      ),
    );
  }

  Widget _buildHomeContent() {
    final productService = Provider.of<ProductService>(context);
    
    if (productService.isLoading && productService.products.isEmpty) {
      return const Center(child: CircularProgressIndicator());
    }
    
    if (productService.error != null && productService.products.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Text(
              'Error loading products',
              style: Theme.of(context).textTheme.titleLarge,
            ),
            const SizedBox(height: 8),
            Text(productService.error!),
            const SizedBox(height: 16),
            ElevatedButton(
              onPressed: _loadData,
              child: const Text('Retry'),
            ),
          ],
        ),
      );
    }
    
    return RefreshIndicator(
      onRefresh: () => productService.getProducts(refresh: true),
      child: CustomScrollView(
        slivers: [
          // Categories
          SliverToBoxAdapter(
            child: _buildCategories(),
          ),
          
          // Featured products
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    'Featured Products',
                    style: Theme.of(context).textTheme.titleLarge,
                  ),
                  TextButton(
                    onPressed: () {
                      // Navigate to all products screen
                    },
                    child: const Text('See All'),
                  ),
                ],
              ),
            ),
          ),
          
          // Products grid
          SliverPadding(
            padding: const EdgeInsets.all(16),
            sliver: SliverGrid(
              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                crossAxisCount: 2,
                childAspectRatio: 0.7,
                crossAxisSpacing: 16,
                mainAxisSpacing: 16,
              ),
              delegate: SliverChildBuilderDelegate(
                (context, index) {
                  if (index == productService.products.length && 
                      productService.hasMorePages) {
                    // Load more products
                    Future.microtask(() => productService.loadMoreProducts());
                    return const Center(child: CircularProgressIndicator());
                  }
                  
                  if (index >= productService.products.length) {
                    return null;
                  }
                  
                  final product = productService.products[index];
                  
                  return ProductCard(
                    product: product,
                    onTap: () {
                      Navigator.of(context).push(
                        MaterialPageRoute(
                          builder: (_) => ProductDetailScreen(
                            productSlug: product.slug,
                          ),
                        ),
                      );
                    },
                  );
                },
                childCount: productService.hasMorePages
                    ? productService.products.length + 1
                    : productService.products.length,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCategories() {
    final productService = Provider.of<ProductService>(context);
    
    return SizedBox(
      height: 50,
      child: ListView.builder(
        padding: const EdgeInsets.symmetric(horizontal: 16),
        scrollDirection: Axis.horizontal,
        itemCount: productService.categories.length,
        itemBuilder: (context, index) {
          final category = productService.categories[index];
          
          return Padding(
            padding: const EdgeInsets.only(right: 8),
            child: CategoryChip(
              category: category,
              onTap: () {
                // Filter products by category
                productService.getProducts(
                  categoryId: category.id,
                  refresh: true,
                );
              },
            ),
          );
        },
      ),
    );
  }
}

class ProductSearchDelegate extends SearchDelegate {
  @override
  List<Widget> buildActions(BuildContext context) {
    return [
      IconButton(
        icon: const Icon(Icons.clear),
        onPressed: () {
          query = '';
        },
      ),
    ];
  }

  @override
  Widget buildLeading(BuildContext context) {
    return IconButton(
      icon: const Icon(Icons.arrow_back),
      onPressed: () {
        close(context, null);
      },
    );
  }

  @override
  Widget buildResults(BuildContext context) {
    if (query.length < 3) {
      return const Center(
        child: Text('Search term must be at least 3 characters long'),
      );
    }
    
    return FutureBuilder(
      future: Provider.of<ProductService>(context, listen: false)
          .getProducts(search: query, refresh: true),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return const Center(child: CircularProgressIndicator());
        }
        
        final productService = Provider.of<ProductService>(context);
        
        if (productService.products.isEmpty) {
          return const Center(
            child: Text('No products found'),
          );
        }
        
        return GridView.builder(
          padding: const EdgeInsets.all(16),
          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: 2,
            childAspectRatio: 0.7,
            crossAxisSpacing: 16,
            mainAxisSpacing: 16,
          ),
          itemCount: productService.products.length,
          itemBuilder: (context, index) {
            final product = productService.products[index];
            
            return ProductCard(
              product: product,
              onTap: () {
                Navigator.of(context).push(
                  MaterialPageRoute(
                    builder: (_) => ProductDetailScreen(
                      productSlug: product.slug,
                    ),
                  ),
                );
              },
            );
          },
        );
      },
    );
  }

  @override
  Widget buildSuggestions(BuildContext context) {
    return const Center(
      child: Text('Type to search products'),
    );
  }
} 