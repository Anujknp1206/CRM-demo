<div class="preview-controls no-print">

    <div class="d-md-none mb-2 text-right">
        <button class="btn btn-danger btn-sm" id="closeControls">
            ✖ Close
        </button>
    </div>
    <h6><b>Document Type</b></h6>

    <label>
        <input type="radio" name="docType" value="pi" checked> Proforma Invoice (PI)
    </label><br>

    <label>
        <input type="radio" name="docType" value="po"> Purchase Order (PO)
    </label>
    <hr>

    <label><input type="checkbox" class="toggle-section" data-target="company" checked> Company Section</label><br>
    <hr>
    <label><input type="checkbox" id="doc_info" class="toggle-section" data-target="docinfo" checked> Document Info
        Section</label><br>
    <label><input type="checkbox" class="toggle-extra document-info" data-target="pi-number" checked> PI
        Number</label><br>
    <label><input type="checkbox" class="toggle-extra document-info" data-target="pi-date" checked> PI Date</label><br>
    <label><input type="checkbox" class="toggle-extra document-info" data-target="order-number" checked> Order
        Number</label><br>
    <label><input type="checkbox" class="toggle-extra document-info" data-target="order-date" checked> Order
        Date</label><br>
    <label><input type="checkbox" class="toggle-extra document-info" data-target="quote-number" checked> Quotation
        Number</label><br>
    <label><input type="checkbox" class="toggle-extra document-info" data-target="quote-date" checked> Quotation
        Date</label><br>
    <hr>
    <label><input type="checkbox" id="buyer_seller" class="toggle-section" data-target="customer" checked> Buyer/Seller
        Section</label><br>

    <label><input type="checkbox" class="toggle-extra buyer_seller-checkbox" data-target="customer-gst" checked>
        GST</label><br>
    <!-- <label><input type="checkbox" class="toggle-extra" data-target="customer-pan" checked> PAN</label><br> -->
    <label><input type="checkbox" class="toggle-extra buyer_seller-checkbox" data-target="customer-email" checked>
        Email</label><br>
    <label><input type="checkbox" class="toggle-extra buyer_seller-checkbox" data-target="customer-mobile" checked>
        Mobile</label><br>
    <label><input type="checkbox" class="toggle-extra buyer_seller-checkbox" data-target="customer-address" checked>
        Address</label><br>


    <hr>
    <label><input type="checkbox" id="Item_section" class="toggle-section" data-target="items" checked> Items
        Section</label><br>
    <label><input type="checkbox" class="toggle-extra item_section" data-target="Cfv" checked> CFV</label>
    <hr>

    <label><input type="checkbox" id="total_section" class="toggle-section" data-target="totals" checked> Items
        Totals</label><br>
    <label><input type="checkbox" class="toggle-extra total-sections" data-target="subtotal" checked>
        Subtotal</label><br>
    <label><input type="checkbox" class="toggle-extra total-sections" data-target="discount" checked>
        Discount</label><br>
    <label><input type="checkbox" class="toggle-extra total-sections" data-target="taxable-amount" checked>
        Taxable</label><br>
    <label><input type="checkbox" class="toggle-extra total-sections" data-target="tax-percent" checked> Tax
        %</label><br>
    <label><input type="checkbox" class="toggle-extra total-sections" data-target="tax-amount" checked> Tax
        Amount</label><br>
    <label><input type="checkbox" class="toggle-extra total-sections" data-target="final-row" checked> Final</label><br>
    <hr>


    <label><input type="checkbox" class="toggle-section" data-target="payments" checked> Payments Section</label><br>
    <hr>

    <label><input type="checkbox" class="toggle-extra" data-target="post-date" checked> Post Date</label><br>
    <hr>
    <label><input type="checkbox" id="payments_total" class="toggle-section" data-target="paymenttotals" checked>
        Payment Totals</label><br>
    <label><input type="checkbox" class="toggle-extra payment-total-sections" data-target="total-received" checked>
        Total Received</label><br>
    <label><input type="checkbox" class="toggle-extra payment-total-sections" data-target="order-amount" checked> Order
        Amount</label><br>
    <label><input type="checkbox" class="toggle-extra payment-total-sections" data-target="remaining" checked>
        Remaining</label><br>
    <hr>
    <label><input type="checkbox" class="toggle-extra" data-target="receipt-note" checked> Receipt Note</label><br>
    <hr>

    <label><input type="checkbox" class="toggle-section" data-target="sign" checked> Signature</label><br>
    <hr>
    <label><input type="checkbox" class="toggle-extra" data-target="print-info" checked> Print Info</label><br>
    <hr>

    <div class="row mt-3">

        <div class="col-6">
            <button onclick="printDocument()" class="btn btn-success btn-sm w-100">
                <i class="fas fa-print mr-1"></i> Print
            </button>
        </div>

        <div class="col-6">
            <button class="btn btn-danger btn-sm w-100" onclick="savePdf(this)">
                <i class="fas fa-file-pdf  mr-1"></i> Save PDF
            </button>
        </div>

    </div>
</div>