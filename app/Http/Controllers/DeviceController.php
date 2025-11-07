<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DeviceController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $account = auth()->user();
        $devices = $account->devices()->with('esp32MessageLogs')->get();

        $totalDevices = $devices->count();
        $activeNow = $devices->where('status', 'online')->count();

        $totalEnergySaved = $devices->reduce(function ($carry, $device) {
            return $carry + $device->esp32MessageLogs->sum('energy');
        }, 0);

        $thisMonthEnergy = $devices->reduce(function ($carry, $device) {
            return $carry + $device->esp32MessageLogs()->whereMonth('created_at', now()->month)->sum('energy');
        }, 0);

        $recentActivities = $account->auditLogs()->latest()->take(5)->get();

        // Calculate change over time
        $devicesChange = $account->devices()->where('created_at', '>=', now()->subDays(30))->count();

        $lastMonthEnergy = $devices->reduce(function ($carry, $device) {
            return $carry + $device->esp32MessageLogs()->whereMonth('created_at', now()->subMonth()->month)->sum('energy');
        }, 0);

        $energyChange = 0;
        if ($lastMonthEnergy > 0) {
            $energyChange = (($thisMonthEnergy - $lastMonthEnergy) / $lastMonthEnergy) * 100;
        }

        return view('dashboard', compact(
            'devices',
            'totalDevices',
            'activeNow',
            'totalEnergySaved',
            'thisMonthEnergy',
            'recentActivities',
            'devicesChange',
            'energyChange'
        ));
    }

    public function show(Device $device)
    {
        $this->authorize('view', $device);
        return view('devices.show', compact('device'));
    }
}