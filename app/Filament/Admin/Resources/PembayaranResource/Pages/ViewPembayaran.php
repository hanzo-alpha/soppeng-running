<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PembayaranResource\Pages;

use App\Filament\Admin\Resources\PembayaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPembayaran extends ViewRecord
{
    protected static string $resource = PembayaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
