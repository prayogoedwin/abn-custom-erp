<x-layouts.app>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Dashboard') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <a href="{{ route('produks.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $title ?? __('Produks') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-500 dark:text-gray-400">{{ $title ?? __('Create') }}</span>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Create {{ $title }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $subheading ?? __('Fill in the details below') }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-5">
            <form action="{{ route($tablename . '.titipstore') }}" method="POST" id="pembelianForm">
                @csrf
                <input type="hidden" name="pembelian_id" value="{{ $pembelian_id }}">

                <div id="produk-container">
                    <div class="produk-row border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Produk</label>
                                <select name="produk_id[]" class="produk-select block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">Pilih Produk</option>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->id }}" data-satuan="{{ $produk->satuan }}">
                                        {{$produk->nama_produk}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="container-netto hidden">
                                <x-forms.input append="satuan" label="Netto" name="netto[]" type="number" />
                            </div>

                            <div class="container-rendeman hidden">
                                <x-forms.input append="%" label="Rendeman" name="rendeman[]" type="number" />
                            </div>
                        </div>

                        <button type="button" class="btn-remove hidden text-red-500 text-sm mt-2">Hapus Produk</button>
                    </div>
                </div>

                <button type="button" id="add-produk" class="mb-4 bg-green-500 text-white px-4 py-2 rounded shadow">
                    + Tambah Produk Lain
                </button>

                <div class="flex gap-3 mt-3 border-t pt-4">
                    <x-button type="primary">Simpan Semua</x-button>
                    <a href="{{ route('pembelians.index') }}"><x-button type="secondary">Batal</x-button></a>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('produk-container');
            const addButton = document.getElementById('add-produk');

            // 1. Fungsi Update Visibility untuk baris tertentu
            function handleRowLogic(row) {
                const select = row.querySelector('.produk-select');
                const nettoDiv = row.querySelector('.container-netto');
                const rendemanDiv = row.querySelector('.container-rendeman');
                const appendSatuan = row.querySelector('.text-gray-900'); // Sesuai temuan Anda

                select.addEventListener('change', function() {
                    const selectedOption = select.options[select.selectedIndex];
                    const val = selectedOption.value;
                    const satuan = selectedOption.getAttribute('data-satuan');

                    // Reset
                    nettoDiv.classList.add('hidden');
                    rendemanDiv.classList.add('hidden');

                    if (val !== "") {
                        nettoDiv.classList.remove('hidden');
                        if (appendSatuan) appendSatuan.textContent = satuan || '-';

                        // Logika Rendeman
                        if (val === "1" || val === "2") {
                            rendemanDiv.classList.remove('hidden');
                        }
                    }
                });
            }

            // 2. Inisialisasi baris pertama
            handleRowLogic(container.querySelector('.produk-row'));

            // 3. Logika Tambah Baris (Clone)
            addButton.addEventListener('click', function() {
                const rows = container.querySelectorAll('.produk-row');
                const newRow = rows[0].cloneNode(true); // Clone baris pertama

                // Reset nilai di baris baru
                newRow.querySelectorAll('input').forEach(input => input.value = '');
                newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
                newRow.querySelector('.container-netto').classList.add('hidden');
                newRow.querySelector('.container-rendeman').classList.add('hidden');

                // Tampilkan tombol hapus di baris baru
                const removeBtn = newRow.querySelector('.btn-remove');
                removeBtn.classList.remove('hidden');
                removeBtn.addEventListener('click', () => newRow.remove());

                // Jalankan logic untuk baris baru
                handleRowLogic(newRow);

                container.appendChild(newRow);
            });
        });
    </script>



    <style>
        /* Tambahkan jika Tailwind .hidden belum terdefinisi atau butuh fallback */
        .hidden {
            display: none;
        }
    </style>
</x-layouts.app>