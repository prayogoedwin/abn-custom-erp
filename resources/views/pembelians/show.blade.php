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
            <a href="{{ route($tablename . '.edit', $pembelian->id) }}">
                <x-button type="primary">{{ __('Edit Produk') }}</x-button>
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
                    <h3 class="text-sm font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-4">Informasi Transaksi</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kode Transaksi</label>
                            <div class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $pembelian->no_transaksi }}</div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Supplier</label>
                            <div class="text-md font-semibold text-gray-900 dark:text-gray-100">{{ $pembelian->supplier->nama ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tanggal</label>
                            <div class="text-md font-semibold text-gray-900 dark:text-gray-100">{{ $pembelian->created_at->format('d F Y H:i') }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase mb-4">Ringkasan Pembayaran</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total Netto Keseluruhan:</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($pembelian->details->sum('netto'), 2) }}</span>
                        </div>
                        <div class="flex justify-between text-xl border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                            <span class="font-bold text-gray-700 dark:text-gray-300">Total Tagihan:</span>
                            <span class="font-black text-indigo-600 dark:text-indigo-400">Rp {{ number_format($pembelian->details->sum('harga_netto'), 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <h3 class="text-sm font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-4">Daftar Produk</h3>
                <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">Produk</th>
                                <th class="px-4 py-3 text-center">Rendeman</th>
                                <th class="px-4 py-3 text-center">Netto</th>
                                <th class="px-4 py-3">Harga Basis</th>
                                <th class="px-4 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($pembelian->details as $detail)
                            <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900/50">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                    {{ $detail->produk->nama_produk }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    {{ $detail->rendeman ? $detail->rendeman . '%' : '-' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    {{ number_format($detail->netto, 2) }} <span class="text-xs text-gray-400">{{ $detail->satuan }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    Rp {{ number_format($detail->harga_basis, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white">
                                    Rp {{ number_format($detail->harga_netto, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-8 flex gap-3">
                <a href="{{ route('pembelian.cetaknota', $pembelian->id) }}" target="_blank">
                    <x-button type="secondary" class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Cetak Nota
                    </x-button>
                </a>
                <a href="{{ route('pembelians.index') }}">
                    <x-button type="primary">Kembali ke Daftar</x-button>
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>