<x-filament-panels::page>
    <!-- Banners & Blockers -->
    @if($hariIniLibur)
        <div class="p-4 bg-danger-500/10 border border-danger-500/20 rounded-xl text-danger-600 dark:text-danger-400">
            <h3 class="text-sm font-bold">Hari ini adalah Hari Libur</h3>
            <p class="text-xs mt-1">Keterangan: {{ $namaLibur }}. Pengisian absensi dinonaktifkan.</p>
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

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-800 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                        <th class="py-3 px-4 w-10">No</th>
                        <th class="py-3 px-4">Siswa</th>
                        <th class="py-3 px-4 text-center">Status Kehadiran</th>
                        <th class="py-3 px-4">Detail Tambahan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-150 dark:divide-gray-800">
                    @foreach($siswaData as $siswaId => $data)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
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
                                    @php
                                        $statuses = [
                                            'H' => ['color' => 'success'],
                                            'S' => ['color' => 'warning'],
                                            'I' => ['color' => 'info'],
                                            'A' => ['color' => 'danger'],
                                            'D' => ['color' => 'gray'],
                                            'T' => ['color' => 'warning'],
                                        ];
                                    @endphp
                                    @foreach($statuses as $val => $style)
                                        @php
                                            $active = $data['status'] === $val;
                                        @endphp
                                        <x-filament::button 
                                            size="sm"
                                            color="{{ $active ? $style['color'] : 'gray' }}"
                                            outlined="{{ !$active }}"
                                            wire:click="$set('siswaData.{{ $siswaId }}.status', '{{ $val }}')"
                                            :disabled="$data['is_locked'] || $hariIniLibur || $belumWaktunya"
                                            class="font-bold rounded-lg"
                                        >
                                            {{ $val }}
                                        </x-filament::button>
                                    @endforeach
                                </div>
                                <div class="text-center mt-1 text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider">
                                    @php
                                        $labels = ['H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin', 'A' => 'Alpa', 'D' => 'Dispensasi', 'T' => 'Terlambat'];
                                    @endphp
                                    {{ $labels[$data['status']] }}
                                </div>
                            </td>
                            <td class="py-4 px-4 text-xs">
                                <div class="space-y-2">
                                    @if($data['status'] === 'D')
                                        <div class="space-y-1">
                                            <label class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase block">Keterangan Dispensasi (Wajib)</label>
                                            <x-filament::input 
                                                type="text" 
                                                wire:model.defer="siswaData.{{ $siswaId }}.catatan"
                                                placeholder="Siswa bertugas..."
                                                :disabled="$data['is_locked'] || $hariIniLibur || $belumWaktunya"
                                                class="w-full"
                                            />
                                        </div>
                                    @endif

                                    @if($data['status'] === 'T')
                                        <div class="space-y-1">
                                            <label class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase block">Menit Terlambat (Wajib)</label>
                                            <div class="flex items-center gap-1.5">
                                                <x-filament::input 
                                                    type="number" 
                                                    wire:model.defer="siswaData.{{ $siswaId }}.menit_terlambat"
                                                    placeholder="Contoh: 20"
                                                    :disabled="$data['is_locked'] || $hariIniLibur || $belumWaktunya"
                                                    class="w-20"
                                                />
                                                <span class="text-gray-400">menit</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if(in_array($data['status'], ['S', 'I']))
                                        <div class="space-y-1">
                                            <label class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase block">Bukti Surat</label>
                                            @if($data['bukti_surat_existing'])
                                                <div class="flex items-center gap-1 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-1.5 rounded-lg text-xs mb-1">
                                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($data['bukti_surat_existing']) }}" target="_blank" class="hover:underline font-bold text-primary-600 dark:text-primary-400 truncate max-w-[150px]">
                                                        Lihat Surat Terunggah
                                                    </a>
                                                </div>
                                            @endif

                                            @if(!$data['is_locked'] && !$hariIniLibur && !$belumWaktunya)
                                                <input 
                                                    type="file" 
                                                    wire:model="buktiSuratUpload.{{ $siswaId }}"
                                                    class="block w-full text-[10px] text-gray-400"
                                                >
                                                <div wire:loading wire:target="buktiSuratUpload.{{ $siswaId }}" class="text-[10px] text-primary-500 font-bold">
                                                    Mengunggah...
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
                    href="/guru"
                >
                    Kembali
                </x-filament::button>
                <x-filament::button 
                    color="success" 
                    wire:click="simpanPresensi"
                    :disabled="$hariIniLibur || $belumWaktunya"
                >
                    Simpan Presensi
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>
</x-filament-panels::page>
