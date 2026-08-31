<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BlogStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Artikel', Post::count())
                ->description('Semua postingan')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('emerald'),

            Stat::make('Artikel Terbit', Post::where('status', 'published')->count())
                ->description('Sudah dipublikasi')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Draft Artikel', Post::where('status', 'draft')->count())
                ->description('Dalam penulisan')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('warning'),

            Stat::make('Portofolio Proyek', Project::count())
                ->description('Project tersimpan')
                ->descriptionIcon('heroicon-m-code-bracket')
                ->color('info'),

            Stat::make('Total Komentar', Comment::count())
                ->description('Respon pembaca')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('primary'),
        ];
    }
}