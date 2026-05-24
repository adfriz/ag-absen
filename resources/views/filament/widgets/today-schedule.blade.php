<x-filament-widgets::widget class="fi-wi-table relative">
    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Widgets\View\WidgetsRenderHook::TABLE_WIDGET_START, scopes: static::class) }}

    {{-- Content --}}
    <div wire:loading.remove>
        {{ $this->table }}
    </div>

    {{-- Skeleton Loader --}}
    <div wire:loading.grid class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
        @for ($i = 0; $i < 4; $i++)
            <div class="p-6 bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm animate-pulse flex flex-col gap-4">
                {{-- Header --}}
                <div class="flex justify-between items-center pb-2 border-b border-gray-50 dark:border-gray-800/50">
                    <div class="h-6 w-24 bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                    <div class="h-5 w-28 bg-gray-200/80 dark:bg-gray-700/80 rounded-md"></div>
                </div>
                
                {{-- Body Details --}}
                <div class="space-y-3 flex-1">
                    <div class="flex items-center gap-2.5">
                        <div class="w-4 h-4 rounded bg-gray-200 dark:bg-gray-700 flex-shrink-0"></div>
                        <div class="h-4 w-40 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <div class="w-4 h-4 rounded bg-gray-200 dark:bg-gray-700 flex-shrink-0"></div>
                        <div class="h-4 w-48 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    </div>
                    <div class="pt-1">
                        <div class="h-6 w-32 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                    </div>
                </div>

                {{-- Action Button --}}
                <div class="h-9 w-full bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
            </div>
        @endfor
    </div>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Widgets\View\WidgetsRenderHook::TABLE_WIDGET_END, scopes: static::class) }}
</x-filament-widgets::widget>
