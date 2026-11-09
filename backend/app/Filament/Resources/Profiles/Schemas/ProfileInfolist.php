<?php

namespace App\Filament\Resources\Profiles\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;

class ProfileInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'default' => 1,
                    'lg' => 3,
                ])
                    ->columnSpanFull()
                    ->schema([
                        /* --- Area Utama --- */
                        Group::make()
                            ->columnSpan([
                                'default' => 1,
                                'lg' => 2,
                            ])
                            ->schema([
                                Section::make('Informasi Pengguna')
                                    ->icon('heroicon-o-user-circle')
                                    ->schema([
                                        TextEntry::make('user.name')
                                            ->label('Nama Pengguna')
                                            ->weight('bold')
                                            ->size(TextSize::Large),

                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                TextEntry::make('headline')
                                                    ->label('Headline / Jabatan')
                                                    ->placeholder('-'),

                                                TextEntry::make('location')
                                                    ->label('Lokasi')
                                                    ->icon('heroicon-o-map-pin')
                                                    ->placeholder('-'),
                                            ]),

                                        TextEntry::make('bio')
                                            ->label('Biografi')
                                            ->html()
                                            ->placeholder('-')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Jaringan & Media Sosial')
                                    ->icon('heroicon-o-share')
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                TextEntry::make('website')
                                                    ->label('Website')
                                                    ->url(fn ($record) => $record->website, true)
                                                    ->icon('heroicon-o-globe-alt')
                                                    ->placeholder('-'),

                                                TextEntry::make('github')
                                                    ->label('GitHub')
                                                    ->url(fn ($record) => $record->github, true)
                                                    ->icon('heroicon-o-code-bracket')
                                                    ->placeholder('-'),

                                                TextEntry::make('linkedin')
                                                    ->label('LinkedIn')
                                                    ->url(fn ($record) => $record->linkedin, true)
                                                    ->icon('heroicon-o-briefcase')
                                                    ->placeholder('-'),

                                                TextEntry::make('instagram')
                                                    ->label('Instagram')
                                                    ->url(fn ($record) => $record->instagram, true)
                                                    ->icon('heroicon-o-camera')
                                                    ->placeholder('-'),

                                                TextEntry::make('twitter')
                                                    ->label('Twitter (X)')
                                                    ->url(fn ($record) => $record->twitter, true)
                                                    ->icon('heroicon-o-at-symbol')
                                                    ->placeholder('-'),
                                            ]),
                                    ]),
                            ]),

                        /* --- Sidebar Foto Profil & Log System --- */
                        Group::make()
                            ->columnSpan([
                                'default' => 1,
                                'lg' => 1,
                            ])
                            ->schema([
                                Section::make('Foto Profil')
                                    ->icon('heroicon-o-photo')
                                    ->schema([
                                        ImageEntry::make('avatar')
                                            ->disk('public')
                                            ->visibility('public')
                                            ->hiddenLabel()
                                            ->circular()
                                            ->alignCenter()
                                            ->placeholder('Tidak ada foto profil'),
                                    ]),

                                Section::make('Metadata Profil')
                                    ->icon('heroicon-o-clock')
                                    ->schema([
                                        TextEntry::make('created_at')
                                            ->label('Profil Dibuat')
                                            ->dateTime('d M Y, H:i')
                                            ->placeholder('-'),

                                        TextEntry::make('updated_at')
                                            ->label('Terakhir Diperbarui')
                                            ->dateTime('d M Y, H:i')
                                            ->placeholder('-'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}