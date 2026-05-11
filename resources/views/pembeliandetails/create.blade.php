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
                @method('POST')

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Produk</label>
                    <select
                        name="produk_id"
                        id="produk_select"
                        class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="">Pilih Produk</option>

                        @foreach($produks as $produk)
                        <option value="{{ $produk->id }}"
                            data-satuan="{{ $produk->satuan }}">
                            {{$produk->nama_produk}}

                        </option>
                        @endforeach
                    </select>
                </div>

                <div id="container-netto" class="mb-4 hidden">
                    <div class="relative">
                        <x-forms.input append="satuan" label="Netto" name="netto" type="number" value="{{ old('netto') }}" />

                    </div>
                </div>

                <div id="container-rendeman" class="mb-4 hidden">
                    <x-forms.input append="%" label="Rendeman" name="rendeman" type="number" value="{{ old('rendeman') }}" />
                </div>

                <div id="container-bobot" class="mb-4 hidden">
                    <x-forms.input prepend="Rp." label="Bobot" name="bobot" type="number" value="{{ old('bobot') }}" />
                </div>

                

                <div class="flex gap-3 mt-3">
                    <x-button type="primary">{{ __('Save') }}</x-button>
                    <a href="{{ route('pembelians.index') }}">
                        <x-button type="secondary">{{ __('Cancel') }}</x-button>
                    </a>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const produkSelect = document.getElementById('produk_select');
            const containerNetto = document.getElementById('container-netto');
            const containerRendeman = document.getElementById('container-rendeman');
            const containerBobot = document.getElementById('container-bobot');
            

            // Cari elemen tempat teks satuan muncul (sesuaikan selector dengan output komponen Anda)
            // Asumsi: komponen x-forms.input merender append di dalam elemen dengan class/id tertentu
            const appendSatuan = document.querySelector('#container-netto .text-gray-500');

            function updateVisibility() {
                const selectedOption = produkSelect.options[produkSelect.selectedIndex];
                const val = selectedOption.value;
                const satuan = selectedOption.getAttribute('data-satuan');


                // Reset visibility
                containerNetto.classList.add('hidden');
                containerRendeman.classList.add('hidden');
                containerBobot.classList.add('hidden');

                if (val !== "") {
                    containerNetto.classList.remove('hidden');

                    if (appendSatuan) {
                        appendSatuan.textContent = satuan;
                    }

                    // Logika Bobot (ID 1)
                    if (val === "1") {
                        containerBobot.classList.remove('hidden');
                    }

                    // Logika Rendeman (ID 1 & 2)
                    if (val === "1" || val === "2") {
                        containerRendeman.classList.remove('hidden');
                    }
                }
            }

            produkSelect.addEventListener('change', updateVisibility);
            updateVisibility(); // Jalankan saat reload untuk handle old input
        });
    </script>

    

    <style>
        /* Tambahkan jika Tailwind .hidden belum terdefinisi atau butuh fallback */
        .hidden {
            display: none;
        }
    </style>
</x-layouts.app>