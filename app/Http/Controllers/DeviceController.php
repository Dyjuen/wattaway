<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = auth()->user()->devices;
        return view('dashboard', compact('devices'));
    }

    public function show(Device $device)
    {
        $this->authorize('view', $device);
        return view('devices.show', compact('device'));
    }
}