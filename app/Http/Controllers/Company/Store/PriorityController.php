<?php

namespace App\Http\Controllers\Company\Store;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Priority;
use Illuminate\Http\Request;
class PriorityController extends Controller
{
    public function index(Company $company)
    {
        return view('company.production.priorities.index', [
            'priorities' => Priority::latest()->get(),
            'company' => $company,
            'label' => 'Priority List',
            'title' => 'Priority Management'
        ]);
    }

    public function store(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|max:50|unique:priorities,name',
            'level' => 'required|integer|min:1|max:10'
        ]);

        $priority = Priority::create([
            'name' => ucfirst($request->name),
            'level' => $request->level
        ]);

        return response()->json([
            'success' => true,
            'priority' => $priority
        ]);
    }

    public function update(Request $request, Company $company, Priority $priority)
    {
        $request->validate([
            'name' => 'required|max:50|unique:priorities,name,' . $priority->id,
            'level' => 'required|integer|min:1|max:10'
        ]);

        $priority->update([
            'name' => ucfirst($request->name),
            'level' => $request->level
        ]);

        return response()->json([
            'success' => true,
            'priority' => $priority
        ]);
    }

    public function destroy(Company $company, Priority $priority)
    {
        $priority->delete();

        return response()->json(['success' => true]);
    }
}