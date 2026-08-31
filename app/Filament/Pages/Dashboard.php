<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected string $view = 'filament.pages.dashboard';

    public function getGreetingData(): array
    {
        Carbon::setLocale('id');

        // Pastikan waktu diambil sesuai zona waktu WITA (Asia/Makassar)
        $now = now()->setTimezone('Asia/Makassar');
        $hour = (int) $now->format('H');

        $sapaan = match (true) {
            $hour >= 3 && $hour < 11  => 'Selamat Pagi',
            $hour >= 11 && $hour < 15 => 'Selamat Siang',
            $hour >= 15 && $hour < 18 => 'Selamat Sore',
            default                   => 'Selamat Malam',
        };

        return [
            'sapaan'  => $sapaan,
            'nama'    => auth()->user()->name ?? 'asepshan',
            'tanggal' => $now->translatedFormat('l, d F Y'),
            'waktu'   => $now->format('H:i'),
        ];
    }
}