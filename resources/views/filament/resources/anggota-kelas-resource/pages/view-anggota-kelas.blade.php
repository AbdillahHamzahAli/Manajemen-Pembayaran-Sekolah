<x-filament-panels::page>
    {{-- {{ dd($anggota_kelas, $kelas->tahunAjaran->tahun_ajaran) }}     --}}
    <div class="space-y-4">
        <x-filament::card>
            <dl class="grid grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Kelas</dt>
                    <dd class="text-lg font-semibold">{{ $kelas->tingkat_kelas }} {{ $kelas->nama_kelas }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tahun Ajaran</dt>
                    <dd class="text-lg font-semibold">{{ $kelas->tahunAjaran->tahun_ajaran }}</dd>
                </div>
            </dl>
        </x-filament::card>
    </div>
    <div class="space-y-4">
        <h2 class="text-xl font-bold mb-2">Anggota</h2>
        {{ $this->table }}
    </div>
    
</x-filament-panels::page>
