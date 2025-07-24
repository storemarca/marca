class Category {
  final int id;
  final String name;
  final String slug;
  final String? description;
  final String? image;
  final int? parentId;
  final Category? parent;
  final List<Category> children;
  final DateTime createdAt;
  final DateTime updatedAt;

  Category({
    required this.id,
    required this.name,
    required this.slug,
    this.description,
    this.image,
    this.parentId,
    this.parent,
    this.children = const [],
    required this.createdAt,
    required this.updatedAt,
  });

  factory Category.fromJson(Map<String, dynamic> json) {
    // Parse children
    List<Category> parseChildren(dynamic childrenData) {
      if (childrenData == null) return [];
      
      if (childrenData is List) {
        return childrenData
            .map((child) => Category.fromJson(child))
            .toList();
      }
      
      return [];
    }
    
    // Parse parent
    Category? parseParent(dynamic parentData) {
      if (parentData == null) return null;
      
      if (parentData is Map<String, dynamic>) {
        return Category.fromJson(parentData);
      }
      
      return null;
    }

    return Category(
      id: json['id'],
      name: json['name'],
      slug: json['slug'],
      description: json['description'],
      image: json['image'],
      parentId: json['parent_id'],
      parent: parseParent(json['parent']),
      children: parseChildren(json['children']),
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
      'slug': slug,
      'description': description,
      'image': image,
      'parent_id': parentId,
      'parent': parent?.toJson(),
      'children': children.map((child) => child.toJson()).toList(),
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }
} 