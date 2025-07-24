import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import '../models/affiliate.dart';
import '../services/affiliate_service.dart';
import '../widgets/loading_indicator.dart';

class AffiliateLinksScreen extends StatefulWidget {
  const AffiliateLinksScreen({Key? key}) : super(key: key);

  @override
  State<AffiliateLinksScreen> createState() => _AffiliateLinksScreenState();
}

class _AffiliateLinksScreenState extends State<AffiliateLinksScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _fetchAffiliateLinks();
    });
  }

  Future<void> _fetchAffiliateLinks() async {
    try {
      await Provider.of<AffiliateService>(context, listen: false).getAffiliateLinks();
    } catch (e) {
      // Error is handled in the AffiliateService
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('الروابط التسويقية'),
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
                    'فشل تحميل الروابط التسويقية: ${affiliateService.error}',
                    textAlign: TextAlign.center,
                    style: const TextStyle(color: Colors.red),
                  ),
                  const SizedBox(height: 16),
                  ElevatedButton(
                    onPressed: _fetchAffiliateLinks,
                    child: const Text('إعادة المحاولة'),
                  ),
                ],
              ),
            );
          }

          if (affiliateService.links.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.link_off,
                    size: 64,
                    color: Colors.grey[400],
                  ),
                  const SizedBox(height: 16),
                  Text(
                    'لا توجد روابط تسويقية',
                    style: TextStyle(
                      fontSize: 18,
                      color: Colors.grey[600],
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'أنشئ رابطًا جديدًا للبدء في كسب العمولات',
                    style: TextStyle(
                      color: Colors.grey[500],
                    ),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 24),
                  ElevatedButton.icon(
                    onPressed: () => _showCreateLinkDialog(context),
                    icon: const Icon(Icons.add),
                    label: const Text('إنشاء رابط جديد'),
                  ),
                ],
              ),
            );
          }

          return RefreshIndicator(
            onRefresh: _fetchAffiliateLinks,
            child: Padding(
              padding: const EdgeInsets.all(16.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  ElevatedButton.icon(
                    onPressed: () => _showCreateLinkDialog(context),
                    icon: const Icon(Icons.add),
                    label: const Text('إنشاء رابط جديد'),
                    style: ElevatedButton.styleFrom(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 16,
                        vertical: 12,
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),
                  Expanded(
                    child: ListView.builder(
                      itemCount: affiliateService.links.length,
                      itemBuilder: (context, index) {
                        final link = affiliateService.links[index];
                        return _buildLinkCard(link);
                      },
                    ),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildLinkCard(AffiliateLink link) {
    return Card(
      margin: const EdgeInsets.only(bottom: 16),
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                if (link.productImage != null) ...[
                  ClipRRect(
                    borderRadius: BorderRadius.circular(4),
                    child: Image.network(
                      link.productImage!,
                      width: 60,
                      height: 60,
                      fit: BoxFit.cover,
                      errorBuilder: (_, __, ___) => Container(
                        width: 60,
                        height: 60,
                        color: Colors.grey[300],
                        child: const Icon(Icons.image_not_supported),
                      ),
                    ),
                  ),
                  const SizedBox(width: 16),
                ],
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        link.title,
                        style: const TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 16,
                        ),
                      ),
                      const SizedBox(height: 4),
                      if (link.productName != null) ...[
                        Text(
                          link.productName!,
                          style: TextStyle(
                            color: Colors.grey[600],
                          ),
                        ),
                        const SizedBox(height: 4),
                      ],
                      Text(
                        'تم الإنشاء: ${link.formattedCreatedAt}',
                        style: TextStyle(
                          color: Colors.grey[500],
                          fontSize: 12,
                        ),
                      ),
                    ],
                  ),
                ),
                IconButton(
                  icon: const Icon(Icons.delete_outline),
                  color: Colors.red,
                  onPressed: () => _deleteLink(link),
                ),
              ],
            ),
            const Divider(height: 24),
            InkWell(
              onTap: () => _copyToClipboard(link.url),
              child: Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: Colors.grey[100],
                  borderRadius: BorderRadius.circular(4),
                ),
                child: Row(
                  children: [
                    Expanded(
                      child: Text(
                        link.url,
                        style: const TextStyle(
                          fontFamily: 'monospace',
                          fontSize: 12,
                        ),
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                    const SizedBox(width: 8),
                    const Icon(
                      Icons.copy,
                      size: 16,
                      color: Colors.grey,
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 16),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceAround,
              children: [
                _buildStatItem(
                  icon: Icons.remove_red_eye,
                  value: link.clicks.toString(),
                  label: 'مشاهدات',
                ),
                _buildStatItem(
                  icon: Icons.shopping_cart,
                  value: link.conversions.toString(),
                  label: 'مبيعات',
                ),
                _buildStatItem(
                  icon: Icons.percent,
                  value: link.formattedConversionRate,
                  label: 'نسبة التحويل',
                ),
                _buildStatItem(
                  icon: Icons.attach_money,
                  value: link.formattedEarnings,
                  label: 'الأرباح',
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatItem({
    required IconData icon,
    required String value,
    required String label,
  }) {
    return Column(
      children: [
        Icon(
          icon,
          color: Theme.of(context).primaryColor,
          size: 20,
        ),
        const SizedBox(height: 4),
        Text(
          value,
          style: const TextStyle(
            fontWeight: FontWeight.bold,
          ),
        ),
        Text(
          label,
          style: TextStyle(
            color: Colors.grey[600],
            fontSize: 12,
          ),
        ),
      ],
    );
  }

  void _copyToClipboard(String text) {
    Clipboard.setData(ClipboardData(text: text));
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('تم نسخ الرابط إلى الحافظة'),
        duration: Duration(seconds: 2),
      ),
    );
  }

  Future<void> _deleteLink(AffiliateLink link) async {
    final scaffoldMessenger = ScaffoldMessenger.of(context);
    
    // Show confirmation dialog
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('حذف الرابط'),
        content: Text(
          'هل أنت متأكد من حذف الرابط "${link.title}"؟',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('إلغاء'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            style: TextButton.styleFrom(foregroundColor: Colors.red),
            child: const Text('حذف'),
          ),
        ],
      ),
    );

    if (confirmed != true) return;

    try {
      final affiliateService = Provider.of<AffiliateService>(context, listen: false);
      final success = await affiliateService.deleteAffiliateLink(link.id);
      
      if (success && mounted) {
        scaffoldMessenger.showSnackBar(
          const SnackBar(
            content: Text('تم حذف الرابط بنجاح'),
            backgroundColor: Colors.green,
          ),
        );
      }
    } catch (e) {
      scaffoldMessenger.showSnackBar(
        SnackBar(
          content: Text('فشل حذف الرابط: ${e.toString()}'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  void _showCreateLinkDialog(BuildContext context) {
    final _titleController = TextEditingController();

    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('إنشاء رابط تسويقي جديد'),
        content: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              TextField(
                controller: _titleController,
                decoration: const InputDecoration(
                  labelText: 'عنوان الرابط',
                  hintText: 'أدخل عنوانًا وصفيًا للرابط',
                  border: OutlineInputBorder(),
                ),
              ),
              const SizedBox(height: 16),
              const Text(
                'نوع الرابط:',
                style: TextStyle(
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 8),
              Row(
                children: [
                  Expanded(
                    child: ElevatedButton.icon(
                      onPressed: () {
                        Navigator.pop(context);
                        _createLink(
                          context,
                          _titleController.text.trim(),
                          null,
                          null,
                        );
                      },
                      icon: const Icon(Icons.home),
                      label: const Text('الصفحة الرئيسية'),
                      style: ElevatedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 12),
                      ),
                    ),
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: ElevatedButton.icon(
                      onPressed: () {
                        Navigator.pop(context);
                        _showProductSelectionDialog(context);
                      },
                      icon: const Icon(Icons.shopping_bag),
                      label: const Text('منتج محدد'),
                      style: ElevatedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 12),
                      ),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('إلغاء'),
          ),
        ],
      ),
    );
  }

  void _showProductSelectionDialog(BuildContext context) {
    // In a real app, you would fetch products from an API
    // For this example, we'll use dummy data
    final products = [
      {'id': '1', 'name': 'هاتف ذكي', 'image': 'https://via.placeholder.com/150'},
      {'id': '2', 'name': 'لابتوب', 'image': 'https://via.placeholder.com/150'},
      {'id': '3', 'name': 'سماعات لاسلكية', 'image': 'https://via.placeholder.com/150'},
      {'id': '4', 'name': 'ساعة ذكية', 'image': 'https://via.placeholder.com/150'},
    ];

    final _titleController = TextEditingController();

    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('اختر منتجًا'),
        content: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              TextField(
                controller: _titleController,
                decoration: const InputDecoration(
                  labelText: 'عنوان الرابط',
                  hintText: 'أدخل عنوانًا وصفيًا للرابط',
                  border: OutlineInputBorder(),
                ),
              ),
              const SizedBox(height: 16),
              const Text(
                'اختر منتجًا:',
                style: TextStyle(
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 8),
              SizedBox(
                height: 300,
                width: double.maxFinite,
                child: ListView.builder(
                  shrinkWrap: true,
                  itemCount: products.length,
                  itemBuilder: (context, index) {
                    final product = products[index];
                    return ListTile(
                      leading: ClipRRect(
                        borderRadius: BorderRadius.circular(4),
                        child: Image.network(
                          product['image']!,
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
                      title: Text(product['name']!),
                      onTap: () {
                        Navigator.pop(context);
                        _createLink(
                          context,
                          _titleController.text.trim().isNotEmpty
                              ? _titleController.text.trim()
                              : product['name']!,
                          product['id'],
                          product['name'],
                        );
                      },
                    );
                  },
                ),
              ),
            ],
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('إلغاء'),
          ),
        ],
      ),
    );
  }

  Future<void> _createLink(
    BuildContext context,
    String title,
    String? productId,
    String? productName,
  ) async {
    if (title.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('يرجى إدخال عنوان للرابط'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    final scaffoldMessenger = ScaffoldMessenger.of(context);
    
    try {
      final affiliateService = Provider.of<AffiliateService>(context, listen: false);
      
      final data = {
        'title': title,
        if (productId != null) 'product_id': productId,
      };
      
      await affiliateService.createAffiliateLink(data);
      
      if (mounted) {
        scaffoldMessenger.showSnackBar(
          const SnackBar(
            content: Text('تم إنشاء الرابط بنجاح'),
            backgroundColor: Colors.green,
          ),
        );
      }
    } catch (e) {
      scaffoldMessenger.showSnackBar(
        SnackBar(
          content: Text('فشل إنشاء الرابط: ${e.toString()}'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }
} 