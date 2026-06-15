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
        <div class="p-6 min-h-[500px]">
            <form action="{{ route($tablename . '.store') }}" method="POST" class="max-w-3xl">
                @csrf
                @method('POST')

                <div class="mb-5 rounded-lg border border-blue-200 bg-blue-50/70 px-4 py-3 text-sm text-blue-700 dark:border-blue-900/60 dark:bg-blue-950/30 dark:text-blue-200">
                    Pilih supplier, isi data transaksi, lalu lanjut ke detail pembelian.
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
                    <x-forms.input label="{{ $column['title'] }}" name="{{ $column['name'] }}" type="number" step="{{ $column['name'] === 'nominal' ? '1' : '0.01' }}" value="{{ old($column['name']) }}" />
                </div>
                @elseif($column['type'] === 'select')
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ $column['title'] }}
                    </label>

                    <select
                        name="{{ $column['name'] }}"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/40 searchable-select">

                        
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
                    <a href="{{ route($tablename . '.index') }}" class="text-white font-medium py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors flex items-center justify-center cursor-pointer bg-gray-600 hover:bg-gray-700 focus:ring-gray-500 dark:bg-gray-500 dark:hover:bg-gray-600">
                        {{ __('Batal') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

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
                    noResultsText: 'Supplier tidak ditemukan',
                    placeholder: true,
                    placeholderValue: 'Cari atau Pilih Supplier',
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