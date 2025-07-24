import 'package:intl/intl.dart';

class AffiliateLink {
  final String id;
  final String code;
  final String url;
  final String title;
  final String? productId;
  final String? productName;
  final String? productImage;
  final DateTime createdAt;
  final int clicks;
  final int conversions;
  final double earnings;

  AffiliateLink({
    required this.id,
    required this.code,
    required this.url,
    required this.title,
    this.productId,
    this.productName,
    this.productImage,
    required this.createdAt,
    required this.clicks,
    required this.conversions,
    required this.earnings,
  });

  String get formattedCreatedAt {
    final dateFormat = DateFormat('MMM dd, yyyy');
    return dateFormat.format(createdAt);
  }

  String get formattedEarnings {
    final formatter = NumberFormat.currency(
      symbol: 'SAR ',
      decimalDigits: 2,
    );
    return formatter.format(earnings);
  }

  double get conversionRate {
    if (clicks == 0) return 0;
    return conversions / clicks * 100;
  }

  String get formattedConversionRate {
    return '${conversionRate.toStringAsFixed(1)}%';
  }

  factory AffiliateLink.fromJson(Map<String, dynamic> json) {
    return AffiliateLink(
      id: json['id'],
      code: json['code'],
      url: json['url'],
      title: json['title'],
      productId: json['product_id'],
      productName: json['product_name'],
      productImage: json['product_image'],
      createdAt: DateTime.parse(json['created_at']),
      clicks: json['clicks'],
      conversions: json['conversions'],
      earnings: json['earnings'].toDouble(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'code': code,
      'url': url,
      'title': title,
      'product_id': productId,
      'product_name': productName,
      'product_image': productImage,
      'created_at': createdAt.toIso8601String(),
      'clicks': clicks,
      'conversions': conversions,
      'earnings': earnings,
    };
  }
}

class AffiliateStat {
  final String period;
  final int clicks;
  final int conversions;
  final double earnings;

  AffiliateStat({
    required this.period,
    required this.clicks,
    required this.conversions,
    required this.earnings,
  });

  String get formattedEarnings {
    final formatter = NumberFormat.currency(
      symbol: 'SAR ',
      decimalDigits: 2,
    );
    return formatter.format(earnings);
  }

  double get conversionRate {
    if (clicks == 0) return 0;
    return conversions / clicks * 100;
  }

  String get formattedConversionRate {
    return '${conversionRate.toStringAsFixed(1)}%';
  }

  factory AffiliateStat.fromJson(Map<String, dynamic> json) {
    return AffiliateStat(
      period: json['period'],
      clicks: json['clicks'],
      conversions: json['conversions'],
      earnings: json['earnings'].toDouble(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'period': period,
      'clicks': clicks,
      'conversions': conversions,
      'earnings': earnings,
    };
  }
}

class AffiliateStats {
  final double totalEarnings;
  final double availableBalance;
  final int totalClicks;
  final int totalConversions;
  final List<AffiliateStat> dailyStats;
  final List<AffiliateStat> monthlyStats;

  AffiliateStats({
    required this.totalEarnings,
    required this.availableBalance,
    required this.totalClicks,
    required this.totalConversions,
    required this.dailyStats,
    required this.monthlyStats,
  });

  String get formattedTotalEarnings {
    final formatter = NumberFormat.currency(
      symbol: 'SAR ',
      decimalDigits: 2,
    );
    return formatter.format(totalEarnings);
  }

  String get formattedAvailableBalance {
    final formatter = NumberFormat.currency(
      symbol: 'SAR ',
      decimalDigits: 2,
    );
    return formatter.format(availableBalance);
  }

  double get conversionRate {
    if (totalClicks == 0) return 0;
    return totalConversions / totalClicks * 100;
  }

  String get formattedConversionRate {
    return '${conversionRate.toStringAsFixed(1)}%';
  }

  factory AffiliateStats.fromJson(Map<String, dynamic> json) {
    return AffiliateStats(
      totalEarnings: json['total_earnings'].toDouble(),
      availableBalance: json['available_balance'].toDouble(),
      totalClicks: json['total_clicks'],
      totalConversions: json['total_conversions'],
      dailyStats: (json['daily_stats'] as List)
          .map((item) => AffiliateStat.fromJson(item))
          .toList(),
      monthlyStats: (json['monthly_stats'] as List)
          .map((item) => AffiliateStat.fromJson(item))
          .toList(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'total_earnings': totalEarnings,
      'available_balance': availableBalance,
      'total_clicks': totalClicks,
      'total_conversions': totalConversions,
      'daily_stats': dailyStats.map((stat) => stat.toJson()).toList(),
      'monthly_stats': monthlyStats.map((stat) => stat.toJson()).toList(),
    };
  }
}

class WithdrawalRequest {
  final String id;
  final double amount;
  final String status;
  final String paymentMethod;
  final String accountDetails;
  final DateTime createdAt;
  final DateTime? processedAt;

  WithdrawalRequest({
    required this.id,
    required this.amount,
    required this.status,
    required this.paymentMethod,
    required this.accountDetails,
    required this.createdAt,
    this.processedAt,
  });

  String get formattedAmount {
    final formatter = NumberFormat.currency(
      symbol: 'SAR ',
      decimalDigits: 2,
    );
    return formatter.format(amount);
  }

  String get formattedCreatedAt {
    final dateFormat = DateFormat('MMM dd, yyyy');
    return dateFormat.format(createdAt);
  }

  String get formattedProcessedAt {
    if (processedAt == null) return 'قيد المعالجة';
    final dateFormat = DateFormat('MMM dd, yyyy');
    return dateFormat.format(processedAt!);
  }

  factory WithdrawalRequest.fromJson(Map<String, dynamic> json) {
    return WithdrawalRequest(
      id: json['id'],
      amount: json['amount'].toDouble(),
      status: json['status'],
      paymentMethod: json['payment_method'],
      accountDetails: json['account_details'],
      createdAt: DateTime.parse(json['created_at']),
      processedAt: json['processed_at'] != null
          ? DateTime.parse(json['processed_at'])
          : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'amount': amount,
      'status': status,
      'payment_method': paymentMethod,
      'account_details': accountDetails,
      'created_at': createdAt.toIso8601String(),
      'processed_at': processedAt?.toIso8601String(),
    };
  }
} 