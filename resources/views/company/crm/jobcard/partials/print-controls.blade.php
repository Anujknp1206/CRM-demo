<h6><b>Show Sections</b></h6>

<label><input type="checkbox" class="toggle-section" data-target="company" checked> Company</label><br>
<label><input type="checkbox" class="toggle-section" data-target="docinfo" checked> Doc Info</label><br>
<label><input type="checkbox" class="toggle-section" data-target="items" checked> Items</label><br>
<label><input type="checkbox" class="toggle-section" data-target="remarks" checked> Remarks</label>

<hr>

<h6><b>Item Columns</b></h6>

<label><input type="checkbox" class="column-toggle" data-col="sn" checked> S.N.</label><br>
<label><input type="checkbox" class="column-toggle" data-col="name" checked> Item</label><br>
<label><input type="checkbox" class="column-toggle" data-col="desc" checked> Description</label><br>
<label><input type="checkbox" class="column-toggle" data-col="qty" checked> Qty</label><br>
<label><input type="checkbox" class="column-toggle" data-col="worker" checked> Worker</label><br>
<label><input type="checkbox" class="column-toggle" data-col="status" checked> Status</label>

<hr>

<h6><b>Item Rows</b></h6>

@foreach($planning->items as $i => $item)
    <label>
        <input type="checkbox" class="row-toggle" data-row="{{ $i }}" checked>
        Item {{ $i + 1 }}
    </label><br>
@endforeach

<hr>

<div class="row mt-3">
    <div class="col-6">
        <button onclick="window.print()" class="btn btn-success btn-sm w-100">
            <i class="fas fa-print"></i> Print
        </button>
    </div>

    <div class="col-6">
        <button onclick="savePdf(this)" class="btn btn-danger btn-sm w-100">
            <i class="fas fa-file-pdf"></i> PDF
        </button>
    </div>
</div>