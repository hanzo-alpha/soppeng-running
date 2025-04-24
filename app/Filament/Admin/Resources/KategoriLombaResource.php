<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Enums\StatusPendaftaran;
use App\Filament\Admin\Resources\KategoriLombaResource\Pages;
use App\Models\KategoriLomba;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Laravolt\Indonesia\Models\City;

class KategoriLombaResource extends Resource
{
    protected static ?string $model = KategoriLomba::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $label = 'Kategori Lomba';
    protected static ?string $modelLabel = 'Kategori Lomba';
    protected static ?string $pluralLabel = 'Kategori Lomba';
    protected static ?string $pluralModelLabel = 'Kategori Lomba';
    protected static ?string $navigationLabel = 'Kategori Lomba';
    protected static ?string $navigationGroup = 'Master';

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('harga')
                    ->numeric(),
                Forms\Components\TextInput::make('jumlah')
                    ->label('Jumlah Persediaan')
                    ->numeric(),
                Forms\Components\ColorPicker::make('warna'),
                Forms\Components\Select::make('kategori')
                    ->label('Kategori')
                    ->options(StatusPendaftaran::class)
                    ->native(false)
                    ->searchable()
                    ->default(StatusPendaftaran::TIKET_RUN)
                    ->required(),
                Forms\Components\Select::make('kabupaten')
                    ->options(fn() => City::whereIn('code', ['7312', '7313', '7314'])->pluck('name', 'code'))
                    ->native(false),
                Forms\Components\TextInput::make('deskripsi')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('harga')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ColorColumn::make('warna'),
                Tables\Columns\TextColumn::make('kategori')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->searchable(),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageKategoriLombas::route('/'),
        ];
    }
}
