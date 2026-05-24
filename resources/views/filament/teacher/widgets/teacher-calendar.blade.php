<x-filament-widgets::widget>
    <x-filament::section>
        <div id="custom-teacher-calendar" class="p-1">

            <!-- Calendar Header (Month Browser) -->
            <div class="flex items-center justify-between mb-6 pb-2 border-b border-gray-100 dark:border-gray-800">
                <!-- Dropdown selectors for Month & Year -->
                <div class="flex items-center gap-1">
                    <!-- Month Select -->
                    <select 
                        wire:model.live="currentMonth" 
                        class="text-xs font-black text-gray-800 dark:text-gray-200 bg-transparent border-0 py-1 pl-1 pr-6 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 focus:ring-0 cursor-pointer uppercase tracking-wider focus:outline-none"
                    >
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" class="bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                                {{ \Carbon\Carbon::create(null, $m, 1)->locale('id')->isoFormat('MMMM') }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Year Select -->
                    <select 
                        wire:model.live="currentYear" 
                        class="text-xs font-black text-gray-800 dark:text-gray-200 bg-transparent border-0 py-1 pl-1 pr-6 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 focus:ring-0 cursor-pointer uppercase tracking-wider focus:outline-none"
                    >
                        @php
                            $startYear = \Carbon\Carbon::today()->year - 4;
                            $endYear = \Carbon\Carbon::today()->year + 2;
                        @endphp
                        @foreach(range($startYear, $endYear) as $y)
                            <option value="{{ $y }}" class="bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
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
                        class="p-2 bg-gray-100 hover:bg-primary-50 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg border-0 outline-none focus:outline-none focus:ring-0 transition duration-200 ease-out hover:scale-105 active:scale-95"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </button>

                    <!-- Today Button -->
                    <button 
                        type="button"
                        wire:click="goToToday"
                        class="px-3 py-1.5 bg-gray-100 hover:bg-primary-600 dark:bg-gray-800 dark:hover:bg-primary-600 text-[10px] font-black text-gray-600 hover:text-white dark:text-gray-300 dark:hover:text-white rounded-lg border-0 outline-none focus:outline-none focus:ring-0 uppercase tracking-wider transition duration-200 ease-out hover:scale-105 active:scale-95"
                    >
                        Hari Ini
                    </button>

                    <!-- Next Month -->
                    <button 
                        type="button"
                        wire:click="nextMonth"
                        class="p-2 bg-gray-100 hover:bg-primary-50 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg border-0 outline-none focus:outline-none focus:ring-0 transition duration-200 ease-out hover:scale-105 active:scale-95"
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
                    <div class="text-[9px] font-extrabold text-primary-600 dark:text-primary-400 uppercase py-1 tracking-widest">{{ $dayName }}</div>
                @endforeach
                
                @php
                    $startOfMonth = \Carbon\Carbon::create($currentYear, $currentMonth, 1)->startOfMonth();
                    $endOfMonth = \Carbon\Carbon::create($currentYear, $currentMonth, 1)->endOfMonth();
                    $daysInMonth = $endOfMonth->day;
                    $firstDayOfWeek = $startOfMonth->dayOfWeek;
                    
                    $todayDate = \Carbon\Carbon::today()->toDateString();
                @endphp
                
                <!-- Empty Padding Days -->
                @for($i = 0; $i < $firstDayOfWeek; $i++)
                    <div class="py-2"></div>
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
                        class="py-2.5 text-xs font-bold rounded-lg flex flex-col items-center justify-center relative group border-0 transition duration-200 ease-out hover:-translate-y-0.5 hover:scale-105 active:scale-90 outline-none focus:outline-none focus:ring-0
                               {{ $isSelected 
                                   ? 'bg-primary-600 text-white font-black shadow-md shadow-primary-500/40 dark:shadow-primary-950/60 scale-105 ring-2 ring-primary-500/20' 
                                   : ($isToday 
                                       ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-extrabold hover:bg-emerald-500/20 dark:hover:bg-emerald-500/30' 
                                       : 'text-gray-600 dark:text-gray-400 hover:bg-primary-50/80 dark:hover:bg-gray-800/80 hover:text-primary-600 dark:hover:text-primary-400') }}"
                    >
                        {{ $day }}
                        
                        <!-- Today Dot indicator if not selected -->
                        @if($isToday && !$isSelected)
                            <span class="absolute bottom-1 w-1.5 h-1.5 bg-emerald-500 dark:bg-emerald-400 rounded-full"></span>
                        @endif
                    </button>
                @endfor
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
