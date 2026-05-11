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
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $subheading ?? __('Fill in the details below') }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-5">
            <form action="{{ route($tablename . '.update', $data) }}" method="POST" id="pembelianForm">
                @csrf
                @method("PUT")
                <input type="hidden" name="pengiriman_id" value="{{ $data->id }}">

                <template id="row-template">
                    <div class="produk-row border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Produk</label>
                                <select name="nama_produk[]" class="produk-select block w-full border-gray-300 p-2 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">Pilih Produk</option>
                                    @foreach($produks as $produk)
                                    <option value="{{ $produk->nama_produk }}" data-satuan="{{ $produk->satuan }}">
                                        {{$produk->nama_produk}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>



                            <div class="container-jumlah_per_karung">
                                <x-forms.input append="satuan" label="Jumlah per Karung" name="jumlah_per_karung[]" type="number" />
                            </div>

                            <div class="container-jumlah_karung ">
                                <x-forms.input label="Jumlah Karung" name="jumlah_karung[]" type="decimal" />
                            </div>

                            <div class="container-bruto ">
                                <x-forms.input label="Bruto" name="bruto[]" type="decimal" />
                                <p class="text-xs text-gray-500 mt-1">*Jumlah per Karung x jumlah karung</p>
                            </div>

                            <div class="container-tara">
                                <x-forms.input label="Tara" name="tara[]" type="decimal" />
                                <p class="text-xs text-gray-500 mt-1">*Jumlah Karung x 0.3 KG</p>
                            </div>

                            <div class="container-netto">
                                <x-forms.input label="netto" name="netto[]" type="decimal" />
                                <p class="text-xs text-gray-500 mt-1">*Bruto - Tara</p>
                            </div>
                        </div>

                        <button type="button" class="btn-remove hidden text-red-500 text-sm mt-2">Hapus Produk</button>
                    </div>
                </template>

                <button type="button" id="add-produk" class="mb-4 bg-green-500 text-white px-4 py-2 rounded shadow">
                    + Tambah Produk Lain
                </button>

                <div class="flex gap-3 mt-3 border-t pt-4">
                    <x-button type="primary">Simpan Semua</x-button>
                    <a href="{{ route('pengirimans.index') }}"><x-button type="secondary">Batal</x-button></a>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the old data from the server
            const oldData = @json($pengirimandetails);
            const container = document.querySelector('.produk-row')?.parentNode || document.getElementById('add-produk').parentNode;
            const template = document.getElementById('row-template');

            // Store produk options for reference
            const produkOptions = [];
            @foreach($produks as $produk)
            produkOptions.push({
                value: '{{ $produk->nama_produk }}',
                satuan: '{{ $produk->satuan }}'
            });
            @endforeach

            console.log(produkOptions);

            // Function to create a new row
            function createProdukRow(data = null) {
                const clone = document.importNode(template.content, true);
                const row = clone.querySelector('.produk-row');


                // Setup event listeners for this row
                setupRowEventListeners(row);

                // If data is provided, populate the row
                if (data) {
                    populateRowWithData(row, data);
                }

                return row;
            }

            // Function to setup row event listeners
            function setupRowEventListeners(row) {
                // Product select change
                const produkSelect = row.querySelector('.produk-select');
                if (produkSelect) {
                    produkSelect.addEventListener('change', function() {
                        calculateRowValues(row);
                    });
                }

                // Jumlah per karung input
                const jumlahPerKarung = row.querySelector('input[name="jumlah_per_karung[]"]');
                if (jumlahPerKarung) {
                    jumlahPerKarung.addEventListener('input', function() {
                        calculateRowValues(row);
                    });
                }

                // Jumlah karung input
                const jumlahKarung = row.querySelector('input[name="jumlah_karung[]"]');
                if (jumlahKarung) {
                    jumlahKarung.addEventListener('input', function() {
                        calculateBruto(row);
                        calculateTara(row);
                        calculateNetto(row);
                        calculateRowValues(row);
                    });
                }

                // Tara manual edit
                const taraInput = row.querySelector('input[name="tara[]"]');
                if (taraInput) {
                    taraInput.addEventListener('input', function() {
                        calculateNetto(row);
                    });
                }

                // Bruto manual edit
                const brutoInput = row.querySelector('input[name="bruto[]"]');
                if (brutoInput) {
                    brutoInput.addEventListener('input', function() {
                        calculateNetto(row);
                    });
                }

                // Remove button
                const removeBtn = row.querySelector('.btn-remove');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        row.remove();
                        updateRemoveButtons();
                    });
                }
            }


            // Calculate bruto value
            function calculateBruto(row) {
                const jumlahPerKarung = parseFloat(row.querySelector('input[name="jumlah_per_karung[]"]')?.value) || 0;
                const jumlahKarung = parseFloat(row.querySelector('input[name="jumlah_karung[]"]')?.value) || 0;
                const brutoInput = row.querySelector('input[name="bruto[]"]');

                if (brutoInput && jumlahPerKarung && jumlahKarung) {
                    const bruto = jumlahPerKarung * jumlahKarung;
                    brutoInput.value = bruto.toFixed(2);
                } else if (brutoInput) {
                    brutoInput.value = '';
                }
            }

            // Calculate tara value (jumlah karung x 0.3 KG)
            function calculateTara(row) {
                const jumlahKarung = parseFloat(row.querySelector('input[name="jumlah_karung[]"]')?.value) || 0;
                const taraInput = row.querySelector('input[name="tara[]"]');

                if (taraInput && jumlahKarung) {
                    const tara = jumlahKarung * 0.3;
                    taraInput.value = tara.toFixed(2);
                } else if (taraInput) {
                    taraInput.value = '';
                }
            }

            // Calculate netto value (bruto - tara)
            function calculateNetto(row) {
                const bruto = parseFloat(row.querySelector('input[name="bruto[]"]')?.value) || 0;
                const tara = parseFloat(row.querySelector('input[name="tara[]"]')?.value) || 0;
                const nettoInput = row.querySelector('input[name="netto[]"]');

                if (nettoInput) {
                    const netto = bruto - tara;
                    nettoInput.value = netto.toFixed(2);
                }
            }

            // Calculate all row values
            function calculateRowValues(row) {
                calculateBruto(row);
                calculateTara(row);
                calculateNetto(row);
            }

            // Populate row with existing data
            function populateRowWithData(row, data) {
                // Set produk select
                const produkSelect = row.querySelector('.produk-select');
                produkSelect.value = data.nama_barang;
                console.log(data);

                // Set jumlah per karung
                const jumlahPerKarung = row.querySelector('input[name="jumlah_per_karung[]"]');
                jumlahPerKarung.value = data.jumlah_per_karung;

                // Set jumlah karung
                const jumlahKarung = row.querySelector('input[name="jumlah_karung[]"]');
                jumlahKarung.value = data.jumlah_karung;

                // Set bruto
                const bruto = row.querySelector('input[name="bruto[]"]');
                bruto.value = data.bruto;

                // Set tara
                const tara = row.querySelector('input[name="tara[]"]');
                tara.value = data.tara;

                // Set netto
                const netto = row.querySelector('input[name="netto[]"]');
                netto.value = data.netto;

                // Recalculate to ensure consistency
                calculateRowValues(row);
            }

            // Update remove buttons visibility
            function updateRemoveButtons() {
                const rows = document.querySelectorAll('.produk-row');
                rows.forEach((row, index) => {
                    const removeBtn = row.querySelector('.btn-remove');
                    if (removeBtn) {
                        if (rows.length === 1) {
                            removeBtn.classList.add('hidden');
                        } else {
                            removeBtn.classList.remove('hidden');
                        }
                    }
                });
            }

            // Load existing data into rows
            function loadExistingData() {
                if (oldData && oldData.length > 0) {


                    // Create rows for each data item
                    oldData.forEach((item, index) => {
                        const row = createProdukRow(item);
                        if (row) {
                            // Insert before the add button
                            const addButton = document.getElementById('add-produk');
                            addButton.insertAdjacentElement('beforebegin', row);
                        }
                    });

                    updateRemoveButtons();
                } else {
                    // Create one empty row if no data
                    const addButton = document.getElementById('add-produk');
                    if (addButton && document.querySelectorAll('.produk-row').length === 0) {
                        const row = createProdukRow();
                        if (row) {
                            addButton.insertAdjacentElement('beforebegin', row);
                            updateRemoveButtons();
                        }
                    }
                }
            }

            // Add new product row
            document.getElementById('add-produk')?.addEventListener('click', function() {
                const newRow = createProdukRow();
                if (newRow) {
                    this.insertAdjacentElement('beforebegin', newRow);
                    updateRemoveButtons();
                }
            });



            // Initialize on page load
            loadExistingData();
        });
    </script>


    <style>
        /* Tambahkan jika Tailwind .hidden belum terdefinisi atau butuh fallback */
        .hidden {
            display: none;
        }
    </style>
</x-layouts.app>