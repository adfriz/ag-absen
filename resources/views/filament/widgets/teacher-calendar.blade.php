<x-filament-widgets::widget>
    <x-filament::section>
        <div id="custom-teacher-calendar" class="p-1">

            <!-- Calendar Header (Month Browser) -->
            <div class="flex items-center justify-between mb-5 pb-3 border-b border-gray-100 dark:border-gray-800/50">
                <!-- Dropdown selectors for Month & Year -->
                <div class="flex items-center gap-1">
                    <!-- Month Select -->
                    <select 
                        wire:model.live="currentMonth" 
                        class="text-sm font-bold text-gray-800 dark:text-gray-200 bg-transparent border-0 py-1 pl-2 pr-8 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/80 focus:ring-0 cursor-pointer uppercase tracking-wider focus:outline-none transition duration-150 ease-out"
                    >
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" class="bg-white dark:bg-gray-900 text-gray-850 dark:text-gray-150">
                                {{ \Carbon\Carbon::create(null, $m, 1)->locale('id')->isoFormat('MMMM') }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Year Select -->
                    <select 
                        wire:model.live="currentYear" 
                        class="text-sm font-bold text-gray-800 dark:text-gray-200 bg-transparent border-0 py-1 pl-2 pr-8 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/80 focus:ring-0 cursor-pointer uppercase tracking-wider focus:outline-none transition duration-150 ease-out"
                    >
                        @php
                            $startYear = \Carbon\Carbon::today()->year - 4;
                            $endYear = \Carbon\Carbon::today()->year + 2;
                        @endphp
                        @foreach(range($startYear, $endYear) as $y)
                            <option value="{{ $y }}" class="bg-white dark:bg-gray-900 text-gray-850 dark:text-gray-150">
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-center gap-1.5">
                    <!-- Previous Month -->
                    <button 
                        type="button"
                        wire:click="previousMonth"
                        class="p-2 bg-gray-50 hover:bg-primary-50 dark:bg-gray-800/50 dark:hover:bg-gray-700/80 text-gray-500 dark:text-gray-400 rounded-full border-0 outline-none focus:outline-none focus:ring-0 transition duration-150 ease-out active:scale-95"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </button>

                    <!-- Today Button -->
                    <button 
                        type="button"
                        wire:click="goToToday"
                        class="px-3 py-1.5 bg-gray-50 hover:bg-primary-600 dark:bg-gray-800/50 dark:hover:bg-primary-600 text-[10px] font-bold text-gray-500 hover:text-white dark:text-gray-300 dark:hover:text-white rounded-full border-0 outline-none focus:outline-none focus:ring-0 uppercase tracking-wider transition duration-150 ease-out active:scale-95"
                    >
                        Hari Ini
                    </button>

                    <!-- Next Month -->
                    <button 
                        type="button"
                        wire:click="nextMonth"
                        class="p-2 bg-gray-50 hover:bg-primary-50 dark:bg-gray-800/50 dark:hover:bg-gray-700/80 text-gray-500 dark:text-gray-400 rounded-full border-0 outline-none focus:outline-none focus:ring-0 transition duration-150 ease-out active:scale-95"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-1 text-center">
                <!-- Day Names -->
                @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $dayName)
                    <div class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase py-2 tracking-wider">{{ $dayName }}</div>
                @endforeach
            </div>

            @php
                $startOfMonth = \Carbon\Carbon::create($currentYear, $currentMonth, 1)->startOfMonth();
                $endOfMonth = \Carbon\Carbon::create($currentYear, $currentMonth, 1)->endOfMonth();
                $daysInMonth = $endOfMonth->day;
                $firstDayOfWeek = $startOfMonth->dayOfWeek;
                
                $todayDate = \Carbon\Carbon::today()->toDateString();
            @endphp

            <!-- Calendar Days Container (Relative to keep height stable) -->
            <div class="relative min-h-[180px] sm:min-h-[220px]">
                
                <!-- Active Days (Visible when not loading) -->
                <div wire:loading.remove class="grid grid-cols-7 gap-y-2 gap-x-1 text-center">
                    <!-- Empty Padding Days -->
                    @for($i = 0; $i < $firstDayOfWeek; $i++)
                        <div class="aspect-square flex items-center justify-center"></div>
                    @endfor
                    
                    <!-- Active Days -->
                    @for($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $loopDate = \Carbon\Carbon::create($currentYear, $currentMonth, $day)->toDateString();
                            $isSelected = ($loopDate === $selectedDate);
                            $isToday = ($loopDate === $todayDate);
                        @endphp
                        <button
                            type="button"
                            wire:click="selectDate({{ $day }})"
                            class="w-8 h-8 sm:w-9 sm:h-9 md:w-10 md:h-10 mx-auto text-xs font-semibold rounded-full flex items-center justify-center relative group border-0 transition duration-150 ease-out hover:scale-105 active:scale-90 outline-none focus:outline-none focus:ring-0
                                   {{ $isSelected 
                                       ? 'bg-primary-600 text-white font-bold shadow-md shadow-primary-500/30 dark:shadow-primary-950/50 scale-105' 
                                       : ($isToday 
                                           ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold hover:bg-emerald-500/20 dark:hover:bg-emerald-500/30' 
                                           : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/60 hover:text-primary-600 dark:hover:text-primary-400') }}"
                        >
                            {{ $day }}
                            
                            <!-- Today Dot indicator if not selected -->
                            @if($isToday && !$isSelected)
                                <span class="absolute bottom-1 w-1 h-1 bg-emerald-500 dark:bg-emerald-400 rounded-full"></span>
                            @endif
                        </button>
                    @endfor
                </div>

                <!-- Skeleton Days (Visible when loading) -->
                <div wire:loading.grid class="grid grid-cols-7 gap-y-2 gap-x-1 text-center w-full absolute inset-0 bg-transparent">
                    <!-- Empty Padding Days -->
                    @for($i = 0; $i < $firstDayOfWeek; $i++)
                        <div class="aspect-square flex items-center justify-center"></div>
                    @endfor
                    
                    <!-- Pulsing Day Placeholders -->
                    @for($day = 1; $day <= $daysInMonth; $day++)
                        <div class="w-8 h-8 sm:w-9 sm:h-9 md:w-10 md:h-10 mx-auto bg-gray-100 dark:bg-gray-800/60 rounded-full animate-pulse flex items-center justify-center text-transparent select-none">
                            00
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
