<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class MqttAuthController extends Controller
{
    public function auth(Request $request)
    {
        Log::debug('MQTT Auth Request', $request->all());

        $deviceId = $request->input('username');
        $apiToken = $request->input('password');

        if (empty($deviceId) || empty($apiToken)) {
            return response()->json(['result' => 'error', 'message' => 'Missing credentials'], 400);
        }

        $device = Device::find($deviceId);

        if (! $device || ! $device->api_token) {
            return response()->json(['result' => 'error', 'message' => 'Device not found or no token'], 404);
        }

        // We are not hashing the token for now, but if we were, it would be like this:
        // if (Hash::check($apiToken, $device->api_token)) {
        //     return response()->json(['result' => 'ok']);
        // }

        if ($apiToken === $device->api_token) {
            return response()->json(['result' => 'ok']);
        }

        return response()->json(['result' => 'error', 'message' => 'Invalid credentials'], 401);
    }

    public function superuser(Request $request)
    {
        Log::debug('MQTT Superuser Request', $request->all());

        // For now, no device is a superuser.
        return response()->json(['result' => 'error', 'message' => 'Not a superuser'], 403);
    }

    public function acl(Request $request)
    {
        Log::debug('MQTT ACL Request', $request->all());

        $deviceId = $request->input('username');
        $topic = $request->input('topic');
        $acc = $request->input('acc'); // 1 for sub, 2 for pub

        $device = Device::find($deviceId);
        if (! $device) {
            return response()->json(['result' => 'error', 'message' => 'Device not found'], 404);
        }

        // Define topics for the device
        $dataTopic = "devices/{$deviceId}/data";
        $commandsTopic = "devices/{$deviceId}/commands";
        $statusTopic = "devices/{$deviceId}/status";

        // Subscription ACL
        if ($acc == 1) { // Subscribe
            if ($topic === $commandsTopic) {
                return response()->json(['result' => 'ok']);
            }
        }

        // Publication ACL
        if ($acc == 2) { // Publish
            if ($topic === $dataTopic || $topic === $statusTopic) {
                return response()->json(['result' => 'ok']);
            }
        }

        Log::warning('MQTT ACL Denied', $request->all());

        return response()->json(['result' => 'error', 'message' => 'ACL denied'], 403);
    }
}
