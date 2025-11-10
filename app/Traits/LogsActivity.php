<?php
// app/Traits/LogsActivity.php

namespace App\Traits;

use App\Services\ActivityLogger;

trait LogsActivity
{
    /**
     * Boot the logs activity trait
     */
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            ActivityLogger::log(
                strtolower(class_basename($model)) . '.created',
                class_basename($model) . " created: " . ($model->title ?? $model->name ?? $model->id),
                $model
            );
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            unset($changes['updated_at']);
            
            if (!empty($changes)) {
                ActivityLogger::log(
                    strtolower(class_basename($model)) . '.updated',
                    class_basename($model) . " updated: " . ($model->title ?? $model->name ?? $model->id),
                    $model,
                    ['changes' => $changes]
                );
            }
        });

        static::deleted(function ($model) {
            ActivityLogger::log(
                strtolower(class_basename($model)) . '.deleted',
                class_basename($model) . " deleted: " . ($model->title ?? $model->name ?? $model->id),
                $model
            );
        });
    }
}