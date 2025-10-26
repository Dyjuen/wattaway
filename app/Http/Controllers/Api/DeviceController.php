<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Http\Requests\ControlDeviceRequest;
use App\Http\Requests\CreateScheduleRequest;
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

    public function control(ControlDeviceRequest $request, Device $device)
    {
        $validated = $request->validated();

        $this->mqttPublishService->setRelayState($device, $validated['action']);

        AuditLog::log(
            action: 'device.control',
            description: "Device {$device->name} turned {$validated['action']}",
            context: ['device_id' => $device->id, 'new_values' => ['state' => $validated['action']]]
        );

        return response()->json(['message' => 'Command sent to device.']);
    }

    public function setSchedule(CreateScheduleRequest $request, Device $device)
    {
        $validated = $request->validated();

        $schedule = $device->schedules()->create($validated);

        return response()->json(['message' => 'Schedule created.', 'schedule' => $schedule], 201);
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
