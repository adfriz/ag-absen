<div class="relative min-h-screen flex items-center justify-center bg-slate-950 text-slate-100 overflow-hidden dark">
    @vite(['resources/css/app.css'])

    <!-- Background Glow Effects -->
    <div class="absolute w-[500px] h-[500px] bg-emerald-600/10 rounded-full blur-[120px] -top-40 -left-40 pointer-events-none"></div>
    <div class="absolute w-[500px] h-[500px] bg-cyan-600/10 rounded-full blur-[120px] -bottom-40 -right-40 pointer-events-none"></div>

    <div class="max-w-md w-full px-6 relative z-10 py-12">
        <!-- HEADER LOGO -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-tr from-emerald-600 to-cyan-500 rounded-3xl shadow-xl shadow-cyan-950/50 mb-5 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
            </div>
            <h1 class="text-3xl font-black text-white tracking-tight">Sistem Absensi Al Ghazaly</h1>
            <p class="text-slate-400 text-sm mt-1.5 font-medium">Silakan masuk ke akun Anda</p>
        </div>

        <!-- MAIN GLASSMORPHIC CARD -->
        <div class="bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 rounded-[32px] p-8 shadow-2xl shadow-slate-950/80 relative overflow-hidden">
            <form wire:submit.prevent="authenticate" class="space-y-6">
                {{ $this->form }}

                <x-filament-panels::form.actions
                    :actions="$this->getCachedFormActions()"
                    :full-width="$this->hasFullWidthFormActions()"
                />
            </form>
        </div>

        <p class="text-center text-xs text-slate-500 mt-8 font-medium">© 2026 Al Ghazaly School Attendance System</p>
    </div>
</div>
