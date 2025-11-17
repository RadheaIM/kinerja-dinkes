@extends('layouts.app')

@section('title', 'Data Sasaran Puskesmas')
@section('page_title', 'Data Sasaran Puskesmas')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Sasaran Puskesmas</h6>

            {{-- Hanya Admin yang bisa melihat tombol Import --}}
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('laporan-puskesmas.importForm') }}" class="btn btn-primary btn-sm mt-2">
                <i class="fas fa-upload"></i> Import Data Sasaran
            </a>
            @endif
        </div>
        <div class="card-body">

            {{-- Notifikasi Sukses/Error --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Search Bar (Bisa dilihat semua role) --}}
            <form method="GET" action="{{ route('laporan-puskesmas.index') }}" class="mb-4">
                <div class="input-group">
                
                    <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan nama puskesmas..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">Cari</button>
                </div>
            </form>

            <div class="table-responsive">
                {{-- TAMBAHAN: table-sm agar lebih rapat/rapih --}}
                <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            {{-- DISESUAIKAN DENGAN FILE CSV --}}
                            <th>NO</th>
                            <th>PUSKESMAS</th>
                            <th>BUMIL</th>
                            <th>BULIN</th>
                            <th>BBL</th>
                            <th>BALITA = D/S</th>
                            <th>PENDIDIKAN DASAR</th>
                            <th>USPRO</th>
                            <th>LANSIA</th>
                            <th>HIPERTENSI</th>
                            <th>DM</th>
                            <th>ODGJ BERAT</th>
                            <th>TB</th>
                            <th>HIV</th>
                            <th>IDL</th>
                            
                            @if(Auth::user()->role === 'admin')
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>

                    {{-- ==================================================== --}}
                    {{-- === PERUBAHAN BARU: Menambahkan Table Footer (<tfoot>) === --}}
                    {{-- ==================================================== --}}
                    {{-- Footer ini akan Tampil di SETIAP Halaman Paginasi --}}
                    <tfoot style="background-color: #f8f9fa; font-weight: bold;">
                        <tr class="text-center">
                            <td colspan="2">TOTAL KESELURUHAN</td>
                            <td>{{ number_format($totals->bumil ?? 0) }}</td>
                            <td>{{ number_format($totals->bulin ?? 0) }}</td>
                            <td>{{ number_format($totals->bbl ?? 0) }}</td>
                            <td>{{ number_format($totals->balita_ds ?? 0) }}</td>
                            <td>{{ number_format($totals->pendidikan_dasar ?? 0) }}</td>
                            <td>{{ number_format($totals->uspro ?? 0) }}</td>
                            <td>{{ number_format($totals->lansia ?? 0) }}</td>
                            <td>{{ number_format($totals->hipertensi ?? 0) }}</td>
                            <td>{{ number_format($totals->dm ?? 0) }}</td>
                            <td>{{ number_format($totals->odgj_berat ?? 0) }}</td>
                            <td>{{ number_format($totals->tb ?? 0) }}</td>
                            <td>{{ number_format($totals->hiv ?? 0) }}</td>
                            <td>{{ number_format($totals->idl ?? 0) }}</td>
                            @if(Auth::user()->role === 'admin')
                                <td></td> {{-- Kolom kosong untuk Aksi --}}
                            @endif
                        </tr>
                    </tfoot>
                    {{-- ================================================ --}}

                    <tbody>
                        @forelse ($laporans as $laporan)
                            <tr>
                                {{-- Menampilkan Nomor urut --}}
                                <td class="text-center">{{ $laporans->firstItem() + $loop->index }}</td>
                                
                                <td>{{ $laporan->puskesmas }}</td>
                                
                                <td class="text-center">{{ number_format($laporan->bumil ?? 0) }}</td>
                                <td class="text-center">{{ number_format($laporan->bulin ?? 0) }}</td>
                                <td class="text-center">{{ number_format($laporan->bbl ?? 0) }}</td>
                                <td class="text-center">{{ number_format($laporan->balita_ds ?? 0) }}</td>
                                <td class="text-center">{{ number_format($laporan->pendidikan_dasar ?? 0) }}</td>
                                <td class="text-center">{{ number_format($laporan->uspro ?? 0) }}</td>
                                <td class="text-center">{{ number_format($laporan->lansia ?? 0) }}</td>
                                <td class="text-center">{{ number_format($laporan->hipertensi ?? 0) }}</td>
                                <td class="text-center">{{ number_format($laporan->dm ?? 0) }}</td>
                                <td class="text-center">{{ number_format($laporan->odgj_berat ?? 0) }}</td>
                                <td class="text-center">{{ number_format($laporan->tb ?? 0) }}</td>
                                <td class="text-center">{{ number_format($laporan->hiv ?? 0) }}</td>
                                <td class="text-center">{{ number_format($laporan->idl ?? 0) }}</td>

                                {{-- Tombol Aksi (Hanya Admin) --}}
                                @if(Auth::user()->role === 'admin')
                                <td class="text-center">
                                    <form action="{{ route('laporan-puskesmas.destroy', $laporan->id) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus data sasaran ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ Auth::user()->role === 'admin' ? '16' : '15' }}" class="text-center">
                                    Tidak ada data sasaran yang ditemukan.
                                    @if(Auth::user()->role === 'admin')
                                        Silakan <a href="{{ route('laporan-puskesmas.importForm') }}">import data</a> terlebih dahulu.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Link Pagination --}}
            <div class="d-flex justify-content-center">
                {{ $laporans->appends(request()->query())->links() }}
            </div>

        </div>
    </div>
</div>
@endsection