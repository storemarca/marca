import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../models/loyalty_point.dart';
import '../services/loyalty_service.dart';
import '../widgets/loading_indicator.dart';
import 'rewards_screen.dart';

class LoyaltyPointsScreen extends StatefulWidget {
  const LoyaltyPointsScreen({Key? key}) : super(key: key);

  @override
  State<LoyaltyPointsScreen> createState() => _LoyaltyPointsScreenState();
}

class _LoyaltyPointsScreenState extends State<LoyaltyPointsScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _fetchLoyaltyPoints();
    });
  }

  Future<void> _fetchLoyaltyPoints() async {
    try {
      await Provider.of<LoyaltyService>(context, listen: false).getUserLoyalty();
    } catch (e) {
      // Error is handled in the LoyaltyService
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('نقاط الولاء'),
      ),
      body: Consumer<LoyaltyService>(
        builder: (context, loyaltyService, child) {
          if (loyaltyService.isLoading) {
            return const Center(child: LoadingIndicator());
          }

          if (loyaltyService.error != null) {
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
                    'فشل تحميل نقاط الولاء: ${loyaltyService.error}',
                    textAlign: TextAlign.center,
                    style: const TextStyle(color: Colors.red),
                  ),
                  const SizedBox(height: 16),
                  ElevatedButton(
                    onPressed: _fetchLoyaltyPoints,
                    child: const Text('إعادة المحاولة'),
                  ),
                ],
              ),
            );
          }

          if (loyaltyService.userLoyalty == null) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.star_border,
                    size: 64,
                    color: Colors.grey[400],
                  ),
                  const SizedBox(height: 16),
                  Text(
                    'لا توجد نقاط ولاء',
                    style: TextStyle(
                      fontSize: 18,
                      color: Colors.grey[600],
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'ابدأ التسوق لكسب نقاط الولاء',
                    style: TextStyle(
                      color: Colors.grey[500],
                    ),
                  ),
                ],
              ),
            );
          }

          final userLoyalty = loyaltyService.userLoyalty!;

          return RefreshIndicator(
            onRefresh: _fetchLoyaltyPoints,
            child: SingleChildScrollView(
              physics: const AlwaysScrollableScrollPhysics(),
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildPointsCard(userLoyalty),
                  const SizedBox(height: 24),
                  _buildTierProgressCard(userLoyalty),
                  const SizedBox(height: 24),
                  _buildRewardsCard(),
                  const SizedBox(height: 24),
                  const Text(
                    'سجل النقاط',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 8),
                  _buildTransactionsList(userLoyalty.transactions),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildPointsCard(UserLoyalty userLoyalty) {
    return Card(
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          children: [
            const Text(
              'إجمالي نقاط الولاء',
              style: TextStyle(
                fontSize: 16,
                color: Colors.grey,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              '${userLoyalty.totalPoints}',
              style: TextStyle(
                fontSize: 48,
                fontWeight: FontWeight.bold,
                color: Theme.of(context).primaryColor,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              'المستوى: ${userLoyalty.tier}',
              style: const TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.bold,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildTierProgressCard(UserLoyalty userLoyalty) {
    return Card(
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'التقدم نحو المستوى التالي',
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 16),
            LinearProgressIndicator(
              value: userLoyalty.pointsToNextTier > 0
                  ? 1 - (userLoyalty.pointsToNextTier / 
                      (userLoyalty.pointsToNextTier + userLoyalty.totalPoints))
                  : 1.0,
              minHeight: 8,
              backgroundColor: Colors.grey[300],
            ),
            const SizedBox(height: 8),
            Text(
              userLoyalty.pointsToNextTier > 0
                  ? 'تحتاج ${userLoyalty.pointsToNextTier} نقطة أخرى للوصول إلى المستوى التالي'
                  : 'لقد وصلت إلى أعلى مستوى!',
              style: TextStyle(
                color: Colors.grey[600],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildRewardsCard() {
    return Card(
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text(
                  'المكافآت المتاحة',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                TextButton(
                  onPressed: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => const RewardsScreen(),
                      ),
                    );
                  },
                  child: const Text('عرض الكل'),
                ),
              ],
            ),
            const SizedBox(height: 8),
            Consumer<LoyaltyService>(
              builder: (context, loyaltyService, child) {
                if (loyaltyService.rewards.isEmpty) {
                  return const Padding(
                    padding: EdgeInsets.symmetric(vertical: 16.0),
                    child: Center(
                      child: Text('لا توجد مكافآت متاحة حالياً'),
                    ),
                  );
                }

                final displayRewards = loyaltyService.rewards.length > 3
                    ? loyaltyService.rewards.sublist(0, 3)
                    : loyaltyService.rewards;

                return ListView.builder(
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  itemCount: displayRewards.length,
                  itemBuilder: (context, index) {
                    final reward = displayRewards[index];
                    return ListTile(
                      contentPadding: EdgeInsets.zero,
                      leading: ClipRRect(
                        borderRadius: BorderRadius.circular(4),
                        child: Image.network(
                          reward.imageUrl,
                          width: 50,
                          height: 50,
                          fit: BoxFit.cover,
                          errorBuilder: (_, __, ___) => Container(
                            width: 50,
                            height: 50,
                            color: Colors.grey[300],
                            child: const Icon(Icons.image_not_supported),
                          ),
                        ),
                      ),
                      title: Text(
                        reward.name,
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                      subtitle: Text(
                        '${reward.pointsRequired} نقطة',
                        style: TextStyle(
                          color: Theme.of(context).primaryColor,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      trailing: ElevatedButton(
                        onPressed: loyaltyService.userLoyalty != null &&
                                loyaltyService.userLoyalty!.totalPoints >=
                                    reward.pointsRequired
                            ? () => _redeemReward(reward)
                            : null,
                        style: ElevatedButton.styleFrom(
                          padding: const EdgeInsets.symmetric(horizontal: 16),
                        ),
                        child: const Text('استبدال'),
                      ),
                    );
                  },
                );
              },
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildTransactionsList(List<LoyaltyPoint> transactions) {
    if (transactions.isEmpty) {
      return const Padding(
        padding: EdgeInsets.symmetric(vertical: 16.0),
        child: Center(
          child: Text('لا توجد معاملات'),
        ),
      );
    }

    return ListView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: transactions.length,
      itemBuilder: (context, index) {
        final transaction = transactions[index];
        return Card(
          margin: const EdgeInsets.only(bottom: 8),
          child: ListTile(
            title: Text(transaction.description),
            subtitle: Text(transaction.formattedCreatedAt),
            trailing: Text(
              transaction.points > 0
                  ? '+${transaction.points}'
                  : '${transaction.points}',
              style: TextStyle(
                color: transaction.points > 0 ? Colors.green : Colors.red,
                fontWeight: FontWeight.bold,
                fontSize: 16,
              ),
            ),
          ),
        );
      },
    );
  }

  Future<void> _redeemReward(LoyaltyReward reward) async {
    final scaffoldMessenger = ScaffoldMessenger.of(context);
    
    // Show confirmation dialog
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('استبدال المكافأة'),
        content: Text(
          'هل أنت متأكد من استبدال ${reward.pointsRequired} نقطة للحصول على ${reward.name}؟',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('إلغاء'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text('استبدال'),
          ),
        ],
      ),
    );

    if (confirmed != true) return;

    try {
      final loyaltyService = Provider.of<LoyaltyService>(context, listen: false);
      final success = await loyaltyService.redeemReward(reward.id);
      
      if (success && mounted) {
        scaffoldMessenger.showSnackBar(
          SnackBar(
            content: Text('تم استبدال ${reward.name} بنجاح!'),
            backgroundColor: Colors.green,
          ),
        );
      }
    } catch (e) {
      scaffoldMessenger.showSnackBar(
        SnackBar(
          content: Text('فشل استبدال المكافأة: ${e.toString()}'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }
} 