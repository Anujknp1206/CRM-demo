<?php

namespace App\Http\Controllers\Company\CRM;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Action;
use Illuminate\Http\Request;
use Auth;

class ActionController extends Controller
{
    // SHOW ALL ACTIONS OF SPECIFIC COMPANY
    public function index(Company $company)
    {
        $user = Auth::user();
        $title = $company->company_name . " - Actions";
        $label = "Action List";

        // Filter actions by company
        $actions = Action::where('company_id', $company->id)->get();

        return view('company.crm.actions.index', compact('actions', 'title', 'label', 'company'));
    }

    // CREATE PAGE
    public function create(Company $company)
    {
        $title = $company->company_name . " - Add Action";
        $label = "Add Action";

        return view('company.crm.actions.create', compact('title', 'label', 'company'));
    }

    // STORE ACTION FOR THIS COMPANY
    public function store(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable'
        ]);

        Action::create([
            'company_id' => $company->id,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        toast('Action created successfully.', 'success');

        return redirect()->route('actions.index', $company->id);
    }

    // EDIT PAGE
    public function edit(Company $company, $id)
    {
        $title = $company->company_name . " - Update Action";
        $label = "Update Action";

        // Ensure action belongs to this company
        $action = Action::where('company_id', $company->id)->findOrFail($id);

        return view('company.crm.actions.edit', compact('title', 'label', 'action', 'company'));
    }

    // UPDATE ACTION
    public function update(Request $request, Company $company, $id)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable'
        ]);

        $action = Action::where('company_id', $company->id)->findOrFail($id);

        $action->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        toast('Action updated successfully.', 'success');

        return redirect()->route('actions.index', $company->id);
    }

    // DELETE ACTION
    public function destroy(Company $company, Action $action)
    {
        // Prevent deleting action of another company
        if ($action->company_id != $company->id) {
            abort(403, "Unauthorized action deletion!");
        }

        $action->delete();

        toast('Action deleted successfully.', 'success');

        return redirect()->route('actions.index', $company->id);
    }
}
