@props([
'label',
'name',
'type' => 'text',
'placeholder' => '',
'error' => false,
'class' => '',
'labelClass' => '',
'prepend' => null,
'append' => null,
'disabled' => null,
'readonly' => null,
])

@if ($label)
<label for="{{ $name }}"
    {{ $attributes->merge(['class' => 'block ml-1 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 ' . $labelClass]) }}>
    {{ $label }}
</label>
@endif
<div class="flex">
    @if($prepend)
    <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-e-0 border-gray-300 rounded-s-lg dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
        {{ $prepend }}
    </span>
    @endif

    @if($disabled)
    <input type="{{ $type }}" id="{{ $name }}" placeholder="{{ $placeholder }}" name="{{ $name }}" disabled 
    


        {{ $attributes->merge([
            'class' => 'w-full px-4 py-1.5 text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent ' . 
            ($prepend ? 'rounded-none ' : 'rounded-s-lg ') . 
            ($append ? 'rounded-none' : 'rounded-e-lg')
        ]) }}>


    @elseif($readonly)
    <input type="{{ $type }}" id="{{ $name }}" placeholder="{{ $placeholder }}" name="{{ $name }}" readonly

        {{ $attributes->merge([
            'class' => 'w-full px-4 py-1.5 text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent ' . 
            ($prepend ? 'rounded-none ' : 'rounded-s-lg ') . 
            ($append ? 'rounded-none' : 'rounded-e-lg')
        ]) }}>

    @else


    <input type="{{ $type }}" id="{{ $name }}" placeholder="{{ $placeholder }}" name="{{ $name }}" 
    


        {{ $attributes->merge([
            'class' => 'w-full px-4 py-1.5 text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent ' . 
            ($prepend ? 'rounded-none ' : 'rounded-s-lg ') . 
            ($append ? 'rounded-none' : 'rounded-e-lg')
        ]) }}>

    @endif

    @if($append)
    <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-s-0 border-gray-300 rounded-e-lg dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
        {{ $append }}
    </span>
    @endif
</div>
@error($name)
<span class="text-red-500">{{ $message }}</span>
@enderror