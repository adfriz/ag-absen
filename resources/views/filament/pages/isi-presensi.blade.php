<x-filament-panels::page>
    <!-- Banners & Blockers -->
    @if($hariIniLibur)
    <div class="p-4 bg-danger-500/10 border border-danger-500/20 rounded-xl text-danger-600 dark:text-danger-400">
        <h3 class="text-sm font-bold">Hari ini adalah Hari Libur</h3>
        <p class="text-xs mt-1">Keterangan: {{ $namaLibur }}. Pengisian absensi dinonaktifkan.</p>
    </div>
    @elseif($isReadOnly)
    <div class="p-4 bg-info-500/10 border border-info-500/20 rounded-xl text-info-600 dark:text-info-400">
        <h3 class="text-sm font-bold">Mode Lihat (Read-Only)</h3>
        <p class="text-xs mt-1">Anda terdata izin hari ini. Pengisian atau perubahan absensi dinonaktifkan.</p>
    </div>
    @elseif($belumWaktunya)
    <div class="p-4 bg-warning-500/10 border border-warning-500/20 rounded-xl text-warning-600 dark:text-warning-400">
        <h3 class="text-sm font-bold">Belum Waktunya Pelajaran</h3>
        <p class="text-xs mt-1">Pengisian absensi dinonaktifkan hingga waktu pelajaran dimulai ({{ date('H:i', strtotime($jadwal->jam_mulai)) }}).</p>
    </div>
    @endif

    <!-- Success Message -->
    @if($savedMessage)
    <div class="p-4 bg-success-500/10 border border-success-500/20 rounded-xl text-success-600 dark:text-success-400" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
        <p class="text-xs font-bold">{{ $savedMessage }}</p>
    </div>
    @endif

    <!-- Errors -->
    @if(session()->has('error'))
    <div class="p-4 bg-danger-500/10 border border-danger-500/20 rounded-xl text-danger-600 dark:text-danger-400">
        <p class="text-xs font-bold">{{ session('error') }}</p>
    </div>
    @endif

    <!-- Table Container -->
    <x-filament::section>
        <x-slot name="heading">Daftar Kehadiran Siswa</x-slot>
        <x-slot name="description">Klik status kehadiran untuk setiap siswa, lalu simpan.</x-slot>

        @php
        $statuses = [
        'H' => ['color' => '#10b981'], // emerald-500
        'S' => ['color' => '#f59e0b'], // amber-500
        'I' => ['color' => '#3b82f6'], // blue-500
        'A' => ['color' => '#ef4444'], // red-500
        'D' => ['color' => '#6366f1'], // indigo-500
        'T' => ['color' => '#f59e0b'], // amber-500
        ];
        $labels = ['H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin', 'A' => 'Alpa', 'D' => 'Dispensasi', 'T' => 'Terlambat'];
        @endphp

        <!-- Desktop Layout (Table with Fixed Widths) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse table-fixed">
                <colgroup>
                    <col class="w-[60px]">
                    <col class="w-[35%]">
                    <col class="w-[320px]">
                    <col class="">
                </colgroup>
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-800 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Siswa</th>
                        <th class="py-3 px-4 text-center">Status Kehadiran</th>
                        <th class="py-3 px-4">Detail Tambahan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-150 dark:divide-gray-800">
                    @foreach($siswaData as $siswaId => $data)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors" x-data="{ activeStatus: '{{ $data['status'] }}' }">
                        <td class="py-4 px-4 text-sm text-gray-500 font-mono">{{ $loop->iteration }}</td>
                        <td class="py-4 px-4">
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white text-base">{{ $data['nama'] }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 font-mono mt-0.5">NISN: {{ $data['nisn'] }}</p>
                            </div>
                            @if($data['is_locked'])
                            <span class="inline-flex items-center gap-1 text-[10px] bg-danger-500/10 text-danger-600 dark:text-danger-400 border border-danger-500/20 px-2 py-0.5 rounded-full mt-2 font-bold uppercase">
                                Terkunci (Melebihi H+3)
                            </span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex justify-center gap-1">
                                @foreach($statuses as $val => $style)
                                <button
                                    type="button"
                                    x-on:click="activeStatus = '{{ $val }}'; $wire.set('siswaData.{{ $siswaId }}.status', '{{ $val }}', false)"
                                    :disabled="{{ $data['is_locked'] || $hariIniLibur || $belumWaktunya ? 'true' : 'false' }}"
                                    class="px-3 py-1.5 text-xs font-bold rounded-lg border transition-all duration-150 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                    :class="activeStatus === '{{ $val }}' 
                                                ? 'text-white border-transparent' 
                                                : 'bg-transparent border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5'"
                                    :style="activeStatus === '{{ $val }}' ? 'background-color: {{ $style['color'] }}' : ''">
                                    {{ $val }}
                                </button>
                                @endforeach
                            </div>
                            <div
                                class="text-center mt-1 text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider"
                                x-text="{H: 'Hadir', S: 'Sakit', I: 'Izin', A: 'Alpa', D: 'Dispensasi', T: 'Terlambat'}[activeStatus]">
                                {{ $labels[$data['status']] }}
                            </div>
                        </td>
                        <td class="py-4 px-4 text-xs">
                            <div class="space-y-2">
                                <div x-show="activeStatus === 'D'" x-cloak class="space-y-1">
                                    <label class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase block">Keterangan Dispensasi (Wajib)</label>
                                    <x-filament::input
                                        type="text"
                                        wire:model.defer="siswaData.{{ $siswaId }}.catatan"
                                        placeholder="Siswa bertugas..."
                                        :disabled="$data['is_locked'] || $hariIniLibur || $belumWaktunya"
                                        class="w-full" />
                                </div>

                                <div x-show="activeStatus === 'T'" x-cloak class="space-y-1">
                                    <label class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase block">Menit Terlambat (Wajib)</label>
                                    <div class="flex items-center gap-1.5">
                                        <x-filament::input
                                            type="number"
                                            wire:model.defer="siswaData.{{ $siswaId }}.menit_terlambat"
                                            placeholder="Contoh: 20"
                                            :disabled="$data['is_locked'] || $hariIniLibur || $belumWaktunya"
                                            class="w-20" />
                                        <span class="text-gray-400">menit</span>
                                    </div>
                                </div>

                                <div x-show="activeStatus === 'S' || activeStatus === 'I'" x-cloak class="space-y-1">
                                    <label class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase block">Bukti Surat</label>
                                    @if($data['bukti_surat_existing'])
                                    <div class="flex items-center justify-between gap-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-2 rounded-xl text-xs mb-2">
                                        <a href="{{ \Illuminate\Support\Facades\Storage::url($data['bukti_surat_existing']) }}" target="_blank" class="flex items-center gap-1.5 hover:underline font-bold text-primary-600 dark:text-primary-400 truncate max-w-[150px]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                            Lihat Surat
                                        </a>
                                        @if(!$data['is_locked'] && !$hariIniLibur && !$belumWaktunya)
                                        <button
                                            type="button"
                                            wire:click="hapusBuktiSurat({{ $siswaId }})"
                                            class="text-danger-600 dark:text-danger-400 hover:text-danger-700 font-bold flex items-center gap-1 text-[10px] uppercase tracking-wider">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus
                                        </button>
                                        @endif
                                    </div>
                                    @endif

                                    @if(!$data['is_locked'] && !$hariIniLibur && !$belumWaktunya)
                                    @if(!isset($buktiSuratUpload[$siswaId]) && !$data['bukti_surat_existing'])
                                    <label class="group relative flex flex-col items-center justify-center gap-1.5 border border-dashed border-gray-300 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/30 hover:bg-gray-50 dark:hover:bg-gray-900 hover:border-primary-500 dark:hover:border-primary-400 rounded-xl p-3 cursor-pointer transition-all duration-200 text-center w-full">
                                        <div class="p-1.5 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 group-hover:border-primary-200 dark:group-hover:border-primary-900 transition-colors shadow-xs">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 group-hover:text-primary-500 dark:group-hover:text-primary-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                                            </svg>
                                        </div>
                                        <div class="text-[10px] font-medium text-gray-600 dark:text-gray-400">
                                            <span class="text-primary-600 dark:text-primary-400 font-semibold hover:underline">Unggah berkas</span>
                                        </div>
                                        <input
                                            type="file"
                                            wire:model="buktiSuratUpload.{{ $siswaId }}"
                                            style="display: none !important;">
                                    </label>
                                    @elseif(isset($buktiSuratUpload[$siswaId]))
                                    <div class="flex items-center justify-between gap-2 mt-1 bg-warning-500/10 border border-warning-500/20 p-2 rounded-xl text-xs">
                                        <span class="text-warning-700 dark:text-warning-400 font-medium truncate">Siap diunggah...</span>
                                        <button
                                            type="button"
                                            wire:click="$set('buktiSuratUpload.{{ $siswaId }}', null)"
                                            class="text-gray-500 hover:text-gray-700 text-[10px] font-bold uppercase">
                                            Batal
                                        </button>
                                    </div>
                                    @endif
                                    <div wire:loading wire:target="buktiSuratUpload.{{ $siswaId }}" class="text-[10px] text-primary-500 font-bold">
                                        Mengunggah...
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Layout (Cards) -->
        <div class="md:hidden space-y-4">
            @foreach($siswaData as $siswaId => $data)
            <div class="p-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-850 rounded-2xl shadow-sm space-y-4" x-data="{ activeStatus: '{{ $data['status'] }}' }">
                <!-- Top Info -->
                <div class="flex justify-between items-start gap-2">
                    <div>
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-xs text-gray-400 font-mono font-bold">#{{ $loop->iteration }}</span>
                            <h4 class="font-bold text-gray-900 dark:text-white text-base leading-tight">{{ $data['nama'] }}</h4>
                        </div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 font-mono mt-0.5">NISN: {{ $data['nisn'] }}</p>
                    </div>
                    @if($data['is_locked'])
                    <span class="inline-flex items-center text-[9px] bg-danger-500/10 text-danger-600 dark:text-danger-400 border border-danger-500/20 px-2 py-0.5 rounded-full font-bold uppercase shrink-0">
                        Terkunci
                    </span>
                    @endif
                </div>

                <!-- Status Selection -->
                <div class="space-y-2">
                    <label class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider block">Status Kehadiran</label>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($statuses as $val => $style)
                        <button
                            type="button"
                            x-on:click="activeStatus = '{{ $val }}'; $wire.set('siswaData.{{ $siswaId }}.status', '{{ $val }}', false)"
                            :disabled="{{ $data['is_locked'] || $hariIniLibur || $belumWaktunya ? 'true' : 'false' }}"
                            class="px-3 py-1.5 text-xs font-bold rounded-lg border transition-all duration-150 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                            :class="activeStatus === '{{ $val }}' 
                                        ? 'text-white border-transparent' 
                                        : 'bg-transparent border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5'"
                            :style="activeStatus === '{{ $val }}' ? 'background-color: {{ $style['color'] }}' : ''">
                            {{ $val }}
                        </button>
                        @endforeach
                    </div>
                    <div
                        class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider"
                        x-text="{H: 'Hadir', S: 'Sakit', I: 'Izin', A: 'Alpa', D: 'Dispensasi', T: 'Terlambat'}[activeStatus]">
                        {{ $labels[$data['status']] }}
                    </div>
                </div>

                <!-- Dynamic Details Form -->
                <div x-show="['D', 'T', 'S', 'I'].includes(activeStatus)" x-cloak class="pt-3 border-t border-gray-100 dark:border-gray-800 space-y-3">
                    <div x-show="activeStatus === 'D'" x-cloak class="space-y-1">
                        <label class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase block">Keterangan Dispensasi (Wajib)</label>
                        <x-filament::input
                            type="text"
                            wire:model.defer="siswaData.{{ $siswaId }}.catatan"
                            placeholder="Siswa bertugas..."
                            :disabled="$data['is_locked'] || $hariIniLibur || $belumWaktunya"
                            class="w-full" />
                    </div>

                    <div x-show="activeStatus === 'T'" x-cloak class="space-y-1">
                        <label class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase block">Menit Terlambat (Wajib)</label>
                        <div class="flex items-center gap-1.5">
                            <x-filament::input
                                type="number"
                                wire:model.defer="siswaData.{{ $siswaId }}.menit_terlambat"
                                placeholder="Contoh: 20"
                                :disabled="$data['is_locked'] || $hariIniLibur || $belumWaktunya"
                                class="w-20" />
                            <span class="text-xs text-gray-400">menit</span>
                        </div>
                    </div>

                    <div x-show="activeStatus === 'S' || activeStatus === 'I'" x-cloak class="space-y-1">
                        <label class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase block">Bukti Surat Test</label>
                        @if($data['bukti_surat_existing'])
                        <div class="flex items-center justify-between gap-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-2 rounded-xl text-xs mb-2">
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($data['bukti_surat_existing']) }}" target="_blank" class="flex items-center gap-1.5 hover:underline font-bold text-primary-600 dark:text-primary-400 truncate max-w-[200px]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                Lihat Surat
                            </a>
                            @if(!$data['is_locked'] && !$hariIniLibur && !$belumWaktunya)
                            <button
                                type="button"
                                wire:click="hapusBuktiSurat({{ $siswaId }})"
                                class="text-danger-600 dark:text-danger-400 hover:text-danger-700 font-bold flex items-center gap-1 text-[10px] uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus
                            </button>
                            @endif
                        </div>
                        @endif

                        @if(!$data['is_locked'] && !$hariIniLibur && !$belumWaktunya)
                        @if(!isset($buktiSuratUpload[$siswaId]) && !$data['bukti_surat_existing'])
                        <label class="group relative flex flex-col items-center justify-center gap-1.5 border border-dashed border-gray-300 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/30 hover:bg-gray-50 dark:hover:bg-gray-900 hover:border-primary-500 dark:hover:border-primary-400 rounded-xl p-3 cursor-pointer transition-all duration-200 text-center w-full">
                            <div class="p-1.5 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 group-hover:border-primary-200 dark:group-hover:border-primary-900 transition-colors shadow-xs">
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 group-hover:text-primary-500 dark:group-hover:text-primary-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                                </svg>
                            </div>
                            <div class="text-[10px] font-medium text-gray-600 dark:text-gray-400">
                                <span class="text-primary-600 dark:text-primary-400 font-semibold hover:underline">Unggah berkas</span>
                            </div>
                            <input
                                type="file"
                                wire:model="buktiSuratUpload.{{ $siswaId }}"
                                style="display: none !important;">
                        </label>
                        @elseif(isset($buktiSuratUpload[$siswaId]))
                        <div class="flex items-center justify-between gap-2 mt-1 bg-warning-500/10 border border-warning-500/20 p-2 rounded-xl text-xs">
                            <span class="text-warning-700 dark:text-warning-400 font-medium truncate">Siap diunggah...</span>
                            <button
                                type="button"
                                wire:click="$set('buktiSuratUpload.{{ $siswaId }}', null)"
                                class="text-gray-500 hover:text-gray-700 text-[10px] font-bold uppercase">
                                Batal
                            </button>
                        </div>
                        @endif
                        <div wire:loading wire:target="buktiSuratUpload.{{ $siswaId }}" class="text-[10px] text-primary-500 font-bold">
                            Mengunggah...
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="pt-4 border-t border-gray-200 dark:border-gray-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-6">
            <p class="text-xs text-gray-400 dark:text-gray-500 max-w-xl">
                * Pastikan data presensi sudah benar sebelum menekan tombol Simpan Presensi.
            </p>
            <div class="flex items-center gap-2.5">
                <x-filament::button
                    color="gray"
                    outlined
                    tag="a"
                    href="/dashboard">
                    Kembali
                </x-filament::button>
                <x-filament::button
                    color="success"
                    wire:click="simpanPresensi"
                    :disabled="$hariIniLibur || $belumWaktunya || $isReadOnly">
                    Simpan Presensi
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>
</x-filament-panels::page>