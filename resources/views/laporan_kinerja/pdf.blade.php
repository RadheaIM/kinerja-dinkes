<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kinerja Bulanan</title>
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            color: #333;
        }
        h2 {
            text-align: center;
            margin-bottom: 10px;
        }
        p {
            text-align: center;
            margin-top: 0;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #444;
        }
        th, td {
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>

    <h2>Laporan Kinerja Bulanan</h2>

    {{-- Tampilkan periode filter jika ada --}}
    <p>
        <strong>Periode:</strong>
        @if(request('bulan') && request('tahun'))
            {{ \Carbon\Carbon::createFromDate(request('tahun'), request('bulan'), 1)->translatedFormat('F Y') }}
        @else
            Semua Periode
        @endif
    </p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Kegiatan</th>
                <th>Output</th>
                <th>Deskripsi</th>
                <th>Unit</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($laporans as $index => $laporan)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($laporan->created_at)->translatedFormat('d F Y') }}</td>
                    <td>{{ $laporan->judul ?? '-' }}</td>
                    <td>{{ $laporan->output ?? '-' }}</td>
                    <td>{{ $laporan->deskripsi ?? '-' }}</td>
                    <td>{{ $laporan->unit ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data laporan untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <br><br>
    <p style="text-align:right;">Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }}</p>

</body>
</html>
