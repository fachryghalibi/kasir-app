<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cash Flow Approval Threshold
    |--------------------------------------------------------------------------
    |
    | Transaksi dengan nominal di atas threshold ini akan otomatis
    | ditandai untuk review. Boss dapat mengubah nilai ini sesuai kebutuhan.
    |
    */
    'approval_threshold' => env('CASHFLOW_APPROVAL_THRESHOLD', 5000000), // Default: 5 juta

    /*
    |--------------------------------------------------------------------------
    | Auto Approve Small Transactions
    |--------------------------------------------------------------------------
    |
    | Jika true, transaksi di bawah threshold akan otomatis approved.
    | Jika false, semua transaksi butuh manual approval.
    |
    */
    'auto_approve_small_transactions' => env('CASHFLOW_AUTO_APPROVE', true),

    /*
    |--------------------------------------------------------------------------
    | Review Period (Days)
    |--------------------------------------------------------------------------
    |
    | Berapa lama transaksi pending dapat direview sebelum dihapus otomatis.
    | Default: 30 hari
    |
    */
    'review_period_days' => env('CASHFLOW_REVIEW_PERIOD', 30),
];