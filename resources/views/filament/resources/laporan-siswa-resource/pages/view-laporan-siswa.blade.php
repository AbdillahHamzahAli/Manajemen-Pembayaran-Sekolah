<x-filament-panels::page>
    <form wire:submit.prevent="filter" class="space-y-6">
        <x-filament::section class="max-w-xl mx-auto">
            <div class="flex gap-6">
                <x-filament::input.wrapper class="flex-1">
                    <x-filament::input.select
                        wire:model="tahunAjaran"
                        wire:change="filter"
                        label="Tahun Pelajaran"                          
                        placeholder="Pilih Tahun Pelajaran"
                    >
                        @foreach ($siswa->anggotaKelas as $item)
                            <option value="{{ $item->kelas->tahunAjaran->id }}">{{ $item->kelas->tahunAjaran->tahun_ajaran }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
                
                
                <div class="flex gap-2">
                    <x-filament::button type="submit">
                        Filter
                    </x-filament::button>
                    @if ($tahunAjaran)
                        <x-filament::button type="button" color="success">
                            <a href="{{ route('laporan-siswa.pdf', ['nis' => $siswa->nis, 'id' => $tahunAjaran]) }}" class="">
                                Download
                            </a>
                        </x-filament::button>
                    @endif     
                </div>
            </div>
        </x-filament::section>
    </form>
    <x-filament::section>
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="font-medium">Nama Siswa:</span>
                    <div>{{ $siswa->nama_siswa }}</div>
                </div>
                <div>
                    <span class="font-medium">NISN:</span>
                    <div>{{ $siswa->nis }}</div>
                </div>
                @if ($tahunAjaran)
                    @php
                        $filteredData = $siswa->anggotaKelas->where('kelas.tahun_ajaran_id', $tahunAjaran)->first();
                    @endphp
                    <div>
                        <span class="font-medium">Kelas:</span>
                        <div>{{ $filteredData->kelas->tingkat_kelas }} {{ $filteredData->kelas->nama_kelas }}</div>
                    </div>
                    <div>
                        <span class="font-medium">Tahun Ajaran:</span>
                        <div>{{ $filteredData->kelas->tahunAjaran->tahun_ajaran }}</div>
                    </div>
                @else
                    <div class="text-center text-gray-500">
                        Siswa Belum Terdaftar di Kelas Manapun
                    </div>
                @endif
            </div>
        </div>
    </x-filament::section>
    @if($SPP)
        <x-filament::section>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="px-6 py-3">Bulan</th>
                            <th scope="col" class="px-6 py-3">Nominal</th>
                            <th scope="col" class="px-6 py-3">Bayar</th>
                            <th scope="col" class="px-6 py-3">Tunggakan</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $bulan = ['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'];
                        @endphp
    
                        @foreach($bulan as $namaBulan)
                            @php
                                $nominal = $SPP->nominal;
                                $pembayaran = collect($TransaksiSPP)->where('bulan', $namaBulan)->sum('total_bayar');
                                $tunggakan = $nominal - $pembayaran;
                                $status = $pembayaran >= $nominal ? 'Lunas' : 'Belum Lunas';
                                $statusColor = $status === 'Lunas' ? 'text-status-lunas' : 'text-status-belum-lunas';
                            @endphp
                            <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                                <td class="px-6 py-4">{{ $namaBulan }}</td>
                                <td class="px-6 py-4">Rp {{ number_format($nominal, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">Rp {{ number_format($pembayaran, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">Rp {{ number_format($tunggakan, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 {{ $statusColor }}" style="font-weight: 500;">
                                        {{ $status }}
                                </td>
                            </tr>
                        @endforeach                   
                    </tbody>
                </table>
            </div>
            <style>
                .text-status-lunas {
                    color: #16a34a;
                }
                @media (prefers-color-scheme: dark) {
                    .text-status-lunas {
                        color: #4ade80;
                    }
                }
                .text-status-belum-lunas {
                    color: #dc2626;
                }
                @media (prefers-color-scheme: dark) {
                    .text-status-belum-lunas {
                        color: #f87171;
                    }
                }
            </style>
        </x-filament::section>
    @else
        <x-filament::section>
            <div class="text-center text-gray-500 dark:text-gray-400">
                Data SPP belum tersedia
            </div>
        </x-filament::section>
    @endif
    @if($Iuran->isNotEmpty())
        <x-filament::section>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="px-6 py-3">Jenis</th>
                            <th scope="col" class="px-6 py-3">Nominal</th>
                            <th scope="col" class="px-6 py-3">Bayar</th>
                            <th scope="col" class="px-6 py-3">Tunggakan</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($Iuran as $item)
                            @php
                                $nominal = $item->nominal;
                                $namaIuran = $item->nama_iuran;
                                $pembayaran = collect($TransaksiIuran)->where('iuran_id', $item->id)->sum('total_bayar');
                                $tunggakan = $nominal - $pembayaran;
                                $status = $pembayaran >= $nominal ? 'Lunas' : 'Belum Lunas';
                                $statusColor = $status === 'Lunas' ? 'text-status-lunas' : 'text-status-belum-lunas';
                            @endphp
                            <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                                <td class="px-6 py-4">{{ $namaIuran }}</td>
                                <td class="px-6 py-4">Rp {{ number_format($nominal, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">Rp {{ number_format($pembayaran, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">Rp {{ number_format($tunggakan, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 {{ $statusColor }}" style="font-weight: 500;">
                                        {{ $status }}
                                </td>
                            </tr>
                        @endforeach                   
                    </tbody>
                </table>
            </div>
            <style>
                .text-status-lunas {
                    color: #16a34a;
                }
                @media (prefers-color-scheme: dark) {
                    .text-status-lunas {
                        color: #4ade80;
                    }
                }
                .text-status-belum-lunas {
                    color: #dc2626;
                }
                @media (prefers-color-scheme: dark) {
                    .text-status-belum-lunas {
                        color: #f87171;
                    }
                }
            </style>
        </x-filament::section>
    @else
        <x-filament::section>
            <div class="text-center text-gray-500 dark:text-gray-400">
                Data Iuran belum tersedia
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>