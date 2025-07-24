import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../models/limited_offer.dart';
import '../services/limited_offer_service.dart';
import '../services/cart_service.dart';
import '../services/product_service.dart';
import '../widgets/limited_offer_card.dart';

class LimitedOffersScreen extends StatefulWidget {
  const LimitedOffersScreen({Key? key}) : super(key: key);

  @override
  State<LimitedOffersScreen> createState() => _LimitedOffersScreenState();
}

class _LimitedOffersScreenState extends State<LimitedOffersScreen> {
  bool _isLoading = false;
  bool _isAddingToCart = false;
  String? _addingToCartOfferId;

  @override
  void initState() {
    super.initState();
    _loadOffers();
  }

  Future<void> _loadOffers() async {
    setState(() {
      _isLoading = true;
    });

    try {
      await Provider.of<LimitedOfferService>(context, listen: false).getLimitedOffers();
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  Future<void> _addToCart(LimitedOffer offer) async {
    setState(() {
      _isAddingToCart = true;
      _addingToCartOfferId = offer.id;
    });

    try {
      final productService = Provider.of<ProductService>(context, listen: false);
      final cartService = Provider.of<CartService>(context, listen: false);
      
      // Fetch product details
      final product = await productService.getProductById(offer.productId);
      
      if (product != null) {
        // Add to cart with offer details
        await cartService.addToCart(
          product: product,
          quantity: 1,
          offer: offer,
        );
        
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('تمت إضافة المنتج إلى السلة')),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('لم يتم العثور على المنتج')),
        );
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('فشل إضافة المنتج إلى السلة: $e')),
      );
    } finally {
      setState(() {
        _isAddingToCart = false;
        _addingToCartOfferId = null;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final offerService = Provider.of<LimitedOfferService>(context);
    final activeOffers = offerService.activeOffers;

    return Scaffold(
      appBar: AppBar(
        title: const Text('العروض المحدودة'),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _loadOffers,
              child: activeOffers.isEmpty
                  ? Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(
                            Icons.local_offer_outlined,
                            size: 64,
                            color: Colors.grey[400],
                          ),
                          const SizedBox(height: 16),
                          Text(
                            'لا توجد عروض محدودة حالياً',
                            style: TextStyle(
                              fontSize: 18,
                              color: Colors.grey[600],
                            ),
                          ),
                          const SizedBox(height: 24),
                          ElevatedButton(
                            onPressed: _loadOffers,
                            child: const Text('تحديث'),
                          ),
                        ],
                      ),
                    )
                  : ListView.builder(
                      padding: const EdgeInsets.all(16),
                      itemCount: activeOffers.length,
                      itemBuilder: (context, index) {
                        final offer = activeOffers[index];
                        return Padding(
                          padding: const EdgeInsets.only(bottom: 16),
                          child: LimitedOfferCard(
                            offer: offer,
                            onAddToCart: _isAddingToCart && _addingToCartOfferId == offer.id
                                ? null
                                : () => _addToCart(offer),
                          ),
                        );
                      },
                    ),
            ),
    );
  }
} 