<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kinerja Puskesmas</title>
    <!-- CSS Internal untuk DomPDF -->
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt; /* Ukuran font lebih kecil agar tabel muat */
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header h3 {
            margin: 0;
            padding: 0;
            font-size: 12pt;
            font-weight: bold;
        }
        .info-box {
            margin-bottom: 20px;
            padding: 10px 0;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }
        .info-box p {
            margin: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px 3px; /* Padding dikurangi */
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        /* Definisikan ulang alignment */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        /* Lebar Kolom */
        .indicator-col {
            width: 28%; /* Alokasi lebih banyak untuk indikator */
        }
        .target-col {
            width: 10%;
        }
        .month-col {
            width: 5%; /* Lebar standar untuk 12 bulan */
            font-size: 7pt; /* Font bulan lebih kecil */
            line-height: 1;
        }
        
        /* Kolom untuk data target dan realisasi bulanan */
        .data-col {
            text-align: center; /* Set rata tengah untuk data angka */
        }
    </style>
</head>
<body>

    <div class="header">
        <h3 style="margin-bottom: 5px;">REKAPITULASI LAPORAN KINERJA BULANAN</h3>
        <h3 style="margin-bottom: 5px;">PUSKESMAS/LABKESDA: {{ strtoupper($laporan->puskesmas_name) }}</h3>
        <h3>TAHUN: {{ $laporan->tahun }}</h3>
    </div>

    <div class="info-box">
        <p><strong>Detail Laporan:</strong></p>
        <p>Jenis Laporan: **{{ $laporan->jenis_laporan == 'capaian_program' ? 'Capaian Program' : 'Capaian Labkesda' }}**</p>
        <p>Status: **{{ $laporan->status ?? 'Draft' }}**</p>
    </div>

    <!-- TABEL CAPAIAN KINERJA -->
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 5%;">No.</th>
                <th rowspan="2" class="indicator-col">Indikator Kinerja</th>
                <th rowspan="2" class="target-col">Target Sasaran (1 Tahun)</th>
                <th colspan="12">Realisasi Bulanan</th>
            </tr>
            <tr>
                <!-- Nama bulan disingkat menjadi 3 huruf -->
                @php
                    $monthAbbreviations = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                @endphp
                @foreach ($monthAbbreviations as $name)
                    <th class="month-col">{{ $name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($laporan->details as $index => $detail)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $detail->indikator_name }}</td>
                    {{-- Target Sasaran menggunakan rata tengah juga agar konsisten dengan data lain --}}
                    <td class="data-col">{{ number_format($detail->target_sasaran, 0, ',', '.') }}</td> 
                    @for ($i = 1; $i <= 12; $i++)
                        <!-- Menampilkan realisasi bulan dengan format angka dan class 'data-col' (rata tengah) -->
                        <td class="data-col">{{ number_format($detail->{'bln_'.$i} ?? 0, 0, ',', '.') }}</td>
                    @endfor
                </tr>
            @empty
                <tr>
                    <td colspan="15" class="text-center">Tidak ada detail capaian kinerja ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <!-- Bagian Tanda Tangan (Opsional, tapi penting untuk dokumen resmi) -->
    <div style="margin-top: 40px; page-break-inside: avoid;">
        <table style="border: none;">
            <tr>
                <td style="border: none; width: 60%;"></td>
                <td style="border: none; text-align: center;">
                    [Nama Daerah/Kota], {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    [Jabatan/Kepala Puskesmas]<br>
                    <br><br><br><br>
                    ( _________________________ )<br>
                    NIP. [Nomor Induk Pegawai]
                </td>
            </tr>
        </table>
    </div>

</body>
</html>