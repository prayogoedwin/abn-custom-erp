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
        <span class="text-gray-500 dark:text-gray-400">{{ $title ?? __('Edit') }}</span>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Edit {{ $title }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $subheading ?? __('Fill in the details below2') }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-5">
            <form action="{{ route($tablename . '.titipupdate', $data) }}" method="POST" id="pembelianForm">
                @csrf
                <input type="hidden" name="pembelian_id" value="{{ $pembelian_id }}">



                <div id="produk-container">
                </div>

                <template id="row-template">
                    <div class="produk-row border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Produk</label>
                                <select name="produk_id[]" class="produk-select block w-full border-gray-300 p-2 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">Pilih Produk</option>
                                    @foreach($produks as $p)
                                    <option value="{{ $p->id }}" data-satuan="{{ $p->satuan }}" data-harga="{{ $p->harga_basis }}">
                                        {{ $p->nama_produk }}
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

                        <button type="button" class="btn-remove text-red-500 text-sm mt-2">Hapus Produk</button>
                    </div>
                </template>


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
        $olddata = @json($pembeliandetails);

        console.log('$olddata:', $olddata);




        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('produk-container');
            const template = document.getElementById('row-template');
            const addButton = document.getElementById('add-produk');

            // Fungsi utama untuk menambah baris
            function addRow(data = null) {
                const clone = template.content.cloneNode(true);
                const row = clone.querySelector('.produk-row');

                container.appendChild(clone);

                // Inisialisasi logika pada baris baru
                handleRowLogic(row, data);
            }

            function handleRowLogic(row, data = null) {
                const select = row.querySelector('.produk-select');
                const inNetto = row.querySelector('input[name="netto[]"]');
                const inRendeman = row.querySelector('input[name="rendeman[]"]');


                // Jika ada data lama, isi field-nya
                if (data) {
                    select.value = data.produk_id;
                    inNetto.value = data.netto;
                    inRendeman.value = data.rendeman;


                    // Trigger tampilan manual karena value diset lewat JS
                    row.querySelector('.container-netto').classList.remove('hidden');
                    if (data.rendeman) row.querySelector('.container-rendeman').classList.remove('hidden');

                }

                // Event Listeners
                select.addEventListener('change', () => {
                    // ... logika show/hide yang sebelumnya ...
                });

                row.querySelector('.btn-remove').addEventListener('click', () => row.remove());
            }

            // 1. Render data lama jika ada
            if ($olddata && $olddata.length > 0) {
                $olddata.forEach(item => addRow(item));
            } else {
                // 2. Jika create baru, kasih 1 baris kosong
                addRow();
            }

            // 3. Tombol tambah baris manual
            addButton.addEventListener('click', () => addRow());
        });
    </script>



    <style>
        .hidden {
            display: none;
        }
    </style>
</x-layouts.app>