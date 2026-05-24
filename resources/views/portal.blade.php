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
<body class="bg-slate-950 text-slate-100 flex items-center justify-center min-h-screen relative overflow-hidden">
    
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
        <div class="bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 rounded-[32px] p-8 shadow-2xl shadow-slate-950/80 relative overflow-hidden min-h-[300px] flex flex-col justify-center">
            @livewire('portal-login')
        </div>

        <p class="text-center text-xs text-slate-500 mt-8 font-medium">© 2026 Al Ghazaly School Attendance System</p>
    </div>

    @filamentScripts
    @livewireScripts
</body>
</html>
