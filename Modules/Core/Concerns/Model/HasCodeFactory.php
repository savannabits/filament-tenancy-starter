<?php

namespace Modules\Core\Concerns\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Modules\Core\Models\CodeFactory;

trait HasCodeFactory
{

    public abstract function getCodePrefix();
    public static function getCodeColumnName(): string
    {
        return 'code';
    }

    public function getCodePadLength(): int
    {
        return 2;
    }

    public function getCodePadString(): string
    {
        return '0';
    }

    public function shouldOmitPrefix(): bool
    {
        return false; // Override this to change
    }

    public function hasCodeColumn(): bool
    {
        return Schema::hasColumn($this->getModel()->getTable(), static::getCodeColumnName());
    }
    public static function bootHasCodeFactory(): void
    {
        static::creating(function (Model $model) {
            if ($model->hasCodeColumn()) {
                if (! $model->getAttribute(static::getCodeColumnName())) {
                    $uuid = Str::uuid()->toString();
                    $model->{static::getCodeColumnName()} = $uuid;
                }
            }
        });
        static::created(function(Model $model) {
            if ($model->hasCodeColumn()) {
                if (Str::isUuid($model->getAttribute(static::getCodeColumnName()))) {
                    $model = $model::withoutGlobalScopes()->where('id','=', $model->getAttribute('id'))->firstOrFail();
                    $model->updateQuietly([static::getCodeColumnName() => $model->calculated_code]);
                }
            }
        });
    }
    public function getCalculatedCodeAttribute(): string
    {
        $code = Str::of($this->id)->padLeft($this->getCodePadLength() ?: 2,$this->getCodePadString() ?: '0');
        if (!$this->shouldOmitPrefix()) {
            $code = $code->prepend($this->getCodePrefix())->upper();
        }
        return $code->toString();
    }
}
