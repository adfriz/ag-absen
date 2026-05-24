<x-filament::section>
    <x-slot name="heading">
        Masuk ke Sistem
    </x-slot>
    
    <x-slot name="description">
        Masukkan username dan password Anda.
    </x-slot>

    <form wire:submit.prevent="authenticate" class="space-y-6">
        {{ $this->form }}

        <x-filament::button type="submit" size="lg" class="w-full" wire:target="authenticate">
            Sign In
        </x-filament::button>
    </form>
</x-filament::section>
