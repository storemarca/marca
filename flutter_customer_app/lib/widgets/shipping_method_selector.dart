import 'package:flutter/material.dart';
import '../models/shipping_cost.dart';

class ShippingMethodSelector extends StatelessWidget {
  final List<ShippingRate> rates;
  final ShippingRate? selectedRate;
  final Function(ShippingRate) onRateSelected;
  final String zoneName;

  const ShippingMethodSelector({
    Key? key,
    required this.rates,
    required this.selectedRate,
    required this.onRateSelected,
    required this.zoneName,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'تكلفة الشحن إلى $zoneName',
          style: const TextStyle(
            fontWeight: FontWeight.bold,
            fontSize: 16,
          ),
        ),
        const SizedBox(height: 16),
        ListView.builder(
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          itemCount: rates.length,
          itemBuilder: (context, index) {
            final rate = rates[index];
            final cost = rate.baseCost;
            final formattedCost = cost > 0 
                ? '${cost.toStringAsFixed(2)} ريال'
                : 'مجاني';
            
            return Card(
              margin: const EdgeInsets.only(bottom: 8),
              elevation: selectedRate?.id == rate.id ? 4 : 1,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(8),
                side: BorderSide(
                  color: selectedRate?.id == rate.id
                      ? Theme.of(context).primaryColor
                      : Colors.transparent,
                  width: 2,
                ),
              ),
              child: InkWell(
                onTap: () => onRateSelected(rate),
                borderRadius: BorderRadius.circular(8),
                child: Padding(
                  padding: const EdgeInsets.all(12.0),
                  child: Row(
                    children: [
                      Container(
                        width: 40,
                        height: 40,
                        decoration: BoxDecoration(
                          color: Colors.grey[200],
                          borderRadius: BorderRadius.circular(4),
                        ),
                        child: const Icon(Icons.local_shipping),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'خدمة التوصيل',
                              style: TextStyle(
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              'توصيل إلى $zoneName',
                              style: TextStyle(
                                color: Colors.grey[600],
                                fontSize: 12,
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(width: 8),
                      Text(
                        formattedCost,
                        style: TextStyle(
                          fontWeight: FontWeight.bold,
                          color: cost > 0 ? Colors.black : Colors.green,
                        ),
                      ),
                      const SizedBox(width: 8),
                      Radio<String>(
                        value: rate.id,
                        groupValue: selectedRate?.id,
                        onChanged: (_) => onRateSelected(rate),
                      ),
                    ],
                  ),
                ),
              ),
            );
          },
        ),
      ],
    );
  }
} 