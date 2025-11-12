<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Http\Requests\ControlDeviceRequest;
use App\Http\Requests\CreateScheduleRequest;
use App\Http\Requests\UpdateDeviceConfigurationRequest;
use App\Http\Resources\DeviceResource;
use App\Http\Resources\DeviceReadingResource;
use App\Models\Device;
use App\Models\DeviceReading;
use App\Services\DeviceDataService;
use App\Services\MqttPublishService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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

        $this->mqttPublishService->setRelayState($device, $validated['channel'], $validated['action']);

        AuditLog::log(
            action: 'device.control',
            description: "Device {$device->name} channel {$validated['channel']} turned {$validated['action']}",
            context: ['device_id' => $device->id, 'new_values' => ['channel' => $validated['channel'], 'state' => $validated['action']]]
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

    public function readings(Request $request, Device $device)
    {
        $this->authorize('view', $device);

        $range = $request->query('range', '24h'); // Default to 24 hours
        $endDate = now();
        $startDate = match($range) {
            '1h' => now()->subHour(),
            '6h' => now()->subHours(6),
            '24h' => now()->subDay(),
            '7d' => now()->subDays(7),
            default => now()->subDay(),
        };

        $readings = $device->deviceReadings()
                        ->with('channelReadings')
                        ->whereBetween('timestamp', [$startDate, $endDate])
                        ->orderBy('timestamp', 'asc') // Order ascending for charts
                        ->get();

        return DeviceReadingResource::collection($readings);
    }

    public function updateConfiguration(UpdateDeviceConfigurationRequest $request, Device $device, string $type)
    {
        $this->authorize('update', $device);

        $validated = $request->validated();
        $configuration = $validated['configuration'];

        $device->configurations()->updateOrCreate(
            ['type' => $type],
            ['configuration' => $configuration]
        );

        $this->mqttPublishService->publishConfiguration($device, $type, $configuration);

        AuditLog::log(
            action: 'device.configuration.update',
            description: "Device {$device->name} {$type} configuration updated",
            context: ['device_id' => $device->id, 'new_values' => [$type => $configuration]]
        );

        return response()->json(['message' => 'Device configuration updated successfully.']);
    }

    public function getConfiguration(Device $device)
    {
        $this->authorize('view', $device);

        $configurations = $device->configurations->keyBy('type')->map(function ($config) {
            return $config->configuration;
        });

        return response()->json($configurations);
    }
}