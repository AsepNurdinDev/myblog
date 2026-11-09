<?php

namespace App\Filament\Resources\Comments\Schemas;

use App\Models\Enums\CommentStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CommentForm
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
                        /* --- Area Utama (Lebar: 2/3 Desktop) --- */
                        Group::make()
                            ->columnSpan([
                                'default' => 1,
                                'lg' => 2,
                            ])
                            ->schema([
                                Section::make('Artikel & Pengirim')
                                    ->description('Informasi terkait artikel target dan pengirim komentar.')
                                    ->icon('heroicon-o-user')
                                    ->schema([
                                        Select::make('post_id')
                                            ->label('Artikel')
                                            ->relationship('post', 'title')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->columnSpanFull(),

                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label('Nama Pengirim')
                                                    ->placeholder('Masukkan nama...')
                                                    ->required()
                                                    ->maxLength(255),

                                                TextInput::make('email')
                                                    ->label('Alamat Email')
                                                    ->placeholder('nama@example.com')
                                                    ->email()
                                                    ->required()
                                                    ->maxLength(255),
                                            ]),
                                    ]),

                                Section::make('Isi Komentar')
                                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                                    ->schema([
                                        Textarea::make('content')
                                            ->label('Komentar')
                                            ->placeholder('Tuliskan komentar...')
                                            ->rows(5)
                                            ->required()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        /* --- Sidebar Samping (Lebar: 1/3 Desktop) --- */
                        Group::make()
                            ->columnSpan([
                                'default' => 1,
                                'lg' => 1,
                            ])
                            ->schema([
                                Section::make('Moderasi Status')
                                    ->icon('heroicon-o-shield-check')
                                    ->schema([
                                        Select::make('status')
                                            ->label('Status Moderasi')
                                            ->options(CommentStatus::class)
                                            ->default(CommentStatus::Pending ?? 'pending')
                                            ->required(),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}