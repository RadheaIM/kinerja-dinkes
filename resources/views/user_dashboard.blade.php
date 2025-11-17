@extends('layouts.app')

@section('title', 'Dashboard Pengguna')
@section('page_title', 'Dashboard Sistem Informasi Kinerja')

@section('content')
<div class="container-fluid">

    {{-- Pesan Selamat Datang --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5 class="card-title">Selamat Datang, {{ $user->name }}!</h5>
            <p class="card-text">
                Ini adalah dashboard pribadi Anda untuk unit <strong>{{ $namaUnit ?? Auth::user()->email }}</strong>. 
                Anda dapat melihat status pelaporan tahunan dan mengambil tindakan cepat dari halaman ini.
            </p>
        </div>
    </div>

    {{-- Pesan Error (jika ada, dari HomeController) --}}
    @if(session('error_manual'))
    <div class="alert alert-danger">
        {{ session('error_manual') }}
    </div>
    @endif

    {{-- Ringkasan Laporan Tahun Ini --}}
    <h5 class="mb-3">Ringkasan Laporan Anda (Tahun {{ $tahunIni }})</h5>
    <div class="row">

        <!-- ========================================================== -->
        <!-- KOTAK STATUS LAPORAN KINERJA -->
        <!-- ========================================================== -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">1. Laporan Kinerja</h6>
                </div>
                <div class="card-body text-center">
                    @if($laporanKinerja) {{-- $laporanKinerja adalah data laporan (objek) --}}
                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        <p>Laporan Kinerja Anda untuk tahun {{ $tahunIni }} sudah diisi.</p>
                        
                        {{-- PERBAIKAN LINK EDIT (menggunakan ID laporan) --}}
                        <a href="{{ route('laporan-kinerja.edit', ['id' => $laporanKinerja->id]) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i> Edit Laporan Kinerja
                        </a>
                    @else
                        <i class="fas fa-times-circle fa-4x text-danger mb-3"></i>
                        <p>Anda BELUM mengisi Laporan Kinerja untuk tahun {{ $tahunIni }}.</p>
                        
                        {{-- PERBAIKAN LINK CREATE (menggunakan $namaUnit) --}}
                        @if($user->role === 'labkesda')
                            <a href="{{ route('laporan-kinerja.create.labkesda', ['tahun' => $tahunIni]) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Buat Laporan Kinerja
                            </a>
                        @else
                            <a href="{{ route('laporan-kinerja.create', ['puskesmas' => $namaUnit, 'tahun' => $tahunIni]) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Buat Laporan Kinerja
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- ========================================================== -->
        <!-- KOTAK STATUS LAPORAN ADMINISTRASI & TU -->
        <!-- ========================================================== -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">2. Laporan Administrasi & TU</h6>
                </div>
                <div class="card-body text-center">
                    @if($laporanAdminTu) {{-- $laporanAdminTu adalah boolean (true/false) --}}
                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        <p>Laporan Administrasi & TU Anda untuk tahun {{ $tahunIni }} sudah diisi.</p>
                        
                        {{-- INI ADALAH PERBAIKAN YANG MENYELESAIKAN ERROR ANDA --}}
                        <a href="{{ route('administrasi-tu.edit', ['puskesmas' => $namaUnit, 'tahun' => $tahunIni]) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i> Edit Laporan Admin & TU
                        </a>
                    @else
                        <i class="fas fa-times-circle fa-4x text-danger mb-3"></i>
                        <p>Anda BELUM mengisi Laporan Administrasi & TU untuk tahun {{ $tahunIni }}.</p>
                        
                        {{-- PERBAIKAN LINK CREATE (menggunakan $namaUnit) --}}
                        @if($user->role === 'labkesda')
                            <a href="{{ route('administrasi-tu.create.labkesda', ['tahun' => $tahunIni]) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Buat Laporan Admin & TU
                            </a>
                        @else
                            <a href="{{ route('administrasi-tu.create', ['puskesmas' => $namaUnit, 'tahun' => $tahunIni]) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Buat Laporan Admin & TU
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection