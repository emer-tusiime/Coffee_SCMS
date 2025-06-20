<?php

namespace App\Http\Controllers\WorkforceManager;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAttendance;
use App\Models\Workforce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmployeeAttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $attendance = EmployeeAttendance::with('employee')
            ->where('location_id', Auth::user()->workforceManager->location_id)
            ->latest()
            ->paginate(15);

        return view('workforce-manager.attendance.index', compact('attendance'));
    }

    public function markAttendance(Request $request, Workforce $employee)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:present,absent,late,leave',
            'notes' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        EmployeeAttendance::create([
            'employee_id' => $employee->id,
            'location_id' => Auth::user()->workforceManager->location_id,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('workforce-manager.attendance.index')
            ->with('success', 'Attendance marked successfully');
    }

    public function getAttendanceReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $attendance = EmployeeAttendance::with('employee')
            ->where('location_id', Auth::user()->workforceManager->location_id)
            ->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ])
            ->get()
            ->groupBy('employee_id');

        return view('workforce-manager.attendance.report', compact('attendance'));
    }

    public function exportAttendanceReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $attendance = EmployeeAttendance::with('employee')
            ->where('location_id', Auth::user()->workforceManager->location_id)
            ->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ])
            ->get()
            ->groupBy('employee_id');

        // Generate CSV
        $filename = 'attendance-report-' . date('Y-m-d') . '.csv';
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('Employee Name', 'Employee ID', 'Date', 'Status', 'Notes');
        $callback = function() use($attendance, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($attendance as $employeeId => $records) {
                $employee = $records->first()->employee;
                foreach ($records as $record) {
                    fputcsv($file, array(
                        $employee->name,
                        $employee->id,
                        $record->created_at->format('Y-m-d'),
                        $record->status,
                        $record->notes
                    ));
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
