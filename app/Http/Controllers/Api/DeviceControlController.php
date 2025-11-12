<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Services\MqttPublishService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceControlController extends Controller
{
    protected $mqttPublishService;

    public function __construct(MqttPublishService $mqttPublishService)
    {
        $this->mqttPublishService = $mqttPublishService;
    }

    public function updateRelayState(Request $request, $deviceId)
    {
        $device = Device::find($deviceId);
        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'channel' => 'required|integer|min:1|max:3',
            'state' => 'required|string|in:on,off',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $validated = $validator->validated();
        $channel = $validated['channel'];
        $state = $validated['state'];

        try {
            $success = $this->mqttPublishService->setRelayState($device, $channel, $state);
            if ($success) {
                return response()->json(['success' => true, 'message' => 'Relay state update command sent.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to send command to device.'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send command to device: '.$e->getMessage()], 500);
        }
    }
}
