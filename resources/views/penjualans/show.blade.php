<x-layouts.app>
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}"
            class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Dashboard') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <a href="{{ route($tablename . '.index') }}"
            class="text-blue-600 dark:text-blue-400 hover:underline">{{ $title }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-500 dark:text-gray-400">{{ __('View') }}</span>
    </div>

    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">View {{ $title }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $title }} details</p>
        </div>
        <div class="flex gap-2">
            @if(auth()->user()->hasPermission('edit-' . $tablename))
            <a href="{{ route($tablename . '.edit', $penjualan->id) }}">
                <x-button type="primary">{{ __('Edit Penjualan') }}</x-button>
            </a>
            @endif
            <a href="{{ route($tablename . '.index') }}">
                <x-button type="secondary">{{ __('Back') }}</x-button>
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 border-b border-gray-100 dark:border-gray-700 pb-6">
                <div>
                    <h3 class="text-sm font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-4">Informasi Penjualan</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">No. Transaksi Penjualan</label>
                            <div class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $penjualan->no_transaksi_penjualan }}</div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Customer</label>
                            <div class="text-md font-semibold text-gray-900 dark:text-gray-100">{{ $penjualan->customer->nama ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Informasi Pengiriman</label>
                            <div class="text-md font-semibold text-gray-900 dark:text-gray-100">
                                <a href="{{ route('pengirimans.show', $penjualan->pengiriman->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                    {{ $penjualan->pengiriman->no_transaksi ?? '-' }}
                                </a>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tanggal Transaksi</label>
                            <div class="text-md font-semibold text-gray-900 dark:text-gray-100">{{ $penjualan->created_at->format('d F Y H:i') }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase mb-4">Ringkasan Penjualan</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total Netto Pengiriman:</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($penjualan->details->sum('netto_pengiriman'), 2) }} kg</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total Netto Terjual:</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($penjualan->details->sum('netto'), 2) }} kg</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total Selisih:</span>
                            <span class="font-semibold {{ $penjualan->details->sum('selisih') >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ number_format($penjualan->details->sum('selisih'), 2) }} kg
                            </span>
                        </div>
                        <div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                            <span class="text-gray-500">Total Sub Total:</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format($penjualan->details->sum('sub_total'), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total PPh:</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format($penjualan->details->sum('pph'), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total PPN:</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format($penjualan->details->sum('ppn'), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-xl border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                            <span class="font-bold text-gray-700 dark:text-gray-300">Total Nominal Akhir:</span>
                            <span class="font-black text-indigo-600 dark:text-indigo-400">Rp {{ number_format($penjualan->details->sum('nominal_akhir'), 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <h3 class="text-sm font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-4">Daftar Produk Terjual</h3>
                <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">Nama Produk</th>
                                <th class="px-4 py-3 text-center">Tipe</th>
                                <th class="px-4 py-3 text-center">Netto Kirim (kg)</th>
                                <th class="px-4 py-3 text-center">Netto Terjual (kg)</th>
                                <th class="px-4 py-3 text-center">Selisih (kg)</th>
                                <th class="px-4 py-3 text-center">Basis Harga</th>
                                <th class="px-4 py-3 text-center">Sub Total</th>
                                <th class="px-4 py-3 text-center">PPh</th>
                                <th class="px-4 py-3 text-center">PPN</th>
                                <th class="px-4 py-3 text-center">Nominal Akhir</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($penjualan->details as $detail)
                            <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900/50">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                    {{ $detail->produk->name ?? $detail->produk_id }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($detail->tipe == 'Titip') 
                                            bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @else 
                                            bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @endif">
                                        {{ $detail->tipe }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    {{ number_format($detail->netto_pengiriman, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($detail->netto, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="{{ $detail->selisih >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ number_format($detail->selisih, 2) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    Rp {{ number_format($detail->basis_harga, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    Rp {{ number_format($detail->sub_total, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    Rp {{ number_format($detail->pph, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    Rp {{ number_format($detail->ppn, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center font-semibold text-indigo-600 dark:text-indigo-400">
                                    Rp {{ number_format($detail->nominal_akhir, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-700">
                            <tr class="font-semibold text-gray-900 dark:text-white">
                                <td class="px-4 py-3 text-right" colspan="2">Total:</td>
                                <td class="px-4 py-3 text-center">{{ number_format($penjualan->details->sum('netto_pengiriman'), 2) }}</td>
                                <td class="px-4 py-3 text-center">{{ number_format($penjualan->details->sum('netto'), 2) }}</td>
                                <td class="px-4 py-3 text-center">{{ number_format($penjualan->details->sum('selisih'), 2) }}</td>
                                <td class="px-4 py-3 text-center"></td>
                                <td class="px-4 py-3 text-center">Rp {{ number_format($penjualan->details->sum('sub_total'), 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center">Rp {{ number_format($penjualan->details->sum('pph'), 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center">Rp {{ number_format($penjualan->details->sum('ppn'), 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center font-bold">Rp {{ number_format($penjualan->details->sum('nominal_akhir'), 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>