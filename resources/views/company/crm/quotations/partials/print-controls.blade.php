<div class="print-controls">
    <div class="d-md-none mb-2 text-right">
        <button class="btn btn-danger btn-sm" id="closeControls">
            ✖ Close
        </button>
    </div>
    <h6><b>Document Type</b></h6>
    <label><input type="radio" name="doc_type" value="quotation" checked> Quotation</label><br>
    <label><input type="radio" name="doc_type" value="pi"> Proforma Invoice</label>
    <hr>
    <h6><b>Language</b></h6>
    <button type="button" id="toHindi" class="btn btn-sm btn-outline-primary w-100 mb-1">
        Convert to Hindi
    </button>

    <button type="button" id="toEnglish" class="btn btn-sm btn-outline-secondary w-100">
        Convert to English
    </button>
    <hr>
    <label><input type="checkbox" class="toggle-section" data-target="docinfo" checked> Doc Info Section</label>
    <hr>
    <label><input type="checkbox" id="buyer_seller" class="toggle-section" data-target="customer" checked> Buyer &
        Seller
        Section</label><br>
    <label><input type="checkbox" class="toggle-extra buyer_seller-checkbox" data-target="customer-gst" checked>
        GST</label><br>
    <label><input type="checkbox" class="toggle-extra buyer_seller-checkbox" data-target="customer-attn" checked>
        Contact Person</label><br>
    <label><input type="checkbox" class="toggle-extra buyer_seller-checkbox" data-target="customer-email" checked>
        Email</label><br>
    <label><input type="checkbox" class="toggle-extra buyer_seller-checkbox" data-target="customer-phone" checked>
        Phone</label><br>
    <label><input type="checkbox" class="toggle-extra buyer_seller-checkbox" data-target="customer-address" checked>
        Address</label><br>
    <hr>
    <label><input type="checkbox" class="toggle-extra" data-target="quotation-remarks" checked> Remarks
        Section</label><br>
    <hr>
    <label><input type="checkbox" class="toggle-extra" data-target="delivery-address" checked>
        Destination Section</label><br>
    <hr>

    <label><input type="checkbox" id="Item_section" class="toggle-section" data-target="items" checked> Items
        Section</label><br>
    <label><input type="checkbox" class="column-toggle item_section" data-col="sn" checked> S.N.</label><br>
    <label><input type="checkbox" class="column-toggle item_section" data-col="name" checked> Item</label><br>
    <label><input type="checkbox" class="column-toggle item_section" data-col="desc" checked> Description</label><br>
    <label><input type="checkbox" class="column-toggle item_section" data-col="qty" checked> Qty</label><br>
    <label><input type="checkbox" class="column-toggle item_section" data-col="rate" checked> Rate</label><br>
    <label><input type="checkbox" class="column-toggle item_section" data-col="total" checked> Total</label><br>
    <label><input type="checkbox" class="column-toggle item_section" data-col="Cfv" checked> CFV</label><br>
    <hr>
    <label>
        <input type="checkbox" id="total_section" class="toggle-extra" data-target="section-totals" checked> Totals
        Section
    </label><br>
    <label>
        <input type="checkbox" class="toggle-extra total-sections" data-target="subtotal" checked>
        Subtotal
    </label><br>
    <label>
        <input type="checkbox" class="toggle-extra total-sections" data-target="discount" checked>
        Discount
    </label><br>
    <label>
        <input type="checkbox" class="toggle-extra total-sections" data-target="taxable-amount" checked>
        Taxable Amount
    </label><br> <label><input type="checkbox" class="toggle-extra total-sections" data-target="tax-percent" checked>
        Tax
        (%)</label><br>
    <label><input type="checkbox" class="toggle-extra total-sections" data-target="tax-amount" checked> Tax
        Amount</label><br>

    <label>
        <input type="checkbox" class="toggle-extra total-sections" data-target="final-row" checked>
        Final Amount
    </label><br>
    <hr>
    <h6><b>Item Rows</b></h6>
    @foreach($quotation->items as $i => $item)
        <label><input type="checkbox" class="row-toggle item_section" data-row="{{ $i }}" checked> Item
            {{ $i + 1 }}</label><br>
    @endforeach

    <hr>
    <label><input type="checkbox" class="toggle-section" data-target="terms" checked> Terms Section </label>
    <div class="row mt-3">

        <div class="col-6">
            <button onclick="printDocument()" class="btn btn-success btn-sm w-100">
                <i class="fas fa-print mr-1"></i> Print
            </button>
        </div>
        <!-- <div class="col-6" id="pdfBtnWrapper"> -->
        <div class="col-6" id="">
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