<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\MqttMessageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_devices' => Device::count(),
            'online_devices' => Device::where('status', 'online')->count(),
            'messages_today' => MqttMessageLog::whereDate('created_at', Carbon::today())->count(),
            'errors_today' => MqttMessageLog::whereDate('created_at', Carbon::today())->where('status', 'error')->count(),
        ];

        $recentMessages = MqttMessageLog::with('device')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentMessages'));
    }
}
