<?php

namespace Modules\Core\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Core\Concerns\Model\FrameworkTraits;

class Team extends Model
{
    use FrameworkTraits;
    protected $guarded = [];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'team_user');
    }
}
