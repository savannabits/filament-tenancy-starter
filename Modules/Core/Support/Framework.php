<?php

namespace Modules\Core\Support;

use Modules\Core\app\Models\Team;

class Framework
{
    public function defaultTeam(): ?Team
    {
        return Team::query()->whereCode('DEFAULT')->first();
    }
}
