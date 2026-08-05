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
            <a href="{{ route($tablename . '.edit', $pengiriman->id) }}">
                <x-button type="primary">{{ __('Edit Pengiriman') }}</x-button>
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
                    <h3 class="text-sm font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-4">Informasi Pengiriman</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kode Transaksi</label>
                            <div class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $pengiriman->no_transaksi }}</div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Customer</label>
                            <div class="text-md font-semibold text-gray-900 dark:text-gray-100">{{ $pengiriman->customer->nama ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nomor Polisi (Nopol)</label>
                            <div class="text-md font-semibold text-gray-900 dark:text-gray-100">{{ $pengiriman->nopol ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tanggal</label>
                            <div class="text-md font-semibold text-gray-900 dark:text-gray-100">{{ $pengiriman->created_at->format('d F Y H:i') }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase mb-4">Ringkasan Pengiriman</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total Bruto:</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($pengiriman->details->sum('bruto'), 2) }} kg</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total Tara:</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($pengiriman->details->sum('tara'), 2) }} kg</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total Netto:</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($pengiriman->details->sum('netto'), 2) }} kg</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total Jumlah Karung:</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($pengiriman->details->sum('jumlah_karung'), 0) }} karung</span>
                        </div>
                        <div class="flex justify-between text-xl border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                            <span class="font-bold text-gray-700 dark:text-gray-300">Total Berat Kirim:</span>
                            <span class="font-black text-indigo-600 dark:text-indigo-400">{{ number_format($pengiriman->details->sum('netto'), 2) }} kg</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <h3 class="text-sm font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-4">Daftar Barang</h3>
                <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">Nama Barang</th>
                                <th class="px-4 py-3 text-center">Jumlah per Karung</th>
                                <th class="px-4 py-3 text-center">Jumlah Karung</th>
                                <th class="px-4 py-3 text-center">Bruto (kg)</th>
                                <th class="px-4 py-3 text-center">Tara (kg)</th>
                                <th class="px-4 py-3 text-center">Netto (kg)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($pengiriman->details as $detail)
                            <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900/50">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                    {{ $detail->nama_barang }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    {{ number_format($detail->jumlah_per_karung, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    {{ number_format($detail->jumlah_karung, 0) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    {{ number_format($detail->bruto, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    {{ number_format($detail->tara, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($detail->netto, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-700">
                            <tr class="font-semibold text-gray-900 dark:text-white">
                                <td class="px-4 py-3 text-right" colspan="2">Total:</td>
                                <td class="px-4 py-3 text-center">{{ number_format($pengiriman->details->sum('jumlah_karung'), 0) }}</td>
                                <td class="px-4 py-3 text-center">{{ number_format($pengiriman->details->sum('bruto'), 2) }}</td>
                                <td class="px-4 py-3 text-center">{{ number_format($pengiriman->details->sum('tara'), 2) }}</td>
                                <td class="px-4 py-3 text-center font-bold">{{ number_format($pengiriman->details->sum('netto'), 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="mt-8 flex gap-3">
                <a href="{{ route('pengirimans.suratjalan', $pengiriman->id) }}" target="_blank">
                    <x-button type="secondary" class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"></path>
                        </svg>
                        Surat Jalan
                    </x-button>
                </a>
                <a href="{{ route($tablename . '.index') }}">
                    <x-button type="primary">Kembali ke Daftar</x-button>
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>