<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(){
        return view('tenant.inventory.item.index');
    }
    public function categories() {
        return view('tenant.inventory.category.index');
    }
    public function units() {
        return view('tenant.inventory.unit.index');
    }
}
