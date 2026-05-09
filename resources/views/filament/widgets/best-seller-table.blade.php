<x-filament-widgets::widget>
    {{-- Ubah angka border-top untuk menebalkan garis aksen hijau (default: 5px) --}}
    <div class="fi-wi-stats-overview-stat" style="border-radius:12px; padding:1.25rem; height:100%; border-top: 5px solid #1a6b45;">

        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
            <h3 style="font-size:0.95rem; font-weight:600; margin:0;">Produk Terlaris</h3>
            <span style="font-size:0.75rem; color:#16a34a; font-weight:600;">
                Top {{ $this->getBestSellers()->count() }}
            </span>
        </div>

        @forelse($this->getBestSellers() as $index => $product)
            <div style="display:flex; align-items:center; gap:0.75rem; padding:0.6rem 0; border-bottom:1px solid rgba(156,163,175,0.15);">
                
                {{-- Nomor ranking: #1 hijau gelap, #2 hijau sedang, #3 hijau muda, sisanya abu --}}
                <div style="width:1.5rem; height:1.5rem; border-radius:50%; background:{{ $index === 0 ? '#1a6b45' : ($index === 1 ? '#25a06a' : ($index === 2 ? '#4ade80' : 'rgba(156,163,175,0.2)')) }}; display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:700; color:white; flex-shrink:0;">
                    {{ $index + 1 }}
                </div>

                <div style="flex:1; min-width:0;">
                    <div style="font-size:0.875rem; font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $product->name }}</div>
                    <div style="font-size:0.75rem; opacity:0.5;">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</div>
                </div>

                <span style="font-size:0.8rem; font-weight:600; color:#16a34a; flex-shrink:0;">
                    {{ number_format($product->total_sold ?? 0, 0, ',', '.') }} pcs
                </span>

            </div>
        @empty
            <div style="text-align:center; padding:2rem 0; opacity:0.4; font-size:0.875rem;">
                Belum ada data penjualan
            </div>
        @endforelse

    </div>
</x-filament-widgets::widget>