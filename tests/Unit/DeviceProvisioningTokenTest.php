<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Device;
use App\Models\DeviceProvisioningToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceProvisioningTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_token_generation_format(): void
    {
        $token = DeviceProvisioningToken::generate('WS123456789012', 'ESP32-ABCDEF123456');

        $this->assertNotNull($token->token);
        $this->assertMatchesRegularExpression('/^WS-[A-Z0-9]{12}$/', $token->token);
        $this->assertEquals('WS123456789012', $token->serial_number);
        $this->assertEquals('ESP32-ABCDEF123456', $token->hardware_id);
        $this->assertEquals('pending', $token->status);
    }

    public function test_expiration_date_setting(): void
    {
        $token = DeviceProvisioningToken::generate('WS123456789013', 'ESP32-ABCDEF123457');

        $this->assertNotNull($token->expires_at);
        $this->assertTrue($token->expires_at->isSameDay(now()->addDays(30)));
    }

    public function test_is_pending_method(): void
    {
        $token = DeviceProvisioningToken::generate('WS123456789014', 'ESP32-ABCDEF123458');
        $this->assertTrue($token->isPending());

        $token->update(['expires_at' => now()->subDay()]);
        $this->assertFalse($token->isPending());

        $token->update(['status' => 'paired', 'expires_at' => now()->addDay()]);
        $this->assertFalse($token->isPending());
    }

    public function test_is_paired_method(): void
    {
        $token = DeviceProvisioningToken::generate('WS123456789015', 'ESP32-ABCDEF123459');
        $this->assertFalse($token->isPaired());

        $token->update(['status' => 'paired']);
        $this->assertTrue($token->isPaired());
    }

    public function test_is_expired_method(): void
    {
        $token = DeviceProvisioningToken::generate('WS123456789016', 'ESP32-ABCDEF123460');
        $this->assertFalse($token->isExpired());

        $token->update(['expires_at' => now()->subDay()]);
        $this->assertTrue($token->isExpired());
    }

    public function test_mark_as_paired_method(): void
    {
        $account = Account::factory()->create();
        $device = Device::factory()->create(['account_id' => $account->id]);
        $token = DeviceProvisioningToken::generate('WS123456789017', 'ESP32-ABCDEF123461');

        $token->markAsPaired($account, $device);

        $this->assertEquals('paired', $token->fresh()->status);
        $this->assertEquals($device->id, $token->fresh()->device_id);
        $this->assertEquals($account->id, $token->fresh()->paired_by_account_id);
        $this->assertNotNull($token->fresh()->paired_at);
    }

    public function test_revoke_method(): void
    {
        $token = DeviceProvisioningToken::generate('WS123456789018', 'ESP32-ABCDEF123462');
        $this->assertEquals('pending', $token->status);

        $token->revoke();

        $this->assertEquals('revoked', $token->fresh()->status);
    }

    public function test_get_qr_code_url_method(): void
    {
        $token = DeviceProvisioningToken::generate('WS123456789019', 'ESP32-ABCDEF123463');
        $expectedUrl = config('app.url') . '/pair?token=' . $token->token;

        $this->assertEquals($expectedUrl, $token->getQrCodeUrl());
    }

    public function test_get_qr_code_data_method(): void
    {
        $token = DeviceProvisioningToken::generate('WS123456789020', 'ESP32-ABCDEF123464');
        $expectedData = json_encode([
            'token' => $token->token,
            'serial_number' => $token->serial_number,
            'url' => $token->getQrCodeUrl(),
        ]);

        $this->assertEquals($expectedData, $token->getQrCodeData());
    }

    public function test_pending_scope(): void
    {
        DeviceProvisioningToken::generate('WS123456789021', 'ESP32-ABCDEF123465'); // Pending
        $expiredToken = DeviceProvisioningToken::generate('WS123456789022', 'ESP32-ABCDEF123466');
        $expiredToken->update(['expires_at' => now()->subDay()]);
        $pairedToken = DeviceProvisioningToken::generate('WS123456789023', 'ESP32-ABCDEF123467');
        $pairedToken->update(['status' => 'paired']);

        $pendingTokens = DeviceProvisioningToken::pending()->get();

        $this->assertCount(1, $pendingTokens);
        $this->assertEquals('WS123456789021', $pendingTokens->first()->serial_number);
    }

    public function test_paired_scope(): void
    {
        DeviceProvisioningToken::generate('WS123456789024', 'ESP32-ABCDEF123468');
        $pairedToken = DeviceProvisioningToken::generate('WS123456789025', 'ESP32-ABCDEF123469');
        $pairedToken->update(['status' => 'paired']);

        $pairedTokens = DeviceProvisioningToken::paired()->get();

        $this->assertCount(1, $pairedTokens);
        $this->assertEquals('WS123456789025', $pairedTokens->first()->serial_number);
    }

    public function test_expired_scope(): void
    {
        DeviceProvisioningToken::generate('WS123456789026', 'ESP32-ABCDEF123470');
        $expiredToken = DeviceProvisioningToken::generate('WS123456789027', 'ESP32-ABCDEF123471');
        $expiredToken->update(['expires_at' => now()->subDay()]);

        $expiredTokens = DeviceProvisioningToken::expired()->get();

        $this->assertCount(1, $expiredTokens);
        $this->assertEquals('WS123456789027', $expiredTokens->first()->serial_number);
    }
}
