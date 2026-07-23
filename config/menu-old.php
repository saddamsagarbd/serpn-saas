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
            'enabled' => true,
            'items' => [
                ['sub' => [
                    'label' => 'Setup',
                    'icon'  => 'cog', 
                    'enabled' => true,
                    'items' => [
                        [
                            'label' => 'Categories',       
                            'route' => 'tenant.inventory.categories.index',
                            'enabled' => true,
                        ],
                        [
                            'label' => 'Brands',         
                            'route' => 'tenant.inventory.brands',
                            'enabled' => true,
                        ],
                        [
                            'label' => 'Units',            
                            'route' => 'tenant.inventory.units',
                            'enabled' => true,
                        ],
                        [
                            'label' => 'Styles',            
                            'route' => 'tenant.inventory.styles',
                            'enabled' => true,
                        ],
                        [
                            'label' => 'Fabric Spec',            
                            'route' => 'tenant.inventory.fabrics',
                            'enabled' => true,
                        ],
                        [
                            'label' => 'Color Context',            
                            'route' => 'tenant.inventory.color',
                            'enabled' => true,
                        ],
                        [
                            'label' => 'Warehouse',        
                            'route' => 'tenant.inventory.warehouses.index',
                            'enabled' => true,
                        ],
                    ],
                ]],
                [
                    'label' => 'Items', 
                    'icon'  => 'package', 
                    'route' => 'tenant.inventory.items.index',
                    'enabled' => true,
                ],
                ['sub' => [
                    'label' => 'Stock',
                    'icon'  => 'layers',
                    'enabled' => true,
                    'items' => [
                        [
                            'label' => 'Stock Ledger',       
                            'route' => 'tenant.inventory.stock',
                            'enabled' => true,
                        ],
                        [
                            'label' => 'Stock Adjustment',            
                            'route' => 'tenant.inventory.stock.entry',
                            'enabled' => true,
                        ],
                        [
                            'label' => 'Barcode',        
                            'route' => 'tenant.inventory.barcode',
                            'enabled' => true,
                        ],
                    ],
                ]],
            ],
        ],

        'purchase' => [
            'label' => 'Purchase',
            'icon'  => 'shopping-bag',
            'enabled' => true,
            'items' => [
                [
                    'label' => 'Purchase Order',         
                    'route' => 'tenant.purchase.purchase',
                    'enabled' => true,
                ],
                [
                    'label' => 'GRN/MRR',          
                    'route' => 'tenant.purchase.grn',
                    'enabled' => true,
                ],
                [
                    'label' => 'Suppliers',        
                    'route' => 'tenant.purchase.suppliers',
                    'enabled' => true,
                ],
                // ['label' => 'Purchase Return',  'route' => 'purchase.purchase-return'],
            ],
        ],

        'sales' => [
            'label' => 'Sales',
            'icon'  => 'shopping-cart',
            'enabled' => true,
            'items' => [
                [
                    'label' => 'POS',            
                    'route' => 'tenant.sales.pos',
                    'enabled' => true,
                ],
                [
                    'label' => 'Sales',          
                    'route' => 'tenant.sales.sales',
                    'enabled' => true,
                ],
                [
                    'label' => 'Customers',      
                    'route' => 'tenant.sales.customers',
                    'enabled' => true,
                ],
                [
                    'label' => 'Sales Return',   
                    'route' => 'tenant.sales.sales-return',
                    'enabled' => true,
                ],
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
            'enabled' => true,
            'items' => [
                // ['label' => 'Dashboard',          'route' => 'tenant.accounts.dashboard'],
                [
                    'label' => 'Chart of Accounts',  
                    'route' => 'tenant.accounts.coa.index',
                    'enabled' => true,
                ],
                [
                    'label' => 'Income',             
                    'route' => 'tenant.accounts.income',
                    'enabled' => true,
                ],
                [
                    'label' => 'Expense',            
                    'route' => 'tenant.accounts.expense',
                    'enabled' => true,
                ],
                [
                    'label' => 'Transactions',       
                    'route' => 'tenant.accounts.transactions',
                    'enabled' => true,
                ],
                [
                    'label' => 'Ledger',             
                    'route' => 'tenant.accounts.ledger',
                    'enabled' => true,
                ],
                [
                    'label' => 'Cash Book',          
                    'route' => 'tenant.accounts.cash-book',
                    'enabled' => true,
                ],
                [
                    'label' => 'Bank Accounts',      
                    'route' => 'tenant.accounts.bank-accounts',
                    'enabled' => true,
                ],
                [
                    'label' => 'Journal Entry',      
                    'route' => 'tenant.accounts.journal-entry',
                    'enabled' => true,
                ],
                [
                    'label' => 'Trial Balance',      
                    'route' => 'tenant.accounts.trial-balance',
                    'enabled' => true,
                ],
                [
                    'label' => 'Profit & Loss',      
                    'route' => 'tenant.accounts.profit-loss',
                    'enabled' => true,
                ],
                [
                    'label' => 'Balance Sheet',      
                    'route' => 'tenant.accounts.balance-sheet',
                    'enabled' => true,
                ],
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