<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FirmwareVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FirmwareController extends Controller
{
    public function index()
    {
        $firmwareVersions = FirmwareVersion::latest()->paginate(20);
        return view('admin.firmware.index', compact('firmwareVersions'));
    }

    public function create()
    {
        return view('admin.firmware.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'version' => 'required|string|regex:/^\d+\.\d+\.\d+$/|unique:firmware_versions',
            'description' => 'required|string|max:5000',
            'file' => 'required|file|mimes:bin|max:2048',
            'is_stable' => 'boolean',
        ]);

        $path = $request->file('file')->store('firmware');

        FirmwareVersion::create([
            'version' => $request->version,
            'description' => $request->description,
            'file_path' => $path,
            'file_size' => $request->file('file')->getSize(),
            'checksum' => md5_file($request->file('file')->getRealPath()),
            'is_stable' => $request->boolean('is_stable'),
            'released_at' => now(),
        ]);

        return redirect()->route('admin.firmware.index')->with('success', 'Firmware uploaded successfully.');
    }

    public function show(FirmwareVersion $firmware)
    {
        return view('admin.firmware.show', compact('firmware'));
    }

    public function destroy(FirmwareVersion $firmware)
    {
        Storage::delete($firmware->file_path);
        $firmware->delete();

        return redirect()->route('admin.firmware.index')->with('success', 'Firmware deleted successfully.');
    }

    public function download(FirmwareVersion $firmware)
    {
        return Storage::download($firmware->file_path, $firmware->version . '.bin');
    }
}
