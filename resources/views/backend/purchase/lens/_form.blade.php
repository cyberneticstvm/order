@php
    $editing = isset($purchase);
    $oldProductIds = old('product_id');
    $rows = [];

    if (is_array($oldProductIds)) {
        foreach ($oldProductIds as $key => $productId) {
            $rows[] = [
                'product_id' => $productId,
                'qty' => old("qty.$key"),
                'purchase_price' => old("purchase_price.$key"),
                'discount' => old("discount.$key", 0),
                'tax_amount' => old("tax_amount.$key", 0),
                'total' => old("total.$key", 0),
            ];
        }
    } elseif ($editing) {
        foreach ($purchase->detail as $item) {
            $rows[] = [
                'product_id' => $item->product_id,
                'qty' => $item->qty,
                'purchase_price' => $item->unit_price_purchase,
                'discount' => $item->discount,
                'tax_amount' => $item->tax_amount,
                'total' => $item->total,
            ];
        }
    } else {
        $rows[] = [
            'product_id' => null,
            'qty' => null,
            'purchase_price' => null,
            'discount' => 0,
            'tax_amount' => 0,
            'total' => 0,
        ];
    }
@endphp

<form id="lensPurchaseForm" class="row g-3" method="post" action="{{ $formAction }}">
    @csrf
    <div class="col-md-2">
        <label class="form-label req">Invoice Date</label>
        <input type="date" name="order_date" class="form-control" value="{{ old('order_date', $editing ? $purchase->order_date?->format('Y-m-d') : date('Y-m-d')) }}" required>
        @error('order_date')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
    <div class="col-md-2">
        <label class="form-label req">Entry Date</label>
        <input type="date" name="delivery_date" class="form-control" value="{{ old('delivery_date', $editing ? $purchase->delivery_date?->format('Y-m-d') : date('Y-m-d')) }}" required>
        @error('delivery_date')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label req">Branch</label>
        <select name="branch_id" class="form-control select2" required>
            <option value="">Select</option>
            @foreach($branches as $branchId => $branchName)
                <option value="{{ $branchId }}" @selected((string) old('branch_id', $editing ? $purchase->branch_id : '') === (string) $branchId)>{{ $branchName }}</option>
            @endforeach
        </select>
        @error('branch_id')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label req">Supplier</label>
        <select name="supplier_id" class="form-control select2" required>
            <option value="">Select</option>
            @foreach($suppliers as $supplierId => $supplierName)
                <option value="{{ $supplierId }}" @selected((string) old('supplier_id', $editing ? $purchase->supplier_id : '') === (string) $supplierId)>{{ $supplierName }}</option>
            @endforeach
        </select>
        @error('supplier_id')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
    <div class="col-md-2">
        <label class="form-label req">Purchase Invoice Number</label>
        <input type="text" name="purchase_invoice_number" class="form-control" value="{{ old('purchase_invoice_number', $editing ? $purchase->purchase_invoice_number : '') }}" maxlength="255" required>
        @error('purchase_invoice_number')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Purchase Note</label>
        <textarea name="purchase_note" class="form-control" rows="3" placeholder="Purchase Note">{{ old('purchase_note', $editing ? $purchase->purchase_note : '') }}</textarea>
    </div>

    <div class="row g-4 table-responsive">
        <div class="col text-end">
            <button class="btn btn-primary" type="button" onclick="addLensPurchaseRow()">Add New Row</button>
        </div>
        <div class="col-12">
            <h5 class="text-center text-secondary">Purchase Details</h5>
            @if($errors->has('product_id') || $errors->has('qty') || $errors->has('purchase_price') || $errors->has('discount'))
                <div class="alert alert-danger">Please correct the highlighted purchase detail values.</div>
            @endif
            <table class="table table-bordered table-stripped mt-3">
                <thead class="text-center">
                    <tr>
                        <th>Remove</th>
                        <th width="40%">Product</th>
                        <th width="6%">Qty</th>
                        <th width="11%">Purchase Price</th>
                        <th width="11%">Item Total</th>
                        <th width="10%">Discount</th>
                        <th width="10%">Tax Amount</th>
                        <th width="12%">Total</th>
                    </tr>
                </thead>
                <tbody id="lensPurchaseRows">
                    @foreach($rows as $key => $row)
                        <tr>
                            <td class="text-center"><button type="button" class="btn btn-link p-0 lensRemoveRow"><i class="fa fa-trash text-danger"></i></button></td>
                            <td>
                                <select class="form-control select2 lensProduct" name="product_id[]" required>
                                    <option value="">Select</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-tax="{{ $product->tax_percentage ?? 0 }}" @selected((string) $row['product_id'] === (string) $product->id)>{{ $product->name }}-{{ $product->code }}-{{ $product->tax_percentage ?? 0 }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="qty[]" class="w-100 border-0 text-end lensQty" value="{{ $row['qty'] }}" min="1" step="1" required></td>
                            <td><input type="number" name="purchase_price[]" class="w-100 border-0 text-end lensPurchasePrice" value="{{ $row['purchase_price'] }}" min="0" step="0.01" required></td>
                            <td><input type="number" class="w-100 border-0 text-end lensItemTotal readOnly" value="0.00" step="0.01" readonly></td>
                            <td><input type="number" name="discount[]" class="w-100 border-0 text-end lensDiscount" value="{{ $row['discount'] ?? 0 }}" min="0" step="0.01" required></td>
                            <td><input type="number" name="tax_amount[]" class="w-100 border-0 text-end lensTaxAmount readOnly" value="{{ $row['tax_amount'] ?? 0 }}" step="0.01" readonly></td>
                            <td><input type="number" name="total[]" class="w-100 border-0 text-end lensLineTotal readOnly" value="{{ $row['total'] ?? 0 }}" step="0.01" readonly></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7" class="fw-bold text-end">Line Total</td>
                        <td><input type="text" id="lensLinesTotal" class="text-end border-0 fw-bold w-100 readOnly" value="0.00" readonly></td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-end fw-bold border-0">Other Charges</td>
                        <td colspan="2"><input type="text" class="form-control" name="other_charges_desc" value="{{ old('other_charges_desc', $editing ? $purchase->other_charges_desc : '') }}" maxlength="255" placeholder="Description"></td>
                        <td><input type="number" name="other_charges" id="lensOtherCharges" class="text-end border-0 fw-bold w-100" value="{{ old('other_charges', $editing ? $purchase->other_charges : 0) }}" min="0" step="0.01" placeholder="0.00"></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end fw-bold border-0">Adjustment</td>
                        <td colspan="2"><input type="text" class="form-control" name="adjust_desc" value="{{ old('adjust_desc', $editing ? $purchase->adjust_desc : '') }}" maxlength="255" placeholder="Description"></td>
                        <td>
                            <select class="form-control" name="adjust_type" id="lensAdjustType">
                                <option value="">Select</option>
                                <option value="plus" @selected(old('adjust_type', $editing ? $purchase->adjust_type : '') === 'plus')>+</option>
                                <option value="minus" @selected(old('adjust_type', $editing ? $purchase->adjust_type : '') === 'minus')>-</option>
                            </select>
                            @error('adjust_type')<small class="text-danger">{{ $message }}</small>@enderror
                        </td>
                        <td><input type="number" name="adjust_amount" id="lensAdjustAmount" class="text-end border-0 fw-bold w-100" value="{{ old('adjust_amount', $editing ? $purchase->adjust_amount : 0) }}" min="0" step="0.01" placeholder="0.00"></td>
                    </tr>
                    <tr>
                        <td colspan="7" class="fw-bold text-end">Final Total</td>
                        <td><input type="text" id="lensGrandTotal" class="text-end border-0 fw-bold w-100 readOnly" value="{{ $editing ? number_format($tot, 2, '.', '') : '0.00' }}" readonly></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="col-12 text-end">
            <button class="btn btn-secondary" onclick="window.history.back()" type="button">Cancel</button>
            <button class="btn btn-submit btn-success" type="submit">{{ $editing ? 'Update' : 'Save' }}</button>
        </div>
    </div>
</form>

<script>
    (function () {
        const numberValue = (value) => {
            const parsed = Number.parseFloat(value);
            return Number.isFinite(parsed) ? parsed : 0;
        };

        const taxFromName = (name) => {
            const parts = String(name || '').split('-');
            return numberValue(parts[parts.length - 1]);
        };

        const recalculateLensPurchase = () => {
            let linesTotal = 0;

            document.querySelectorAll('#lensPurchaseRows tr').forEach((row) => {
                const product = row.querySelector('.lensProduct');
                const qty = Math.max(0, Number.parseInt(row.querySelector('.lensQty').value, 10) || 0);
                const price = numberValue(row.querySelector('.lensPurchasePrice').value);
                const discountInput = row.querySelector('.lensDiscount');
                const discount = numberValue(discountInput.value);
                const selected = product.options[product.selectedIndex];
                const taxPercentage = numberValue(selected?.dataset.tax);

                discountInput.setCustomValidity(discount > price ? 'Discount cannot be greater than the purchase price.' : '');

                const taxableAmount = Math.max(0, qty * (price - discount));
                const taxAmount = taxableAmount * taxPercentage / 100;
                const lineTotal = taxableAmount + taxAmount;

                row.querySelector('.lensItemTotal').value = taxableAmount.toFixed(2);
                row.querySelector('.lensTaxAmount').value = taxAmount.toFixed(2);
                row.querySelector('.lensLineTotal').value = lineTotal.toFixed(2);
                linesTotal += lineTotal;
            });

            const otherCharges = numberValue(document.getElementById('lensOtherCharges').value);
            const adjustment = numberValue(document.getElementById('lensAdjustAmount').value);
            const adjustmentType = document.getElementById('lensAdjustType').value;
            const finalTotal = linesTotal + otherCharges + (adjustmentType === 'minus' ? -adjustment : adjustment);
            const adjustmentInput = document.getElementById('lensAdjustAmount');

            adjustmentInput.setCustomValidity(finalTotal < 0 ? 'Adjustment cannot make the purchase total negative.' : '');
            document.getElementById('lensLinesTotal').value = linesTotal.toFixed(2);
            document.getElementById('lensGrandTotal').value = finalTotal.toFixed(2);
        };

        window.addLensPurchaseRow = async function () {
            const response = await fetch('{{ route('ajax.product.get.by.category', ['category' => 'lens', 'type' => 'purchase']) }}', {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) {
                window.alert('Unable to load lens products. Please try again.');
                return;
            }

            const data = await response.json();
            const row = document.createElement('tr');
            row.innerHTML = '<td class="text-center"><button type="button" class="btn btn-link p-0 lensRemoveRow"><i class="fa fa-trash text-danger"></i></button></td>' +
                '<td><select class="form-control lensProduct" name="product_id[]" required><option value="">Select</option></select></td>' +
                '<td><input type="number" name="qty[]" class="w-100 border-0 text-end lensQty" min="1" step="1" required></td>' +
                '<td><input type="number" name="purchase_price[]" class="w-100 border-0 text-end lensPurchasePrice" min="0" step="0.01" required></td>' +
                '<td><input type="number" class="w-100 border-0 text-end lensItemTotal readOnly" value="0.00" step="0.01" readonly></td>' +
                '<td><input type="number" name="discount[]" class="w-100 border-0 text-end lensDiscount" value="0.00" min="0" step="0.01" required></td>' +
                '<td><input type="number" name="tax_amount[]" class="w-100 border-0 text-end lensTaxAmount readOnly" value="0.00" step="0.01" readonly></td>' +
                '<td><input type="number" name="total[]" class="w-100 border-0 text-end lensLineTotal readOnly" value="0.00" step="0.01" readonly></td>';

            const select = row.querySelector('.lensProduct');
            (data.products || []).forEach((product) => {
                const option = document.createElement('option');
                option.value = product.id;
                option.textContent = product.name;
                option.dataset.tax = taxFromName(product.name);
                select.appendChild(option);
            });

            document.getElementById('lensPurchaseRows').appendChild(row);
            if (window.jQuery && window.jQuery.fn.select2) {
                window.jQuery(select).select2({ placeholder: 'Select' });
            }
            recalculateLensPurchase();
        };

        window.addEventListener('load', function () {
            const form = document.getElementById('lensPurchaseForm');
            const rows = document.getElementById('lensPurchaseRows');

            form.addEventListener('input', recalculateLensPurchase);
            form.addEventListener('change', recalculateLensPurchase);
            rows.addEventListener('click', function (event) {
                const removeButton = event.target.closest('.lensRemoveRow');
                if (!removeButton) return;

                if (rows.querySelectorAll('tr').length === 1) {
                    window.alert('At least one purchase detail row is required.');
                    return;
                }

                const row = removeButton.closest('tr');
                const select = row.querySelector('.lensProduct');
                if (window.jQuery && window.jQuery.fn.select2 && window.jQuery(select).hasClass('select2-hidden-accessible')) {
                    window.jQuery(select).select2('destroy');
                }
                row.remove();
                recalculateLensPurchase();
            });

            recalculateLensPurchase();
        });
    })();
</script>
