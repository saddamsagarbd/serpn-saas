<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'in_time' => 'datetime',
        'out_time' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function ($attendance) {
            if ($attendance->in_time && $attendance->out_time) {
                $in   = Carbon::parse($attendance->in_time);
                $out  = Carbon::parse($attendance->out_time);
                
                $duration = $in->diff($out);
                $attendance->total_hours = sprintf('%02dh %02dm', $duration->h, $duration->i);
            }
        });
    }
}
