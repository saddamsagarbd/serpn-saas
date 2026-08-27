<?php

return [

    'default_features' => [
        'Profile',
        'Company',
        'Department',
        'Designation',
    ],

    'employee' => [
        'Employee',
        'Categories',
        'Brands',
        'Units',
        'Stock',
        'Stock Adjustment',
        'Barcode',
    ],

    'merchandising' => [
        'Styles',
        'MPR Order',
        'Costing Sheet',
        'Costing Approval',
        'Order & Matrix Entry',
        'Booking / Sourcing',
        'NA (Time & Action)',
        'Sample Tracking',
        'Shipment / L-C Docs',
    ],
    'production' => [
        'Work Orders',
        'Cutting',
        'Sewing / Line Output',
        'Assembly / Line Output',
        'Finishing & Packing',
        'QC / Inspection',
        'Machines / Lines',
        'Daily Production Report',
    ],
    'inventory' => [
        'Products',
        'Categories',
        'Brands',
        'Units',
        'Stock',
        'Stock Adjustment',
        'Barcode',
    ],

    'sales' => [
        'POS',
        'Sales',
        'Customers',
        'Sales Return',
        'Quotation',
    ],

    'purchase' => [
        'Purchase',
        'Suppliers',
        'Purchase Return',
    ],

    'accounts' => [
        'Dashboard',
        'Income',
        'Expense',
        'Transactions',
        'Chart of Accounts',
        'Cash Book',
        'Bank Accounts',
        'Ledger',
        'Journal Entry',
        'Trial Balance',
        'Profit & Loss',
        'Balance Sheet',
    ],

    'hrm' => [
        'Employees',
        'Departments',
        'Designation',
        'Attendance',
        'Leave',
        'Payroll',
    ],

    'crm' => [
        'Customers',
        'Leads',
        'Follow Up',
    ],

    'website' => [
        'Website',
        'Pages',
        'Blogs',
    ],

    'sms' => [
        'SMS',
        'Templates',
    ],

    'reports' => [
        'Sales Report',
        'Purchase Report',
        'Stock Report',
        'Income Report',
        'Expense Report',
        'Customer Report',
    ],

    /**
     * Used to build the "Enable Core Modules" checkbox list
     * (see config('saas.selectable_features') in the features blade).
     * 'default_features' is intentionally excluded here since it
     * already has its own dedicated checkbox above the loop.
     */

    'selectable_features' => [
        'inventory' => 'Inventory',
        'sales'     => 'Sales',
        'purchase'  => 'Purchase',
        'accounts'  => 'Accounts',
        'hrm'       => 'HRM',
        'crm'       => 'CRM',
        'website'   => 'Website',
        'sms'       => 'SMS',
        'reports'   => 'Reports',
    ],

];