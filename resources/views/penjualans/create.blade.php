<x-layouts.app>
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Dashboard') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <a href="{{ route($tablename . '.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $title ?? __('Produks') }}</a>
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

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6 min-h-[500px]">
            <form action="{{ route('penjualans.store') }}" method="POST" class="max-w-3xl">
                @csrf
                @method('POST')

                <div class="mb-5 rounded-lg border border-blue-200 bg-blue-50/70 px-4 py-3 text-sm text-blue-700 dark:border-blue-900/60 dark:bg-blue-950/30 dark:text-blue-200">
                    Pilih Pengiriman, lalu lanjut ke penjualan detail.
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Pengiriman
                    </label>

                    <select
                        name="pengiriman_id"
                        id="pengiriman-select"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/40 searchable-select">

                        
                        @foreach($pengirimans as $option)
                        <option value="{{ $option->id }}">
                            {{ $option->no_transaksi . ' - ' . $option->nopol . ' - ' . $option->customer->nama; }}
                        </option>
                        @endforeach
                    </select>

                    @error('pengiriman_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quick info dari pengiriman -->
                <div id="pengiriman-info" class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg hidden">
                    <h3 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-2">Informasi Pengiriman</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Customer:</span>
                            <span id="customer-name" class="ml-2 font-medium text-gray-800 dark:text-gray-200"></span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">No. Polisi:</span>
                            <span id="nopol" class="ml-2 font-medium text-gray-800 dark:text-gray-200"></span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">No. Transaksi:</span>
                            <span id="no-transaksi" class="ml-2 font-medium text-gray-800 dark:text-gray-200"></span>
                        </div>
                    </div>
                </div>

                <div id="produk-list-container" class="hidden">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Detail Produk</h3>
                    <div id="produk-list"></div>
                </div>

                <template id="row-template">
                    <div class="produk-row border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
                        <!-- Quick info dari pengiriman_details -->
                        <div class="mb-4 p-3 bg-blue-50/50 dark:bg-blue-950/20 rounded-lg">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Produk:</span>
                                    <span class="produk-nama ml-2 font-medium text-gray-800 dark:text-gray-200"></span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Jml/Karung:</span>
                                    <span class="produk-jumlah-per-karung ml-2 font-medium text-gray-800 dark:text-gray-200"></span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Jml Karung:</span>
                                    <span class="produk-jumlah-karung ml-2 font-medium text-gray-800 dark:text-gray-200"></span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Bruto:</span>
                                    <span class="produk-bruto ml-2 font-medium text-gray-800 dark:text-gray-200"></span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Tara:</span>
                                    <span class="produk-tara ml-2 font-medium text-gray-800 dark:text-gray-200"></span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Netto:</span>
                                    <span class="produk-netto ml-2 font-medium text-gray-800 dark:text-gray-200"></span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Satuan:</span>
                                    <span class="produk-satuan ml-2 font-medium text-gray-800 dark:text-gray-200"></span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div hidden>
                                <input type="text" name="produk_id[]" class="produk-id">
                            </div>
                            <div hidden>
                                <input type="text" name="pengiriman_detail_id[]" class="pengiriman-detail-id">
                            </div>

                            <div hidden>
                                <input type="text" name="netto_pengiriman[]" class="netto-pengiriman" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe</label>
                                <select name="tipe[]" class="tipe-select produk-select block w-full border-gray-300 p-2 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="Jual">Jual</option>
                                    <option value="Titip">Titip</option>
                                </select>
                            </div>



                            <div class="container-netto">
                                <x-forms.input label="Netto" name="netto[]" type="decimal" required />
                            </div>

                            <div class="container-selisih">
                                <x-forms.input label="Selisih" name="selisih[]" type="number" step="any" readonly=true required/>
                            </div>

                            <div class="container-basis_harga">
                                <x-forms.input label="Basis Harga" name="basis_harga[]" type="number" step="any" />
                            </div>

                            <div class="container-rendeman">
                                <x-forms.input label="% Rendeman" name="rendeman[]" type="number" min="0" max="100" step="any" />
                            </div>

                            <div class="container-bobot hidden">
                                <x-forms.input label="Bobot" name="bobot[]" type="number" step="any" />
                            </div>

                            <div class="container-harga_jadi">
                                <x-forms.input label="Harga Jadi" name="harga_jadi[]" type="number" step="any" />
                            </div>

                            <div class="container-sub_total">
                                <x-forms.input label="Sub Total" name="sub_total[]" type="number" step="any" readonly="true" />
                                <p class="text-xs text-gray-500 mt-1">netto * Harga jadi</p>
                            </div>

                            <div class="container-pph hidden">
                                <x-forms.input label="PPH" name="pph[]" type="number" step="any" />
                            </div>

                            <div class="container-ppn">
                                <x-forms.input label="PPN" name="ppn[]" type="number" step="any" />
                                <p class="text-xs text-gray-500 mt-1">sub total * 12 %</p>
                            </div>

                            <input type="hidden" name="pph[]" class="pph-input" value="0" />

                            <div class="container-nominal_akhir">
                                <x-forms.input label="Nominal Akhir" name="nominal_akhir[]" type="number" step="any" readonly=true />
                                <p class="text-xs text-gray-500 mt-1">sub total + ppn</p>

                            </div>

                            <div class="container-button_calculate">
                                <button type="button" class="btn-calculate mt-6 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Hitung
                                </button>
                            </div>
                        </div>

                        <button type="button" class="btn-remove hidden text-red-500 text-sm mt-2">Hapus Produk</button>
                    </div>
                </template>

                <div class="mt-2 flex gap-3 border-t border-gray-200 pt-4 dark:border-gray-700">
                    <x-button type="primary">{{ __('Create') }}</x-button>
                    <a href="{{ route($tablename . '.index') }}" class="text-white font-medium py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors flex items-center justify-center cursor-pointer bg-gray-600 hover:bg-gray-700 focus:ring-gray-500 dark:bg-gray-500 dark:hover:bg-gray-600">
                        {{ __('Batal') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const pengirimanDetails = @json($pengirimandetails); // all pengirimandetails in db
        const Pengiriman = @json($pengirimans); // all pengiriman in db
        console.log('Pengiriman:', Pengiriman);
        const Produks = @json($produks); // all produks in db

        document.addEventListener('DOMContentLoaded', function() {
            const pengirimanSelect = document.getElementById('pengiriman-select');
            const pengirimanInfo = document.getElementById('pengiriman-info');
            const produkListContainer = document.getElementById('produk-list-container');
            const produkList = document.getElementById('produk-list');
            const rowTemplate = document.getElementById('row-template');

            // Function to calculate values for a row
            function calculateRow(row) {
                const nettoDiv = row.querySelector('.container-netto');
                const nettoInput = nettoDiv.querySelector('input');
                const rendemanDiv = row.querySelector('.container-rendeman');
                const rendemanInput = rendemanDiv.querySelector('input');
                const bobotDiv = row.querySelector('.container-bobot');
                const bobotInput = bobotDiv.querySelector('input');
                const basisHargaDiv = row.querySelector('.container-basis_harga');
                const basisHargaInput = basisHargaDiv.querySelector('input');
                const hargaJadiDiv = row.querySelector('.container-harga_jadi');
                const hargaJadiInput = hargaJadiDiv.querySelector('input');
                const subTotalDiv = row.querySelector('.container-sub_total');
                const subTotalInput = subTotalDiv.querySelector('input');
                const pphDiv = row.querySelector('.container-pph');
                const pphInput = pphDiv.querySelector('input');
                const ppnDiv = row.querySelector('.container-ppn');
                const ppnInput = ppnDiv.querySelector('input');
                const nominalAkhirDiv = row.querySelector('.container-nominal_akhir');
                const nominalAkhirInput = nominalAkhirDiv.querySelector('input');
                const selisihDiv = row.querySelector('.container-selisih');
                const selisihInput = selisihDiv.querySelector('input');

                const tipeSelect = row.querySelector('.tipe-select');

                const netto = parseFloat(nettoInput.value) || 0;
                const rendeman = parseFloat(rendemanInput.value) || 0;
                const bobot = parseFloat(bobotInput.value) || 0;
                const pph = parseFloat(pphInput.value) || 0;

                const produkNama = row.querySelector('.produk-nama').textContent;

                console.log('produkNama:', produkNama);
                let hargaJadi = 0;

                if (produkNama === 'Kopi') {
                    
                    hargaJadi = parseFloat(basisHargaInput.value) * (rendeman / 100);
                    hargaJadiInput.value = hargaJadi;
                }
                if (produkNama === 'Lada') {
                    
                    // hargaBasisMaster + (hargaBasisMaster * (rendeman / 100)) + bobot;
                    hargaJadi = parseFloat(basisHargaInput.value) + (parseFloat(basisHargaInput.value) * (rendeman / 100)) + bobot;
                    hargaJadiInput.value = hargaJadi;
                }


                // calculate harga jadi based on basis harga and rendeman
                // harga jadi => default baiknya basis * rendeman 
                console.log('hargaJadi:', hargaJadi);
                

                // Calculate sub total
                const subTotal = netto * hargaJadi;
                subTotalInput.value = subTotal.toFixed(2);

                const ppn = parseFloat(ppnInput.value) || subTotal * 0.12; //ppn = 12%
                ppnInput.value = ppn.toFixed(2);



                // Calculate nominal akhir (sub total + ppn)
                const nominalAkhir = subTotal + ppn;
                nominalAkhirInput.value = nominalAkhir.toFixed(2);

                const nettoPengirimanval = row.querySelector('.netto-pengiriman').value
                const nettoPengiriman = parseFloat(nettoPengirimanval);
                const selisih = nettoPengiriman - netto;
                selisihInput.value = selisih;

            }

            // Add event listeners to a row
            function addRowEventListeners(row) {
                // Add click event to calculate button
                const calculateBtn = row.querySelector('.btn-calculate');
                if (calculateBtn) {
                    calculateBtn.addEventListener('click', () => calculateRow(row));
                }

                // Handle tipe change (show/hide)
                const tipeSelect = row.querySelector('.tipe-select');
                const basisHargaDiv = row.querySelector('.container-basis_harga');
                const subTotalDiv = row.querySelector('.container-sub_total');
                // const pphDiv = row.querySelector('.container-pph');
                const ppnDiv = row.querySelector('.container-ppn');
                const nominalAkhirDiv = row.querySelector('.container-nominal_akhir');
                const selisihDiv = row.querySelector('.container-selisih');

                tipeSelect.addEventListener('change', function() {
                    if (this.value === 'Titip') {
                        basisHargaDiv.classList.add('hidden');
                        subTotalDiv.classList.add('hidden');
                        // pphDiv.classList.add('hidden');
                        ppnDiv.classList.add('hidden');
                        nominalAkhirDiv.classList.add('hidden');
                        selisihDiv.classList.add('hidden');
                    } else {
                        basisHargaDiv.classList.remove('hidden');
                        subTotalDiv.classList.remove('hidden');
                        // pphDiv.classList.remove('hidden');
                        ppnDiv.classList.remove('hidden');
                        nominalAkhirDiv.classList.remove('hidden');
                        selisihDiv.classList.remove('hidden');
                    }
                });
            }

            // Function to create product rows from pengiriman details
            function loadPengirimanDetails(pengirimanId) {
                // Clear existing rows
                produkList.innerHTML = '';

                // Filter details for selected pengiriman
                const details = pengirimanDetails.filter(detail => detail.pengiriman_id == pengirimanId);

                if (details.length === 0) {
                    produkListContainer.classList.add('hidden');
                    return;
                }

                produkListContainer.classList.remove('hidden');

                // Create a row for each detail
                details.forEach(detail => {
                    const newRow = rowTemplate.content.cloneNode(true);
                    const rowDiv = newRow.querySelector('.produk-row');

                    // Set pengiriman_detail_id
                    rowDiv.querySelector('.pengiriman-detail-id').value = detail.id;
                    let produk_id = detail.produk_id;
                    rowDiv.querySelector('.produk-id').value = produk_id;
                    let produkTerpilih = Produks.find(produk => produk.id == produk_id);
                    let basis_harga_penjualan = produkTerpilih.harga_basis_penjualan;

                    rowDiv.querySelector('.netto-pengiriman').value = detail.netto;

                    // Set quick info
                    rowDiv.querySelector('.produk-nama').textContent = detail.nama_barang || '-';
                    rowDiv.querySelector('.produk-jumlah-per-karung').textContent = detail.jumlah_per_karung || '-';
                    rowDiv.querySelector('.produk-jumlah-karung').textContent = detail.jumlah_karung || '-';
                    rowDiv.querySelector('.produk-bruto').textContent = detail.bruto || '-';
                    rowDiv.querySelector('.produk-tara').textContent = detail.tara || '-';
                    rowDiv.querySelector('.produk-netto').textContent = detail.netto || '-';
                    rowDiv.querySelector('.produk-satuan').textContent = produkTerpilih.satuan || '-';

                    if(detail.nama_barang == 'Lada') {
                        rowDiv.querySelector('.container-bobot').classList.remove('hidden');
                    }
                    // Set initial input value
                    const nettoDiv = rowDiv.querySelector('.container-netto');
                    const nettoInput = nettoDiv.querySelector('input');
                    nettoInput.value = detail.netto;

                    const basisHargaDiv = rowDiv.querySelector('.container-basis_harga');
                    const basisHargaInput = basisHargaDiv.querySelector('input');
                    basisHargaInput.value = basis_harga_penjualan;



                    // Add event listeners
                    addRowEventListeners(rowDiv);

                    produkList.appendChild(rowDiv);
                });
            }

            // Handle pengiriman selection change
            pengirimanSelect.addEventListener('change', function() {
                const selectedId = this.value;

                if (!selectedId) {
                    pengirimanInfo.classList.add('hidden');
                    produkListContainer.classList.add('hidden');
                    return;
                }

                // Find selected pengiriman
                const selectedPengiriman = Pengiriman.find(p => p.id == selectedId);
                console.log('selectedPengiriman:', selectedPengiriman);


                if (selectedPengiriman) {
                    // Update quick info
                    document.getElementById('customer-name').textContent = selectedPengiriman.customer.nama;
                    document.getElementById('nopol').textContent = selectedPengiriman.nopol || '-';
                    document.getElementById('no-transaksi').textContent = selectedPengiriman.no_transaksi || '-';
                    pengirimanInfo.classList.remove('hidden');

                    // Load pengiriman details
                    loadPengirimanDetails(selectedId);
                }
            });
        });
    </script>


    <!-- Choices.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <!-- Choices.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <style>
        /* Choices.js Custom Wrapper Styles */
        .choices__inner {
            min-height: 42px;
            border-radius: 0.5rem;
            border-color: rgb(209 213 219) !important;
            background: rgb(255 255 255) !important;
            padding: 0.25rem 0.75rem !important;
        }

        .dark .choices__inner {
            border-color: rgb(75 85 99) !important;
            background: rgb(17 24 39) !important;
        }



        .choices__input {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: left 0.75rem center !important;
            background-size: 1.1rem !important;
            padding-left: 2.25rem !important;
            /* Shims text to the right of the icon */
            background-color: transparent !important;
            color: rgb(17 24 39) !important;
        }


        .dark .choices__input {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23f3f4f6' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'/%3E%3C/svg%3E") !important;
            color: rgb(243 244 246) !important;

        }

        .choices__list--dropdown {
            border-radius: 0.5rem;
            border-color: rgb(75 85 99) !important;
            background: rgb(255 255 255) !important;
            color: rgb(17 24 39) !important;
            z-index: 20;
        }

        .dark .choices__list--dropdown {
            background: rgb(31, 41, 55) !important;
            color: rgb(243 244 246) !important;
        }

        .choices__list--dropdown .choices__item--selectable.is-highlighted {
            background-color: rgb(30 64 175) !important;
            color: #fff !important;
        }

        /* Match template placeholder styling */
        .choices__placeholder {
            opacity: 1;
            color: rgb(107 114 128);
        }

        .dark .choices__placeholder {
            color: rgb(156 163 175);
        }

        .dark .is-selected {
            background-color: rgb(30 64 175) !important;
            color: #fff !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productSelect = document.querySelector('.searchable-select');

            if (productSelect) {
                const choices = new Choices(productSelect, {
                    searchEnabled: true,
                    removeItemButton: true,
                    itemSelectText: '',
                    noResultsText: 'Produk tidak ditemukan',
                    placeholder: true,
                    placeholderValue: 'Cari atau Pilih Pengiriman',
                    shouldSort: false,
                    classNames: {
                        containerOuter: 'choices',
                        containerInner: 'choices__inner',
                        input: 'choices__input',
                        inputCloned: 'choices__input--cloned',
                        list: 'choices__list',
                        listItems: 'choices__list--multiple',
                        listSingle: 'choices__list--single',
                        listDropdown: 'choices__list--dropdown',
                        item: 'choices__item',
                        itemSelectable: 'choices__item--selectable',
                        itemDisabled: 'choices__item--disabled',
                        itemChoice: 'choices__item--choice',
                        placeholder: 'choices__placeholder',
                        group: 'choices__group',
                        groupHeading: 'choices__heading'
                    }

                });

                
            }
        });
    </script>
</x-layouts.app>