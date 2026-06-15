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
            <form action="{{ route($tablename . '.store') }}" method="POST" id="pembelianForm">
                @csrf
                <input type="hidden" name="pengiriman_id" value="{{ $pengiriman_id }}">

                <div id="produk-container">
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

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe Pembelian</label>
                                <select name="nama_produk[]" class="produk-select block w-full border-gray-300 p-2 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="titip">Titip</option>
                                    <option value="{{ $produk->nama_produk }}">Jual</option>
                                </select>
                            </div>

                            <div class="container-satuan hidden">
                                <input
                                    class="input-satuan"
                                    name="satuan[]" />
                            </div>

                            <div class="container-jumlah_per_karung hidden">
                                <x-forms.input append="satuan" label="Jumlah per Karung" name="jumlah_per_karung[]" type="number" />
                            </div>

                            <div class="container-jumlah_karung hidden">
                                <x-forms.input label="Jumlah Karung" name="jumlah_karung[]" type="decimal" />
                            </div>

                            <div class="container-bruto hidden">
                                <x-forms.input label="Bruto" name="bruto[]" type="decimal" />
                                <p class="text-xs text-gray-500 mt-1">*Jumlah per Karung x jumlah karung</p>
                            </div>

                            <div class="container-tara hidden">
                                <x-forms.input label="Tara" name="tara[]" type="decimal" />
                                <p class="text-xs text-gray-500 mt-1">*Jumlah Karung x 0.3 KG</p>
                            </div>

                            <div class="container-netto hidden">
                                <x-forms.input label="netto" name="netto[]" type="decimal" />
                                <p class="text-xs text-gray-500 mt-1">*Bruto - Tara</p>
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
                    <a href="{{ route('pengirimans.index') }}"><x-button type="secondary">Batal</x-button></a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('produk-container');
            const addButton = document.getElementById('add-produk');

            // Function to calculate Bruto (Jumlah per Karung × Jumlah Karung)
            function calculateBruto(row) {
                const jumlahPerKarungInput = row.querySelector('.container-jumlah_per_karung input');
                const jumlahKarungInput = row.querySelector('.container-jumlah_karung input');
                const brutoInput = row.querySelector('.container-bruto input');

                if (jumlahPerKarungInput && jumlahKarungInput && brutoInput) {
                    const jumlahPerKarung = parseFloat(jumlahPerKarungInput.value) || 0;
                    const jumlahKarung = parseFloat(jumlahKarungInput.value) || 0;
                    const bruto = jumlahPerKarung * jumlahKarung;
                    brutoInput.value = bruto.toFixed(2);

                    // Make bruto input readonly
                    brutoInput.readOnly = true;
                    brutoInput.classList.add('bg-gray-100', 'dark:bg-gray-700', 'cursor-not-allowed');

                    // Recalculate tara and netto after bruto changes
                    calculateTara(row);
                }
            }

            // Function to calculate Tara (Jumlah Karung × 0.3 KG)
            function calculateTara(row) {
                const jumlahKarungInput = row.querySelector('.container-jumlah_karung input');
                const taraInput = row.querySelector('.container-tara input');

                if (jumlahKarungInput && taraInput) {
                    const jumlahKarung = parseFloat(jumlahKarungInput.value) || 0;
                    const tara = jumlahKarung * 0.3; // 0.3 kg per karung
                    taraInput.value = tara.toFixed(2);

                    // Make tara input readonly
                    taraInput.readOnly = true;
                    taraInput.classList.add('bg-gray-100', 'dark:bg-gray-700', 'cursor-not-allowed');

                    // Recalculate netto after tara changes
                    calculateNetto(row);
                }
            }

            // Function to calculate Netto (Bruto - Tara)
            function calculateNetto(row) {
                const brutoInput = row.querySelector('.container-bruto input');
                const taraInput = row.querySelector('.container-tara input');
                const nettoInput = row.querySelector('.container-netto input');

                if (brutoInput && taraInput && nettoInput) {
                    const bruto = parseFloat(brutoInput.value) || 0;
                    const tara = parseFloat(taraInput.value) || 0;
                    const netto = bruto - tara;
                    nettoInput.value = netto > 0 ? netto.toFixed(2) : 0;

                    // Make netto input readonly
                    nettoInput.readOnly = true;
                    nettoInput.classList.add('bg-gray-100', 'dark:bg-gray-700', 'cursor-not-allowed');
                }
            }

            // Function to setup event listeners for inputs in a row
            function setupInputListeners(row) {
                const jumlahPerKarungInput = row.querySelector('.container-jumlah_per_karung input');
                const jumlahKarungInput = row.querySelector('.container-jumlah_karung input');

                if (jumlahPerKarungInput) {
                    jumlahPerKarungInput.addEventListener('input', function() {
                        calculateBruto(row);
                    });
                }

                if (jumlahKarungInput) {
                    jumlahKarungInput.addEventListener('input', function() {
                        calculateBruto(row); // This will trigger bruto, then tara, then netto
                    });
                }
            }

            // Function to update visibility for a specific row
            function handleRowLogic(row) {
                const select = row.querySelector('.produk-select');
                const jumlahPerKarungDiv = row.querySelector('.container-jumlah_per_karung');
                const jumlahKarungDiv = row.querySelector('.container-jumlah_karung');
                const brutoDiv = row.querySelector('.container-bruto');
                const taraDiv = row.querySelector('.container-tara');
                const nettoDiv = row.querySelector('.container-netto');
                const satuanContainer = row.querySelector('.container-satuan');
                const satuanInput = row.querySelector('.text-gray-900');

                // Remove existing event listener to avoid duplicates
                const newSelect = select.cloneNode(true);
                select.parentNode.replaceChild(newSelect, select);

                newSelect.addEventListener('change', function() {
                    const selectedOption = newSelect.options[newSelect.selectedIndex];
                    const produkValue = selectedOption.value;
                    const satuan = selectedOption.getAttribute('data-satuan') || '';
                    const produkNama = selectedOption.text;

                    // Set satuan value
                    if (satuanInput) {
                        satuanInput.value = satuan;
                    }

                    // Update satuan display for "Jumlah per Karung" field
                    if (jumlahPerKarungDiv) {
                        console.log(satuan);
                        const appendSpan = jumlahPerKarungDiv.querySelector('.text-gray-900');
                        appendSpan.textContent = satuan;

                    }

                    // Hide all containers first
                    if (jumlahPerKarungDiv) jumlahPerKarungDiv.classList.add('hidden');
                    if (jumlahKarungDiv) jumlahKarungDiv.classList.add('hidden');
                    if (brutoDiv) brutoDiv.classList.add('hidden');
                    if (taraDiv) taraDiv.classList.add('hidden');
                    if (nettoDiv) nettoDiv.classList.add('hidden');

                    // Check if produk is selected (not empty)
                    if (produkValue !== "") {
                        // Show all fields for any selected product
                        if (jumlahPerKarungDiv) jumlahPerKarungDiv.classList.remove('hidden');
                        if (jumlahKarungDiv) jumlahKarungDiv.classList.remove('hidden');
                        if (brutoDiv) brutoDiv.classList.remove('hidden');
                        if (taraDiv) taraDiv.classList.remove('hidden');
                        if (nettoDiv) nettoDiv.classList.remove('hidden');

                        // Make bruto, tara, and netto readonly
                        const brutoInput = brutoDiv.querySelector('input');
                        const taraInput = taraDiv.querySelector('input');
                        const nettoInput = nettoDiv.querySelector('input');

                        if (brutoInput) {
                            brutoInput.readOnly = true;
                            brutoInput.classList.add('bg-gray-100', 'dark:bg-gray-700', 'cursor-not-allowed');
                        }
                        if (taraInput) {
                            taraInput.readOnly = true;
                            taraInput.classList.add('bg-gray-100', 'dark:bg-gray-700', 'cursor-not-allowed');
                        }
                        if (nettoInput) {
                            nettoInput.readOnly = true;
                            nettoInput.classList.add('bg-gray-100', 'dark:bg-gray-700', 'cursor-not-allowed');
                        }

                        // Setup input listeners after showing fields
                        setupInputListeners(row);

                        // Initial calculation if there are values
                        calculateBruto(row);
                    }
                });

                // Trigger change event to set initial state
                newSelect.dispatchEvent(new Event('change'));
            }

            // Initialize first row
            const firstRow = container.querySelector('.produk-row');
            if (firstRow) {
                handleRowLogic(firstRow);

                // Add remove button to first row if needed (hidden initially)
                const firstRemoveBtn = firstRow.querySelector('.btn-remove');
                if (firstRemoveBtn) {
                    firstRemoveBtn.classList.add('hidden');
                }
            }

            // Add new row logic
            addButton.addEventListener('click', function() {
                const rows = container.querySelectorAll('.produk-row');
                if (rows.length === 0) return;

                const originalRow = rows[0];
                const newRow = originalRow.cloneNode(true);

                // Reset all input values in new row
                newRow.querySelectorAll('input').forEach(input => {
                    if (input.type !== 'hidden') {
                        input.value = '';
                    }
                    // Remove readonly attribute from cloned inputs
                    input.readOnly = false;
                });

                newRow.querySelectorAll('select').forEach(select => {
                    select.value = '';
                });

                // Reset satuan display for Jumlah per Karung field
                const jumlahPerKarungDiv = newRow.querySelector('.container-jumlah_per_karung');
                if (jumlahPerKarungDiv) {
                    const appendSpan = jumlahPerKarungDiv.querySelector('.append-satuan');
                    if (appendSpan) {
                        appendSpan.textContent = '';
                    }
                }

                // Reset visibility of containers
                const containers = newRow.querySelectorAll('.container-jumlah_per_karung, .container-jumlah_karung, .container-bruto, .container-tara, .container-netto');
                containers.forEach(container => {
                    container.classList.add('hidden');
                });

                // Show and setup remove button for new row
                const removeBtn = newRow.querySelector('.btn-remove');
                if (removeBtn) {
                    removeBtn.classList.remove('hidden');
                    removeBtn.addEventListener('click', function() {
                        newRow.remove();
                    });
                }

                // Apply logic to new row
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