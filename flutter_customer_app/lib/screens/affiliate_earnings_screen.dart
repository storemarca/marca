import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../models/affiliate.dart';
import '../services/affiliate_service.dart';
import '../widgets/loading_indicator.dart';

class AffiliateEarningsScreen extends StatefulWidget {
  const AffiliateEarningsScreen({Key? key}) : super(key: key);

  @override
  State<AffiliateEarningsScreen> createState() => _AffiliateEarningsScreenState();
}

class _AffiliateEarningsScreenState extends State<AffiliateEarningsScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _fetchData();
    });
  }

  Future<void> _fetchData() async {
    try {
      final affiliateService = Provider.of<AffiliateService>(context, listen: false);
      await affiliateService.getAffiliateStats();
      await affiliateService.getWithdrawals();
    } catch (e) {
      // Error is handled in the AffiliateService
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('الأرباح والسحب'),
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
                    'فشل تحميل البيانات: ${affiliateService.error}',
                    textAlign: TextAlign.center,
                    style: const TextStyle(color: Colors.red),
                  ),
                  const SizedBox(height: 16),
                  ElevatedButton(
                    onPressed: _fetchData,
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
                    Icons.account_balance_wallet_outlined,
                    size: 64,
                    color: Colors.grey[400],
                  ),
                  const SizedBox(height: 16),
                  Text(
                    'لا توجد بيانات أرباح متاحة',
                    style: TextStyle(
                      fontSize: 18,
                      color: Colors.grey[600],
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'ابدأ بإنشاء روابط تسويقية لكسب الأرباح',
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
            onRefresh: _fetchData,
            child: SingleChildScrollView(
              physics: const AlwaysScrollableScrollPhysics(),
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildEarningsCard(stats),
                  const SizedBox(height: 24),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text(
                        'طلبات السحب',
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      ElevatedButton.icon(
                        onPressed: stats.availableBalance >= 100
                            ? () => _showWithdrawalDialog(context, stats.availableBalance)
                            : null,
                        icon: const Icon(Icons.money),
                        label: const Text('طلب سحب'),
                        style: ElevatedButton.styleFrom(
                          padding: const EdgeInsets.symmetric(horizontal: 16),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  if (affiliateService.withdrawals.isEmpty) ...[
                    Card(
                      elevation: 2,
                      child: Container(
                        padding: const EdgeInsets.all(16),
                        width: double.infinity,
                        child: Column(
                          children: [
                            Icon(
                              Icons.history,
                              size: 48,
                              color: Colors.grey[400],
                            ),
                            const SizedBox(height: 8),
                            Text(
                              'لا توجد طلبات سحب',
                              style: TextStyle(
                                color: Colors.grey[600],
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ] else ...[
                    ListView.builder(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      itemCount: affiliateService.withdrawals.length,
                      itemBuilder: (context, index) {
                        final withdrawal = affiliateService.withdrawals[index];
                        return _buildWithdrawalCard(withdrawal);
                      },
                    ),
                  ],
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildEarningsCard(AffiliateStats stats) {
    return Card(
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'ملخص الأرباح',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 24),
            Row(
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'إجمالي الأرباح',
                        style: TextStyle(
                          color: Colors.grey[600],
                        ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        stats.formattedTotalEarnings,
                        style: const TextStyle(
                          fontSize: 20,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ],
                  ),
                ),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'الرصيد المتاح',
                        style: TextStyle(
                          color: Colors.grey[600],
                        ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        stats.formattedAvailableBalance,
                        style: TextStyle(
                          fontSize: 20,
                          fontWeight: FontWeight.bold,
                          color: stats.availableBalance >= 100
                              ? Colors.green
                              : null,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            const Divider(),
            const SizedBox(height: 8),
            Text(
              stats.availableBalance >= 100
                  ? 'يمكنك سحب أرباحك الآن'
                  : 'الحد الأدنى للسحب هو 100 ريال',
              style: TextStyle(
                color: stats.availableBalance >= 100
                    ? Colors.green
                    : Colors.orange,
                fontWeight: FontWeight.bold,
              ),
            ),
            if (stats.availableBalance < 100) ...[
              const SizedBox(height: 8),
              Text(
                'تحتاج ${(100 - stats.availableBalance).toStringAsFixed(2)} ريال إضافية للسحب',
                style: TextStyle(
                  color: Colors.grey[600],
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }

  Widget _buildWithdrawalCard(WithdrawalRequest withdrawal) {
    Color statusColor;
    switch (withdrawal.status.toLowerCase()) {
      case 'pending':
        statusColor = Colors.orange;
        break;
      case 'approved':
        statusColor = Colors.green;
        break;
      case 'rejected':
        statusColor = Colors.red;
        break;
      default:
        statusColor = Colors.grey;
    }

    return Card(
      margin: const EdgeInsets.only(bottom: 16),
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  withdrawal.formattedAmount,
                  style: const TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                Chip(
                  label: Text(_getStatusText(withdrawal.status)),
                  backgroundColor: statusColor,
                  labelStyle: const TextStyle(
                    color: Colors.white,
                    fontSize: 12,
                  ),
                  padding: EdgeInsets.zero,
                ),
              ],
            ),
            const SizedBox(height: 8),
            Text(
              'طريقة الدفع: ${withdrawal.paymentMethod}',
              style: TextStyle(
                color: Colors.grey[700],
              ),
            ),
            const SizedBox(height: 4),
            Text(
              'تفاصيل الحساب: ${withdrawal.accountDetails}',
              style: TextStyle(
                color: Colors.grey[700],
              ),
            ),
            const SizedBox(height: 8),
            const Divider(),
            const SizedBox(height: 8),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  'تاريخ الطلب: ${withdrawal.formattedCreatedAt}',
                  style: TextStyle(
                    color: Colors.grey[600],
                    fontSize: 12,
                  ),
                ),
                if (withdrawal.processedAt != null)
                  Text(
                    'تاريخ المعالجة: ${withdrawal.formattedProcessedAt}',
                    style: TextStyle(
                      color: Colors.grey[600],
                      fontSize: 12,
                    ),
                  ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  String _getStatusText(String status) {
    switch (status.toLowerCase()) {
      case 'pending':
        return 'قيد المعالجة';
      case 'approved':
        return 'تمت الموافقة';
      case 'rejected':
        return 'مرفوض';
      default:
        return status;
    }
  }

  void _showWithdrawalDialog(BuildContext context, double availableBalance) {
    final _amountController = TextEditingController();
    final _accountDetailsController = TextEditingController();
    String _selectedPaymentMethod = 'bank_transfer';

    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('طلب سحب الأرباح'),
        content: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'الرصيد المتاح: ${availableBalance.toStringAsFixed(2)} ريال',
                style: const TextStyle(
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 16),
              TextField(
                controller: _amountController,
                decoration: const InputDecoration(
                  labelText: 'المبلغ',
                  hintText: 'أدخل المبلغ المراد سحبه',
                  border: OutlineInputBorder(),
                ),
                keyboardType: TextInputType.number,
              ),
              const SizedBox(height: 16),
              const Text(
                'طريقة السحب:',
                style: TextStyle(
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 8),
              StatefulBuilder(
                builder: (context, setState) {
                  return Column(
                    children: [
                      RadioListTile<String>(
                        title: const Text('تحويل بنكي'),
                        value: 'bank_transfer',
                        groupValue: _selectedPaymentMethod,
                        onChanged: (value) {
                          setState(() {
                            _selectedPaymentMethod = value!;
                          });
                        },
                      ),
                      RadioListTile<String>(
                        title: const Text('PayPal'),
                        value: 'paypal',
                        groupValue: _selectedPaymentMethod,
                        onChanged: (value) {
                          setState(() {
                            _selectedPaymentMethod = value!;
                          });
                        },
                      ),
                      RadioListTile<String>(
                        title: const Text('محفظة إلكترونية'),
                        value: 'wallet',
                        groupValue: _selectedPaymentMethod,
                        onChanged: (value) {
                          setState(() {
                            _selectedPaymentMethod = value!;
                          });
                        },
                      ),
                    ],
                  );
                },
              ),
              const SizedBox(height: 16),
              TextField(
                controller: _accountDetailsController,
                decoration: const InputDecoration(
                  labelText: 'تفاصيل الحساب',
                  hintText: 'أدخل تفاصيل الحساب للسحب',
                  border: OutlineInputBorder(),
                ),
                maxLines: 3,
              ),
            ],
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('إلغاء'),
          ),
          ElevatedButton(
            onPressed: () => _requestWithdrawal(
              context,
              _amountController.text,
              _selectedPaymentMethod,
              _accountDetailsController.text,
              availableBalance,
            ),
            child: const Text('تأكيد'),
          ),
        ],
      ),
    );
  }

  Future<void> _requestWithdrawal(
    BuildContext context,
    String amountText,
    String paymentMethod,
    String accountDetails,
    double availableBalance,
  ) async {
    // Validate inputs
    if (amountText.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('يرجى إدخال المبلغ'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    if (accountDetails.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('يرجى إدخال تفاصيل الحساب'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    double amount;
    try {
      amount = double.parse(amountText);
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('يرجى إدخال مبلغ صحيح'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    if (amount < 100) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('الحد الأدنى للسحب هو 100 ريال'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    if (amount > availableBalance) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('المبلغ المطلوب أكبر من الرصيد المتاح'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    Navigator.pop(context);
    final scaffoldMessenger = ScaffoldMessenger.of(context);
    
    try {
      final affiliateService = Provider.of<AffiliateService>(context, listen: false);
      
      final data = {
        'amount': amount,
        'payment_method': paymentMethod,
        'account_details': accountDetails,
      };
      
      await affiliateService.requestWithdrawal(data);
      
      if (mounted) {
        scaffoldMessenger.showSnackBar(
          const SnackBar(
            content: Text('تم إرسال طلب السحب بنجاح'),
            backgroundColor: Colors.green,
          ),
        );
      }
    } catch (e) {
      scaffoldMessenger.showSnackBar(
        SnackBar(
          content: Text('فشل طلب السحب: ${e.toString()}'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }
} 