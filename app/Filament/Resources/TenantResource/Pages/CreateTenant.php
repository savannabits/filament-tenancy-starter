<?php

namespace App\Filament\Resources\TenantResource\Pages;

use App\Filament\Resources\TenantResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\app\Models\Tenant;
use Throwable;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    /**
     * @throws Throwable
     */
    protected function handleRecordCreation(array $data): Model
    {
        $record = parent::handleRecordCreation(collect($data)->except('domain')->toArray());
        $record->domains()->create(['domain' => collect($data)->get('domain')]);
        return $record;
    }


    /**
     * @throws Throwable
     */
    private function createTenantRecord(array $data)
    {
        \Log::info("Saving Tenant");
        $record = new Tenant(collect($data)->except('domain')->toArray());
        $record->saveOrFail();
        \Log::info("Saving Domains");
        $record = $record::find($record->id);
        $record->domains()->create(['domain' => collect($data)->get('domain')]);
        return $record;
    }
}
