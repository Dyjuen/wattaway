<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FirmwareVersion;
use App\Services\MqttPublishService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FirmwareController extends Controller
{
    public function index()
    {
        $firmwares = FirmwareVersion::orderBy('created_at', 'desc')->get();
        return view('admin.firmware.index', compact('firmwares'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'version' => 'required|string|unique:firmware_versions,version',
            'description' => 'nullable|string',
            'firmware_file' => 'required|file|mimes:bin',
        ]);

        $file = $request->file('firmware_file');
        $version = $request->input('version');
        $fileName = 'esp32-firmware-v' . Str::slug($version) . '.bin';
        $path = $file->storeAs('firmware', $fileName, 'public');

        FirmwareVersion::create([
            'version' => $version,
            'description' => $request->input('description'),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'checksum' => md5_file($file->getRealPath()),
        ]);

        return back()->with('success', 'Firmware uploaded successfully.');
    }

    public function download(FirmwareVersion $firmware)
    {
        return Storage::disk('public')->download($firmware->file_path);
    }

    public function destroy(FirmwareVersion $firmware)
    {
        // Delete the file from storage
        if (Storage::disk('public')->exists($firmware->file_path)) {
            Storage::disk('public')->delete($firmware->file_path);
        }

        // Delete the database record
        $firmware->delete();

        return back()->with('success', 'Firmware version ' . $firmware->version . ' deleted successfully.');
    }

    public function triggerOtaUpdate(MqttPublishService $mqttService)
    {
        $devices = \App\Models\Device::all();
        $successCount = 0;

        foreach ($devices as $device) {
            if ($mqttService->triggerOtaCheck($device)) {
                $successCount++;
            }
        }

        return back()->with('success', "OTA update command sent to {$successCount} out of " . count($devices) . ' devices.');
    }
}