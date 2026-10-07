<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .periode { color: #555; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        .ringkasan-table td:nth-child(n+2) { text-align: right; }
    </style>
</head>
<body>
    <h1>Laporan Stok Opname - Margin Escape</h1>
    <div class="periode">Periode: {{ $periodeMulai }} s/d {{ $periodeAkhir }}</div>

    <h3>Ringkasan per Jenis Kopi</h3>
    <table class="ringkasan-table">
        <tr>
            <th>Jenis Kopi</th>
            <th>Total Keluar (gr)</th>
            <th>Total Masuk (gr)</th>
            <th>Rata-rata Harian (gr)</th>
            <th>Stok Akhir Terbaru (gr)</th>
        </tr>
        @foreach ($ringkasan as $r)
        <tr>
            <td>{{ $r['jenis_kopi'] }}</td>
            <td>{{ number_format($r['total_keluar']) }}</td>
            <td>{{ number_format($r['total_masuk']) }}</td>
            <td>{{ number_format($r['rata_rata'], 1) }}</td>
            <td>{{ number_format($r['stok_akhir_terbaru']) }}</td>
        </tr>
        @endforeach
    </table>

    <h3>Detail Harian</h3>
    <table>
        <tr>
            <th>Tanggal</th>
            <th>Jenis Kopi</th>
            <th>Stok Awal</th>
            <th>Keluar</th>
            <th>Masuk</th>
            <th>Stok Akhir</th>
        </tr>
        @foreach ($detail as $d)
        <tr>
            <td>{{ $d['tanggal'] }}</td>
            <td>{{ $d['jenis_kopi'] }}</td>
            <td>{{ number_format($d['stok_awal']) }}</td>
            <td>{{ number_format($d['keluar']) }}</td>
            <td>{{ number_format($d['masuk']) }}</td>
            <td>{{ number_format($d['stok_akhir']) }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>