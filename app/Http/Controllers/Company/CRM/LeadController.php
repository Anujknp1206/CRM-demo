<?php

namespace App\Http\Controllers\Company\CRM;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Followup;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Customer;
use App\Models\CustomerPhone;
use App\Models\Country;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    /**
     * Display a listing of the company's leads.
     */
    public function index(Request $request, Company $company)
    {
        $leads = Lead::with([
            'creator',
            'latestFollowup',
            'customer:id,country_id,state_id,city_id,name',
            'customer.country',
            'customer.state',
            'customer.city',
            // 'customer.primaryPhone:id,customer_id,phone'
        ])
            ->where('company_id', $company->id)
            ->latest()
            ->get();
        $actions = Action::where('company_id', $company->id)
            ->where('name', '!=', 'Lead Created')
            ->orderBy('name')
            ->get();
        // Execute query
        return view('company.crm.lead.index', [
            'company' => $company,
            'leads' => $leads,
            'title' => Auth::user()->name . " Leads Management",
            'label' => "Leads List",
            'actions' => $actions,
        ]);
    }
    public function checkCustomerByMobile(Request $request, Company $company)
    {
        $customer = Customer::where('company_id', $company->id)
            ->whereHas('phones', fn($q) => $q->where('phone', $request->mobile))
            ->with([
                'phones',
                'country',
                'state',
                'city'
            ])
            ->first();

        if (!$customer) {
            return response()->json(['exists' => false]);
        }

        return response()->json([
            'exists' => true,
            'customer' => $customer
        ]);
    }

    /**
     * Show form to create lead.
     */
    public function create(Company $company)
    {
        return view('company.crm.lead.create', [
            'company' => $company,
            'companies' => Company::orderBy('company_name')->get(),
            'countries' => Country::orderBy('name')->get(),
            'title' => Auth::user()->name . " Leads Management",
            'label' => "Add Lead",
        ]);
    }
    public function store(Request $request, Company $company)
    {
        $validated = $request->validate([

            // 🔹 BASIC DETAILS
            'customerName' => 'required|string|max:100',
            'mobile' => 'required|regex:/^[0-9]{9,15}$/',
            'extra_mobile.*' => 'nullable|regex:/^[0-9]{9,15}$/',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:100',
            'gst' => 'nullable|string|max:15',

            // 🔹 LOCATION
            'country' => 'required|exists:countries,id',
            'state' => 'required|exists:states,id',
            'city' => 'required|exists:cities,id',

            // 🔹 BUSINESS INFO
            'purpose' => 'nullable|string|max:150',
            'remark' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:255',
            'lead_date' => 'required|date',
        ]);

        DB::transaction(function () use ($request, $company) {
            $customer = Customer::where('company_id', $company->id)
                ->whereHas('phones', fn($q) => $q->where('phone', $request->mobile))
                ->first();

            if (!$customer) {
                // 🟢 CREATE CUSTOMER
                $customer = Customer::create([
                    'company_id' => $company->id,
                    'name' => $request->customerName,
                    'email' => $request->email,
                    'address' => $request->address,
                    'gst' => $request->gst,
                    'country_id' => $request->country,
                    'state_id' => $request->state,
                    'city_id' => $request->city,
                ]);

                // 🔹 Collect all phones (primary + extra)
                $phones = collect([$request->mobile])
                    ->merge($request->extra_mobile ?? [])
                    ->unique()
                    ->filter();

                // 🔹 Save phones
                foreach ($phones as $index => $phone) {
                    CustomerPhone::create([
                        'customer_id' => $customer->id,
                        'phone' => $phone,
                        'is_primary' => $index === 0, // first = primary
                    ]);
                }

            } else {
                // 🟡 UPDATE CUSTOMER (APPEND / MERGE)
                $customer->update([
                    'name' => $request->customerName ?: $customer->name,
                    'email' => $request->email ?: $customer->email,
                    'address' => $request->address ?: $customer->address,
                    'gst' => $request->gst ?: $customer->gst,
                    'country_id' => $request->country,
                    'state_id' => $request->state,
                    'city_id' => $request->city,
                ]);

                // Add phone if new
                $phones = collect([$request->mobile])
                    ->merge($request->extra_mobile ?? [])
                    ->unique()
                    ->filter();

                foreach ($phones as $index => $phone) {
                    CustomerPhone::firstOrCreate(
                        [
                            'customer_id' => $customer->id,
                            'phone' => $phone,
                        ],
                        [
                            'is_primary' => $index === 0,
                        ]
                    );
                }

            }


            /* ============================
               1. CREATE LEAD
            ============================ */
            $lead = new Lead();
            $lead->company_id = $company->id;
            $lead->customer_id = $customer->id;
            $lead->purpose = $request->purpose;
            $lead->remark = $request->remark;
            $lead->message = $request->message;
            $lead->reference = $request->reference;
            $lead->created_by = auth()->id();

            // 👇 IMPORTANT
            $lead->created_at = \Carbon\Carbon::parse($request->lead_date)
                ->setTimeFrom(now());

            $lead->save();


            /* ============================
               2. GENERATE LEAD CODE
            ============================ */
            $prefix = $company->initials() . '#' . $lead->created_by;

            $lead->update([
                'lead_code' => $prefix . '-' . $lead->id
            ]);

            /* ============================
               3. FIND OR CREATE ACTION
            ============================ */
            $action = Action::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'name' => 'Lead Created'
                ],
                [
                    'description' => 'Initial follow-up created automatically'
                ]
            );

            Followup::create([
                'company_id' => $company->id,
                'lead_id' => $lead->id,
                'action_id' => $action->id,
                'nextactionDate' => now(), // or now()->addDays(1)
                'describeAction' => 'Lead created and assigned',
                'managed_by' => auth()->id(),
            ]);

            notifyAdmins(
                'New Lead Created',
                "Lead #{$lead->lead_code} created for {$lead->customerName}",
                route('leads.edit', [$company->id, $lead->id]),
                'success'
            );

        });

        toast('Lead Created Successfully!', 'success');
        return redirect()->route('leads.index', ['company' => $company->id]);
    }
    public function edit(Company $company, Lead $lead)
    {
        abort_if($lead->company_id != $company->id, 403);

        $customer = $lead->customer;

        return view('company.crm.lead.edit', [
            'company' => $company,
            'lead' => $lead,
            'countries' => Country::all(),
            'states' => $customer?->country?->states ?? collect(),
            'cities' => $customer?->state?->cities ?? collect(),
            'title' => Auth::user()->name . " Leads Management",
            'label' => "Edit Lead",
        ]);
    }
    public function update(Request $request, Company $company, Lead $lead)
    {
        abort_if($lead->company_id != $company->id, 403);

        $request->validate([
            'customerName' => 'required|string|max:100',
            'mobile' => 'required|regex:/^[0-9]{9,15}$/',
            'extra_mobile.*' => 'nullable|regex:/^[0-9]{9,15}$/',
            'gst' => 'nullable|string|max:15',

            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:255',

            'country' => 'required|exists:countries,id',
            'state' => 'required|exists:states,id',
            'city' => 'required|exists:cities,id',

            'purpose' => 'nullable|string|max:150',
            'remark' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:100',
            'lead_date' => 'required|date',
        ]);

        DB::transaction(function () use ($request, $lead) {

            /** --------------------
             * UPDATE CUSTOMER
             * -------------------- */
            $customer = $lead->customer;

            $customer->update([
                'name' => $request->customerName ?: $customer->name,
                'email' => $request->email ?: $customer->email,
                'address' => $request->address ?: $customer->address,
                'gst' => $request->gst ?: $customer->gst,
                'country_id' => $request->country,
                'state_id' => $request->state,
                'city_id' => $request->city,
            ]);


            /** --------------------
             * SYNC PHONES
             * -------------------- */
            $phones = collect([$request->mobile])
                ->merge($request->extra_mobile ?? [])
                ->unique()
                ->filter();

            // Remove old phones not in request
            $lead->customer->phones()
                ->whereNotIn('phone', $phones)
                ->delete();

            foreach ($phones as $index => $phone) {
                CustomerPhone::updateOrCreate(
                    [
                        'customer_id' => $lead->customer->id,
                        'phone' => $phone
                    ],
                    [
                        'is_primary' => $index === 0
                    ]
                );
            }

            /** --------------------
             * UPDATE LEAD
             * -------------------- */

            $lead->forceFill([
                'purpose' => $request->purpose,
                'remark' => $request->remark,
                'message' => $request->message,
                'reference' => $request->reference,
                'created_at' => $request->lead_date,
            ])->save();
        });

        toast('Lead Updated Successfully!', 'success');

        return redirect()->route('leads.index', ['company' => $company->id]);
    }
    public function destroy(Company $company, Lead $lead)
    {
        abort_if($lead->company_id != $company->id, 403);

        if (
            $lead->quotations()->exists()
        ) {
            toast('Cannot delete! Lead has quotations.', 'error');
            return back();
        }

        $lead->delete();

        toast('Lead Deleted Successfully!', 'success');
        return redirect()->route('leads.index', $company->id);
    }
    public function ajaxSingleLeadDetails(Request $request, Company $company)
    {
        $lead = Lead::with([
            'customer.country',
            'customer.state',
            'customer.city',
            'customer.primaryPhone',
        ])
            ->where('company_id', $company->id)
            ->findOrFail($request->id);

        $customer = $lead->customer;

        return response()->json([
            'lead_code' => $lead->lead_code,
            'customerName' => $customer?->name ?? '---',
            'phones' => $customer?->phones
                ? $customer->phones->pluck('phone')->values()
                : [],
            'email' => $customer?->email ?? '',
            'gst' => $customer?->gst ?? '',              // ✅ ADD THIS

            'country' => [
                'name' => $customer?->country?->name ?? '---',
                'phonecode' => $customer?->country?->phonecode ?? '---', // ✅ ADD THIS
            ],

            'state' => [
                'name' => $customer?->state?->name ?? '--',
            ],

            'city' => [
                'name' => $customer?->city?->name ?? '---',
            ],

            'address' => $customer?->address ?? '---',
            'purpose' => $lead->purpose,
            'remark' => $lead->remark,
            'reference' => $lead->reference,
            'created_at' => optional($lead->created_at)->format('d/m/Y h:i A'),
        ]);
    }
    public function ajaxLeadDetails(Request $request, Company $company)
    {
        $search = $request->search;

        $leads = Lead::with([
            'customer',
            'customer.primaryPhone',
            'customer.phones' // ✅ ADD THIS
        ])
            ->where('company_id', $company->id)
            ->where(function ($q) use ($search) {
                $q->where('lead_code', 'LIKE', "%$search%")
                    ->orWhereHas(
                        'customer',
                        fn($c) =>
                        $c->where('name', 'LIKE', "%$search%")
                    )
                    ->orWhereHas(
                        'customer.phones',
                        fn($p) =>
                        $p->where('phone', 'LIKE', "%$search%")
                    );
            })
            ->limit(20)
            ->get();

        return response()->json([
            'results' => $leads->map(fn($lead) => [
                'id' => $lead->id,
                'text' =>
                    $lead->lead_code . ' - ' .
                    $lead->customer->name . ' (' .
                    optional($lead->customer->primaryPhone)->phone . ')'
            ])
        ]);

    }
    public function data(Request $request, Company $company)
    {
        $query = Lead::with([
            'customer.primaryPhone',
            'latestFollowup'
        ])
            ->where('company_id', $company->id)
            ->orderByDesc('id');

        $search = trim($request->search ?? '');
        $from = $request->from_date;
        $to = $request->to_date;

        // Search Filter
        if (!empty($search)) {

            $query->where(function ($q) use ($search) {

                // Lead table fields
                $q->where('lead_code', 'LIKE', "%{$search}%")
                    ->orWhere('id', $search);

                // Customer name
                $q->orWhereHas('customer', function ($customer) use ($search) {
                    $customer->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                });

                // Customer primary phone
                $q->orWhereHas('customer.primaryPhone', function ($phone) use ($search) {
                    $phone->where('phone', 'LIKE', "%{$search}%");
                });
            });
        }

        // From Date
        if (!empty($from)) {
            $query->whereDate('created_at', '>=', $from);
        }

        // To Date
        if (!empty($to)) {
            $query->whereDate('created_at', '<=', $to);
        }

        // Initial Load
        if (empty($search) && empty($from) && empty($to)) {
            $query->limit(10);
        }

        $leads = $query->get();

        return view('company.crm.lead.partials.lead_rows', compact('leads', 'company'))->render();
    }
    public function storeAjax(Request $request, Company $company)
    {
        $request->validate([
            'customerName' => 'required|max:255',
            'mobile' => 'required|digits:10',
            'email' => 'nullable|email',
            'country' => 'required|exists:countries,id',
            'state' => 'required|exists:states,id',
            'city' => 'required|exists:cities,id',
        ]);

        DB::transaction(function () use ($request, $company, &$lead) {

            // 1️⃣ Find or create customer by phone
            $customer = Customer::where('company_id', $company->id)
                ->whereHas('phones', fn($q) => $q->where('phone', $request->mobile))
                ->first();

            if (!$customer) {
                $customer = Customer::create([
                    'company_id' => $company->id,
                    'name' => $request->customerName,
                    'email' => $request->email,
                    'address' => $request->address,
                    'country_id' => $request->country,
                    'state_id' => $request->state,
                    'city_id' => $request->city,
                ]);

                CustomerPhone::create([
                    'customer_id' => $customer->id,
                    'phone' => $request->mobile,
                    'is_primary' => true,
                ]);
            }

            // 2️⃣ Create Lead
            $lead = Lead::create([
                'company_id' => $company->id,
                'customer_id' => $customer->id,
                'purpose' => $request->purpose,
                'remark' => $request->remark,
                'message' => $request->message,
                'reference' => $request->reference,
                'status' => 'new',
                'created_by' => auth()->id(),
            ]);

            // 3️⃣ Generate Lead Code
            $lead->update([
                'lead_code' => $company->initials() . $lead->id
            ]);

            // 4️⃣ Find or Create Action
            $action = Action::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'name' => 'Lead Created',
                ],
                [
                    'description' => 'Initial follow-up created automatically',
                ]
            );

            // 5️⃣ Create First Followup ✅
            Followup::create([
                'company_id' => $company->id,
                'lead_id' => $lead->id,
                'action_id' => $action->id,
                'nextactionDate' => now(), // or now()->addDay()
                'describeAction' => 'Lead created via Quick Add',
                'managed_by' => auth()->id(),
            ]);
        });

        return response()->json([
            'status' => true,
            'message' => 'Lead created successfully',
            'data' => [
                'id' => $lead->id,
                'text' => $lead->lead_code . ' - ' . $lead->customer->name
            ]
        ]);
    }
}
