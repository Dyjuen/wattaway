<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MqttMessageLog;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $messages = MqttMessageLog::orderBy('created_at', 'desc')->paginate(50);

        return view('admin.messages.index', compact('messages'));
    }
}
