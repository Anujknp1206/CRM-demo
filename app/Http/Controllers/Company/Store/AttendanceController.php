<?php

namespace App\Http\Controllers\Company\Store;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
class AttendanceController extends Controller
{
    public function index(Company $company)
    {
        $title = $company->company_name . " - Attendance Management";
        return view('company.store.employees.attandance', [
            'company' => $company,
            'title' => $title,
            'label' => 'Attendance',
            'departments' => Department::where('company_id', $company->id)->get()
        ]);
    }
    public function checkTodayAttendance($companyId)
    {
        $attendanceMarked = Attendance::where('company_id', $companyId)
            ->whereDate('date', now()->toDateString())
            ->where('is_present', 1)
            ->exists();

        return response()->json([
            'attendanceMarked' => $attendanceMarked
        ]);
    }
    public function data(Request $request, Company $company)
    {
        $date = $request->date ?? now()->toDateString();

        $query = Employee::with('department')
            ->where('company_id', $company->id)
            ->where('status', true);

        $search = $request->search ?: null;
        $dept = $request->department_id ?: null;
        $status = $request->status !== null && $request->status !== ''
            ? (int) $request->status
            : null;

        // =============================
        // SEARCH + DEPARTMENT FILTER
        // =============================
        if ($search !== null) {
            $query->where('id', $search);
        }

        if ($dept !== null) {
            $query->where('department_id', $dept);
        }

        // =============================
        // ✅ STATUS FILTER (MOVE HERE)
        // =============================
        if ($status !== null) {

            // ✅ Present
            if ($status === 1) {
                $query->whereHas('attendances', function ($q) use ($date) {
                    $q->where('date', $date)
                        ->where('is_present', 1);
                });
            }

            // ✅ Absent
            elseif ($status === 0) {
                $query->whereDoesntHave('attendances', function ($q) use ($date) {
                    $q->where('date', $date)
                        ->where('is_present', 1);
                });
            }
        }

        // =============================
        // ✅ NOW FETCH DATA
        // =============================
        $employees = $query->with([
            'attendances' => function ($q) use ($date) {
                $q->where('date', $date);
            }
        ])->get();

        return view('company.store.employees.partials.rows', compact('employees'))->render();
    }
    public function store(Request $request, Company $company)
    {
        Attendance::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'date' => $request->date
            ],
            [
                'company_id' => $company->id,
                'is_present' => $request->is_present
            ]
        );

        return response()->json(['success' => true]);
    }
}
