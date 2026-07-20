<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Receipt #{{ $order->order_number }}</title>
    <style>
        @page { size: 58mm auto; margin: 0; }
        * { box-sizing: border-box; }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
            line-height: 1.25;
            color: #000;
            margin: 0;
            padding: 2px 3px;
        }
        .center { text-align: center; }
        .b { font-weight: bold; }
        .big { font-size: 12px; }
        .muted { opacity: .7; }
        .rule td { border-top: 1px dashed #000; padding: 0; height: 1px; }
        .rule { margin: 3px 0; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        td { padding: 1px 0; vertical-align: top; word-break: break-word; overflow-wrap: break-word; }
        .lbl { white-space: nowrap; width: 42pt; }
        .val { text-align: right; padding-left: 6px; }
        .name { font-weight: bold; }
        .mods { font-size: 9px; padding-left: 4px; }
        .tot td { font-size: 10px; }
        .tot td:first-child { width: 60%; }
        .grand td { font-size: 12px; font-weight: bold; border-top: 1px dashed #000; padding-top: 3px; }        .no-print { display: none; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>
    <div class="center big b">MealHQ</div>
    <div class="center muted">123 Main St, New York</div>
    <div class="center muted">info@mealhq.test</div>
    <table class="rule"><tr><td></td></tr></table>

    <table>
        <tr><td class="lbl">Order</td><td class="val b">{{ $order->order_number }}</td></tr>
        <tr><td class="lbl">Date</td><td class="val">{{ $order->created_at?->format('d/m/Y H:i') ?? '—' }}</td></tr>
        <tr><td class="lbl">Type</td><td class="val">{{ ucfirst(str_replace('_',' ',$order->type)) }}</td></tr>
        @if($order->tableSession?->restaurantTable)
            <tr><td class="lbl">Table</td><td class="val">{{ $order->tableSession->restaurantTable->table_number }}{{ $order->tableSession->restaurantTable->zone?->name ? ' ('.$order->tableSession->restaurantTable->zone->name.')' : '' }}</td></tr>
        @endif
        <tr><td class="lbl">Customer</td><td class="val">{{ $order->customer?->name ?? 'Walk-in' }}</td></tr>
        @if($order->user)<tr><td class="lbl">Served</td><td class="val">{{ $order->user->name }}</td></tr>@endif
    </table>
    <table class="rule"><tr><td></td></tr></table>

    @foreach($order->items as $item)
    <table>
        <tr>
            <td class="name">{{ $item->quantity }}x {{ $item->item_name }}@if($item->variant_name) ({{ $item->variant_name }}) @endif</td>
            <td class="val">${{ number_format($item->subtotal, 2) }}</td>
        </tr>
        @if($item->modifiers->isNotEmpty())
            <tr><td class="mods" colspan="2">+ {{ $item->modifiers->pluck('modifier_item_name')->join(', ') }}</td></tr>
        @endif
        @if($item->special_instructions)
            <tr><td class="mods muted" colspan="2">Note: {{ $item->special_instructions }}</td></tr>
        @endif
    </table>
    @endforeach

    <table class="rule"><tr><td></td></tr></table>
    <table class="tot">
        <tr><td>Subtotal</td><td class="val">${{ number_format($order->subtotal, 2) }}</td></tr>
        <tr><td>Tax</td><td class="val">${{ number_format($order->tax_amount, 2) }}</td></tr>
        <tr><td>Service</td><td class="val">${{ number_format($order->service_charge, 2) }}</td></tr>
        @if($order->discount_amount > 0)
            <tr><td>Discount</td><td class="val">-${{ number_format($order->discount_amount, 2) }}</td></tr>
        @endif
        <tr class="grand"><td>TOTAL</td><td class="val">${{ number_format($order->total_amount, 2) }}</td></tr>
    </table>
    <table class="rule"><tr><td></td></tr></table>

    <div class="center muted">{{ ucfirst($order->status) }}</div>
    <div class="center big b" style="margin-top:6px">THANK YOU!</div>
    <div class="center muted" style="margin-top:4px">Powered by MealHQ</div>
</body>
</html>
