<x-layouts.app>
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}"
            class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Dashboard') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <a href="{{ route($tablename . '.index') }}"
            class="text-blue-600 dark:text-blue-400 hover:underline">{{ $title ?? __('Produks') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-500 dark:text-gray-400">{{ __('Edit') }}</span>
    </div>

    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Edit {{ $title }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $subheading ?? __('Ubah detail pembelian di bawah ini') }}</p>
        </div>
        <div class="flex gap-2">
            <x-button type="primary" form="pembelianForm">Edit Detail Pembelian</x-button>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6 min-h-[500px]">
            <form id="pembelianForm" action="{{ route('pembelians.update', $data) }}" method="POST" class="max-w-2xl">
                @csrf
                @method('PUT')

                @foreach($columns as $column)
                @if(!$column['inform'] )
                @continue
                @endif

                @if($column['type'] === 'text')
                <div class="mb-4">
                    <x-forms.input label="{{ $column['title'] }}" name="{{ $column['name'] }}" type="text" value="{{ old($column['name'], $data->{$column['name']}) }}" required />
                </div>


                @elseif($column['type'] === 'email')

                <div class="mb-4">
                    <x-forms.input label="{{ $column['title'] }}" name="{{ $column['name'] }}" type="email" value="{{ old($column['name'], $data->{$column['name']}) }}" required />
                </div>


                @elseif($column['type'] === 'password')

                <div class="mb-4">
                    <x-forms.input label="Password" name="password" type="password" />
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Leave blank to keep current password') }}</p>
                </div>

                <div class="mb-6">
                    <x-forms.input label="Confirm Password" name="password_confirmation" type="password" />
                </div>


                @elseif($column['type'] === 'number')

                <div class="mb-4">
                    <x-forms.input label="{{ $column['title'] }}" name="{{ $column['name'] }}" type="number" step="0.01" value="{{ old($column['name'], $data->{$column['name']}) }}" required />
                </div>




                @elseif($column['type'] === 'select')
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ $column['title'] }}
                    </label>

                    <select
                        name="{{ $column['name'] }}"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/40 searchable-select">


                        @foreach($column['options'] as $option)
                        @if(old($column['value'], $data->{$column['name']}) == $option['value'])
                        <option value="{{ $option['value'] }}" selected>
                            {{ $option['label'] }}
                        </option>
                        @else
                        <option value="{{ $option['value'] }}">
                            {{ $option['label'] }}
                        </option>
                        @endif

                        @endforeach
                    </select>

                    @error($column['name'])
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                @endif
                @endforeach


                <div class="flex gap-3">
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
                    noResultsText: 'Produk tidak ditemukan',
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