<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Services\MqttPublishService;

class DeviceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'name',
        'action',
        'schedule_type',
        'scheduled_time',
        'days_of_week',
        'start_date',
        'end_date',
        'is_enabled',
        'last_executed_at',
    ];

    protected function casts(): array
    {
        return [
            'days_of_week' => 'array',
            'scheduled_time' => 'datetime:H:i:s',
            'is_enabled' => 'boolean',
            'last_executed_at' => 'datetime',
        ];
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopeForToday($query)
    {
        $dayOfWeek = now()->dayOfWeek;
        return $query->whereJsonContains('days_of_week', $dayOfWeek);
    }

    public function scopeDueNow($query)
    {
        return $query->whereTime('scheduled_time', '=', now()->format('H:i:00'));
    }

    public function shouldExecuteNow(): bool
    {
        if (!$this->is_enabled) {
            return false;
        }

        if ($this->last_executed_at && $this->last_executed_at->isToday()) {
            return false;
        }

        if ($this->start_date && now()->isBefore($this->start_date)) {
            return false;
        }

        if ($this->end_date && now()->isAfter($this->end_date)) {
            return false;
        }

        return true;
    }

    public function execute(): bool
    {
        if (!$this->shouldExecuteNow()) {
            return false;
        }

        $mqttService = app(MqttPublishService::class);
        $mqttService->setRelayState($this->device, $this->action);

        $this->markExecuted();

        return true;
    }

    public function markExecuted(): void
    {
        $this->update(['last_executed_at' => now()]);
    }
}
