<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Models\Enums\PostStatus;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostForm
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
                        /* --- Kolom Utama (Lebar: 2/3 pada Desktop) --- */
                        Group::make()
                            ->columnSpan([
                                'default' => 1,
                                'lg' => 2,
                            ])
                            ->schema([
                                Section::make('Informasi Utama')
                                    ->description('Judul, slug, dan taksonomi artikel.')
                                    ->icon('heroicon-o-document-text')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Judul Artikel')
                                            ->placeholder('Masukkan judul artikel...')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(
                                                fn (Set $set, ?string $state) =>
                                                    $set('slug', Str::slug($state ?? ''))
                                            ),

                                        TextInput::make('slug')
                                            ->label('Slug URL')
                                            ->placeholder('judul-artikel')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true)
                                            ->helperText('Slug dibuat otomatis dari judul.'),

                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                Select::make('category_id')
                                                    ->label('Kategori')
                                                    ->relationship('category', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required(),

                                                Select::make('tags')
                                                    ->label('Tags')
                                                    ->relationship('tags', 'name')
                                                    ->multiple()
                                                    ->searchable()
                                                    ->preload(),
                                            ]),

                                        Textarea::make('excerpt')
                                            ->label('Ringkasan / Excerpt')
                                            ->placeholder('Ringkasan singkat untuk kartu preview artikel...')
                                            ->rows(3)
                                            ->maxLength(500)
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Konten Artikel')
                                    ->description('Susun konten menggunakan komponen fleksibel.')
                                    ->icon('heroicon-o-pencil-square')
                                    ->schema([
                                        Builder::make('content')
                                            ->label('Isi Artikel')
                                            ->blocks([
                                                Builder\Block::make('text')
                                                    ->label('Teks')
                                                    ->icon('heroicon-o-bars-3-bottom-left')
                                                    ->schema([
                                                        RichEditor::make('content')
                                                            ->label('Teks')
                                                            ->required()
                                                            ->columnSpanFull(),
                                                    ]),

                                                Builder\Block::make('image')
                                                    ->label('Gambar')
                                                    ->icon('heroicon-o-photo')
                                                    ->schema([
                                                        FileUpload::make('image')
                                                            ->label('Gambar')
                                                            ->image()
                                                            ->disk('public')
                                                            ->directory('posts/content')
                                                            ->imageEditor()
                                                            ->maxSize(5120)
                                                            ->required(),

                                                        TextInput::make('caption')
                                                            ->label('Caption')
                                                            ->placeholder('Jelaskan gambar ini...')
                                                            ->maxLength(255),
                                                    ]),

                                                Builder\Block::make('code')
                                                    ->label('Code Snippet')
                                                    ->icon('heroicon-o-code-bracket')
                                                    ->schema([
                                                        Grid::make([
                                                            'default' => 1,
                                                            'sm' => 2,
                                                        ])
                                                            ->schema([
                                                                Select::make('language')
                                                                    ->label('Bahasa Pemrograman')
                                                                    ->options([
                                                                        'php' => 'PHP',
                                                                        'javascript' => 'JavaScript',
                                                                        'typescript' => 'TypeScript',
                                                                        'html' => 'HTML',
                                                                        'css' => 'CSS',
                                                                        'bash' => 'Bash',
                                                                        'shell' => 'Shell',
                                                                        'go' => 'Go',
                                                                        'python' => 'Python',
                                                                        'java' => 'Java',
                                                                        'c' => 'C',
                                                                        'cpp' => 'C++',
                                                                        'sql' => 'SQL',
                                                                        'json' => 'JSON',
                                                                        'yaml' => 'YAML',
                                                                        'markdown' => 'Markdown',
                                                                        'text' => 'Plain Text',
                                                                    ])
                                                                    ->searchable()
                                                                    ->default('php')
                                                                    ->required(),

                                                                TextInput::make('filename')
                                                                    ->label('Nama File')
                                                                    ->placeholder('Contoh: routes/web.php')
                                                                    ->maxLength(255),
                                                            ]),

                                                        Textarea::make('code')
                                                            ->label('Kode')
                                                            ->rows(12)
                                                            ->required()
                                                            ->columnSpanFull(),
                                                    ]),

                                                Builder\Block::make('quote')
                                                    ->label('Kutipan')
                                                    ->icon('heroicon-o-chat-bubble-left')
                                                    ->schema([
                                                        Textarea::make('quote')
                                                            ->label('Kutipan')
                                                            ->required()
                                                            ->rows(3)
                                                            ->columnSpanFull(),

                                                        TextInput::make('author')
                                                            ->label('Sumber / Penulis')
                                                            ->placeholder('Opsional')
                                                            ->maxLength(255),
                                                    ]),
                                            ])
                                            ->collapsible()
                                            ->cloneable()
                                            ->reorderable()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        /* --- Sidebar Samping (Lebar: 1/3 pada Desktop) --- */
                        Group::make()
                            ->columnSpan([
                                'default' => 1,
                                'lg' => 1,
                            ])
                            ->schema([
                                Section::make('Publikasi')
                                    ->icon('heroicon-o-globe-alt')
                                    ->schema([
                                        Select::make('status')
                                            ->label('Status')
                                            ->options(PostStatus::class)
                                            ->default(PostStatus::Draft)
                                            ->required(),

                                        DateTimePicker::make('published_at')
                                            ->label('Tanggal Publikasi')
                                            ->seconds(false)
                                            ->native(false)
                                            ->default(now()),
                                    ]),

                                Section::make('Media')
                                    ->icon('heroicon-o-photo')
                                    ->schema([
                                        FileUpload::make('cover_image')
                                            ->label('Cover Artikel')
                                            ->image()
                                            ->disk('public')
                                            ->directory('posts/covers')
                                            ->imageEditor()
                                            ->maxSize(5120),
                                    ]),

                                Section::make('SEO Metadata')
                                    ->icon('heroicon-o-magnifying-glass')
                                    ->collapsible()
                                    ->schema([
                                        TextInput::make('seo_title')
                                            ->label('SEO Title')
                                            ->placeholder('Judul di Search Engine')
                                            ->maxLength(255),

                                        Textarea::make('seo_description')
                                            ->label('SEO Description')
                                            ->placeholder('Deskripsi singkat di Search Engine')
                                            ->rows(3)
                                            ->maxLength(255),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}