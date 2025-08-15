<x-filament-panels::page>
    {{ $this->form }}

    <x-filament::button color="primary" class="mt-4" wire:click="save">
        Update Password
    </x-filament::button>
</x-filament-panels::page>
