<x-filament-widgets::widget>
    <div 
        x-data="{ 
            time: '{{ now()->format('H:i:s') }}',
            tick() {
                const now = new Date();
                this.time = now.getHours().toString().padStart(2, '0') + ':' + 
                            now.getMinutes().toString().padStart(2, '0') + ':' + 
                            now.getSeconds().toString().padStart(2, '0');
            }
        }"
        x-init="setInterval(() => tick(), 1000)"
        style="display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem;"
    >
        
        {{-- Kiri: Greeting & Tanggal --}}
        <div>
            @php
                $hour = now()->hour;
                $sapa = $hour < 5 ? 'Selamat Malam' : ($hour < 12 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 19 ? 'Selamat Sore' : 'Selamat Malam')));
                $tanggal = now()->translatedFormat('l, d F Y');
            @endphp
            <div style="font-size:1rem; font-weight:600;">{{ $sapa }}, {{ auth()->user()?->name }}!</div>
            <div style="font-size:0.85rem; opacity:0.5;">{{ $tanggal }}</div>
        </div>

        {{-- Kanan: Jam realtime --}}
        <div 
            style="font-size:1.75rem; font-weight:700; letter-spacing:0.05em; color:#1a6b45; font-variant-numeric: tabular-nums;"
            x-text="time"
        >
            {{-- Placeholder awal agar tidak kosong saat loading --}}
            {{ now()->format('H:i:s') }}
        </div>
    </div>
</x-filament-widgets::widget>