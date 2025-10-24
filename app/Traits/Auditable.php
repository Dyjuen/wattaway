<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    protected static function bootAuditable()
    {
        static::created(function ($model) {
            AuditLog::log(
                action: get_class($model) . '.created',
                description: class_basename($model) . " created: {" . ($model->name ?? $model->id) . "}",
                context: ['new_values' => $model->getAttributes()]
            );
        });

        static::updated(function ($model) {
            AuditLog::log(
                action: get_class($model) . '.updated',
                description: class_basename($model) . " updated: {" . ($model->name ?? $model->id) . "}",
                context: [
                    'old_values' => $model->getOriginal(),
                    'new_values' => $model->getChanges(),
                ]
            );
        });

        static::deleted(function ($model) {
            AuditLog::log(
                action: get_class($model) . '.deleted',
                description: class_basename($model) . " deleted: {" . ($model->name ?? $model->id) . "}",
                context: ['old_values' => $model->getAttributes()]
            );
        });
    }
}
