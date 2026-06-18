<x-layouts.app>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Dashboard') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <a href="{{ route('pembelians.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $title ?? __('Pembelian') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-500 dark:text-gray-400">{{ __('Edit') }}</span>
    </div>

    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Edit {{ $title }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $subheading ?? __('Ubah detail pembelian di bawah ini') }}</p>
        </div>
        <div class="flex gap-2">
            <x-button type="primary" form="pembelianForm">Simpan Perubahan</x-button>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-5">
            <form action="{{ route('pembeliandetails.update', $pembelian) }}" method="POST" id="pembelianForm">
                @csrf
                @method('PUT')

                <input type="hidden" name="pembelian_id" value="{{ $pembelian->id }}">

                <div id="produk-container">
                    @foreach($pembelian->details as $index => $detail)
                    @php
                        
                        $isJual = $detail->tipe_transaksi_pembelian === 'jual';
                    @endphp

                    <div class="produk-row border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
                        <div class="flex flex-col gap-5">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Produk</label>
                                    <select name="produk_id[]" class="produk-select block w-full border-gray-300 p-2 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">Pilih Produk</option>
                                        @foreach($produks as $produk)
                                        @if($produk->id == $detail->produk_id)
                                        <option value="{{ $produk->id }}"
                                            data-satuan="{{ $produk->satuan }}"
                                            data-harga="{{ $produk->harga_basis_pembelian }}"
                                            data-produk-tipe="{{ $produk->nama_produk }}" selected>
                                            {{ $produk->nama_produk }}
                                        </option>

                                        @else
                                        <option value="{{ $produk->id }}"
                                            data-satuan="{{ $produk->satuan }}"
                                            data-harga="{{ $produk->harga_basis_pembelian }}"
                                            data-produk-tipe="{{ $produk->nama_produk }}">
                                            {{ $produk->nama_produk }}
                                        </option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe Pembelian</label>
                                    <select name="tipe_pembelian[]" class="tipe-select block w-full border-gray-300 p-2 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        @if($isJual)
                                        <option value="titip">Titip</option>
                                        <option value="jual" selected>Jual</option>
                                        @else
                                        <option value="titip" selected>Titip</option>
                                        <option value="jual">Jual</option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="container-netto">
                                    <x-forms.input label="Netto" name="netto[]" type="number" class="input-netto" value="{{ $detail->netto }}" />
                                    <span class="text-xs text-gray-500 label-satuan block mt-1">{{ $detail->satuan }}</span>
                                </div>

                                <div class="container-rendeman {{ $isJual ? '' : 'hidden' }}">
                                    <x-forms.input label="Rendeman (%)" name="rendeman[]" type="number" class="input-rendeman" min="0" max="100" step="0.01" value="{{ $detail->rendeman }}" />
                                </div>

                                <div class="container-bobot {{ ($isJual && $detail->produk->nama_produk === 'Lada') ? '' : 'hidden' }}">
                                    <x-forms.input label="Bobot" name="bobot[]" type="number" class="input-bobot" min="0" max="100" step="0.01" value="{{ $detail->bobot }}" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="container-harga_basis_master {{ $isJual ? '' : 'hidden' }}">
                                    <x-forms.input label="Harga Basis Master" name="harga_basis_pembelian[]" type="number" class="input-harga-basis" value="{{ $detail->harga_basis_pembelian }}" />
                                </div>

                                <div class="container-harga_beli {{ ($isJual && $detail->produk->nama_produk === 'Lada') ? '' : 'hidden' }}">
                                    <x-forms.input label="Harga Beli" name="harga_beli[]" type="number" class="input-harga-beli" value="{{ $detail->harga_beli ?? 0 }}" />
                                    <p class="text-xs text-gray-500 mt-1">*(harga basis + 5%) + bobot</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="container-harga {{ $isJual ? '' : 'hidden' }}">
                                    <x-forms.input label="Harga" name="harga[]" type="number" class="input-harga" value="{{ $detail->harga }}" />
                                    <p class="text-xs text-gray-500 mt-1">*Harga Basis Master x rendeman %</p>
                                </div>

                                <div class="container-harga_netto {{ $isJual ? '' : 'hidden' }}">
                                    <x-forms.input label="Jumlah Uang" name="harga_netto[]" type="number" class="input-harga-netto" value="{{ $detail->harga_netto }}" />
                                    <p class="text-xs text-gray-500 mt-1">*Harga x netto</p>
                                </div>
                            </div>

                        </div>

                        <div class="flex gap-2 mt-4">
                            <button type="button" class="btn-hitung bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm transition-colors">Hitung</button>
                            <button type="button" class="btn-remove {{ $loop->first ? 'hidden' : '' }} bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition-colors">Hapus Produk</button>
                        </div>
                    </div>
                    @endforeach
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
        let pembeliandetails = @json($pembelian->details);
        console.log(pembeliandetails);

        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('produk-container');
            const addButton = document.getElementById('add-produk');

            // 1. Fungsi Event Listener Logika Baris
            function handleRowLogic(row) {
                const select = row.querySelector('.produk-select');
                const tipe = row.querySelector('.tipe-select');

                const hargaDiv = row.querySelector('.container-harga');
                const rendemanDiv = row.querySelector('.container-rendeman');
                const bobotDiv = row.querySelector('.container-bobot');
                const hargaBasisDiv = row.querySelector('.container-harga_basis_master');
                const hargaBeliDiv = row.querySelector('.container-harga_beli');
                const hargaNettoDiv = row.querySelector('.container-harga_netto');
                const labelSatuan = row.querySelector('.label-satuan');

                const inputRendeman = row.querySelector('input[name="rendeman[]"]');
                const inputNetto = row.querySelector('input[name="netto[]"]');
                const inputBobot = row.querySelector('input[name="bobot[]"]');
                const inputHargaMaster = row.querySelector('input[name="harga[]"]');
                const inputHargaBeli = row.querySelector('input[name="harga_beli[]"]');
                const inputHargaBasisPembelian = row.querySelector('input[name="harga_basis_pembelian[]"]');
                const inputJumlahUang = row.querySelector('input[name="harga_netto[]"]');

                // Event ketika Tipe Pembelian berubah
                tipe.addEventListener('change', function() {
                    [hargaDiv, rendemanDiv, bobotDiv, hargaBasisDiv, hargaBeliDiv, hargaNettoDiv].forEach(el => el?.classList.add('hidden'));

                    if (tipe.value === "jual") {
                        [hargaDiv, rendemanDiv, hargaBasisDiv, hargaBeliDiv, hargaNettoDiv].forEach(el => el?.classList.remove('hidden'));

                        const selectedOption = select.options[select.selectedIndex];
                        const productType = selectedOption.getAttribute('data-produk-tipe') || '';
                        if (productType === "Lada" && bobotDiv) {
                            bobotDiv.classList.remove('hidden');
                        }
                    }
                });

                // Event ketika Produk berubah
                select.addEventListener('change', function() {
                    const selectedOption = select.options[select.selectedIndex];
                    const satuan = selectedOption.getAttribute('data-satuan') || '';
                    const hargaBasisMaster = parseFloat(selectedOption.getAttribute('data-harga')) || 0;
                    const productType = selectedOption.getAttribute('data-produk-tipe') || '';

                    if (labelSatuan) labelSatuan.textContent = satuan;
                    if (inputHargaBasisPembelian) inputHargaBasisPembelian.value = hargaBasisMaster;

                    if (bobotDiv) bobotDiv.classList.add('hidden');
                    if (productType === "Lada" && tipe.value === "jual" && bobotDiv) {
                        bobotDiv.classList.remove('hidden');
                    }

                    // Reset values on product change
                    if (inputBobot) inputBobot.value = 0;
                    if (inputHargaMaster) inputHargaMaster.value = 0;
                    if (inputNetto) inputNetto.value = 0;
                    if (inputJumlahUang) inputJumlahUang.value = 0;
                    if (inputRendeman) inputRendeman.value = 0;
                    if (inputHargaBeli) inputHargaBeli.value = 0;
                });

                function eksekusiKalkulasi() {
                    const selectedOption = select.options[select.selectedIndex];
                    const productType = selectedOption.getAttribute('data-produk-tipe') || 'standar';
                    const netto = parseFloat(inputNetto.value) || 0;
                    let hargaBasisMaster = parseFloat(inputHargaBasisPembelian.value) || 0;

                    if (productType === 'Kopi') {
                        const rendeman = parseFloat(inputRendeman.value) || 0;
                        const hasilHarga = (hargaBasisMaster * (rendeman / 100));
                        inputHargaMaster.value = Math.round(hasilHarga);
                    } else if (productType === 'Lada') {
                        const rendeman = parseFloat(inputRendeman.value) || 0;
                        const bobot = parseFloat(inputBobot.value) || 0;
                        const hasilHarga = (hargaBasisMaster * (rendeman / 100));

                        const hargaBeli = hargaBasisMaster + (hargaBasisMaster * (5 / 100)) + bobot;
                        if (inputHargaBeli) inputHargaBeli.value = Math.round(hargaBeli);
                        inputHargaMaster.value = Math.round(hasilHarga);
                    } else {
                        inputHargaMaster.value = Math.round(hargaBasisMaster);
                    }

                    inputJumlahUang.value = Math.round(inputHargaMaster.value * netto);
                }

                // Attach remove event natively to static rows if they have the button visible
                const removeBtn = row.querySelector('.btn-remove');
                if (removeBtn) {
                    removeBtn.onclick = function() {
                        const activeRows = container.querySelectorAll('.produk-row');
                        if (activeRows.length > 1) {
                            row.remove();
                        } else {
                            alert('Minimal harus ada satu produk.');
                        }
                    };
                }
            }

            // Global delegation untuk tombol Hitung
            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-hitung')) {
                    const row = e.target.closest('.produk-row');
                    if (row) {
                        // Jalankan kalkulasi internal row tersebut
                        // Kita bisa trigger programmatically via function di handleRowLogic, 
                        // namun agar simpel, trigger event khusus atau panggil fungsinya.
                    }
                }
            });

            // Inisialisasi logika ke semua baris yang berhasil di-render oleh Blade
            container.querySelectorAll('.produk-row').forEach(row => {
                handleRowLogic(row);

                // Override khusus tombol hitung per row agar bekerja akurat
                row.querySelector('.btn-hitung').addEventListener('click', function() {
                    // Re-calculate context logic inside handleRowLogic context
                });
            });

            // Perbaikan trigger hitung internal: definisikan ulang fungsi jalankan kalkulasi secara dinamis saat klik
            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-hitung')) {
                    const row = e.target.closest('.produk-row');
                    const select = row.querySelector('.produk-select');
                    const selectedOption = select.options[select.selectedIndex];
                    const productType = selectedOption.getAttribute('data-produk-tipe') || 'standar';

                    const inputNetto = row.querySelector('input[name="netto[]"]');
                    const inputRendeman = row.querySelector('input[name="rendeman[]"]');
                    const inputBobot = row.querySelector('input[name="bobot[]"]');
                    const inputHargaMaster = row.querySelector('input[name="harga[]"]');
                    const inputHargaBeli = row.querySelector('input[name="harga_beli[]"]');
                    const inputHargaBasisPembelian = row.querySelector('input[name="harga_basis_pembelian[]"]');
                    const inputJumlahUang = row.querySelector('input[name="harga_netto[]"]');

                    const netto = parseFloat(inputNetto.value) || 0;
                    let hargaBasisMaster = parseFloat(inputHargaBasisPembelian.value) || 0;

                    if (productType === 'Kopi') {
                        const rendeman = parseFloat(inputRendeman.value) || 0;
                        inputHargaMaster.value = Math.round(hargaBasisMaster * (rendeman / 100));
                    } else if (productType === 'Lada') {
                        const rendeman = parseFloat(inputRendeman.value) || 0;
                        const bobot = parseFloat(inputBobot.value) || 0;
                        inputHargaMaster.value = Math.round(hargaBasisMaster * (rendeman / 100));
                        if (inputHargaBeli) {
                            inputHargaBeli.value = Math.round(hargaBasisMaster + (hargaBasisMaster * 0.05) + bobot);
                        }
                    } else {
                        inputHargaMaster.value = Math.round(hargaBasisMaster);
                    }
                    inputJumlahUang.value = Math.round(inputHargaMaster.value * netto);
                }
            });


            // 3. Logika Tambah Baris Baru (Clone Baris Pertama)
            addButton.addEventListener('click', function() {
                const rows = container.querySelectorAll('.produk-row');
                const newRow = rows[0].cloneNode(true);

                // Reset nilai input dan visibilitas kontainer di baris kloningan baru
                newRow.querySelectorAll('input').forEach(input => input.value = '');
                newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);

                // Sembunyikan field kondisional untuk baris baru
                ['.container-rendeman', '.container-bobot', '.container-harga_basis_master', '.container-harga_beli', '.container-harga', '.container-harga_netto'].forEach(selector => {
                    newRow.querySelector(selector)?.classList.add('hidden');
                });
                newRow.querySelector('.label-satuan').textContent = '';

                // Tampilkan kembali tombol hapus produk untuk baris baru
                const newRemoveBtn = newRow.querySelector('.btn-remove');
                if (newRemoveBtn) {
                    newRemoveBtn.classList.remove('hidden');
                }

                handleRowLogic(newRow);
                container.appendChild(newRow);
            });
        });
    </script>

    <style>
        .hidden {
            display: none;
        }
    </style>
</x-layouts.app>