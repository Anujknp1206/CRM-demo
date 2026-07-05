<div class="print-controls">
    <div class="d-md-none mb-2 text-right">
        <button class="btn btn-danger btn-sm" id="closeControls">
            ✖ Close
        </button>
    </div>
    <h6><b>Language</b></h6>
    <button type="button" id="toHindi" class="btn btn-sm btn-outline-primary w-100 mb-1">
        Convert to Hindi
    </button>

    <button type="button" id="toEnglish" class="btn btn-sm btn-outline-secondary w-100">
        Convert to English
    </button>
    <hr>
    <hr>
    <h6><b>Sections</b></h6>

    <label>
        <input type="checkbox" class="toggle-section" data-target="company" checked>
        Company Info
    </label><br>

    <label>
        <input type="checkbox" class="toggle-section" data-target="docheading" checked>
        Document Heading
    </label><br>
    <label>
        <input type="checkbox" class="toggle-section" data-target="docinfo" checked>
        Document Info
    </label><br>

    <label>
        <input type="checkbox" class="toggle-section" data-target="items" checked>
        Items Table
    </label><br>

    <label>
        <input type="checkbox" class="toggle-section" data-target="instructions" checked>
        Instructions
    </label><br>

    <label>
        <input type="checkbox" class="toggle-section" data-target="signature" checked>
        Signature
    </label>

    <hr>
    <h6><b>Columns</b></h6>

    <label>
        <input type="checkbox" class="column-toggle" data-col="sn" checked>
        Serial Number
    </label><br>
    <label>
        <input type="checkbox" class="column-toggle" data-col="code" checked>
        Item Code
    </label><br>
    <label>
        <input type="checkbox" class="column-toggle" data-col="part" checked>
        Item Name
    </label><br>

    <!-- <label>
        <input type="checkbox" class="column-toggle" data-col="specs" checked>
        Specification
    </label><br> -->
    <label>
        <input type="checkbox" class="column-toggle" data-col="qty" checked>
        Quantity
    </label><br>

    <label>
        <input type="checkbox" class="column-toggle" data-col="remarks" checked>
        Remarks
    </label><br>

    <label>
        <input type="checkbox" class="column-toggle" data-col="notes" checked>
        Notes
    </label>
    <hr>
    <div class="row mt-3">
        <div class="col-6">
            <button onclick="printDocument()" class="btn btn-success btn-sm w-100">
                <i class="fas fa-print mr-1"></i> Print
            </button>
        </div>
        <div class="col-6">
            <button type="button" id="downloadPdf" class="btn btn-danger btn-sm w-100">

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