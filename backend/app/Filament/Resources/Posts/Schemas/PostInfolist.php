<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;

class PostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        /* --- Area Utama --- */
                        Group::make()
                            ->columnSpan(2)
                            ->schema([
                                Section::make('Detail Artikel')
                                    ->icon('heroicon-o-document-text')
                                    ->schema([
                                        TextEntry::make('title')
                                            ->label('Judul Artikel')
                                            ->weight('bold')
                                            ->size(TextSize::Large),

                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('slug')
                                                    ->label('Slug')
                                                    ->badge()
                                                    ->color('gray')
                                                    ->copyable(),

                                                TextEntry::make('category.name')
                                                    ->label('Kategori')
                                                    ->badge()
                                                    ->color('primary')
                                                    ->placeholder('-'),
                                            ]),

                                        TextEntry::make('tags.name')
                                            ->label('Tags')
                                            ->badge()
                                            ->color('info')
                                            ->separator(', ')
                                            ->placeholder('Tidak ada tag'),

                                        TextEntry::make('excerpt')
                                            ->label('Ringkasan')
                                            ->placeholder('Tidak ada ringkasan.')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Pengaturan SEO')
                                    ->icon('heroicon-o-magnifying-glass')
                                    ->collapsible()
                                    ->schema([
                                        TextEntry::make('seo_title')
                                            ->label('SEO Title')
                                            ->placeholder('-'),

                                        TextEntry::make('seo_description')
                                            ->label('SEO Description')
                                            ->placeholder('-'),
                                    ]),
                            ]),

                        /* --- Sidebar Status & Cover --- */
                        Group::make()
                            ->columnSpan(1)
                            ->schema([
                                Section::make('Informasi Publikasi')
                                    ->icon('heroicon-o-information-circle')
                                    ->schema([
                                        TextEntry::make('status')
                                            ->label('Status')
                                            ->badge(),

                                        TextEntry::make('published_at')
                                            ->label('Tanggal Rilis')
                                            ->dateTime('d M Y, H:i')
                                            ->placeholder('Belum dipublikasi'),

                                        TextEntry::make('created_at')
                                            ->label('Dibuat Pada')
                                            ->dateTime('d M Y, H:i'),

                                        TextEntry::make('updated_at')
                                            ->label('Terakhir Diubah')
                                            ->dateTime('d M Y, H:i'),
                                    ]),

                                Section::make('Cover Artikel')
                                    ->icon('heroicon-o-photo')
                                    ->schema([
                                        ImageEntry::make('cover_image')
                                            ->disk('public')
                                            ->visibility('public')
                                            ->hiddenLabel()
                                            ->placeholder('Tidak ada gambar cover'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
