<?php

/**
 * =============================================================================
 * MULTI-TENANT MODULE CONFIG
 * =============================================================================
 * Layer model:
 *   1. CORE        -> business_types: ['*']            always available
 *   2. VERTICAL    -> business_types: ['merchandising'] etc.  shown only to that type
 *   3. PLAN/FEATURE-> 'enabled' + tied to saas.php plan gating (unchanged from
 *                     your existing pattern — independent of business_type)
 *
 * A tenant's `business_type` column drives what MenuResolver returns.
 * Supported business_types used below:
 *   'merchandising'        - buying houses, garment factories, exporters
 *   'real_estate'     - developers, brokers, property management
 *   'manufacturing'   - general production houses (non-garment)
 *   'general_retail'  - generic trading/retail (no vertical extras)
 *
 * '*' on an item/group means "visible to every business_type".
 * =============================================================================
 */

return [

    // -------------------------------------------------------------------
    // Always-visible links, no dropdown, no business_type gating
    // -------------------------------------------------------------------
    'default_features' => [
        // ['label' => 'Dashboard', 'icon' => 'home',     'route' => 'tenant.dashboard'],
        ['label' => 'Profile',   'icon' => 'user',     'route' => 'tenant.profile'],
        ['label' => 'Settings',  'icon' => 'settings', 'route' => 'tenant.settings'],
    ],

    'menus' => [

        // =====================================================================
        // CORE — INVENTORY  (shared skeleton; vertical-only leaves tagged)
        // =====================================================================
        'inventory' => [
            'label' => 'Inventory',
            'icon' => 'package',
            'enabled' => true,
            'business_types' => ['*'],
            'items' => [
                ['sub' => [
                    'label' => 'Setup',
                    'icon' => 'cog',
                    'enabled' => true,
                    'business_types' => ['*'],
                    'items' => [
                        ['label' => 'Categories',      'route' => 'tenant.inventory.categories.index', 'enabled' => true, 'business_types' => ['*']],
                        ['label' => 'Brands',          'route' => 'tenant.inventory.brands',            'enabled' => true, 'business_types' => ['*']],
                        ['label' => 'Units',           'route' => 'tenant.inventory.units',             'enabled' => true, 'business_types' => ['*']],
                        ['label' => 'Warehouse',       'route' => 'tenant.inventory.warehouses.index',  'enabled' => true, 'business_types' => ['*']],

                        // Garment-only setup items
                        ['label' => 'Styles',          'route' => 'tenant.inventory.styles',            'enabled' => true, 'business_types' => ['merchandising']],
                        ['label' => 'Size Charts',      'route' => 'tenant.inventory.units',       'enabled' => true, 'business_types' => ['merchandising']],
                        ['label' => 'Fabric Spec',      'route' => 'tenant.inventory.fabrics',           'enabled' => true, 'business_types' => ['merchandising']],
                        ['label' => 'Color Context',    'route' => 'tenant.inventory.color',             'enabled' => true, 'business_types' => ['merchandising']],

                        // Manufacturing-only setup items
                        ['label' => 'Raw Materials',    'route' => 'tenant.inventory.raw-materials',     'enabled' => true, 'business_types' => ['manufacturing']],
                        ['label' => 'Bill of Materials (BOM)', 'route' => 'tenant.inventory.bom',        'enabled' => true, 'business_types' => ['manufacturing', 'merchandising']],
                    ],
                ]],
                [
                    'label' => 'Items',
                    'icon' => 'package',
                    'route' => 'tenant.inventory.items.index',
                    'enabled' => true,
                    'business_types' => ['merchandising', 'manufacturing', 'general_retail'],
                ],
                ['sub' => [
                    'label' => 'Stock',
                    'icon' => 'layers',
                    'enabled' => true,
                    'business_types' => ['merchandising', 'manufacturing', 'general_retail'],
                    'items' => [
                        ['label' => 'Stock Ledger',      'route' => 'tenant.inventory.stock',       'enabled' => true, 'business_types' => ['*']],
                        ['label' => 'Stock Adjustment',  'route' => 'tenant.inventory.stock.entry', 'enabled' => true, 'business_types' => ['*']],
                        ['label' => 'Stock Transfer',    'route' => 'tenant.inventory.stock.transfer', 'enabled' => true, 'business_types' => ['*']],
                        ['label' => 'Barcode',           'route' => 'tenant.inventory.barcode',     'enabled' => true, 'business_types' => ['merchandising', 'general_retail']],
                    ],
                ]],
            ],
        ],

        // =====================================================================
        // GARMENT VERTICAL — Merchandising
        // =====================================================================
        'merchandising' => [
            'label' => 'Merchandising',
            'icon' => 'scissors',
            'enabled' => true,
            'business_types' => ['merchandising'],
            'items' => [
                ['label' => 'Buyers',                'route' => 'tenant.merch.buyers',            'enabled' => true],
                ['label' => 'Styles & Seasons',      'route' => 'tenant.merch.styles',            'enabled' => true],
                ['label' => 'Costing Sheet',         'route' => 'tenant.merch.costing',           'enabled' => true],
                ['label' => 'Costing Approval',      'route' => 'tenant.merch.costing.approval',  'enabled' => true],
                ['label' => 'MPR (Material Req.)',   'route' => 'tenant.merch.mpr',               'enabled' => true],
                ['label' => 'Booking / Sourcing',    'route' => 'tenant.merch.booking',           'enabled' => true],
                ['label' => 'TNA (Time & Action)',   'route' => 'tenant.merch.tna',               'enabled' => true],
                ['label' => 'Sample Tracking',       'route' => 'tenant.merch.samples',           'enabled' => true],
                ['label' => 'Shipment / L-C Docs',   'route' => 'tenant.merch.shipment-docs',     'enabled' => true],
            ],
        ],

        // =====================================================================
        // GARMENT / MANUFACTURING — Production Floor
        // =====================================================================
        'production' => [
            'label' => 'Production',
            'icon' => 'activity',
            'enabled' => true,
            'business_types' => ['merchandising', 'manufacturing'],
            'items' => [
                ['label' => 'Work Orders',           'route' => 'tenant.production.work-orders',  'enabled' => true, 'business_types' => ['*']],
                ['label' => 'Cutting',                'route' => 'tenant.production.cutting',      'enabled' => true, 'business_types' => ['merchandising']],
                ['label' => 'Sewing / Line Output',   'route' => 'tenant.production.sewing',       'enabled' => true, 'business_types' => ['merchandising']],
                ['label' => 'Assembly / Line Output', 'route' => 'tenant.production.assembly',     'enabled' => true, 'business_types' => ['manufacturing']],
                ['label' => 'Finishing & Packing',    'route' => 'tenant.production.finishing',    'enabled' => true, 'business_types' => ['*']],
                ['label' => 'QC / Inspection',        'route' => 'tenant.production.qc',           'enabled' => true, 'business_types' => ['*']],
                ['label' => 'Machines / Lines',       'route' => 'tenant.production.machines',     'enabled' => true, 'business_types' => ['manufacturing']],
                ['label' => 'Daily Production Report','route' => 'tenant.production.daily-report', 'enabled' => true, 'business_types' => ['*']],
            ],
        ],

        // =====================================================================
        // REAL ESTATE VERTICAL
        // =====================================================================
        'real_estate' => [
            'label' => 'Properties',
            'icon' => 'building',
            'enabled' => true,
            'business_types' => ['real_estate'],
            'items' => [
                ['label' => 'Projects',              'route' => 'tenant.re.projects',            'enabled' => true],
                ['label' => 'Properties / Units',    'route' => 'tenant.re.units',               'enabled' => true],
                ['label' => 'Renters / Occupants',   'route' => 'tenant.re.occupants',           'enabled' => true],
                ['label' => 'Bookings',              'route' => 'tenant.re.bookings',            'enabled' => true],
                ['label' => 'Lease Agreements',      'route' => 'tenant.re.leases',              'enabled' => true],
                ['label' => 'Rent Collection',       'route' => 'tenant.re.rent-collection',     'enabled' => true],
                ['label' => 'Installment Plans',     'route' => 'tenant.re.installments',        'enabled' => true],
                ['label' => 'Maintenance Requests',  'route' => 'tenant.re.maintenance',         'enabled' => true],
                ['label' => 'Broker / Commission',   'route' => 'tenant.re.broker-commission',   'enabled' => true],
            ],
        ],

        // =====================================================================
        // CORE — PURCHASE  (generic; vertical labels swapped by resolver if needed)
        // =====================================================================
        'purchase' => [
            'label' => 'Purchase',
            'icon' => 'shopping-bag',
            'enabled' => true,
            'business_types' => ['merchandising', 'manufacturing', 'general_retail'],
            'items' => [
                ['label' => 'Purchase Order',   'route' => 'tenant.purchase.purchase',    'enabled' => true, 'business_types' => ['*']],
                ['label' => 'GRN / MRR',        'route' => 'tenant.purchase.grn',         'enabled' => true, 'business_types' => ['*']],
                ['label' => 'Suppliers',        'route' => 'tenant.purchase.suppliers',   'enabled' => true, 'business_types' => ['*']],
                ['label' => 'Purchase Return',  'route' => 'tenant.purchase.return',      'enabled' => true, 'business_types' => ['*']],
                ['label' => 'Fabric/Trim Booking', 'route' => 'tenant.purchase.fabric-booking', 'enabled' => true, 'business_types' => ['merchandising']],
            ],
        ],

        // =====================================================================
        // CORE — SALES  (real estate uses its own booking/lease flow instead)
        // =====================================================================
        'sales' => [
            'label' => 'Sales',
            'icon' => 'shopping-cart',
            'enabled' => true,
            'business_types' => ['merchandising', 'manufacturing', 'general_retail'],
            'items' => [
                ['label' => 'POS',            'route' => 'tenant.sales.pos',            'enabled' => true, 'business_types' => ['general_retail']],
                ['label' => 'Sales Orders',   'route' => 'tenant.sales.sales',          'enabled' => true, 'business_types' => ['*']],
                ['label' => 'Export Invoice', 'route' => 'tenant.sales.export-invoice', 'enabled' => true, 'business_types' => ['merchandising']],
                ['label' => 'Customers',      'route' => 'tenant.sales.customers',      'enabled' => true, 'business_types' => ['*']],
                ['label' => 'Sales Return',   'route' => 'tenant.sales.sales-return',   'enabled' => true, 'business_types' => ['*']],
                ['label' => 'Quotation',      'route' => 'tenant.sales.quotation',      'enabled' => true, 'business_types' => ['*']],
            ],
        ],

        // =====================================================================
        // CORE — ACCOUNTS  (identical for every business type)
        // =====================================================================
        'accounts' => [
            'label' => 'Accounts',
            'icon' => 'calculator',
            'enabled' => true,
            'business_types' => ['*'],
            'items' => [
                ['label' => 'Chart of Accounts', 'route' => 'tenant.accounts.coa.index',    'enabled' => true],
                ['label' => 'Income',            'route' => 'tenant.accounts.income',       'enabled' => true],
                ['label' => 'Expense',           'route' => 'tenant.accounts.expense',      'enabled' => true],
                ['label' => 'Transactions',      'route' => 'tenant.accounts.transactions', 'enabled' => true],
                ['label' => 'Ledger',            'route' => 'tenant.accounts.ledger',       'enabled' => true],
                ['label' => 'Cash Book',         'route' => 'tenant.accounts.cash-book',    'enabled' => true],
                ['label' => 'Bank Accounts',     'route' => 'tenant.accounts.bank-accounts','enabled' => true],
                ['label' => 'Journal Entry',     'route' => 'tenant.accounts.journal-entry','enabled' => true],
                ['label' => 'Trial Balance',     'route' => 'tenant.accounts.trial-balance','enabled' => true],
                ['label' => 'Profit & Loss',     'route' => 'tenant.accounts.profit-loss',  'enabled' => true],
                ['label' => 'Balance Sheet',     'route' => 'tenant.accounts.balance-sheet','enabled' => true],
            ],
        ],

        // =====================================================================
        // CORE — HRM  (available to all, optional per plan)
        // =====================================================================
        'hrm' => [
            'label' => 'HRM',
            'icon' => 'users',
            'enabled' => false,
            'business_types' => ['*'],
            'items' => [
                ['label' => 'Employees',    'route' => 'tenant.hrm.employees',    'enabled' => false],
                ['label' => 'Departments',  'route' => 'tenant.hrm.departments',  'enabled' => false],
                ['label' => 'Designation',  'route' => 'tenant.hrm.designation',  'enabled' => false],
                ['label' => 'Attendance',   'route' => 'tenant.hrm.attendance',   'enabled' => false],
                ['label' => 'Leave',        'route' => 'tenant.hrm.leave',        'enabled' => false],
                ['label' => 'Payroll',      'route' => 'tenant.hrm.payroll',      'enabled' => false],

                // Garment-specific: piece-rate/line-based wage tracking
                ['label' => 'Line Wage / Piece Rate', 'route' => 'tenant.hrm.piece-rate', 'enabled' => false, 'business_types' => ['merchandising']],
            ],
        ],

        // =====================================================================
        // CORE — CRM  (optional, all verticals)
        // =====================================================================
        'crm' => [
            'label' => 'CRM',
            'icon' => 'contact',
            'enabled' => false,
            'business_types' => ['*'],
            'items' => [
                ['label' => 'Leads',      'route' => 'tenant.crm.leads',      'enabled' => false],
                ['label' => 'Follow Up',  'route' => 'tenant.crm.follow-up',  'enabled' => false],

                // Real estate specific: site-visit + inquiry pipeline
                ['label' => 'Site Visits', 'route' => 'tenant.crm.site-visits', 'enabled' => false, 'business_types' => ['real_estate']],
            ],
        ],

        // =====================================================================
        // CORE — REPORTS  (mostly shared, some vertical-only)
        // =====================================================================
        'reports' => [
            'label' => 'Reports',
            'icon' => 'bar-chart-2',
            'enabled' => true,
            'business_types' => ['*'],
            'items' => [
                ['label' => 'Sales Report',      'route' => 'tenant.reports.sales-report',     'enabled' => true, 'business_types' => ['*']],
                ['label' => 'Purchase Report',   'route' => 'tenant.reports.purchase-report',  'enabled' => true, 'business_types' => ['*']],
                ['label' => 'Stock Report',      'route' => 'tenant.reports.stock-report',     'enabled' => true, 'business_types' => ['merchandising', 'manufacturing', 'general_retail']],
                ['label' => 'Income Report',     'route' => 'tenant.reports.income-report',    'enabled' => true, 'business_types' => ['*']],
                ['label' => 'Expense Report',    'route' => 'tenant.reports.expense-report',   'enabled' => true, 'business_types' => ['*']],
                ['label' => 'Customer Report',   'route' => 'tenant.reports.customer-report',  'enabled' => true, 'business_types' => ['*']],

                // Garment-specific
                ['label' => 'Style Profitability', 'route' => 'tenant.reports.style-profitability', 'enabled' => true, 'business_types' => ['merchandising']],
                ['label' => 'Export Statement',    'route' => 'tenant.reports.export-statement',     'enabled' => true, 'business_types' => ['merchandising']],

                // Real estate specific
                ['label' => 'Rent Collection Report', 'route' => 'tenant.reports.rent-collection', 'enabled' => true, 'business_types' => ['real_estate']],
                ['label' => 'Occupancy Report',       'route' => 'tenant.reports.occupancy',        'enabled' => true, 'business_types' => ['real_estate']],
            ],
        ],

    ],

];