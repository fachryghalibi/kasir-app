<?php

namespace App\Http\Controllers\Api\Boss;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats()
    {
        return response()->json([
            'message' => 'Boss Dashboard API - Coming Soon',
        ]);
    }
}