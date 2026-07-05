<?php

namespace App\Http\Controllers\Company\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Action;
use App\Models\Followup;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class FollowupController extends Controller
{
    /**
     * Show the follow-up list for a specific lead within a company.
     */
    public function index(Company $company, $lead_id)
    {
        $lead = Lead::with('followups.action')
            ->where('company_id', $company->id) // company-level filter
            ->findOrFail($lead_id);

        $actions = Action::where('company_id', $company->id)
            ->where('name', '!=', 'Lead Created')
            ->orderBy('name')
            ->get();
        $user = Auth::user();
        $title = $user->name . " FollowUp Management";
        $label = "FollowUp List";

        return view('company.crm.followup.index', compact('lead', 'actions', 'title', 'label', 'company'));
    }
    public function leadInfo(Company $company, Lead $lead)
    {
        $last = $lead->latestFollowup;

        return response()->json([
            'last_action' => $last?->action?->name,
            'next_date' => $last?->nextactionDate?->format('d/m/Y'),
            'min_date' => $last
                ? $last->nextactionDate->format('Y-m-d')
                : $lead->created_at->format('Y-m-d'),
        ]);
    }

    /**
     * Show the form to create a follow-up for a company lead.
     */


    /**
     * Store a follow-up entry for a company lead.
     */
    public function store(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'nextactionDate' => 'required|date',
            'selectAction' => 'required|exists:actions,id',
        ]);

        $lead = Lead::findOrFail($request->lead_id);

        $followup = Followup::create([
            'company_id' => $lead->company_id,
            'lead_id' => $lead->id,
            'nextactionDate' => $request->nextactionDate,
            'describeAction' => $request->describeAction,
            'action_id' => $request->selectAction,
            'managed_by' => auth()->id(),
        ]);

        // Load relations for table
        $followup->load(['action', 'manager']);

        return response()->json([
            'status' => true,
            'message' => 'Follow-up added successfully',
            'data' => [
                'id' => $followup->id,
                'nextactionDate' => $followup->nextactionDate ?? 'No Followup Needed',
                'action' => $followup->action->name ?? '----',
                'description' => $followup->describeAction ?? '----',
                'manager' => $followup->manager->name ?? '----',
                'delete_url' => route('followups.destroy', ['followup' => $followup->id, 'company' => $lead->company_id]),
            ]
        ]);
    }


    /**
     * Edit a follow-up entry within a company.
     */

    /**
     * Update a follow-up entry within a company.
     */
    public function update(Request $request, Followup $followup)
    {
        $request->validate([
            'nextactionDate' => 'required|date',
            'selectAction' => 'required|exists:actions,id',
        ]);

        $followup->update([
            'nextactionDate' => $request->nextactionDate,
            'describeAction' => $request->describeAction,
            'action_id' => $request->selectAction,
        ]);

        $followup->load(['action', 'manager']);

        return response()->json([
            'status' => true,
            'message' => 'Follow-up updated successfully',
            'data' => [
                'nextactionDate' => $followup->nextactionDate,
                'action' => $followup->action->name,
                'description' => $followup->describeAction ?? '----',
                'manager' => $followup->manager->name ?? '----',
            ]
        ]);
    }


    /**
     * Delete a follow-up entry within a company.
     */
    public function destroy(Company $company, Followup $followup)
    {

        $followup->delete();
        toast('Follow-up Deleted Successfully', 'success');

        return back();
    }
}
