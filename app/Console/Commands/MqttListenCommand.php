<?php

namespace App\Console\Commands;

use App\Jobs\ProcessIncomingDeviceData;
use App\Models\MqttMessageLog;
use App\Services\MonitoringService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\MqttClient;

class MqttListenCommand extends Command
{
    protected $signature = 'mqtt:listen';
    protected $description = 'Listen to MQTT broker for device messages';

    public function handle(MqttClient $mqtt)
    {
        $username = config('mqtt.username');
        $password = config('mqtt.password');

        pcntl_async_signals(true);
        pcntl_signal(SIGINT, fn() => $mqtt->interrupt());
        pcntl_signal(SIGTERM, fn() => $mqtt->interrupt());

        while (true) {
            try {
                if (!$mqtt->isConnected()) {
                    $connectionSettings = (new \PhpMqtt\Client\ConnectionSettings)
                        ->setUsername($username)
                        ->setPassword($password)
                        ->setConnectTimeout(5)
                        ->setKeepAliveInterval(10)
                        ->setUseTls(false);

                    $mqtt->connect($connectionSettings, true);
                    $this->info('Connected to MQTT broker.');

                    $mqtt->subscribe(config('mqtt.topics.data'), function ($topic, $message) {
                        Log::info('Received message on data topic', ['topic' => $topic, 'message' => $message]);
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
                    }, 1);

                    // *** NEW: Subscription for device status ***
                    $statusTopic = config('mqtt.topics.status');
                    if ($statusTopic === null) {
                        Log::critical('MQTT status topic is null. Aborting.');
                        return 1;
                    }
                    $mqtt->subscribe($statusTopic, function ($topic, $message) {
                        $deviceId = null;
                        try {
                            // Extract device_id from topic: devices/{id}/status
                            preg_match('/devices\/(\d+)\/status/', $topic, $matches);
                            $deviceId = $matches[1] ?? null;

                            if (!$deviceId) {
                                throw new \Exception('Could not parse device ID from status topic');
                            }

                            $status = strtolower(trim($message));

                            // Ignore other messages on this topic (like command ACKs)
                            if ($status !== 'online' && $status !== 'offline') {
                                return;
                            }

                            // Use MonitoringService to update the device status in the DB
                            (new MonitoringService())->updateStatusFromMqtt((int)$deviceId, $status);

                            $this->info("Processed status update for device {$deviceId}: {$status}");

                        } catch (\Exception $e) {
                            $this->error("Error processing status message for device {$deviceId}: " . $e->getMessage());
                            Log::error("Error processing status message for device {$deviceId}", [
                                'topic' => $topic,
                                'message' => $message,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }, 0);
                }

                $mqtt->loop(true);

            } catch (\Exception $e) {
                Log::error('MQTT connection error: ' . $e->getMessage());
                $this->error('MQTT connection error: ' . $e->getMessage());
                $this->info('Attempting to reconnect in 5 seconds...');
                sleep(5);
                    }
                }    }
}