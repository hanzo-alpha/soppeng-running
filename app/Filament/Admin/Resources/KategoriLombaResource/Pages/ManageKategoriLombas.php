<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\KategoriLombaResource\Pages;

use App\Filament\Admin\Resources\KategoriLombaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageKategoriLombas extends ManageRecords
{
    protected static string $resource = KategoriLombaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-s-plus'),
        ];
    }
}
