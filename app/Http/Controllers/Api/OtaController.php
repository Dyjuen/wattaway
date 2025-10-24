<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FirmwareVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class OtaController extends Controller
{
    public function checkUpdate(Request $request)
    {
        $device = $request->user();
        $currentVersion = $request->header('x-firmware-version');

        $latestStableFirmware = FirmwareVersion::stable()->latest()->first();

        if (!$latestStableFirmware || !$latestStableFirmware->isNewerThan($currentVersion)) {
            return response()->json(['update_available' => false]);
        }

        return response()->json([
            'update_available' => true,
            'version' => $latestStableFirmware->version,
            'size' => $latestStableFirmware->file_size,
            'checksum' => $latestStableFirmware->checksum,
            'download_url' => URL::temporarySignedRoute(
                'api.ota.download', now()->addMinutes(5), ['firmware' => $latestStableFirmware->id]
            ),
            'release_notes' => $latestStableFirmware->description,
        ]);
    }

    public function downloadFirmware(Request $request, FirmwareVersion $firmware)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }

        $device = $request->user();
        Log::info("Device {$device->id} is downloading firmware version {$firmware->version}");

        $device->update(['firmware_version' => $firmware->version]);

        return Storage::download($firmware->file_path, $firmware->version . '.bin');
    }
}
