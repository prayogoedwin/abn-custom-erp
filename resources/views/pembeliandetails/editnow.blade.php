<x-layouts.app>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Breadcrumb -->
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Dashboard') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <a href="{{ route('pembelians.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $title ?? __('Pembelian') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-500 dark:text-gray-400">{{ $title ?? __('Edit') }}</span>
    </div>

    <!-- Header Section -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Edit {{ $title }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $subheading ?? __('Lengkapi detail pembelian di bawah ini') }}</p>
        </div>
        <div class="flex gap-2">
            <x-button type="primary" form="pembelianForm">Simpan / Lanjut</x-button>
        </div>
    </div>

    <!-- Container Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-5">
            <form action="{{ route($tablename . '.update', $pembelian) }}" method="POST" id="pembelianForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="pembelian_id" value="{{ $pembelian->id }}">

                <!-- Container Baris Produk -->
                <div id="produk-container"></div>

                <button type="button" id="add-produk" class="mb-4 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded shadow transition-colors">
                    + Tambah Produk Lain
                </button>

                <div class="flex gap-3 mt-3 border-t pt-4">
                    <a href="{{ route('pembelians.index') }}"><x-button type="secondary">Batal</x-button></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Template Baris Produk (Di-render via JavaScript) -->
    <template id="produk-row-template">
        <div class="produk-row border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
            <div class="flex flex-col gap-5">

                <!-- Row 1: Produk & Tipe Pembelian -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Produk</label>
                        <select name="produk_id[]" class="produk-select block w-full border-gray-300 p-2 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Pilih Produk</option>
                            @foreach($produks as $produk)
                                <option value="{{ $produk->id }}"
                                    data-satuan="{{ $produk->satuan }}"
                                    data-harga="{{ $produk->harga_basis_pembelian }}"
                                    data-produk-tipe="{{ $produk->nama_produk }}">
                                    {{ $produk->nama_produk }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe Pembelian</label>
                        <select name="tipe_pembelian[]" class="tipe-select block w-full border-gray-300 p-2 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="titip">Titip</option>
                            <option value="jual">Jual</option>
                        </select>
                    </div>
                </div>

                <!-- Row 2: Netto, Rendeman, Bobot -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="container-netto">
                        <x-forms.input label="Netto" name="netto[]" type="number" class="input-netto" />
                        <span class="text-xs text-gray-500 label-satuan block mt-1"></span>
                    </div>

                    <div class="container-rendeman hidden">
                        <x-forms.input label="Rendeman (%)" name="rendeman[]" type="number" class="input-rendeman" min="-100" max="100" step="0.01" />
                    </div>

                    <div class="container-bobot hidden">
                        <x-forms.input label="Bobot" name="bobot[]" type="number" class="input-bobot" step="0.01" />
                    </div>
                </div>

                <!-- Row 3: Harga Basis Master & Harga -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="container-harga_basis_master hidden">
                        <x-forms.input label="Harga Basis Master" name="harga_basis_pembelian[]" type="number" class="input-harga-basis" />
                    </div>

                    <div class="container-harga hidden">
                        <x-forms.input label="Harga" name="harga[]" type="number" class="input-harga" readonly="true" />
                        <p class="text-xs text-gray-500 mt-1">*(harga basis + % rendeman) + bobot</p>
                    </div>
                </div>

                <!-- Row 4: Harga Beli & Jumlah Uang -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="container-harga_beli hidden">
                        <x-forms.input label="Harga Beli" name="harga_beli[]" type="number" class="input-harga-beli" />
                        <p class="text-xs text-gray-500 mt-1">*Harga Editable</p>
                    </div>

                    <div class="container-harga_netto hidden">
                        <x-forms.input label="Jumlah Uang" name="harga_netto[]" type="number" class="input-harga-netto" />
                        <p class="text-xs text-gray-500 mt-1">*Harga Beli x netto</p>
                    </div>
                </div>

            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2 mt-4">
                <button type="button" class="btn-hitung bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm transition-colors">Hitung</button>
                <button type="button" class="btn-remove bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition-colors hidden">Hapus Produk</button>
            </div>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('produk-container');
            const template = document.getElementById('produk-row-template');
            const addButton = document.getElementById('add-produk');

            // Data detail dari controller Laravel
            const existingDetails = @json($pembelian->details ?? []);

            // -------------------------------------------------------------
            // FUNGSI UTAMA: populateDetails
            // -------------------------------------------------------------
            function populateDetails(details) {
                container.innerHTML = ''; // Clear container

                if (!details || details.length === 0) {
                    addDetailRow(); // Tambah 1 baris kosong jika data kosong
                    return;
                }

                details.forEach((data, index) => {
                    addDetailRow(data, index > 0);
                });
            }

            // Fungsi Pembantu: Menambah Satu Baris Detail
            function addDetailRow(data = {}, showRemoveButton = false) {
                const clone = template.content.cloneNode(true);
                const row = clone.querySelector('.produk-row');

                // Elements inside row
                const selectProduk = row.querySelector('.produk-select');
                const selectTipe = row.querySelector('.tipe-select');
                const inputNetto = row.querySelector('input[name="netto[]"]');
                const inputRendeman = row.querySelector('input[name="rendeman[]"]');
                const inputBobot = row.querySelector('input[name="bobot[]"]');
                const inputHargaBasis = row.querySelector('input[name="harga_basis_pembelian[]"]');
                const inputHarga = row.querySelector('input[name="harga[]"]');
                const inputHargaBeli = row.querySelector('input[name="harga_beli[]"]');
                const inputHargaNetto = row.querySelector('input[name="harga_netto[]"]');
                const btnRemove = row.querySelector('.btn-remove');
                const btnHitung = row.querySelector('.btn-hitung');
                const labelSatuan = row.querySelector('.label-satuan');

                // 1. Isikan Data Awal (jika ada)
                if (data.produk_id) selectProduk.value = data.produk_id;
                if (data.tipe_transaksi_pembelian) selectTipe.value = data.tipe_transaksi_pembelian;
                if (data.netto) inputNetto.value = data.netto;
                if (data.rendeman) inputRendeman.value = data.rendeman;
                if (data.bobot) inputBobot.value = data.bobot;
                if (data.harga_basis_pembelian) inputHargaBasis.value = data.harga_basis_pembelian;
                if (data.harga) inputHarga.value = data.harga;
                if (data.harga_beli) inputHargaBeli.value = data.harga_beli;
                if (data.harga_netto) inputHargaNetto.value = data.harga_netto;
                if (data.satuan) labelSatuan.textContent = data.satuan;

                // 2. Fungsi Tampilan & Visibilitas Field
                function updateVisibility() {
                    const isJual = selectTipe.value === 'jual';
                    const selectedOption = selectProduk.options[selectProduk.selectedIndex];
                    const productType = selectedOption ? selectedOption.getAttribute('data-produk-tipe') : '';

                    row.querySelector('.container-harga').classList.toggle('hidden', !isJual);
                    row.querySelector('.container-rendeman').classList.toggle('hidden', !isJual);
                    row.querySelector('.container-harga_basis_master').classList.toggle('hidden', !isJual);
                    row.querySelector('.container-harga_beli').classList.toggle('hidden', !isJual);
                    row.querySelector('.container-harga_netto').classList.toggle('hidden', !isJual);

                    // Khusus Bobot hanya jika tipe "jual" dan produk "Lada"
                    const isLada = productType === 'Lada';
                    row.querySelector('.container-bobot').classList.toggle('hidden', !(isJual && isLada));
                }

                // 3. Kalkulasi Otomatis
                function hitung() {
                    const selectedOption = selectProduk.options[selectProduk.selectedIndex];
                    const productType = selectedOption ? selectedOption.getAttribute('data-produk-tipe') : '';
                    
                    const netto = parseFloat(inputNetto.value) || 0;
                    const hargaBasis = parseFloat(inputHargaBasis.value) || 0;
                    const rendeman = parseFloat(inputRendeman.value) || 0;
                    const bobot = parseFloat(inputBobot.value) || 0;

                    let hargaBeliKalkulasi = hargaBasis;

                    if (productType === 'Kopi') {
                        hargaBeliKalkulasi = hargaBasis + (hargaBasis * (rendeman / 100));
                    } else if (productType === 'Lada') {
                        hargaBeliKalkulasi = hargaBasis + (hargaBasis * (rendeman / 100)) + bobot;
                    }

                    inputHarga.value = Math.round(hargaBeliKalkulasi);
                    
                    // Jika harga_beli belum diisi manual, set otomatis dari kalkulasi
                    if (!inputHargaBeli.value || inputHargaBeli.value == 0) {
                        inputHargaBeli.value = Math.round(hargaBeliKalkulasi);
                    }

                    const hargaBeli = parseFloat(inputHargaBeli.value) || 0;
                    inputHargaNetto.value = Math.round(hargaBeli * netto);
                }

                // 4. Event Listeners
                selectTipe.addEventListener('change', updateVisibility);

                selectProduk.addEventListener('change', function () {
                    const selectedOption = selectProduk.options[selectProduk.selectedIndex];
                    if (selectedOption && selectedOption.value) {
                        inputHargaBasis.value = parseFloat(selectedOption.getAttribute('data-harga')) || 0;
                    }

                    const satuan = selectedOption.getAttribute('data-satuan');

                    if (labelSatuan) labelSatuan.textContent = satuan;
                    updateVisibility();
                });

                inputHargaBeli.addEventListener('input', function () {
                    const netto = parseFloat(inputNetto.value) || 0;
                    const hargaBeli = parseFloat(inputHargaBeli.value) || 0;
                    inputHargaNetto.value = Math.round(hargaBeli * netto);
                });

                btnHitung.addEventListener('click', hitung);

                // Tombol Hapus Baris
                if (showRemoveButton) {
                    btnRemove.classList.remove('hidden');
                }
                btnRemove.addEventListener('click', function () {
                    row.remove();
                });

                // Apply awal visibilitas
                updateVisibility();

                // Append ke DOM
                container.appendChild(row);
            }

            // -------------------------------------------------------------
            // INISIALISASI
            // -------------------------------------------------------------
            populateDetails(existingDetails);

            // Listener Tambah Baris Baru
            addButton.addEventListener('click', function () {
                addDetailRow({}, true);
            });
        });
    </script>

    <style>
        .hidden {
            display: none !important;
        }
    </style>
</x-layouts.app>