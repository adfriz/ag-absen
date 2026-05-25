<div class="relative min-h-screen flex items-center justify-center transition-colors duration-300 overflow-hidden custom-login-page"
    x-data="{
        forgotOpen: $wire.entangle('forgotOpen'),
        darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
    }"
    x-init="
        $watch('darkMode', val => {
            if (val) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        });
        if (darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    "
    x-bind:class="{ 'dark': darkMode, 'bg-slate-950 text-slate-100': darkMode, 'bg-slate-50 text-slate-900': !darkMode }">
    @vite(['resources/css/app.css'])

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <!-- Theme Toggle Button -->
    <div class="absolute top-4 right-4 z-50">
        <div x-show="darkMode" x-cloak>
            <x-filament::icon-button
                icon="heroicon-m-sun"
                color="gray"
                size="lg"
                label="Light Mode"
                @click="darkMode = false; localStorage.setItem('theme', 'light')" />
        </div>
        <div x-show="!darkMode" x-cloak>
            <x-filament::icon-button
                icon="heroicon-m-moon"
                color="gray"
                size="lg"
                label="Dark Mode"
                @click="darkMode = true; localStorage.setItem('theme', 'dark')" />
        </div>
    </div>

    <!-- Background Gradient Effects -->
    <div class="absolute w-[500px] h-[500px] rounded-full blur-[120px] -top-40 -left-40 pointer-events-none 
    bg-gradient-to-r from-[#60D752]/20 dark:from-[#60D752]/30 via-transparent to-[#60D752]/20 dark:to-[#60D752]/30">
    </div>

    <div class="absolute w-[500px] h-[500px] rounded-full blur-[120px] -bottom-40 -right-40 pointer-events-none 
    bg-gradient-to-r from-cyan-600/20 dark:from-cyan-600/30 via-transparent to-cyan-600/20 dark:to-cyan-600/30">
    </div>

    <div class="max-w-md w-full px-6 relative z-10 py-12">
        <!-- HEADER LOGO -->
        <div class="text-center mb-8">
            <img src="{{ asset('img/Logo.svg')}}" class="w-48 h-48 mx-auto mb-5" alt="Logo">
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Sistem Absensi Al Ghazaly</h1>
            <p class="text-slate-600 dark:text-slate-400 text-sm mt-1.5 font-medium">Silakan masuk ke akun Anda</p>
        </div>

        <!-- MAIN CARD -->
        <div class="bg-white/80 dark:bg-slate-900/60 backdrop-blur-xl border border-slate-200 dark:border-slate-800/80 rounded-[32px] p-8 shadow-2xl shadow-slate-200/50 dark:shadow-slate-950/80 relative overflow-hidden">
            <form wire:submit.prevent="authenticate" class="space-y-6">
                {{ $this->form }}

                <x-filament-panels::form.actions
                    :actions="$this->getCachedFormActions()"
                    :full-width="$this->hasFullWidthFormActions()" />

                <div class="text-center pt-2 border-t border-slate-200 dark:border-slate-800/50">
                    <a href="#" @click.prevent="forgotOpen = true; $wire.resetResetState()" class="text-xs text-emerald-600 dark:text-emerald-500 hover:text-emerald-550 dark:hover:text-emerald-400 font-semibold transition duration-150">
                        Lupa Password?
                    </a>
                </div>
            </form>
        </div>

        <p class="text-center text-xs text-slate-400 dark:text-slate-500 mt-8 font-medium">© 2026 Al Ghazaly School Attendance System</p>
    </div>

    <!-- Forgot Password Modal -->
    <div x-show="forgotOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 flex items-center justify-center p-4 bg-slate-950/40 dark:bg-slate-950/80 backdrop-blur-md"
        style="z-index: 99999;"
        x-cloak>

        <div @click.outside="forgotOpen = false"
            class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-md w-full p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute w-[300px] h-[300px] bg-emerald-600/5 rounded-full blur-[100px] -top-20 -left-20 pointer-events-none"></div>

            <div class="flex justify-between items-center mb-6 border-b border-slate-200 dark:border-slate-800/50 pb-3">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Lupa Password</h3>
                <button type="button" @click="forgotOpen = false" class="text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            @if($resetSuccess)
            <div class="space-y-4">
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-2xl text-sm leading-relaxed">
                    <strong>Permintaan Terkirim!</strong><br>
                    Permintaan reset password Anda telah terkirim ke Admin. Silakan tunggu konfirmasi selanjutnya.
                </div>
                <button type="button" @click="forgotOpen = false" class="w-full py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-white rounded-xl text-sm font-semibold transition duration-150">
                    Tutup
                </button>
            </div>
            @else
            <form wire:submit.prevent="requestPasswordReset" class="space-y-4">
                <p class="text-slate-500 dark:text-slate-400 text-xs leading-relaxed">
                    Masukkan Username / NIP Anda. Kami akan mengirimkan permintaan reset password ke dashboard Administrator sekolah.
                </p>

                <div>
                    <label for="forgotUsername" class="block text-xs font-semibold text-slate-700 dark:text-slate-350 mb-1.5">Username / NIP</label>
                    <input type="text"
                        id="forgotUsername"
                        wire:model="forgotUsername"
                        placeholder="Contoh: 1980xxxx"
                        class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800/80 text-slate-900 dark:text-white text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none transition duration-155 placeholder:text-slate-400 dark:placeholder:text-slate-700"
                        required>
                    @error('forgotUsername')
                    <span class="text-xs text-rose-600 dark:text-rose-505 mt-1.5 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="button" @click="forgotOpen = false" class="flex-1 py-2.5 bg-white dark:bg-slate-950 hover:bg-slate-50 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-850 text-slate-700 dark:text-slate-400 rounded-xl text-sm font-semibold transition duration-150">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-sm font-semibold shadow-lg shadow-emerald-100/50 dark:shadow-emerald-950/50 transition duration-150">
                        Kirim Permintaan
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>