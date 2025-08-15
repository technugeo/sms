@if (session('password_updated'))
    <div class="mb-4 p-3 text-sm text-green-700 bg-green-100 rounded-lg">
        {{ session('password_updated') }}
    </div>
@endif



<x-filament::page>
    <form wire:submit.prevent="authenticate" class="space-y-6 max-w-md mx-auto">
        <x-filament::input
            id="email"
            type="email"
            wire:model.defer="email"
            placeholder="Email"
            required
            autofocus
        />
        @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror

        <x-filament::input
            id="password"
            type="password"
            wire:model.defer="password"
            placeholder="Password"
            required
        />
        @error('password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror

        <button type="submit" class="filament-button filament-button-primary w-full">
            Login
        </button>
    </form>
</x-filament::page>
