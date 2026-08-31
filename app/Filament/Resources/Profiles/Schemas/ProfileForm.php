<?php

namespace App\Filament\Resources\Profiles\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProfileForm
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
                                Section::make('Biodata Diri')
                                    ->description('Informasi utama mengenai akun profil pengguna.')
                                    ->icon('heroicon-o-user')
                                    ->schema([
                                        Select::make('user_id')
                                            ->label('Pengguna')
                                            ->relationship('user', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->columnSpanFull(),

                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                TextInput::make('headline')
                                                    ->label('Headline / Posisi')
                                                    ->placeholder('Contoh: Backend & DevOps Engineer')
                                                    ->maxLength(255),

                                                TextInput::make('location')
                                                    ->label('Lokasi')
                                                    ->placeholder('Contoh: Buleleng, Bali')
                                                    ->prefixIcon('heroicon-o-map-pin')
                                                    ->maxLength(255),
                                            ]),

                                        RichEditor::make('bio')
                                            ->label('Biografi / Deskripsi Diri')
                                            ->placeholder('Tuliskan ringkasan singkat profil...')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Media Sosial & Tautan')
                                    ->description('Tautan ke situs web pribadi dan jaringan profesional.')
                                    ->icon('heroicon-o-link')
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                TextInput::make('website')
                                                    ->label('Website Utama')
                                                    ->placeholder('https://asepblog.my.id')
                                                    ->url()
                                                    ->prefixIcon('heroicon-o-globe-alt'),

                                                TextInput::make('github')
                                                    ->label('Username / URL GitHub')
                                                    ->placeholder('https://github.com/username')
                                                    ->prefixIcon('heroicon-o-code-bracket'),

                                                TextInput::make('linkedin')
                                                    ->label('URL LinkedIn')
                                                    ->placeholder('https://linkedin.com/in/username')
                                                    ->prefixIcon('heroicon-o-briefcase'),

                                                TextInput::make('instagram')
                                                    ->label('Username / URL Instagram')
                                                    ->placeholder('https://instagram.com/username')
                                                    ->prefixIcon('heroicon-o-camera'),

                                                TextInput::make('twitter')
                                                    ->label('Username / URL Twitter (X)')
                                                    ->placeholder('https://x.com/username')
                                                    ->prefixIcon('heroicon-o-at-symbol')
                                                    ->columnSpanFull(),
                                            ]),
                                    ]),
                            ]),

                        /* --- Sidebar Samping (Lebar: 1/3 Desktop) --- */
                        Group::make()
                            ->columnSpan([
                                'default' => 1,
                                'lg' => 1,
                            ])
                            ->schema([
                                Section::make('Foto Profil')
                                    ->icon('heroicon-o-photo')
                                    ->schema([
                                        FileUpload::make('avatar')
                                            ->label('Foto Avatar')
                                            ->image()
                                            ->avatar()
                                            ->disk('public')
                                            ->directory('profiles/avatars')
                                            ->visibility('public')
                                            ->maxSize(2048)
                                            ->alignCenter(),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}