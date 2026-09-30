<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncAttendanceDevice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:sync-device';
    protected $description = 'Sync attendance data from MSSQL device (1.104) and handle offline catch-up.';

    public function handle()
    {
        $deviceIp = config('database.connections.sqlsrv2.host', '192.168.1.104');
        $devicePort = config('database.connections.sqlsrv2.port', 1433);

        // ১. ডিভাইস অনলাইন কি না Socket দিয়ে চেক
        $isOnline = $this->checkDeviceOnline($deviceIp, $devicePort);

        if (!$isOnline) {
            Cache::put('device_104_status', 'offline', now()->addMinutes(10));
            $this->error("Device {$deviceIp} is Offline. Skipping sync.");
            return 0;
        }

        // পূর্বে অফলাইন ছিল কি না চেক করা
        $wasOffline = Cache::get('device_104_status') === 'offline';
        Cache::put('device_104_status', 'online', now()->addHours(24));

        $this->info("Device is Online! Starting sync...");

        // ২. ক্যাচ-আপ ডেট রেঞ্জ নির্ধারণ
        $startDate = $wasOffline ? now()->subDays(7)->format('Y-m-d') : now()->format('Y-m-d');
        $endDate = now()->format('Y-m-d');

        try {
            $punches = DB::connection('sqlsrv2')
                ->table('CHECKINOUT')
                ->join('USERINFO', 'CHECKINOUT.USERID', '=', 'USERINFO.USERID')
                ->select(
                    'CHECKINOUT.USERID as zk_userid',
                    'USERINFO.BADGENUMBER as emp_id',
                    'CHECKINOUT.CHECKTIME',
                    'CHECKINOUT.CHECKTYPE'
                )
                ->whereBetween('CHECKTIME', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->get();

            if ($punches->isEmpty()) {
                $this->info("No new punch records found.");
                return 0;
            }

            // N+1 Query কমানোর জন্য সব এমপ্লয়ি একবারে লোড করা
            $badgeNumbers = $punches->pluck('emp_id')->unique()->filter();
            $employees = Employee::whereIn('employee_id', $badgeNumbers)
                ->get()
                ->keyBy('employee_id');

            foreach ($punches->groupBy('emp_id') as $badgeNumber => $userPunches) {
                
                $employee = $employees->get($badgeNumber);

                if ($employee) {
                    $punchesByDate = $userPunches->groupBy(function ($item) {
                        return Carbon::parse($item->CHECKTIME)->format('Y-m-d');
                    });

                    foreach ($punchesByDate as $punchDate => $dailyPunches) {
                        
                        $sortedPunches = $dailyPunches->sortBy('CHECKTIME');
                        
                        $firstPunch = $sortedPunches->first()->CHECKTIME ?? null;
                        $lastPunch  = $sortedPunches->last()->CHECKTIME ?? null;

                        $inTime  = $firstPunch ? Carbon::parse($firstPunch)->format('H:i:s') : null;
                        $outTime = ($sortedPunches->count() > 1 && $lastPunch) 
                                    ? Carbon::parse($lastPunch)->format('H:i:s') 
                                    : null;

                        // ডাটাবেজ আপডেট/ইনসার্ট
                        $attendance = Attendance::firstOrNew([
                            'employee_id' => $employee->id,
                            'date'        => $punchDate,
                        ]);

                        $attendance->tenant_id = $employee->tenant_id ?? tenant('id');
                        
                        // ইন-টাইম যা প্রথম পাওয়া গেছে তা বসাবে
                        if (!$attendance->in_time || $inTime < $attendance->in_time) {
                            $attendance->in_time = $inTime;
                        }

                        // আউট-টাইম যদি নতুন থাকে বা লেটেস্ট হয় তবেই আপডেট করবে
                        if ($outTime && (!$attendance->out_time || $outTime > $attendance->out_time)) {
                            $attendance->out_time = $outTime;
                        }

                        $attendance->status = $attendance->in_time ? 'Present' : 'Absent';
                        $attendance->save();
                    }
                }
            }

            $this->info("Sync completed successfully from {$startDate} to {$endDate}.");

        } catch (\Exception $e) {
            Cache::put('device_104_status', 'offline', now()->addMinutes(10));
            Log::error("Attendance Sync Error: " . $e->getMessage());
            $this->error("Error fetching data: " . $e->getMessage());
        }

        return 0;
    }

    /**
     * IP & Port কানেকশন টেস্ট করার মেথড
     */
    private function checkDeviceOnline($ip, $port, $timeout = 3)
    {
        $connection = @fsockopen($ip, $port, $errno, $errstr, $timeout);
        if (is_resource($connection)) {
            fclose($connection);
            return true;
        }
        return false;
    }
}
