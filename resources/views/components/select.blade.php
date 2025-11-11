@props(['id' => '', 'name' => ''])

<select id="{{ $id }}" name="{{ $name }}" {{ $attributes->merge(['class' => 'w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 text-white']) }}>
    {{ $slot }}
</select>
