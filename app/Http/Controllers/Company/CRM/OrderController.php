<?php

namespace App\Http\Controllers\Company\CRM;

use App\Http\Controllers\Controller;
use App\Models\Bom;
use App\Models\BomItem;
use App\Models\Company;
use App\Models\Component;
use App\Models\Lead;
use App\Models\OrderItem;
use App\Models\OrderItemStageStatus;
use App\Models\ProductionStage;
use App\Models\ProductionStatus;
use App\Models\Quotation;
use App\Models\Machine;
use App\Models\Order;
use App\Models\User;
use App\Models\QuotationFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use App\Models\Planning;
use Mpdf\Mpdf;
use Illuminate\Support\Str;
class OrderController extends Controller
{
    public function index(Request $request, Company $company)
    {
        return view('company.crm.orders.index', [
            'company' => $company,
            'title' => Auth::user()->name . ":: Order Management",
            'label' => "Order List"
        ]);
    }
    public function create(Company $company)
    {
        $users = User::role([
            'staff',
            'admin',
            'super admin'
        ])->get();
        $machines = Machine::all();
        $components = Component::all();
        $companies = Company::orderBy('company_name')->get();

        $title = Auth::user()->name . " Order Management";
        $label = "Add Order";

        return view('company.crm.orders.create', compact(
            'company',
            'companies',
            'users',
            'machines',
            'components',
            'title',
            'label'
        ));
    }
    public function store(Request $request, Company $company)
    {
        // dd($request->all());
        $request->validate([
            'order_date' => 'required|date_format:d/m/Y',
            'po_date' => 'nullable|date_format:d/m/Y',
            'delivery_date' => 'required|date_format:d/m/Y',

            'assigned_user_id' => 'required|exists:users,id',
            'customer_name' => 'required|max:255',

            'item_id.*' => 'nullable|integer',
            'item_type.*' => 'nullable|in:machine,component',
            'quantity.*' => 'nullable|numeric',
            'unit_price.*' => 'nullable|numeric',
            'total.*' => 'nullable|numeric',

            'total_amount' => 'nullable|numeric',
            'discount' => [
                'nullable',
                'numeric',
                'min:0',
                function ($a, $v, $fail) use ($request) {
                    if ($v > $request->total_amount) {
                        $fail('Discount cannot be greater than Total Amount.');
                    }
                }
            ],
            'tax' => 'nullable|numeric',
            'final_amount' => 'nullable|numeric',
        ]);
        /* ================= CREATE ORDER ================= */


        $orderDate = Carbon::createFromFormat('d/m/Y', $request->order_date)->format('Y-m-d');

        $poDate = $request->po_date
            ? Carbon::createFromFormat('d/m/Y', $request->po_date)->format('Y-m-d')
            : null;

        $deliveryDate = Carbon::createFromFormat('d/m/Y', $request->delivery_date)->format('Y-m-d');

        $order = Order::create([
            'company_id' => $company->id,
            'quotation_id' => $request->quotation_id,
            'lead_id' => $request->lead_id,
            'user_id' => auth()->id(),
            'assigned_user_id' => $request->assigned_user_id,

            'order_date' => $orderDate,
            'po_date' => $poDate,
            'delivery_date' => $deliveryDate,

            'contact_person' => $request->contact_person,
            'delivery_address' => $request->delivery_address,

            'remark' => $request->remark,
            'hi_remark' => $request->hi_remark,
            'terms_conditions' => $request->terms_conditions,
            'hi_terms_conditions' => $request->hi_terms_conditions,
            'currency' => $request->currency,
            'conversion_rate' => $request->conversion_rate,
            'total_amount' => (float) $request->total_amount,
            'discount' => (float) $request->discount,
            'tax' => (float) $request->tax,
            'tax_amount' => (float) $request->tax_amount,
            'final_amount' => (float) $request->final_amount,
        ]);

        /* ================= ORDER NUMBER ================= */

        $date = now()->format('ymd');
        $leadCode = $order->quotation->lead->id;
        $prefix = $company->initials() . '#' . $order->quotation->assigned_user_id;
        $order->po_number = 'PO' . $prefix
            . '/'
            . $date
            . '/'
            . $leadCode
            . '/'
            . $order->id;

        $order->order_number =
            'ORD' . $prefix
            . '/'
            . $date
            . '/'
            . $leadCode
            . '/'
            . $order->id;
        $order->save();

        if ($request->update_customer_master == 1 && $request->lead_id) {

            $lead = Lead::with('customer')->find($request->lead_id);

            if ($lead && $lead->customer) {

                $lead->customer->update([
                    'name' => $request->customer_name,
                    'email' => $request->filled('email') ? $request->email : null,
                    'gst' => $request->customer_gst,
                    'address' => $request->office_address,
                ]);

                // Update primary phone safely
                if ($request->mobile) {
                    $lead->customer->phones()
                        ->where('is_primary', 1)
                        ->update([
                            'phone' => $request->mobile
                        ]);
                }
            }
        }

        Quotation::where('id', $request->quotation_id)
            ->update(['status' => 'converted']);
        Lead::where('id', $request->lead_id)
            ->update(['status' => 'ordered']);

        /* ================= SAVE ITEMS ================= */

        if ($request->item_id) {
            foreach ($request->item_id as $i => $itemId) {

                if (!$itemId)
                    continue;
                $type = $request->item_type[$i] ?? null;
                $hiName = $request->item_name_hindi[$i] ?? null;

                if ($type === 'machine' && $hiName) {

                    $machine = Machine::find($itemId);

                    if ($machine) {
                        $machine->update([
                            'hi_name' => $hiName,
                        ]);
                    }

                } elseif ($type === 'component' && $hiName) {

                    $component = Component::find($itemId);

                    if ($component) {
                        $component->update([
                            'hi_name' => $hiName,
                        ]);
                    }
                }
                $order->items()->create([
                    'machine_id' => $request->item_type[$i] === 'machine' ? $itemId : null,
                    'component_id' => $request->item_type[$i] === 'component' ? $itemId : null,
                    'description' => $request->description_html[$i] ?? null,
                    'hi_description' => $request->description_hi_html[$i] ?? null,
                    'quantity' => (float) $request->quantity[$i],
                    'unit_price' => (float) $request->unit_price[$i],
                    'total_price' => (float) $request->total[$i],
                    'converted_total_price' => (float) ($request->converted_total_price[$i] ?? 0),
                    'sort_order' => $request->sort_order[$i] ?? ($i + 1),
                ]);
            }
        }

        /* ================= UPLOAD FILES ================= */

        $path_load = config('url.public_path');

        if ($request->hasFile('uploads')) {

            if (!file_exists($path_load . '/orders')) {
                mkdir($path_load . '/orders', 0777, true);
            }

            foreach ($request->file('uploads') as $file) {

                if (!$file->isValid()) {
                    continue;
                }

                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // move file (same as quotation)
                $file->move($path_load . '/orders', $filename);

                $order->files()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => 'orders/' . $filename,
                    'uploaded_by' => auth()->id(), // ✅ keep consistency
                ]);
            }
        }
        /* ================= COPY QUOTATION FILES TO ORDER ================= */



        if ($request->existing_file_ids && $order->files()->count() === 0) {

            $quotationFiles = QuotationFile::whereIn(
                'id',
                $request->existing_file_ids
            )->get();

            foreach ($quotationFiles as $qFile) {
                $order->files()->create([
                    'file_name' => $qFile->file_name,
                    'file_path' => $qFile->file_path,
                    'uploaded_by' => auth()->id(),
                ]);
            }
        }

        /* ================= REPLACE FILES ================= */

        if ($request->replace_file_ids) {

            foreach ($request->replace_file_ids as $fileId) {

                $inputName = 'replace_file_' . $fileId;

                if (!$request->hasFile($inputName)) {
                    continue;
                }

                $file = $request->file($inputName);

                if (!$file->isValid()) {
                    continue;
                }

                if (!file_exists($path_load . '/orders')) {
                    mkdir($path_load . '/orders', 0777, true);
                }

                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($path_load . '/orders', $filename);

                $order->files()
                    ->where('id', $fileId)
                    ->update([
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => 'orders/' . $filename,
                    ]);
            }
        }
        /* ================= DELETE FILES ================= */

        if ($request->deleted_files) {
            $order->files()
                ->whereIn('id', $request->deleted_files)
                ->delete();
        }

        notifyAdmins(
            'New Order Created',
            "Order {$order->order_number} created",
            route('orders.edit', [$order->company_id, $order->id]),
            'success'
        );
        toast('Order Created Successfully', 'success');
        return redirect()->route('orders.index', $company->id);
    }
    public function generateNumber(Request $request)
    {
        if (!$request->lead_id) {
            return response()->json([
                'order' => 'Select Quotation First',
                'po' => 'Select Quotation First'
            ]);
        }

        /*
        Next Order preview id
        */
        $nextId = (Order::max('id') ?? 0) + 1;

        /*
        Date YYMMDD
        */
        $date = now()->format('ymd');

        /*
        Get Lead Code
        */
        $leadCode = 'LEAD';

        $lead = Lead::find($request->lead_id);

        if ($lead && $lead->id) {
            $leadCode = $lead->id;
        }

        /*
        Generate numbers
        */
        $orderNumber =
            'ORD'
            . '/'
            . $date
            . '/'
            . $leadCode
            . '/'
            . $nextId;

        $poNumber =
            'PO'
            . '/'
            . $date
            . '/'
            . $leadCode
            . '/'
            . $nextId;

        return response()->json([
            'order' => $orderNumber,
            'po' => $poNumber,
            'today' => now()->format('d/m/Y')
        ]);
    }
    public function edit(Company $company, Order $order)
    {
        abort_if($order->company_id != $company->id, 403);

        $users = User::role([
            'staff',
            'admin',
            'super admin'
        ])->get();
        $machines = Machine::all();
        $components = Component::all();

        $title = Auth::user()->name . " Order Management";
        $label = "Edit Order";

        // Load relations
        $order->load([
            'items.machine',
            'items.component',
            'files',
            'quotation.lead.customer.phones',
            'quotation.lead.customer.country',
        ]);
        $statuses = Order::STATUSES;

        return view('company.crm.orders.edit', compact(
            'company',
            'order',
            'users',
            'machines',
            'components',
            'statuses',
            'title',
            'label'
        ));
    }
    public function update(Request $request, Company $company, Order $order)
    {
        $request->validate([
            'order_date' => 'required|date_format:d/m/Y',
            'po_date' => 'nullable|date_format:d/m/Y',
            'delivery_date' => 'required|date_format:d/m/Y',
            'assigned_user_id' => 'required|exists:users,id',
            'customer_name' => 'required|max:255',
            'item_id.*' => 'nullable|integer',
            'item_type.*' => 'nullable|in:machine,component',
            'quantity.*' => 'nullable|numeric',
            'unit_price.*' => 'nullable|numeric',
            'total.*' => 'nullable|numeric',
            'total_amount' => 'nullable|numeric',
            'status' => 'required',
            'discount' => [
                'nullable',
                'numeric',
                'min:0',
                function ($a, $v, $fail) use ($request) {
                    if ($v > $request->total_amount) {
                        $fail('Discount cannot be greater than Total Amount.');
                    }
                }
            ],
            'tax' => 'nullable|numeric',
            'final_amount' => 'nullable|numeric',
        ]);

        /* ================= UPDATE ORDER ================= */

        $order->update([
            'assigned_user_id' => $request->assigned_user_id,
            'order_date' => Carbon::createFromFormat('d/m/Y', $request->order_date)->format('Y-m-d'),
            'po_date' => $request->po_date
                ? Carbon::createFromFormat('d/m/Y', $request->po_date)->format('Y-m-d')
                : null,
            'delivery_date' => Carbon::createFromFormat('d/m/Y', $request->delivery_date)->format('Y-m-d'),
            'delivery_address' => $request->delivery_address,
            'remark' => $request->remark,
            'hi_remark' => $request->hi_remark,
            'terms_conditions' => $request->terms_conditions,
            'hi_terms_conditions' => $request->hi_terms_conditions,
            'total_amount' => (float) $request->total_amount,
            'discount' => (float) $request->discount,
            'tax' => (float) $request->tax,
            'currency' => $request->currency,
            'status' => $request->status,
            'conversion_rate' => $request->conversion_rate,
            'tax_amount' => (float) $request->tax_amount,
            'final_amount' => (float) $request->final_amount,
        ]);

        /* ================= UPDATE ITEMS ================= */


        if ($request->item_id) {

            foreach ($request->item_id as $i => $itemId) {

                if (!$itemId) {
                    continue;
                }

                $type = $request->item_type[$i] ?? null;
                $hiName = $request->item_name_hindi[$i] ?? null;

                // Update only Hindi Name in master tables
                if ($type === 'machine' && $hiName) {

                    Machine::where('id', $itemId)->update([
                        'hi_name' => $hiName,
                    ]);

                } elseif ($type === 'component' && $hiName) {

                    Component::where('id', $itemId)->update([
                        'hi_name' => $hiName,
                    ]);
                }

                $order->items()->updateOrCreate(
                    ['id' => $request->row_item_id[$i] ?? null],
                    [
                        'machine_id' => $type === 'machine' ? $itemId : null,
                        'component_id' => $type === 'component' ? $itemId : null,
                        'description' => $request->description_html[$i] ?? null,
                        'hi_description' => $request->description_hi_html[$i] ?? null,
                        'quantity' => $request->quantity[$i],
                        'unit_price' => $request->unit_price[$i],
                        'total_price' => $request->total[$i],
                        'converted_total_price' => (float) ($request->converted_total_price[$i] ?? 0),
                        'sort_order' => $request->sort_order[$i],
                    ]
                );
            }
        }
        /* ================= HANDLE FILE UPLOADS (LIKE QUOTATION) ================= */

        $path_load = config('url.public_path');

        if ($request->hasFile('uploads')) {

            if (!file_exists($path_load . '/orders')) {
                mkdir($path_load . '/orders', 0777, true);
            }

            foreach ($request->file('uploads') as $file) {

                if (!$file->isValid()) {
                    continue;
                }

                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // move file (same as quotation)
                $file->move($path_load . '/orders', $filename);

                $order->files()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => 'orders/' . $filename,
                    'uploaded_by' => auth()->id(), // ✅ keep consistency
                ]);
            }
        }
        /* ================= COPY QUOTATION FILES TO ORDER ================= */



        if ($request->existing_file_ids && $order->files()->count() === 0) {

            $quotationFiles = QuotationFile::whereIn(
                'id',
                $request->existing_file_ids
            )->get();

            foreach ($quotationFiles as $qFile) {
                $order->files()->create([
                    'file_name' => $qFile->file_name,
                    'file_path' => $qFile->file_path,
                    'uploaded_by' => auth()->id(),
                ]);
            }
        }

        /* ================= REPLACE FILES ================= */

        if ($request->replace_file_ids) {

            foreach ($request->replace_file_ids as $fileId) {

                $inputName = 'replace_file_' . $fileId;

                if (!$request->hasFile($inputName)) {
                    continue;
                }

                $file = $request->file($inputName);

                if (!$file->isValid()) {
                    continue;
                }

                if (!file_exists($path_load . '/orders')) {
                    mkdir($path_load . '/orders', 0777, true);
                }

                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($path_load . '/orders', $filename);

                $order->files()
                    ->where('id', $fileId)
                    ->update([
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => 'orders/' . $filename,
                    ]);
            }
        }
        /* ================= DELETE FILES ================= */

        if ($request->deleted_files) {
            $order->files()
                ->whereIn('id', $request->deleted_files)
                ->delete();
        }
        // ✅ DELETE REMOVED ITEMS
        if ($request->deleted_items_ids) {
            $order->items()
                ->whereIn('id', $request->deleted_items_ids)
                ->delete();
        }

        if ($order->lead_id) {

            $lead = Lead::with('customer')->find($order->lead_id);

            if ($lead && $lead->customer) {

                $lead->customer->update([
                    'name' => $request->customer_name,
                    'email' => $request->filled('email') ? $request->email : null,
                    'gst' => $request->customer_gst,
                    'address' => $request->office_address,
                ]);

                if ($request->mobile) {
                    $lead->customer->phones()
                        ->where('is_primary', 1)
                        ->update([
                            'phone' => $request->mobile
                        ]);
                }
            }
        }

        toast('Order Updated Successfully', 'success');
        return redirect()->route('orders.index', $company->id);
    }
    public function destroy(Company $company, Order $order)
    {
        abort_if($order->company_id != $company->id, 403);

        /*
        |--------------------------------------------------------------------------
        | RESTRICT IF PAYMENTS EXIST
        |--------------------------------------------------------------------------
        */
        if ($order->payments()->exists()) {

            toast('Cannot delete! Order has payments.', 'error');

            return back();
        }

        /*
        |--------------------------------------------------------------------------
        | RESTRICT IF BOM EXISTS
        |--------------------------------------------------------------------------
        */
        if ($order->boms()->exists()) {

            toast('Cannot delete! BOM already created for this order.', 'error');

            return back();
        }

        $quotationId = $order->quotation_id;

        /*
        |--------------------------------------------------------------------------
        | DELETE ORDER FILES
        |--------------------------------------------------------------------------
        */
        foreach ($order->files as $file) {

            // optional physical delete
            if ($file->file && \Storage::exists($file->file)) {

                \Storage::delete($file->file);
            }

            $file->delete();
        }

        /*
        |--------------------------------------------------------------------------
        | DELETE ORDER ITEMS
        |--------------------------------------------------------------------------
        */
        $order->items()->delete();

        /*
        |--------------------------------------------------------------------------
        | DELETE ORDER
        |--------------------------------------------------------------------------
        */
        $order->delete();

        /*
        |--------------------------------------------------------------------------
        | ROLLBACK QUOTATION STATUS
        |--------------------------------------------------------------------------
        */
        if ($quotationId) {

            $remainingOrders = Order::where(
                'quotation_id',
                $quotationId
            )->count();

            if ($remainingOrders === 0) {

                Quotation::where('id', $quotationId)
                    ->update([
                        'status' => 'draft'
                    ]);
            }
        }

        toast('Order deleted successfully', 'success');

        return redirect()->route(
            'orders.index',
            $company->id
        );
    }
    public function print(Company $company, Order $order)
    {
        $order->load([
            'items' => fn($q) => $q->orderBy('sort_order'),
            'items.machine',
            'items.component',
            'quotation.lead.customer.primaryPhone',
            'assignedUser',
            'company',
        ]);
        $user = Auth::user();
        $settings = Setting::first();
        $title = $user->name . " Order Print Preview";
        $label = 'Preview Order';

        return view('company.crm.orders.print', compact(
            'company',
            'order',
            'settings',
            'title',
            'label'
        ));
    }

    public function pdf($companyId, $orderId, Request $request)
    {
        $lang = $request->lang ?? 'en';
        $company = Company::findOrFail($companyId);

        $order = Order::with([
            'items.machine',
            'items.component',
            'quotation.lead.customer.primaryPhone',
            'quotation.lead.customer.country',
            'assignedUser'
        ])->findOrFail($orderId);

        $settings = Setting::first();

        $sections = array_filter(explode(',', $request->sections ?? ''));
        $extras = array_filter(explode(',', $request->extras ?? ''));
        $columns = array_filter(explode(',', $request->columns ?? ''));
        $docType = $request->doc_type ?? 'order';
        $currency = ($request->currency && $request->currency !== 'undefined')
            ? $request->currency
            : $order->currency;
        $rows = $request->has('rows') ? explode(',', $request->rows) : [];

        if (!empty($rows)) {
            $order->setRelation(
                'items',
                $order->items->filter(function ($item, $index) use ($rows) {
                    return in_array((string) $index, $rows, true);
                })->values()
            );
        }

        $currencySymbols = [
            'INR' => '₹',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£'
        ];

        $currencySymbol = $currencySymbols[$currency] ?? '₹';

        $html = view(
            'company.crm.orders.pdf',
            compact(
                'order',
                'settings',
                'sections',
                'company',
                'extras',
                'columns',
                'docType',
                'currencySymbol',
                'lang'
            )
        )->render();
        $config = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $config['fontDir'];

        $fontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $fontConfig['fontdata'];

        $customFontDir = public_path('fonts');

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 28,
            'margin_header' => 5,
            'margin_bottom' => 12,
            'margin_footer' => 2,
            'tempDir' => storage_path('app/mpdf'),
            'fontDir' => array_merge($fontDirs, [
                public_path('fonts')
            ]),
            'fontdata' => $fontData + [
                'sourcesans' => [
                    'R' => 'SourceSans3-Regular.ttf',
                    'B' => 'SourceSans3-Bold.ttf',
                ],
                'sourcesanssemibold' => [
                    'R' => 'SourceSans3-SemiBold.ttf',
                ],
                'sourcesansmedium' => [
                    'R' => 'SourceSans3-Medium.ttf',
                ],
                'sourcesansextrabold' => [
                    'R' => 'SourceSans3-ExtraBold.ttf',
                ],
                'notohindi' => [
                    'B' => 'NotoSansDevanagari-Bold.ttf',
                    'R' => 'NotoSansDevanagari-Regular.ttf',
                ],
                'sourcesansblack' => [
                    'R' => 'SourceSans3-Black.ttf',
                ],
            ],
            'default_font' => 'sourcesans'
        ]);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $footerHtml = '
<table width="100%" style="border-collapse:collapse;">
    <tr>
        <td style="
            background:#2b4c7e;
            color:#ffffff;
            font-size:9px;
            padding:8px 12px;
        ">
            ESTD 1966 | India\'s Most Preferred Pulveriser Brand |
            ICICI Bank, Ashok Nagar, Kanpur |
            A/C: 083205004030 | IFSC: ICIC0000832
        </td>

        <td style="
            background:#2b4c7e;
            color:#ffffff;
            font-size:10px;
            text-align:right;
            padding:8px 12px;
            width:90px;
            white-space:nowrap;
        ">
            Page {PAGENO} of {nbpg}
        </td>
    </tr>
</table>
';

        $mpdf->SetHTMLFooter($footerHtml);
        $headerHtml = '
<table width="100%" style="border-collapse:collapse;">
    <tr>
        <td width="40%">
            <img src="' . asset('admin/uploads/logo/' . $settings->logo) . '" width="220">
        </td>

        <td width="60%" align="right" style="font-size:11px;line-height:1.6;">
            <div style="font-weight:bold;">
                <div>
                    +' . ($company->country->phonecode ?? '') . '-' . $company->mobile . ($company->alternate_mobile ? ' | +' . ($company->country->phonecode ?? '') . '-' . $company->alternate_mobile : '') . '
                </div>

                <div>' . $company->email . ($company->website ? ' | ' . $company->website : '') . '
                </div>

                <div>
                    ' . $company->address . ',
                    ' . ($company->city->name ?? '') . ',
                    ' . ($company->state->name ?? '') . ',
                    ' . ($company->country->name ?? '') . '
                    - ' . $company->pincode . '
                </div>
            </div>

            <div style="font-size:11px;">
                GST In: ' . $company->gstin_no . ' |
                IEC: ' . $company->iec_code . ' |
                PAN: ' . $company->pan_no . ' |
                ESTD 1966
            </div>
        </td>
    </tr>
</table>

<div style="height:6px;background:#2cca38;"></div>
<div style="height:6px;background:#0b3d6d;"></div>
';

        $mpdf->SetHTMLHeader($headerHtml);
        $mpdf->WriteHTML($html);


        $initials = $company->initials();
        $documentLabel = $docType === 'po' ? 'PO' : 'Order';
        $number = $docType === 'po' ? $order->po_number : $order->order_number;
        $cleanNumber = preg_replace('/[^A-Za-z0-9\-]/', '', $number);
        $dateTime = Carbon::now()->format('Ymd-His');

        $customerName = $order->customer_name;
        $fileName = $this->generateFileName(
            $company,
            $docType,
            $number,
            $customerName,
            $order->quotation->lead->id,     // or ->id
            $order->quotation->assigned_user_id
        );
        $pdfContent = $mpdf->Output('', 'S');
        return response($pdfContent, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }
    public function ajaxOrderDetails(Request $request, Company $company)
    {
        $order = Order::with([
            'items.machine',
            'items.component',
            'quotation.lead.customer.primaryPhone',
            'quotation.lead.customer.country',
            'files'
        ])
            ->where('company_id', $company->id)
            ->findOrFail($request->id);

        $customer = optional($order->quotation?->lead?->customer);
        $country = optional($customer)->country;
        return response()->json([
            ...$order->toArray(),
            'files' => $order->files->map(function ($file) {
                return [
                    'file_name' => $file->file_name,
                    'file_path' => $file->file_path,
                    'url' => asset('admin/uploads/' . $file->file_path),
                ];
            }),

            // ✅ CUSTOMER SNAPSHOT (SAFE)
            'customer_name' => $order->customer_name ?? $customer->name,
            'email' => $order->email ?? $customer->email,
            'mobile' => $order->mobile ?? optional($customer->primaryPhone)->phone,
            'customer_gst' => $customer->gst ?? null,
            'currency_symbol' => $order->currency_symbol,
            'country' => [
                'name' => $country->name ?? null,
                'phonecode' => $country->phonecode ?? null,
            ],
        ]);
    }
    public function data(Request $request, Company $company)
    {
        $user = auth()->user();
        $query = Order::with([
            'creator',
            'assignedUser'
        ])
            ->where('company_id', $company->id)
            ->where(function ($q) {
                $q->whereHas('lead.customer') // direct
                    ->orWhereHas('quotation.lead.customer'); // indirect
            });

        $search = $request->search;
        if ($search === "" || $search === null) {
            $search = null;
        }

        $from = $request->from_date ?: null;
        $to = $request->to_date ?: null;

        // CASE 1 → SEARCH ONLY
        if ($search) {

            $query->where(function ($q) use ($search) {

                $q->where('order_number', 'LIKE', "%{$search}%");

                if (is_numeric($search)) {
                    $q->orWhere('id', $search);
                }

                // DIRECT LEAD CUSTOMER
                $q->orWhereHas('lead.customer', function ($customer) use ($search) {
                    $customer->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                });

                $q->orWhereHas('lead.customer.phones', function ($phone) use ($search) {
                    $phone->where('phone', 'LIKE', "%{$search}%");
                });

                // QUOTATION CUSTOMER
                $q->orWhereHas('quotation.lead.customer', function ($customer) use ($search) {
                    $customer->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                });

                $q->orWhereHas('quotation.lead.customer.phones', function ($phone) use ($search) {
                    $phone->where('phone', 'LIKE', "%{$search}%");
                });

            });
        }
        if ($from) {
            $query->whereDate('order_date', '>=', $from);
        }

        if ($to) {
            $query->whereDate('order_date', '<=', $to);
        }

        // CASE 3 → NOTHING → TODAY ONLY
        if ($search === null && $from === null && $to === null) {
            $query->latest()->limit(10);
        }

        $orders = $query->latest()->get()->map(function ($order) {

            $order->progress_percent = 0; // default
            $order->is_delayed = false;

            return $order;
        });

        return view('company.crm.orders.partials.order_rows', [
            'orders' => $orders,
            'company' => $company
        ])->render();
    }
    public function ajaxSearch(Request $request, Company $company)
    {
        $search = $request->search;

        $orders = Order::with([
            'quotation.lead.customer.primaryPhone'
        ])
            ->where('company_id', $company->id)
            ->where(function ($q) use ($search) {

                $q->where('order_number', 'LIKE', "%{$search}%")

                    ->orWhereHas('quotation.lead.customer', function ($qc) use ($search) {
                        $qc->where('name', 'LIKE', "%{$search}%");
                    })

                    ->orWhereHas('quotation.lead.customer.phones', function ($qp) use ($search) {
                        $qp->where('phone', 'LIKE', "%{$search}%");
                    });

            })
            ->limit(20)
            ->get();

        return $orders->map(function ($o) {
            $customer = optional($o->quotation?->lead?->customer);
            $phone = optional($customer?->primaryPhone)->phone;

            return [
                'id' => $o->id,
                'order_number' => $o->order_number,
                'customer_name' => $customer->name ?? '',
                'mobile' => $phone ?? '',
                'delivery_date' => $o->delivery_date?->format('Y-m-d')
            ];
        });
    }
    public function getQuotationForOrder(Request $request)
    {
        $q = Quotation::with([
            'lead.customer.phones',
            'lead.customer.country',
            'assignedUser',
            'company',
            'items.machine',
            'items.component',
            'files'
        ])->findOrFail($request->id);

        $customer = optional($q->lead)->customer;
        $country = optional($customer)->country;
        return response()->json([
            ...$q->toArray(),

            // ✅ CUSTOMER SNAPSHOT
            'customer_name' => $customer->name ?? null,
            'email' => $customer->email ?? null,
            'customer_gst' => $customer->gst ?? null,
            'phones' => $customer->phones->pluck('phone')->toArray(),
            'currency' => $q->currency,
            'conversion_rate' => $q->conversion_rate,

            'country' => [
                'name' => $country->name ?? null,
                'phonecode' => $country->phonecode ?? null,
            ],
            'office_address' => $customer->address ?? null,

            'pi_date' => $q->pi_date
                ? Carbon::parse($q->pi_date)->format('d/m/Y')
                : null,
        ]);
    }
    private function generateFileName(
        $company,
        $docType,
        $number,
        $customerName,
        $leadCode,
        $assignedUserId
    ) {

        $initials = $company->initials();

        $userId = $assignedUserId;

        // Document short codes
        $docMap = [
            'quotation' => 'Q',
            'pi' => 'PI',
            'order' => 'O',
            'po' => 'PO',
            'payment' => 'PAY'
        ];

        $docInitial = $docMap[$docType]
            ?? strtoupper(substr($docType, 0, 2));

        // First 2 words of customer name
        $words = explode(' ', trim($customerName));

        $firstTwoWords = array_slice($words, 0, 2);

        $cleanCustomer = preg_replace(
            '/[^A-Za-z0-9]/',
            '',
            implode('', $firstTwoWords)
        );

        return "{$initials}#{$userId}{$docInitial}{$leadCode}-{$cleanCustomer}.pdf";
    }
    public function proformaPreview(Request $request, Company $company, Order $order)
    {
        $order->load([
            'quotation.lead.customer.country',
            'quotation.lead.customer.primaryPhone',
            'items.machine',
            'items.component',
            'payments'
        ]);
        $currency = $request->currency ?? 'INR';

        $currencyMap = [
            'INR' => '₹',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£'
        ];

        $currencySymbol = $currencyMap[$currency] ?? '₹';
        $payments = $order->payments->sortBy('payment_date');
        $settings = Setting::first();
        $user = Auth::user();
        $settings = Setting::first();
        $title = $user->name . " Proforma Invoice / Post Order Print Preview";
        $label = 'Preview PI / PO';
        return view('company.crm.orders.proforma-preview', compact(
            'company',
            'label',
            'title',
            'order',
            'payments',
            'settings',
            'currencySymbol'
        ));
    }
    public function proformaInvoice(Request $request, $companyId, $orderId)
    {
        $company = Company::findOrFail($companyId);

        $order = Order::with([
            'quotation.lead.customer.country',
            'quotation.lead.customer.primaryPhone',
            'items.machine',
            'items.component',
            'payments'
        ])->findOrFail($orderId);

        $payments = $order->payments->sortBy('payment_date');

        $settings = Setting::first();
        $sections = array_filter(explode(',', $request->sections ?? ''));
        $extras = array_filter(explode(',', $request->extras ?? ''));
        $currency = ($request->currency && $request->currency !== 'undefined')
            ? $request->currency
            : $order->currency;
        $currencyMap = [
            'INR' => '₹',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£'
        ];
        $type = $request->type ?? 'pi';
        $currencySymbol = $currencyMap[$currency] ?? '₹';
        $pdf = Pdf::loadView(
            'company.crm.orders.proforma-invoice',
            compact(
                'company',
                'order',
                'payments',
                'settings',
                'sections',
                'extras',
                'currencySymbol',
                'type'
            )
        );

        $pdf->setPaper('A4', 'portrait');
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        $canvas = $dompdf->getCanvas();

        $canvas->page_text(
            520,
            823,
            "Page {PAGE_NUM} of {PAGE_COUNT}",
            null,
            7,
            [255, 255, 255]
        );

        $number = $order->quotation->pi_number ?? $order->order_number;
        $customerName = $order->quotation->lead->customer->name ?? $order->customer_name;

        $fileName = $this->generateFileName(
            $company,
            $type,
            $number,
            $customerName,
            $order->quotation->lead->lead_code,     // or ->id
            $order->quotation->assigned_user_id
        );
        return $pdf->download($fileName);
    }

    public function saveTranslation(Request $request)
    {
        $orderId = $request->order_id;
        $lang = $request->lang;

        $translations = collect($request->translations)->map(function ($t) {
            $t['text'] = preg_replace('/<\?xml.*?\?>/i', '', $t['text']);
            return $t;
        })->toArray();

        $key = "order_pdf_{$orderId}_{$lang}";

        Cache::put($key, $translations, now()->addMinutes(30));

        return response()->json(['status' => 'saved']);
    }
    public function getTranslation(Request $request)
    {
        $orderId = $request->order_id;
        $lang = $request->lang;

        $key = "order_pdf_{$orderId}_{$lang}";

        return response()->json([
            'translations' => Cache::get($key)
        ]);
    }

    public function orderItemsForBom(Request $request, Company $company)
    {
        $order = Order::with(['items.machine', 'items.component'])
            ->where('company_id', $company->id)
            ->findOrFail($request->order_id);

        return response()->json([
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'order_remark' => $order->remark, // 🔥 ADD THIS

            'items' => $order->items->map(function ($item) {
                $hasBom = BomItem::where('order_item_id', $item->id)->exists();
                return [
                    'id' => $item->id,
                    'description' => $item->description,
                    'name' => optional($item->machine)->name
                        ?? optional($item->component)->name
                        ?? 'Item', // 🔥 ITEM NAME
                    'quantity' => $item->quantity,
                    'machine' => optional($item->machine)->name,
                    'component' => optional($item->component)->name,
                    'has_bom' => $hasBom,
                ];
            })
        ]);
    }
    public function productionDetail(Company $company, Order $order)
    {
        if ($order->company_id != $company->id) {
            abort(404);
        }

        $title = 'Production Tracking';
        $label = 'Order #' . $order->order_number;

        /*
        ✅ Statuses (keep global)
        */
        $statuses = ProductionStatus::orderBy('sequence')->get();

        /*
        🔥 LOAD FULL HIERARCHY
        Order → Items → BOM → Parts → Stages → Tracking
        */
        $order->load([

            'items.machine',
            'items.component',

            // 🔥 BOM + PARTS
            'items.bom.parts.stages',

            // 🔥 TRACKING (UPDATED TABLE)
            'items.stageTracking.stage',
            'items.stageTracking.status'
        ]);

        /*
        🔥 MAP DATA FOR EASY BLADE ACCESS
        */
        foreach ($order->items as $item) {

            // Map tracking by stage_id
            $item->stage_map = $item->stageTracking
                ->keyBy('order_item_stage_id');

            // Optional: group by part
            if ($item->bom && $item->bom->parts) {

                foreach ($item->bom->parts as $part) {

                    $part->stages = $part->stages->sortBy('sequence');

                    // attach tracking inside each stage
                    foreach ($part->stages as $stage) {

                        $stage->tracking = $item->stage_map[$stage->id] ?? null;
                    }
                }
            }
        }

        return view(
            'company.crm.orders.production',
            compact(
                'company',
                'order',
                'statuses',
                'title',
                'label'
            )
        );
    }
    public function storeProductionStage(Request $request, Company $company)
    {

        $request->validate([

            'name' => 'required',

            'code' => 'nullable|unique:production_stages,code'

        ]);


        $last =
            ProductionStage::max(
                'sequence'
            );


        $stage =
            ProductionStage::create([

                'name' => $request->name,

                'code' =>
                    $request->code
                    ? Str::slug($request->code)
                    : Str::slug($request->name),

                'sequence' =>
                    ($last ?? 0) + 1,

                'active' => 1

            ]);


        /*
        Auto add new stage
        for all order items
        */

        $pending =
            ProductionStatus::firstOrCreate(
                [
                    'name' => 'Pending'
                ],
                [
                    'badge_color' => 'secondary'
                ]
            );


        foreach (
            OrderItem::all()
            as $item
        ) {

            OrderItemStageStatus::firstOrCreate(

                [
                    'order_item_id' => $item->id,

                    'production_stage_id' => $stage->id

                ],

                [
                    'production_status_id' => $pending->id
                ]

            );

        }


        return back()
            ->with(
                'success',
                'Stage Added Successfully'
            );

    }
    public function storeProductionStatus(Request $request, Company $company)
    {
        $request->validate([

            'name' =>
                'required|unique:production_statuses,name'

        ]);


        ProductionStatus::create([

            'name' =>
                $request->name,

            'badge_color' =>
                $request->badge_color

        ]);


        return back()
            ->with(
                'success',
                'Status Added Successfully'
            );

    }
    public function updateProductionItem(Request $request, Company $company)
    {
        $request->validate([

            'item_id' => 'required',

            'statuses' => 'required|array'

        ]);


        /*
        Save all stage statuses
        for this item
        */

        foreach (
            $request->statuses
            as $stageId => $statusId
        ) {

            OrderItemStageStatus::updateOrCreate(

                [
                    'order_item_id' =>
                        $request->item_id,

                    'production_stage_id' =>
                        $stageId

                ],

                [
                    'production_status_id' =>
                        $statusId,

                    'remarks' =>
                        $request->remarks,

                    'updated_by' =>
                        auth()->id()

                ]

            );

        }


        /*
        Sync order status
        */

        $this->syncOrderStatusByItem(
            $request->item_id
        );


        return back()
            ->with(
                'success',
                'Production Updated'
            );

    }
    public function destroyStage($company_id, $id)
    {
        $stage = ProductionStage::findOrFail($id);

        // Get Pending status id
        $pendingId = ProductionStatus::where(
            'name',
            'Pending'
        )->value('id');

        /*
        Block delete only if stage has
        tracking records with NON-pending statuses
        */
        $hasNonPendingUsage =
            $stage->tracking()
                ->where(
                    'production_status_id',
                    '!=',
                    $pendingId
                )
                ->exists();

        if ($hasNonPendingUsage) {

            return response()->json([
                'message' =>
                    'Cannot delete. Stage has progressed production records.'
            ], 422);

        }

        /*
        Optional:
        remove pending tracking rows first
        so FK constraints do not block stage delete
        */
        $stage->tracking()->delete();

        $stage->delete();

        return response()->json([
            'success' => true
        ]);
    }
    public function updateStage(Request $request, $company_id, $id)
    {
        $stage =
            ProductionStage::findOrFail($id);

        $request->validate([

            'name' => 'required',

            'code' =>
                'nullable|unique:production_stages,code,' . $id

        ]);

        $stage->update([

            'name' => $request->name,

            'code' => $request->code

        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Stage Updated Successfully'
            );
    }
    private function syncOrderStatusByItem($itemId)
    {
        $item =
            OrderItem::with(
                'order.items'
            )->find($itemId);

        $order =
            $item->order;


        $dispatchStage =
            ProductionStage::where(
                'code',
                'dispatch'
            )->first();

        if (!$dispatchStage) {
            return;
        }


        $completeId =
            ProductionStatus::where(
                'name',
                'Complete'
            )->value('id');


        $allDispatched = true;


        foreach (
            $order->items as $i
        ) {

            $dispatchTrack =
                OrderItemStageStatus::where(
                    'order_item_id',
                    $i->id
                )

                    ->where(
                        'production_stage_id',
                        $dispatchStage->id
                    )

                    ->first();


            if (
                !$dispatchTrack
                ||
                $dispatchTrack->production_status_id
                !=
                $completeId
            ) {

                $allDispatched = false;

                break;

            }

        }


        if ($allDispatched) {

            $order->update([
                'status' => 'dispatched'
            ]);

        } else {

            $order->update([
                'status' => 'in_production'
            ]);

        }

    }
}

