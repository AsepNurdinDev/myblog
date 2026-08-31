<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Support\Enums\TextSize;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectInfolist
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
                                Section::make('Detail Proyek')
                                    ->icon('heroicon-o-folder')
                                    ->schema([
                                        TextEntry::make('title')
                                            ->label('Judul Proyek')
                                            ->weight('bold')
                                            ->size(TextSize::Large),

                                        TextEntry::make('slug')
                                            ->label('Slug')
                                            ->badge()
                                            ->color('gray')
                                            ->copyable(),

                                        TextEntry::make('description')
                                            ->label('Deskripsi Singkat')
                                            ->placeholder('Tidak ada deskripsi.')
                                            ->columnSpanFull(),

                                        TextEntry::make('content')
                                            ->label('Detail Konten')
                                            ->html()
                                            ->placeholder('Tidak ada konten tambahan.')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Tautan Publikasi')
                                    ->icon('heroicon-o-link')
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                TextEntry::make('url')
                                                    ->label('Live Demo')
                                                    ->url(fn ($record) => $record->url, true)
                                                    ->icon('heroicon-o-globe-alt')
                                                    ->placeholder('-'),

                                                TextEntry::make('github_url')
                                                    ->label('GitHub Repository')
                                                    ->url(fn ($record) => $record->github_url, true)
                                                    ->icon('heroicon-o-code-bracket')
                                                    ->placeholder('-'),
                                            ]),
                                    ]),
                            ]),

                        /* --- Sidebar Status & Thumbnail --- */
                        Group::make()
                            ->columnSpan([
                                'default' => 1,
                                'lg' => 1,
                            ])
                            ->schema([
                                Section::make('Informasi Proyek')
                                    ->icon('heroicon-o-information-circle')
                                    ->schema([
                                        TextEntry::make('status')
                                            ->label('Status')
                                            ->badge(),

                                        TextEntry::make('started_at')
                                            ->label('Mulai Ditangani')
                                            ->date('d M Y')
                                            ->placeholder('-'),

                                        TextEntry::make('completed_at')
                                            ->label('Selesai Ditangani')
                                            ->date('d M Y')
                                            ->placeholder('Masih Berjalan'),

                                        TextEntry::make('created_at')
                                            ->label('Dibuat Pada')
                                            ->dateTime('d M Y, H:i'),

                                        TextEntry::make('updated_at')
                                            ->label('Terakhir Diperbarui')
                                            ->dateTime('d M Y, H:i'),
                                    ]),

                                Section::make('Thumbnail Proyek')
                                    ->icon('heroicon-o-photo')
                                    ->schema([
                                        ImageEntry::make('thumbnail')
                                            ->hiddenLabel()
                                            ->placeholder('Tidak ada gambar thumbnail'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}