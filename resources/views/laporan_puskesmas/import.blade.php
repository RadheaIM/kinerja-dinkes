@extends('layouts.app')

@section('title', 'Import Sasaran Puskesmas')
@section('page_title', 'Import Data Sasaran Puskesmas')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Upload File Excel Sasaran Puskesmas</h6>
                <a href="{{ route('laporan-puskesmas.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left fa-sm"></i> Kembali
                </a>
            </div>
            <div class="card-body">

                {{-- Notifikasi Error Validasi (jika ada) --}}
                @if ($errors->any())
                    <div class="alert alert-danger pb-0">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                {{-- Notifikasi Error Runtime (dari Controller, misal GAGAL IMPORT) --}}
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="alert alert-warning">
                    <strong>Perhatian!</strong> Meng-import file baru akan **MENGHAPUS SEMUA** data sasaran puskesmas yang lama dan menggantinya dengan data dari file ini.
                </div>

                {{-- 
                Form ini akan dikirim ke route('laporan-puskesmas.import')
                yang ditangani oleh fungsi 'import' di SasaranPuskesmasController
                --}}
                <form method="POST" action="{{ route('laporan-puskesmas.import') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="file" class="form-label">Pilih File Excel (.xlsx, .xls) <span class="text-danger">*</span></label>
                        <input class="form-control @error('file') is-invalid @enderror" type="file" id="file" name="file" required accept=".xlsx, .xls">
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="alert alert-info small p-2">
                        <strong>Format Wajib:</strong> Pastikan file Excel Anda memiliki kolom `PUSKESMAS`, lalu kolom-kolom data seperti `BUMIL`, `BULIN`, `HIPERTENSI`, `DM`, dll.
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-upload"></i> Upload dan Proses File
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection