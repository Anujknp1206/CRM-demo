<?php

namespace App\Http\Controllers\Company\Store;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Company $company)
    {
        $title = $company->company_name . " - Project Management";
        $label = 'Project List';
        $projects = Project::where('company_id', $company->id)->get();
        return view('company.store.projects.index', compact('company', 'projects', 'title', 'label'));
    }

    public function store(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:projects,code',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $project = Project::create([
            'company_id' => $company->id,
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return response()->json([
            'message' => 'Project created successfully',
            'project' => $project
        ]);
    }
    public function search(Request $request, Company $company)
    {
        $q = $request->q;

        $projects = Project::where('company_id', $company->id)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($projects);
    }

    public function show(Company $company, Project $project)
    {
        return response()->json($project);
    }

    public function update(Request $request, Company $company, Project $project)
    {
        $project->update($request->all());

        return response()->json([
            'message' => 'Project updated successfully',
            'project' => $project
        ]);
    }

    public function destroy(Company $company, Project $project)
    {
        $project->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
