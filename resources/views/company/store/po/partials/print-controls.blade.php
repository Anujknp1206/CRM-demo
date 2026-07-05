<div class="print-controls">

    <div class="d-md-none mb-2 text-right">
        <button class="btn btn-danger btn-sm" id="closeControls">
            ✖ Close
        </button>
    </div>

    {{-- ================= SECTIONS ================= --}}
    <h6><b>Show Sections</b></h6>

    <label><input type="checkbox" class="toggle-section" data-target="company" checked> Company</label><br>
    <label><input type="checkbox" class="toggle-section" data-target="docinfo" checked> PO Info</label><br>
    <label><input type="checkbox" class="toggle-section" data-target="supplier" checked> Supplier</label><br>
    <label><input type="checkbox" class="toggle-section" data-target="items" checked> Items</label><br>
    <label><input type="checkbox" class="toggle-section" data-target="totals" checked> Totals</label><br>
    <label><input type="checkbox" class="toggle-section" data-target="remark" checked> Remark</label>

    <hr>

    {{-- ================= EXTRA FIELDS ================= --}}
    <h6><b>Extra Fields</b></h6>

    {{-- COMPANY --}}
    <label><input type="checkbox" class="toggle-extra" data-target="company-name" checked> Company Name</label><br>
    <label><input type="checkbox" class="toggle-extra" data-target="company-address" checked> Company
        Address</label><br>
    <label><input type="checkbox" class="toggle-extra" data-target="company-phone" checked> Company Phone</label><br>
    <label><input type="checkbox" class="toggle-extra" data-target="company-email" checked> Company Email</label><br>

    <hr>

    {{-- SUPPLIER --}}
    <label><input type="checkbox" class="toggle-extra" data-target="supplier-name" checked> Supplier Name</label><br>
    <label><input type="checkbox" class="toggle-extra" data-target="supplier-email" checked> Supplier Email</label><br>
    <label><input type="checkbox" class="toggle-extra" data-target="supplier-phone" checked> Supplier Phone</label><br>
    <label><input type="checkbox" class="toggle-extra" data-target="supplier-address" checked> Supplier
        Address</label><br>

    <hr>

    {{-- PO INFO --}}
    <label><input type="checkbox" class="toggle-extra" data-target="po-code" checked> PO Code</label><br>
    <label><input type="checkbox" class="toggle-extra" data-target="po-date" checked> Date</label><br>
    <label><input type="checkbox" class="toggle-extra" data-target="rfi-code" checked> RFI Code</label><br>
    <label><input type="checkbox" class="toggle-extra" data-target="created-by" checked> Created By</label><br>

    <hr>

    {{-- TOTALS --}}
    <label><input type="checkbox" class="toggle-extra" data-target="subtotal" checked> Subtotal</label><br>
    <label><input type="checkbox" class="toggle-extra" data-target="discount" checked> Discount</label><br>
    <label><input type="checkbox" class="toggle-extra" data-target="tax-percent" checked> Tax (%)</label><br>
    <label><input type="checkbox" class="toggle-extra" data-target="tax-amount" checked> Tax Amount</label><br>
    <label><input type="checkbox" class="toggle-extra" data-target="final-row" checked> Final Total</label><br>

    <hr>

    {{-- ================= ITEM COLUMNS ================= --}}
    <h6><b>Item Columns</b></h6>

    <label><input type="checkbox" class="column-toggle" data-col="sn" checked> S.N.</label><br>
    <label><input type="checkbox" class="column-toggle" data-col="name" checked> Item</label><br>
    <label><input type="checkbox" class="column-toggle" data-col="brand" checked> Brand</label><br>
    <label><input type="checkbox" class="column-toggle" data-col="condition" checked> Condition</label><br>
    <label><input type="checkbox" class="column-toggle" data-col="unit" checked> Unit</label><br>
 


    <label><input type="checkbox" class="column-toggle" data-col="qty" checked> Qty</label><br>
    <label><input type="checkbox" class="column-toggle" data-col="rate" checked> Rate</label><br>
    <label><input type="checkbox" class="column-toggle" data-col="total" checked> Amount</label><br>

    <hr>

    {{-- ================= ITEM ROWS ================= --}}
    <h6><b>Item Rows</b></h6>

    @foreach($po->items as $i => $item)
        <label>
            <input type="checkbox" class="row-toggle" data-row="{{ $i }}" checked>
            Item {{ $i + 1 }}
        </label><br>
    @endforeach

    <hr>

    {{-- ================= PDF ================= --}}
    <div class="row mt-3">
        <div class="col-12">
            <button type="button" id="downloadBtn" class="btn btn-danger btn-sm w-100">

                <span class="btn-text">
                    <i class="fas fa-file-pdf mr-1"></i> Save PDF
                </span>

                <span class="btn-loader d-none">
                    <i class="fas fa-spinner fa-spin mr-1"></i> Generating...
                </span>

            </button>
        </div>
    </div>

</div>