<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Categorías principales
        $mainCategories = [
            [
                'name' => 'Electronics',
                'name_ar' => 'إلكترونيات',
                'description' => 'All electronic devices and accessories',
                'description_ar' => 'جميع الأجهزة الإلكترونية والملحقات',
                'is_featured' => true,
                'order' => 1,
            ],
            [
                'name' => 'Fashion',
                'name_ar' => 'أزياء',
                'description' => 'Clothing, shoes, and accessories',
                'description_ar' => 'ملابس وأحذية وإكسسوارات',
                'is_featured' => true,
                'order' => 2,
            ],
            [
                'name' => 'Home & Kitchen',
                'name_ar' => 'المنزل والمطبخ',
                'description' => 'Home appliances, furniture, and kitchenware',
                'description_ar' => 'أجهزة منزلية وأثاث وأدوات مطبخ',
                'is_featured' => true,
                'order' => 3,
            ],
            [
                'name' => 'Beauty & Personal Care',
                'name_ar' => 'الجمال والعناية الشخصية',
                'description' => 'Makeup, skincare, and personal care products',
                'description_ar' => 'مكياج ومنتجات العناية بالبشرة والعناية الشخصية',
                'is_featured' => true,
                'order' => 4,
            ],
        ];

        foreach ($mainCategories as $category) {
            $this->createCategory($category);
        }

        // Subcategorías para Electronics
        $electronicsId = Category::where('name', 'Electronics')->first()->id;
        $electronicsSubcategories = [
            [
                'name' => 'Smartphones',
                'name_ar' => 'الهواتف الذكية',
                'description' => 'Latest smartphones and accessories',
                'description_ar' => 'أحدث الهواتف الذكية والملحقات',
                'parent_id' => $electronicsId,
                'order' => 1,
            ],
            [
                'name' => 'Laptops',
                'name_ar' => 'أجهزة الكمبيوتر المحمولة',
                'description' => 'Laptops and notebooks',
                'description_ar' => 'أجهزة الكمبيوتر المحمولة والدفاتر',
                'parent_id' => $electronicsId,
                'order' => 2,
            ],
            [
                'name' => 'Audio',
                'name_ar' => 'الصوتيات',
                'description' => 'Headphones, speakers, and audio accessories',
                'description_ar' => 'سماعات الرأس ومكبرات الصوت والملحقات الصوتية',
                'parent_id' => $electronicsId,
                'order' => 3,
            ],
        ];

        foreach ($electronicsSubcategories as $category) {
            $this->createCategory($category);
        }

        // Subcategorías para Fashion
        $fashionId = Category::where('name', 'Fashion')->first()->id;
        $fashionSubcategories = [
            [
                'name' => 'Men\'s Clothing',
                'name_ar' => 'ملابس رجالية',
                'description' => 'Men\'s shirts, pants, and outerwear',
                'description_ar' => 'قمصان وبناطيل وملابس خارجية للرجال',
                'parent_id' => $fashionId,
                'order' => 1,
            ],
            [
                'name' => 'Women\'s Clothing',
                'name_ar' => 'ملابس نسائية',
                'description' => 'Women\'s dresses, tops, and bottoms',
                'description_ar' => 'فساتين وبلوزات وبناطيل للنساء',
                'parent_id' => $fashionId,
                'order' => 2,
            ],
            [
                'name' => 'Shoes',
                'name_ar' => 'أحذية',
                'description' => 'Men\'s and women\'s shoes',
                'description_ar' => 'أحذية رجالية ونسائية',
                'parent_id' => $fashionId,
                'order' => 3,
            ],
        ];

        foreach ($fashionSubcategories as $category) {
            $this->createCategory($category);
        }
    }

    /**
     * Create a category with the given data.
     */
    private function createCategory(array $data): void
    {
        $slug = Str::slug($data['name']);
        
        Category::create([
            'name' => $data['name'],
            'name_ar' => $data['name_ar'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'description_ar' => $data['description_ar'] ?? null,
            'parent_id' => $data['parent_id'] ?? null,
            'order' => $data['order'] ?? 0,
            'is_active' => $data['is_active'] ?? true,
            'is_featured' => $data['is_featured'] ?? false,
        ]);
    }
} 