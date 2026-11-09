<x-filament-panels::page>
    @php
        $greeting = $this->getGreetingData();
    @endphp

    <!-- Card Welcome Banner Custom -->
    <div style="background: linear-gradient(135deg, #059669 0%, #0d9488 50%, #0891b2 100%); color: #ffffff; border-radius: 1rem; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);">
        <div style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
            <div>
                <h2 style="font-size: 1.5rem; font-weight: 800; color: #ffffff; margin: 0;">
                    {{ $greeting['sapaan'] }}, {{ $greeting['nama'] }}! 👋
                </h2>
                <p style="margin-top: 0.5rem; color: #ccfbf1; font-size: 0.875rem;">
                    Kelola konten blog, portofolio proyek, dan moderasi komentar pengunjung dalam satu panel kontrol.
                </p>
            </div>

            <!-- Box Jam Realtime (Alpine.js) -->
            <div x-data="realtimeClock()" x-init="startClock()" style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); border-radius: 0.75rem; padding: 0.5rem 1rem; text-align: right;">
                <p x-text="tanggal" style="font-size: 0.75rem; color: #ccfbf1; margin: 0;">{{ $greeting['tanggal'] }}</p>
                <p x-text="waktu" style="font-size: 1.125rem; font-weight: 700; color: #ffffff; margin: 0;">{{ $greeting['waktu'] }} WITA</p>
            </div>
        </div>
    </div>

    <!-- Render Widget Native Filament -->
    @if (count($widgets = $this->getVisibleWidgets()))
        <x-filament-widgets::widgets
            :widgets="$widgets"
            :columns="$this->getColumns()"
        />
    @endif

    <!-- Script Clock Alpine.js -->
    <script>
        function realtimeClock() {
            return {
                tanggal: '{{ $greeting['tanggal'] }}',
                waktu: '{{ $greeting['waktu'] }} WITA',
                startClock() {
                    setInterval(() => {
                        const now = new Date();
                        
                        // Format Tanggal Bahasa Indonesia
                        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', timeZone: 'Asia/Makassar' };
                        this.tanggal = now.toLocaleDateString('id-ID', options);

                        // Format Waktu HH:mm:ss WITA
                        const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false, timeZone: 'Asia/Makassar' };
                        this.waktu = now.toLocaleTimeString('id-ID', timeOptions).replace(/\./g, ':') + ' WITA';
                    }, 1000);
                }
            }
        }
    </script>
</x-filament-panels::page>