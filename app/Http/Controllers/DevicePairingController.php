<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DevicePairingService;
use Illuminate\Support\Facades\Auth;

class DevicePairingController extends Controller
{
    public function __construct(private readonly DevicePairingService $pairingService) {}

    public function handlePublicScan(Request $request, $token)
    {
        // Basic validation for the token format
        if (!preg_match('/^WS-[A-Z0-9]{12}$/', $token)) {
            return redirect()->route('login')->with('error', 'Invalid provisioning token format.');
        }

        session(['provisioning_token' => $token]);

        return redirect()->route('login')->with('status', 'Please log in or register to pair your device.');
    }

    public function showConfirmPairing(Request $request)
    {
        $token = $request->query('token');

        if (!$token || !preg_match('/^WS-[A-Z0-9]{12}$/', $token)) {
            return redirect()->route('dashboard')->with('error', 'Invalid or missing provisioning token.');
        }

        return view('pairing.confirm', ['token' => $token]);
    }

    public function pairDevice(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'size:15', 'regex:/^WS-[A-Z0-9]{12}$/'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            $this->pairingService->pairDevice(Auth::user(), $validated['token'], $validated['device_name']);

            return redirect()->route('dashboard')->with('status', 'Device paired successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
