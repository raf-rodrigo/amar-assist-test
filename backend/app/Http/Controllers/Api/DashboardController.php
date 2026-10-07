<?php

namespace App\Http\Controllers\Api;

use App\Actions\GetMonthlyDashboard;
use App\Http\Controllers\Controller;
use App\Http\Requests\DashboardRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(DashboardRequest $request, GetMonthlyDashboard $dashboard): JsonResponse
    {
        $month = $request->validated('month');
        $referenceMonth = $month
            ? Carbon::createFromFormat('!Y-m', $month)
            : now();

        return response()->json($dashboard->execute($request->user(), $referenceMonth));
    }
}
