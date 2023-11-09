<?php

namespace Modules\Core\Concerns\Model;

use Illuminate\Database\Eloquent\Model;

trait Immutable
{
    public static function bootImmutable(): void
    {
        static::updating(function (Model $model) {
            throw new \RuntimeException("The record is immutable and cannot be modified.");
        });
        static::deleting(function (Model $model) {
            throw new \RuntimeException("The record is immutable and cannot be deleted.");
        });
    }
    public function hasImmutableTrait(): void
    {
    }


}
