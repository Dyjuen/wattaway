<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Esp32MessageLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $payload = json_decode($this->payload, true);
        $rate = config('wattaway.rate_per_kwh', 0.15);

        return [
            'voltage' => isset($payload['voltage']) ? round($payload['voltage'], 2) : null,
            'current' => isset($payload['current']) ? round($payload['current'], 2) : null,
            'power' => isset($payload['power']) ? round($payload['power'], 2) : null,
            'energy' => isset($payload['energy']) ? round($payload['energy'], 2) : null,
            'frequency' => isset($payload['frequency']) ? round($payload['frequency'], 2) : null,
            'power_factor' => isset($payload['power_factor']) ? round($payload['power_factor'], 2) : null,
            'cost' => isset($payload['energy']) ? round($payload['energy'] * $rate, 4) : null,
            'timestamp' => $this->created_at->toIso8601String(),
        ];
    }
}
