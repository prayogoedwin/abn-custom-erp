<x-layouts.app>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Dashboard') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <a href="{{ route('pembelians.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $title ?? __('Pembelian') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-500 dark:text-gray-400">{{ $title ?? __('Create') }}</span>
    </div>

    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Create {{ $title }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $subheading ?? __('Fill in the details below') }}</p>
        </div>
        <div class="flex gap-2">

            <x-button type="primary" form="pembelianForm">Lanjut</x-button>

        </div>
    </div>


    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-5">
            <form action="{{ route($tablename . '.update') }}" method="POST" id="pembelianForm">
                @csrf
                <input type="hidden" name="pembelian_id" value="{{ $pembelian_id }}">

                <div id="produk-container">
                    <div class="produk-row border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
                        <div class="flex flex-col gap-5">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Produk</label>
                                    <select name="produk_id[]" class="produk-select block w-full border-gray-300 p-2 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
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
                                    <select name="tipe_pembelian[]" class="tipe-select block w-full border-gray-300 p-2 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="titip">Titip</option>
                                        <option value="jual">Jual</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="container-netto">
                                    <x-forms.input label="Netto" name="netto[]" type="number" class="input-netto" />
                                    <span class="text-xs text-gray-500 label-satuan block mt-1"></span>
                                </div>

                                <div class="container-rendeman hidden">
                                    <x-forms.input label="Rendeman (%)" name="rendeman[]" type="number" class="input-rendeman" min="0" max="100" step="0.01" />
                                </div>

                                <div class="container-bobot hidden">
                                    <x-forms.input label="Bobot" name="bobot[]" type="number" class="input-bobot" min="0" max="100" step="0.01" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="container-harga_basis_master hidden">
                                    <x-forms.input label="Harga Basis Master" name="harga_basis_pembelian[]" type="number" class="input-harga-basis" />
                                </div>

                                <div class="container-harga_beli hidden">
                                    <x-forms.input label="Harga Beli" name="harga_beli[]" type="number" class="input-harga-beli" />
                                    <p class="text-xs text-gray-500 mt-1">*(harga basis + 5%) + bobot</p>

                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="container-harga hidden">
                                    <x-forms.input label="Harga" name="harga[]" type="number" class="input-harga" />
                                    <p class="text-xs text-gray-500 mt-1">*Harga Basis Master x rendeman %</p>
                                </div>

                                <div class="container-harga_netto hidden">
                                    <x-forms.input label="Jumlah Uang" name="harga_netto[]" type="number" class="input-harga-netto" />
                                    <p class="text-xs text-gray-500 mt-1">*Harga x netto</p>
                                </div>
                            </div>

                        </div>

                        <div class="flex gap-2 mt-4">
                            <button type="button" class="btn-hitung bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm transition-colors">Hitung</button>
                            <button type="button" class="btn-remove hidden bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition-colors">Hapus Produk</button>
                        </div>
                    </div>
                </div>

                <button type="button" id="add-produk" class="mb-4 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded shadow transition-colors">
                    + Tambah Produk Lain
                </button>

                <div class="flex gap-3 mt-3 border-t pt-4">

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
                const tipe = row.querySelector('.tipe-select');
                const nettoDiv = row.querySelector('.container-netto');
                const hargaDiv = row.querySelector('.container-harga');
                const rendemanDiv = row.querySelector('.container-rendeman');
                const bobotDiv = row.querySelector('.container-bobot');
                const hargaBasisDiv = row.querySelector('.container-harga_basis_master');
                const hargaBeliDiv = row.querySelector('.container-harga_beli');
                const hargaNettoDiv = row.querySelector('.container-harga_netto');
                const appendSatuan = row.querySelector('.text-gray-900');
                const hiddenInputSatuan = row.querySelector('.input-satuan');
                const removeBtn = row.querySelector('.btn-remove');


                // Field Harga-Harga
                const inputRendeman = row.querySelector('input[name="rendeman[]"]');
                const inputNetto = row.querySelector('input[name="netto[]"]');
                const inputBobot = row.querySelector('input[name="bobot[]"]');

                const inputHargaMaster = row.querySelector('input[name="harga[]"]');
                const inputHargaBeli = row.querySelector('input[name="harga_beli[]"]');
                const inputHargaBasisPembelian = row.querySelector('input[name="harga_basis_pembelian[]"]');
                const inputJumlahUang = row.querySelector('input[name="harga_netto[]"]');
                const inputSatuan = row.querySelector('input[name="satuan[]"]');

                tipe.addEventListener('change', function() {
                    if (hargaDiv) hargaDiv.classList.add('hidden');
                    if (rendemanDiv) rendemanDiv.classList.add('hidden');
                    if (bobotDiv) bobotDiv.classList.add('hidden');
                    if (hargaBasisDiv) hargaBasisDiv.classList.add('hidden');
                    if (hargaBeliDiv) hargaBeliDiv.classList.add('hidden');
                    if (hargaNettoDiv) hargaNettoDiv.classList.add('hidden');


                    const selectedTipe = tipe.options[tipe.selectedIndex];
                    console.log(selectedTipe.value);
                    if (selectedTipe.value == "jual") {
                        if (hargaDiv) hargaDiv.classList.remove('hidden');
                        if (rendemanDiv) rendemanDiv.classList.remove('hidden');
                        if (hargaBasisDiv) hargaBasisDiv.classList.remove('hidden');
                        if (hargaBeliDiv) hargaBeliDiv.classList.remove('hidden');
                        if (hargaNettoDiv) hargaNettoDiv.classList.remove('hidden');

                        const selectedOption = select.options[select.selectedIndex];
                        const productType = selectedOption.getAttribute('data-produk-tipe') || 'standar';

                        if (productType === "Lada") {
                            if (bobotDiv) bobotDiv.classList.remove('hidden');
                        }

                    }

                });

                select.addEventListener('change', function() {
                    const selectedOption = select.options[select.selectedIndex];
                    const val = selectedOption.value;
                    const satuan = selectedOption.getAttribute('data-satuan');
                    const hargaBasisMaster = parseFloat(selectedOption.getAttribute('data-harga')) || 0;

                    const productType = selectedOption.getAttribute('data-produk-tipe');


                    if (bobotDiv) bobotDiv.classList.add('hidden');

                    if (productType === "Lada") {
                        const selectedTipe = tipe.options[tipe.selectedIndex];
                        console.log(selectedTipe.value);
                        if (selectedTipe.value == "jual") {
                            if (bobotDiv) bobotDiv.classList.remove('hidden');
                        }
                    }


                    inputHargaBasisPembelian.value = hargaBasisMaster;

                    console.log(satuan, hargaBasisMaster, productType);


                    inputBobot.value = 0;
                    inputHargaMaster.value = 0;
                    inputNetto.value = 0;
                    inputJumlahUang.value = 0;
                    inputRendeman.value = 0;
                    inputHargaBeli.value = 0;
                });

                function eksekusiKalkulasi() {
                    const selectedOption = select.options[select.selectedIndex];
                    const productType = selectedOption.getAttribute('data-produk-tipe') || 'standar';
                    const netto = parseFloat(inputNetto.value) || 0;


                    let hargaBasisMaster = parseFloat(inputHargaBasisPembelian.value) || 0;;

                    if (productType === 'Kopi') {
                        const rendeman = parseFloat(inputRendeman.value) || 0;
                        const hasilHarga = (hargaBasisMaster * (rendeman / 100));
                        inputHargaMaster.value = Math.round(hasilHarga);
                        console.log('kopppi');
                    } else if (productType === 'Lada') {
                        console.log('laddddaa');
                        const rendeman = parseFloat(inputRendeman.value) || 0;
                        const bobot = parseFloat(inputBobot.value) || 0;
                        const hasilHarga = (hargaBasisMaster * (rendeman / 100));

                        //Harga Beli = (harga basis + 5% atau kurangi 5%) + bobot ATAU (harga basis + 5% atau kurangi 5%) - bobot 
                        const hargaBeli = hargaBasisMaster + (hargaBasisMaster * (5 / 100)) + bobot;
                        inputHargaBeli.value = Math.round(hargaBeli);
                        inputHargaMaster.value = Math.round(hasilHarga);
                    } else {
                        const hasilHarga = hargaBasisMaster;
                        inputHargaMaster.value = Math.round(hasilHarga);
                    }

                    inputJumlahUang.value = Math.round(inputHargaMaster.value * netto)


                }


                container.addEventListener('click', function(e) {
                    const target = e.target;
                    const row = target.closest('.produk-row');
                    if (!row) return;

                    // Tombol Hitung ditekan
                    if (target.classList.contains('btn-hitung')) {
                        eksekusiKalkulasi();
                    }


                });
            }




            // 2. Inisialisasi baris pertama
            handleRowLogic(container.querySelector('.produk-row'));

            // Listener Tombol Hitung


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
                const newRemoveBtn = newRow.querySelector('.btn-remove');
                newRemoveBtn.classList.remove('hidden');
                newRemoveBtn.addEventListener('click', () => newRow.remove());

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