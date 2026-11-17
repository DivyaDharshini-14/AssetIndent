@extends('layouts/contentNavbarLayout')

@section('title', 'Record Quotation')

@section('content')
@php
    $indentItems = $rfq->purchaseRequisition->indent->items ?? collect();
@endphp
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Record Quotation — {{ $rfq->rfq_no }}</h4>
    <a class="btn btn-secondary btn-sm" href="{{ route('rfqs.show', $rfq) }}">Back to RFQ</a>
</div>

    <div class="accordion" id="vendorAccordion">
        @foreach ($vendors as $vIndex => $vendor)
            @php
                $savedQuotation = $rfq->quotations->firstWhere('vendor_id', $vendor->id);
                $hasSavedQuotation = $savedQuotation !== null;
                $vendorStatus = $hasSavedQuotation ? $vendor->pivot->status : 'Awaiting Quote';
                $badgeHtml = '';
                if ($hasSavedQuotation) {
                    $badgeClass = $vendorStatus === 'Quoted' ? 'bg-success' : 'bg-danger';
                    $badgeHtml = '<span class="badge ' . $badgeClass . ' ms-2">' . $vendorStatus . '</span>';
                }
            @endphp
            <form action="{{ route('rfqs.quotations.store', $rfq) }}" method="POST">
                @csrf
                <div class="accordion-item mb-3 border vendor-card" data-vendor-index="{{ $vIndex }}" data-has-saved="{{ $hasSavedQuotation ? 'true' : 'false' }}">
                    <div class="accordion-header d-flex align-items-center bg-light py-2 px-3 border-bottom" id="heading-{{ $vIndex }}">
                        <div class="form-check form-switch mb-0 d-flex align-items-center">
                            <input class="form-check-input vendor-toggle shadow-none" type="checkbox" id="toggle-vendor-{{ $vIndex }}" style="transform: scale(0.85); cursor: pointer;" data-target="#collapse-{{ $vIndex }}" {{ $hasSavedQuotation ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold ms-2" for="toggle-vendor-{{ $vIndex }}" style="cursor: pointer; font-size: 0.9rem;">
                                {{ $vendor->name }} {!! $badgeHtml !!}
                            </label>
                        </div>
                    </div>
                    
                    <div id="collapse-{{ $vIndex }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $vIndex }}" data-bs-parent="#vendorAccordion">
                        <div class="accordion-body vendor-body pt-2 pb-3 px-3">
                            <input type="hidden" name="vendor_id" value="{{ $vendor->id }}">
                            <input type="hidden" name="contact_person" value="{{ $vendor->name }}">

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0" style="font-size: 0.85rem;">Quoted Items</h6>
                                <div class="d-flex align-items-center">
                                    <label class="form-label mb-0 me-2 text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Status</label>
                                    <select name="status" class="form-select form-select-sm" style="width: auto;" required>
                                        <option value="Quoted" {{ $vendorStatus === 'Quoted' || !$hasSavedQuotation ? 'selected' : '' }}>Quoted</option>
                                        <option value="Declined" {{ $vendorStatus === 'Declined' ? 'selected' : '' }}>Declined</option>
                                    </select>
                                </div>
                            </div>

                            <div class="table-responsive border rounded mb-2">
                                <table class="table table-sm table-hover mb-0 vendor-items-table">
                                    <thead class="table-light">
                                        <tr style="font-size: 0.75rem;">
                                            <th>Item</th>
                                            <th style="width:80px">Qty</th>
                                            <th style="width:80px">Unit</th>
                                            <th style="width:120px">Unit Price</th>
                                            <th style="width:100px">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($indentItems as $i => $item)
                                            @php
                                                $savedItem = $hasSavedQuotation ? $savedQuotation->items->firstWhere('indent_item_id', $item->id) : null;
                                            @endphp
                                            <tr>
                                                <td class="align-middle" style="font-size: 0.85rem;">
                                                    {{ $item->item_name }}
                                                    <input type="hidden" name="items[{{ $i }}][indent_item_id]" value="{{ $item->id }}">
                                                    <input type="hidden" name="items[{{ $i }}][item_name]" value="{{ $item->item_name }}">
                                                </td>
                                                <td class="align-middle">
                                                    <input type="number" min="1" class="form-control form-control-sm qty-input px-1"
                                                           name="items[{{ $i }}][quantity]" value="{{ $savedItem ? $savedItem->quantity : $item->quantity }}">
                                                </td>
                                                <td class="align-middle">
                                                    <input class="form-control form-control-sm px-1" name="items[{{ $i }}][unit]" value="{{ $savedItem ? $savedItem->unit : $item->unit }}">
                                                </td>
                                                <td class="align-middle">
                                                    <input type="number" step="0.01" min="0.01" class="form-control form-control-sm price-input px-1"
                                                           name="items[{{ $i }}][unit_price]" value="{{ $savedItem ? $savedItem->unit_price : '' }}" required placeholder="0.00">
                                                </td>
                                                <td class="align-middle line-total fw-semibold" style="font-size: 0.85rem;">0.00</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="text-center py-2" style="font-size: 0.85rem;">No indent items found.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="row g-2 mt-2 align-items-end">
                                <div class="col">
                                    <label class="form-label mb-1 text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Items Total</label>
                                    <input name="price" type="number" step="0.01" class="form-control form-control-sm quote-price bg-light" value="{{ $savedQuotation ? $savedQuotation->price : '' }}" readonly>
                                </div>
                                <div class="col">
                                    <label class="form-label mb-1 text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Tax</label>
                                    <input name="tax" type="number" step="0.01" value="{{ $savedQuotation ? $savedQuotation->tax : '0' }}" class="form-control form-control-sm quote-tax">
                                </div>
                                <div class="col">
                                    <label class="form-label mb-1 text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Shipping</label>
                                    <input name="shipping" type="number" step="0.01" value="{{ $savedQuotation ? $savedQuotation->shipping : '0' }}" class="form-control form-control-sm quote-shipping">
                                </div>
                                <div class="col">
                                    <label class="form-label mb-1 text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Grand Total</label>
                                    <input type="text" class="form-control form-control-sm quote-total fw-bold text-primary bg-label-primary" value="{{ $savedQuotation ? $savedQuotation->originalQuote() : '' }}" readonly>
                                </div>
                                <div class="col">
                                    <label class="form-label mb-1 text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Del. Days</label>
                                    <input name="delivery_days" type="number" min="0" value="{{ $savedQuotation ? $savedQuotation->delivery_days : '' }}" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="row g-2 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label mb-1 text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Warranty</label>
                                    <input name="warranty" class="form-control form-control-sm" value="{{ $savedQuotation ? $savedQuotation->warranty : '' }}" placeholder="e.g. 1 Year">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1 text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Notes</label>
                                    <input name="notes" class="form-control form-control-sm" value="{{ $savedQuotation ? $savedQuotation->notes : '' }}" placeholder="Optional notes">
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end mt-3">
                                <button type="submit" class="btn btn-primary btn-sm">Save Quotation</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @endforeach
    </div>

@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.accordion-collapse').forEach(collapseEl => {
        collapseEl.addEventListener('show.bs.collapse', function () {
            const card = this.closest('.vendor-card');
            const toggle = card.querySelector('.vendor-toggle');
            if (!toggle.checked) {
                toggle.checked = true;
            }
        });
        collapseEl.addEventListener('hide.bs.collapse', function () {
            const card = this.closest('.vendor-card');
            const toggle = card.querySelector('.vendor-toggle');
            const hasSaved = card.dataset.hasSaved === 'true';
            
            if (toggle.checked && !hasSaved) {
                toggle.checked = false;
            }
        });
    });

    document.querySelectorAll('.vendor-toggle').forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            const card = this.closest('.vendor-card');
            const hasSaved = card.dataset.hasSaved === 'true';
            
            if (hasSaved) {
                e.preventDefault(); // keep checkbox ON
                
                const targetSelector = this.getAttribute('data-target');
                const collapseEl = document.querySelector(targetSelector);
                const bsCollapse = bootstrap.Collapse.getOrCreateInstance(collapseEl, { toggle: false });
                
                if (collapseEl.classList.contains('show')) {
                    bsCollapse.hide();
                } else {
                    bsCollapse.show();
                }
            }
        });

        toggle.addEventListener('change', function(e) {
            const card = this.closest('.vendor-card');
            const hasSaved = card.dataset.hasSaved === 'true';
            
            if (!hasSaved) {
                const targetSelector = this.getAttribute('data-target');
                const collapseEl = document.querySelector(targetSelector);
                const bsCollapse = bootstrap.Collapse.getOrCreateInstance(collapseEl, { toggle: false });
                
                if (this.checked) {
                    bsCollapse.show();
                } else {
                    bsCollapse.hide();
                }
            }
        });
    });

    function recalcVendor(card) {
        let total = 0;
        card.querySelectorAll('.vendor-items-table tbody tr').forEach(function (row) {
            const qty = parseFloat(row.querySelector('.qty-input')?.value || 0);
            const price = parseFloat(row.querySelector('.price-input')?.value || 0);
            const line = qty * price;
            const cell = row.querySelector('.line-total');
            if (cell) cell.textContent = line.toFixed(2);
            total += line;
        });
        
        const priceInput = card.querySelector('.quote-price');
        if (priceInput) priceInput.value = total.toFixed(2);
        
        const tax = parseFloat(card.querySelector('.quote-tax')?.value || 0);
        const shipping = parseFloat(card.querySelector('.quote-shipping')?.value || 0);
        const totalInput = card.querySelector('.quote-total');
        if (totalInput) totalInput.value = (total + tax + shipping).toFixed(2);
    }

    document.querySelectorAll('.vendor-card').forEach(card => {
        card.querySelectorAll('.qty-input, .price-input, .quote-tax, .quote-shipping').forEach(el => {
            el.addEventListener('input', () => recalcVendor(card));
        });
        recalcVendor(card);
    });
});
</script>
@endsection
