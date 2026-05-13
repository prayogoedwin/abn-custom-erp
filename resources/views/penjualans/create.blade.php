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
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $subheading ?? __('Fill in the details below') }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <form action="{{ route('pengirimans.store') }}" method="POST" class="max-w-3xl">
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
                        name="penjualan_id"
                        id="pengiriman-select"
                        placeholder="Ketik nama customer..."
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/40 searchable-select">

                        <option value="">
                            {{ __('Select pengiriman') }}
                        </option>
                        @foreach($pengirimans as $option)
                        <option value="{{ $option->id }}">
                            {{ $option->no_transaksi . ' - ' . $option->nopol . ' - ' . $option->customer->nama; }}
                        </option>
                        @endforeach
                    </select>

                    @error('penjualan_id')
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
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div hidden>
                                <input type="text" name="pengiriman_detail_id[]" class="pengiriman-detail-id">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe</label>
                                <select name="tipe[]" class="tipe-select produk-select block w-full border-gray-300 p-2 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="Titip">Titip</option>
                                    <option value="Jual">Jual</option>
                                </select>
                            </div>

                            <div class="container-netto_pengiriman hidden">
                                <x-forms.input append="satuan" label="Netto Pengiriman" name="netto_pengiriman[]" type="number" step="any" class="netto-pengiriman-input" />
                            </div>

                            <div class="container-netto">
                                <x-forms.input label="Netto" name="netto[]" type="number" step="any" class="netto-input" />
                            </div>

                            <div class="container-selisih">
                                <x-forms.input label="Selisih" name="selisih[]" type="number" step="any" class="selisih-input" readonly />
                            </div>

                            <div class="container-basis_harga">
                                <x-forms.input label="Basis Harga" name="basis_harga[]" type="number" step="any" class="basis-harga-input" />
                            </div>

                            <div class="container-sub_total">
                                <x-forms.input label="Sub Total" name="sub_total[]" type="number" step="any" class="sub-total-input" readonly />
                                <p class="text-xs text-gray-500 mt-1">*netto * basis harga</p>
                            </div>
                            <div class="container-pph">
                                <x-forms.input label="PPH" name="pph[]" type="number" step="any" class="pph-input" />
                            </div>
                            <div class="container-ppn">
                                <x-forms.input label="PPN" name="ppn[]" type="number" step="any" class="ppn-input" />
                            </div>
                            <div class="container-nominal_akhir">
                                <x-forms.input label="Nominal Akhir" name="nominal_akhir[]" type="number" step="any" class="nominal-akhir-input" readonly />
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
                    <a href="{{ route($tablename . '.index') }}">
                        <x-button type="secondary">{{ __('Batal') }}</x-button>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const pengirimanDetails = @json($pengirimandetails); // all pengirimandetails in db
        const Pengiriman = @json($pengirimans); // all pengiriman in db

        document.addEventListener('DOMContentLoaded', function() {
            const pengirimanSelect = document.getElementById('pengiriman-select');
            const pengirimanInfo = document.getElementById('pengiriman-info');
            const produkListContainer = document.getElementById('produk-list-container');
            const produkList = document.getElementById('produk-list');
            const rowTemplate = document.getElementById('row-template');

            // Function to calculate values for a row
            function calculateRow(row) {
                const nettoInput = row.querySelector('.netto-input');
                const basisHargaInput = row.querySelector('.basis-harga-input');
                const subTotalInput = row.querySelector('.sub-total-input');
                const pphInput = row.querySelector('.pph-input');
                const ppnInput = row.querySelector('.ppn-input');
                const nominalAkhirInput = row.querySelector('.nominal-akhir-input');
                const selisihInput = row.querySelector('.selisih-input');
                const nettoPengirimanInput = row.querySelector('.netto-pengiriman-input');
                const tipeSelect = row.querySelector('.tipe-select');

                const netto = parseFloat(nettoInput.value) || 0;
                const basisHarga = parseFloat(basisHargaInput.value) || 0;
                const pph = parseFloat(pphInput.value) || 0;
                const ppn = parseFloat(ppnInput.value) || 0;

                // Calculate sub total
                const subTotal = netto * basisHarga;
                subTotalInput.value = subTotal.toFixed(2);

                // Calculate nominal akhir (sub total + ppn - pph)
                const nominalAkhir = subTotal + ppn - pph;
                nominalAkhirInput.value = nominalAkhir.toFixed(2);

                // Calculate selisih if netto pengiriman exists and tipe is Titip
                if (nettoPengirimanInput && !nettoPengirimanInput.closest('.container-netto_pengiriman').classList.contains('hidden')) {
                    const nettoPengiriman = parseFloat(nettoPengirimanInput.value) || 0;
                    const selisih = nettoPengiriman - netto;
                    selisihInput.value = selisih.toFixed(2);
                } else {
                    selisihInput.value = '';
                }
            }

            // Add event listeners to a row
            function addRowEventListeners(row) {
                // Add click event to calculate button
                const calculateBtn = row.querySelector('.btn-calculate');
                if (calculateBtn) {
                    calculateBtn.addEventListener('click', () => calculateRow(row));
                }

                // Handle tipe change (show/hide netto_pengiriman)
                const tipeSelect = row.querySelector('.tipe-select');
                const containerNettoPengiriman = row.querySelector('.container-netto_pengiriman');

                tipeSelect.addEventListener('change', function() {
                    if (this.value === 'Titip') {
                        containerNettoPengiriman.classList.remove('hidden');
                    } else {
                        containerNettoPengiriman.classList.add('hidden');
                        // Clear selisih when switching to Jual
                        const selisihInput = row.querySelector('.selisih-input');
                        if (selisihInput) selisihInput.value = '';
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

                    // Set quick info
                    rowDiv.querySelector('.produk-nama').textContent = detail.nama_barang || '-';
                    rowDiv.querySelector('.produk-jumlah-per-karung').textContent = detail.jumlah_per_karung || '-';
                    rowDiv.querySelector('.produk-jumlah-karung').textContent = detail.jumlah_karung || '-';
                    rowDiv.querySelector('.produk-bruto').textContent = detail.bruto || '-';
                    rowDiv.querySelector('.produk-tara').textContent = detail.tara || '-';
                    rowDiv.querySelector('.produk-netto').textContent = detail.netto || '-';

                    // Set initial netto value from detail.netto
                    const nettoInput = rowDiv.querySelector('.netto-input');
                    if (nettoInput && detail.netto) {
                        nettoInput.value = detail.netto;
                    }

                    // Set netto pengiriman initial value
                    const nettoPengirimanInput = rowDiv.querySelector('.netto-pengiriman-input');
                    if (nettoPengirimanInput && detail.netto) {
                        nettoPengirimanInput.value = detail.netto;
                    }

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

                if (selectedPengiriman) {
                    // Update quick info
                    document.getElementById('customer-name').textContent = selectedPengiriman.customer?.nama || '-';
                    document.getElementById('nopol').textContent = selectedPengiriman.nopol || '-';
                    document.getElementById('no-transaksi').textContent = selectedPengiriman.no_transaksi || '-';
                    pengirimanInfo.classList.remove('hidden');

                    // Load pengiriman details
                    loadPengirimanDetails(selectedId);
                }
            });
        });
    </script>




    <style>
        .ts-wrapper.single .ts-control {
            min-height: 42px;
            border-radius: 0.5rem;
            border-color: rgb(209 213 219);
            background: rgb(255 255 255);
            box-shadow: none;
        }

        .dark .ts-wrapper.single .ts-control {
            border-color: rgb(75 85 99);
            background: rgb(17 24 39);
            color: rgb(243 244 246);
        }

        .ts-wrapper .ts-control input {
            color: rgb(17 24 39);
        }

        .ts-wrapper .ts-control input::placeholder {
            color: rgb(107 114 128);
            opacity: 1;
        }

        .dark .ts-wrapper .ts-control input {
            color: rgb(243 244 246) !important;
        }

        .dark .ts-wrapper .ts-control input::placeholder {
            color: rgb(156 163 175);
            opacity: 1;
        }

        .ts-dropdown {
            border-radius: 0.5rem;
            border-color: rgb(75 85 99);
            background: rgb(17 24 39);
            color: rgb(243 244 246);
        }

        .ts-dropdown .active {
            background: rgb(30 64 175);
            color: #fff;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const customerSelect = document.querySelector('select.searchable-select');

            if (customerSelect) {
                new TomSelect(customerSelect, {
                    create: false,
                    allowEmptyOption: true,
                    maxOptions: 25,
                    closeAfterSelect: true,
                    openOnFocus: false,
                    hidePlaceholder: false,
                    placeholder: customerSelect.getAttribute('placeholder') || 'Ketik nama customer...',
                    render: {
                        no_results: function() {
                            return '<div class="no-results">Supplier tidak ditemukan</div>';
                        }
                    }
                });
            }
        });
    </script>
</x-layouts.app>