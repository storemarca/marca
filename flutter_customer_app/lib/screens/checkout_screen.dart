import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../models/address.dart';
import '../models/shipping_cost.dart';
import '../services/address_service.dart';
import '../services/cart_service.dart';
import '../services/order_service.dart';
import '../services/shipping_service.dart';
import '../widgets/shipping_method_selector.dart';

class CheckoutScreen extends StatefulWidget {
  const CheckoutScreen({Key? key}) : super(key: key);

  @override
  State<CheckoutScreen> createState() => _CheckoutScreenState();
}

class _CheckoutScreenState extends State<CheckoutScreen> {
  Address? _selectedAddress;
  ShippingRate? _selectedRate;
  bool _isLoading = false;
  String _paymentMethod = 'card';
  bool _cashOnDelivery = false;
  
  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    setState(() {
      _isLoading = true;
    });
    
    try {
      // Load addresses
      final addressService = Provider.of<AddressService>(context, listen: false);
      await addressService.getAddresses();
      
      if (addressService.addresses.isNotEmpty) {
        setState(() {
          _selectedAddress = addressService.addresses.first;
        });
        
        // Load shipping data
        final shippingService = Provider.of<ShippingService>(context, listen: false);
        await shippingService.loadShippingData();
        
        // Find zone for the selected address
        if (_selectedAddress != null) {
          final zone = shippingService.findZoneByCity(
            _selectedAddress!.country,
            _selectedAddress!.city,
          );
          
          if (zone != null && zone.id.isNotEmpty) {
            // Get applicable rates for the zone
            final cartService = Provider.of<CartService>(context, listen: false);
            final rates = shippingService.getApplicableRates(zone.id, cartService.subtotal);
            
            if (rates.isNotEmpty) {
              setState(() {
                _selectedRate = rates.first;
              });
            }
          }
        }
      }
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  double _calculateTotal() {
    final cartService = Provider.of<CartService>(context, listen: false);
    double subtotal = cartService.subtotal;
    double shippingCost = 0;
    double cashOnDeliveryCost = 0;
    
    if (_selectedRate != null) {
      final shippingService = Provider.of<ShippingService>(context, listen: false);
      final cartService = Provider.of<CartService>(context, listen: false);
      
      // Get product IDs for checking free shipping eligibility
      final List<String> productIds = cartService.items.map((item) => item.product.id.toString()).toList();
      
      // Calculate total weight
      final double totalWeight = cartService.items.fold(
        0, 
        (sum, item) => sum + (item.product.weight * item.quantity)
      );
      
      shippingCost = shippingService.calculateShippingCost(
        zoneId: _selectedRate!.zoneId,
        methodId: _selectedRate!.methodId,
        orderValue: subtotal,
        orderWeight: totalWeight,
        productIds: productIds,
      );
    }
    
    return subtotal + shippingCost + cashOnDeliveryCost;
  }

  void _placeOrder() async {
    if (_selectedAddress == null || _selectedRate == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('الرجاء اختيار عنوان التوصيل وطريقة الشحن')),
      );
      return;
    }
    
    setState(() {
      _isLoading = true;
    });
    
    try {
      final orderService = Provider.of<OrderService>(context, listen: false);
      final cartService = Provider.of<CartService>(context, listen: false);
      
      final orderId = await orderService.createOrder(
        addressId: _selectedAddress!.id,
        shippingMethodId: _selectedRate!.methodId,
        shippingZoneId: _selectedRate!.zoneId,
        paymentMethod: _paymentMethod,
        cashOnDelivery: _cashOnDelivery,
        items: cartService.items,
        subtotal: cartService.subtotal,
        shippingCost: _selectedRate!.baseCost,
        total: _calculateTotal(),
      );
      
      if (orderId != null) {
        // Clear cart after successful order
        await cartService.clearCart();
        
        // Navigate to order success screen
        if (mounted) {
          Navigator.pushReplacementNamed(
            context, 
            '/order-success',
            arguments: {'orderId': orderId},
          );
        }
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('فشل إنشاء الطلب: $e')),
      );
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final addressService = Provider.of<AddressService>(context);
    final cartService = Provider.of<CartService>(context);
    final shippingService = Provider.of<ShippingService>(context);
    
    // Get applicable rates for the selected address
    List<ShippingRate> applicableRates = [];
    String zoneName = '';
    
    if (_selectedAddress != null) {
      final zone = shippingService.findZoneByCity(
        _selectedAddress!.country,
        _selectedAddress!.city,
      );
      
      if (zone != null && zone.id.isNotEmpty) {
        zoneName = zone.name;
        applicableRates = shippingService.getApplicableRates(zone.id, cartService.subtotal);
      }
    }
    
    return Scaffold(
      appBar: AppBar(
        title: const Text('إتمام الطلب'),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Addresses section
                  const Text(
                    'عنوان التوصيل',
                    style: TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 18,
                    ),
                  ),
                  const SizedBox(height: 8),
                  if (addressService.addresses.isEmpty)
                    const Text('لا توجد عناوين مسجلة. الرجاء إضافة عنوان أولاً.')
                  else
                    ListView.builder(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      itemCount: addressService.addresses.length,
                      itemBuilder: (context, index) {
                        final address = addressService.addresses[index];
                        return Card(
                          margin: const EdgeInsets.only(bottom: 8),
                          elevation: _selectedAddress?.id == address.id ? 4 : 1,
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(8),
                            side: BorderSide(
                              color: _selectedAddress?.id == address.id
                                  ? Theme.of(context).primaryColor
                                  : Colors.transparent,
                              width: 2,
                            ),
                          ),
                          child: InkWell(
                            onTap: () {
                              setState(() {
                                _selectedAddress = address;
                                _selectedRate = null;
                              });
                            },
                            borderRadius: BorderRadius.circular(8),
                            child: Padding(
                              padding: const EdgeInsets.all(12),
                              child: Row(
                                children: [
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Text(
                                          address.name,
                                          style: const TextStyle(
                                            fontWeight: FontWeight.bold,
                                          ),
                                        ),
                                        const SizedBox(height: 4),
                                        Text(
                                          '${address.address}, ${address.city}, ${address.country}',
                                          style: TextStyle(
                                            color: Colors.grey[600],
                                          ),
                                        ),
                                        const SizedBox(height: 4),
                                        Text(
                                          'هاتف: ${address.phone}',
                                          style: TextStyle(
                                            color: Colors.grey[600],
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                  Radio<String>(
                                    value: address.id,
                                    groupValue: _selectedAddress?.id,
                                    onChanged: (_) {
                                      setState(() {
                                        _selectedAddress = address;
                                        _selectedRate = null;
                                      });
                                    },
                                  ),
                                ],
                              ),
                            ),
                          ),
                        );
                      },
                    ),
                  
                  TextButton.icon(
                    onPressed: () {
                      Navigator.pushNamed(context, '/address-form').then((_) => _loadData());
                    },
                    icon: const Icon(Icons.add),
                    label: const Text('إضافة عنوان جديد'),
                  ),
                  
                  const SizedBox(height: 24),
                  
                  // Shipping methods section
                  if (_selectedAddress != null && applicableRates.isNotEmpty)
                    ShippingMethodSelector(
                      rates: applicableRates,
                      selectedRate: _selectedRate,
                      onRateSelected: (rate) {
                        setState(() {
                          _selectedRate = rate;
                        });
                      },
                      zoneName: zoneName,
                    )
                  else if (_selectedAddress != null)
                    const Text('لا توجد خدمات شحن متاحة لهذا العنوان.'),
                  
                  const SizedBox(height: 24),
                  
                  // Payment method section
                  const Text(
                    'طريقة الدفع',
                    style: TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 18,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Card(
                    child: Column(
                      children: [
                        RadioListTile<String>(
                          title: const Text('بطاقة ائتمان'),
                          value: 'card',
                          groupValue: _paymentMethod,
                          onChanged: (value) {
                            setState(() {
                              _paymentMethod = value!;
                              _cashOnDelivery = false;
                            });
                          },
                        ),
                        RadioListTile<String>(
                          title: const Text('الدفع عند الاستلام'),
                          value: 'cash',
                          groupValue: _paymentMethod,
                          onChanged: (value) {
                            setState(() {
                              _paymentMethod = value!;
                              _cashOnDelivery = true;
                            });
                          },
                        ),
                      ],
                    ),
                  ),
                  
                  const SizedBox(height: 24),
                  
                  // Order summary
                  const Text(
                    'ملخص الطلب',
                    style: TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 18,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Card(
                    child: Padding(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        children: [
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              const Text('المجموع الفرعي'),
                              Text('${cartService.subtotal.toStringAsFixed(2)} ريال'),
                            ],
                          ),
                          const SizedBox(height: 8),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              const Text('تكلفة الشحن'),
                              Text(
                                _selectedRate != null
                                    ? '${_selectedRate!.baseCost.toStringAsFixed(2)} ريال'
                                    : '0.00 ريال',
                              ),
                            ],
                          ),
                          if (_cashOnDelivery) ...[
                            const SizedBox(height: 8),
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: const [
                                Text('رسوم الدفع عند الاستلام'),
                                Text('0.00 ريال'),
                              ],
                            ),
                          ],
                          const Divider(height: 24),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              const Text(
                                'الإجمالي',
                                style: TextStyle(
                                  fontWeight: FontWeight.bold,
                                  fontSize: 16,
                                ),
                              ),
                              Text(
                                '${_calculateTotal().toStringAsFixed(2)} ريال',
                                style: const TextStyle(
                                  fontWeight: FontWeight.bold,
                                  fontSize: 16,
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ),
                  
                  const SizedBox(height: 24),
                  
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      onPressed: _selectedAddress != null && _selectedRate != null
                          ? _placeOrder
                          : null,
                      style: ElevatedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 16),
                      ),
                      child: const Text(
                        'إتمام الطلب',
                        style: TextStyle(
                          fontSize: 16,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
    );
  }
} 