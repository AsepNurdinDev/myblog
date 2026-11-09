<?php

namespace App\Filament\Resources\Comments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CommentInfolist
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
                                Section::make('Detail Komentar')
                                    ->icon('heroicon-o-chat-bubble-left-right')
                                    ->schema([
                                        TextEntry::make('post.title')
                                            ->label('Artikel')
                                            ->weight('bold')
                                            ->color('primary'),

                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                TextEntry::make('name')
                                                    ->label('Nama Pengirim')
                                                    ->icon('heroicon-o-user'),

                                                TextEntry::make('email')
                                                    ->label('Alamat Email')
                                                    ->icon('heroicon-o-envelope')
                                                    ->copyable(),
                                            ]),

                                        TextEntry::make('content')
                                            ->label('Isi Komentar')
                                            ->placeholder('-')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        /* --- Sidebar Status --- */
                        Group::make()
                            ->columnSpan([
                                'default' => 1,
                                'lg' => 1,
                            ])
                            ->schema([
                                Section::make('Informasi Moderasi')
                                    ->icon('heroicon-o-information-circle')
                                    ->schema([
                                        TextEntry::make('status')
                                            ->label('Status')
                                            ->badge(),

                                        TextEntry::make('created_at')
                                            ->label('Dikirim Pada')
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