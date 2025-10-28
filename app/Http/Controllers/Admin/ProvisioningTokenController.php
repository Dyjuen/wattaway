<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeviceProvisioningToken;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ProvisioningTokenController extends Controller
{
    public function __construct(
        private readonly QrCodeService $qrCodeService
    ) {}

    public function index(Request $request): View
    {
        $tokens = DeviceProvisioningToken::query()
            ->when($request->input('status'), fn ($query, $status) => $query->where('status', $status))
            ->when($request->input('batch'), fn ($query, $batch) => $query->whereJsonContains('metadata->batch', $batch))
            ->paginate(50);

        return view('admin.provisioning-tokens.index', compact('tokens'));
    }

    public function show(DeviceProvisioningToken $token): View
    {
        return view('admin.provisioning-tokens.show', compact('token'));
    }

    public function generateQr(DeviceProvisioningToken $token): Response
    {
        $qrCode = $this->qrCodeService->generateQrCode($token, 300);
        $qrCode = base64_decode(substr($qrCode, strpos($qrCode, ',') + 1));

        return response($qrCode)->header('Content-Type', 'image/png');
    }

    public function revoke(DeviceProvisioningToken $token): RedirectResponse
    {
        $token->revoke();

        return redirect()->route('admin.provisioning-tokens.index')->with('success', 'Token revoked successfully.');
    }

    public function export(Request $request): Response
    {
        $tokens = DeviceProvisioningToken::query()
            ->when($request->input('status'), fn ($query, $status) => $query->where('status', $status))
            ->when($request->input('batch'), fn ($query, $batch) => $query->whereJsonContains('metadata->batch', $batch))
            ->get();

        $filename = "provisioning_tokens.csv";
        $handle = fopen('php://output', 'w');

        fputcsv($handle, ['Token', 'Serial Number', 'Hardware ID', 'Status', 'Expires At']);

        foreach ($tokens as $token) {
            fputcsv($handle, [
                $token->token,
                $token->serial_number,
                $token->hardware_id,
                $token->status,
                $token->expires_at?->toIso8601String(),
            ]);
        }

        fclose($handle);

        return new Response("", 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
