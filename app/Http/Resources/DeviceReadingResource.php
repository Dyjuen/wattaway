<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceReadingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'timestamp' => $this->timestamp->toIso8601String(),
            'voltage' => $this->voltage,
            'channels' => $this->whenLoaded('channelReadings', function () {
                return $this->channelReadings->map(fn ($channel) => [
                    'channel' => $channel->channel,
                    'current' => $channel->current,
                    'power' => $channel->power,
                ]);
            }),
        ];
    }
}