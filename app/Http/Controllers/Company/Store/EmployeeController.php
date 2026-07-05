<?php

namespace App\Http\Controllers\Company\Store;

use App\Http\Controllers\Controller;
use App\Models\Bom;
use App\Models\BomItem;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class EmployeeController extends Controller
{
    public function index(Company $company)
    {
        $title = $company->company_name . " - Employee Management";

        return view('company.store.employees.index', [
            'company' => $company,
            'title' => $title,
            'label' => 'Employee List',
            'employees' => Employee::with('department')
                ->where('company_id', $company->id)
                ->get(),
            'departments' => Department::where('company_id', $company->id)->get()
        ]);
    }

    public function show(Company $company, Employee $employee)
    {
        return response()->json($employee);
    }

    public function store(Request $request, Company $company)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'mobile' => 'required|string|max:20',

            'department_id' => 'nullable|exists:departments,id',
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',

            'joining_date' => 'nullable|date',
            'status' => 'required|boolean',
        ]);

        $employee = Employee::create([
            'company_id' => $company->id,

            // personal
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name ?? '-',   // 🔥 FIX
            'father_name' => $request->father_name ?? '-', // 🔥 FIX
            'email' => $request->email,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'previous_company' => $request->previous_company,
            'experience_years' => $request->experience_years,
            'reference_name' => $request->reference_name,

            // location
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'pincode' => $request->pincode,

            // office
            'department_id' => $request->department_id,
            'joining_date' => $request->joining_date,
            'user_id' => $request->user_id,
            'password' => bcrypt($request->password),
            'status' => $request->status,

            // bank
            'bank_name' => $request->bank_name,
            'account_no' => $request->account_no,
            'account_holder' => $request->account_holder,
            'branch_name' => $request->branch_name,
            'ifsc_code' => $request->ifsc_code,
            'pan' => $request->pan,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Employee added successfully',
            'employee' => $employee->load('department')
        ]);
    }
    public function update(Request $request, Company $company, Employee $employee)
    {

        $employee->update($request->only($employee->getFillable()));

        return response()->json([
            'success' => true,
            'message' => 'Employee updated successfully',
            'employee' => $employee->load('department')
        ]);
    }

    public function revealPassword(Request $request)
    {
        if (!Hash::check($request->admin_password, auth()->user()->password)) {
            return response()->json(['message' => 'Invalid admin password'], 403);
        }

        $newPassword = strtoupper(Str::random(8));

        Employee::where('id', $request->employee_id)
            ->update(['password' => bcrypt($newPassword)]);

        return response()->json([
            'password' => $newPassword
        ]);
    }
    public function assignedSearch(Request $request, $company)
    {
        $search = trim($request->search ?? '');

        $query = Employee::with('department')
            ->where('company_id', $company)
            ->presentToday(); // only today's present employees

        if ($search !== '') {

            $query->where(function ($q) use ($search) {

                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('user_id', 'like', "%{$search}%")
                    ->orWhereRaw(
                        "CONCAT(first_name,' ',last_name) LIKE ?",
                        ["%{$search}%"]
                    );

            });
        }

        $employees = $query->limit(20)->get();

        return response()->json([
            'results' => $employees->map(function ($e) {

                return [
                    'id' => $e->id,

                    'text' =>
                        $e->first_name . ' ' . $e->last_name .
                        ' (' . optional($e->department)->name . ')' .
                        ' - ' . $e->user_id,

                    'department_id' => optional($e->department)->id,
                    'department_name' => optional($e->department)->name,
                ];

            })
        ]);
    }
    public function employeeBoms(Request $request, $company)
    {
        $employee = Employee::with('department')
            ->findOrFail(
                $request->employee_id
            );

        $boms = Bom::with('order')
            ->where('company_id', $company)

            ->whereHas('items', function ($q) use ($employee) {

                $q->where(
                    'employee_id',
                    $employee->id
                );

            })

            ->select(
                'id',
                'bom_number',
                'order_id'
            )

            ->distinct()

            ->get()

            ->map(function ($bom) {

                return [

                    'id' => $bom->id,

                    'bom_number' => $bom->bom_number,

                    'order_id' => $bom->order_id,

                    // FULL ORDER CODE
                    'order_number' =>
                        optional(
                            $bom->order
                        )->order_number

                ];

            });

        return response()->json([

            'department_id' =>
                $employee->department_id,

            'department_name' =>
                optional(
                    $employee->department
                )->name,

            'single_bom' =>
                $boms->count() == 1,

            'boms' => $boms->values()

        ]);
    }
    public function workingFeatures(
        Request $request,
        $company
    ) {
        $employeeId = $request->employee_id;

        $bomId = $request->bom_id;


        $brands = \App\Models\Brand::select(
            'id',
            'name'
        )->get();

        $conditions = \App\Models\Condition::select(
            'id',
            'name'
        )->get();


        $items = \App\Models\BomItem::with('item')

            ->where(
                'employee_id',
                $employeeId
            )

            /* IMPORTANT */
            ->where(
                'bom_id',
                $bomId
            )

            ->whereHas('bom', function ($q) use ($company) {

                $q->where(
                    'company_id',
                    $company
                );

            })

            ->get();



        $rows = $items->map(function ($row) {

            $issued = \App\Models\IssueItem::where(
                'bom_item_id',
                $row->id
            )->sum('issued_qty');

            return [

                'bom_item_id' => $row->id,

                'item_id' => $row->item_id,

                'item_name' => $row->item->name,

                'unit_id' => $row->item->unit_id,

                'unit_name' => optional($row->item->unit)->name,

                'pending_qty' =>
                    $row->quantity - $issued

            ];

        })

            ->where('pending_qty', '>', 0)

            /*
            |--------------------------------------------------------------------------
            | MERGE SAME ITEMS
            |--------------------------------------------------------------------------
            */

            ->groupBy(function ($item) {

                return implode('_', [

                    $item['item_id'],
                    $item['unit_id']

                ]);

            })

            ->map(function ($group) {

                $first = $group->first();

                return [

                    'bom_item_id' => $first['bom_item_id'],

                    'item_id' => $first['item_id'],

                    'item_name' => $first['item_name'],

                    'unit_id' => $first['unit_id'],

                    'unit_name' => $first['unit_name'],

                    /*
                    |--------------------------------------------------------------------------
                    | TOTAL QTY
                    |--------------------------------------------------------------------------
                    */

                    'pending_qty' =>
                        $group->sum('pending_qty'),

                ];

            })

            ->values();


        return response()->json([

            'items' => $rows,

            'brands' => $brands,

            'conditions' => $conditions

        ]);
    }
    public function toggleStatus($company, Employee $employee)
    {
        $employee->status = !$employee->status;

        $employee->save();

        return response()->json([
            'success' => true,
            'status' => $employee->status
        ]);
    }
    public function destroy(Company $company, Employee $employee)
    {
        $employee->delete();
        return response()->json(['success' => true]);
    }
}
