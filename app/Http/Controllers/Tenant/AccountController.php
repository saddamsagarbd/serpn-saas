<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\LedgerEntry;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

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
     * Chart of Accounts Tree View Structure
     * Route: accounts.chart-of-accounts
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ChartOfAccount::with('parent')->select('chart_of_accounts.*');
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('type', function($row){
                    return $row->type ?? '';
                })
                ->editColumn('parent_name', function($row){
                    return $row->parent ? $row->parent->name : 'Main Root Head';
                })
                ->make(true);
        }
        $parentHeads = ChartOfAccount::where('status', 'active')->get();
        return view('tenant.account.chart-of-accounts', compact('parentHeads'));
    }

    // নতুন অ্যাকাউন্ট হেড তৈরি (এজাক্স ফ্রেন্ডলি)
    public function storeCoa(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:chart_of_accounts,code',
            'type' => 'required|in:asset,liability,equity,income,expense',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'opening_balance' => 'nullable|numeric',
        ]);

        $coa = ChartOfAccount::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'type' => $validated['type'],
            'parent_id' => $validated['parent_id'] ?? null,
            'opening_balance' => $validated['opening_balance'] ?? 0.00,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Account head registered successfully!',
                'data' => $coa
            ]);
        }

        return redirect()->back()->with('success', 'Account head created successfully!');
    }

    public function updateCoa(Request $request, $tenant, String $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // 💡 সমাধান: ,code,' . $id যুক্ত করার কারণে লারাভেল এই অ্যাকাউন্টের নিজের কোডকে ডুপ্লিকেট ধরবে না
            'code' => 'required|string|unique:chart_of_accounts,code,' . $id,
            'type' => 'required|in:asset,liability,equity,income,expense',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'opening_balance' => 'nullable|numeric',
        ]);

        $coa = ChartOfAccount::findOrFail($id);
        
        $coa->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'type' => $validated['type'],
            'parent_id' => $validated['parent_id'] ?: null,
            'opening_balance' => $validated['opening_balance'] ?? 0.00,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Account head updated successfully!',
                'data' => $coa
            ]);
        }

        return redirect()->back()->with('success', 'Account head updated successfully!');
    }

    /**
     * Direct Income / Receipt Voucher View
     * Route: accounts.income
     */
    public function income()
    {
        // ড্রপডাউনের জন্য ইনকাম হেড এবং ক্যাশ/ব্যাংক (Asset) হেড ফিল্টার
        $incomeHeads = ChartOfAccount::where('type', 'income')->where('status', 'active')->get();
        $assetHeads = ChartOfAccount::where('type', 'asset')->where('status', 'active')->get();
        
        return view('tenant.account.income', compact('incomeHeads', 'assetHeads'));
    }

    /**
     * Expense Payment Voucher View
     * Route: accounts.expense
     */
    public function expense()
    {
        // ড্রপডাউনের জন্য ইনকাম হেড এবং ক্যাশ/ব্যাংক (Asset) হেড ফিল্টার
        $expenseHeads = ChartOfAccount::where('type', 'expense')->where('status', 'active')->get();
        $assetHeads = ChartOfAccount::where('type', 'asset')->where('status', 'active')->get();
        
        return view('tenant.account.expense', compact('expenseHeads', 'assetHeads'));
    }

    /**
     * Master Audit Trail & All Transactions Grid
     * Route: accounts.transactions
     */
    public function transactions(Request $request)
    {
        $vouchers = Voucher::with('entries.account')
                    ->orderBy('date', 'desc')
                    ->orderBy('id', 'desc')
                    ->paginate(15);
        return view('tenant.account.transactions', compact('vouchers'));
    }

    /**
     * Liquid Cash Flow Ledger (Cash Book)
     * Route: accounts.cash-book
     */
    public function cashBook(Request $request)
    {
        $cashAccount = ChartOfAccount::where('type', 'asset')
            ->where(function($q) {
                $q->where('name', 'like', '%cash%')
                ->orWhere('code', '1001'); // আমাদের তৈরি করা প্রথম অ্যাসেট কোড
            })->first();

        $entries = [];
        $openingBalance = 0;

        if ($cashAccount) {
            $openingBalance = $cashAccount->opening_balance;

            // ডেট ফিল্টারিং (ডিফল্ট: চলতি মাসের ১ তারিখ থেকে আজ পর্যন্ত)
            $fromDate = $request->get('from_date', date('Y-m-01'));
            $toDate = $request->get('to_date', date('Y-m-d'));

            // শুধুমাত্র ক্যাশ অ্যাকাউন্টের ডেবিট ও ক্রেডিট ট্রানজেকশন লোড
            $entries = LedgerEntry::where('chart_of_account_id', $cashAccount->id)
                ->with('voucher')
                ->join('vouchers', 'ledger_entries.voucher_id', '=', 'vouchers.id')
                ->whereBetween('vouchers.date', [$fromDate, $toDate])
                ->orderBy('vouchers.date', 'asc')
                ->select('ledger_entries.*')
                ->get();
        }
        return view('tenant.account.cash-book', compact('entries', 'openingBalance', 'cashAccount'));
    }

    /**
     * Bank Accounts Directory and Bank Ledgers
     * Route: accounts.bank-accounts
     */
    public function bankAccounts(Request $request)
    {
        // চার্ট অফ অ্যাকাউন্টস থেকে সব ব্যাংক অ্যাকাউন্ট খুঁজে বের করা
        $bankAccounts = ChartOfAccount::with('ledgerEntries')
            ->where('type', 'asset')
            ->where(function($q) {
                $q->where('name', 'like', '%bank%')
                ->orWhere('name', 'like', '%A/C%')
                ->orWhere('code', 'like', '1020%'); // ব্যাংক সিরিজের কোড
            })->get();

        $selectedAccountId = $request->input('account_id', $bankAccounts->first()?->id);
        $entries = [];
        $openingBalance = 0;
        $selectedAccount = null;

        if ($selectedAccountId) {
            $selectedAccount = ChartOfAccount::find($selectedAccountId);
            $openingBalance = $selectedAccount->opening_balance;

            $fromDate = $request->input('from_date', date('Y-m-01'));
            $toDate = $request->input('to_date', date('Y-m-d'));

            $entries = LedgerEntry::where('chart_of_account_id', $selectedAccountId)
                ->with('voucher')
                ->join('vouchers', 'ledger_entries.voucher_id', '=', 'vouchers.id')
                ->whereBetween('vouchers.date', [$fromDate, $toDate])
                ->orderBy('vouchers.date', 'asc')
                ->select('ledger_entries.*')
                ->get();
        }

        return view('tenant.account.bank-accounts', compact('bankAccounts', 'entries', 'openingBalance', 'selectedAccountId', 'selectedAccount'));
    }

    /**
     * Specific Account Ledger Book (Running Balance)
     * Route: accounts.ledger
     */
    public function ledger(Request $request)
    {
        $accounts = ChartOfAccount::where('status', 'active')->get();
        $selectedAccount = $request->get('account_id');
        $entries = [];
        $openingBalance = 0;

        if ($selectedAccount) {
            $account = ChartOfAccount::findOrFail($selectedAccount);
            $openingBalance = $account->opening_balance;

            $entries = LedgerEntry::where('chart_of_account_id', $selectedAccount)
                ->with('voucher')
                ->join('vouchers', 'ledger_entries.voucher_id', '=', 'vouchers.id')
                ->orderBy('vouchers.date', 'asc')
                ->select('ledger_entries.*')
                ->get();
        }
        return view('tenant.account.ledger', compact('accounts', 'entries', 'selectedAccount', 'openingBalance'));
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
        // সমস্ত একটিভ অ্যাকাউন্ট এবং তাদের টোটাল ডেবিট ও ক্রেডিট সামারি বের করা
        $accounts = ChartOfAccount::where('status', 'active')->get();
        
        $trialBalanceData = [];
        $totalDebitSum = 0;
        $totalCreditSum = 0;

        foreach ($accounts as $account) {
            // ডেডিকেটেড লেজার এন্ট্রি থেকে ডেবিট ও ক্রেডিট যোগফল নেওয়া
            $debitTotal = LedgerEntry::where('chart_of_account_id', $account->id)->sum('debit');
            $creditTotal = LedgerEntry::where('chart_of_account_id', $account->id)->sum('credit');
            
            $opening = $account->opening_balance ?? 0;
            $finalDebit = 0;
            $finalCredit = 0;

            // অ্যাকাউন্টিং রুল অনুযায়ী ক্লোজিং ব্যালেন্স ডেবিট না ক্রেডিটে বসবে তা নির্ধারণ
            if (in_array($account->type, ['asset', 'expense'])) {
                $balance = $opening + ($debitTotal - $creditTotal);
                if ($balance >= 0) { $finalDebit = $balance; } else { $finalCredit = abs($balance); }
            } else {
                $balance = $opening + ($creditTotal - $debitTotal);
                if ($balance >= 0) { $finalCredit = $balance; } else { $finalDebit = abs($balance); }
            }

            if ($finalDebit > 0 || $finalCredit > 0) {
                $trialBalanceData[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'type' => $account->type,
                    'debit' => $finalDebit,
                    'credit' => $finalCredit,
                ];
                $totalDebitSum += $finalDebit;
                $totalCreditSum += $finalCredit;
            }
        }
        return view('tenant.account.trial-balance', compact('trialBalanceData', 'totalDebitSum', 'totalCreditSum'));
    }

    /**
     * Operating Income & Expense Statement (Profit & Loss)
     * Route: accounts.profit-loss
     */
    public function profitLoss(Request $request)
    {
        $fromDate = $request->input('from_date', date('Y-m-01'));
        $toDate = $request->input('to_date', date('Y-m-d'));

        // ১. অপারেটিং ইনকাম এন্ট্রি ক্যালকুলেশন
        $incomeAccounts = ChartOfAccount::where('type', 'income')->get();
        $incomeData = [];
        $totalIncome = 0;

        foreach ($incomeAccounts as $account) {
            $creditTotal = LedgerEntry::where('chart_of_account_id', $account->id)
                ->join('vouchers', 'ledger_entries.voucher_id', '=', 'vouchers.id')
                ->whereBetween('vouchers.date', [$fromDate, $toDate])
                ->sum('credit');
            $debitTotal = LedgerEntry::where('chart_of_account_id', $account->id)
                ->join('vouchers', 'ledger_entries.voucher_id', '=', 'vouchers.id')
                ->whereBetween('vouchers.date', [$fromDate, $toDate])
                ->sum('debit');
                
            $netIncome = $creditTotal - $debitTotal; // ইনকাম বাড়ে ক্রেডিটে
            if($netIncome > 0) {
                $incomeData[] = ['name' => $account->name, 'code' => $account->code, 'amount' => $netIncome];
                $totalIncome += $netIncome;
            }
        }

        // ২. অপারেটিং এক্সপেন্স এন্ট্রি ক্যালকুলেশন
        $expenseAccounts = ChartOfAccount::where('type', 'expense')->get();
        $expenseData = [];
        $totalExpense = 0;

        foreach ($expenseAccounts as $account) {
            $debitTotal = LedgerEntry::where('chart_of_account_id', $account->id)
                ->join('vouchers', 'ledger_entries.voucher_id', '=', 'vouchers.id')
                ->whereBetween('vouchers.date', [$fromDate, $toDate])
                ->sum('debit');
            $creditTotal = LedgerEntry::where('chart_of_account_id', $account->id)
                ->join('vouchers', 'ledger_entries.voucher_id', '=', 'vouchers.id')
                ->whereBetween('vouchers.date', [$fromDate, $toDate])
                ->sum('credit');

            $netExpense = $debitTotal - $creditTotal; // খরচ বাড়ে ডেবিটে
            if($netExpense > 0) {
                $expenseData[] = ['name' => $account->name, 'code' => $account->code, 'amount' => $netExpense];
                $totalExpense += $netExpense;
            }
        }

        $netProfitOrLoss = $totalIncome - $totalExpense;
        
        return view('tenant.account.profit-loss', compact('incomeData', 'expenseData', 'totalIncome', 'totalExpense', 'netProfitOrLoss', 'fromDate', 'toDate'));
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