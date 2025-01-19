<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Siswa</title>
    <style>
        .text-status-lunas {
            color: #16a34a;
        }
        .text-status-belum-lunas {
            color: #dc2626;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
            font-size: 12px;
        }
        th, td {
            padding: 0.5rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background-color: #f9fafb;
            font-weight: 600;
        }
        .section {
            margin-bottom: 1rem;
            padding: 1rem;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        .font-medium {
            font-weight: 500;
        }
        .section-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .tables-container {
            display: flex;
            gap: 1rem;
        }
        .table-section {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="section">
        <div class="grid">
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
            @endif
        </div>
    </div>

    <div class="tables-container">
        @if($SPP)
        <div class="table-section">
            <div class="section">
                <div class="section-title">Pembayaran SPP</div>
                <table>
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th>Nominal</th>
                            <th>Bayar</th>
                            <th>Status</th>
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
                                $status = $pembayaran >= $nominal ? 'Lunas' : 'Belum Lunas';
                                $statusColor = $status === 'Lunas' ? 'text-status-lunas' : 'text-status-belum-lunas';
                            @endphp
                            <tr>
                                <td>{{ $namaBulan }}</td>
                                <td>Rp {{ number_format($nominal, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($pembayaran, 0, ',', '.') }}</td>
                                <td class="{{ $statusColor }}" style="font-weight: 500;">{{ $status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($Iuran)
        <div class="table-section">
            <div class="section">
                <div class="section-title">Pembayaran Iuran</div>
                <table>
                    <thead>
                        <tr>
                            <th>Jenis</th>
                            <th>Nominal</th>
                            <th>Bayar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($Iuran as $item)
                            @php
                                $nominal = $item->nominal;
                                $namaIuran = $item->nama_iuran;
                                $pembayaran = collect($TransaksiIuran)->where('iuran_id', $item->id)->sum('total_bayar');
                                $status = $pembayaran >= $nominal ? 'Lunas' : 'Belum Lunas';
                                $statusColor = $status === 'Lunas' ? 'text-status-lunas' : 'text-status-belum-lunas';
                            @endphp
                            <tr>
                                <td>{{ $namaIuran }}</td>
                                <td>Rp {{ number_format($nominal, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($pembayaran, 0, ',', '.') }}</td>
                                <td class="{{ $statusColor }}" style="font-weight: 500;">{{ $status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</body>
</html>