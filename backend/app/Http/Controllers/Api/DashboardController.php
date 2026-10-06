<?php

namespace App\Http\Controllers\Api;

use App\Actions\GetMonthlyDashboard;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request, GetMonthlyDashboard $dashboard): JsonResponse
    {
        return response()->json($dashboard->execute($request->user(), now()));
    }
}

