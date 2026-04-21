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

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Edit {{ $title }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Update {{ $title }} details</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <form action="{{ route($tablename . '.update', $data) }}" method="POST" class="max-w-2xl">
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
                        class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm p-2">

                        
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
                    <x-button type="primary">{{ __('Update') }}</x-button>
                    <a href="{{ route($tablename . '.index') }}">
                        <x-button type="secondary">{{ __('Cancel') }}</x-button>
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>