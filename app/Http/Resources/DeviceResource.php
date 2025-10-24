<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => 'smart_socket',
            'status' => $this->status,
            'last_seen' => $this->last_seen_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'latest_data' => new Esp32MessageLogResource($this->whenLoaded('esp32MessageLogs')),
            'api_token' => $this->when($request->user()->can('viewApiToken', $this->resource), $this->api_token),
        ];
    }
}
