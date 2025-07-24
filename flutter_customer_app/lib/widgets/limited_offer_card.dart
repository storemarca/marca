import 'package:flutter/material.dart';
import 'dart:async';
import '../models/limited_offer.dart';

class LimitedOfferCard extends StatefulWidget {
  final LimitedOffer offer;
  final VoidCallback? onAddToCart;

  const LimitedOfferCard({
    Key? key,
    required this.offer,
    this.onAddToCart,
  }) : super(key: key);

  @override
  State<LimitedOfferCard> createState() => _LimitedOfferCardState();
}

class _LimitedOfferCardState extends State<LimitedOfferCard> {
  late Timer _timer;
  late String _remainingTime;
  
  @override
  void initState() {
    super.initState();
    _remainingTime = widget.offer.remainingTimeText;
    
    // Update remaining time every second
    _timer = Timer.periodic(const Duration(seconds: 1), (_) {
      if (mounted) {
        setState(() {
          _remainingTime = widget.offer.remainingTimeText;
        });
      }
    });
  }
  
  @override
  void dispose() {
    _timer.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Card(
      elevation: 4,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
        side: BorderSide(
          color: Theme.of(context).primaryColor,
          width: 2,
        ),
      ),
      child: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Icon(
                  Icons.local_offer,
                  color: Theme.of(context).primaryColor,
                ),
                const SizedBox(width: 8),
                Text(
                  'عرض محدود!',
                  style: TextStyle(
                    color: Theme.of(context).primaryColor,
                    fontWeight: FontWeight.bold,
                    fontSize: 18,
                  ),
                ),
                const Spacer(),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 8,
                    vertical: 4,
                  ),
                  decoration: BoxDecoration(
                    color: Colors.red,
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Text(
                    widget.offer.formattedDiscountPercentage,
                    style: const TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 8),
            Row(
              children: [
                Text(
                  'السعر الأصلي: ',
                  style: TextStyle(
                    color: Colors.grey[600],
                  ),
                ),
                Text(
                  widget.offer.formattedOriginalPrice,
                  style: const TextStyle(
                    decoration: TextDecoration.lineThrough,
                    color: Colors.grey,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 4),
            Row(
              children: [
                Text(
                  'سعر العرض: ',
                  style: TextStyle(
                    color: Colors.grey[600],
                  ),
                ),
                Text(
                  widget.offer.formattedOfferPrice,
                  style: const TextStyle(
                    fontWeight: FontWeight.bold,
                    color: Colors.green,
                    fontSize: 18,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 8),
            const Divider(),
            const SizedBox(height: 8),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      _remainingTime,
                      style: const TextStyle(
                        fontWeight: FontWeight.bold,
                        color: Colors.red,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'متبقي ${widget.offer.remainingQuantity} قطعة',
                      style: TextStyle(
                        color: widget.offer.remainingQuantity < 5
                            ? Colors.red
                            : Colors.grey[600],
                      ),
                    ),
                  ],
                ),
                if (widget.onAddToCart != null)
                  ElevatedButton.icon(
                    onPressed: widget.offer.isAvailable
                        ? widget.onAddToCart
                        : null,
                    icon: const Icon(Icons.shopping_cart),
                    label: const Text('أضف للسلة'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.green,
                      foregroundColor: Colors.white,
                    ),
                  ),
              ],
            ),
          ],
        ),
      ),
    );
  }
} 