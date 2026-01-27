<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Store transaction (akan diimplementasi nanti)
     */
    public function store(Request $request)
    {
        return response()->json([
            'message' => 'Transaction API - Coming Soon',
        ]);
    }
}