import 'package:share_plus/share_plus.dart';

class SocialShare {
  static void shareProduct({
    required String name,
    required String description,
    required String url,
    String? imageUrl,
  }) {
    final String shareText = 
        '$name\n\n'
        '$description\n\n'
        'تسوق الآن: $url';
    
    Share.share(shareText, subject: name);
  }

  static void shareApp({
    required String appName,
    required String appDescription,
    required String appUrl,
  }) {
    final String shareText = 
        'جرب تطبيق $appName!\n\n'
        '$appDescription\n\n'
        'حمله الآن: $appUrl';
    
    Share.share(shareText, subject: 'تطبيق $appName');
  }

  static void shareOrder({
    required String orderNumber,
    required String trackingUrl,
  }) {
    final String shareText = 
        'يمكنك تتبع طلبي رقم #$orderNumber من خلال الرابط:\n'
        '$trackingUrl';
    
    Share.share(shareText, subject: 'تتبع الطلب #$orderNumber');
  }
} 