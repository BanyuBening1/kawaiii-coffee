<div class="space-y-6">
    <!-- Header Summary & Navigation back -->
    <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-800">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">
                Detail Transaksi #{{ $record->transaction_code }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Diproses pada {{ \Carbon\Carbon::parse($record->transaction_date)->translatedFormat('d F Y, H:i') }}
            </p>
        </div>
        <a href="{{ \App\Filament\Resources\Transactions\TransactionsResource::getUrl('index') }}" 
           class="fi-btn fi-btn-color-gray fi-btn-style-outlined inline-flex items-center justify-center gap-1 font-semibold rounded-lg text-sm px-3 py-2 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
            &larr; Kembali ke Daftar
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kolom Kiri: Informasi Utama & Items -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Card Informasi Transaksi -->
            <div class="p-6 bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
                <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
                    Informasi Transaksi
                </h3>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 block text-xs">Kode Transaksi</span>
                        <span class="font-mono font-semibold text-gray-900 dark:text-white">{{ $record->transaction_code }}</span>
                    </div>

                    <div>
                        <span class="text-gray-500 dark:text-gray-400 block text-xs">Tanggal</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($record->transaction_date)->format('d/m/Y H:i') }}</span>
                    </div>

                    <div>
                        <span class="text-gray-500 dark:text-gray-400 block text-xs">Kasir</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $record->cashier->name ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="text-gray-500 dark:text-gray-400 block text-xs">Metode Pembayaran</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 mt-1">
                            {{ ucfirst($record->payment_method ?? '-') }}
                        </span>
                    </div>

                    <div>
                        <span class="text-gray-500 dark:text-gray-400 block text-xs">Status</span>
                        @php
                            $status = strtolower($record->status);
                            $badgeClass = match($status) {
                                'completed', 'success', 'lunas' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 ring-emerald-600/20',
                                'pending' => 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 ring-amber-600/20',
                                default => 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400 ring-rose-600/20',
                            };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset {{ $badgeClass }} mt-1">
                            {{ ucfirst($record->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card Items / Detail Pesanan -->
            @if($record->details && $record->details->count() > 0)
            <div class="p-6 bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
                <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-4">Daftar Item</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-gray-200 dark:border-gray-800 text-gray-500">
                            <tr>
                                <th class="py-2">Produk</th>
                                <th class="py-2 text-right">Harga</th>
                                <th class="py-2 text-center">Qty</th>
                                <th class="py-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($record->details as $item)
                            <tr>
                                <td class="py-3 font-medium text-gray-900 dark:text-white">{{ $item->product->name ?? 'Produk' }}</td>
                                <td class="py-3 text-right text-gray-600 dark:text-gray-300">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="py-3 text-center text-gray-600 dark:text-gray-300">{{ $item->quantity }}x</td>
                                <td class="py-3 text-right font-semibold text-gray-900 dark:text-white">Rp {{ number_format($item->subtotal ?? ($item->price * $item->quantity), 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Kolom Kanan: Ringkasan Total -->
        <div class="space-y-6">
            <div class="p-6 bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
                <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-4">Ringkasan Pembayaran</h3>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center pt-2 border-t border-gray-100 dark:border-gray-800">
                        <span class="text-base font-bold text-gray-900 dark:text-white">Total Transaksi</span>
                        <span class="text-xl font-extrabold text-primary-600 dark:text-primary-400">
                            Rp {{ number_format($record->total, 0, ',', '.') }}
                        </span>
                    </div>

                    @if(isset($record->paid_amount))
                    <div class="flex justify-between items-center text-gray-600 dark:text-gray-400">
                        <span>Dibayar</span>
                        <span>Rp {{ number_format($record->paid_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif

                    @if(isset($record->change_amount))
                    <div class="flex justify-between items-center text-emerald-600 dark:text-emerald-400 font-medium">
                        <span>Kembalian</span>
                        <span>Rp {{ number_format($record->change_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            @if(!empty($record->notes))
            <div class="p-6 bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
                <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-2">Catatan</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $record->notes }}</p>
            </div>
            @endif
        </div>
    </div>
</div>