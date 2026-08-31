<?php

namespace App\Filament\Resources\Tags\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Tag')
                    ->description('Kelola nama dan slug untuk label/tag artikel.')
                    ->icon('heroicon-o-hashtag')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Tag')
                                    ->placeholder('Masukkan nama tag...')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(
                                        fn ($set, ?string $state) =>
                                            $set('slug', Str::slug($state ?? ''))
                                    ),

                                TextInput::make('slug')
                                    ->label('Slug URL')
                                    ->placeholder('nama-tag')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Slug dibuat otomatis dari nama tag.'),
                            ]),
                    ]),
            ]);
    }
}