<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاکتور #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'Vazirmatn', sans-serif;
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th,
        .items-table td {
            padding: 12px;
            text-align: right;
            border-bottom: 1px solid #ddd;
        }
        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .total-section {
            text-align: left;
            margin-top: 20px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>فاکتور #{{ $order->id }}</h1>
        <p>تاریخ: {{ $order->created_at->format('Y/m/d H:i') }}</p>
        <p>وضعیت: {{ match($order->status->value) {
            'paid' => 'پرداخت شده',
            'pending' => 'در انتظار پرداخت',
            'failed' => 'ناموفق',
            'cancelled' => 'لغو شده',
            default => $order->status->value
        } }}</p>
    </div>

    <div class="info-section">
        <div>
            <h3>اطلاعات مشتری</h3>
            <p>نام: {{ $order->user->name }}</p>
            <p>ایمیل: {{ $order->user->email }}</p>
            @if($order->user->mobile)
                <p>موبایل: {{ $order->user->mobile }}</p>
            @endif
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>عنوان</th>
                <th>قیمت</th>
                <th>تعداد</th>
                <th>جمع</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        @if($item->orderable)
                            @if($item->orderable_type === 'App\\Domains\\Courses\\Models\\Course')
                                {{ $item->orderable->title }}
                            @elseif($item->orderable_type === 'App\\Domains\\Commerce\\Models\\Product')
                                {{ $item->orderable->title }}
                            @else
                                {{ class_basename($item->orderable_type) }}
                            @endif
                        @else
                            {{ class_basename($item->orderable_type) }} (حذف شده)
                        @endif
                    </td>
                    <td>{{ number_format($item->price) }} تومان</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->price * $item->quantity) }} تومان</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <span>مجموع کل:</span>
            <span>{{ number_format($order->total_amount) }} تومان</span>
        </div>
    </div>

    @if($order->gateway_ref_id)
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <p><strong>شناسه تراکنش:</strong> {{ $order->gateway_ref_id }}</p>
        </div>
    @endif
</body>
</html>

