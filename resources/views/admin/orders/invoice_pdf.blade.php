<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة - {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            direction: rtl;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 30px;
        }
        .invoice-header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .invoice-header:after {
            content: "";
            display: table;
            clear: both;
        }
        .invoice-title {
            float: right;
        }
        .invoice-title h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .invoice-title p {
            margin: 5px 0 0;
            color: #666;
        }
        .company-details {
            float: left;
            text-align: left;
        }
        .company-details h2 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .company-details p {
            margin: 5px 0 0;
            color: #666;
        }
        .invoice-info {
            margin-bottom: 30px;
        }
        .invoice-info:after {
            content: "";
            display: table;
            clear: both;
        }
        .client-info {
            float: right;
            width: 50%;
        }
        .shipping-info {
            float: left;
            width: 50%;
            text-align: left;
        }
        .info-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .info-content {
            color: #666;
            line-height: 1.5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 10px;
            text-align: right;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f8f8;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            color: #666;
        }
        .total-row {
            font-weight: bold;
        }
        .payment-info {
            margin-bottom: 30px;
        }
        .payment-info:after {
            content: "";
            display: table;
            clear: both;
        }
        .payment-details {
            float: right;
            width: 50%;
        }
        .shipping-details {
            float: left;
            width: 50%;
            text-align: left;
        }
        .invoice-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
        }
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- رأس الفاتورة -->
        <div class="invoice-header">
            <div class="invoice-title">
                <h1>فاتورة</h1>
                <p>رقم الفاتورة: INV-{{ $order->order_number }}</p>
                <p>تاريخ الطلب: {{ $order->created_at->format('Y-m-d') }}</p>
                <p>رقم الطلب: {{ $order->order_number }}</p>
            </div>
            <div class="company-details">
                <h2>{{ config('app.name') }}</h2>
                <p>info@marca.com</p>
                <p>+966 12 345 6789</p>
            </div>
        </div>
        
        <!-- معلومات العميل والشحن -->
        <div class="invoice-info">
            <div class="client-info">
                <div class="info-title">معلومات العميل</div>
                <div class="info-content">
                    <p><strong>{{ $order->shipping_name }}</strong></p>
                    <p>{{ $order->shipping_email }}</p>
                    <p>{{ $order->shipping_phone }}</p>
                    <p>{{ $order->formattedShippingAddress }}</p>
                </div>
            </div>
            <div class="shipping-info">
                <div class="info-title">عنوان الشحن</div>
                <div class="info-content">
                    <p>{{ $order->shipping_address }}</p>
                    <p>{{ $order->shipping_city }}, {{ $order->shipping_country }}</p>
                    @if($order->shipping_zip)
                        <p>{{ $order->shipping_zip }}</p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- المنتجات -->
        <table>
            <thead>
                <tr>
                    <th>المنتج</th>
                    <th>السعر</th>
                    <th>الكمية</th>
                    <th class="text-left">الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product_name }}</strong>
                            @if($item->product_sku)
                                <br><small>{{ $item->product_sku }}</small>
                            @endif
                        </td>
                        <td>{{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td class="text-left">{{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right">المجموع الفرعي</td>
                    <td class="text-left">{{ number_format($order->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right">الشحن</td>
                    <td class="text-left">{{ number_format($order->shipping_cost, 2) }}</td>
                </tr>
                @if($order->discount > 0)
                    <tr>
                        <td colspan="3" class="text-right">الخصم</td>
                        <td class="text-left">- {{ number_format($order->discount, 2) }}</td>
                    </tr>
                @endif
                @if($order->tax > 0)
                    <tr>
                        <td colspan="3" class="text-right">الضريبة</td>
                        <td class="text-left">{{ number_format($order->tax, 2) }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td colspan="3" class="text-right">الإجمالي</td>
                    <td class="text-left">{{ number_format($order->grand_total, 2) }}</td>
                </tr>
            </tfoot>
        </table>
        
        <!-- معلومات الدفع والشحن -->
        <div class="payment-info">
            <div class="payment-details">
                <div class="info-title">معلومات الدفع</div>
                <div class="info-content">
                    <p>طريقة الدفع: {{ $order->payment_method }}</p>
                    <p>حالة الدفع: 
                        @switch($order->payment_status)
                            @case('pending')
                                قيد الانتظار
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
                    </p>
                </div>
            </div>
            <div class="shipping-details">
                <div class="info-title">معلومات الشحن</div>
                <div class="info-content">
                    <p>طريقة الشحن: {{ $order->shipping_method ?? 'التوصيل القياسي' }}</p>
                    @if($order->tracking_number)
                        <p>رقم التتبع: {{ $order->tracking_number }}</p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- تذييل الفاتورة -->
        <div class="invoice-footer">
            <p>شكراً لطلبك من {{ config('app.name') }}</p>
            <p>إذا كان لديك أي استفسارات، يرجى التواصل معنا على info@marca.com</p>
        </div>
    </div>
</body>
</html> 