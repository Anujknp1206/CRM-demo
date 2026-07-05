<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Component;
use Illuminate\Http\Request;

class ComponentController extends Controller
{
    public function index()
    {
        $title = "Components Management";
        $label = "Components List";
        $components = Component::all();
        return view('admin.components.index', compact('components', 'title', 'label'));
    }

    public function create()
    {
        $title = "Components Management";
        $label = "Components List";
        return view('admin.components.create', compact('title', 'label'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'moc' => 'required',
            'size' => 'required',
            'origin' => 'required',
        ]);

        Component::create($request->all());
        toast('Component Created Successfully', 'success');
        return redirect()->route('components.index');
    }

    public function edit($id)
    {
        $title = "Components Management";
        $label = "Components List";
        $component = Component::findOrFail($id);
        return view('admin.components.edit', compact('component', 'title', 'label'));
    }

    public function update(Request $request, $id)
    {
        $component = Component::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'moc' => 'required',
            'size' => 'required',
            'origin' => 'required',
        ]);

        $component->update($request->all());
        toast('Component Updated Successfully', 'success');
        return redirect()->route('components.index');
    }

    public function destroy($id)
    {
        $component = Component::findOrFail($id);

        if ($component->quotationItems()->exists()) {
            toast('Cannot delete! Component used in quotations.', 'error');
            return back();
        }

        if ($component->orderItems()->exists()) {
            toast('Cannot delete! Component used in orders.', 'error');
            return back();
        }

        $component->delete();

        toast('Component Deleted Successfully', 'success');
        return back();
    }
}
