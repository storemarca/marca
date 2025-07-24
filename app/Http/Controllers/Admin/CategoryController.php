<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * عرض قائمة الأقسام
     */
    public function index()
    {
        $categories = Category::with('parent')->orderBy('sort_order')->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * عرض نموذج إنشاء قسم جديد
     */
    public function create()
    {
        $categories = Category::whereNull('parent_id')->get();
        return view('admin.categories.create', compact('categories'));
    }

    /**
     * تخزين قسم جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);
        
        // إنشاء slug إذا لم يتم توفيره
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        // معالجة الصورة إذا تم تحميلها
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $validated['image'] = $path;
        }
        
        // تعيين قيمة افتراضية لـ is_active
        $validated['is_active'] = $request->has('is_active');
        
        // تعيين قيمة افتراضية لـ sort_order
        if (empty($validated['sort_order'])) {
            $validated['sort_order'] = 0;
        }
        
        Category::create($validated);
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'تم إنشاء القسم بنجاح');
    }

    /**
     * عرض قسم محدد
     */
    public function show(Category $category)
    {
        $category->load('parent', 'children', 'products');
        return view('admin.categories.show', compact('category'));
    }

    /**
     * عرض نموذج تعديل قسم
     */
    public function edit(Category $category)
    {
        $categories = Category::whereNull('parent_id')
                           ->where('id', '!=', $category->id)
                           ->get();
        return view('admin.categories.edit', compact('category', 'categories'));
    }

    /**
     * تحديث قسم محدد
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);
        
        // التحقق من أن القسم لا يمكن أن يكون أبًا لنفسه
        if ($validated['parent_id'] == $category->id) {
            return back()->withErrors(['parent_id' => 'لا يمكن أن يكون القسم أبًا لنفسه']);
        }
        
        // إنشاء slug إذا لم يتم توفيره
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        // معالجة الصورة إذا تم تحميلها
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            
            $path = $request->file('image')->store('categories', 'public');
            $validated['image'] = $path;
        }
        
        // تعيين قيمة افتراضية لـ is_active
        $validated['is_active'] = $request->has('is_active');
        
        // تعيين قيمة افتراضية لـ sort_order
        if (empty($validated['sort_order'])) {
            $validated['sort_order'] = 0;
        }
        
        $category->update($validated);
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'تم تحديث القسم بنجاح');
    }

    /**
     * حذف قسم محدد
     */
    public function destroy(Category $category)
    {
        // التحقق من وجود منتجات مرتبطة بهذا القسم
        if ($category->products()->count() > 0) {
            return back()->withErrors(['error' => 'لا يمكن حذف القسم لأنه يحتوي على منتجات']);
        }
        
        // التحقق من وجود أقسام فرعية
        if ($category->children()->count() > 0) {
            return back()->withErrors(['error' => 'لا يمكن حذف القسم لأنه يحتوي على أقسام فرعية']);
        }
        
        // حذف الصورة إذا كانت موجودة
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }
        
        $category->delete();
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'تم حذف القسم بنجاح');
    }
} 