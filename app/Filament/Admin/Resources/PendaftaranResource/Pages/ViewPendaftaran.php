<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PendaftaranResource\Pages;

use App\Filament\Admin\Resources\PendaftaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPendaftaran extends ViewRecord
{
    protected static string $resource = PendaftaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil'),
        ];
    }
}
