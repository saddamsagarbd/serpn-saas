<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
        $tenants = Tenant::all(); // with('domains') সাময়িকভাবে বাদ দেওয়া হলো রিলেশন এরর এড়াতে
        return view('dashboard', compact('tenants'));
    }
}
