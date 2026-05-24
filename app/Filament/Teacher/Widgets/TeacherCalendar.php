<?php

namespace App\Filament\Teacher\Widgets;

use Filament\Widgets\Widget;
use Carbon\Carbon;

class TeacherCalendar extends Widget
{
    protected static string $view = 'filament.teacher.widgets.teacher-calendar';

    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    public $selectedDate;
    public $currentYear;
    public $currentMonth;

    public function mount()
    {
        $this->selectedDate = Carbon::today()->toDateString();
        $this->currentYear = Carbon::today()->year;
        $this->currentMonth = Carbon::today()->month;
    }

    public function selectDate($day)
    {
        $this->selectedDate = Carbon::create($this->currentYear, $this->currentMonth, $day)->toDateString();
        $this->dispatch('date-selected', date: $this->selectedDate);
    }

    public function goToToday()
    {
        $today = Carbon::today();
        $this->currentYear = $today->year;
        $this->currentMonth = $today->month;
        $this->selectDate($today->day);
    }

    public function updatedCurrentMonth()
    {
        $this->autoSelectValidDay();
    }

    public function updatedCurrentYear()
    {
        $this->autoSelectValidDay();
    }

    protected function autoSelectValidDay()
    {
        $previousDay = Carbon::parse($this->selectedDate)->day;
        $daysInMonth = Carbon::create($this->currentYear, $this->currentMonth, 1)->daysInMonth;
        $targetDay = min($previousDay, $daysInMonth);
        
        $this->selectDate($targetDay);
    }

    public function previousMonth()
    {
        $current = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentYear = $current->year;
        $this->currentMonth = $current->month;
        $this->autoSelectValidDay();
    }

    public function nextMonth()
    {
        $current = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentYear = $current->year;
        $this->currentMonth = $current->month;
        $this->autoSelectValidDay();
    }
}
