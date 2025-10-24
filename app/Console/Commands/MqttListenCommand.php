<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use App\Jobs\ProcessIncomingDeviceData;
use App\Models\Device;
use Illuminate\Support\Facades\Log;

class MqttListenCommand extends Command
{
    protected $signature = 'mqtt:listen';
    protected $description = 'Listen to MQTT broker for device messages';

    public function handle()
    {
        $host = config('mqtt.host');
        $port = config('mqtt.port');
        $clientId = config('mqtt.client_id');
        $username = config('mqtt.username');
        $password = config('mqtt.password');

        $mqtt = new MqttClient($host, $port, $clientId);

        pcntl_async_signals(true);
        pcntl_signal(SIGINT, fn() => $mqtt->interrupt());
        pcntl_signal(SIGTERM, fn() => $mqtt->interrupt());

        try {
            $connectionSettings = (new \PhpMqtt\Client\ConnectionSettings)
                ->setUsername($username)
                ->setPassword($password)
                ->setConnectTimeout(5)
                ->setUseTls(false);

            $mqtt->connect($connectionSettings, true);
            $this->info('Connected to MQTT broker.');

            $mqtt->subscribe(config('mqtt.topics.data'), function ($topic, $message) {
                $this->info("Received message on topic [{$topic}]: {$message}");

                // Extract device_id from topic
                $matches = [];
                if (preg_match('/devices\/(\\d+)\/data/', $topic, $matches)) {
                    $deviceId = $matches[1];
                    $device = Device::find($deviceId);

                    if (!$device) {
                        Log::warning("Received message for unknown device: {$deviceId}");
                        return;
                    }

                    $data = json_decode($message, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        Log::warning("Received invalid JSON from device: {$deviceId}");
                        return;
                    }

                    ProcessIncomingDeviceData::dispatch($deviceId, $data);
                }
            }, 0);

            $mqtt->loop(true);
            $this->info('MQTT listener stopped.');

        } catch (\Exception $e) {
            Log::error('MQTT connection error: ' . $e->getMessage());
            $this->error('MQTT connection error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
