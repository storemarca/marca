import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../services/auth_service.dart';
import '../utils/app_extensions.dart';

class ProfileScreen extends StatelessWidget {
  const ProfileScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final authService = Provider.of<AuthService>(context);
    final user = authService.currentUser;
    final isRtl = context.isRtl;

    return Scaffold(
      appBar: AppBar(
        title: Text('profile.my_profile'.tr(context)),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // User profile header
            Center(
              child: Column(
                children: [
                  CircleAvatar(
                    radius: 50,
                    backgroundColor: Theme.of(context).primaryColor,
                    child: user?.profileImage != null
                        ? ClipRRect(
                            borderRadius: BorderRadius.circular(50),
                            child: Image.network(
                              user!.profileImage!,
                              width: 100,
                              height: 100,
                              fit: BoxFit.cover,
                              errorBuilder: (_, __, ___) => const Icon(
                                Icons.person,
                                size: 50,
                                color: Colors.white,
                              ),
                            ),
                          )
                        : const Icon(
                            Icons.person,
                            size: 50,
                            color: Colors.white,
                          ),
                  ),
                  const SizedBox(height: 16),
                  Text(
                    user?.name ?? 'Guest',
                    style: Theme.of(context).textTheme.headlineMedium,
                  ),
                  if (user?.email != null) ...[
                    const SizedBox(height: 4),
                    Text(
                      user!.email,
                      style: Theme.of(context).textTheme.bodyMedium,
                    ),
                  ],
                ],
              ),
            ),
            const SizedBox(height: 32),

            // Personal information section
            Text(
              'profile.personal_info'.tr(context),
              style: Theme.of(context).textTheme.titleLarge,
            ),
            const SizedBox(height: 8),
            Card(
              child: Column(
                children: [
                  ListTile(
                    leading: const Icon(Icons.edit),
                    title: Text('profile.edit_profile'.tr(context)),
                    trailing: Icon(
                      isRtl ? Icons.chevron_left : Icons.chevron_right,
                    ),
                    onTap: () {
                      // Navigate to edit profile screen
                    },
                  ),
                  const Divider(height: 1),
                  ListTile(
                    leading: const Icon(Icons.location_on),
                    title: Text('address.addresses'.tr(context)),
                    trailing: Icon(
                      isRtl ? Icons.chevron_left : Icons.chevron_right,
                    ),
                    onTap: () {
                      Navigator.pushNamed(context, '/addresses');
                    },
                  ),
                  const Divider(height: 1),
                  ListTile(
                    leading: const Icon(Icons.payment),
                    title: Text('profile.payment_methods'.tr(context)),
                    trailing: Icon(
                      isRtl ? Icons.chevron_left : Icons.chevron_right,
                    ),
                    onTap: () {
                      // Navigate to payment methods screen
                    },
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Orders section
            Text(
              'orders.my_orders'.tr(context),
              style: Theme.of(context).textTheme.titleLarge,
            ),
            const SizedBox(height: 8),
            Card(
              child: Column(
                children: [
                  ListTile(
                    leading: const Icon(Icons.shopping_bag),
                    title: Text('orders.my_orders'.tr(context)),
                    trailing: Icon(
                      isRtl ? Icons.chevron_left : Icons.chevron_right,
                    ),
                    onTap: () {
                      Navigator.pushNamed(context, '/orders');
                    },
                  ),
                  const Divider(height: 1),
                  ListTile(
                    leading: const Icon(Icons.favorite),
                    title: Text('profile.wishlist'.tr(context)),
                    trailing: Icon(
                      isRtl ? Icons.chevron_left : Icons.chevron_right,
                    ),
                    onTap: () {
                      // Navigate to wishlist screen
                    },
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Loyalty and affiliate section
            Text(
              'loyalty.loyalty_points'.tr(context),
              style: Theme.of(context).textTheme.titleLarge,
            ),
            const SizedBox(height: 8),
            Card(
              child: Column(
                children: [
                  ListTile(
                    leading: const Icon(Icons.star),
                    title: Text('loyalty.loyalty_points'.tr(context)),
                    trailing: Icon(
                      isRtl ? Icons.chevron_left : Icons.chevron_right,
                    ),
                    onTap: () {
                      Navigator.pushNamed(context, '/loyalty-points');
                    },
                  ),
                  const Divider(height: 1),
                  ListTile(
                    leading: const Icon(Icons.card_giftcard),
                    title: Text('loyalty.rewards'.tr(context)),
                    trailing: Icon(
                      isRtl ? Icons.chevron_left : Icons.chevron_right,
                    ),
                    onTap: () {
                      Navigator.pushNamed(context, '/rewards');
                    },
                  ),
                  const Divider(height: 1),
                  ListTile(
                    leading: const Icon(Icons.link),
                    title: Text('affiliate.affiliate_marketing'.tr(context)),
                    trailing: Icon(
                      isRtl ? Icons.chevron_left : Icons.chevron_right,
                    ),
                    onTap: () {
                      Navigator.pushNamed(context, '/affiliate-links');
                    },
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Settings section
            Text(
              'app.settings'.tr(context),
              style: Theme.of(context).textTheme.titleLarge,
            ),
            const SizedBox(height: 8),
            Card(
              child: Column(
                children: [
                  ListTile(
                    leading: const Icon(Icons.language),
                    title: Text('app.language'.tr(context)),
                    trailing: Icon(
                      isRtl ? Icons.chevron_left : Icons.chevron_right,
                    ),
                    onTap: () {
                      Navigator.pushNamed(context, '/language');
                    },
                  ),
                  const Divider(height: 1),
                  ListTile(
                    leading: const Icon(Icons.notifications),
                    title: Text('app.notifications'.tr(context)),
                    trailing: Icon(
                      isRtl ? Icons.chevron_left : Icons.chevron_right,
                    ),
                    onTap: () {
                      // Navigate to notifications settings
                    },
                  ),
                  const Divider(height: 1),
                  ListTile(
                    leading: const Icon(Icons.help),
                    title: Text('profile.help'.tr(context)),
                    trailing: Icon(
                      isRtl ? Icons.chevron_left : Icons.chevron_right,
                    ),
                    onTap: () {
                      // Navigate to help screen
                    },
                  ),
                  const Divider(height: 1),
                  ListTile(
                    leading: const Icon(Icons.info),
                    title: Text('profile.about'.tr(context)),
                    trailing: Icon(
                      isRtl ? Icons.chevron_left : Icons.chevron_right,
                    ),
                    onTap: () {
                      // Navigate to about screen
                    },
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Logout button
            SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                onPressed: () {
                  // Show logout confirmation dialog
                  showDialog(
                    context: context,
                    builder: (context) => AlertDialog(
                      title: Text('auth.logout'.tr(context)),
                      content: Text('common.confirm'.tr(context)),
                      actions: [
                        TextButton(
                          onPressed: () => Navigator.pop(context),
                          child: Text('common.cancel'.tr(context)),
                        ),
                        TextButton(
                          onPressed: () {
                            Navigator.pop(context);
                            authService.logout();
                            Navigator.pushReplacementNamed(context, '/login');
                          },
                          child: Text('common.yes'.tr(context)),
                        ),
                      ],
                    ),
                  );
                },
                icon: const Icon(Icons.logout),
                label: Text('auth.logout'.tr(context)),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.red,
                  padding: const EdgeInsets.symmetric(vertical: 12),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
} 