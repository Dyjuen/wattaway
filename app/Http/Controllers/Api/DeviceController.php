<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeviceResource;
use App\Models\Device;
use App\Services\DeviceDataService;
use App\Services\MqttPublishService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceController extends Controller
{
    public function __construct(
        protected DeviceDataService $deviceDataService,
        protected MqttPublishService $mqttPublishService
    ) {}

    public function index()
    {
        $devices = Auth::user()->account->devices()->withLatestData()->paginate(20);
        return DeviceResource::collection($devices);
    }

    public function show(Device $device)
    {
        $this->authorize('view', $device);
        $device->load('esp32MessageLogs');
        return new DeviceResource($device);
    }

    public function control(Request $request, Device $device)
    {
        $this->authorize('update', $device);

        $validated = $request->validate([
            'state' => 'required|in:on,off',
        ]);

        $this->mqttPublishService->setRelayState($device, $validated['state']);

        return response()->json(['message' => 'Command sent to device.']);
    }

    public function setSchedule(Request $request, Device $device)
    {
        $this->authorize('update', $device);

        $validated = $request->validate([
            'schedule' => 'required|array',
        ]);

        $device->setConfig('schedule', $validated['schedule']);

        return response()->json(['message' => 'Schedule updated.']);
    }

    public function getData(Device $device)
    {
        $this->authorize('view', $device);
        return $this->deviceDataService->getDeviceLatestData($device);
    }

    public function getHistory(Request $request, Device $device)
    {
        $this->authorize('view', $device);

        $validated = $request->validate([
            'hours' => 'sometimes|integer|min:1|max:720',
        ]);

        return $this->deviceDataService->getDeviceDataHistory($device, $validated['hours'] ?? 24);
    }
}
