<?php

namespace App\Filament\Resources\Skills\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class SkillForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Keahlian (Skill)')
                    ->description('Kelola daftar skill teknis dan slug untuk portofolio.')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Skill')
                                    ->placeholder('Contoh: Go, Laravel, Docker...')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(
                                        fn ($set, ?string $state) =>
                                            $set('slug', Str::slug($state ?? ''))
                                    ),

                                TextInput::make('slug')
                                    ->label('Slug URL')
                                    ->placeholder('go')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Slug dibuat otomatis dari nama skill.'),
                            ]),
                    ]),
            ]);
    }
}