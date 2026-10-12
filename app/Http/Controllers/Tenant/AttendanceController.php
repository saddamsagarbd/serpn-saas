<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Company;
use App\Models\Employee;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index($tenant, Request $request)
    {
        if ($request->ajax()) {
            $fromDate = $request->input('from_date', date('Y-m-d'));
            $toDate   = $request->input('to_date', date('Y-m-d'));
            $empId    = $request->input('search'); // e.g., '1004'

            // ১. নির্দিষ্ট এমপ্লয়ি অবজেক্ট বের করা (যদি search থাকে)
            $employee = $empId ? Employee::where('employee_id', $empId)->first() : null;

            // ২. অ্যাটেনডেন্স ক্যোয়ারি
            $attendances = Attendance::whereBetween('date', [$fromDate, $toDate])
                ->when($employee, fn($q) => $q->where('employee_id', $employee->id))
                ->get()
                ->keyBy(fn($item) => Carbon::parse($item->date)->format('Y-m-d'));

            $period = CarbonPeriod::create($fromDate, $toDate);
            $data   = [];

            foreach ($period as $date) {
                $formattedDate = $date->format('Y-m-d');

                if (isset($attendances[$formattedDate])) {
                    $record = $attendances[$formattedDate];
                    $data[] = [
                        'date'          => $formattedDate,
                        'employee_name' => $record->employee->name ?? ($employee->name ?? 'N/A'),
                        'emp_id'        => $record->employee->employee_id ?? ($employee->employee_id ?? $empId),
                        'in_time'       => Carbon::parse($record->in_time)->format('h:i'),
                        'out_time'      => Carbon::parse($record->out_time)->format('h:i'),
                        'work_hours'    => $record->total_hours, // Schema অনুযায়ী মিল রাখা হয়েছে
                        'status'        => $record->status ?? "Absent",
                    ];
                } else {
                    // ওই তারিখে উপস্থিতি না থাকলে ফাঁকা রো
                    $data[] = [
                        'date'          => $formattedDate,
                        'employee_name' => $employee->name ?? 'N/A',
                        'emp_id'        => $employee->employee_id ?? $empId,
                        'in_time'       => null,
                        'out_time'      => null,
                        'work_hours'    => null,
                        'status'        => null,
                    ];
                }
            }

            return response()->json([
                'data'      => $data,
                'total'     => count($data),
                'last_page' => 1
            ]);
        }

        return view('tenant.hrm.attendance.index');
    }

    public function getFilterData()
    {
        return response()->json([
            'companies' => Company::select('id', 'name')->get(),
        ]);
    }

    /**
     * Fetch Punch Logs from MSSQL Server (1.104) and Sync
     */

    public function syncDeviceLogs(Request $request)
    {
        $fromDate = $request->input('from_date', date('Y-m-d'));
        $toDate = $request->input('to_date', date('Y-m-d'));

        $startDateTime = $fromDate . ' 00:00:00';
        $endDateTime   = $toDate . ' 23:59:59';

        try {
            // MSSQL (1.104) Server DB Query [BADGENUMBER] .[USERINFO]
            $punches = DB::connection('sqlsrv2')
                ->table('CHECKINOUT')
                ->join('USERINFO', 'CHECKINOUT.USERID', '=', 'USERINFO.USERID')
                ->select(
                    'CHECKINOUT.USERID as zk_userid',
                    'USERINFO.BADGENUMBER as emp_id',
                    'CHECKINOUT.CHECKTIME',
                    'CHECKINOUT.CHECKTYPE'
                )
                ->whereBetween('CHECKINOUT.CHECKTIME', [$startDateTime, $endDateTime])
                ->get();

            $badgeNumbers = $punches->pluck('emp_id')->unique()->filter();

            $employees = Employee::whereIn('employee_id', $badgeNumbers)
                ->orWhereIn('sensor_id', $badgeNumbers)
                ->get()
                ->keyBy('employee_id');

            foreach ($punches->groupBy('emp_id') as $badgeNumber => $userPunches) {
                
                $employee = $employees->get($badgeNumber);

                if ($employee) {
                    // sorted by y-m-d format every punch
                    $punchesByDate = $userPunches->groupBy(function ($item) {
                        return Carbon::parse($item->CHECKTIME)->format('Y-m-d');
                    });

                    foreach ($punchesByDate as $punchDate => $dailyPunches) {
                        
                        $sortedPunches = $dailyPunches->sortBy('CHECKTIME');
                        
                        $firstPunch = $sortedPunches->first()->CHECKTIME ?? null;
                        $lastPunch  = $sortedPunches->last()->CHECKTIME ?? null;

                        // set null if no multiple punch found
                        $inTime  = $firstPunch ? Carbon::parse($firstPunch)->format('H:i:s') : null;
                        $outTime = ($sortedPunches->count() > 1 && $lastPunch) 
                                    ? Carbon::parse($lastPunch)->format('H:i:s') 
                                    : null;

                        Attendance::updateOrCreate(
                            [
                                'employee_id' => $employee->id,
                                'date'        => $punchDate, // date range
                            ],
                            [
                                'tenant_id' => $employee->tenant_id ?? tenant('id'),
                                'in_time'   => $inTime,
                                'out_time'  => $outTime,
                                'status'    => $inTime ? 'Present' : 'Absent',
                            ]
                        );
                    }
                }
            }

            return response()->json(['success' => true, 'message' => 'Attendance successfully synced from Device 1.104!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'MSSQL Connection Error: ' . $e->getMessage()], 500);
        }
    }
}
