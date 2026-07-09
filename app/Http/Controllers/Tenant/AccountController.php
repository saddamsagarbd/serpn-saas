<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Accounts Module Dashboard View
     * Route: accounts.dashboard
     */
    // public function dashboard()
    // {
    //     // বাস্তব প্রজেক্টে এখানে DB::table বা Model Queries বসবে
    //     return view('tenant.account.dashboard');
    // }

    /**
     * Direct Income / Receipt Voucher View
     * Route: accounts.income
     */
    public function income()
    {
        return view('tenant.account.income');
    }

    /**
     * Expense Payment Voucher View
     * Route: accounts.expense
     */
    public function expense()
    {
        return view('tenant.account.expense');
    }

    /**
     * Master Audit Trail & All Transactions Grid
     * Route: accounts.transactions
     */
    public function transactions(Request $request)
    {
        return view('tenant.account.transactions');
    }

    /**
     * Chart of Accounts Tree View Structure
     * Route: accounts.chart-of-accounts
     */
    public function chartOfAccounts()
    {
        return view('tenant.account.chart-of-accounts');
    }

    /**
     * Liquid Cash Flow Ledger (Cash Book)
     * Route: accounts.cash-book
     */
    public function cashBook()
    {
        return view('tenant.account.cash-book');
    }

    /**
     * Bank Accounts Directory and Bank Ledgers
     * Route: accounts.bank-accounts
     */
    public function bankAccounts()
    {
        return view('tenant.account.bank-accounts');
    }

    /**
     * Specific Account Ledger Book (Running Balance)
     * Route: accounts.ledger
     */
    public function ledger(Request $request)
    {
        return view('tenant.account.ledger');
    }

    /**
     * Double-Entry Manual Journal Adjustment Grid
     * Route: accounts.journal-entry
     */
    public function journalEntry()
    {
        return view('tenant.account.journal-entry');
    }

    /**
     * Financial Trail Audit Statement (Trial Balance)
     * Route: accounts.trial-balance
     */
    public function trialBalance()
    {
        return view('tenant.account.trial-balance');
    }

    /**
     * Operating Income & Expense Statement (Profit & Loss)
     * Route: accounts.profit-loss
     */
    public function profitLoss()
    {
        return view('tenant.account.profit-loss');
    }

    /**
     * Core Financial Position Framework (Balance Sheet)
     * Route: accounts.balance-sheet
     */
    public function balanceSheet()
    {
        return view('tenant.account.balance-sheet');
    }
}
