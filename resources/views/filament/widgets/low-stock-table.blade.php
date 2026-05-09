<x-filament-widgets::widget>
    {{-- Ubah angka border-top untuk menebalkan garis aksen hijau (default: 5px) --}}
    <div class="fi-wi-stats-overview-stat" style="border-radius:12px; padding:1.25rem; height:100%; border-top: 5px solid #1a6b45;">
        
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
            <h3 style="font-size:0.95rem; font-weight:600; margin:0;">Stok Menipis</h3>
            <span style="font-size:0.75rem; color:#16a34a; font-weight:600;">
                {{ $this->getLowStock()->count() }} bahan
            </span>
        </div>

        @forelse($this->getLowStock() as $item)
            <div style="display:flex; align-items:center; justify-content:space-between; padding:0.6rem 0; border-bottom:1px solid rgba(156,163,175,0.15);">
                <div>
                    <div style="font-size:0.875rem; font-weight:500;">{{ $item->name }}</div>
                    <div style="font-size:0.75rem; opacity:0.5;">Min: {{ $item->min_stock }} {{ $item->unit }}</div>
                </div>
                <span style="font-size:0.8rem; font-weight:600; color:#dc2626;">
                    {{ $item->stock }} {{ $item->unit }}
                </span>
            </div>
        @empty
            <div style="text-align:center; padding:2rem 0; opacity:0.4; font-size:0.875rem;">
                Semua stok aman
            </div>
        @endforelse

    </div>
</x-filament-widgets::widget>