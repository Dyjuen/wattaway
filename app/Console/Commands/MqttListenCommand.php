<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use App\Jobs\ProcessIncomingDeviceData;
use App\Models\Device;
use App\Models\MqttMessageLog;
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
                $deviceId = null;
                try {
                    // Extract device_id from topic
                    preg_match('/devices\/(\d+)\/data/', $topic, $matches);
                    $deviceId = $matches[1] ?? null;

                    $data = json_decode($message, true);

                    if (!$data) {
                        throw new \Exception('Invalid JSON format');
                    }

                    // Log incoming MQTT message
                    MqttMessageLog::logIncoming(
                        deviceId: $deviceId,
                        type: 'data',
                        payload: $data,
                        topic: $topic,
                        status: 'success'
                    );

                    // Dispatch job to process data
                    ProcessIncomingDeviceData::dispatch($deviceId, $data);

                } catch (\Exception $e) {
                    // Log error
                    MqttMessageLog::logIncoming(
                        deviceId: $deviceId,
                        type: 'data',
                        payload: ['raw_message' => $message],
                        topic: $topic,
                        status: 'error',
                        error: $e->getMessage()
                    );

                    $this->error("Error processing message: " . $e->getMessage());
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
