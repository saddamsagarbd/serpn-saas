<?php

namespace App\Livewire\Tenant;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Livewire\Actions\Logout;

class Dashboard extends Component
{
    #[Layout('layouts.tenant')]
    public function render()
    {
        return view('tenant.dashboard');
    }

    public function logout(Request $request, Logout $logout){
        $logout();
        return redirect()->route('tenant.login');
    }
}