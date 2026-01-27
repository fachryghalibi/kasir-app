<?php

namespace App\Http\Controllers\Api\Boss;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function sales()
    {
        return response()->json([
            'message' => 'Sales Report API - Coming Soon',
        ]);
    }
}