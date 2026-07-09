<?php

return [

    // Top-level links — always visible, rendered as plain links (no dropdown)
    'default_features' => [
        ['label' => 'Profile',   'icon' => 'user',             'route' => 'profile'],
        ['label' => 'Settings',  'icon' => 'settings',         'route' => 'settings'],
    ],

    // Feature-wise dropdown menus — one dropdown per module in saas.php,
    // only rendered if the tenant/plan has that feature enabled.
    'menus' => [  
    
        'inventory' => [
            'label' => 'Inventory',
            'icon'  => 'package',
            'items' => [
                ['sub' => [
                    'label' => 'Setup',
                    'icon'  => 'cog', 
                    'items' => [
                        ['label' => 'Categories',       'route' => 'inventory.categories'],
                        // ['label' => 'Brands',         'route' => 'inventory.brands'],
                        ['label' => 'Units',            'route' => 'inventory.units'],
                        ['label' => 'Warehouse',        'route' => 'inventory.warehouses.index'], // 💡 রুটটি ঠিক করে দেওয়া হয়েছে
                    ],
                ]],
                ['label' => 'Items', 'icon'  => 'package', 'route' => 'inventory.items.index'],
                ['sub' => [
                    'label' => 'Stock',
                    'icon'  => 'layers', 
                    'items' => [
                        ['label' => 'Stock Ledger',       'route' => 'inventory.stock'],
                        ['label' => 'Stock Adjustment',            'route' => 'inventory.stock.entry'],
                        ['label' => 'Barcode',        'route' => 'inventory.barcode'],
                    ],
                ]],
            ],
        ],

        'purchase' => [
            'label' => 'Purchase',
            'icon'  => 'shopping-bag',
            'items' => [
                ['label' => 'Purchase Order',         'route' => 'purchase.purchase'],
                ['label' => 'GRN/MRR',          'route' => 'purchase.grn'],
                ['label' => 'Suppliers',        'route' => 'purchase.suppliers'],
                // ['label' => 'Purchase Return',  'route' => 'purchase.purchase-return'],
            ],
        ],

        'sales' => [
            'label' => 'Sales',
            'icon'  => 'shopping-cart',
            'items' => [
                ['label' => 'POS',            'route' => 'sales.pos'],
                ['label' => 'Sales',          'route' => 'sales.sales'],
                ['label' => 'Customers',      'route' => 'sales.customers'],
                ['label' => 'Sales Return',   'route' => 'sales.sales-return'],
                // ['label' => 'Quotation',      'route' => 'sales.quotation'],
            ],
        ],

        /*
        *   1= Super Admin
            2= Tenant
        */

        'accounts' => [
            'label' => 'Accounts',
            'icon'  => 'calculator',
            'items' => [
                // ['label' => 'Dashboard',          'route' => 'accounts.dashboard'],
                ['label' => 'Income',             'route' => 'accounts.income'],
                ['label' => 'Expense',            'route' => 'accounts.expense'],
                ['label' => 'Transactions',       'route' => 'accounts.transactions'],
                ['label' => 'Chart of Accounts',  'route' => 'accounts.chart-of-accounts', 'for' => 1],
                ['label' => 'Cash Book',          'route' => 'accounts.cash-book'],
                ['label' => 'Bank Accounts',      'route' => 'accounts.bank-accounts'],
                ['label' => 'Ledger',             'route' => 'accounts.ledger'],
                ['label' => 'Journal Entry',      'route' => 'accounts.journal-entry'],
                ['label' => 'Trial Balance',      'route' => 'accounts.trial-balance'],
                ['label' => 'Profit & Loss',      'route' => 'accounts.profit-loss'],
                ['label' => 'Balance Sheet',      'route' => 'accounts.balance-sheet'],
            ],
        ],

        // 'hrm' => [
        //     'label' => 'HRM',
        //     'icon'  => 'users',
        //     'items' => [
        //         ['label' => 'Employees',    'route' => 'hrm.employees'],
        //         ['label' => 'Departments',  'route' => 'hrm.departments'],
        //         ['label' => 'Designation',  'route' => 'hrm.designation'],
        //         ['label' => 'Attendance',   'route' => 'hrm.attendance'],
        //         ['label' => 'Leave',        'route' => 'hrm.leave'],
        //         ['label' => 'Payroll',      'route' => 'hrm.payroll'],
        //     ],
        // ],

        // 'crm' => [
        //     'label' => 'CRM',
        //     'icon'  => 'contact',
        //     'items' => [
        //         ['label' => 'Customers',   'route' => 'crm.customers'],
        //         ['label' => 'Leads',       'route' => 'crm.leads'],
        //         ['label' => 'Follow Up',   'route' => 'crm.follow-up'],
        //     ],
        // ],

        // 'website' => [
        //     'label' => 'Website',
        //     'icon'  => 'globe',
        //     'items' => [
        //         ['label' => 'Website',  'route' => 'website.website'],
        //         ['label' => 'Pages',    'route' => 'website.pages'],
        //         ['label' => 'Blogs',    'route' => 'website.blogs'],
        //     ],
        // ],

        // 'sms' => [
        //     'label' => 'SMS',
        //     'icon'  => 'message-square',
        //     'items' => [
        //         ['label' => 'SMS',        'route' => 'sms.sms'],
        //         ['label' => 'Templates',  'route' => 'sms.templates'],
        //     ],
        // ],

        'reports' => [
            'label' => 'Reports',
            'icon'  => 'bar-chart-2',
            'items' => [
                ['label' => 'Sales Report',     'route' => 'reports.sales-report'],
                ['label' => 'Purchase Report',  'route' => 'reports.purchase-report'],
                ['label' => 'Stock Report',     'route' => 'reports.stock-report'],
                ['label' => 'Income Report',    'route' => 'reports.income-report'],
                ['label' => 'Expense Report',   'route' => 'reports.expense-report'],
                ['label' => 'Customer Report',  'route' => 'reports.customer-report'],
            ],
        ],

    ],

];