<?php

namespace App\Filament\Admin\Resources\PesertaResource\Pages;

use App\Filament\Admin\Resources\PesertaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPeserta extends ViewRecord
{
    protected static string $resource = PesertaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
