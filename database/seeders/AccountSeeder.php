<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // ---- Assets ----
            ['code' => '1000', 'name' => 'Cash in Hand',        'type' => 'asset',     'is_bank_account' => true,  'is_system_defined' => true],
            ['code' => '1010', 'name' => 'Bank Account',        'type' => 'asset',     'is_bank_account' => true,  'is_system_defined' => true],
            ['code' => 'AR',   'name' => 'Accounts Receivable', 'type' => 'asset',     'is_control_account' => true, 'is_system_defined' => true],
            ['code' => '1500', 'name' => 'Fixed Assets',        'type' => 'asset',     'is_system_defined' => true],
            ['code' => '1510', 'name' => 'Accumulated Depreciation', 'type' => 'asset', 'is_system_defined' => true],

            // ---- Liabilities ----
            ['code' => 'AP',   'name' => 'Accounts Payable',    'type' => 'liability', 'is_control_account' => true, 'is_system_defined' => true],
            ['code' => '2100', 'name' => 'Tax Payable',         'type' => 'liability', 'is_system_defined' => true],

            // ---- Equity ----
            ['code' => '3000', 'name' => "Owner's Equity",      'type' => 'equity',    'is_system_defined' => true],
            ['code' => '3900', 'name' => 'Retained Earnings',   'type' => 'equity',    'is_system_defined' => true],

            // ---- Income ----
            ['code' => '4000', 'name' => 'Sales Revenue',       'type' => 'income',    'is_system_defined' => false],
            ['code' => '4900', 'name' => 'Other Income',        'type' => 'income',    'is_system_defined' => false],

            // ---- Expense ----
            ['code' => '5000', 'name' => 'Cost of Goods Sold',  'type' => 'expense',   'is_system_defined' => false],
            ['code' => '5100', 'name' => 'Rent Expense',        'type' => 'expense',   'is_system_defined' => false],
            ['code' => '5200', 'name' => 'Utilities Expense',   'type' => 'expense',   'is_system_defined' => false],
            ['code' => '5300', 'name' => 'Salaries Expense',    'type' => 'expense',   'is_system_defined' => false],
            ['code' => '5900', 'name' => 'Depreciation Expense','type' => 'expense',   'is_system_defined' => false],
        ];

        foreach ($accounts as $account) {
            ChartOfAccount::firstOrCreate(
                ['code' => $account['code']],
                [
                    'name'                => $account['name'],
                    'type'                => $account['type'],
                    'is_bank_account'     => $account['is_bank_account'] ?? false,
                    'is_control_account'  => $account['is_control_account'] ?? false,
                    'is_system_defined'   => $account['is_system_defined'] ?? false,
                ]
            );
        }
    }
}