<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeviceProvisioningToken;
use App\Services\QrCodeService;
use Illuminate\Http\Request;

class ProvisioningTokenController extends Controller
{
    public function __construct(
        private readonly QrCodeService $qrCodeService
    ) {}

    public function index()
    {
        $tokens = DeviceProvisioningToken::orderBy('created_at', 'desc')->paginate(50);

        return view('admin.provisioning-tokens.index', compact('tokens'));
    }

    public function qrCode(DeviceProvisioningToken $token)
    {
        $qrCodeData = $this->qrCodeService->generateQrCode($token);
        $qrCodeImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $qrCodeData));

        return response($qrCodeImage)->header('Content-Type', 'image/png');
    }

    public function revoke(DeviceProvisioningToken $token)
    {
        $token->status = 'revoked';
        $token->save();

        return back()->with('success', 'Token revoked successfully.');
    }
}