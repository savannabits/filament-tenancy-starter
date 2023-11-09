<?php

namespace Modules\Core\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Core\app\Models\Team;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $default = Team::firstOrCreate(['code' => 'DEFAULT'],[
            'name' => 'MAIN BRANCH',
        ]);
        $default->submit();
    }
}
