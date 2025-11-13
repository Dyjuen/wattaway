<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $account = auth()->user();
        $devices = $account->devices()->with('esp32MessageLogs')->get();

        $totalDevices = $devices->count();
        $activeNow = $account->devices()->where('last_seen_at', '>=', now()->subMinutes(10))->count();

        // Correctly calculate energy for this month by joining tables
        $thisMonthEnergy = DB::table('channel_readings')
            ->join('device_readings', 'channel_readings.device_reading_id', '=', 'device_readings.id')
            ->join('devices', 'device_readings.device_id', '=', 'devices.id')
            ->where('devices.account_id', $account->id)
            ->whereMonth('channel_readings.created_at', now()->month)
            ->sum('channel_readings.power');

        // Correctly calculate energy for last month by joining tables
        $lastMonthEnergy = DB::table('channel_readings')
            ->join('device_readings', 'channel_readings.device_reading_id', '=', 'device_readings.id')
            ->join('devices', 'device_readings.device_id', '=', 'devices.id')
            ->where('devices.account_id', $account->id)
            ->whereMonth('channel_readings.created_at', now()->subMonthNoOverflow()->month)
            ->sum('channel_readings.power');

        $recentActivities = $account->auditLogs()->latest()->take(5)->get();
        $devicesChange = $account->devices()->where('created_at', '>=', now()->subDays(30))->count();

        $energyChange = 0;
        if ($lastMonthEnergy > 0) {
            $energyChange = (($thisMonthEnergy - $lastMonthEnergy) / $lastMonthEnergy) * 100;
        }

        // Calculate Peak Power in the last 24 hours
        $peakPower = DB::table('channel_readings')
            ->join('device_readings', 'channel_readings.device_reading_id', '=', 'device_readings.id')
            ->join('devices', 'device_readings.device_id', '=', 'devices.id')
            ->where('devices.account_id', $account->id)
            ->where('channel_readings.created_at', '>=', now()->subDay())
            ->max('channel_readings.power');

        // Data for Energy Usage Chart
        $energyUsage = DB::table('channel_readings')
            ->join('device_readings', 'channel_readings.device_reading_id', '=', 'device_readings.id')
            ->join('devices', 'device_readings.device_id', '=', 'devices.id')
            ->where('devices.account_id', $account->id)
            ->where('channel_readings.created_at', '>=', now()->subDay())
            ->select(
                DB::raw('SUM(channel_readings.power) as total_power'),
                DB::raw("DATE_FORMAT(channel_readings.created_at, '%H:00') as hour")
            )
            ->groupBy('hour')
            ->orderBy('hour', 'asc')
            ->get();

        $energyUsageData = [
            'labels' => $energyUsage->pluck('hour'),
            'data' => $energyUsage->pluck('total_power'),
        ];

        // This variable is no longer used in the view but is kept to avoid breaking other parts if they depend on it.
        $totalEnergySaved = 0;

        return view('dashboard', compact(
            'devices',
            'totalDevices',
            'activeNow',
            'totalEnergySaved',
            'thisMonthEnergy',
            'recentActivities',
            'devicesChange',
            'energyChange',
            'energyUsageData',
            'peakPower'
        ));
    }

    public function show(Device $device)
    {
        $this->authorize('view', $device);

        $latestReading = $device->deviceReadings()->with('channelReadings')->latest('timestamp')->first();

        return view('devices.show', compact('device', 'latestReading'));
    }
}
