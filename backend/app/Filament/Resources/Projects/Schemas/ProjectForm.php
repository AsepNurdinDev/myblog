<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\Enums\ProjectStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
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
                                Section::make('Informasi Proyek')
                                    ->description('Judul, slug, dan deskripsi utama proyek.')
                                    ->icon('heroicon-o-folder')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Judul Proyek')
                                            ->placeholder('Contoh: Sistem Informasi Kasir')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(
                                                fn ($set, ?string $state) => $set('slug', Str::slug($state ?? ''))
                                            ),

                                        TextInput::make('slug')
                                            ->label('Slug URL')
                                            ->placeholder('sistem-informasi-kasir')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true)
                                            ->helperText('Slug dibuat otomatis dari judul.'),

                                        Textarea::make('description')
                                            ->label('Deskripsi Singkat')
                                            ->placeholder('Ringkasan tentang proyek ini...')
                                            ->rows(3)
                                            ->maxLength(500)
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Detail Konten / Studi Kasus')
                                    ->description('Penjelasan rinci mengenai arsitektur, fitur, atau tantangan proyek.')
                                    ->icon('heroicon-o-document-text')
                                    ->schema([
                                        RichEditor::make('content')
                                            ->label('Konten Lengkap')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Tautan / Links')
                                    ->icon('heroicon-o-link')
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                TextInput::make('url')
                                                    ->label('Live Demo URL')
                                                    ->placeholder('https://example.com')
                                                    ->url()
                                                    ->prefixIcon('heroicon-o-globe-alt'),

                                                TextInput::make('github_url')
                                                    ->label('Repository GitHub')
                                                    ->placeholder('https://github.com/user/repo')
                                                    ->url()
                                                    ->prefixIcon('heroicon-o-code-bracket'),
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
                                Section::make('Status & Jadwal')
                                    ->icon('heroicon-o-calendar')
                                    ->schema([
                                        Select::make('status')
                                            ->label('Status Proyek')
                                            ->options(ProjectStatus::class)
                                            ->default(ProjectStatus::Ongoing ?? 'ongoing')
                                            ->required(),

                                        DatePicker::make('started_at')
                                            ->label('Tanggal Mulai')
                                            ->native(false),

                                        DatePicker::make('completed_at')
                                            ->label('Tanggal Selesai')
                                            ->native(false),
                                    ]),

                                Section::make('Media Thumbnail')
                                    ->icon('heroicon-o-photo')
                                    ->schema([
                                        FileUpload::make('thumbnail')
                                            ->label('Thumbnail Proyek')
                                            ->image()
                                            ->disk('public')
                                            ->directory('projects/thumbnails')
                                            ->imageEditor()
                                            ->maxSize(5120),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
