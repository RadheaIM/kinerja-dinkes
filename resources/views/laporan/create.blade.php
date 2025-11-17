@extends('layouts.app')

@section('title', 'Upload Laporan Kinerja')
@section('page_title', 'Upload Laporan Kinerja')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Upload Laporan Kinerja</h1>

    {{-- ✅ Notifikasi Sukses --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- ⚠️ Notifikasi Error --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i> <strong>Terjadi kesalahan!</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- 🗂️ Form Upload --}}
    <div class="card shadow-lg border-0 rounded-lg">
        <div class="card-body">
            <form action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Judul --}}
                <div class="form-group">
                    <label for="judul" class="font-weight-bold">Judul Laporan</label>
                    <input type="text"
                        name="judul"
                        id="judul"
                        class="form-control @error('judul') is-invalid @enderror"
                        value="{{ old('judul') }}"
                        placeholder="Masukkan judul laporan (contoh: Laporan Kinerja Bulanan)"
                        required>
                    @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Unit --}}
                <div class="form-group">
                    <label for="unit" class="font-weight-bold">Unit</label>
                    <input type="text"
                        name="unit"
                        id="unit"
                        class="form-control @error('unit') is-invalid @enderror"
                        value="{{ old('unit') }}"
                        placeholder="Masukkan nama unit atau bidang"
                        required>
                    @error('unit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- File --}}
                <div class="form-group">
                    <label for="file" class="font-weight-bold">File Laporan</label>
                    <input type="file"
                        name="file"
                        id="file"
                        class="form-control-file @error('file') is-invalid @enderror"
                        accept=".pdf,.doc,.docx,.xlsx,.xls"
                        required>
                    <small class="text-muted d-block mt-1">
                        Format yang diizinkan: PDF, DOC, DOCX, XLSX, XLS — Maks. 5MB
                    </small>
                    @error('file')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tombol Aksi --}}
                <div class="mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload mr-1"></i> Upload
                    </button>
                    <a href="{{ route('laporan.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
