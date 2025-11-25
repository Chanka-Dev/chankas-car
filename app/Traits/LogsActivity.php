<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    /**
     * Boot del trait
     */
    protected static function bootLogsActivity()
    {
        // Evento: Cuando se crea un registro
        static::created(function ($model) {
            ActivityLog::log(
                'created',
                static::getLogDescription($model, 'created'),
                get_class($model),
                $model->getKey()
            );
        });

        // Evento: Cuando se actualiza un registro
        static::updated(function ($model) {
            $changes = $model->getChanges();
            unset($changes['updated_at']); // No registrar cambios en updated_at

            if (!empty($changes)) {
                ActivityLog::log(
                    'updated',
                    static::getLogDescription($model, 'updated'),
                    get_class($model),
                    $model->getKey(),
                    ['changes' => $changes, 'original' => $model->getOriginal()]
                );
            }
        });

        // Evento: Cuando se elimina un registro
        static::deleted(function ($model) {
            ActivityLog::log(
                'deleted',
                static::getLogDescription($model, 'deleted'),
                get_class($model),
                $model->getKey()
            );
        });
    }

    /**
     * Generar descripción del log
     */
    protected static function getLogDescription($model, $action)
    {
        $modelName = class_basename($model);
        $identifier = $model->nombre ?? $model->name ?? $model->id ?? 'ID: ' . $model->getKey();

        $actions = [
            'created' => 'creó',
            'updated' => 'actualizó',
            'deleted' => 'eliminó',
        ];

        return sprintf(
            '%s %s: %s',
            $actions[$action] ?? $action,
            $modelName,
            $identifier
        );
    }
}
