<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\MqttMessageLog;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_devices' => Device::count(),
            'online_devices' => Device::where('status', 'online')->count(),
            'messages_today' => MqttMessageLog::whereDate('created_at', today())->count(),
            'errors_today' => MqttMessageLog::whereDate('created_at', today())->where('status', 'error')->count(),
        ];

        $recentMessages = MqttMessageLog::with('device')->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recentMessages'));
    }

    public function messages(Request $request)
    {
        $query = MqttMessageLog::with('device')->latest();

        if ($request->filled('device_id')) {
            $query->where('device_id', $request->device_id);
        }
        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $messages = $query->paginate(50);
        $devices = Device::all();

        return view('admin.messages.index', compact('messages', 'devices'));
    }

    public function showMessage(MqttMessageLog $message)
    {
        return view('admin.messages.show', compact('message'));
    }

    public function devices()
    {
        $devices = Device::latest()->paginate(50);
        return view('admin.devices.index', compact('devices'));
    }
}
