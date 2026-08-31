<?php

namespace App\Filament\Resources\Tags\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TagInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Tag')
                    ->icon('heroicon-o-hashtag')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Nama Tag')
                                    ->weight('bold')
                                    ->copyable(),

                                TextEntry::make('slug')
                                    ->label('Slug URL')
                                    ->badge()
                                    ->color('gray')
                                    ->copyable(),

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