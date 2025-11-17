@extends('layouts.app')

@section('title', 'Edit Laporan')
@section('page_title', 'Edit Laporan')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Edit Laporan</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('laporan.update', $laporan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Judul</label>
                    <input type="text" name="judul" class="form-control" value="{{ old('judul', $laporan->judul) }}" required>
                </div>

                <div class="form-group">
                    <label>Unit</label>
                    <input type="text" name="unit" class="form-control" value="{{ old('unit', $laporan->unit) }}" required>
                </div>

                <div class="form-group">
                    <label>File Lama</label><br>
                    <a href="{{ asset('storage/' . $laporan->file) }}" target="_blank" class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i> Lihat File
                    </a>
                </div>

                <div class="form-group">
                    <label>Ganti File (Opsional)</label>
                    <input type="file" name="file" class="form-control-file" accept=".pdf,.doc,.docx,.xlsx,.xls">
                    <small class="text-muted">Kosongkan jika tidak ingin mengganti file</small>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="{{ route('laporan.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </form>
        </div>
    </div>
</div>
@endsection
