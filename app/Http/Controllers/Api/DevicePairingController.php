<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Services\DevicePairingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DevicePairingController extends Controller
{
    public function __construct(
        private readonly DevicePairingService $pairingService
    ) {}

    public function validateToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'size:15', 'regex:/^WS-[A-Z0-9]{12}$/'],
        ]);

        $result = $this->pairingService->validateToken($validated['token']);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        return response()->json($result);
    }

    public function pairDevice(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'size:15', 'regex:/^WS-[A-Z0-9]{12}$/'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            $device = $this->pairingService->pairDevice(Auth::user(), $validated['token'], $validated['device_name']);

            return response()->json([
                'success' => true,
                'message' => 'Device paired successfully',
                'data' => [
                    'id' => $device->id,
                    'name' => $device->name,
                    'serial_number' => $device->serial_number,
                    'status' => $device->status,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function unpairDevice(Request $request, Device $device): JsonResponse
    {
        $this->authorize('update', $device);

        try {
            $this->pairingService->unpairDevice($device);

            return response()->json([
                'success' => true,
                'message' => 'Device unpaired successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
