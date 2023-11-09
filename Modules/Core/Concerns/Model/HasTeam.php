<?php

namespace Modules\Core\Concerns\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Modules\Core\app\Models\Team;
use Illuminate\Database\Eloquent\Model;
use function Modules\Core\framework;

trait HasTeam
{
    public static function getTeamColumnName(): string
    {
        return 'team_id';
    }

    public function hasTeamColumn(): bool
    {
        return Schema::hasColumn($this->getModel()->getTable(),'team_id');
    }
    public static function bootHasTeam(): void
    {
        self::creating(function (Model $model) {
            $col = static::getTeamColumnName();
            if ($model->hasTeamColumn()) {
                if (!$model->{$col}) {
                    if (auth()->check()) {
                        $model->{$col} = auth()->user()->team?->id;
                    } else {
                        $model->{$col} = framework()->defaultTeam()?->getAttribute('id');
                    }
                }
            }
        });

        // Add scope
        if (auth()->check()) {
            static::addGlobalScope('team', function (Builder $query) {
                if (in_array($query->getModel()->getMorphClass(), static::getSharedModels())) {
                    return;
                }
                if ($this->hasTeamColumn()) {
                    $user = auth()->user();
                    if ($user) {
                        $query->whereBelongsTo($user->team)
                            ->orWhereNull('team_id')
                            ->orWhere('team_id','=', framework()->defaultTeam()?->id);
                    } else  {
                        $query->whereNull('team_id')
                            ->orWhere('team_id','=', framework()->defaultTeam()?->id);
                    }
                }
            });
        }
    }

    public function team()
    {
        if (!$this->hasTeamColumn()) return null;
        return $this->belongsTo(Team::class, $this->getTeamColumnName());
    }

    protected function initializeHasTeam()
    {
//        $this->casts['is_cross_team'] = 'bool';
    }

    protected static function getSharedModels() {
        return config('core.shared_team_models',[]);
    }
}
