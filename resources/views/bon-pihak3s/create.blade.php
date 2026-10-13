<x-layouts.app>
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}"
            class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Dashboard') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <a href="{{ route('bon-pihak3s.index') }}"
            class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Bon Pihak 3s') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-500 dark:text-gray-400">{{ __('Create') }}</span>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Create Bon Pihak 3') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Create a new bon pihak 3') }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">

            <form action="{{ route('bon-pihak3s.store') }}" method="POST" class="max-w-2xl">
                @csrf

                <!-- Quick info -->
                <div id="bon-info" class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <h3 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-2">Informasi Pihak 3</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Karyawan:</span>
                            <span id="customer-name" class="ml-2 font-medium text-gray-800 dark:text-gray-200">{{ $pihak3->nama }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Kontak:</span>
                            <span id="kontak" class="ml-2 font-medium text-gray-800 dark:text-gray-200">{{ $pihak3->kontak }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Alamat:</span>
                            <span id="no-transaksi" class="ml-2 font-medium text-gray-800 dark:text-gray-200">{{ $pihak3->alamat }}</span>
                        </div>
                    </div>
                </div>
                <div class="mb-5 rounded-lg border border-blue-200 bg-blue-50/70 px-4 py-3 text-sm text-blue-700 dark:border-blue-900/60 dark:bg-blue-950/30 dark:text-blue-200">
                    Sisa Bon: {{ $currentSisa }}
                </div>

                <input type="hidden" name="pihak3_id" value="{{ $pihak3->id }}">

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Metode
                    </label>

                    <select
                        name="metode"
                        class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm p-2">

                        <option value="">{{ __('Select Metode') }}</option>
                        @foreach($metodeOptions as $option)
                        <option value="{{ $option['value'] }}" {{ old('metode') == $option['value'] ? 'selected' : '' }}>
                            {{ $option['label'] }}
                        </option>
                        @endforeach
                    </select>

                    @error('metode')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <x-forms.input label="Masuk" name="masuk" type="number" value="{{ old('masuk') }}" />
                </div>
                <div class="mb-4">
                    <x-forms.input label="Keluar" name="keluar" type="number" value="{{ old('keluar') }}" />
                </div>

                <div class="mb-4">
                    <x-forms.input label="Keterangan" name="keterangan" type="text" value="{{ old('keterangan') }}" />
                </div>


                <div class="flex gap-3">
                    <x-button type="primary">{{ __('Create') }}</x-button>
                    <a href="{{ route('bon-suppliers.index') }}" class="text-white font-medium py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors flex items-center justify-center cursor-pointer bg-gray-600 hover:bg-gray-700 focus:ring-gray-500 dark:bg-gray-500 dark:hover:bg-gray-600">
                        {{ __('Batal') }}
                    </a>
                </div>

            </form>
        </div>
    </div>
</x-layouts.app>