<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FirmwareVersion;
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
        ]);

        return back()->with('success', 'Firmware uploaded successfully.');
    }

    public function download(FirmwareVersion $firmware)
    {
        return Storage::disk('public')->download($firmware->file_path);
    }
}