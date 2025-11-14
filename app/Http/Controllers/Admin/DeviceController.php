<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceProvisioningToken;
use App\Services\QrCodeService;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function __construct(
        private readonly QrCodeService $qrCodeService
    ) {}

    public function index()
    {
        $devices = Device::orderBy('created_at', 'desc')->paginate(50);

        return view('admin.devices.index', compact('devices'));
    }

    public function create()
    {
        return view('admin.devices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255', 'unique:devices,serial_number'],
            'hardware_id' => ['required', 'string', 'max:255', 'unique:devices,hardware_id'],
        ]);

        $token = DeviceProvisioningToken::generate($validated['serial_number'], $validated['hardware_id']);

        $device = Device::create([
            'name' => $validated['name'],
            'serial_number' => $validated['serial_number'],
            'hardware_id' => $validated['hardware_id'],
            'provisioning_token_id' => $token->id,
            'account_id' => null,
        ]);

        return redirect()->route('admin.devices.show', $device);
    }

    public function show(Device $device)
    {
        $token = $device->provisioningToken;

        if (! $token) {
            $token = DeviceProvisioningToken::generate($device->serial_number, $device->hardware_id);
            $device->update(['provisioning_token_id' => $token->id]);
            $device->load('provisioningToken'); // Reload the relationship
        }

        $qrCodeDataUri = $this->qrCodeService->generateQrCode($device->provisioningToken);

        return view('admin.devices.show', compact('device', 'qrCodeDataUri'));
    }
}
