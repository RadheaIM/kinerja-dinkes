<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Sasaran Puskesmas</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h2>Laporan Sasaran Tiap Puskesmas</h2>
    <table>
        <thead>
            <tr>
                <th>Puskesmas</th>
                <th>Bumil</th>
                <th>Bulin</th>
                <th>BBL</th>
                <th>Balita</th>
                <th>Pendidikan Dasar</th>
                <th>Uspro</th>
                <th>Lansia</th>
                <th>Hipertensi</th>
                <th>DM</th>
                <th>ODGJ Berat</th>
                <th>TB</th>
                <th>HIV</th>
                <th>IDL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
            <tr>
                <td>{{ $item->puskesmas }}</td>
                <td>{{ $item->bumil }}</td>
                <td>{{ $item->bulin }}</td>
                <td>{{ $item->bbl }}</td>
                <td>{{ $item->balita }}</td>
                <td>{{ $item->pendidikan_dasar }}</td>
                <td>{{ $item->uspro }}</td>
                <td>{{ $item->lansia }}</td>
                <td>{{ $item->hipertensi }}</td>
                <td>{{ $item->dm }}</td>
                <td>{{ $item->odgj_berat }}</td>
                <td>{{ $item->tb }}</td>
                <td>{{ $item->hiv }}</td>
                <td>{{ $item->idl }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
