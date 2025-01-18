<!DOCTYPE html>
<html>
<head>
    <title>Invoice SPP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .invoice-details {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .total-section {
            margin-top: 20px;
            text-align: right;
        }
        .status {
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        <h2>INVOICE PEMBAYARAN SPP</h2>
        <p>{{ date('d/m/Y') }}</p>
    </div>

    <div class="invoice-details">
        <table>
            <tr>
                <td><strong>NIS</strong></td>
                <td>{{ $transaksiSPP->anggota_kelas->siswa->nis }}</td>
            </tr>
            <tr>
                <td><strong>Nama</strong></td>
                <td>{{ $transaksiSPP->anggota_kelas->siswa->nama_siswa }}</td>
            </tr>
            <tr>
                <td><strong>Kelas</strong></td>
                <td>{{ $transaksiSPP->anggota_kelas->kelas->tingkat_kelas }} {{ $transaksiSPP->anggota_kelas->kelas->nama_kelas }}</td>
            </tr>
            <tr>
                <td><strong>Bulan:</strong></td>
                <td>{{ $transaksiSPP->bulan }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>Deskripsi</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Pembayaran SPP</td>
                <td>Rp. {{ number_format($transaksiSPP->bayar, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Tunggakan</td>
                <td>Rp. {{ number_format($transaksiSPP->tunggakan, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-section">
        <p><strong>Status Pembayaran:</strong> 
            <span class="status" style="color: {{ $transaksiSPP->status == 'Lunas' ? 'green' : 'red' }}">
                {{ strtoupper($transaksiSPP->status) }}
            </span>
        </p>
    </div>

    <div style="margin-top: 50px; text-align: right;">
        <p>Petugas</p>
        <br><br><br>
        <p>(_________________)</p>
    </div>
</body>
</html>
