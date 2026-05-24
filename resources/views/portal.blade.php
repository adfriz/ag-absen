<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Absensi - Al Ghazaly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        evergreen: {
                            50: 'rgb(var(--evergreen-50) / <alpha-value>)',
                            100: 'rgb(var(--evergreen-100) / <alpha-value>)',
                            200: 'rgb(var(--evergreen-200) / <alpha-value>)',
                            300: 'rgb(var(--evergreen-300) / <alpha-value>)',
                            400: 'rgb(var(--evergreen-400) / <alpha-value>)',
                            500: 'rgb(var(--evergreen-500) / <alpha-value>)',
                            600: 'rgb(var(--evergreen-600) / <alpha-value>)',
                            700: 'rgb(var(--evergreen-700) / <alpha-value>)',
                            800: 'rgb(var(--evergreen-800) / <alpha-value>)',
                            900: 'rgb(var(--evergreen-900) / <alpha-value>)',
                            950: 'rgb(var(--evergreen-950) / <alpha-value>)',
                        },
                        primary: {
                            50: 'rgb(var(--primary-50) / <alpha-value>)',
                            100: 'rgb(var(--primary-100) / <alpha-value>)',
                            200: 'rgb(var(--primary-200) / <alpha-value>)',
                            300: 'rgb(var(--primary-300) / <alpha-value>)',
                            400: 'rgb(var(--primary-400) / <alpha-value>)',
                            500: 'rgb(var(--primary-500) / <alpha-value>)',
                            600: 'rgb(var(--primary-600) / <alpha-value>)',
                            700: 'rgb(var(--primary-700) / <alpha-value>)',
                            800: 'rgb(var(--primary-800) / <alpha-value>)',
                            900: 'rgb(var(--primary-900) / <alpha-value>)',
                            950: 'rgb(var(--primary-950) / <alpha-value>)',
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;850&display=swap" rel="stylesheet">
    
    @filamentStyles
    @livewireStyles
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }

        :root {
            --evergreen-50: 236, 251, 233;
            --evergreen-100: 217, 248, 211;
            --evergreen-200: 180, 240, 168;
            --evergreen-300: 142, 233, 124;
            --evergreen-400: 105, 225, 81;
            --evergreen-500: 67, 218, 37;
            --evergreen-600: 54, 174, 30;
            --evergreen-700: 40, 131, 22;
            --evergreen-800: 27, 87, 15;
            --evergreen-900: 13, 44, 7;
            --evergreen-950: 9, 31, 5;

            --primary-50: 236, 251, 233;
            --primary-100: 217, 248, 211;
            --primary-200: 180, 240, 168;
            --primary-300: 142, 233, 124;
            --primary-400: 105, 225, 81;
            --primary-500: 67, 218, 37;
            --primary-600: 54, 174, 30;
            --primary-700: 40, 131, 22;
            --primary-800: 27, 87, 15;
            --primary-900: 13, 44, 7;
            --primary-950: 9, 31, 5;
        }

        .dark {
            --evergreen-50: 241, 252, 246;
            --evergreen-100: 225, 247, 235;
            --evergreen-200: 195, 238, 214;
            --evergreen-300: 150, 224, 183;
            --evergreen-400: 100, 203, 147;
            --evergreen-500: 61, 177, 117;
            --evergreen-600: 45, 145, 93;
            --evergreen-700: 38, 116, 76;
            --evergreen-800: 32, 92, 63;
            --evergreen-900: 27, 76, 53;
            --evergreen-950: 15, 42, 29;

            --primary-50: 241, 252, 246;
            --primary-100: 225, 247, 235;
            --primary-200: 195, 238, 214;
            --primary-300: 150, 224, 183;
            --primary-400: 100, 203, 147;
            --primary-500: 61, 177, 117;
            --primary-600: 45, 145, 93;
            --primary-700: 38, 116, 76;
            --primary-800: 32, 92, 63;
            --primary-900: 27, 76, 53;
            --primary-950: 15, 42, 29;
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 flex items-center justify-center min-h-screen relative overflow-hidden" x-data="{ showLogin: false, role: 'guru' }">
    
    <!-- Background Glow Effects -->
    <div class="absolute w-[500px] h-[500px] bg-evergreen-600/10 rounded-full blur-[120px] -top-40 -left-40 pointer-events-none"></div>
    <div class="absolute w-[500px] h-[500px] bg-emerald-600/10 rounded-full blur-[120px] -bottom-40 -right-40 pointer-events-none"></div>

    <div class="max-w-md w-full px-6 relative z-10">
        
        <!-- HEADER LOGO -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-tr from-evergreen-600 to-emerald-500 rounded-3xl shadow-xl shadow-evergreen-950/50 mb-5 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
            </div>
            <h1 class="text-3xl font-black text-white tracking-tight">Sistem Absensi Al Ghazaly</h1>
            <p class="text-slate-400 text-sm mt-1.5 font-medium">Portal akses presensi harian internal sekolah</p>
        </div>

        <!-- MAIN GLASSMORPHIC CARD -->
        <div class="bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 rounded-[32px] p-8 shadow-2xl shadow-slate-950/80 relative overflow-hidden min-h-[380px] flex flex-col justify-center">
            
            <!-- STATE A: ROLE SELECTION -->
            <div x-show="!showLogin" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 -translate-x-12"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 -translate-x-12"
                 class="space-y-5"
             >
                <div class="pb-2">
                    <h2 class="text-lg font-bold text-white uppercase tracking-wider text-center">Pilih Akses Masuk</h2>
                    <p class="text-xs text-slate-400 text-center mt-1">Silakan pilih peran Anda untuk masuk ke system.</p>
                </div>

                <!-- BUTTON GURU -->
                <button 
                    @click="role = 'guru'; showLogin = true"
                    class="w-full flex items-center gap-5 p-5 bg-gradient-to-r from-evergreen-950/40 to-slate-900/40 hover:from-evergreen-600 hover:to-evergreen-500 rounded-2xl border border-slate-800 hover:border-evergreen-400/50 shadow-md group transition-all duration-300 text-left active:scale-[0.98]"
                >
                    <div class="w-12 h-12 rounded-xl bg-evergreen-500/10 group-hover:bg-white/10 flex items-center justify-center text-evergreen-400 group-hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.57 50.57 0 0 0-2.658-.813A5.906 5.906 0 0 1 12 3.48a5.905 5.905 0 0 1 6.87 5.854 50.45 50.45 0 0 0-2.658.813M4.26 10.147a49.048 49.048 0 0 1 15.48 0m-15.48 0a50.562 50.562 0 0 1 15.48 0M12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM6.5 18a2.25 2.25 0 1 1 0-4.5 2.25 2.25 0 0 1 0 4.5ZM17.5 18a2.25 2.25 0 1 1 0-4.5 2.25 2.25 0 0 1 0 4.5Z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-base font-black text-white group-hover:text-white transition-colors">Masuk sebagai Guru</p>
                        <p class="text-xs text-slate-400 group-hover:text-white/80 mt-0.5 font-medium transition-colors">Input absensi & delegasi pengajaran</p>
                    </div>
                </button>

                <!-- BUTTON ADMIN -->
                <button 
                    @click="role = 'admin'; showLogin = true"
                    class="w-full flex items-center gap-5 p-5 bg-gradient-to-r from-slate-900/40 to-slate-950/40 hover:from-amber-600 hover:to-amber-500 rounded-2xl border border-slate-800 hover:border-amber-400/50 shadow-md group transition-all duration-300 text-left active:scale-[0.98]"
                >
                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 group-hover:bg-white/10 flex items-center justify-center text-amber-400 group-hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-base font-black text-white group-hover:text-white transition-colors">Masuk sebagai Admin</p>
                        <p class="text-xs text-slate-400 group-hover:text-white/80 mt-0.5 font-medium transition-colors">Kelola data master & perizinan guru</p>
                    </div>
                </button>
            </div>

            <!-- STATE B: LOGIN FORM -->
            <div x-show="showLogin" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-x-12"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-12"
                 class="space-y-6"
            >
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <div>
                        <h2 class="text-lg font-black text-white uppercase tracking-wider flex items-center gap-2">
                            Login <span x-text="role === 'admin' ? 'Admin' : 'Guru'" class="text-evergreen-400"></span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">Masukkan kredensial akun Anda.</p>
                    </div>
                    
                    <!-- BUTTON BACK -->
                    <button 
                        @click="showLogin = false"
                        class="flex items-center gap-1 text-[11px] font-bold text-slate-400 hover:text-white bg-slate-800/80 px-3 py-1.5 rounded-xl border border-slate-700/60 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                        Kembali
                    </button>
                </div>

                <!-- MOUNT LIVEWIRE LOGIN DYNAMICALLY -->
                <div x-show="role === 'guru'">
                    @livewire('portal-login', ['role' => 'guru'], key('login-guru'))
                </div>
                <div x-show="role === 'admin'">
                    @livewire('portal-login', ['role' => 'admin'], key('login-admin'))
                </div>
            </div>

        </div>

        <p class="text-center text-xs text-slate-500 mt-8 font-medium">© 2026 Al Ghazaly School Attendance System</p>
    </div>

    @filamentScripts
    @livewireScripts
</body>
</html>
