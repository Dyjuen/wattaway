<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DeviceProvisioningToken;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    public function generateQrCode(DeviceProvisioningToken $token, int $size = 300): string
    {
        $qrCode = QrCode::format('png')
            ->size($size)
            ->errorCorrection('H')
            ->generate($token->getQrCodeUrl());

        return 'data:image/png;base64,'.base64_encode((string) $qrCode);
    }

    public function generatePrintableLabel(DeviceProvisioningToken $token): string
    {
        $qrCode = $this->generateQrCode($token);

        return view('admin.provisioning-tokens.label', [
            'token' => $token,
            'qrCode' => $qrCode,
        ])->render();
    }
}
