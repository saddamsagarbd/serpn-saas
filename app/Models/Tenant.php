<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'id', 'plan_id', 'company_name', 'owner_name', 'owner_phone', 'owner_email', 'data'
    ];

    public static function getCustomColumns(): array
    {
        return ['id', 'plan_id', 'company_name', 'owner_name', 'owner_phone', 'owner_email'];
    }

    // প্ল্যানের সাথে রিলেশনশিপ
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
