import 'package:intl/intl.dart';

class LoyaltyPoint {
  final String id;
  final int points;
  final String type;
  final String description;
  final DateTime createdAt;
  final DateTime? expiresAt;

  LoyaltyPoint({
    required this.id,
    required this.points,
    required this.type,
    required this.description,
    required this.createdAt,
    this.expiresAt,
  });

  bool get isExpired {
    if (expiresAt == null) return false;
    return DateTime.now().isAfter(expiresAt!);
  }

  String get formattedCreatedAt {
    final dateFormat = DateFormat('MMM dd, yyyy');
    return dateFormat.format(createdAt);
  }

  String get formattedExpiresAt {
    if (expiresAt == null) return 'Never';
    final dateFormat = DateFormat('MMM dd, yyyy');
    return dateFormat.format(expiresAt!);
  }

  factory LoyaltyPoint.fromJson(Map<String, dynamic> json) {
    return LoyaltyPoint(
      id: json['id'],
      points: json['points'],
      type: json['type'],
      description: json['description'],
      createdAt: DateTime.parse(json['created_at']),
      expiresAt: json['expires_at'] != null
          ? DateTime.parse(json['expires_at'])
          : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'points': points,
      'type': type,
      'description': description,
      'created_at': createdAt.toIso8601String(),
      'expires_at': expiresAt?.toIso8601String(),
    };
  }
}

class UserLoyalty {
  final int totalPoints;
  final String tier;
  final int pointsToNextTier;
  final List<LoyaltyPoint> transactions;

  UserLoyalty({
    required this.totalPoints,
    required this.tier,
    required this.pointsToNextTier,
    required this.transactions,
  });

  factory UserLoyalty.fromJson(Map<String, dynamic> json) {
    return UserLoyalty(
      totalPoints: json['total_points'],
      tier: json['tier'],
      pointsToNextTier: json['points_to_next_tier'],
      transactions: (json['transactions'] as List)
          .map((item) => LoyaltyPoint.fromJson(item))
          .toList(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'total_points': totalPoints,
      'tier': tier,
      'points_to_next_tier': pointsToNextTier,
      'transactions': transactions.map((t) => t.toJson()).toList(),
    };
  }
}

class LoyaltyReward {
  final String id;
  final String name;
  final String description;
  final int pointsRequired;
  final String imageUrl;
  final bool isAvailable;

  LoyaltyReward({
    required this.id,
    required this.name,
    required this.description,
    required this.pointsRequired,
    required this.imageUrl,
    required this.isAvailable,
  });

  factory LoyaltyReward.fromJson(Map<String, dynamic> json) {
    return LoyaltyReward(
      id: json['id'],
      name: json['name'],
      description: json['description'],
      pointsRequired: json['points_required'],
      imageUrl: json['image_url'],
      isAvailable: json['is_available'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'description': description,
      'points_required': pointsRequired,
      'image_url': imageUrl,
      'is_available': isAvailable,
    };
  }
} 