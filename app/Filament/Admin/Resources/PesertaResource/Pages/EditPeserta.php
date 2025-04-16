<?php

namespace App\Filament\Admin\Resources\PesertaResource\Pages;

use App\Filament\Admin\Resources\PesertaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPeserta extends EditRecord
{
    protected static string $resource = PesertaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
