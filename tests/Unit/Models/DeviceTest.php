<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Device;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeviceTest extends TestCase
{
    use RefreshDatabase;

    public function test_generates_api_token_on_creation()
    {
        $device = Device::factory()->create();

        $this->assertNotNull($device->api_token);
        $this->assertEquals(64, strlen($device->api_token));
    }

    public function test_regenerates_api_token()
    {
        $device = Device::factory()->create();
        $oldToken = $device->api_token;

        $device->regenerateApiToken();

        $this->assertNotEquals($oldToken, $device->api_token);
        $this->assertEquals(64, strlen($device->api_token));
    }

    public function test_belongs_to_account()
    {
        $account = Account::factory()->create();
        $device = Device::factory()->create(['account_id' => $account->id]);

        $this->assertInstanceOf(Account::class, $device->account);
        $this->assertEquals($account->id, $device->account->id);
    }
}