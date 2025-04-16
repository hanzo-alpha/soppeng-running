<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PendaftaranResource\Pages;

use App\Filament\Admin\Resources\PendaftaranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPendaftaran extends EditRecord
{
    protected static string $resource = PendaftaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        $record->pembayaran()->update([
            'ukuran_jersey' => $data['ukuran_jersey'],
            'kategori_lomba' => $data['kategori_lomba'],
        ]);
        return $record;
    }
}
