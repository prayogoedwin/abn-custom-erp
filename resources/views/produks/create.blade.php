<x-layouts.app>
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}"
            class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Dashboard') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <a href="{{ route('produks.index') }}"
            class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Produks') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-500 dark:text-gray-400">{{ __('Create') }}</span>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Create Produk') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Create a new produk') }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <form action="{{ route('produks.store') }}" method="POST" class="max-w-2xl">
                @csrf

                <div class="mb-4">
                    <x-forms.input label="Nama Produk" name="nama_produk" type="text" value="{{ old('nama_produk') }}" required />
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Kategori') }}
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-gray-50 dark:bg-gray-900 p-4 rounded-md">
                        @forelse($kategoris as $kategori)
                        <div>
                            <x-forms.checkbox
                                name="kategoris[]"
                                value="{{ $kategori->id }}"
                                label="{{ $kategori->nama }}"
                                :checked="in_array($kategori->id, old('kategoris', $produk->kategoris->pluck('id')->toArray()))" />
                        </div>
                        @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No kategoris available.') }}</p>
                        @endforelse
                    </div>
                    @error('kategoris')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <x-forms.input label="Harga" name="harga" type="number" step="0.01" value="{{ old('harga') }}" required />
                </div>

                <div class="mb-4">
                    <x-forms.input label="Stok" name="stok" type="number" value="{{ old('stok') }}" required />
                </div>

                

                <div class="flex gap-3">
                    <x-button type="primary">{{ __('Create') }}</x-button>
                    <a href="{{ route('produks.index') }}">
                        <x-button type="secondary">{{ __('Cancel') }}</x-button>
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
