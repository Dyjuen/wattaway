<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\AuditLog;
use App\Models\Device;
use App\Models\DeviceProvisioningToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DevicePairingService
{
    public function __construct(
        private readonly AuditLog $auditLog,
    ) {}

    public function validateToken(string $token): array
    {
        $token = DeviceProvisioningToken::where('token', $token)->first();

        if (! $token) {
            return ['success' => false, 'error' => 'Token not found'];
        }

        if ($token->isExpired()) {
            $token->update(['status' => 'expired']);

            return ['success' => false, 'error' => 'Token has expired'];
        }

        if ($token->isPaired()) {
            return ['success' => false, 'error' => 'Token has already been used', 'paired_at' => $token->paired_at];
        }

        if ($token->status === 'revoked') {
            return ['success' => false, 'error' => 'Token has been revoked'];
        }

        return [
            'success' => true,
            'device_info' => [
                'serial_number' => $token->serial_number,
                'manufacturing_date' => $token->metadata['manufacturing_date'] ?? null,
                'batch' => $token->metadata['batch'] ?? null,
            ],
        ];
    }

    public function pairDevice(Account $account, string $token, ?string $customName = null): Device
    {
        return DB::transaction(function () use ($account, $token, $customName) {
            $provisioningToken = DeviceProvisioningToken::where('token', $token)->pending()->first();

            if (! $provisioningToken) {
                throw new \Exception('Invalid or already used token.');
            }

            if ($provisioningToken->isExpired()) {
                throw new \Exception('Expired token.');
            }

            $device = Device::updateOrCreate(
                ['serial_number' => $provisioningToken->serial_number],
                [
                    'account_id' => $account->id,
                    'provisioning_token_id' => $provisioningToken->id,
                    'name' => $customName ?? 'Smart Socket '.$provisioningToken->serial_number,
                    'hardware_id' => $provisioningToken->hardware_id,
                    'status' => 'pending_activation',
                    'api_token' => Str::random(64),
                ]
            );

            $provisioningToken->markAsPaired($account, $device);

            $this->auditLog->create([
                'account_id' => $account->id,
                'action' => 'device.paired',
                'description' => "User {$account->name} paired device {$device->name} ({$device->serial_number})",
                'auditable_id' => $device->id,
                'auditable_type' => Device::class,
                'context' => [
                    'device_id' => $device->id,
                    'serial_number' => $device->serial_number,
                    'token' => $token,
                ],
            ]);

            return $device;
        });
    }

    public function unpairDevice(Device $device): void
    {
        DB::transaction(function () use ($device) {
            if ($device->provisioningToken) {
                $device->provisioningToken->revoke();
            }

            $this->auditLog->create([
                'account_id' => $device->account_id,
                'action' => 'device.unpaired',
                'auditable_id' => $device->id,
                'auditable_type' => Device::class,
                'context' => [
                    'device_id' => $device->id,
                    'serial_number' => $device->serial_number,
                ],
            ]);

            $device->delete();
        });
    }
}
