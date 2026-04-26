<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Events Map') }}
        </h2>
    </x-slot>

    <livewire:map-view />
</x-app-layout>
