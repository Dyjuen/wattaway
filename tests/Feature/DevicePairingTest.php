<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Device;
use App\Models\DeviceProvisioningToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DevicePairingTest extends TestCase
{
    use RefreshDatabase;

    public function test_token_validation_succeeds_for_valid_token(): void
    {
        // Arrange
        $account = Account::factory()->create();
        $token = DeviceProvisioningToken::generate('WS20250001234', 'ESP32-AABBCCDDEEFF');

        // Act
        $response = $this->actingAs($account, 'sanctum')
            ->postJson('/api/v1/pairing/validate', [
                'token' => $token->token,
            ]);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'device_info' => [
                    'serial_number' => 'WS20250001234',
                ],
            ]);
    }

    public function test_token_validation_fails_for_invalid_format(): void
    {
        $account = Account::factory()->create();
        $response = $this->actingAs($account, 'sanctum')
            ->postJson('/api/v1/pairing/validate', [
                'token' => 'INVALID-TOKEN',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['token']);
    }

    public function test_token_validation_fails_for_non_existent_token(): void
    {
        $account = Account::factory()->create();
        $response = $this->actingAs($account, 'sanctum')
            ->postJson('/api/v1/pairing/validate', [
                'token' => 'WS-NONEXISTENT00',
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'error' => 'Token not found',
            ]);
    }

    public function test_token_validation_fails_for_expired_token(): void
    {
        $account = Account::factory()->create();
        $token = DeviceProvisioningToken::generate('WS20250001235', 'ESP32-AABBCCDDEEF0');
        $token->update(['expires_at' => now()->subDay()]);

        $response = $this->actingAs($account, 'sanctum')
            ->postJson('/api/v1/pairing/validate', [
                'token' => $token->token,
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'error' => 'Token has expired',
            ]);
        $this->assertEquals('expired', $token->fresh()->status);
    }

    public function test_token_validation_fails_for_paired_token(): void
    {
        $account = Account::factory()->create();
        $device = Device::factory()->create(['account_id' => $account->id]);
        $token = DeviceProvisioningToken::generate('WS20250001236', 'ESP32-AABBCCDDEEF1');
        $token->markAsPaired($account, $device);

        $response = $this->actingAs($account, 'sanctum')
            ->postJson('/api/v1/pairing/validate', [
                'token' => $token->token,
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'error' => 'Token has already been used',
            ]);
    }

    public function test_token_validation_fails_for_revoked_token(): void
    {
        $account = Account::factory()->create();
        $token = DeviceProvisioningToken::generate('WS20250001237', 'ESP32-AABBCCDDEEF2');
        $token->revoke();

        $response = $this->actingAs($account, 'sanctum')
            ->postJson('/api/v1/pairing/validate', [
                'token' => $token->token,
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'error' => 'Token has been revoked',
            ]);
    }

    public function test_device_pairing_creates_device_record(): void
    {
        $account = Account::factory()->create();
        $token = DeviceProvisioningToken::generate('WS20250001238', 'ESP32-AABBCCDDEEF3');

        $response = $this->actingAs($account, 'sanctum')
            ->postJson('/api/v1/pairing/pair', [
                'token' => $token->token,
                'device_name' => 'My Test Device',
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('devices', [
            'account_id' => $account->id,
            'provisioning_token_id' => $token->id,
            'name' => 'My Test Device',
            'serial_number' => 'WS20250001238',
            'hardware_id' => 'ESP32-AABBCCDDEEF3',
            'status' => 'pending_activation',
        ]);
    }

    public function test_device_pairing_consumes_token(): void
    {
        $account = Account::factory()->create();
        $token = DeviceProvisioningToken::generate('WS20250001239', 'ESP32-AABBCCDDEEF4');

        $this->actingAs($account, 'sanctum')
            ->postJson('/api/v1/pairing/pair', [
                'token' => $token->token,
            ])->assertStatus(201);

        $this->assertEquals('paired', $token->fresh()->status);
        $this->assertNotNull($token->fresh()->paired_at);
        $this->assertNotNull($token->fresh()->device_id);
        $this->assertEquals($account->id, $token->fresh()->paired_by_account_id);
    }

    public function test_device_pairing_fails_with_expired_token(): void
    {
        $account = Account::factory()->create();
        $token = DeviceProvisioningToken::generate('WS20250001240', 'ESP32-AABBCCDDEEF5');
        $token->update(['expires_at' => now()->subDay()]);

        $response = $this->actingAs($account, 'sanctum')
            ->postJson('/api/v1/pairing/pair', [
                'token' => $token->token,
            ]);

        $response->assertStatus(400)
            ->assertJson(['success' => false, 'message' => 'Expired token.']);
    }

    public function test_device_pairing_fails_with_already_paired_token(): void
    {
        $account = Account::factory()->create();
        $device = Device::factory()->create(['account_id' => $account->id]);
        $token = DeviceProvisioningToken::generate('WS20250001241', 'ESP32-AABBCCDDEEF6');
        $token->markAsPaired($account, $device);

        $response = $this->actingAs($account, 'sanctum')
            ->postJson('/api/v1/pairing/pair', [
                'token' => $token->token,
            ]);

        $response->assertStatus(400)
            ->assertJson(['success' => false, 'message' => 'Invalid or already used token.']);
    }

    public function test_device_activation_succeeds_with_correct_hardware_id(): void
    {
        $account = Account::factory()->create();
        $token = DeviceProvisioningToken::generate('WS20250001242', 'ESP32-AABBCCDDEEF7');
        $device = Device::create([
            'account_id' => $account->id,
            'provisioning_token_id' => $token->id,
            'name' => 'Test Device for Activation',
            'serial_number' => 'WS20250001242',
            'hardware_id' => 'ESP32-AABBCCDDEEF7',
            'status' => 'pending_activation',
            'api_token' => Str::random(64),
        ]);

        $response = $this->postJson('/api/v1/device/activate', [
            'serial_number' => 'WS20250001242',
            'hardware_id' => 'ESP32-AABBCCDDEEF7',
            'firmware_version' => '1.0.0',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'device_id',
                'api_token',
                'mqtt_credentials' => ['host', 'port', 'username', 'password', 'topics' => ['publish', 'subscribe']],
                'user' => ['id', 'name', 'email'],
            ]);

        $this->assertEquals('online', $device->fresh()->status);
        $this->assertNotNull($device->fresh()->activated_at);
    }

    public function test_device_activation_fails_with_wrong_hardware_id(): void
    {
        $account = Account::factory()->create();
        $token = DeviceProvisioningToken::generate('WS20250001243', 'ESP32-AABBCCDDEEF8');
        $device = Device::create([
            'account_id' => $account->id,
            'provisioning_token_id' => $token->id,
            'name' => 'Test Device for Activation',
            'serial_number' => 'WS20250001243',
            'hardware_id' => 'ESP32-AABBCCDDEEF8',
            'status' => 'pending_activation',
            'api_token' => Str::random(64),
        ]);

        $response = $this->postJson('/api/v1/device/activate', [
            'serial_number' => 'WS20250001243',
            'hardware_id' => 'WRONG-HARDWARE-ID',
            'firmware_version' => '1.0.0',
        ]);

        $response->assertStatus(403)
            ->assertJson(['error' => 'Hardware verification failed']);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'device.activation_failed',
            'auditable_id' => $device->id,
            'auditable_type' => Device::class,
            'context->expected_hardware_id' => 'ESP32-AABBCCDDEEF8',
            'context->received_hardware_id' => 'WRONG-HARDWARE-ID',
        ]);
    }

    public function test_device_activation_fails_for_non_existent_device(): void
    {
        $response = $this->postJson('/api/v1/device/activate', [
            'serial_number' => 'WSNONEXISTENT',
            'hardware_id' => 'ESP32-AABBCCDDEEFF',
            'firmware_version' => '1.0.0',
        ]);

        $response->assertStatus(404)
            ->assertJson(['status' => 'NOT_PAIRED']);
    }

    public function test_unpairing_device_revokes_token_and_deletes_device(): void
    {
        $account = Account::factory()->create();
        $token = DeviceProvisioningToken::generate('WS20250001244', 'ESP32-AABBCCDDEEF9');
        $device = Device::create([
            'account_id' => $account->id,
            'provisioning_token_id' => $token->id,
            'name' => 'Device to Unpair',
            'serial_number' => 'WS20250001244',
            'hardware_id' => 'ESP32-AABBCCDDEEF9',
            'status' => 'online',
            'api_token' => Str::random(64),
        ]);

        $this->actingAs($account, 'sanctum')
            ->deleteJson("/api/v1/devices/{$device->id}/unpair")
            ->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Device unpaired successfully']);

        $this->assertSoftDeleted('devices', ['id' => $device->id]); // Assuming soft deletes
        $this->assertEquals('revoked', $token->fresh()->status);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'device.unpaired',
            'auditable_id' => $device->id,
            'auditable_type' => Device::class,
        ]);
    }
}
