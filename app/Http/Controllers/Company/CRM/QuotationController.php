<?php
namespace App\Http\Controllers\Company\CRM;
use App\Http\Controllers\Controller;
use App\Models\QuotationItem;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\Quotation;
use App\Models\Company;
use App\Models\Lead;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Machine;
use Illuminate\Validation\Rule;
use App\Models\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

use Mpdf\Mpdf;
class QuotationController extends Controller
{
    public function index(Request $request, Company $company)
    {
        return view('company.crm.quotations.index', [
            'company' => $company,
            'title' => Auth::user()->name . " Quotation Management",
            'label' => "Quotation List"
        ]);
    }
    public function create(Company $company)
    {
        $leads = Lead::all();
        $users = User::role([
            'staff',
            'admin',
            'super admin'
        ])->get();
        $machines = Machine::all();
        $components = Component::all();

        $user = Auth::user();
        $title = $user->name . " Quotation Management";
        $label = "Add Quotation";
        $companies = Company::orderBy('company_name')->get();

        return view('company.crm.quotations.create', compact(
            'leads',
            'users',
            'machines',
            'components',
            'title',
            'label',
            'companies',
            'company',
        ));
    }
    function cleanDescription($text)
    {
        return trim(
            preg_replace(
                '/\s+/',
                ' ',
                strip_tags(html_entity_decode($text))
            )
        );
    }
    public function store(Request $request, Company $company)
    {
        // dd($request->all());
        $path_load = config('url.public_path');
        // ------------------------------
        // VALIDATION
        // ------------------------------
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:20',
            'office_address' => 'nullable|string',
            'gst' => 'nullable|string|max:50',
            'lead_id' => 'nullable|exists:leads,id',
            'quotation_date' => 'required|date',
            'company_id' => 'required|exists:companies,id',
            'assigned_user_id' => 'required|exists:users,id',
            'tax_amount' => 'nullable|numeric',
            'discount' => [
                'nullable',
                'numeric',
                'min:0',
                function ($a, $v, $fail) use ($request) {
                    if ($v > $request->subtotal) {
                        $fail('Discount cannot be greater than Subtotal.');
                    }
                }
            ],
        ]);
        if (!auth()->user()->hasAnyRole(['Super Admin', 'Admin'])) {
            $request->merge([
                'assigned_user_id' => auth()->id(),
            ]);
        }

        $lead = Lead::with('customer')->findOrFail($request->lead_id);

        // 🔁 UPDATE GST ONLY (if provided)
        if ($request->filled('gst_number')) {
            $lead->customer->update([
                'gst' => $request->gst_number
            ]);
        }


        // ------------------------------
        // CREATE BASE QUOTATION
        // ------------------------------
        $quotation = Quotation::create([
            'company_id' => $company->id,
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'contact_person' => $request->contact_person,
            'office_address' => $request->address,
            'delivery_address' => $request->delivery_address,
            'assigned_user_id' => $request->assigned_user_id,
            'quote_date' => $request->quotation_date,
            'pi_date' => $request->pi_date,
            'special_clause' => $request->special_clause,
            'hi_special_clause' => $request->hi_special_clause,
            'terms_conditions' => $request->terms_conditions,
            'hi_terms_conditions' => $request->hi_terms_conditions,
            'currency' => $request->currency,
            'conversion_rate' => $request->conversion_rate ?? 1,
            'total_amount' => $request->subtotal,
            'discount' => $request->discount ?? 0,
            'tax' => $request->tax,
            'tax_amount' => $request->tax_amount,
            'final_amount' => $request->final_total,

        ]);
        $customer = $lead->customer;

        $customer->update([
            'address' => $request->address,
            'email' => $request->filled('email') ? $request->email : null,
        ]);
        // ------------------------------
        // GENERATE QUOTATION & PI NUMBER
        // Format: COMPANY/YYMMDD/HHMM/ID
        // ------------------------------
        $selectedCompany = Company::find($request->company_id);
        $date = now()->format('ymd');

        $leadCode = $quotation->lead->id;
        $prefix = $selectedCompany->initials() . '#' . $quotation->assigned_user_id;

        $quotation->quote_number =
            $prefix
            . '/'
            . $date
            . '/'
            . $leadCode
            . '/'
            . $quotation->id;

        $quotation->pi_number =
            'PI-'
            . $prefix
            . '/'
            . $date
            . '/'
            . $leadCode
            . '/'
            . $quotation->id;
        $quotation->save();
        Lead::where('id', $request->lead_id)
            ->update(['status' => 'quoted']);

        // ------------------------------
        // SAVE QUOTATION ITEMS
        // ------------------------------
        if ($request->item_id) {
            foreach ($request->item_id ?? [] as $idx => $itemId) {

                if (
                    empty($itemId) ||
                    empty($request->quantity[$idx]) ||
                    empty($request->unit_price[$idx])
                ) {
                    continue;
                }
                $type = $request->item_type[$idx] ?? null;
                $description = $request->description_en_html[$idx] ?? '';
                $hiDescription = $request->description_hi[$idx] ?? '';
                $hiName = $request->item_name_hindi[$idx] ?? '';
                $qty = $request->quantity[$idx] ?? 0;
                $unitPrice = $request->unit_price[$idx] ?? 0;
                $total = $request->total[$idx] ?? 0;
                $sortOrder = $request->sort_order[$idx] ?? ($idx + 1);
                $convertedPrice = $request->converted_price[$idx] ?? null;
                $convertedTotal = $request->converted_total[$idx] ?? null;
                $cleanHiDescription = $this->cleanDescription($hiDescription);

                // ------------------------------
                // UPDATE MACHINE / COMPONENT
                // ------------------------------
                if ($type === 'machine') {
                    $machine = Machine::find($itemId);
                    if ($machine) {
                        $machine->update([
                            'hi_name' => $hiName,
                            'hi_description' => $cleanHiDescription,
                        ]);
                    }
                } elseif ($type === 'component') {
                    $component = Component::find($itemId);

                    if ($component) {
                        $component->update([
                            'hi_name' => $hiName,
                            'hi_description' => $cleanHiDescription,
                        ]);
                    }
                }
                $quotation->items()->create([
                    'machine_id' => $type === 'machine' ? $itemId : null,
                    'component_id' => $type === 'component' ? $itemId : null,
                    'description' => $description,
                    'hi_description' => $hiDescription,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'total_price' => $total,
                    'converted_unit_price' => $convertedPrice,
                    'converted_total_price' => $convertedTotal,
                    'sort_order' => $sortOrder,
                ]);
            }
        }


        // ------------------------------
        // HANDLE FILE UPLOADS
        // ------------------------------
        if ($request->hasFile('uploads')) {

            if (!file_exists($path_load . '/quotations')) {
                mkdir($path_load . '/quotations', 0777, true);
            }

            foreach ($request->file('uploads') as $file) {
                if (!$file->isValid())
                    continue;

                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($path_load . '/quotations', $filename);

                $quotation->files()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => 'quotations/' . $filename,
                    'uploaded_by' => Auth::id(),
                ]);
            }
        }
        notifyAdmins(
            'New Quotation Created',
            "Quotation {$quotation->quote_number} generated",
            route('quotations.edit', [$quotation->company_id, $quotation->id]),
            'success'
        );
        toast('Quotation Created Successfully', 'success');

        return redirect()->route('quotations.index', $company->id);
    }
    public function edit(Company $company, Quotation $quotation)
    {
        abort_if($quotation->company_id != $company->id, 403);
        $companies = Company::orderBy('company_name')->get();
        $leads = Lead::where('company_id', $company->id)->get();
        $users = User::role([
            'staff',
            'admin',
            'super admin'
        ])->get();
        $machines = Machine::all();
        $components = Component::all();
        $user = Auth::user();
        $title = $user->name . " Quotation Management";
        $label = "Edit Quotation";

        $quotation->load([
            'items' => function ($q) {
                $q->orderBy('sort_order')
                    ->with(['machine', 'component']);
            },
            'lead.customer.primaryPhone',
            'assignedUser',
            'company',
        ]);
        return view('company.crm.quotations.edit', compact('company', 'quotation', 'leads', 'companies', 'users', 'machines', 'components', 'title', 'label'));
    }
    public function update(Request $request, Company $company, Quotation $quotation)
    {
        // dd($request->all());
        $customer = optional($quotation->lead)->customer;

        if (!$customer) {
            return back()->withErrors('Selected lead has no customer attached.');
        }
        // Ensure the quotation belongs to the given company
        abort_if($quotation->company_id != $company->id, 403);

        // Validate inputs
        // dd($request->all());
        $request->validate([
            'lead_id' => 'nullable|exists:leads,id',
            'quote_date' => 'required|date',
            'customer_name' => 'required|max:255',
            'assigned_user_id' => 'required|exists:users,id',
            'email' => 'nullable|email|max:255',
            'office_address' => 'nullable|string',
            'delivery_address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'customer_gst' => 'nullable|string|max:50',
            'currency' => 'required|string',
            'conversion_rate' => 'nullable|numeric|min:0',
            'special_clause' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'subtotal' => 'nullable|numeric',
            'discount' => [
                'nullable',
                'numeric',
                'min:0',
                function ($a, $v, $fail) use ($request) {
                    if ($v > $request->subtotal) {
                        $fail('Discount cannot be greater than Subtotal.');
                    }
                }
            ],
            'mobile' => [
                'nullable',
                'regex:/^[0-9]{9,15}$/',
            ],
            'tax' => 'nullable|numeric',
            'final_amount' => 'nullable|numeric',
        ]);
        // ✅ Attach lead if provided (CRITICAL)
        if ($request->filled('lead_id')) {
            $quotation->update([
                'lead_id' => $request->lead_id,
            ]);

            // reload relation
            $quotation->load('lead.customer.primaryPhone');
        }

        if ($request->filled('customer_gst')) {
            $quotation->lead
                ->customer
                ->update([
                    'gst' => $request->customer_gst
                ]);
        }
        $customer = optional($quotation->lead)->customer;

        if (!$customer) {
            return back()->withErrors('Selected lead has no customer attached.');
        }

        // Update customer core details
        $customer->update([
            'name' => $request->customer_name,
            'email' => $request->filled('email') ? $request->email : null,
            'gst' => $request->gst,
            'address' => $request->office_address,
        ]);
        if ($request->filled('mobile')) {

            $existingPhone = $customer->phones()
                ->where('phone', $request->mobile)
                ->first();

            if ($existingPhone) {
                // ✅ Already exists → just make it primary (optional)
                $existingPhone->update(['is_primary' => 1]);

            } else {

                $primaryPhone = $customer->primaryPhone;

                if ($primaryPhone) {
                    // ✅ Safe update (no duplicate)
                    $primaryPhone->update([
                        'phone' => $request->mobile,
                    ]);
                } else {
                    $customer->phones()->create([
                        'phone' => $request->mobile,
                        'is_primary' => 1,
                    ]);
                }
            }
        }
        // ------------------------------
        // Update main quotation fields
        // ------------------------------
        $quotation->update([
            'contact_person' => $request->contact_person,
            'delivery_address' => $request->delivery_address,
            'assigned_user_id' => $request->assigned_user_id,
            'quote_date' => $request->quote_date,
            'pi_date' => $request->pi_date,
            'special_clause' => $request->special_clause,
            'hi_special_clause' => $request->hi_special_clause,
            'terms_conditions' => $request->terms_conditions,
            'hi_terms_conditions' => $request->hi_terms_conditions,
            'total_amount' => $request->subtotal,
            'discount' => $request->discount ?? 0,
            'currency' => $request->currency,
            'conversion_rate' => $request->conversion_rate ?? 1,
            'tax' => $request->tax,
            'tax_amount' => $request->tax_amount,
            'final_amount' => $request->final_total,
        ]);
        // ------------------------------
        // Handle Quotation Items
        // ------------------------------
        // ------------------------------
        // Handle Quotation Items
        // ------------------------------
        if ($request->item_id && is_array($request->item_id)) {

            foreach ($request->item_id as $index => $value) {

                if (empty($value)) {
                    continue;
                }

                $rowItemId = $request->row_item_id[$index] ?? null;

                $sortOrder = $request->sort_order[$index] ?? ($index + 1);

                $type = $request->item_type[$index] ?? null;

                $description = $request->description_en_html[$index] ?? null;

                $hiDescription = $request->description_hi[$index] ?? null;

                $hiName = $request->item_name_hindi[$index] ?? null;

                $qty = $request->quantity[$index] ?? 0;

                $unitPrice = $request->unit_price[$index] ?? 0;

                $total = $request->total[$index] ?? 0;

                $convertedPrice = $request->converted_price[$index] ?? null;

                $convertedTotal = $request->converted_total[$index] ?? null;

                $machineId = null;
                $componentId = null;

                // Clean Hindi Description
                $cleanHiDescription = $this->cleanDescription($hiDescription);

                // ------------------------------
                // MACHINE
                // ------------------------------
                if ($type === 'machine' && $value) {

                    $machineId = (int) $value;

                    $machine = Machine::find($machineId);

                    if ($machine) {

                        $updateData = [];

                        // Update hi_name only if empty
                        if (empty($machine->hi_name) && !empty($hiName)) {
                            $updateData['hi_name'] = $hiName;
                        }

                        // Update hi_description only if empty
                        if (
                            empty($machine->hi_description)
                            && !empty($cleanHiDescription)
                        ) {
                            $updateData['hi_description'] = $cleanHiDescription;
                        }

                        // OPTIONAL:
                        // Update English description only if empty
                        if (
                            empty($machine->description)
                            && !empty($description)
                        ) {
                            $updateData['description'] = $description;
                        }

                        if (!empty($updateData)) {
                            $machine->update($updateData);
                        }
                    }
                }

                // ------------------------------
                // COMPONENT
                // ------------------------------
                elseif ($type === 'component' && $value) {

                    $componentId = (int) $value;

                    $component = Component::find($componentId);

                    if ($component) {

                        $updateData = [];

                        // Update hi_name only if empty
                        if (empty($component->hi_name) && !empty($hiName)) {
                            $updateData['hi_name'] = $hiName;
                        }

                        // Update hi_description only if empty
                        if (
                            empty($component->hi_description)
                            && !empty($cleanHiDescription)
                        ) {
                            $updateData['hi_description'] = $cleanHiDescription;
                        }

                        // OPTIONAL:
                        // Update English description only if empty
                        if (
                            empty($component->description)
                            && !empty($description)
                        ) {
                            $updateData['description'] = $description;
                        }

                        if (!empty($updateData)) {
                            $component->update($updateData);
                        }
                    }
                }

                // ------------------------------
                // SAVE QUOTATION ITEM
                // ------------------------------
                $data = [
                    'machine_id' => $machineId,
                    'component_id' => $componentId,
                    'description' => $description,
                    'hi_description' => $hiDescription,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'total_price' => $total,
                    'converted_unit_price' => $convertedPrice,
                    'converted_total_price' => $convertedTotal,
                    'sort_order' => $sortOrder,
                ];

                // UPDATE
                if ($rowItemId) {

                    QuotationItem::where('id', $rowItemId)
                        ->update($data);

                } else {

                    // CREATE NEW
                    $quotation->items()->create($data);
                }
            }
        }



        // Delete items marked for deletion
        if ($request->deleted_items_ids) {
            QuotationItem::whereIn('id', $request->deleted_items_ids)->delete();
        }

        // ------------------------------
        // Handle Files
        // ------------------------------
        $publicPath = config('url.public_path');


        // Delete old files
        if ($request->deleted_files) {
            foreach ($request->deleted_files as $fileId) {
                $file = $quotation->files()->find($fileId);
                if ($file && file_exists($publicPath . '/' . $file->file_path)) {
                    unlink($publicPath . '/' . $file->file_path);
                }
                $file?->delete();
            }
        }

        // Replace files
        if ($request->replace_file_ids) {
            foreach ($request->replace_file_ids as $fileId) {
                $fileInput = $request->file("replace_file_{$fileId}");
                if ($fileInput && $fileInput->isValid()) {
                    $file = $quotation->files()->find($fileId);
                    if ($file && file_exists($publicPath . '/quotations' . $file->file_path)) {
                        unlink($publicPath . '/quotations' . $file->file_path);
                    }

                    $filename = time() . '_' . uniqid() . '.' . $fileInput->getClientOriginalExtension();
                    $fileInput->move($publicPath . '/quotations', $filename);

                    if ($file) {
                        $file->update([
                            'file_name' => $fileInput->getClientOriginalName(),
                            'file_path' => 'quotations/' . $filename,
                            'uploaded_by' => auth()->id(),
                        ]);
                    }
                }
            }
        }

        // Upload new files
        if ($request->hasFile('uploads')) {
            if (!file_exists($publicPath . '/quotations')) {
                mkdir($publicPath . '/quotations', 0777, true);
            }

            foreach ($request->file('uploads') as $file) {
                if (!$file->isValid())
                    continue;

                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($publicPath . '/quotations', $filename);

                $quotation->files()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => 'quotations/' . $filename,
                    'uploaded_by' => auth()->id(),
                ]);
            }
        }
        toast('Quotation Updated Successfully', 'success');
        return redirect()->route('quotations.index', $company->id);
    }
    public function destroy(Company $company, Quotation $quotation)
    {
        abort_if($quotation->company_id != $company->id, 403);

        if ($quotation->orders()->exists()) {
            toast('Cannot delete! Quotation has orders.', 'error');
            return back();
        }

        // Reset Lead Status
        if ($quotation->lead_id) {
            Lead::where('id', $quotation->lead_id)
                ->update(['status' => 'new']);
        }

        $quotation->delete();

        toast('Quotation Deleted Successfully', 'success');
        return redirect()->route('quotations.index', $company->id);
    }
    public function duplicate(Company $company, Quotation $quotation)
    {
        abort_if($quotation->company_id != $company->id, 403);

        // 🔹 Clone quotation
        $newQuotation = $quotation->replicate([
            'quote_number',
            'pi_number',
            'created_at',
            'updated_at'
        ]);

        // 👉 OPTIONAL: reset lead (recommended)
        // $newQuotation->lead_id = null;

        $newQuotation->created_at = now();

        // ✅ Fix invalid pi_date
        $originalPiDate = $quotation->getRawOriginal('pi_date');

        if (
            $originalPiDate === '0000-00-00' ||
            empty($originalPiDate)
        ) {

            // remove bad replicated value
            unset($newQuotation->pi_date);

            // assign valid fallback
            $newQuotation->setAttribute(
                'pi_date',
                $quotation->quote_date
            );
        }

        $newQuotation->save();

        $userId = auth()->id();
        // 🔹 Generate new numbers (MATCHING generateNumber)
        $lastQuote = Quotation::orderBy('id', 'DESC')->first();
        $nextId = $lastQuote ? $lastQuote->id + 1 : 1;

        $leadId = $quotation->lead_id; // keep same lead OR set null if needed
        $date = now()->format('ymd');

        $newQuotation->quote_number =
            $company->initials() . '#' . $quotation->assigned_user_id
            . '/'
            . $date . '/' . $leadId . '/' . $nextId;

        $newQuotation->pi_number =
            'PI-' . $company->initials() . '#' . $quotation->assigned_user_id
            . '/'
            . $date . $leadId . '/' . $nextId;

        $newQuotation->save();

        // 🔹 Duplicate items (FIXED 🔥)
        foreach ($quotation->items()->orderBy('sort_order')->get() as $item) {

            $newQuotation->items()->create([
                'machine_id' => $item->machine_id,
                'component_id' => $item->component_id,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_price' => $item->total_price,
                // 🔥 IMPORTANT FIX
                'converted_unit_price' => $item->converted_unit_price,
                'converted_total_price' => $item->converted_total_price,
                'sort_order' => $item->sort_order,
            ]);
        }

        toast('Quotation duplicated successfully', 'success');

        return redirect()->route(
            'quotations.edit',
            [$company->id, $newQuotation->id]
        );
    }
    public function print(Company $company, Quotation $quotation)
    {
        $user = Auth::user();
        $title = $user->name . " Quotation Print Preview";
        $label = "Preview Quotation";
        $quotation->load([
            'items' => fn($q) => $q->orderBy('sort_order'),
            'items.machine',
            'items.component',
            'lead.customer.primaryPhone',
            'assignedUser',
            'company',
        ]);

        return view('company.crm.quotations.print', compact(
            'company',
            'quotation',
            'title',
            'label'
        ));
    }
    public function pdf($companyId, $quotationId, Request $request)
    {
        $company = Company::findOrFail($companyId);
        $lang = $request->lang ?? 'en';

        $quotation = Quotation::with([
            'items' => function ($q) {
                $q->orderBy('sort_order')
                    ->with(['machine', 'component']);
            },
            'lead.customer.primaryPhone',
            'lead.customer.country',
            'assignedUser'
        ])->findOrFail($quotationId);

        $settings = Setting::first();

        $sections = array_filter(explode(',', $request->sections ?? ''));
        $extras = array_filter(explode(',', $request->extras ?? ''));
        $columns = array_filter(explode(',', $request->columns ?? ''));
        $rows = $request->has('rows') ? explode(',', $request->rows) : [];

        $docType = $request->doc_type ?? 'quotation';

        $currency = ($request->currency && $request->currency !== 'undefined')
            ? $request->currency
            : $quotation->currency;

        /*
        |--------------------------------------------------------------------------
        | Filter Selected Rows
        |--------------------------------------------------------------------------
        */
        if (!empty($rows)) {
            $quotation->setRelation(
                'items',
                $quotation->items->filter(function ($item, $index) use ($rows) {
                    return in_array((string) $index, $rows, true);
                })->values()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Currency Symbol
        |--------------------------------------------------------------------------
        */
        $currencySymbols = [
            'INR' => '₹',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£'
        ];

        $currencySymbol = $currencySymbols[$currency] ?? '₹';

        /*
        |--------------------------------------------------------------------------
        | Generate HTML From Existing Blade
        |--------------------------------------------------------------------------
        */
        $html = view(
            'company.crm.quotations.pdf',
            compact(
                'quotation',
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
            <img src="' . public_path('admin/uploads/logo/' . $settings->logo) . '" width="220">
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

        /*
        |--------------------------------------------------------------------------
        | File Name
        |--------------------------------------------------------------------------
        */
        $number = $docType === 'pi'
            ? $quotation->pi_number
            : $quotation->quote_number;

        $customerName = $quotation->customer_name;

        $fileName = $this->generateFileName(
            $company,
            $docType,
            $number,
            $customerName,
            $quotation->lead->id,
            $quotation->assigned_user_id
        );
        $pdfContent = $mpdf->Output('', 'S');
        return response($pdfContent, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }
    private function generateFileName($company, $docType, $number, $customerName, $leadCode, $assignedUserId)
    {

        $initials = $company->initials();

        $userId = $assignedUserId;

        $docMap = [
            'quotation' => 'Q',
            'pi' => 'PI',
            'order' => 'O',
            'po' => 'PO',
            'payment' => 'PAY'
        ];

        $docInitial = $docMap[$docType]
            ?? strtoupper(substr($docType, 0, 2));

        $words = explode(' ', trim($customerName));

        $firstTwoWords = array_slice($words, 0, 2);

        $cleanCustomer = preg_replace(
            '/[^A-Za-z0-9]/',
            '',
            implode('', $firstTwoWords)
        );

        return "{$initials}#{$userId}{$docInitial}{$leadCode}-{$cleanCustomer}.pdf";
    }
    public function data(Request $request, Company $company)
    {
        $user = auth()->user();

        $query = Quotation::with(['creator', 'lead'])
            ->where('company_id', $company->id)
            ->when(!$user->hasAnyRole(['Super Admin', 'Admin']), function ($q) use ($user) {
                // $q->where(function ($sub) use ($user) {
                // $sub->where('assigned_user_id', $user->id)
                // ->orWhere('user_id', $user->id);
                // });
            });
        $search = $request->search;
        if ($search === "" || $search === null) {
            $search = null;
        }

        $from = $request->from_date ?: null;
        $to = $request->to_date ?: null;
        $slab = $request->amount_slab;
        // CASE 1 → SEARCH ONLY
        if ($search !== null && $from === null && $to === null) {

            // If search is numeric → treat as quotation ID
            if (is_numeric($search)) {

                $query->where('id', $search);

            } else {

                $query->where(function ($q) use ($search) {

                    $q->where('quote_number', 'LIKE', "%{$search}%");

                    if (is_numeric($search)) {
                        $q->orWhere('id', $search);
                    }

                    $q->orWhereHas('lead.customer', function ($qc) use ($search) {
                        $qc->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });

                    $q->orWhereHas('lead.customer.phones', function ($qp) use ($search) {
                        $qp->where('phone', 'LIKE', "%{$search}%");
                    });
                });

            }
        }
        if ($slab) {
            switch ($slab) {
                case 'low':
                    $query->where('total_amount', '<', 700000);
                    break;

                case 'medium':
                    $query->whereBetween('total_amount', [700000, 1500000]);
                    break;

                case 'average':
                    $query->whereBetween('total_amount', [1500000, 7000000]);
                    break;

                case 'high':
                    $query->where('total_amount', '>', 7000000);
                    break;
            }
        }
        // CASE 2 → DATE FILTER ONLY
        if ($search === null && ($from !== null || $to !== null)) {
            if ($from !== null) {
                $query->whereDate('quote_date', '>=', $from);
            }
            if ($to !== null) {
                $query->whereDate('quote_date', '<=', $to);
            }
        }

        // CASE 3 → NOTHING ENTERED → Show today's quotations
        if ($search === null && $from === null && $to === null) {
            $query->latest()->limit(10);
        }

        $quotations = $query
            ->orderByDesc('total_amount')  // highest amount first
            ->get();

        return view('company.crm.quotations.partials.quote_rows', [
            'quotations' => $quotations,
            'company' => $company
        ])->render();
    }
    public function ajaxSearch(Request $request, Company $company)
    {
        $search = trim($request->search ?? '');

        $quotations = Quotation::with('lead.customer.phones')
            ->where('company_id', $company->id)
            ->where(function ($q) use ($search) {

                // Exact DB ID match
                if (is_numeric($search)) {
                    $q->where('id', (int) $search);
                }

                // Other searchable fields
                $q->orWhere('quote_number', 'LIKE', "%{$search}%")

                    ->orWhereHas('lead.customer', function ($qc) use ($search) {
                    $qc->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                })

                    ->orWhereHas('lead.customer.phones', function ($qp) use ($search) {
                    $qp->where('phone', 'LIKE', "%{$search}%");
                });
            })
            ->limit(20)
            ->get();

        return $quotations->map(function ($q) {

            $customer = optional($q->lead)->customer;
            $primaryPhone = optional($customer?->phones)->first();

            return [
                'id' => $q->id,
                'quote_number' => $q->quote_number,
                'customer_name' => $customer->name ?? '',
                'mobile' => $primaryPhone->phone ?? '',
            ];
        });
    }
    public function ajaxQuotationDetails(Request $request, Company $company)
    {
        $quotation = Quotation::with([
            'items' => function ($q) {
                $q->orderBy('sort_order')
                    ->with(['machine', 'component']);
            },
            'creator',
            'assignedUser',
            'lead.customer.primaryPhone',
            'lead.customer.country',
            'lead.customer.state',
            'lead.customer.city',
            'files'
        ])
            ->where('company_id', $company->id)
            ->where('id', $request->id)
            ->firstOrFail();

        $items = $quotation->items
            ->sortBy('sort_order')
            ->values()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'machine' => $item->machine,
                    'component' => $item->component,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                    'sort_order' => $item->sort_order,
                ];
            });


        return response()->json([
            'id' => $quotation->id,
            'quote_number' => $quotation->quote_number,
            'pi_number' => $quotation->pi_number,
            'quote_date' => $quotation->quote_date,
            'pi_date' => $quotation->pi_date,
            'lead' => $quotation->lead,
            'contact_person' => $quotation->contact_person,
            'creator' => $quotation->creator,
            'assigned_user' => $quotation->assignedUser,
            'items' => $items,
            'delivery_address' => $quotation->delivery_address,
            'total_amount' => $quotation->total_amount,
            'discount' => $quotation->discount,
            'tax' => $quotation->tax,
            'files' => $quotation->files,
            'tax_amount' => $quotation->tax_amount,
            'final_amount' => $quotation->final_amount,
            'special_clause' => $quotation->special_clause,
            'terms_conditions' => $quotation->terms_conditions,
        ]);
    }
    public function generateNumber(Request $request)
    {
        if (!$request->lead_id) {
            return response()->json([
                'quotation' => 'Select Lead First',
                'pi' => 'Select Lead First'
            ]);
        }

        $company = Company::findOrFail($request->company_id);

        $lastQuote = Quotation::orderBy('id', 'DESC')->first();
        $nextId = $lastQuote ? $lastQuote->id + 1 : 1;

        $leadId = $request->lead_id;
        $date = now()->format('ymd');

        $quotation = $company->initials()
            . '/'
            . $date . '/' . $leadId . '/' . $nextId;

        $pi = 'PI-' . $company->initials() . '/'
            . '/'
            . $date . $leadId . '/' . $nextId;
        return response()->json([
            'quotation' => $quotation,
            'pi' => $pi
        ]);
    }
    public function saveTranslation(Request $request)
    {
        $quotationId = $request->quotation_id;
        $lang = $request->lang;
        $translations = collect($request->translations)->map(function ($t) {
            $t['text'] = preg_replace('/<\?xml.*?\?>/i', '', $t['text']);
            return $t;
        })->toArray();

        $key = "quotation_pdf_{$quotationId}_{$lang}";

        Cache::put($key, $translations, now()->addMinutes(30));

        return response()->json(['status' => 'saved']);
    }
    public function getTranslation(Request $request)
    {
        $quotationId = $request->quotation_id;
        $lang = $request->lang;

        $key = "quotation_pdf_{$quotationId}_{$lang}";

        return response()->json([
            'translations' => Cache::get($key)
        ]);
    }
}