<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1a1d21; margin: 0; padding: 32px; }
        .invoice { max-width: 800px; margin: 0 auto; border: 1px solid #e9ecef; border-radius: 8px; overflow: hidden; }
        .invoice-header { display: flex; justify-content: space-between; align-items: flex-start; padding: 32px; border-bottom: 1px solid #e9ecef; background: #f8f9fa; }
        .brand { font-size: 24px; font-weight: 700; color: #206bc4; }
        .meta { text-align: right; font-size: 13px; line-height: 1.6; color: #495057; }
        .meta strong { color: #1a1d21; }
        .section { padding: 24px 32px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        thead th { text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: .03em; color: #6c757d; border-bottom: 2px solid #e9ecef; padding: 8px; }
        tbody td { padding: 8px; border-bottom: 1px solid #f0f0f0; font-size: 14px; vertical-align: top; word-wrap: break-word; }
        tbody td.col-item { width: 46%; }
        tbody td.col-qty { width: 10%; text-align: right; }
        tbody td.col-unit { width: 22%; text-align: right; }
        tbody td.col-amt { width: 22%; text-align: right; }
        thead th.col-qty, thead th.col-unit, thead th.col-amt { text-align: right; }
        .text-end { text-align: right; }
        .text-muted { color: #6c757d; font-size: 12px; }
        .totals { margin-left: auto; width: 320px; margin-top: 16px; }
        .totals table { width: 100%; border-collapse: collapse; }
        .totals td { padding: 6px 0; font-size: 14px; }
        .totals td.text-end { text-align: right; }
        .totals tr.grand td { border-top: 2px solid #1a1d21; margin-top: 6px; padding-top: 10px; font-size: 18px; font-weight: 700; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 12px; background: #e7f1ff; color: #206bc4; }
        .footer { padding: 24px 32px; border-top: 1px solid #e9ecef; text-align: center; color: #6c757d; font-size: 13px; }
        @media print { body { padding: 0; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="invoice">
        <div class="invoice-header">
            <div>
                <div class="brand">MealHQ</div>
                <div class="text-muted">123 Main Street, New York, NY 10001</div>
                <div class="text-muted">info@mealhq.test &middot; +1 (555) 123-4567</div>
            </div>
            <div class="meta">
                <div><strong>Invoice</strong></div>
                <div>#{{ $order->order_number }}</div>
                <div>{{ $order->created_at?->format('M d, Y h:i A') ?? '—' }}</div>
                <div class="mt-2"><span class="badge">{{ ucfirst($order->status) }}</span></div>
            </div>
        </div>

        <div class="section">
            <div style="display:flex; gap:32px; flex-wrap:wrap">
                <div style="flex:1; min-width:200px">
                    <div class="text-muted" style="text-transform:uppercase; font-size:11px; letter-spacing:.04em">Customer</div>
                    <div style="font-size:15px; font-weight:600">{{ $order->customer?->name ?? 'Walk-in Customer' }}</div>
                    @if($order->customer?->phone)<div class="text-muted">{{ $order->customer->phone }}</div>@endif
                </div>
                <div style="flex:1; min-width:200px">
                    <div class="text-muted" style="text-transform:uppercase; font-size:11px; letter-spacing:.04em">Order Details</div>
                    <div style="font-size:14px">Type: {{ ucfirst(str_replace('_',' ',$order->type)) }}</div>
                    <div style="font-size:14px">Source: {{ ucfirst(str_replace('_',' ',$order->source)) }}</div>
                    @if($order->tableSession?->restaurantTable)
                        <div style="font-size:14px">Table: {{ $order->tableSession->restaurantTable->table_number }} ({{ $order->tableSession->restaurantTable->zone?->name }})</div>
                    @endif
                    @if($order->user)<div class="text-muted">Served by: {{ $order->user->name }}</div>@endif
                </div>
            </div>
        </div>

        <div class="section" style="padding-top:0">
            <table>
                <thead>
                    <tr>
                        <th class="col-item">Item</th>
                        <th class="col-qty text-end">Qty</th>
                        <th class="col-unit text-end">Unit</th>
                        <th class="col-amt text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td class="col-item">
                            <div style="font-weight:600">{{ $item->item_name }}</div>
                            @if($item->variant_name)<div class="text-muted">Variant: {{ $item->variant_name }}</div>@endif
                            @if($item->modifiers->isNotEmpty())
                                <div class="text-muted">
                                    @foreach($item->modifiers as $m)+ {{ $m->modifier_item_name }}@if(!$loop->last) &middot; @endif @endforeach
                                </div>
                            @endif
                            @if($item->special_instructions)<div class="text-muted">Note: {{ $item->special_instructions }}</div>@endif
                        </td>
                        <td class="col-qty">{{ $item->quantity }}</td>
                        <td class="col-unit">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="col-amt">${{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="totals">
                <table>
                    <tr><td class="text-muted">Subtotal</td><td class="text-end">${{ number_format($order->subtotal, 2) }}</td></tr>
                    <tr><td class="text-muted">Tax</td><td class="text-end">${{ number_format($order->tax_amount, 2) }}</td></tr>
                    <tr><td class="text-muted">Service Charge</td><td class="text-end">${{ number_format($order->service_charge, 2) }}</td></tr>
                    @if($order->discount_amount > 0)
                        <tr><td class="text-muted">Discount</td><td class="text-end">-${{ number_format($order->discount_amount, 2) }}</td></tr>
                    @endif
                    <tr class="grand"><td>Total</td><td class="text-end">${{ number_format($order->total_amount, 2) }}</td></tr>
                </table>
            </div>
        </div>

        <div class="footer">
            Thank you for dining with MealHQ! &middot; This receipt is electronically generated.
        </div>
    </div>
</body>
</html>
