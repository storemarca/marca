import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../models/affiliate.dart';
import '../services/affiliate_service.dart';
import '../widgets/loading_indicator.dart';

class AffiliateStatsScreen extends StatefulWidget {
  const AffiliateStatsScreen({Key? key}) : super(key: key);

  @override
  State<AffiliateStatsScreen> createState() => _AffiliateStatsScreenState();
}

class _AffiliateStatsScreenState extends State<AffiliateStatsScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _fetchAffiliateStats();
    });
  }

  Future<void> _fetchAffiliateStats() async {
    try {
      await Provider.of<AffiliateService>(context, listen: false).getAffiliateStats();
    } catch (e) {
      // Error is handled in the AffiliateService
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('إحصائيات التسويق'),
      ),
      body: Consumer<AffiliateService>(
        builder: (context, affiliateService, child) {
          if (affiliateService.isLoading) {
            return const Center(child: LoadingIndicator());
          }

          if (affiliateService.error != null) {
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
                    'فشل تحميل الإحصائيات: ${affiliateService.error}',
                    textAlign: TextAlign.center,
                    style: const TextStyle(color: Colors.red),
                  ),
                  const SizedBox(height: 16),
                  ElevatedButton(
                    onPressed: _fetchAffiliateStats,
                    child: const Text('إعادة المحاولة'),
                  ),
                ],
              ),
            );
          }

          if (affiliateService.stats == null) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.analytics_outlined,
                    size: 64,
                    color: Colors.grey[400],
                  ),
                  const SizedBox(height: 16),
                  Text(
                    'لا توجد إحصائيات متاحة',
                    style: TextStyle(
                      fontSize: 18,
                      color: Colors.grey[600],
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'ابدأ بإنشاء روابط تسويقية لعرض الإحصائيات',
                    style: TextStyle(
                      color: Colors.grey[500],
                    ),
                    textAlign: TextAlign.center,
                  ),
                ],
              ),
            );
          }

          final stats = affiliateService.stats!;

          return RefreshIndicator(
            onRefresh: _fetchAffiliateStats,
            child: SingleChildScrollView(
              physics: const AlwaysScrollableScrollPhysics(),
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildSummaryCards(stats),
                  const SizedBox(height: 24),
                  const Text(
                    'إحصائيات شهرية',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 8),
                  _buildStatsTable(
                    stats.monthlyStats,
                    showPeriod: true,
                  ),
                  const SizedBox(height: 24),
                  const Text(
                    'إحصائيات يومية (آخر 7 أيام)',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 8),
                  _buildStatsTable(
                    stats.dailyStats,
                    showPeriod: true,
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildSummaryCards(AffiliateStats stats) {
    return Column(
      children: [
        Row(
          children: [
            Expanded(
              child: _buildSummaryCard(
                title: 'إجمالي الأرباح',
                value: stats.formattedTotalEarnings,
                icon: Icons.monetization_on,
                color: Colors.green,
              ),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: _buildSummaryCard(
                title: 'الرصيد المتاح',
                value: stats.formattedAvailableBalance,
                icon: Icons.account_balance_wallet,
                color: Colors.blue,
              ),
            ),
          ],
        ),
        const SizedBox(height: 16),
        Row(
          children: [
            Expanded(
              child: _buildSummaryCard(
                title: 'إجمالي النقرات',
                value: stats.totalClicks.toString(),
                icon: Icons.remove_red_eye,
                color: Colors.purple,
              ),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: _buildSummaryCard(
                title: 'إجمالي المبيعات',
                value: stats.totalConversions.toString(),
                icon: Icons.shopping_cart,
                color: Colors.orange,
              ),
            ),
          ],
        ),
        const SizedBox(height: 16),
        _buildSummaryCard(
          title: 'معدل التحويل',
          value: stats.formattedConversionRate,
          icon: Icons.percent,
          color: Colors.teal,
        ),
      ],
    );
  }

  Widget _buildSummaryCard({
    required String title,
    required String value,
    required IconData icon,
    required Color color,
  }) {
    return Card(
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Icon(
                  icon,
                  color: color,
                  size: 24,
                ),
                const SizedBox(width: 8),
                Text(
                  title,
                  style: TextStyle(
                    color: Colors.grey[600],
                    fontSize: 14,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 8),
            Text(
              value,
              style: TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.bold,
                color: color,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatsTable(
    List<AffiliateStat> stats, {
    bool showPeriod = false,
  }) {
    return Card(
      elevation: 2,
      child: SingleChildScrollView(
        scrollDirection: Axis.horizontal,
        child: DataTable(
          columns: [
            if (showPeriod)
              const DataColumn(
                label: Text(
                  'الفترة',
                  style: TextStyle(fontWeight: FontWeight.bold),
                ),
              ),
            const DataColumn(
              label: Text(
                'النقرات',
                style: TextStyle(fontWeight: FontWeight.bold),
              ),
            ),
            const DataColumn(
              label: Text(
                'المبيعات',
                style: TextStyle(fontWeight: FontWeight.bold),
              ),
            ),
            const DataColumn(
              label: Text(
                'معدل التحويل',
                style: TextStyle(fontWeight: FontWeight.bold),
              ),
            ),
            const DataColumn(
              label: Text(
                'الأرباح',
                style: TextStyle(fontWeight: FontWeight.bold),
              ),
            ),
          ],
          rows: stats.map((stat) {
            return DataRow(
              cells: [
                if (showPeriod)
                  DataCell(Text(stat.period)),
                DataCell(Text(stat.clicks.toString())),
                DataCell(Text(stat.conversions.toString())),
                DataCell(Text(stat.formattedConversionRate)),
                DataCell(Text(stat.formattedEarnings)),
              ],
            );
          }).toList(),
        ),
      ),
    );
  }
} 