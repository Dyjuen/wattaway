<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceProvisioningToken;
use Illuminate\Http\Request;

use App\Services\QrCodeService;

class DeviceController extends Controller
{
    public function __construct(
        private readonly QrCodeService $qrCodeService
    ) {}

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
        $qrCodeDataUri = null;
        if ($device->provisioningToken) {
            $qrCodeDataUri = $this->qrCodeService->generateQrCode($device->provisioningToken);
        }

        return view('admin.devices.show', compact('device', 'qrCodeDataUri'));
    }
}