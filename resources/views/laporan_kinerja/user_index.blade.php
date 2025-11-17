@extends('layouts.app')

{{-- Menentukan nama unit yang benar (Labkesda atau Puskesmas) untuk Judul --}}
@php
    $unitName = (Auth::user()->role === 'labkesda') ? 'Labkesda' : (Auth::user()->nama_puskesmas ?? 'Data Saya');
@endphp

@section('title', 'Data Laporan Kinerja Saya')
@section('page_title', 'Data Laporan Kinerja ' . $unitName)

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Laporan Kinerja ({{ $unitName }})</h6>
        </div>
        <div class="card-body">

            {{-- Filter (Hanya filter tahun untuk user) --}}
            <form method="GET" action="{{ route('laporan-kinerja.user.index') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <label for="tahun" class="form-label">Filter Tahun:</label>
                        <select name="tahun" id="tahun" class="form-select">
                            @foreach ($availableYears as $year)
                                <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                    </div>
                </div>
            </form>
            <hr>

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

            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>Nama Unit</th>
                            <th>Tahun</th>
                            <th>Jenis Laporan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laporans as $laporan)
                            <tr>
                                <td>{{ $laporan->puskesmas_name }}</td>
                                <td class="text-center">{{ $laporan->tahun }}</td>
                                <td>
                                    @if ($laporan->jenis_laporan == 'capaian_program')
                                        Capaian Program (Puskesmas)
                                    @elseif ($laporan->jenis_laporan == 'labkesda_capaian')
                                        Capaian (Labkesda)
                                    @else
                                        {{ $laporan->jenis_laporan }}
                                    @endif
                                </td>
                                
                                {{-- === PERUBAHAN DI SINI: TOMBOL AKSI === --}}
                                <td class="text-center">
                                    {{-- TOMBOL EDIT --}}
                                    <a href="{{ route('laporan-kinerja.edit', $laporan->id) }}" class="btn btn-warning btn-sm" title="Edit Laporan">
                                        <i class="fas fa-pencil-alt"></i> Edit
                                    </a>
                                
                                    {{-- TOMBOL HAPUS --}}
                                    <form action="{{ route('laporan-kinerja.destroy', $laporan->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini? Ini tidak bisa dikembalikan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus Laporan">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                                {{-- === AKHIR PERUBAHAN === --}}

                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">
                                    Anda belum membuat Laporan Kinerja untuk tahun {{ $tahun }}.
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