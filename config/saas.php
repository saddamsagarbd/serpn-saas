<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Core Features (সবার জন্য ফ্রি ও ডিফল্ট)
    |--------------------------------------------------------------------------
    | এই ফিচারগুলোর জন্য কোনো চেকবক্সের দরকার নেই, এগুলো সব প্ল্যানেই অন্তর্ভুক্ত থাকবে।
    */
    'default_features' => [
        'inventory_basics' => 'Product Matrix & Core Inventory Management',
        'stock_ledger'     => 'Daily Stock In/Out Tracking',
        'manual_sales'     => 'Manual Invoice & Cash Memo Generation',
        'due_management'   => 'Customer Due Tracker & Ledger Accounts',
        'basic_accounting' => 'Expense Logging & Cashbook Management',
        'dashboard_stats'  => 'Daily Sales, Profit & Loss Overview Charts',
    ],

    /*
    |--------------------------------------------------------------------------
    | Standard / Premium Selectable Features (ডায়নামিক চেকবক্স ফিচার)
    |--------------------------------------------------------------------------
    | এই ফিচারগুলো সুপার অ্যাডমিন প্ল্যান তৈরি করার সময় ডায়নামিকালি সিলেক্ট করতে পারবেন।
    | ভবিষ্যতে নতুন মডিউল বানালে শুধু এখানে এক লাইন যোগ করলেই ফর্মে চলে আসবে।
    */
    'selectable_features' => [
        'barcode'       => 'Barcode Scanning & Automated POS Fast Ledger',
        'website'       => 'Automated E-commerce Live Integrated Website',
        'sms_alert'     => 'Automated SMS Notification & Digital Receipt to Customers',
        'multi_store'   => 'Multi-Warehouse & Multi-Branch Inventory Sync',
        'supplier_mgmt' => 'Advanced Supplier Management & Automated Purchase Orders',
        'hrm_payroll'   => 'Employee Attendance, Role Permissions & Payroll Management',
    ]
];