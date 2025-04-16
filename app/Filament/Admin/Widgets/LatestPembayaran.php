<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Models\Pembayaran;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestPembayaran extends BaseWidget
{
    protected static ?int $sort = 2;
    protected static ?string $heading = 'Pembayaran Terbaru';
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Pembayaran::query()
                    ->limit(10)
                    ->orderBy('created_at', 'desc'),
            )
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('pendaftaran.nama_lengkap')
                    ->label('Nama Lengkap'),
                Tables\Columns\TextColumn::make('kategori.nama')
                    ->label('Kategori')
                    ->alignCenter()
                    ->badge(),
                Tables\Columns\TextColumn::make('ukuran_jersey')
                    ->label('Ukuran Jersey')
                    ->badge()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('total_harga')
                    ->label('Total Harga')
                    ->alignRight()
                    ->money(currency: 'IDR', locale: 'id'),
                Tables\Columns\TextColumn::make('status_pembayaran')
                    ->label('Status Pembayaran')
                    ->badge()
                    ->alignCenter(),
            ]);
    }
}
