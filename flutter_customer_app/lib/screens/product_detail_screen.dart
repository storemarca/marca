import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:carousel_slider/carousel_slider.dart';
import '../services/product_service.dart';
import '../services/cart_service.dart';
import '../widgets/product_card.dart';
import '../utils/social_share.dart';
import '../utils/toast_helper.dart';

class ProductDetailScreen extends StatefulWidget {
  final String productSlug;

  const ProductDetailScreen({
    super.key,
    required this.productSlug,
  });

  @override
  State<ProductDetailScreen> createState() => _ProductDetailScreenState();
}

class _ProductDetailScreenState extends State<ProductDetailScreen> {
  int _quantity = 1;
  int _currentImageIndex = 0;

  @override
  void initState() {
    super.initState();
    _loadProductDetails();
  }

  Future<void> _loadProductDetails() async {
    final productService = Provider.of<ProductService>(context, listen: false);
    await productService.getProductDetails(widget.productSlug);
  }

  void _incrementQuantity() {
    setState(() {
      _quantity++;
    });
  }

  void _decrementQuantity() {
    if (_quantity > 1) {
      setState(() {
        _quantity--;
      });
    }
  }

  Future<void> _addToCart() async {
    final productService = Provider.of<ProductService>(context, listen: false);
    final cartService = Provider.of<CartService>(context, listen: false);
    final product = productService.selectedProduct;
    
    if (product == null) return;
    
    try {
      final success = await cartService.addToCart(
        product: product,
        quantity: _quantity,
      );
      
      if (!mounted) return;
      
      if (success) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('${product.name} added to cart'),
            action: SnackBarAction(
              label: 'View Cart',
              onPressed: () {
                // Navigate to cart screen or set bottom navigation index to cart
                Navigator.of(context).pop();
              },
            ),
          ),
        );
      }
    } catch (e) {
      if (!mounted) return;
      
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Failed to add to cart: $e')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final productService = Provider.of<ProductService>(context);
    final product = productService.selectedProduct;
    
    return Scaffold(
      appBar: AppBar(
        title: Text(product?.name ?? 'Product Details'),
        actions: [
          if (product != null)
            IconButton(
              icon: const Icon(Icons.share),
              onPressed: () {
                SocialShare.shareProduct(
                  name: product.name,
                  description: product.description,
                  url: 'https://yourstore.com/products/${product.slug}',
                  imageUrl: product.images.isEmpty ? null : product.images[0],
                );
                ToastHelper.showSuccess('تمت مشاركة المنتج');
              },
            ),
        ],
      ),
      body: productService.isLoading
          ? const Center(child: CircularProgressIndicator())
          : product == null
              ? Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Text(
                        'Product not found',
                        style: Theme.of(context).textTheme.headlineMedium,
                      ),
                      const SizedBox(height: 16),
                      ElevatedButton(
                        onPressed: () => Navigator.of(context).pop(),
                        child: const Text('Go Back'),
                      ),
                    ],
                  ),
                )
              : Column(
                  children: [
                    // Product details
                    Expanded(
                      child: SingleChildScrollView(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            // Product images carousel
                            Stack(
                              children: [
                                CarouselSlider(
                                  options: CarouselOptions(
                                    height: 300,
                                    viewportFraction: 1.0,
                                    enlargeCenterPage: false,
                                    onPageChanged: (index, reason) {
                                      setState(() {
                                        _currentImageIndex = index;
                                      });
                                    },
                                  ),
                                  items: product.images.isEmpty
                                      ? [
                                          Container(
                                            color: Colors.grey[200],
                                            child: const Center(
                                              child: Icon(
                                                Icons.image_not_supported_outlined,
                                                size: 80,
                                                color: Colors.grey,
                                              ),
                                            ),
                                          ),
                                        ]
                                      : product.images.map((imageUrl) {
                                          return CachedNetworkImage(
                                            imageUrl: imageUrl,
                                            fit: BoxFit.cover,
                                            width: double.infinity,
                                            placeholder: (context, url) => Container(
                                              color: Colors.grey[200],
                                              child: const Center(
                                                child: CircularProgressIndicator(),
                                              ),
                                            ),
                                            errorWidget: (context, url, error) => Container(
                                              color: Colors.grey[200],
                                              child: const Center(
                                                child: Icon(
                                                  Icons.error_outline,
                                                  size: 80,
                                                  color: Colors.grey,
                                                ),
                                              ),
                                            ),
                                          );
                                        }).toList(),
                                ),
                                
                                // Image indicators
                                if (product.images.length > 1)
                                  Positioned(
                                    bottom: 16,
                                    left: 0,
                                    right: 0,
                                    child: Row(
                                      mainAxisAlignment: MainAxisAlignment.center,
                                      children: product.images.asMap().entries.map((entry) {
                                        return Container(
                                          width: 8,
                                          height: 8,
                                          margin: const EdgeInsets.symmetric(horizontal: 4),
                                          decoration: BoxDecoration(
                                            shape: BoxShape.circle,
                                            color: _currentImageIndex == entry.key
                                                ? Theme.of(context).primaryColor
                                                : Colors.white.withAlpha(128),
                                          ),
                                        );
                                      }).toList(),
                                    ),
                                  ),
                                
                                // Sale badge
                                if (product.isOnSale())
                                  Positioned(
                                    top: 16,
                                    left: 16,
                                    child: Container(
                                      padding: const EdgeInsets.symmetric(
                                        horizontal: 12,
                                        vertical: 6,
                                      ),
                                      decoration: BoxDecoration(
                                        color: Theme.of(context).colorScheme.error,
                                        borderRadius: BorderRadius.circular(4),
                                      ),
                                      child: Text(
                                        '-${product.getDiscountPercentage()}%',
                                        style: const TextStyle(
                                          color: Colors.white,
                                          fontWeight: FontWeight.bold,
                                        ),
                                      ),
                                    ),
                                  ),
                              ],
                            ),
                            
                            // Product info
                            Padding(
                              padding: const EdgeInsets.all(16),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  // Product name
                                  Text(
                                    product.name,
                                    style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                  
                                  const SizedBox(height: 8),
                                  
                                  // Product price
                                  Row(
                                    children: [
                                      Text(
                                        product.getFormattedPrice(),
                                        style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                                          color: Theme.of(context).primaryColor,
                                          fontWeight: FontWeight.bold,
                                        ),
                                      ),
                                      
                                      const SizedBox(width: 8),
                                      
                                      if (product.isOnSale())
                                        Text(
                                          '${product.getCurrentPrice()?.currencySymbol}${product.getCurrentPrice()?.price.toStringAsFixed(2)}',
                                          style: Theme.of(context).textTheme.titleMedium?.copyWith(
                                            decoration: TextDecoration.lineThrough,
                                            color: Colors.grey[600],
                                          ),
                                        ),
                                    ],
                                  ),
                                  
                                  const SizedBox(height: 16),
                                  
                                  // Stock status
                                  Container(
                                    padding: const EdgeInsets.symmetric(
                                      horizontal: 12,
                                      vertical: 6,
                                    ),
                                    decoration: BoxDecoration(
                                      color: product.isInStock()
                                          ? Colors.green[100]
                                          : Colors.red[100],
                                      borderRadius: BorderRadius.circular(4),
                                    ),
                                    child: Text(
                                      product.isInStock()
                                          ? 'In Stock'
                                          : 'Out of Stock',
                                      style: TextStyle(
                                        color: product.isInStock()
                                            ? Colors.green[800]
                                            : Colors.red[800],
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                  ),
                                  
                                  const SizedBox(height: 24),
                                  
                                  // Product description
                                  Text(
                                    'Description',
                                    style: Theme.of(context).textTheme.titleLarge?.copyWith(
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                  
                                  const SizedBox(height: 8),
                                  
                                  Text(
                                    product.description,
                                    style: Theme.of(context).textTheme.bodyLarge,
                                  ),
                                  
                                  const SizedBox(height: 24),
                                  
                                  // Related products
                                  if (product.relatedProducts.isNotEmpty) ...[
                                    Text(
                                      'Related Products',
                                      style: Theme.of(context).textTheme.titleLarge?.copyWith(
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                    
                                    const SizedBox(height: 16),
                                    
                                    SizedBox(
                                      height: 250,
                                      child: ListView.builder(
                                        scrollDirection: Axis.horizontal,
                                        itemCount: product.relatedProducts.length,
                                        itemBuilder: (context, index) {
                                          final relatedProduct = product.relatedProducts[index];
                                          
                                          return SizedBox(
                                            width: 180,
                                            child: Padding(
                                              padding: const EdgeInsets.only(right: 16),
                                              child: ProductCard(
                                                product: relatedProduct,
                                                onTap: () {
                                                  Navigator.of(context).pushReplacement(
                                                    MaterialPageRoute(
                                                      builder: (_) => ProductDetailScreen(
                                                        productSlug: relatedProduct.slug,
                                                      ),
                                                    ),
                                                  );
                                                },
                                              ),
                                            ),
                                          );
                                        },
                                      ),
                                    ),
                                  ],
                                ],
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                    
                    // Add to cart section
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Theme.of(context).cardColor,
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withAlpha(13),
                            blurRadius: 10,
                            offset: const Offset(0, -5),
                          ),
                        ],
                      ),
                      child: Row(
                        children: [
                          // Quantity selector
                          Container(
                            decoration: BoxDecoration(
                              border: Border.all(color: Colors.grey[300]!),
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: Row(
                              children: [
                                // Decrease button
                                IconButton(
                                  onPressed: _quantity > 1 ? _decrementQuantity : null,
                                  icon: const Icon(Icons.remove),
                                  color: _quantity > 1
                                      ? Theme.of(context).primaryColor
                                      : Colors.grey[400],
                                ),
                                
                                // Quantity
                                Text(
                                  '$_quantity',
                                  style: Theme.of(context).textTheme.titleLarge,
                                ),
                                
                                // Increase button
                                IconButton(
                                  onPressed: _incrementQuantity,
                                  icon: const Icon(Icons.add),
                                  color: Theme.of(context).primaryColor,
                                ),
                              ],
                            ),
                          ),
                          
                          const SizedBox(width: 16),
                          
                          // Add to cart button
                          Expanded(
                            child: ElevatedButton(
                              onPressed: product.isInStock() && !productService.isLoading
                                  ? _addToCart
                                  : null,
                              style: ElevatedButton.styleFrom(
                                padding: const EdgeInsets.symmetric(vertical: 16),
                              ),
                              child: productService.isLoading
                                  ? const SizedBox(
                                      width: 24,
                                      height: 24,
                                      child: CircularProgressIndicator(
                                        color: Colors.white,
                                        strokeWidth: 2,
                                      ),
                                    )
                                  : const Text('Add to Cart'),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
    );
  }
} 