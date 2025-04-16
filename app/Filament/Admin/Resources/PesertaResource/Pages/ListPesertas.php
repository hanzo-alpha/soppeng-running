<?php

namespace App\Filament\Admin\Resources\PesertaResource\Pages;

use App\Filament\Admin\Resources\PesertaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPesertas extends ListRecords
{
    protected static string $resource = PesertaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
