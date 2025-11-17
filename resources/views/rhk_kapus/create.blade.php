@extends('layouts.app') {{-- Pastikan ini nama layout utama Anda --}}

@section('title', 'Import Massal RHK Kapus')
@section('page_title', 'Import Massal RHK Kapus')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">

            {{-- Menampilkan Error Validasi --}}
            @if ($errors->any())
                <div class="alert alert-danger border-left-danger" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger border-left-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            {{-- 
                ============================================================
                PERBAIKAN UTAMA: Form sekarang ke 'rhk-kapus.store'
                ============================================================
            --}}
            <form action="{{ route('rhk-kapus.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Upload Template Perkin Massal (Untuk Semua Puskesmas)</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-3 text-danger fw-bold">
                            PERHATIAN: Fitur ini akan meng-upload <strong>satu file template</strong> Perkin dan menerapkannya untuk <strong>semua 67 Puskesmas</strong> sekaligus untuk tahun yang Anda pilih. Data lama di tahun tersebut akan dihapus dan diganti.
                        </p>

                        <div class="row">
                            
                            {{-- 
                                ============================================================
                                PERBAIKAN: Dropdown "Pilih Puskesmas" sudah DIHAPUS
                                ============================================================
                            --}}

                            <!-- 1. Pilih Tahun -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="tahun" class="form-label">Tahun Laporan:</label>
                                    <select class="form-select" id="tahun" name="tahun" required>
                                        @php $currentYear = date('Y'); @endphp
                                        <option value="{{ $currentYear + 1 }}">{{ $currentYear + 1 }}</option>
                                        <option value="{{ $currentYear }}" selected>{{ $currentYear }}</option>
                                        <option value="{{ $currentYear - 1 }}">{{ $currentYear - 1 }}</option>
                                    </select>
                                </div>
                            </div>

                            <!-- 2. Upload File -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="file_perkin" class="form-label">Upload File Perkin (.xlsx atau .xls):</label>
                                    <input class="form-control" type="file" id="file_perkin" name="file_perkin" required accept=".xlsx, .xls">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="d-flex justify-content-end">
                    <a href="{{ route('rhk-kapus.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-times me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i> Upload Massal untuk Semua Puskesmas
                    </button>
                </div>
            </form>

        </div>

        <div class="col-lg-4">
             <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Instruksi</h6>
                </div>
                <div class="card-body">
                    <ol>
                        <li class="mb-2">Gunakan fitur ini hanya <strong>satu kali</strong> di awal tahun.</li>
                        <li class="mb-2">Pastikan file Excel Anda adalah <strong>template Perkin Kapus</strong> yang standar.</li>
                        <li class="mb-2">File <strong>tidak perlu</strong> berisi nama puskesmas.</li>
                        <li class="mb-2">Sistem akan otomatis membuat RHK untuk semua unit Puskesmas yang terdaftar di <strong>Data Sasaran</strong>.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection