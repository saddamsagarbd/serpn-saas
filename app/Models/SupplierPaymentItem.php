<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPaymentItem extends Model
{
    protected $guarded = [];

    public function payment()
    {
        return $this->belongsTo(SupplierPayment::class, 'supplier_payment_id');
    }

    public function invoice()
    {
        return $this->belongsTo(SupplierInvoice::class, 'supplier_invoice_id');
    }
}