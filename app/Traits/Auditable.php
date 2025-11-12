<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    protected static function bootAuditable()
    {
        static::created(function (Model $model) {
            $model->audit('created', [
                'new_values' => $model->getAttributes(),
            ]);
        });

        static::updated(function (Model $model) {
            $model->audit('updated', [
                'old_values' => $model->getOriginal(),
                'new_values' => $model->getChanges(),
            ]);
        });

        static::deleted(function (Model $model) {
            $model->audit('deleted', [
                'old_values' => $model->getAttributes(),
            ]);
        });
    }

    public function audit(string $event, array $context = []): void
    {
        AuditLog::create([
            'account_id' => auth()->id(),
            'action' => class_basename($this).'.'.$event,
            'description' => class_basename($this)." {$event}: {".($this->name ?? $this->id).'}',
            'auditable_id' => $this->id,
            'auditable_type' => static::class,
            'context' => $context,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
