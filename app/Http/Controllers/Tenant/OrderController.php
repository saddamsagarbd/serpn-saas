<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ColorContext;
use App\Models\SizeChart;
use App\Models\Style;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index() {
        return view('tenant.orders.index');
    }
    public function create() {
        $styles = Style::get();
        $colors = ColorContext::get();
        $sizes = SizeChart::get();
        return view('tenant.orders.order-create', compact('styles', 'colors', 'sizes'));
    }
}