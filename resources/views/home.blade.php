@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard Sistem Informasi Kinerja')

@section('content')
<div class="container-fluid">

    {{-- Pesan Selamat Datang --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5 class="card-title">Selamat Datang, {{ Auth::user()->name }}!</h5>
            <p class="card-text">
                Selamat datang di "Sistem Informasi Pelaporan Kinerja Puskesmas dan Labkesda" Dinas Kesehatan Kabupaten Garut.
                Sistem ini bertujuan untuk mempermudah proses pengumpulan, pengelolaan, dan pemantauan data kinerja serta administrasi dari seluruh unit pelayanan kesehatan di bawah naungan Dinas Kesehatan.
            </p>
            <p class="card-text">
                Silakan gunakan menu navigasi di sebelah kiri untuk mengakses fitur-fitur yang tersedia, seperti membuat laporan baru, melihat data sasaran, atau melihat rekapitulasi laporan.
            </p>
            <a href="{{ route('rekap.index') }}" class="btn btn-primary">
                <i class="fas fa-calendar-days me-1"></i> Lihat Rekap Laporan
            </a>
        </div>
    </div>

    {{-- ========================================================== --}}
    {{-- === PERBAIKAN: Tampilkan Widget Box atau Error Box === --}}
    {{-- ========================================================== --}}

    @if (isset($dataRingkasan))
        {{-- JIKA DATA ADA, TAMPILKAN WIDGET BOX --}}
        <h5 class="mb-3">Ringkasan Laporan Tahun {{ $tahunIni }}</h5>
        <div class="row">
            <!-- Widget Kinerja Puskesmas -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Laporan Kinerja (Puskesmas)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $dataRingkasan['countKinerjaPusk'] }} Laporan</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Widget Admin TU Puskesmas -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Laporan Admin & TU (Puskesmas)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $dataRingkasan['countAdminPusk'] }} Laporan</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Widget Kinerja Labkesda -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Laporan Kinerja (Labkesda)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $dataRingkasan['countKinerjaLab'] }} Laporan</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-flask fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Widget Admin TU Labkesda -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Laporan Admin & TU (Labkesda)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $dataRingkasan['countAdminLab'] }} Laporan</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-archive fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
    @else
        {{-- JIKA DATA GAGAL DIAMBIL ($dataRingkasan = null), TAMPILKAN ERROR BOX --}}
        <div class="alert alert-warning">
            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Gagal Memuat Data</h5>
            <p>Tidak dapat memuat ringkasan data saat ini. Ini bisa terjadi jika ada masalah koneksi database atau query gagal dijalankan.</p>
            <hr>
            <p class="mb-0">Silakan periksa file log (`storage/logs/laravel.log`) untuk detail teknis.</p>
        </div>
    @endif
    
    {{-- ========================================================== --}}
    {{-- === AKHIR PERBAIKAN === --}}
    {{-- ========================================================== --}}

</div>
@endsection