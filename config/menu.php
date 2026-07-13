<?php

return [

    // Top-level links — always visible, rendered as plain links (no dropdown)
    'default_features' => [
        ['label' => 'Profile',   'icon' => 'user',             'route' => 'tenant.profile'],
        ['label' => 'Settings',  'icon' => 'settings',         'route' => 'tenant.settings'],
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
                        ['label' => 'Categories',       'route' => 'tenant.inventory.categories.index'],
                        ['label' => 'Brands',         'route' => 'tenant.inventory.brands'],
                        ['label' => 'Units',            'route' => 'tenant.inventory.units'],
                        ['label' => 'Styles',            'route' => 'tenant.inventory.styles'],
                        // ['label' => 'Fabric Spec',            'route' => 'tenant.inventory.fabric'],
                        // ['label' => 'Color Context',            'route' => 'tenant.inventory.color'],
                        ['label' => 'Warehouse',        'route' => 'tenant.inventory.warehouses.index'],
                    ],
                ]],
                ['label' => 'Items', 'icon'  => 'package', 'route' => 'tenant.inventory.items.index'],
                ['sub' => [
                    'label' => 'Stock',
                    'icon'  => 'layers', 
                    'items' => [
                        ['label' => 'Stock Ledger',       'route' => 'tenant.inventory.stock'],
                        ['label' => 'Stock Adjustment',            'route' => 'tenant.inventory.stock.entry'],
                        ['label' => 'Barcode',        'route' => 'tenant.inventory.barcode'],
                    ],
                ]],
            ],
        ],

        'purchase' => [
            'label' => 'Purchase',
            'icon'  => 'shopping-bag',
            'items' => [
                ['label' => 'Purchase Order',         'route' => 'tenant.purchase.purchase'],
                ['label' => 'GRN/MRR',          'route' => 'tenant.purchase.grn'],
                ['label' => 'Suppliers',        'route' => 'tenant.purchase.suppliers'],
                // ['label' => 'Purchase Return',  'route' => 'purchase.purchase-return'],
            ],
        ],

        'sales' => [
            'label' => 'Sales',
            'icon'  => 'shopping-cart',
            'items' => [
                ['label' => 'POS',            'route' => 'tenant.sales.pos'],
                ['label' => 'Sales',          'route' => 'tenant.sales.sales'],
                ['label' => 'Customers',      'route' => 'tenant.sales.customers'],
                ['label' => 'Sales Return',   'route' => 'tenant.sales.sales-return'],
                // ['label' => 'Quotation',      'route' => 'tenant.sales.quotation'],
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
                // ['label' => 'Dashboard',          'route' => 'tenant.accounts.dashboard'],
                ['label' => 'Chart of Accounts',  'route' => 'tenant.accounts.coa.index'],
                ['label' => 'Income',             'route' => 'tenant.accounts.income'],
                ['label' => 'Expense',            'route' => 'tenant.accounts.expense'],
                ['label' => 'Transactions',       'route' => 'tenant.accounts.transactions'],
                ['label' => 'Ledger',             'route' => 'tenant.accounts.ledger'],
                ['label' => 'Cash Book',          'route' => 'tenant.accounts.cash-book'],
                ['label' => 'Bank Accounts',      'route' => 'tenant.accounts.bank-accounts'],
                ['label' => 'Journal Entry',      'route' => 'tenant.accounts.journal-entry'],
                ['label' => 'Trial Balance',      'route' => 'tenant.accounts.trial-balance'],
                ['label' => 'Profit & Loss',      'route' => 'tenant.accounts.profit-loss'],
                ['label' => 'Balance Sheet',      'route' => 'tenant.accounts.balance-sheet'],
            ],
        ],

        // 'hrm' => [
        //     'label' => 'HRM',
        //     'icon'  => 'users',
        //     'items' => [
        //         ['label' => 'Employees',    'route' => 'tenant.hrm.employees'],
        //         ['label' => 'Departments',  'route' => 'tenant.hrm.departments'],
        //         ['label' => 'Designation',  'route' => 'tenant.hrm.designation'],
        //         ['label' => 'Attendance',   'route' => 'tenant.hrm.attendance'],
        //         ['label' => 'Leave',        'route' => 'tenant.hrm.leave'],
        //         ['label' => 'Payroll',      'route' => 'tenant.hrm.payroll'],
        //     ],
        // ],

        // 'crm' => [
        //     'label' => 'CRM',
        //     'icon'  => 'contact',
        //     'items' => [
        //         ['label' => 'Customers',   'route' => 'tenant.crm.customers'],
        //         ['label' => 'Leads',       'route' => 'tenant.crm.leads'],
        //         ['label' => 'Follow Up',   'route' => 'tenant.crm.follow-up'],
        //     ],
        // ],

        // 'website' => [
        //     'label' => 'Website',
        //     'icon'  => 'globe',
        //     'items' => [
        //         ['label' => 'Website',  'route' => 'tenant.website.website'],
        //         ['label' => 'Pages',    'route' => 'tenant.website.pages'],
        //         ['label' => 'Blogs',    'route' => 'tenant.website.blogs'],
        //     ],
        // ],

        // 'sms' => [
        //     'label' => 'SMS',
        //     'icon'  => 'message-square',
        //     'items' => [
        //         ['label' => 'SMS',        'route' => 'tenant.sms.sms'],
        //         ['label' => 'Templates',  'route' => 'tenant.sms.templates'],
        //     ],
        // ],

        'reports' => [
            'label' => 'Reports',
            'icon'  => 'bar-chart-2',
            'items' => [
                ['label' => 'Sales Report',     'route' => 'tenant.reports.sales-report'],
                ['label' => 'Purchase Report',  'route' => 'tenant.reports.purchase-report'],
                ['label' => 'Stock Report',     'route' => 'tenant.reports.stock-report'],
                ['label' => 'Income Report',    'route' => 'tenant.reports.income-report'],
                ['label' => 'Expense Report',   'route' => 'tenant.reports.expense-report'],
                ['label' => 'Customer Report',  'route' => 'tenant.reports.customer-report'],
            ],
        ],

    ],

];