<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Machine;

use Illuminate\Http\Request;


class MachineController extends Controller
{
    public function index()
    {
        $title = "Machine Management";
        $label = "Machine List";

        $machines = Machine::all();

        return view('admin.machines.index', compact('machines', 'title', 'label'));
    }

    public function create()
    {
        $title = "Machine Management";
        $label = "Add New Machine";

        return view('admin.machines.create', compact('title', 'label'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'hi_name' => 'required',
            'moc' => 'required',
            'size' => 'required',
            'origin' => 'required',
        ]);

        Machine::create($request->all());

        toast('Machine Created Successfully', 'success');

        return redirect()->route('machines.index');
    }

    public function edit($id)
    {
        $title = "Machine Management";
        $label = "Edit Machine";

        $machine = Machine::findOrFail($id);

        return view('admin.machines.edit', compact('machine', 'title', 'label'));
    }

    public function update(Request $request, $id)
    {
        $machine = Machine::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'moc' => 'required',
            'size' => 'required',
            'origin' => 'required',
        ]);

        $machine->update($request->all());

        toast('Machine Updated Successfully', 'success');

        return redirect()->route('machines.index');
    }

    public function destroy($id)
    {
        $machine = Machine::findOrFail($id);

        // Check usage in quotation items
        if ($machine->quotationItems()->exists()) {
            toast('Cannot delete! Machine used in quotations.', 'error');
            return back();
        }

        // Check usage in order items
        if ($machine->orderItems()->exists()) {
            toast('Cannot delete! Machine used in orders.', 'error');
            return back();
        }

        $machine->delete();

        toast('Machine Deleted Successfully', 'success');
        return back();
    }
}
