<x-layouts.app>
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
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $subheading ?? __('Fill in the details below2') }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-5">
            <form action="{{ route($tablename . '.jualstore') }}" method="POST" id="pembelianForm">
                @csrf
                <input type="hidden" name="pembelian_id" value="{{ $pembelian_id }}">

                <div id="produk-container">
                    <div class="produk-row rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50/60 dark:bg-gray-900/30 p-4 mb-5">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Produk</label>

                                <select name="produk_id[]" class="produk-select block w-full border-gray-300 p-2 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">Pilih Produk</option>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->id }}"
                                        data-satuan="{{ $produk->satuan }}"
                                        data-harga-basis="{{ $produk->harga_basis_pembelian }}"
                                        data-produk-tipe="{{ strtolower($produk->nama_produk) === 'kopi' ? 'kopi' : (strtolower($produk->nama_produk) === 'lada' ? 'lada' : 'standar') }}">
                                        {{$produk->nama_produk}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="container-satuan hidden">
                                <input
                                    class="input-satuan"
                                    name="satuan[]" />
                            </div>

                            <div class="container-netto hidden">
                                <x-forms.input append="satuan" label="Netto" name="netto[]" type="number" />
                            </div>

                            <div class="container-rendeman hidden">
                                <x-forms.input append="%" label="Rendeman" name="rendeman[]" type="number" />
                            </div>

                            <div class="container-bobot hidden">
                                <x-forms.input prepend="Rp." label="Bobot" name="bobot[]" type="number" />
                            </div>
                        </div>
                        <div class="container-hargas hidden grid grid-cols-1 md:grid-cols-4 gap-4">

                            <div class="container-harga-basis">
                                <x-forms.input readonly="true" prepend="Rp." label="Harga Basis Master" name="harga_basis[]" type="number" />
                            </div>
                            <div class="container-harga">
                                <x-forms.input readonly="true" prepend="Rp." label="Harga" name="harga[]" type="number" />
                            </div>
                            <div class="container-harga-basis-pembelian">
                                <x-forms.input prepend="Rp." label="Harga Beli" name="harga_basis_pembelian[]" type="number" />
                            </div>
                            <div class="container-harga-netto">
                                <x-forms.input readonly="true" prepend="Rp." label="Harga Netto" name="harga_netto[]" type="number" />
                            </div>

                        </div>

                        <div class="container-button-hitung hidden mt-3 flex items-center justify-between gap-3">
                            <button type="button" class="btn-hitung inline-flex items-center rounded-md bg-amber-500 px-3 py-2 text-sm font-semibold text-white hover:bg-amber-600">
                                Hitung Harga
                            </button>
                            <button type="button" class="btn-remove hidden inline-flex items-center rounded-md border border-red-400 px-3 py-2 text-sm font-medium text-red-300 hover:bg-red-900/20">
                                Hapus Produk
                            </button>
                        </div>
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
                const hargasDiv = row.querySelector('.container-hargas');
                const rendemanDiv = row.querySelector('.container-rendeman');
                const bobotDiv = row.querySelector('.container-bobot');
                const appendSatuan = row.querySelector('.text-gray-900');
                const hiddenInputSatuan = row.querySelector('.input-satuan');
                const btnHitungDiv = row.querySelector('.container-button-hitung');
                const btnHitung = row.querySelector('.btn-hitung');
                const removeBtn = row.querySelector('.btn-remove');


                // Field Harga-Harga
                const inputRendeman = row.querySelector('input[name="rendeman[]"]');
                const inputNetto = row.querySelector('input[name="netto[]"]');
                const inputBobot = row.querySelector('input[name="bobot[]"]');

                const inputHargaMaster = row.querySelector('input[name="harga[]"]');
                const inputHargaBasisMaster = row.querySelector('input[name="harga_basis[]"]');
                const inputHargaBasisPembelian = row.querySelector('input[name="harga_basis_pembelian[]"]');
                const inputHargaNetto = row.querySelector('input[name="harga_netto[]"]');
                const inputSatuan = row.querySelector('input[name="satuan[]"]');

                select.addEventListener('change', function() {
                    const selectedOption = select.options[select.selectedIndex];
                    const val = selectedOption.value;
                    const satuan = selectedOption.getAttribute('data-satuan');
                    const hargaBasisMaster = parseFloat(selectedOption.getAttribute('data-harga-basis')) || 0;
                    const productType = selectedOption.getAttribute('data-produk-tipe') || 'standar';

                    nettoDiv.classList.add('hidden');
                    rendemanDiv.classList.add('hidden');
                    bobotDiv.classList.add('hidden');
                    hargasDiv.classList.add('hidden');
                    btnHitungDiv.classList.add('hidden');

                    if (val !== "") {
                        nettoDiv.classList.remove('hidden');
                        if (appendSatuan) appendSatuan.textContent = satuan || '-';

                        if (hiddenInputSatuan) hiddenInputSatuan.value = satuan || '';
                        if (inputSatuan) inputSatuan.value = satuan || '';
                        hargasDiv.classList.remove('hidden');
                        btnHitungDiv.classList.remove('hidden');
                        inputHargaBasisMaster.value = hargaBasisMaster;
                        inputHargaBasisPembelian.value = '';

                        if (productType === 'lada') bobotDiv.classList.remove('hidden');
                        if (productType === 'kopi' || productType === 'lada') rendemanDiv.classList.remove('hidden');
                    }
                });

                function eksekusiKalkulasi() {
                    const selectedOption = select.options[select.selectedIndex];
                    const hargaBasisMaster = parseFloat(inputHargaBasisMaster.value) || 0;
                    const productType = selectedOption.getAttribute('data-produk-tipe') || 'standar';
                    const netto = parseFloat(inputNetto.value) || 0;

                    let rekomHargaBeli = hargaBasisMaster;

                    if (productType === 'kopi') {
                        const rendeman = parseFloat(inputRendeman.value) || 0;
                        const hasilHarga = hargaBasisMaster + (hargaBasisMaster * (rendeman / 100));
                        inputHargaMaster.value = Math.round(hasilHarga);
                        rekomHargaBeli = hasilHarga;
                    } else if (productType === 'lada') {
                        const rendeman = parseFloat(inputRendeman.value) || 0;
                        const bobot = parseFloat(inputBobot.value) || 0;
                        const hasilHarga = hargaBasisMaster + (hargaBasisMaster * (rendeman / 100));
                        inputHargaMaster.value = Math.round(hasilHarga);
                        rekomHargaBeli = hasilHarga + bobot;
                    } else {
                        const hasilHarga = hargaBasisMaster;
                        inputHargaMaster.value = Math.round(hasilHarga);
                        rekomHargaBeli = hasilHarga;
                    }

                    const hargaBeliInput = parseFloat(inputHargaBasisPembelian.value);
                    const gunakanHargaBeliInput = inputHargaBasisPembelian.value !== '' && !Number.isNaN(hargaBeliInput);
                    if (gunakanHargaBeliInput) {
                        inputHargaNetto.value = Math.round(hargaBeliInput * netto);
                    } else {
                        inputHargaNetto.value = '';
                    }
                }


                btnHitung.addEventListener('click', function(e) {
                    e.preventDefault();
                    eksekusiKalkulasi();
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

        .produk-row .container-hargas {
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px dashed rgba(148, 163, 184, 0.3);
        }
    </style>
</x-layouts.app>