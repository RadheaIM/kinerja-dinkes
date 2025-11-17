@extends('layouts.app')

@section('title', 'Laporan Kinerja Unit')
@section('page_title', 'Laporan Kinerja Unit')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Laporan Kinerja Unit</h1>

    {{-- Pesan sukses / error --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Tombol aksi --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('laporan.create') }}" class="btn btn-primary">
            <i class="fas fa-upload"></i> Upload Laporan Baru
        </a>

        <div class="d-flex align-items-center">
            {{-- Form pencarian --}}
            <form action="{{ route('laporan.index') }}" method="GET" class="form-inline mr-2">
                <input type="text" name="search" class="form-control form-control-sm mr-2"
                       placeholder="Cari judul/unit..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-sm btn-secondary">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            {{-- Tombol export PDF --}}
            <a href="{{ route('laporan.export') }}" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="bg-primary text-white text-center">
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Unit</th>
                            <th>Pegawai</th>
                            <th>Tanggal Upload</th>
                            <th>File</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laporans as $index => $laporan)
                            <tr>
                                <td class="text-center">{{ $laporans->firstItem() + $index }}</td>
                                <td>{{ $laporan->judul }}</td>
                                <td>{{ $laporan->unit }}</td>
                                <td>{{ $laporan->pegawai->nama ?? '-' }}</td>
                                <td class="text-center">{{ $laporan->created_at->format('d M Y') }}</td>
                                <td class="text-center">
                                    <a href="{{ asset('storage/' . $laporan->file) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                    <a href="{{ route('laporan.download', $laporan->id) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('laporan.destroy', $laporan->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus laporan ini?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Belum ada laporan yang diupload.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $laporans->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
    