<x-filament-widgets::widget>
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1rem;">

        {{-- Omzet Bulan Ini --}}
        <div style="background:linear-gradient(135deg,#1a6b45,#25a06a); border-radius:14px; padding:1.25rem; color:white; box-shadow:0 2px 8px rgba(26,107,69,0.3);">
            <div style="font-size:0.8rem; opacity:0.8; margin-bottom:0.4rem;">Omzet Bulan Ini</div>
            <div style="font-size:1.5rem; font-weight:700;">Rp {{ $this->getOmzetBulanIni() }}</div>
            <div style="font-size:0.75rem; opacity:0.7; margin-top:0.3rem;">{{ now()->translatedFormat('F Y') }}</div>
        </div>

        {{-- Profit Bulan Ini --}}
        <div style="background:linear-gradient(135deg,#166534,#16a34a); border-radius:14px; padding:1.25rem; color:white; box-shadow:0 2px 8px rgba(22,101,52,0.3);">
            <div style="font-size:0.8rem; opacity:0.8; margin-bottom:0.4rem;">Profit Bulan Ini</div>
            <div style="font-size:1.5rem; font-weight:700;">Rp {{ $this->getProfitBulanIni() }}</div>
            <div style="font-size:0.75rem; opacity:0.7; margin-top:0.3rem;">Keuntungan bersih</div>
        </div>

        {{-- Total Omzet --}}
        <div style="background:linear-gradient(135deg,#14532d,#15803d); border-radius:14px; padding:1.25rem; color:white; box-shadow:0 2px 8px rgba(20,83,45,0.3);">
            <div style="font-size:0.8rem; opacity:0.8; margin-bottom:0.4rem;">Total Omzet</div>
            <div style="font-size:1.5rem; font-weight:700;">Rp {{ $this->getOmzetTotal() }}</div>
            <div style="font-size:0.75rem; opacity:0.7; margin-top:0.3rem;">Akumulasi seluruh penjualan</div>
        </div>

    </div>
</x-filament-widgets::widget>