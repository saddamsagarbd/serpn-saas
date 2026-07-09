<?php

namespace App\Livewire\Tenant;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;

class Dashboard extends Component
{
    #[Layout('layouts.tenant')]
    public function render()
    {
        return view('tenant.dashboard');
    }
}