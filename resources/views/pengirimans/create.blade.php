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
                    Pilih customer, isi data transaksi, lalu lanjut ke detail pengiriman detail.
                </div>

                @foreach($columns as $column)
                @if(!$column['inform'] )
                @continue
                @endif
                
                @if($column['type'] === 'text')
                <div class="mb-5">
                    <x-forms.input label="{{ $column['title'] }}" name="{{ $column['name'] }}" type="text" value="{{ old($column['name']) }}" required />
                </div>

                @elseif($column['type'] === 'email')
                
                <div class="mb-5">
                    <x-forms.input label="{{ $column['title'] }}" name="{{ $column['name'] }}" type="email" value="{{ old($column['name']) }}" required />
                </div>

                @elseif($column['type'] === 'password')

                <div class="mb-5">
                    <x-forms.input label="Password" name="password" type="password" required />
                </div>

                <div class="mb-6">
                    <x-forms.input label="Confirm Password" name="password_confirmation" type="password" required />
                </div>

                @elseif($column['type'] === 'number')

                <div class="mb-5">
                    <x-forms.input label="{{ $column['title'] }}" name="{{ $column['name'] }}" type="number" step="{{ $column['name'] === 'nominal' ? '1' : '0.01' }}" value="{{ old($column['name']) }}"  />
                </div>
                @elseif($column['type'] === 'select')
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ $column['title'] }}
                    </label>

                    <select
                        name="{{ $column['name'] }}"
                        placeholder="{{ $column['name'] === 'customer_id' ? 'Ketik nama customer...' : __('Select ' . $column['title']) }}"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/40 {{ $column['name'] === 'customer_id' ? 'searchable-select' : '' }}">

                        <option value="">
                            {{ $column['name'] === 'customer_id' ? '' : __('Select ' . $column['title']) }}
                        </option>
                        @foreach($column['options'] as $option)
                        <option value="{{ $option['value'] }}" {{ old($column['name']) == $option['value'] ? 'selected' : '' }}>
                            {{ $option['label'] }}
                        </option>
                        @endforeach
                    </select>

                    @error($column['name'])
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                @endif
                @endforeach



                <div class="mt-2 flex gap-3 border-t border-gray-200 pt-4 dark:border-gray-700">
                    <x-button type="primary">{{ __('Lanjut') }}</x-button>
                    <a href="{{ route($tablename . '.index') }}">
                        <x-button type="secondary">{{ __('Batal') }}</x-button>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
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