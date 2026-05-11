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

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <form action="{{ route($tablename . '.storelanjut', $data) }}" method="POST">
                @csrf
                @method('POST')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Metode Pembayaran
                                </label>
                                <select
                                    name="metode_pembayaran"
                                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/40">
                                    <option value="">Pilih Metode Pembayaran</option>
                                    <option value="Cash" {{ old('metode_pembayaran', $data->metode_pembayaran) === 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="Transfer" {{ old('metode_pembayaran', $data->metode_pembayaran) === 'Transfer' ? 'selected' : '' }}>Transfer</option>
                                </select>
                                @error("metode_pembayaran")
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Tipe Pembayaran
                                </label>
                                <select
                                    name="tipe_pembayaran"
                                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/40">
                                    <option value="">Pilih Tipe Pembayaran</option>
                                    <option value="Potong" {{ old('tipe_pembayaran', $data->tipe_pembayaran) === 'Potong' ? 'selected' : '' }}>Potong</option>
                                    <option value="Full Pembayaran" {{ old('tipe_pembayaran', $data->tipe_pembayaran) === 'Full Pembayaran' ? 'selected' : '' }}>Full Pembayaran</option>
                                    <option value="Titip" {{ old('tipe_pembayaran', $data->tipe_pembayaran) === 'Titip' ? 'selected' : '' }}>Titip</option>
                                </select>
                                @error("tipe_pembayaran")
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-forms.input label="Nominal" name="nominal" type="number" value="{{ old('nominal', $simpanpinjamsupplier->nominal) }}" required />
                            </div>
                            <div>
                                <x-forms.input label="Keterangan" name="keterangan" type="string" value="{{ old('keterangan', $simpanpinjamsupplier->keterangan) }}" required />
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Supplier</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $supplier->nama }}</p>
                        </div>

                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tipe</p>
                            <p class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">{{ $simpanpinjamsupplier->tipe }}</p>
                        </div>

                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Status Transaksi Supplier</p>
                            <p class="mt-1 inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $data->status_pembayaran === 'Lunas' ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' }}">
                                {{ $data->status_pembayaran }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-3 border-t border-gray-200 pt-4 dark:border-gray-700">
                    <x-button type="primary">Simpan</x-button>
                    <a href="{{ route('pembelian.cetaknota', $data->id) }}" target="_blank">
                        <x-button type="secondary">Cetak Nota</x-button>
                    </a>
                    <a href="{{ route($tablename . '.index') }}">
                        <x-button type="secondary">Batal</x-button>
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>