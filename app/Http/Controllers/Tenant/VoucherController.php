<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\Voucher;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VoucherController extends Controller
{
    // ভাউচার ও ডাবল-এন্ট্রি লেজার পোস্টিং ইঞ্জিন
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:income,expense',
            'date' => 'required|date',
            'chart_of_account_id' => 'required|exists:chart_of_accounts,id', // মূল ইনকাম/এক্সপেন্স হেড
            'payment_method_id' => 'required|exists:chart_of_accounts,id', // ক্যাশ বা ব্যাংক হেড
            'amount' => 'required|numeric|min:0.01',
            'narration' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            // ১. অটোমেটিক ইউনিক ভাউচার নাম্বার জেনারেশন
            $prefix = $request->type == 'income' ? 'INC-' : 'EXP-';
            $voucherNo = $prefix . date('Ymd') . '-' . strtoupper(Str::random(4));

            // ২. ভাউচার মাস্টার রেকর্ড তৈরি
            $voucher = Voucher::create([
                'voucher_no' => $voucherNo,
                'type' => $request->type,
                'date' => $request->date,
                'narration' => $request->narration,
                'total_amount' => $request->amount,
            ]);

            // ৩. কোর অ্যাকাউнтиং ডাবল-এন্ট্রি (Debit-Credit) লজিক
            if ($request->type === 'income') {
                // ইনকাম হলে: ক্যাশ/ব্যাংক একাউন্ট হবে Debit
                LedgerEntry::create([
                    'voucher_id' => $voucher->id,
                    'chart_of_account_id' => $request->payment_method_id,
                    'debit' => $request->amount,
                    'credit' => 0.00
                ]);
                // নির্দিষ্ট ইনকাম হেড হবে Credit
                LedgerEntry::create([
                    'voucher_id' => $voucher->id,
                    'chart_of_account_id' => $request->chart_of_account_id,
                    'debit' => 0.00,
                    'credit' => $request->amount
                ]);
            } else {
                // এক্সপেন্স হলে: নির্দিষ্ট খরচ হেড হবে Debit
                LedgerEntry::create([
                    'voucher_id' => $voucher->id,
                    'chart_of_account_id' => $request->chart_of_account_id,
                    'debit' => $request->amount,
                    'credit' => 0.00
                ]);
                // ক্যাশ/ব্যাংক একাউন্ট হবে Credit
                LedgerEntry::create([
                    'voucher_id' => $voucher->id,
                    'chart_of_account_id' => $request->payment_method_id,
                    'debit' => 0.00,
                    'credit' => $request->amount
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Voucher posted successfully! No: ' . $voucherNo]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Accounting Posting Failed: ' . $e->getMessage()], 500);
        }
    }
}