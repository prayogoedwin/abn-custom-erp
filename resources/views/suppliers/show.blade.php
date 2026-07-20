<x-layouts.app>
    <!-- Breadcrumb Navigation -->
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
        <span class="text-gray-500 dark:text-gray-400">{{ __('View Details') }}</span>
    </div>

    <!-- Header & Action Buttons -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Profil Supplier: {{ $supplier->nama }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Kontak: {{ $supplier->kontak ?? '-' }} | Alamat: {{ $supplier->alamat ?? '-' }}</p>
        </div>
        <div class="flex gap-2">
            @if(auth()->user()->hasPermission('edit-' . $tablename))
            <a href="{{ route($tablename . '.edit', $supplier->id) }}">
                <x-button type="primary">{{ __('Edit Supplier') }}</x-button>
            </a>
            @endif
            <a href="{{ route($tablename . '.index') }}">
                <x-button type="secondary">{{ __('Back') }}</x-button>
            </a>
        </div>
    </div>

    <!-- Section KOTAK BESAR Ringkasan Finansial -->
    <div class="mb-6">
        <h2 class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">Ringkasan Keuangan & Kewajiban</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Sisa Kekurangan Pembelian (Hutang Toko ke Supplier) -->
            <div class="bg-red-600 rounded-lg p-5 text-white shadow-sm">
                <p class="text-red-100 text-xs font-medium uppercase tracking-wider">Kekurangan Transaksi Pembelian</p>
                <h3 class="text-2xl font-bold mt-1">Rp {{ number_format($pembelians->sum('kekurangan'), 0, ',', '.') }}</h3>
            </div>

            <!-- Sisa Hutang Cashbon (Hutang Supplier ke Toko) -->
            <div class="bg-amber-600 rounded-lg p-5 text-white shadow-sm">
                <p class="text-amber-100 text-xs font-medium uppercase tracking-wider">Sisa Hutang Cashbon</p>
                <h3 class="text-2xl font-bold mt-1">Rp {{ number_format($supplier->totalCashbon(), 0, ',', '.') }}</h3>
            </div>

            <!-- Total Titip Uang Supplier -->
            <div class="bg-blue-600 rounded-lg p-5 text-white shadow-sm">
                <p class="text-blue-100 text-xs font-medium uppercase tracking-wider">Total Titipan Uang</p>
                <h3 class="text-2xl font-bold mt-1">Rp {{ number_format($supplier->cashbons ? \App\Models\TitipSupplier::where('supplier_id', $supplier->id)->sum('nominal_titip') : 0, 0, ',', '.') }}</h3>
            </div>

            <!-- Total Ambil Uang Supplier -->
            <div class="bg-emerald-600 rounded-lg p-5 text-white shadow-sm">
                <p class="text-emerald-100 text-xs font-medium uppercase tracking-wider">Total Penarikan (Ambil)</p>
                <h3 class="text-2xl font-bold mt-1">Rp {{ number_format($supplier->cashbons ? \App\Models\AmbilSupplier::where('supplier_id', $supplier->id)->sum('nominal_ambil') : 0, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    <!-- Main Content Tabular Area -->
    <div class="space-y-6">
        
        <!-- TABLE 1: Daftar Stok Barang Titipan (Menggunakan Blade) -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Daftar Mutasi Stok Titipan Barang
            </h3>
            <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3">Nama Produk</th>
                            <th class="px-4 py-3 text-center">Tipe Stok</th>
                            <th class="px-4 py-3 text-right">Jumlah Volume</th>
                            <th class="px-4 py-3">Keterangan</th>
                            <th class="px-4 py-3 text-center">Tanggal Log</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($supplier->stokTitipans as $stok)
                        <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900/50">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                {{ $stok->produk->nama_produk ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if(strtolower($stok->tipe_stok) === 'masuk')
                                    <span class="px-2.5 py-0.5 text-xs font-semibold text-green-800 bg-green-100 rounded-full dark:bg-green-900/30 dark:text-green-400">Masuk</span>
                                @else
                                    <span class="px-2.5 py-0.5 text-xs font-semibold text-red-800 bg-red-100 rounded-full dark:bg-red-900/30 dark:text-red-400">Keluar</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">
                                {{ number_format($stok->jumlah, 2) }} <span class="text-xs text-gray-400 font-normal">{{ $stok->satuan }}</span>
                            </td>
                            <td class="px-4 py-3">
                                {{ $stok->keterangan ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center text-xs">
                                {{ $stok->created_at->format('d M Y H:i') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-400 italic">
                                Belum ada riwayat penitipan barang non-beli untuk supplier ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TABLE 2: Riwayat Transaksi Pembelian (Menggunakan JQuery DataTable) -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-4">
            <table id="dynamic-table" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>

                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">No Transaksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Grand Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>



                        
                    </tr>
                </thead>
            </table>
        </div>
    </div>


    </div>

    <!-- Assets & Scripts untuk JQuery DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.tailwindcss.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#dynamic-table').DataTable({
                
                processing: true,
                serverSide: true,
                ajax: '{{ route("suppliers.showTable", $supplier->id) }}',
                columns: [
                    { data: 'no_transaksi', name: 'no_transaksi' },
                    { data: 'total_nominal_pembelian', name: 'total_nominal_pembelian' },
                    { data: 'keterangan', name: 'keterangan' },
                    { data: 'tanggal', name: 'tanggal' },
                    { data: 'status', name: 'status' },
                ],
                language: {
                    search: "Cari Transaksi:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ transaksi",
                    infoEmpty: "Data tidak ditemukan",
                    zeroRecords: "Tidak ada transaksi pembelian yang cocok",
                    paginate: {
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                }
            });
        });
    </script>

    <style>
        /* Penyesuaian Style agar match dengan tema Tailwind gelap/terang */
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
            background-color: #fff;
        }
        .dark .dataTables_wrapper .dataTables_length select,
        .dark .dataTables_wrapper .dataTables_filter input {
            border-color: #4b5563;
            background-color: #374151;
            color: #f9fafb;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #2563eb !important;
            color: white !important;
            border: none !important;
            border-radius: 0.375rem;
        }
    </style>
</x-layouts.app>