@extends('layouts.app')

@section('title', 'Data Sasaran Puskesmas')
@section('page_title', 'Data Sasaran Puskesmas')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Sasaran Puskesmas</h6>

            {{-- === TOMBOL IMPORT DAN HAPUS MASSAL (Hanya Admin) === --}}
            @if(Auth::user()->role === 'admin')
            <div class="d-flex flex-wrap gap-2 mt-2">
                <a href="{{ route('laporan-puskesmas.importForm') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-upload"></i> Import Data Sasaran
                </a>
                
                {{-- TOMBOL HAPUS MASSAL BERDASARKAN FILTER TAHUN --}}
                {{-- FIX: Tombol SELALU ditampilkan jika admin, karena logic hapus massal sudah menangani "Semua Tahun" --}}
                @php
                    $isAllYears = empty($tahunFilter);
                    $buttonText = $isAllYears ? 'Hapus SEMUA DATA SASARAN' : "Hapus Semua Tahun {$tahunFilter}";
                    $modalText = $isAllYears ? 'SEMUA TAHUN (HATI-HATI!)' : "TAHUN {$tahunFilter}";
                @endphp
                
                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteByYearModal">
                    <i class="fas fa-trash-alt"></i> {{ $buttonText }}
                </button>
            </div>
            @endif
        </div>
        <div class="card-body">

            {{-- Notifikasi Sukses/Error --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {!! session('success') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {!! session('error') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    {!! session('warning') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- ========================================================== --}}
            {{-- === Search Bar dan Filter Tahun Digabung (Perlu Disimpan) === --}}
            {{-- ========================================================== --}}
            <form method="GET" action="{{ route('laporan-puskesmas.index') }}" class="mb-4">
                <div class="row">
                    {{-- Kolom Filter Tahun --}}
                    {{-- KOLOM FILTER TAHUN INI KITA BIARKAN ADA, karena berguna untuk filtering data. --}}
                    <div class="col-md-3 mb-2">
                        <select name="tahun" class="form-select">
                            <option value="" @unless(isset($tahunFilter) && $tahunFilter) selected @endunless>Semua Tahun</option>
                            
                            @php
                                $currentYear = date('Y');
                                $startYear = $currentYear - 3; // Misalnya, 3 tahun ke belakang
                                $endYear = $currentYear + 10; // Sampai 10 tahun ke depan
                            @endphp

                            @for ($y = $endYear; $y >= $startYear; $y--)
                                <option value="{{ $y }}" {{ (isset($tahunFilter) && $tahunFilter == $y) ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- Kolom Search Puskesmas --}}
                    <div class="col-md-6 mb-2">
                        <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan nama puskesmas..." value="{{ request('search') }}">
                    </div>

                    {{-- Tombol Submit --}}
                    <div class="col-md-3 mb-2">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="fas fa-search"></i> Cari & Filter
                        </button>
                    </div>
                </div>
            </form>
            {{-- ========================================================== --}}

            <div class="table-responsive">
                <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>NO</th>
                            <th>PUSKESMAS</th>
                            {{-- BARIS DIHAPUS: <th>TAHUN</th> --}}
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
                    {{-- === TFOOT (Total Keseluruhan) === --}}
                    {{-- ==================================================== --}}
                    <tfoot style="background-color: #f8f9fa; font-weight: bold;">
                        <tr class="text-center">
                            {{-- Colspan disesuaikan menjadi 2 untuk TOTAL KESELURUHAN (menggantikan NO dan PUSKESMAS) --}}
                            <td colspan="2">TOTAL KESELURUHAN</td> 
                            {{-- BARIS DIHAPUS: <td></td> untuk kolom Tahun --}}
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
                                <td class="text-center">{{ $laporans->firstItem() + $loop->index }}</td>
                                
                                <td>{{ $laporan->puskesmas }}</td>
                                {{-- BARIS DIHAPUS: <td class="text-center">{{ $laporan->tahun ?? 'N/A' }}</td> --}}
                                
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
                                    {{-- Menggunakan modal kustom lebih disarankan daripada confirm() --}}
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
                                {{-- Colspan disesuaikan. Total kolom tanpa Tahun: 15 (1 No + 1 Puskesmas + 13 Indikator) + 1 (Aksi) = 16. --}}
                                <td colspan="{{ Auth::user()->role === 'admin' ? '16' : '15' }}" class="text-center">
                                    {{-- ========================================================== --}}
                                    {{-- === LOGIKA BARU: Peringatan Data Kosong Berdasarkan Tahun === --}}
                                    {{-- ========================================================== --}}
                                    @if (isset($tahunFilter) && $tahunFilter && !request('search'))
                                        {{-- Kondisi: Filter Tahun aktif, dan tidak ada search text --}}
                                        <div class="alert alert-danger mb-0">
                                            <strong>PERINGATAN!</strong> Data sasaran Puskesmas **tahun {{ $tahunFilter }}** belum tersedia di sistem.
                                            @if(Auth::user()->role === 'admin')
                                                Silakan <a href="{{ route('laporan-puskesmas.importForm') }}" class="alert-link">import data untuk tahun {{ $tahunFilter }}</a> terlebih dahulu.
                                            @endif
                                        </div>
                                    @else
                                        {{-- Kondisi: Tidak ada data, baik karena tidak ada data sama sekali atau karena hasil search kosong --}}
                                        <div class="alert alert-info mb-0">
                                            Tidak ada data sasaran yang ditemukan.
                                            @if(Auth::user()->role === 'admin')
                                                @if (!isset($tahunFilter) || !$tahunFilter)
                                                Silakan <a href="{{ route('laporan-puskesmas.importForm') }}" class="alert-link">import data</a> terlebih dahulu.
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                    {{-- ========================================================== --}}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Link Pagination --}}
            <div class="d-flex justify-content-center">
                {{-- Pastikan filter tahun dan search dibawa di pagination --}}
                {{ $laporans->appends(request()->query())->links() }}
            </div>

        </div>
    </div>
</div>

{{-- ========================================================== --}}
{{-- === MODAL KONFIRMASI HAPUS MASSAL (Tambahkan di bagian bawah) === --}}
{{-- ========================================================== --}}
@if(Auth::user()->role === 'admin')
<div class="modal fade" id="deleteByYearModal" tabindex="-1" aria-labelledby="deleteByYearModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteByYearModalLabel">Konfirmasi Hapus Data Sasaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="fw-bold">Anda akan menghapus SEMUA data Sasaran Puskesmas untuk:</p>
                <h4 class="text-danger">{{ $modalText }}</h4>
                <p>Tindakan ini tidak dapat dibatalkan. Apakah Anda yakin ingin melanjutkan?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('laporan-puskesmas.destroyByYear') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    {{-- KIRIM NILAI $tahunFilter (akan kosong jika "Semua Tahun" dipilih) --}}
                    <input type="hidden" name="tahun" value="{{ $tahunFilter }}"> 
                    <button type="submit" class="btn btn-danger">{{ $buttonText }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endsection