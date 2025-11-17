@extends('layouts.app')

@section('title', 'Import Data Sasaran Puskesmas')
@section('page_title', 'Import Data Sasaran Puskesmas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Formulir Import Data Excel</h6>
                </div>
                <div class="card-body">
                    
                    <!-- Notifikasi Error Validasi -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Whoops! Ada beberapa masalah dengan input Anda.</strong><br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Notifikasi Sukses/Error dari Controller -->
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('laporan-puskesmas.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="form-group">
                            <label for="file">Pilih File Excel (.xlsx, .csv)</label>
                            <input type="file" name="file" class="form-control-file" id="file" required>
                            <small class="form-text text-muted">
                                Pastikan header di file Excel Anda sesuai dengan template: 
                                (puskesmas, bumil, bulin, bbl, balita_ds, pendidikan_dasar, uspro, lansia, hipertensi, dm, odgj_berat, tb, hiv, idl)
                            </small>
                        </div>
                        
                        <hr>
                        
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-upload"></i> Upload dan Import Data
                        </button>
                        <a href="{{ route('laporan-puskesmas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                        </a>
                    </form>

                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Instruksi Import</h6>
                </div>
                <div class="card-body">
                    <p>Ikuti langkah-langkah berikut untuk mengimpor data:</p>
                    <ol>
                        <li>Siapkan data Anda dalam format file Excel (.xlsx) atau CSV (.csv).</li>
                        <li>Pastikan baris pertama (header) file Anda berisi nama kolom yang sesuai.</li>
                        <li>Klik tombol "Pilih File" dan pilih file Anda.</li>
                        <li>Klik tombol "Upload dan Import Data".</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection