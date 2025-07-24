import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../models/address.dart';
import '../services/address_service.dart';
import '../widgets/loading_indicator.dart';
import 'address_form_screen.dart';

class AddressListScreen extends StatefulWidget {
  final bool isSelecting;
  final Function(Address)? onAddressSelected;

  const AddressListScreen({
    Key? key,
    this.isSelecting = false,
    this.onAddressSelected,
  }) : super(key: key);

  @override
  State<AddressListScreen> createState() => _AddressListScreenState();
}

class _AddressListScreenState extends State<AddressListScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _fetchAddresses();
    });
  }

  Future<void> _fetchAddresses() async {
    try {
      await Provider.of<AddressService>(context, listen: false).getAddresses();
    } catch (e) {
      // Error is handled in the AddressService
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.isSelecting ? 'Select Address' : 'My Addresses'),
      ),
      body: Consumer<AddressService>(
        builder: (context, addressService, child) {
          if (addressService.isLoading) {
            return const Center(child: LoadingIndicator());
          }

          if (addressService.error != null) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(
                    Icons.error_outline,
                    color: Colors.red,
                    size: 48,
                  ),
                  const SizedBox(height: 16),
                  Text(
                    'Failed to load addresses: ${addressService.error}',
                    textAlign: TextAlign.center,
                    style: const TextStyle(color: Colors.red),
                  ),
                  const SizedBox(height: 16),
                  ElevatedButton(
                    onPressed: _fetchAddresses,
                    child: const Text('Retry'),
                  ),
                ],
              ),
            );
          }

          if (addressService.addresses.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.location_off,
                    size: 64,
                    color: Colors.grey[400],
                  ),
                  const SizedBox(height: 16),
                  Text(
                    'No addresses found',
                    style: TextStyle(
                      fontSize: 18,
                      color: Colors.grey[600],
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'Add a new address to continue',
                    style: TextStyle(
                      color: Colors.grey[500],
                    ),
                  ),
                  const SizedBox(height: 24),
                  ElevatedButton(
                    onPressed: () => _navigateToAddressForm(),
                    child: const Text('Add New Address'),
                  ),
                ],
              ),
            );
          }

          return RefreshIndicator(
            onRefresh: _fetchAddresses,
            child: ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: addressService.addresses.length,
              itemBuilder: (context, index) {
                final address = addressService.addresses[index];
                return _buildAddressCard(address);
              },
            ),
          );
        },
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () => _navigateToAddressForm(),
        child: const Icon(Icons.add),
      ),
    );
  }

  Widget _buildAddressCard(Address address) {
    return Card(
      margin: const EdgeInsets.only(bottom: 16),
      elevation: 2,
      child: InkWell(
        onTap: widget.isSelecting
            ? () {
                if (widget.onAddressSelected != null) {
                  widget.onAddressSelected!(address);
                  Navigator.pop(context);
                }
              }
            : null,
        child: Padding(
          padding: const EdgeInsets.all(16.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Text(
                    address.name,
                    style: const TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 16,
                    ),
                  ),
                  const SizedBox(width: 8),
                  if (address.isDefault)
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 8,
                        vertical: 2,
                      ),
                      decoration: BoxDecoration(
                        color: Theme.of(context).primaryColor,
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: const Text(
                        'Default',
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: 10,
                        ),
                      ),
                    ),
                ],
              ),
              const SizedBox(height: 8),
              Text(address.phone),
              const SizedBox(height: 4),
              Text(address.address),
              const SizedBox(height: 4),
              Text(
                '${address.city}, ${address.state} ${address.postalCode}',
              ),
              const SizedBox(height: 4),
              Text(address.country),
              if (!widget.isSelecting) ...[
                const SizedBox(height: 16),
                Row(
                  children: [
                    if (!address.isDefault)
                      Expanded(
                        child: OutlinedButton(
                          onPressed: () => _setAsDefault(address),
                          style: OutlinedButton.styleFrom(
                            padding: const EdgeInsets.symmetric(vertical: 12),
                          ),
                          child: const Text('Set as Default'),
                        ),
                      ),
                    if (!address.isDefault) const SizedBox(width: 8),
                    Expanded(
                      child: OutlinedButton(
                        onPressed: () => _navigateToAddressForm(address: address),
                        style: OutlinedButton.styleFrom(
                          padding: const EdgeInsets.symmetric(vertical: 12),
                        ),
                        child: const Text('Edit'),
                      ),
                    ),
                    const SizedBox(width: 8),
                    Expanded(
                      child: OutlinedButton(
                        onPressed: () => _deleteAddress(address),
                        style: OutlinedButton.styleFrom(
                          padding: const EdgeInsets.symmetric(vertical: 12),
                          foregroundColor: Colors.red,
                        ),
                        child: const Text('Delete'),
                      ),
                    ),
                  ],
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }

  Future<void> _navigateToAddressForm({Address? address}) async {
    final result = await Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => AddressFormScreen(address: address),
      ),
    );

    if (result == true) {
      _fetchAddresses();
    }
  }

  Future<void> _setAsDefault(Address address) async {
    final scaffoldMessenger = ScaffoldMessenger.of(context);
    
    try {
      final addressService = Provider.of<AddressService>(context, listen: false);
      await addressService.setDefaultAddress(address.id);
      
      scaffoldMessenger.showSnackBar(
        const SnackBar(
          content: Text('Default address updated successfully'),
          backgroundColor: Colors.green,
        ),
      );
    } catch (e) {
      scaffoldMessenger.showSnackBar(
        SnackBar(
          content: Text('Failed to update default address: ${e.toString()}'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  Future<void> _deleteAddress(Address address) async {
    final scaffoldMessenger = ScaffoldMessenger.of(context);
    
    // Show confirmation dialog
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Delete Address'),
        content: const Text(
          'Are you sure you want to delete this address? This action cannot be undone.',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Cancel'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            style: TextButton.styleFrom(foregroundColor: Colors.red),
            child: const Text('Delete'),
          ),
        ],
      ),
    );

    if (confirmed != true) return;

    try {
      final addressService = Provider.of<AddressService>(context, listen: false);
      await addressService.deleteAddress(address.id);
      
      scaffoldMessenger.showSnackBar(
        const SnackBar(
          content: Text('Address deleted successfully'),
          backgroundColor: Colors.green,
        ),
      );
    } catch (e) {
      scaffoldMessenger.showSnackBar(
        SnackBar(
          content: Text('Failed to delete address: ${e.toString()}'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }
} 