<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(){
        return view('tenant.inventory.item.index');
    }
    public function itemEntryForm(){
        return view('tenant.inventory.item.entry');
    }
    public function categories() {
        return view('tenant.inventory.category.index');
    }
    public function units() {
        return view('tenant.inventory.unit.index');
    }
    public function stock() {
        return view('tenant.inventory.stock.index');
    }
    public function stockEntry() {
        return view('tenant.inventory.stock.entry');
    }
    public function barcode() {
        return view('tenant.inventory.stock.barcode');
    }
}
