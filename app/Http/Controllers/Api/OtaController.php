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

        if (! $currentVersion) {
            return response()->json(['message' => 'x-firmware-version header is required.'], 400);
        }

        $stableFirmwares = FirmwareVersion::stable()->get();

        if ($stableFirmwares->isEmpty()) {
            return response()->json(['update_available' => false]);
        }

        // Manually find the latest version to ensure correct semantic version comparison
        $latestStableFirmware = null;
        foreach ($stableFirmwares as $firmware) {
            if ($latestStableFirmware === null || version_compare($firmware->version, $latestStableFirmware->version, '>')) {
                $latestStableFirmware = $firmware;
            }
        }

        if (! $latestStableFirmware || ! $latestStableFirmware->isNewerThan($currentVersion)) {
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
        if (! $request->hasValidSignature()) {
            abort(401);
        }

        // The download route is not authenticated, so we cannot get a device from the request.
        // The security is handled by the signed URL's signature.
        // Log::info("A device is downloading firmware version {$firmware->version}");
        // $device->update(['firmware_version' => $firmware->version]);

        return Storage::download($firmware->file_path, $firmware->version.'.bin');
    }
}
