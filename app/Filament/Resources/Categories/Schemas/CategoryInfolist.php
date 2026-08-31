<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Kategori')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Nama Kategori')
                                    ->weight('bold')
                                    ->copyable(),

                                TextEntry::make('slug')
                                    ->label('Slug URL')
                                    ->badge()
                                    ->color('gray')
                                    ->copyable(),
                            ]),

                        TextEntry::make('description')
                            ->label('Deskripsi')
                            ->placeholder('Tidak ada deskripsi.')
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Dibuat Pada')
                                    ->dateTime('d M Y, H:i')
                                    ->placeholder('-'),

                                TextEntry::make('updated_at')
                                    ->label('Terakhir Diperbarui')
                                    ->dateTime('d M Y, H:i')
                                    ->placeholder('-'),
                            ]),
                    ]),
            ]);
    }
}