<?php

namespace App\Services;

use App\Models\Device;
use PhpMqtt\Client\MqttClient;
use App\Models\MqttMessageLog;
use Illuminate\Support\Facades\Log;

class MqttPublishService
{
    protected MqttClient $mqtt;
    protected string $host;
    protected int $port;
    protected string $clientId;
    protected string $username;
    protected string $password;

    public function __construct(MqttClient $mqtt)
    {
        $this->mqtt = $mqtt;
        $this->username = config('mqtt.username');
        $this->password = config('mqtt.password');
    }

    public function sendCommand(Device $device, string $command, array $payload = []): bool
    {
        $topic = str_replace('{device_id}', $device->id, config('mqtt.topics.commands'));
        $message = [
            'command' => $command,
            'payload' => $payload,
            'timestamp' => now()->toIso8601String(),
        ];

        try {
            if (!$this->mqtt->isConnected()) {
                $connectionSettings = (new \PhpMqtt\Client\ConnectionSettings)
                    ->setUsername($this->username)
                    ->setPassword($this->password);
                $this->mqtt->connect($connectionSettings, true);
            }

            $this->mqtt->publish($topic, json_encode($message), 0);

            MqttMessageLog::logOutgoing(
                deviceId: $device->id,
                type: 'command',
                payload: $message,
                topic: $topic,
                status: 'success',
                responseCode: 200
            );

            Log::info("Command sent to device {$device->id}", ['command' => $command, 'payload' => $payload]);
            return true;
        } catch (\Exception $e) {
            MqttMessageLog::logOutgoing(
                deviceId: $device->id,
                type: 'command',
                payload: $message,
                topic: $topic,
                status: 'error',
                responseCode: 500,
                error: $e->getMessage()
            );

            Log::error("Failed to send command to device {$device->id}", ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function setRelayState(Device $device, int $channel, string $state): bool
    {
        return $this->sendCommand($device, 'set_relay_state', ['channel' => $channel, 'state' => $state]);
    }

    public function updateDeviceConfig(Device $device, array $config): bool
    {
        return $this->sendCommand($device, 'update_config', $config);
    }

    public function requestDeviceStatus(Device $device): bool
    {
        return $this->sendCommand($device, 'get_status');
    }

    public function publishConfiguration(Device $device, string $type, array $value): bool
    {
        $topic = "wattaway/{$device->hardware_id}/config/{$type}";
        $payload = json_encode($value);

        try {
            if (!$this->mqtt->isConnected()) {
                $connectionSettings = (new \PhpMqtt\Client\ConnectionSettings)
                    ->setUsername($this->username)
                    ->setPassword($this->password);
                $this->mqtt->connect($connectionSettings, true);
            }

            $this->mqtt->publish($topic, $payload, 1, true); // QoS 1, retained

            MqttMessageLog::logOutgoing(
                deviceId: $device->id,
                type: 'config',
                payload: $value,
                topic: $topic,
                status: 'success',
                responseCode: 200
            );

            Log::info("Configuration '{$type}' sent to device {$device->id}", ['payload' => $value]);
            return true;
        } catch (\Exception $e) {
            MqttMessageLog::logOutgoing(
                deviceId: $device->id,
                type: 'config',
                payload: $value,
                topic: $topic,
                status: 'error',
                responseCode: 500,
                error: $e->getMessage()
            );

            Log::error("Failed to send configuration '{$type}' to device {$device->id}", ['error' => $e->getMessage()]);
            return false;
        }
    }
}
