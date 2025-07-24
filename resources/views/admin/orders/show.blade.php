@extends('layouts.admin')

@section('title', 'تفاصيل الطلب')
@section('header', 'تفاصيل الطلب: ' . $order->order_number)

@push('styles')
<style>
/* تنسيقات محسنة لصفحة تفاصيل الطلب */
/* تعريف المتغيرات الأساسية */
:root {
    --primary-color: #4f46e5;
    --primary-light: #818cf8;
    --primary-dark: #3730a3;
    --secondary-color: #0ea5e9;
    --secondary-light: #38bdf8;
    --secondary-dark: #0369a1;
    --success-color: #10b981;
    --success-light: #34d399;
    --warning-color: #f59e0b;
    --warning-light: #fbbf24;
    --danger-color: #ef4444;
    --danger-light: #f87171;
    --neutral-50: #f9fafb;
    --neutral-100: #f3f4f6;
    --neutral-200: #e5e7eb;
    --neutral-300: #d1d5db;
    --neutral-400: #9ca3af;
    --neutral-500: #6b7280;
    --neutral-600: #4b5563;
    --neutral-700: #374151;
    --neutral-800: #1f2937;
    --neutral-900: #111827;
}

/* ===== تنسيقات الخلفيات والألوان ===== */
/* خلفيات التدرج */
.bg-gradient-to-r { background-image: linear-gradient(to right, var(--tw-gradient-stops)); }
.from-indigo-50 { --tw-gradient-from: #eef2ff; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(238, 242, 255, 0)); }
.to-blue-50 { --tw-gradient-to: #eff6ff; }
.from-teal-50 { --tw-gradient-from: #f0fdfa; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(240, 253, 250, 0)); }
.bg-gradient-to-br { background-image: linear-gradient(to bottom right, var(--tw-gradient-stops)); }
.from-white { --tw-gradient-from: #ffffff; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(255, 255, 255, 0)); }
.to-teal-50 { --tw-gradient-to: #f0fdfa; }
.from-gray-100 { --tw-gradient-from: #f3f4f6; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(243, 244, 246, 0)); }
.from-blue-50 { --tw-gradient-from: #eff6ff; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(239, 246, 255, 0)); }
.to-indigo-50 { --tw-gradient-to: #eef2ff; }
.from-blue-600 { --tw-gradient-from: #2563eb; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(37, 99, 235, 0)); }
.to-indigo-600 { --tw-gradient-to: #4f46e5; }
.from-red-50 { --tw-gradient-from: #fef2f2; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(254, 242, 242, 0)); }
.to-red-100 { --tw-gradient-to: #fee2e2; }
.to-blue-100 { --tw-gradient-to: #dbeafe; }
.from-green-50 { --tw-gradient-from: #f0fdf4; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(240, 253, 244, 0)); }
.from-indigo-50 { --tw-gradient-from: #eef2ff; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(238, 242, 255, 0)); }
.to-purple-50 { --tw-gradient-to: #faf5ff; }

/* تنسيقات الحدود المحسنة */
.border-blue-100 { border-color: #dbeafe; }
.border-teal-100 { border-color: #ccfbf1; }
.border-blue-200 { border-color: #bfdbfe; }
.border-blue-700 { border-color: #1d4ed8; }
.border-red-100 { border-color: #fee2e2; }
.border-red-200 { border-color: #fecaca; }
.border-yellow-100 { border-color: #fef3c7; }
.border-yellow-200 { border-color: #fde68a; }
.border-green-100 { border-color: #dcfce7; }
.border-green-200 { border-color: #bbf7d0; }
.border-indigo-100 { border-color: #e0e7ff; }
.border-indigo-200 { border-color: #c7d2fe; }
.border-purple-100 { border-color: #f3e8ff; }

/* تنسيقات الخلفيات المحسنة بألوان أكثر وضوحاً */
.bg-blue-50 { background-color: #eff6ff; }
.bg-blue-100 { background-color: #dbeafe; }
.bg-teal-50 { background-color: #f0fdfa; }
.bg-teal-100 { background-color: #ccfbf1; }
.bg-red-50 { background-color: #fef2f2; }
.bg-red-100 { background-color: #fee2e2; }
.bg-yellow-50 { background-color: #fffbeb; }
.bg-yellow-100 { background-color: #fef3c7; }
.bg-green-50 { background-color: #f0fdf4; }
.bg-green-100 { background-color: #dcfce7; }
.bg-indigo-50 { background-color: #eef2ff; }
.bg-indigo-100 { background-color: #e0e7ff; }
.bg-purple-50 { background-color: #faf5ff; }
.bg-purple-100 { background-color: #f3e8ff; }

/* ألوان النصوص المحسنة لتباين أفضل */
.text-blue-600 { color: #2563eb; }
.text-blue-700 { color: #1d4ed8; }
.text-teal-500 { color: #14b8a6; }
.text-teal-600 { color: #0d9488; }
.text-teal-700 { color: #0f766e; }
.text-red-500 { color: #ef4444; }
.text-red-600 { color: #dc2626; }
.text-yellow-500 { color: #eab308; }
.text-yellow-600 { color: #ca8a04; }
.text-green-500 { color: #22c55e; }
.text-green-600 { color: #16a34a; }
.text-indigo-400 { color: #818cf8; }
.text-indigo-500 { color: #6366f1; }
.text-indigo-600 { color: #4f46e5; }
.text-blue-400 { color: #60a5fa; }
.text-blue-500 { color: #3b82f6; }
.text-blue-600 { color: #2563eb; }
.text-blue-700 { color: #1d4ed8; }
.text-blue-800 { color: #1e40af; }

/* ===== تحسينات الحركة والتأثيرات ===== */
/* تحولات وانتقالات محسنة */
.transform { transform: translateZ(0); }
.hover\:-translate-y-1:hover { transform: translateY(-0.25rem); }
.transition-all { transition-property: all; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 300ms; }
.duration-300 { transition-duration: 300ms; }
.shadow-sm { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
.hover\:shadow-md:hover { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
.shadow-md { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }

/* تحولات التحويم المحسنة */
.hover\:border-blue-200:hover { border-color: #bfdbfe; }
.hover\:border-teal-200:hover { border-color: #99f6e4; }
.hover\:border-indigo-200:hover { border-color: #c7d2fe; }
.hover\:bg-blue-100:hover { background-color: #dbeafe; }
.hover\:bg-blue-500:hover { background-color: #3b82f6; }
.hover\:bg-teal-200:hover { background-color: #99f6e4; }

/* تنسيقات إضافية محسنة */
.line-clamp-1 { overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 1; }
.space-y-3 > :not([hidden]) ~ :not([hidden]) { --tw-space-y-reverse: 0; margin-top: calc(0.75rem * calc(1 - var(--tw-space-y-reverse))); margin-bottom: calc(0.75rem * var(--tw-space-y-reverse)); }

/* ===== تنسيقات أزرار النسخ والإشعارات ===== */
/* تنسيقات أزرار النسخ */
.copy-btn {
    cursor: pointer;
    position: relative;
    z-index: 10;
}

.copy-btn:hover {
    transform: translateY(-1px);
}

.copy-btn:active {
    transform: translateY(0);
}

/* تأثير النبض عند النقر */
.copy-btn.pulse {
    animation: pulse 1s cubic-bezier(0.4, 0, 0.6, 1) 1;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.8;
        transform: scale(1.1);
    }
}

/* تنسيق تلميحات الأزرار */
[data-tooltip] {
    position: relative;
    overflow: visible !important;
}

[data-tooltip]:hover::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    right: 50%;
    transform: translateX(50%);
    margin-bottom: 5px;
    padding: 5px 10px;
    background-color: rgba(0, 0, 0, 0.8);
    color: white;
    font-size: 0.75rem;
    border-radius: 4px;
    white-space: nowrap;
    z-index: 1000;
    opacity: 0;
    animation: fadeIn 0.3s ease-in-out forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateX(50%) translateY(5px);
    }
    to {
        opacity: 1;
        transform: translateX(50%) translateY(0);
    }
}

/* تنسيقات حاويات البطاقات */
.card-hover {
    transition: all 0.3s ease-in-out;
}

.card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* تنسيقات الإشعارات */
#notification-container {
    position: fixed;
    top: 20px;
    left: 20px;
    z-index: 9999;
    direction: rtl;
}

.notification {
    display: flex;
    align-items: center;
    background-color: white;
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 10px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transform: translateX(-120%);
    opacity: 0;
    transition: all 0.5s ease-in-out;
}

.notification.show {
    transform: translateX(0);
    opacity: 1;
}

.notification .icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 12px;
}

.notification .icon.success {
    background-color: #dcfce7;
    color: #16a34a;
}

.notification .icon.error {
    background-color: #fee2e2;
    color: #dc2626;
}

/* تحسينات إضافية للقراءة */
.data-label {
    font-weight: 600;
    color: var(--neutral-600);
}

.data-value {
    font-weight: 500;
    color: var(--neutral-800);
}

/* تحسين المربعات الصغيرة التي تعرض المعلومات */
.info-box {
    transition: all 0.3s ease;
    border: 1px solid var(--neutral-200);
}

.info-box:hover {
    border-color: var(--primary-light);
    background-color: #fafafa;
}
</style>
@endpush

@section('content')
    <!-- Set initial styles for cards to enable fade-in effect -->
    <style>
        .bg-white.rounded-lg {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease-out, transform 0.5s ease-out;
        }
        
        /* Enhanced card style with better shadows and hover effects */
        .bg-white.rounded-lg.shadow-lg {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }
        
        .bg-white.rounded-lg.shadow-lg:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transform: translateY(-4px);
        }
        
        /* Enhanced section headers */
        .border-b.bg-gradient-to-r {
            transition: all 0.3s ease;
        }
        
        /* Better contrast for data values */
        .text-gray-900 {
            color: #111827 !important;
        }
        
        /* Enhanced buttons */
        button[type="submit"] {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        button[type="submit"]:after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }
        
        button[type="submit"]:hover:after {
            animation: ripple 1s ease-out;
        }
        
        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }
            100% {
                transform: scale(100, 100);
                opacity: 0;
            }
        }
    </style>
    
    <div class="mb-6 bg-gradient-to-r from-blue-600 to-indigo-700 rounded-lg shadow-lg p-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-wrap items-center space-x-3 space-x-reverse">
                <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center px-4 py-2 border border-white rounded-md shadow-sm text-sm font-medium text-white bg-transparent hover:bg-white hover:text-indigo-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white">
                    <i class="fas fa-arrow-right ml-2"></i> العودة للطلبات
                </a>
                <a href="{{ route('admin.orders.invoice', $order->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-indigo-700 bg-white hover:bg-gray-100 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white">
                    <i class="fas fa-file-invoice ml-2"></i> عرض الفاتورة
                </a>
                <button id="copy-all-data" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-500 hover:bg-indigo-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-400">
                    <i class="fas fa-copy ml-2"></i> نسخ جميع البيانات
                </button>
            </div>
            <div class="flex items-center flex-wrap gap-2 mt-3 md:mt-0">
                <div class="order-number-badge px-4 py-2 bg-white bg-opacity-20 backdrop-filter backdrop-blur-lg rounded-lg text-white flex items-center">
                    <span class="font-bold ml-2">رقم الطلب:</span>
                    <span>{{ $order->order_number }}</span>
                    <button class="copy-btn mr-2 text-white hover:text-blue-200" data-value="{{ $order->order_number }}" data-tooltip="نسخ رقم الطلب">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $order->status == 'delivered' ? 'bg-green-100 text-green-800' : ($order->status == 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                    <span class="w-2 h-2 rounded-full {{ $order->status == 'delivered' ? 'bg-green-500' : ($order->status == 'cancelled' ? 'bg-red-500' : 'bg-blue-500') }} mr-2"></span>
                    @switch($order->status)
                        @case('pending')
                            قيد الانتظار
                            @break
                        @case('processing')
                            قيد المعالجة
                            @break
                        @case('shipped')
                            تم الشحن
                            @break
                        @case('delivered')
                            تم التسليم
                            @break
                        @case('cancelled')
                            ملغي
                            @break
                        @default
                            {{ $order->status }}
                    @endswitch
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $order->payment_status == 'paid' ? 'bg-green-100 text-green-800' : ($order->payment_status == 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                    <span class="w-2 h-2 rounded-full {{ $order->payment_status == 'paid' ? 'bg-green-500' : ($order->payment_status == 'failed' ? 'bg-red-500' : 'bg-yellow-500') }} mr-2"></span>
                    @switch($order->payment_status)
                        @case('pending')
                            الدفع قيد الانتظار
                            @break
                        @case('paid')
                            تم الدفع
                            @break
                        @case('failed')
                            فشل الدفع
                            @break
                        @case('refunded')
                            تم الاسترجاع
                            @break
                        @default
                            {{ $order->payment_status }}
                    @endswitch
                </span>
            </div>
        </div>
    </div>

    <!-- لوحة الإحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-lg p-4 border-r-4 border-blue-500 hover:shadow-xl transition-shadow duration-300">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500">إجمالي الطلب</p>
                    <p class="text-xl font-bold text-gray-800">{{ number_format($order->grand_total, 2) }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-shopping-cart text-blue-500"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100">
                <div class="flex justify-between items-center text-xs text-gray-500">
                    <span>الضريبة</span>
                    <span>{{ number_format($order->tax_amount, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-4 border-r-4 border-green-500 hover:shadow-xl transition-shadow duration-300">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500">عدد المنتجات</p>
                    <p class="text-xl font-bold text-gray-800">{{ $order->items->count() }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-box text-green-500"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100">
                <div class="flex justify-between items-center text-xs text-gray-500">
                    <span>إجمالي الكمية</span>
                    <span>{{ $order->items->sum('quantity') }} قطعة</span>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-4 border-r-4 border-purple-500 hover:shadow-xl transition-shadow duration-300">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500">تاريخ الطلب</p>
                    <p class="text-xl font-bold text-gray-800">{{ $order->created_at->format('Y-m-d') }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-calendar-alt text-purple-500"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100">
                <div class="flex justify-between items-center text-xs text-gray-500">
                    <span>الوقت</span>
                    <span>{{ $order->created_at->format('H:i') }}</span>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-4 border-r-4 border-yellow-500 hover:shadow-xl transition-shadow duration-300">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500">طريقة الدفع</p>
                    <p class="text-xl font-bold text-gray-800">{{ $order->payment_method }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-credit-card text-yellow-500"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100">
                <div class="flex justify-between items-center text-xs text-gray-500">
                    <span>حالة الدفع</span>
                    <span class="{{ $order->payment_status == 'paid' ? 'text-green-500' : ($order->payment_status == 'failed' ? 'text-red-500' : 'text-yellow-500') }}">
                        @switch($order->payment_status)
                            @case('paid')
                                تم الدفع
                                @break
                            @case('pending')
                                قيد الانتظار
                                @break
                            @case('failed')
                                فشل الدفع
                                @break
                            @default
                                {{ $order->payment_status }}
                        @endswitch
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- معلومات الطلب والمنتجات -->
        <div class="lg:col-span-2 space-y-6">
            <!-- معلومات الطلب -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                <div class="px-6 py-4 border-b bg-gradient-to-r from-indigo-100 to-blue-100">
                    <h3 class="text-lg font-semibold text-indigo-800 flex items-center">
                        <i class="fas fa-info-circle text-indigo-600 mr-2"></i>
                        معلومات الطلب
                    </h3>
                </div>
                <div class="p-6 bg-gradient-to-br from-white to-blue-50">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                        <div class="bg-white p-4 rounded-lg border border-blue-100 shadow-sm hover:shadow-md hover:border-blue-200 transition-all duration-300 transform hover:-translate-y-1">
                            <dt class="text-sm font-medium text-gray-600 flex items-center mb-2">
                                <div class="bg-blue-100 p-2 rounded-full mr-3">
                                    <i class="fas fa-hashtag text-blue-600"></i>
                                </div>
                                <span>رقم الطلب</span>
                                <button class="copy-btn mr-2 text-blue-500 hover:text-blue-700" data-value="{{ $order->order_number }}" data-tooltip="نسخ رقم الطلب">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </dt>
                            <dd class="mt-1 text-lg text-gray-900 font-bold flex items-center">
                                <span class="bg-blue-50 px-3 py-1 rounded-md border border-blue-100 w-full text-center">{{ $order->order_number }}</span>
                            </dd>
                        </div>
                        <div class="bg-white p-4 rounded-lg border border-purple-100 shadow-sm hover:shadow-md hover:border-purple-200 transition-all duration-300 transform hover:-translate-y-1">
                            <dt class="text-sm font-medium text-gray-600 flex items-center mb-2">
                                <div class="bg-purple-100 p-2 rounded-full mr-3">
                                    <i class="fas fa-calendar-alt text-purple-600"></i>
                                </div>
                                <span>تاريخ الطلب</span>
                                <button class="copy-btn mr-2 text-purple-500 hover:text-purple-700" data-value="{{ $order->created_at->format('Y-m-d H:i') }}" data-tooltip="نسخ تاريخ الطلب">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </dt>
                            <dd class="mt-1 text-lg text-gray-900 font-medium flex items-center">
                                <span class="bg-purple-50 px-3 py-1 rounded-md border border-purple-100 w-full text-center">{{ $order->created_at->format('Y-m-d H:i') }}</span>
                            </dd>
                            <div class="mt-2 text-xs text-gray-500">
                                <span class="bg-gray-100 px-2 py-1 rounded">{{ $order->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-lg border border-green-100 shadow-sm hover:shadow-md hover:border-green-200 transition-all duration-300 transform hover:-translate-y-1">
                            <dt class="text-sm font-medium text-gray-600 flex items-center mb-2">
                                <div class="bg-green-100 p-2 rounded-full mr-3">
                                    <i class="fas fa-credit-card text-green-600"></i>
                                </div>
                                <span>طريقة الدفع</span>
                                <button class="copy-btn mr-2 text-green-500 hover:text-green-700" data-value="{{ $order->payment_method }}" data-tooltip="نسخ طريقة الدفع">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </dt>
                            <dd class="mt-1 text-lg text-gray-900 font-medium flex items-center">
                                <span class="bg-green-50 px-3 py-1 rounded-md border border-green-100 w-full text-center flex items-center justify-center">
                                    @if($order->payment_method == 'credit_card')
                                        <i class="far fa-credit-card ml-2"></i>
                                    @elseif($order->payment_method == 'bank_transfer')
                                        <i class="fas fa-university ml-2"></i>
                                    @elseif($order->payment_method == 'cash_on_delivery')
                                        <i class="fas fa-money-bill-wave ml-2"></i>
                                    @else
                                        <i class="fas fa-money-check ml-2"></i>
                                    @endif
                                    {{ $order->payment_method }}
                                </span>
                            </dd>
                            <div class="mt-2 text-xs text-gray-500 flex items-center">
                                <span class="inline-flex items-center px-2 py-1 rounded-full {{ $order->payment_status == 'paid' ? 'bg-green-100 text-green-800' : ($order->payment_status == 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    <span class="w-2 h-2 rounded-full {{ $order->payment_status == 'paid' ? 'bg-green-500' : ($order->payment_status == 'failed' ? 'bg-red-500' : 'bg-yellow-500') }} mr-1"></span>
                                    {{ $order->payment_status }}
                                </span>
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-lg border border-indigo-100 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all duration-300 transform hover:-translate-y-1">
                            <dt class="text-sm font-medium text-gray-600 flex items-center mb-2">
                                <div class="bg-indigo-100 p-2 rounded-full mr-3">
                                    <i class="fas fa-truck text-indigo-600"></i>
                                </div>
                                <span>رقم التتبع</span>
                                @if($order->tracking_number)
                                <button class="copy-btn mr-2 text-indigo-500 hover:text-indigo-700" data-value="{{ $order->tracking_number }}" data-tooltip="نسخ رقم التتبع">
                                    <i class="fas fa-copy"></i>
                                </button>
                                @endif
                            </dt>
                            <dd class="mt-1 text-lg text-gray-900 font-medium flex items-center">
                                <span class="bg-indigo-50 px-3 py-1 rounded-md border border-indigo-100 w-full text-center">
                                    {{ $order->tracking_number ?? 'غير متوفر' }}
                                </span>
                            </dd>
                            <div class="mt-2 text-xs text-gray-500 flex items-center">
                                <span class="inline-flex items-center px-2 py-1 rounded-full {{ $order->status == 'delivered' ? 'bg-green-100 text-green-800' : ($order->status == 'shipped' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                    <span class="w-2 h-2 rounded-full {{ $order->status == 'delivered' ? 'bg-green-500' : ($order->status == 'shipped' ? 'bg-blue-500' : 'bg-gray-500') }} mr-1"></span>
                                    {{ $order->status }}
                                </span>
                            </div>
                        </div>
                        @if($order->cancellation_reason)
                            <div class="md:col-span-2 bg-white p-4 rounded-lg border border-red-200 shadow-sm">
                                <dt class="text-sm font-medium text-gray-600 flex items-center mb-2">
                                    <div class="bg-red-100 p-2 rounded-full mr-3">
                                        <i class="fas fa-ban text-red-600"></i>
                                    </div>
                                    <span>سبب الإلغاء</span>
                                    <button class="copy-btn mr-2 text-red-500 hover:text-red-700" data-value="{{ $order->cancellation_reason }}" data-tooltip="نسخ سبب الإلغاء">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 bg-red-50 p-3 rounded-md border border-red-100">{{ $order->cancellation_reason }}</dd>
                                <div class="mt-2 text-xs text-gray-500">
                                    <span class="bg-gray-100 px-2 py-1 rounded">تم الإلغاء في: {{ $order->cancelled_at ? $order->cancelled_at->format('Y-m-d H:i') : 'غير محدد' }}</span>
                                </div>
                            </div>
                        @endif
                        @if($order->admin_notes)
                            <div class="md:col-span-2 bg-white p-4 rounded-lg border border-yellow-200 shadow-sm">
                                <dt class="text-sm font-medium text-gray-600 flex items-center mb-2">
                                    <div class="bg-yellow-100 p-2 rounded-full mr-3">
                                        <i class="fas fa-sticky-note text-yellow-600"></i>
                                    </div>
                                    <span>ملاحظات إدارية</span>
                                    <button class="copy-btn mr-2 text-yellow-500 hover:text-yellow-700" data-value="{{ $order->admin_notes }}" data-tooltip="نسخ الملاحظات الإدارية">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 bg-yellow-50 p-3 rounded-md border border-yellow-100">{{ $order->admin_notes }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
            
            <!-- المنتجات -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                <div class="px-6 py-4 border-b bg-gradient-to-r from-indigo-50 to-blue-50">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-shopping-cart text-blue-600 mr-2"></i>
                        <span class="relative">
                            المنتجات
                            <span class="absolute -top-1 -right-6 bg-blue-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ $order->items->count() }}</span>
                        </span>
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-100 to-blue-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-blue-700 uppercase tracking-wider">
                                    المنتج
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-blue-700 uppercase tracking-wider">
                                    السعر
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-blue-700 uppercase tracking-wider">
                                    الكمية
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-blue-700 uppercase tracking-wider">
                                    الإجمالي
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($order->items as $item)
                                <tr class="hover:bg-blue-50 transition-colors duration-200">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="h-16 w-16 flex-shrink-0 rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                                                @if($item->product && $item->product->main_image)
                                                    <img class="h-16 w-16 object-cover" src="{{ asset('storage/' . $item->product->main_image) }}" alt="{{ $item->product_name }}">
                                                @else
                                                    <div class="h-16 w-16 bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center">
                                                        <i class="fas fa-box text-blue-400"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="mr-4 flex flex-col">
                                                <div class="flex items-center">
                                                    <div class="text-sm font-bold text-gray-900 hover:text-blue-600 transition-colors duration-200">{{ $item->product_name }}</div>
                                                    <button class="copy-btn mr-2 text-blue-500 hover:text-blue-700" data-value="{{ $item->product_name }}" data-tooltip="نسخ اسم المنتج">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1 flex items-center">
                                                    <span class="bg-gray-100 px-2 py-1 rounded-md border border-gray-200 shadow-sm">
                                                        <i class="fas fa-barcode text-gray-400 ml-1"></i>
                                                        {{ $item->product_sku ?? 'SKU غير متوفر' }}
                                                    </span>
                                                    @if($item->product && $item->product->category)
                                                        <span class="mr-2 bg-blue-50 text-blue-600 px-2 py-1 rounded-md text-xs border border-blue-100">
                                                            <i class="fas fa-tag text-blue-400 ml-1"></i>
                                                            {{ $item->product->category->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($item->product && $item->product->description)
                                                    <div class="text-xs text-gray-500 mt-1 line-clamp-1">
                                                        {{ \Illuminate\Support\Str::limit($item->product->description, 50) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button class="copy-btn bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-600 hover:from-blue-100 hover:to-indigo-100 px-3 py-1 rounded-full transition-all duration-200 border border-blue-200 shadow-sm flex items-center" data-value="{{ number_format($item->unit_price, 2) }}" data-tooltip="نسخ السعر">
                                            <i class="fas fa-tag text-blue-400 ml-1"></i>
                                            {{ number_format($item->unit_price, 2) }}
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button class="copy-btn bg-gradient-to-r from-green-50 to-teal-50 text-green-600 hover:from-green-100 hover:to-teal-100 px-3 py-1 rounded-full transition-all duration-200 border border-green-200 shadow-sm flex items-center" data-value="{{ $item->quantity }}" data-tooltip="نسخ الكمية">
                                            <i class="fas fa-cubes text-green-400 ml-1"></i>
                                            {{ $item->quantity }}
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button class="copy-btn bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-600 hover:from-indigo-100 hover:to-purple-100 px-3 py-1 rounded-full transition-all duration-200 border border-indigo-200 shadow-sm flex items-center" data-value="{{ number_format($item->unit_price * $item->quantity, 2) }}" data-tooltip="نسخ الإجمالي">
                                            <i class="fas fa-calculator text-indigo-400 ml-1"></i>
                                            {{ number_format($item->unit_price * $item->quantity, 2) }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gradient-to-r from-gray-50 to-blue-50">
                            <tr>
                                <td colspan="3" class="px-6 py-3 text-left text-sm font-medium text-gray-600">
                                    <div class="flex items-center">
                                        <i class="fas fa-receipt text-blue-500 ml-2"></i>
                                        المجموع الفرعي
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                                    <span class="bg-gradient-to-r from-gray-100 to-blue-100 px-4 py-2 rounded-lg border border-gray-200 shadow-sm">{{ number_format($order->subtotal, 2) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-6 py-3 text-left text-sm font-medium text-gray-600">
                                    <div class="flex items-center">
                                        <i class="fas fa-truck text-blue-500 ml-2"></i>
                                        الشحن
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                                    <span class="bg-gradient-to-r from-gray-100 to-blue-100 px-4 py-2 rounded-lg border border-gray-200 shadow-sm">{{ number_format($order->shipping_cost, 2) }}</span>
                                </td>
                            </tr>
                            @if($order->discount > 0)
                                <tr>
                                    <td colspan="3" class="px-6 py-3 text-left text-sm font-medium text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-percent text-red-500 ml-2"></i>
                                            الخصم
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 text-right text-sm font-medium text-red-600">
                                        <span class="bg-gradient-to-r from-red-50 to-red-100 px-4 py-2 rounded-lg border border-red-200 shadow-sm">- {{ number_format($order->discount, 2) }}</span>
                                    </td>
                                </tr>
                            @endif
                            @if($order->tax > 0)
                                <tr>
                                    <td colspan="3" class="px-6 py-3 text-left text-sm font-medium text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-file-invoice-dollar text-blue-500 ml-2"></i>
                                            الضريبة
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                                        <span class="bg-gradient-to-r from-gray-100 to-blue-100 px-4 py-2 rounded-lg border border-gray-200 shadow-sm">{{ number_format($order->tax, 2) }}</span>
                                    </td>
                                </tr>
                            @endif
                            <tr class="bg-gradient-to-r from-blue-50 to-indigo-50">
                                <td colspan="3" class="px-6 py-4 text-left text-base font-bold text-blue-700">
                                    <div class="flex items-center">
                                        <i class="fas fa-money-bill-wave text-blue-600 ml-2 text-lg"></i>
                                        الإجمالي
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right text-base font-bold text-white">
                                    <span class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-2 rounded-lg shadow-md border border-blue-700">{{ number_format($order->grand_total, 2) }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            <!-- معلومات الشحن -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                <div class="px-6 py-4 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-shipping-fast text-blue-500 mr-2"></i>
                        معلومات الشحن والتتبع
                    </h3>
                </div>
                
                @if($order->hasShipments())
                    <div class="p-6">
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-base font-medium text-gray-900">حالة الشحن</h4>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $order->shipment_status_color }}">
                                    {{ $order->shipment_status_text }}
                                </span>
                            </div>
                            
                            <!-- شريط تقدم الشحن -->
                            <div class="relative pt-1">
                                <div class="flex mb-2 items-center justify-between">
                                    <div>
                                        <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-200">
                                            التقدم
                                        </span>
                                    </div>
                                    <div class="text-left">
                                        <span class="text-xs font-semibold inline-block text-blue-600">
                                            {{ $order->shipping_progress }}%
                                        </span>
                                    </div>
                                </div>
                                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-100">
                                    <div style="width:{{ $order->shipping_progress }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-500"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- جدول الشحنات -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            رقم الشحنة
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            شركة الشحن
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            رقم التتبع
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            الحالة
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            التاريخ
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            الإجراءات
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($order->shipments as $shipment)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('admin.shipments.show', $shipment->id) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                                    #{{ $shipment->id }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $shipment->shippingCompany->name ?? 'غير متوفر' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($shipment->tracking_number)
                                                    <div class="flex items-center">
                                                        <span>{{ $shipment->tracking_number }}</span>
                                                        @if($shipment->tracking_url)
                                                            <a href="{{ $shipment->tracking_url }}" target="_blank" class="text-blue-500 hover:text-blue-700 mr-2">
                                                                <i class="fas fa-external-link-alt"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-gray-400">غير متوفر</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $shipment->status_color }}">
                                                    {{ $shipment->status_text }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ optional($shipment->shipped_at)->format('Y-m-d') ?? 'غير متوفر' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                                <a href="{{ route('admin.shipments.tracking-history', $shipment->id) }}" class="text-indigo-600 hover:text-indigo-900 ml-3">
                                                    <i class="fas fa-history"></i>
                                                </a>
                                                <a href="{{ route('admin.shipments.show', $shipment->id) }}" class="text-blue-600 hover:text-blue-900">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="p-6">
                        <div class="bg-yellow-50 border-r-4 border-yellow-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-yellow-400"></i>
                                </div>
                                <div class="mr-3">
                                    <p class="text-sm text-yellow-700">
                                        لا توجد شحنات مرتبطة بهذا الطلب حتى الآن.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 flex justify-center">
                            <a href="{{ route('admin.shipments.create', ['order_id' => $order->id]) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-plus ml-2"></i>
                                إنشاء شحنة جديدة
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- الإجراءات والتحديثات -->
        <div class="lg:col-span-1 space-y-6">
            <!-- تحديث حالة الطلب -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                <div class="px-6 py-4 border-b bg-gradient-to-r from-gray-50 to-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-edit text-blue-500 mr-2"></i>
                        تحديث حالة الطلب
                    </h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                            <select name="status" id="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>تم الشحن</option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>تم التسليم</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                            <textarea name="notes" id="notes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('notes', $order->admin_notes) }}</textarea>
                        </div>
                        <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            تحديث الحالة
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- تحديث حالة الدفع -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                <div class="px-6 py-4 border-b bg-gradient-to-r from-gray-50 to-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-credit-card text-green-500 mr-2"></i>
                        تحديث حالة الدفع
                    </h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.orders.payment-status', $order->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">حالة الدفع</label>
                            <select name="payment_status" id="payment_status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>مدفوع</option>
                                <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>فشل الدفع</option>
                                <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>مسترجع</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="payment_notes" class="block text-sm font-medium text-gray-700 mb-1">ملاحظات الدفع</label>
                            <textarea name="payment_notes" id="payment_notes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('payment_notes', $order->payment_notes) }}</textarea>
                        </div>
                        <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            تحديث حالة الدفع
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- إنشاء شحنة -->
            @if(!$order->shipment && $order->status != 'cancelled')
                <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                    <div class="px-6 py-4 border-b bg-gradient-to-r from-gray-50 to-gray-100">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <i class="fas fa-shipping-fast text-purple-500 mr-2"></i>
                            إنشاء شحنة
                        </h3>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('admin.orders.createShipment', $order->id) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="shipping_company_id" class="block text-sm font-medium text-gray-700 mb-1">شركة الشحن</label>
                                <select name="shipping_company_id" id="shipping_company_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500" required>
                                    <option value="">اختر شركة الشحن</option>
                                    @foreach($shippingCompanies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-1">رقم التتبع</label>
                                <input type="text" name="tracking_number" id="tracking_number" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500" required>
                            </div>
                            <div class="mb-4">
                                <label for="expected_delivery_date" class="block text-sm font-medium text-gray-700 mb-1">تاريخ التسليم المتوقع</label>
                                <input type="date" name="expected_delivery_date" id="expected_delivery_date" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500" required>
                            </div>
                            <div class="mb-4">
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                                <textarea name="notes" id="notes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"></textarea>
                            </div>
                            <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                إنشاء الشحنة
                            </button>
                        </form>
                    </div>
                </div>
            @endif
            
            <!-- إلغاء الطلب -->
            @if(!in_array($order->status, ['shipped', 'delivered', 'cancelled']))
                <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                    <div class="px-6 py-4 border-b bg-gradient-to-r from-gray-50 to-gray-100">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <i class="fas fa-times-circle text-red-500 mr-2"></i>
                            إلغاء الطلب
                        </h3>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('admin.orders.cancelOrder', $order->id) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 mb-1">سبب الإلغاء</label>
                                <textarea name="cancellation_reason" id="cancellation_reason" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500" required></textarea>
                            </div>
                            <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="return confirm('هل أنت متأكد من إلغاء هذا الطلب؟')">
                                إلغاء الطلب
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .tooltip {
            position: relative;
            display: inline-block;
        }
        
        .tooltip .tooltiptext {
            visibility: hidden;
            width: 120px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -60px;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .tooltip .tooltiptext::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #333 transparent transparent transparent;
        }
        
        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }
        
        .copy-success {
            background-color: #4CAF50 !important;
        }
        
        /* تأثيرات الحركة */
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
        
        /* تنسيق الإشعارات */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            transform: translateY(-100px);
            opacity: 0;
            transition: all 0.5s ease;
            z-index: 1000;
        }
        
        .notification.show {
            transform: translateY(0);
            opacity: 1;
        }
        
        .notification .icon {
            margin-left: 15px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .notification .success {
            background-color: #d1fae5;
            color: #059669;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تحسين ظهور البطاقات مع تأثيرات حركية
            document.querySelectorAll('.bg-white.rounded-lg').forEach(function(card, index) {
                card.classList.add('card-hover');
                // إضافة تأخير متزايد لكل بطاقة للظهور بشكل متتالي
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
            
            // إنشاء عنصر الإشعارات
            const notificationContainer = document.createElement('div');
            notificationContainer.id = 'notification-container';
            document.body.appendChild(notificationContainer);
            
            // دالة محسنة لعرض الإشعارات
            function showNotification(message, type = 'success') {
                const notification = document.createElement('div');
                notification.className = 'notification';
                
                const iconDiv = document.createElement('div');
                iconDiv.className = 'icon ' + type;
                
                const icon = document.createElement('i');
                icon.className = type === 'success' ? 'fas fa-check' : 'fas fa-exclamation-triangle';
                iconDiv.appendChild(icon);
                
                const textDiv = document.createElement('div');
                textDiv.textContent = message;
                
                notification.appendChild(iconDiv);
                notification.appendChild(textDiv);
                
                notificationContainer.appendChild(notification);
                
                // عرض الإشعار مع تأخير بسيط للحصول على تأثير أفضل
                setTimeout(() => {
                    notification.classList.add('show');
                }, 10);
                
                // إخفاء الإشعار بعد 3 ثوان
                setTimeout(() => {
                    notification.classList.remove('show');
                    setTimeout(() => {
                        notification.remove();
                    }, 500);
                }, 3000);
            }
            
            // دالة محسنة لنسخ النص إلى الحافظة
            function copyToClipboard(text, button) {
                // استخدام الواجهة الحديثة لنسخ النصوص
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(function() {
                        // تأثير النبض للزر
                        button.classList.add('pulse');
                        
                        // إظهار إشعار النجاح
                        showNotification('تم نسخ النص بنجاح!', 'success');
                        
                        // إزالة تأثير النبض بعد ثانية واحدة
                        setTimeout(function() {
                            button.classList.remove('pulse');
                        }, 1000);
                    }).catch(function(err) {
                        console.error('خطأ في النسخ: ', err);
                        showNotification('حدث خطأ أثناء النسخ!', 'error');
                    });
                } else {
                    // طريقة بديلة للمتصفحات التي لا تدعم واجهة النسخ الحديثة
                    try {
                        // إنشاء عنصر نصي مؤقت
                        const textArea = document.createElement('textarea');
                        textArea.value = text;
                        
                        // إضافة التنسيقات اللازمة لإخفاء العنصر
                        textArea.style.position = 'fixed';
                        textArea.style.opacity = '0';
                        textArea.style.left = '-999999px';
                        textArea.style.top = '-999999px';
                        document.body.appendChild(textArea);
                        
                        // تحديد النص ونسخه
                        textArea.focus();
                        textArea.select();
                        const successful = document.execCommand('copy');
                        
                        // إزالة العنصر المؤقت
                        document.body.removeChild(textArea);
                        
                        if (successful) {
                            // تأثير النبض للزر
                            button.classList.add('pulse');
                            
                            // إظهار إشعار النجاح
                            showNotification('تم نسخ النص بنجاح!', 'success');
                            
                            // إزالة تأثير النبض بعد ثانية واحدة
                            setTimeout(function() {
                                button.classList.remove('pulse');
                            }, 1000);
                        } else {
                            showNotification('حدث خطأ أثناء النسخ!', 'error');
                        }
                    } catch (err) {
                        console.error('خطأ في النسخ: ', err);
                        showNotification('حدث خطأ أثناء النسخ!', 'error');
                    }
                }
            }
            
            // إضافة حدث النقر لجميع أزرار النسخ
            document.querySelectorAll('.copy-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    if (value) {
                        copyToClipboard(value, this);
                    } else {
                        showNotification('لا توجد بيانات للنسخ!', 'error');
                    }
                });
                
                // إضافة تأثير التحويم لعرض التلميح
                if (!button.getAttribute('data-tooltip-setup')) {
                    button.setAttribute('data-tooltip-setup', 'true');
                    
                    // تحسين عرض التلميح عند تمرير المؤشر
                    button.addEventListener('mouseenter', function() {
                        button.style.opacity = '0.9';
                    });
                    
                    button.addEventListener('mouseleave', function() {
                        button.style.opacity = '1';
                    });
                }
            });
            
            // دالة لنسخ جميع بيانات الطلب
            if (document.getElementById('copy-all-data')) {
                document.getElementById('copy-all-data').addEventListener('click', function() {
                    // تجميع جميع البيانات ذات الصلة
                    const orderData = {
                        'رقم الطلب': '{{ $order->order_number }}',
                        'تاريخ الطلب': '{{ $order->created_at->format("Y-m-d H:i") }}',
                        'حالة الطلب': '{{ $order->status }}',
                        'حالة الدفع': '{{ $order->payment_status }}',
                        'طريقة الدفع': '{{ $order->payment_method }}',
                        'رقم التتبع': '{{ $order->tracking_number ?? "غير متوفر" }}',
                        'اسم العميل': '{{ $order->shipping_name }}',
                        'البريد الإلكتروني': '{{ $order->shipping_email }}',
                        'رقم الهاتف': '{{ $order->shipping_phone }}',
                        'العنوان': '{{ $order->formattedShippingAddress }}',
                        'إجمالي الطلب': '{{ number_format($order->grand_total, 2) }}'
                    };
                    
                    // تنسيق البيانات كنص
                    let allDataText = '=== معلومات الطلب ===\n';
                    
                    for (const [key, value] of Object.entries(orderData)) {
                        allDataText += `${key}: ${value}\n`;
                    }
                    
                    // إضافة معلومات المنتجات
                    allDataText += '\n=== المنتجات ===\n';
                    
                    @foreach($order->items as $item)
                        allDataText += '{{ $item->product_name }} - {{ $item->quantity }} × {{ number_format($item->unit_price, 2) }} = {{ number_format($item->unit_price * $item->quantity, 2) }}\n';
                    @endforeach
                    
                    // إضافة معلومات الإجماليات
                    allDataText += '\n=== الإجماليات ===\n';
                    allDataText += 'المجموع الفرعي: {{ number_format($order->subtotal, 2) }}\n';
                    allDataText += 'الشحن: {{ number_format($order->shipping_cost, 2) }}\n';
                    @if($order->discount > 0)
                        allDataText += 'الخصم: {{ number_format($order->discount, 2) }}\n';
                    @endif
                    @if($order->tax > 0)
                        allDataText += 'الضريبة: {{ number_format($order->tax, 2) }}\n';
                    @endif
                    allDataText += 'الإجمالي: {{ number_format($order->grand_total, 2) }}\n';
                    
                    // نسخ إلى الحافظة
                    copyToClipboard(allDataText, this);
                });
            }
            
            // تحسين تجربة المستخدم للنماذج
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    const submitButton = this.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.disabled = true;
                        
                        // إضافة أيقونة تحميل
                        const originalText = submitButton.innerHTML;
                        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> جاري التنفيذ...';
                        
                        // إعادة تمكين الزر بعد 5 ثوان في حالة حدوث خطأ
                        setTimeout(() => {
                            if (submitButton.disabled) {
                                submitButton.disabled = false;
                                submitButton.innerHTML = originalText;
                                showNotification('استغرقت العملية وقتًا طويلاً. يرجى المحاولة مرة أخرى.', 'error');
                            }
                        }, 5000);
                    }
                });
            });
            
            // إضافة تأثيرات تحسين تجربة المستخدم
            // إضافة الفئات للعناصر المهمة
            document.querySelectorAll('.order-info-label').forEach(el => {
                el.classList.add('data-label');
            });
            
            document.querySelectorAll('.order-info-value').forEach(el => {
                el.classList.add('data-value');
            });
            
            // تحسين مربعات المعلومات
            document.querySelectorAll('.bg-gray-50').forEach(el => {
                el.classList.add('info-box');
            });
            
            // تحسين وضوح الجداول
            document.querySelectorAll('table').forEach(table => {
                table.classList.add('enhanced-table');
                
                // إضافة تأثير التحويم على صفوف الجدول
                table.querySelectorAll('tbody tr').forEach(row => {
                    row.addEventListener('mouseenter', function() {
                        this.style.backgroundColor = 'var(--neutral-50)';
                    });
                    
                    row.addEventListener('mouseleave', function() {
                        this.style.backgroundColor = '';
                    });
                });
            });
        });
    </script>
@endpush 