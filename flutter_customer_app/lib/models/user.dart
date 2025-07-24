class User {
  final int id;
  final String name;
  final String email;
  final String? phone;
  final String? avatar;
  final List<String> roles;
  final DateTime createdAt;
  final DateTime updatedAt;

  User({
    required this.id,
    required this.name,
    required this.email,
    this.phone,
    this.avatar,
    required this.roles,
    required this.createdAt,
    required this.updatedAt,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    List<String> parseRoles(dynamic rolesData) {
      if (rolesData == null) return [];
      
      if (rolesData is List) {
        return rolesData.map((role) => role.toString()).toList();
      } else if (rolesData is Map) {
        return rolesData.values.map((role) => role.toString()).toList();
      }
      
      return [];
    }

    return User(
      id: json['id'],
      name: json['name'],
      email: json['email'],
      phone: json['phone'],
      avatar: json['avatar'],
      roles: parseRoles(json['roles']),
      createdAt: json['created_at'] != null 
          ? DateTime.parse(json['created_at']) 
          : DateTime.now(),
      updatedAt: json['updated_at'] != null 
          ? DateTime.parse(json['updated_at']) 
          : DateTime.now(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'phone': phone,
      'avatar': avatar,
      'roles': roles,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }

  bool get isAdmin => roles.contains('admin');
  bool get isCustomer => roles.contains('customer');
} 