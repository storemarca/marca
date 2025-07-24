@extends('layouts.admin')

@section('title', 'تفاصيل القسم')
@section('header', 'تفاصيل القسم: {{ $category->name }}')

@section('content')
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">معلومات القسم</h3>
                <div class="border rounded-md p-4">
                    <div class="mb-3">
                        <span class="font-medium">الاسم:</span>
                        <span>{{ $category->name }}</span>
                    </div>
                    <div class="mb-3">
                        <span class="font-medium">الرابط:</span>
                        <span>{{ $category->slug }}</span>
                    </div>
                    <div class="mb-3">
                        <span class="font-medium">القسم الأب:</span>
                        <span>{{ $category->parent ? $category->parent->name : 'قسم رئيسي' }}</span>
                    </div>
                    <div class="mb-3">
                        <span class="font-medium">الحالة:</span>
                        <span class="{{ $category->is_active ? 'text-green-600' : 'text-red-600' }}">
                            {{ $category->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <span class="font-medium">ترتيب العرض:</span>
                        <span>{{ $category->sort_order }}</span>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">الصورة</h3>
                <div class="border rounded-md p-4 flex justify-center">
                    @if($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="max-h-40">
                    @else
                        <div class="bg-gray-100 p-4 text-center text-gray-500 w-full">
                            لا توجد صورة
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        @if($category->description)
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">الوصف</h3>
                <div class="border rounded-md p-4">
                    {{ $category->description }}
                </div>
            </div>
        @endif
        
        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-2">بيانات SEO</h3>
            <div class="border rounded-md p-4">
                <div class="mb-3">
                    <span class="font-medium">عنوان الميتا:</span>
                    <span>{{ $category->meta_title ?? $category->name }}</span>
                </div>
                <div>
                    <span class="font-medium">وصف الميتا:</span>
                    <span>{{ $category->meta_description ?? 'لا يوجد' }}</span>
                </div>
            </div>
        </div>
        
        @if($category->children->count() > 0)
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">الأقسام الفرعية ({{ $category->children->count() }})</h3>
                <div class="border rounded-md overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عدد المنتجات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($category->children as $child)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('admin.categories.show', $child) }}" class="text-blue-600 hover:text-blue-900">
                                            {{ $child->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $child->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $child->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $child->products_count ?? 0 }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
        
        @if($category->products->count() > 0)
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">المنتجات ({{ $category->products->count() }})</h3>
                <div class="border rounded-md overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($category->products as $product)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('admin.products.show', $product) }}" class="text-blue-600 hover:text-blue-900">
                                            {{ $product->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $product->sku }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $product->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
        
        <div class="flex items-center justify-end">
            <a href="{{ route('admin.categories.edit', $category) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 ml-2">
                تعديل
            </a>
            <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                العودة للقائمة
            </a>
        </div>
    </div>
@endsection 