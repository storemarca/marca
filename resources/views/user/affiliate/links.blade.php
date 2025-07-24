@extends('layouts.user')

@section('title', 'الروابط التسويقية')

@section('styles')
<style>
    .copy-btn:focus {
        box-shadow: none;
    }
    .link-stats {
        transition: all 0.3s ease;
    }
    .link-stats:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>الروابط التسويقية</h1>
        <a href="{{ route('affiliate.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-right ml-1"></i> العودة للوحة التحكم
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- إحصائيات سريعة -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card link-stats border-start border-4 border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">إجمالي النقرات</h6>
                            <h3 class="mb-0">{{ $links->sum('clicks') }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-mouse-pointer text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card link-stats border-start border-4 border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">إجمالي التحويلات</h6>
                            <h3 class="mb-0">{{ $links->sum('conversions') }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-exchange-alt text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card link-stats border-start border-4 border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">معدل التحويل</h6>
                            <h3 class="mb-0">{{ $links->sum('clicks') > 0 ? number_format($links->sum('conversions') / $links->sum('clicks') * 100, 2) : '0.00' }}%</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-percentage text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card link-stats border-start border-4 border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">إجمالي الأرباح</h6>
                            <h3 class="mb-0">{{ number_format($links->sum('earnings'), 2) }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-money-bill-wave text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">إنشاء رابط تسويقي جديد</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('affiliate.create-link') }}" method="POST" class="row g-3">
                        @csrf
                        <div class="col-md-6">
                            <label for="name" class="form-label">اسم الرابط <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            <div class="form-text">اسم الرابط للتعرف عليه (لن يظهر للزوار)</div>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="target_type" class="form-label">نوع الهدف <span class="text-danger">*</span></label>
                            <select class="form-select @error('target_type') is-invalid @enderror" id="target_type" name="target_type" required>
                                <option value="" selected disabled>اختر نوع الهدف</option>
                                <option value="product" {{ old('target_type') == 'product' ? 'selected' : '' }}>منتج</option>
                                <option value="category" {{ old('target_type') == 'category' ? 'selected' : '' }}>تصنيف</option>
                                <option value="page" {{ old('target_type') == 'page' ? 'selected' : '' }}>صفحة</option>
                                <option value="custom" {{ old('target_type') == 'custom' ? 'selected' : '' }}>رابط مخصص</option>
                            </select>
                            @error('target_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 target-fields" id="product-field" style="display: none;">
                            <label for="product_id" class="form-label">اختر المنتج <span class="text-danger">*</span></label>
                            <select class="form-select @error('target_id') is-invalid @enderror" id="product_id" name="target_id">
                                <option value="" selected disabled>اختر المنتج</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('target_id') == $product->id && old('target_type') == 'product' ? 'selected' : '' }}>
                                        {{ $product->name }} - {{ number_format($product->price, 2) }} ر.س
                                    </option>
                                @endforeach
                            </select>
                            @error('target_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 target-fields" id="category-field" style="display: none;">
                            <label for="category_id" class="form-label">اختر التصنيف <span class="text-danger">*</span></label>
                            <select class="form-select @error('target_id') is-invalid @enderror" id="category_id" name="target_id">
                                <option value="" selected disabled>اختر التصنيف</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('target_id') == $category->id && old('target_type') == 'category' ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('target_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 target-fields" id="page-field" style="display: none;">
                            <label for="page_id" class="form-label">اختر الصفحة <span class="text-danger">*</span></label>
                            <select class="form-select @error('target_id') is-invalid @enderror" id="page_id" name="target_id">
                                <option value="" selected disabled>اختر الصفحة</option>
                                <option value="about" {{ old('target_id') == 'about' && old('target_type') == 'page' ? 'selected' : '' }}>من نحن</option>
                                <option value="contact" {{ old('target_id') == 'contact' && old('target_type') == 'page' ? 'selected' : '' }}>اتصل بنا</option>
                                <option value="faq" {{ old('target_id') == 'faq' && old('target_type') == 'page' ? 'selected' : '' }}>الأسئلة الشائعة</option>
                            </select>
                            @error('target_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 target-fields" id="custom-field" style="display: none;">
                            <label for="custom_url" class="form-label">الرابط المخصص <span class="text-danger">*</span></label>
                            <input type="url" class="form-control @error('custom_url') is-invalid @enderror" id="custom_url" name="custom_url" value="{{ old('custom_url') }}">
                            <div class="form-text">أدخل رابط URL كامل (مثال: https://www.example.com/special-offer)</div>
                            @error('custom_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-link me-1"></i> إنشاء الرابط
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">الروابط التسويقية الحالية</h5>
                    <div>
                        <button class="btn btn-sm btn-outline-primary me-2" id="sortByClicks">
                            <i class="fas fa-sort-amount-down me-1"></i> ترتيب حسب النقرات
                        </button>
                        <button class="btn btn-sm btn-outline-success" id="sortByEarnings">
                            <i class="fas fa-sort-amount-down me-1"></i> ترتيب حسب الأرباح
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($links->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped" id="linksTable">
                                <thead>
                                    <tr>
                                        <th>الاسم</th>
                                        <th>النوع</th>
                                        <th>النقرات</th>
                                        <th>التحويلات</th>
                                        <th>معدل التحويل</th>
                                        <th>الأرباح</th>
                                        <th>الرابط</th>
                                        <th>مشاركة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($links as $link)
                                        <tr>
                                            <td>{{ $link->name }}</td>
                                            <td>
                                                <span class="badge {{ $link->target_type == 'product' ? 'bg-primary' : ($link->target_type == 'category' ? 'bg-success' : ($link->target_type == 'page' ? 'bg-info' : 'bg-secondary')) }}">
                                                    {{ $link->target_type_text }}
                                                </span>
                                                @if($link->target_type == 'product' && $link->product)
                                                    <br><small>{{ $link->product->name }}</small>
                                                @elseif($link->target_type == 'category' && $link->category)
                                                    <br><small>{{ $link->category->name }}</small>
                                                @endif
                                            </td>
                                            <td data-clicks="{{ $link->clicks }}">{{ $link->clicks }}</td>
                                            <td>{{ $link->conversions }}</td>
                                            <td>{{ $link->conversion_rate }}%</td>
                                            <td data-earnings="{{ $link->earnings }}">{{ number_format($link->earnings, 2) }} ر.س</td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="text" class="form-control" value="{{ route('affiliate.track', $link->slug) }}" readonly id="link-{{ $link->id }}">
                                                    <button class="btn btn-outline-secondary copy-btn" type="button" onclick="copyToClipboard('link-{{ $link->id }}')">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="shareDropdown-{{ $link->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-share-alt"></i>
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="shareDropdown-{{ $link->id }}">
                                                        <li>
                                                            <a class="dropdown-item" href="https://wa.me/?text={{ urlencode(route('affiliate.track', $link->slug)) }}" target="_blank">
                                                                <i class="fab fa-whatsapp text-success me-2"></i> واتساب
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('affiliate.track', $link->slug)) }}" target="_blank">
                                                                <i class="fab fa-facebook text-primary me-2"></i> فيسبوك
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="https://twitter.com/intent/tweet?url={{ urlencode(route('affiliate.track', $link->slug)) }}" target="_blank">
                                                                <i class="fab fa-twitter text-info me-2"></i> تويتر
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="https://t.me/share/url?url={{ urlencode(route('affiliate.track', $link->slug)) }}" target="_blank">
                                                                <i class="fab fa-telegram text-primary me-2"></i> تليجرام
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $links->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> لا توجد روابط تسويقية حتى الآن. قم بإنشاء روابط جديدة باستخدام النموذج أعلاه.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">نصائح لزيادة التحويلات</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-start border-4 border-primary">
                                <div class="card-body">
                                    <div class="mb-3 text-primary">
                                        <i class="fas fa-bullseye fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">استهدف الجمهور المناسب</h5>
                                    <p class="card-text">قم بمشاركة الروابط التسويقية مع الجمهور المهتم بالمنتجات التي تروج لها. كلما كان الجمهور أكثر استهدافاً، زادت نسبة التحويل.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-start border-4 border-success">
                                <div class="card-body">
                                    <div class="mb-3 text-success">
                                        <i class="fas fa-star fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">أضف قيمة للمحتوى</h5>
                                    <p class="card-text">قم بإنشاء محتوى قيم يشرح فوائد المنتج ومميزاته. المراجعات الصادقة والشرح التفصيلي يزيد من ثقة المستخدمين.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-start border-4 border-info">
                                <div class="card-body">
                                    <div class="mb-3 text-info">
                                        <i class="fas fa-image fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">استخدم المواد التسويقية</h5>
                                    <p class="card-text">استخدم البانرات والصور المتوفرة في قسم المواد التسويقية لجذب انتباه الزوار وزيادة نسبة النقر على الروابط.</p>
                                    <a href="{{ route('affiliate.marketing-materials') }}" class="btn btn-outline-info btn-sm mt-2">
                                        <i class="fas fa-ad me-1"></i> المواد التسويقية
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function copyToClipboard(elementId) {
        const el = document.getElementById(elementId);
        el.select();
        document.execCommand('copy');
        
        // Show tooltip
        const toast = document.createElement('div');
        toast.className = 'position-fixed top-0 end-0 p-3';
        toast.style.zIndex = '5000';
        toast.innerHTML = `
            <div class="toast show align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-check-circle me-2"></i> تم نسخ الرابط بنجاح!
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 3000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const targetTypeSelect = document.getElementById('target_type');
        const targetFields = document.querySelectorAll('.target-fields');
        
        function showTargetField() {
            const selectedType = targetTypeSelect.value;
            
            // Hide all target fields first
            targetFields.forEach(field => {
                field.style.display = 'none';
            });
            
            // Show the selected target field
            if (selectedType === 'product') {
                document.getElementById('product-field').style.display = 'block';
            } else if (selectedType === 'category') {
                document.getElementById('category-field').style.display = 'block';
            } else if (selectedType === 'page') {
                document.getElementById('page-field').style.display = 'block';
            } else if (selectedType === 'custom') {
                document.getElementById('custom-field').style.display = 'block';
            }
        }
        
        // Show the appropriate field on page load
        showTargetField();
        
        // Add event listener for changes
        targetTypeSelect.addEventListener('change', showTargetField);
        
        // Sorting functionality
        document.getElementById('sortByClicks').addEventListener('click', function() {
            sortTable('linksTable', 2, true);
        });
        
        document.getElementById('sortByEarnings').addEventListener('click', function() {
            sortTable('linksTable', 5, true);
        });
        
        function sortTable(tableId, colIndex, numeric) {
            const table = document.getElementById(tableId);
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            rows.sort(function(a, b) {
                let aVal, bVal;
                
                if (numeric) {
                    aVal = parseFloat(a.cells[colIndex].getAttribute('data-' + (colIndex === 2 ? 'clicks' : 'earnings')));
                    bVal = parseFloat(b.cells[colIndex].getAttribute('data-' + (colIndex === 2 ? 'clicks' : 'earnings')));
                } else {
                    aVal = a.cells[colIndex].textContent.trim();
                    bVal = b.cells[colIndex].textContent.trim();
                }
                
                return bVal - aVal;
            });
            
            // Append sorted rows
            rows.forEach(function(row) {
                tbody.appendChild(row);
            });
        }
    });
</script>
@endsection 