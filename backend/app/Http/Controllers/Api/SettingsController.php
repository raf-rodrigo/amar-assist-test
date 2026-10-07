<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingsRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'reminder_time' => substr((string) $request->user()->reminder_time, 0, 5),
        ]);
    }

    public function update(UpdateSettingsRequest $request): JsonResponse
    {
        $request->user()->update($request->validated());

        return response()->json([
            'reminder_time' => substr((string) $request->user()->refresh()->reminder_time, 0, 5),
            'message' => 'Configurações salvas com sucesso.',
        ]);
    }
}
