<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" size="md" color="primary">
                Simpan Password Baru
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
