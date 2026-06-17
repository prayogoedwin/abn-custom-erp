<x-layouts.app>
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Dashboard') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <a href="{{ route($tablename . '.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $title ?? __('Produks') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-500 dark:text-gray-400">{{ $title ?? __('Create') }}</span>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Create {{ $title }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $subheading ?? __('Prosses Pembayaran') }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <form action="{{ route($tablename . '.storelanjut', $data) }}" method="POST">
                @csrf
                @method('POST')

                <div class="mb-4">
                    <h3 class="text-sm font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-4">Daftar Produk</h3>
                    <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3">Produk</th>
                                    <th class="px-4 py-3">Tipe</th>
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
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                        {{ $detail->tipe_transaksi_pembelian }}
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

                    @php
                    $totalTagihan = $pembelian->details->sum('harga_netto');
                    // Memanggil method totalCashbon() dari model Supplier Anda
                    $totalCashbonSupplier = $pembelian->supplier ? $pembelian->supplier->totalCashbon() : 0;
                    @endphp

                    <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-gray-100 dark:border-gray-700 space-y-4">
                        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase">Ringkasan Pembayaran</h3>

                        <div class="space-y-2 text-sm border-b border-gray-200 dark:border-gray-700 pb-3">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Total Netto Keseluruhan:</span>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($pembelian->details->sum('netto'), 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Total Tagihan Awal:</span>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-red-500">
                                <span>Total Cashbon Supplier:</span>
                                <span class="font-semibold" id="label-total-cashbon" data-value="{{ $supplier->totalCashbon() }}">Rp {{ number_format($supplier->totalCashbon(), 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Potong Cashbon</label>
                                <input type="number" name="potong_bon" id="potong_bon" min="0" value="0"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                <span class="text-[11px] text-gray-500 mt-1 block">Sisa Bon: <strong id="sisa-bon-live">Rp {{ number_format($supplier->totalCashbon(), 0, ',', '.') }}</strong></span>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Titip Uang/Modal</label>
                                <input type="number" name="titip" id="titip" min="0" value="0"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>

                        <div class="flex justify-between text-xl border-t border-gray-200 dark:border-gray-700 pt-3 mt-2">
                            <span class="font-bold text-gray-700 dark:text-gray-300">Total Dibayarkan:</span>
                            <span class="font-black text-indigo-600 dark:text-indigo-400" id="total-tagihan-akhir" data-awal="{{ $totalTagihan }}">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <x-forms.input label="Ambil Tunai" name="ambil_tunai" type="number" />
                </div>
                <div class="mb-4">
                    <x-forms.input label="Ambil Transfer" name="ambil_transfer" type="number"/>
                </div>



                <div class="mt-6 flex flex-wrap gap-3 border-t border-gray-200 pt-4 dark:border-gray-700">
                    <x-button type="primary">Simpan</x-button>
                    <a href="{{ route('pembelians.cetaknota', $data->id) }}" target="_blank">
                        <x-button type="secondary">Cetak Nota</x-button>
                    </a>
                    <a href="{{ route($tablename . '.index') }}">
                        <x-button type="secondary">Batal</x-button>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputPotongBon = document.getElementById('potong_bon');
            const inputTitip = document.getElementById('titip');

            const labelSisaBonLive = document.getElementById('sisa-bon-live');
            const labelTotalTagihanAkhir = document.getElementById('total-tagihan-akhir');

            // Mengambil data angka murni dari attribute data
            const totalCashbonAwal = parseFloat(document.getElementById('label-total-cashbon').getAttribute('data-value')) || 0;
            const tagihanAwal = parseFloat(labelTotalTagihanAkhir.getAttribute('data-awal')) || 0;

            function formatRupiah(angka) {
                return 'Rp ' + new Intl.NumberFormat('id-ID', {
                    maximumFractionDigits: 0
                }).format(angka);
            }

            function hitungOtomatis() {
                let nilaiPotongBon = parseFloat(inputPotongBon.value) || 0;
                let nilaiTitip = parseFloat(inputTitip.value) || 0;

                // Validasi agar input potong bon tidak melebihi bon yang ada / tagihan awal
                if (nilaiPotongBon > totalCashbonAwal) {
                    nilaiPotongBon = totalCashbonAwal;
                    inputPotongBon.value = totalCashbonAwal;
                }
                

                // 1. Hitung Sisa Bon
                const sisaBon = totalCashbonAwal - nilaiPotongBon;
                labelSisaBonLive.textContent = formatRupiah(sisaBon);

                // 2. Hitung Total yang Harus Dibayarkan Bersih ke Supplier
                // Rumus: Tagihan Awal - Potongan Cashbon - Titipan Uang
                const tagihanAkhir = tagihanAwal - nilaiPotongBon - nilaiTitip;
                labelTotalTagihanAkhir.textContent = formatRupiah(tagihanAkhir);
            }

            // Pasang event listener ketik (input)
            inputPotongBon.addEventListener('input', hitungOtomatis);
            inputTitip.addEventListener('input', hitungOtomatis);
        });
    </script>
</x-layouts.app>