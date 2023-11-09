<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;

class TenantsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(TeamSeeder::class);
    }
}
