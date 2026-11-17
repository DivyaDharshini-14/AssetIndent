<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Requisition {{ $purchaseRequisition->pr_no }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #222; margin: 24px; }
        h1, h2 { margin: 0 0 8px; }
        .meta { margin-bottom: 16px; }
        .meta p { margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #999; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background: #f3f3f3; }
        .section { margin-bottom: 20px; }
        .grid { display: table; width: 100%; }
        .grid-row { display: table-row; }
        .grid-cell { display: table-cell; width: 33%; padding: 4px 8px 4px 0; }
        .label { color: #666; font-size: 11px; display: block; }
        @media print { .no-print { display: none; } body { margin: 12px; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 16px;">
        <button onclick="window.print()">Print</button>
    </div>

    <h1>Purchase Requisition</h1>
    <p class="meta"><strong>{{ $purchaseRequisition->pr_no }}</strong> — Internal procurement document</p>

    <div class="section">
        <h2>PR Information</h2>
        <div class="grid">
            <div class="grid-row">
                <div class="grid-cell"><span class="label">PR No.</span>{{ $purchaseRequisition->pr_no }}</div>
                <div class="grid-cell"><span class="label">Status</span>{{ $purchaseRequisition->statusEnum()?->label() ?? $purchaseRequisition->status }}</div>
                <div class="grid-cell"><span class="label">Processed By</span>{{ $purchaseRequisition->purchaseOfficer->name ?? '-' }}</div>
            </div>
            <div class="grid-row">
                <div class="grid-cell"><span class="label">Created By</span>{{ $purchaseRequisition->createdByUser->name ?? ($purchaseRequisition->indent?->requestedBy?->name ?? '-') }}</div>
                <div class="grid-cell"><span class="label">Created At</span>{{ $purchaseRequisition->created_at?->format('d M Y h:i A') ?? '-' }}</div>
                <div class="grid-cell"></div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Indent Information</h2>
        <div class="grid">
            <div class="grid-row">
                <div class="grid-cell"><span class="label">Indent No.</span>{{ $purchaseRequisition->indent->indent_no ?? '-' }}</div>
                <div class="grid-cell"><span class="label">Department</span>{{ $purchaseRequisition->indent->department->department_name ?? '-' }}</div>
                <div class="grid-cell"><span class="label">Priority</span>{{ $purchaseRequisition->indent->priority ?? '-' }}</div>
            </div>
            <div class="grid-row">
                <div class="grid-cell"><span class="label">Requested By</span>{{ $purchaseRequisition->indent->requestedBy->name ?? '-' }}</div>
                <div class="grid-cell"><span class="label">Required Date</span>{{ $purchaseRequisition->indent->required_date?->format('d M Y') ?? '-' }}</div>
            </div>
        </div>
        <p><span class="label">Purpose / Reason</span>{{ $purchaseRequisition->indent->purpose ?? '-' }}</p>
    </div>

    <div class="section">
        <h2>Store</h2>
        <p><span class="label">Store</span>{{ $purchaseRequisition->requisitionStore->department_name ?? ($purchaseRequisition->indent->toStore->department_name ?? $purchaseRequisition->indent->fromStore->department_name ?? '-') }}</p>
        @if ($purchaseRequisition->close_reason)
            <p><span class="label">Close Reason</span>{{ $purchaseRequisition->close_reason }}</p>
        @endif
    </div>

    <div class="section">
        <h2>Requested Items</h2>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Est. Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($purchaseRequisition->indent?->items ?? [] as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->category->category_name ?? '-' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->unit }}</td>
                        <td>{{ number_format((float) $item->estimated_price, 2) }}</td>
                        <td>{{ number_format((float) $item->total_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7">No items</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <p><span class="label">Printed At</span>{{ now()->format('d M Y h:i A') }}</p>
    </div>
</body>
</html>
