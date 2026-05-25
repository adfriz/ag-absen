<div class="relative min-h-screen flex items-center justify-center bg-slate-950 text-slate-100 overflow-hidden dark custom-login-page" x-data="{ forgotOpen: $wire.entangle('forgotOpen') }">
    @vite(['resources/css/app.css'])

    <style>
        [x-cloak] { display: none !important; }
    </style>

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

        <!-- MAIN CARD -->
        <div class="bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 rounded-[32px] p-8 shadow-2xl shadow-slate-950/80 relative overflow-hidden">
            <form wire:submit.prevent="authenticate" class="space-y-6">
                {{ $this->form }}

                <x-filament-panels::form.actions
                    :actions="$this->getCachedFormActions()"
                    :full-width="$this->hasFullWidthFormActions()" />

                <div class="text-center pt-2 border-t border-slate-800/50">
                    <a href="#" @click.prevent="forgotOpen = true; $wire.resetResetState()" class="text-xs text-emerald-500 hover:text-emerald-400 font-semibold transition duration-150">
                        Lupa Password?
                    </a>
                </div>
            </form>
        </div>

        <p class="text-center text-xs text-slate-500 mt-8 font-medium">© 2026 Al Ghazaly School Attendance System</p>
    </div>

    <!-- Forgot Password Modal -->
    <div x-show="forgotOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md"
         style="z-index: 99999;"
         x-cloak>
        
        <div @click.outside="forgotOpen = false" 
             class="border border-slate-800 rounded-3xl max-w-md w-full p-8 shadow-2xl relative overflow-hidden" 
             style="background-color: #0f172a;">
            <div class="absolute w-[300px] h-[300px] bg-emerald-600/5 rounded-full blur-[100px] -top-20 -left-20 pointer-events-none"></div>

            <div class="flex justify-between items-center mb-6 border-b border-slate-800/50 pb-3">
                <h3 class="text-lg font-bold text-white tracking-tight">Lupa Password</h3>
                <button type="button" @click="forgotOpen = false" class="text-slate-400 hover:text-white transition duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            @if($resetSuccess)
                <div class="space-y-4">
                    <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-2xl text-sm leading-relaxed">
                        <strong>Permintaan Terkirim!</strong><br>
                        Permintaan reset password Anda telah terkirim ke Admin. Silakan tunggu konfirmasi selanjutnya.
                    </div>
                    <button type="button" @click="forgotOpen = false" class="w-full py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-sm font-semibold transition duration-150">
                        Tutup
                    </button>
                </div>
            @else
                <form wire:submit.prevent="requestPasswordReset" class="space-y-4">
                    <p class="text-slate-400 text-xs leading-relaxed">
                        Masukkan Username / NIP Anda. Kami akan mengirimkan permintaan reset password ke dashboard Administrator sekolah.
                    </p>

                    <div>
                        <label for="forgotUsername" class="block text-xs font-semibold text-slate-350 mb-1.5">Username / NIP</label>
                        <input type="text" 
                               id="forgotUsername" 
                               wire:model="forgotUsername" 
                               placeholder="Contoh: 1980xxxx"
                               class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800/80 rounded-xl text-white text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none transition duration-155 placeholder:text-slate-700"
                               required>
                        @error('forgotUsername')
                            <span class="text-xs text-rose-500 mt-1.5 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="pt-2 flex gap-3">
                        <button type="button" @click="forgotOpen = false" class="flex-1 py-2.5 bg-slate-950 hover:bg-slate-800 border border-slate-850 text-slate-400 rounded-xl text-sm font-semibold transition duration-150">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-sm font-semibold shadow-lg shadow-emerald-950/50 transition duration-150">
                            Kirim Permintaan
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>