import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../models/loyalty_point.dart';
import '../services/loyalty_service.dart';
import '../widgets/loading_indicator.dart';

class RewardsScreen extends StatefulWidget {
  const RewardsScreen({Key? key}) : super(key: key);

  @override
  State<RewardsScreen> createState() => _RewardsScreenState();
}

class _RewardsScreenState extends State<RewardsScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _fetchRewards();
    });
  }

  Future<void> _fetchRewards() async {
    try {
      await Provider.of<LoyaltyService>(context, listen: false).getRewards();
    } catch (e) {
      // Error is handled in the LoyaltyService
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('المكافآت'),
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
                    'فشل تحميل المكافآت: ${loyaltyService.error}',
                    textAlign: TextAlign.center,
                    style: const TextStyle(color: Colors.red),
                  ),
                  const SizedBox(height: 16),
                  ElevatedButton(
                    onPressed: _fetchRewards,
                    child: const Text('إعادة المحاولة'),
                  ),
                ],
              ),
            );
          }

          if (loyaltyService.rewards.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.card_giftcard,
                    size: 64,
                    color: Colors.grey[400],
                  ),
                  const SizedBox(height: 16),
                  Text(
                    'لا توجد مكافآت متاحة حالياً',
                    style: TextStyle(
                      fontSize: 18,
                      color: Colors.grey[600],
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'تحقق مرة أخرى قريباً',
                    style: TextStyle(
                      color: Colors.grey[500],
                    ),
                  ),
                ],
              ),
            );
          }

          return RefreshIndicator(
            onRefresh: _fetchRewards,
            child: GridView.builder(
              padding: const EdgeInsets.all(16),
              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                crossAxisCount: 2,
                childAspectRatio: 0.75,
                crossAxisSpacing: 16,
                mainAxisSpacing: 16,
              ),
              itemCount: loyaltyService.rewards.length,
              itemBuilder: (context, index) {
                final reward = loyaltyService.rewards[index];
                return _buildRewardCard(reward);
              },
            ),
          );
        },
      ),
    );
  }

  Widget _buildRewardCard(LoyaltyReward reward) {
    final loyaltyService = Provider.of<LoyaltyService>(context, listen: false);
    final canRedeem = loyaltyService.userLoyalty != null &&
        loyaltyService.userLoyalty!.totalPoints >= reward.pointsRequired;

    return Card(
      clipBehavior: Clip.antiAlias,
      elevation: 2,
      child: InkWell(
        onTap: () => _showRewardDetails(reward),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Image
            Expanded(
              flex: 3,
              child: Stack(
                fit: StackFit.expand,
                children: [
                  Image.network(
                    reward.imageUrl,
                    fit: BoxFit.cover,
                    errorBuilder: (_, __, ___) => Container(
                      color: Colors.grey[300],
                      child: const Icon(
                        Icons.image_not_supported,
                        size: 48,
                        color: Colors.grey,
                      ),
                    ),
                  ),
                  if (!reward.isAvailable)
                    Container(
                      color: Colors.black54,
                      child: const Center(
                        child: Text(
                          'غير متوفر',
                          style: TextStyle(
                            color: Colors.white,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ),
                ],
              ),
            ),
            // Info
            Expanded(
              flex: 2,
              child: Padding(
                padding: const EdgeInsets.all(8.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      reward.name,
                      style: const TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 14,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 4),
                    Text(
                      '${reward.pointsRequired} نقطة',
                      style: TextStyle(
                        color: Theme.of(context).primaryColor,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const Spacer(),
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton(
                        onPressed: reward.isAvailable && canRedeem
                            ? () => _redeemReward(reward)
                            : null,
                        style: ElevatedButton.styleFrom(
                          padding: const EdgeInsets.symmetric(vertical: 8),
                          textStyle: const TextStyle(fontSize: 12),
                        ),
                        child: const Text('استبدال'),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  void _showRewardDetails(LoyaltyReward reward) {
    final loyaltyService = Provider.of<LoyaltyService>(context, listen: false);
    final canRedeem = loyaltyService.userLoyalty != null &&
        loyaltyService.userLoyalty!.totalPoints >= reward.pointsRequired;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(16)),
      ),
      builder: (context) {
        return Container(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              // Header
              Row(
                children: [
                  ClipRRect(
                    borderRadius: BorderRadius.circular(8),
                    child: Image.network(
                      reward.imageUrl,
                      width: 80,
                      height: 80,
                      fit: BoxFit.cover,
                      errorBuilder: (_, __, ___) => Container(
                        width: 80,
                        height: 80,
                        color: Colors.grey[300],
                        child: const Icon(Icons.image_not_supported),
                      ),
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          reward.name,
                          style: const TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        const SizedBox(height: 8),
                        Text(
                          '${reward.pointsRequired} نقطة',
                          style: TextStyle(
                            color: Theme.of(context).primaryColor,
                            fontWeight: FontWeight.bold,
                            fontSize: 16,
                          ),
                        ),
                        const SizedBox(height: 4),
                        if (!reward.isAvailable)
                          const Text(
                            'غير متوفر حالياً',
                            style: TextStyle(
                              color: Colors.red,
                            ),
                          ),
                      ],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 16),
              const Text(
                'الوصف',
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 8),
              Text(
                reward.description,
                style: TextStyle(
                  color: Colors.grey[700],
                ),
              ),
              const SizedBox(height: 24),
              if (loyaltyService.userLoyalty != null) ...[
                Text(
                  'رصيدك الحالي: ${loyaltyService.userLoyalty!.totalPoints} نقطة',
                  style: const TextStyle(
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 8),
                if (loyaltyService.userLoyalty!.totalPoints < reward.pointsRequired)
                  Text(
                    'تحتاج ${reward.pointsRequired - loyaltyService.userLoyalty!.totalPoints} نقطة إضافية',
                    style: const TextStyle(
                      color: Colors.red,
                    ),
                  ),
              ],
              const SizedBox(height: 24),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: reward.isAvailable && canRedeem
                      ? () {
                          Navigator.pop(context);
                          _redeemReward(reward);
                        }
                      : null,
                  style: ElevatedButton.styleFrom(
                    padding: const EdgeInsets.symmetric(vertical: 16),
                  ),
                  child: const Text('استبدال المكافأة'),
                ),
              ),
            ],
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