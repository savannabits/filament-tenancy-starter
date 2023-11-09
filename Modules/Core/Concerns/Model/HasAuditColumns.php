<?php

namespace Modules\Core\Concerns\Model;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

trait HasAuditColumns
{
    public static function bootHasAuditColumns()
    {
        static::creating(function (Model $record) {
            $auth = auth();
            if ($auth->check()) {
                $record->owner()->associate($auth->id());
            }
        });
        static::saving(function (Model $record) {
            $auth = auth();
            if ($auth->check()) {
                $record->lastModifier()->associate($auth->id());
            }
        });
    }

    public function owner()
    {
        return $this->belongsTo(config('auth.providers.users.model', 'App\\Models\\User'), 'owner_id');
    }

    public function lastModifier()
    {
        return $this->belongsTo(config('auth.providers.users.model', 'App\\Models\\User'), 'modified_by');
    }
}
