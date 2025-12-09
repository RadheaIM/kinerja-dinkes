@extends('layouts.app')

@section('title', 'Dashboard Pengguna')
@section('page_title', 'Dashboard Sistem Informasi Kinerja')

@section('content')
<style>
    /* --- CSS MODERN DASHBOARD (Sama dengan Home) --- */
    
    /* 1. Hero Section */
    .dashboard-hero {
        background: linear-gradient(135deg, #3b82f6, #1e3a8a);
        color: white;
        border-radius: 1rem;
        padding: 2.5rem;
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
        position: relative;
        overflow: hidden;
        border: none;
    }
    
    .dashboard-hero::before {
        content: ''; position: absolute; top: -50px; right: -50px; width: 200px; height: 200px;
        background: rgba(255, 255, 255, 0.1); border-radius: 50%;
    }
    .dashboard-hero::after {
        content: ''; position: absolute; bottom: -30px; right: 80px; width: 120px; height: 120px;
        background: rgba(255, 255, 255, 0.1); border-radius: 50%;
    }

    /* 2. Action Cards */
    .action-card {
        border: none;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        text-align: center;
        padding: 2.5rem 1.5rem;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }

    /* Status Badge */
    .status-badge {
        position: absolute; top: 15px; right: 15px; font-size: 0.75rem;
        padding: 0.35rem 0.8rem; border-radius: 50px; font-weight: 600; text-transform: uppercase;
    }
    .status-badge.missing { background-color: #fef2f2; color: #ef4444; border: 1px solid #fecaca; }
    .status-badge.filled { background-color: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }

    /* Icons */
    .action-icon {
        width: 80px; height: 80px; border-radius: 50%; display: inline-flex;
        align-items: center; justify-content: center; font-size: 2.5rem;
        margin-bottom: 1.5rem; transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .action-card:hover .action-icon { transform: scale(1.1); }
    .action-icon.missing { background-color: #fee2e2; color: #ef4444; }
    .action-icon.filled { background-color: #dcfce7; color: #16a34a; }

    .action-title { font-weight: 700; color: #1e293b; margin-bottom: 0.5rem; font-size: 1.2rem; }
    .action-desc { color: #64748b; font-size: 0.95rem; margin-bottom: 1.5rem; min-height: 3rem; }
</style>

<div class="container-fluid px-4">

    {{-- 1. HERO SECTION --}}
    <div class="card dashboard-hero mb-5">
        <div class="row align-items-center relative" style="z-index: 2;">
            <div class="col-lg-8">
                <h2 class="fw-bold mb-2">Selamat Datang, {{ $user->name }}!</h2>
                <p class="mb-0 opacity-75 fs-6" style="line-height: 1.6;">
                    Ini adalah dashboard pribadi Anda untuk unit <strong>{{ $namaUnit ?? $user->email }}</strong>. 
                    Anda dapat melihat status pelaporan tahunan dan mengambil tindakan cepat dari halaman ini.
                </p>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-center">
                <i class="fas fa-hospital-user text-white" style="font-size: 8rem; opacity: 0.2;"></i>
            </div>
        </div>
    </div>

    {{-- Pesan Error Manual (Jika ada) --}}
    @if(session('error_manual'))
    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error_manual') }}
    </div>
    @endif

    {{-- 2. STATUS LAPORAN --}}
    <div class="d-flex align-items-center mb-4">
        <h5 class="fw-bold text-dark m-0">
            <i class="fas fa-list-check me-2 text-primary"></i>
            Status Laporan Anda (Tahun {{ $tahunIni }})
        </h5>
    </div>

    <div class="row g-4 justify-content-center">

        {{-- KARTU 1: LAPORAN KINERJA --}}
        <div class="col-md-6 col-xl-5">
            <div class="action-card">
                @if($laporanKinerja)
                    {{-- STATUS: SUDAH DIISI --}}
                    <div class="status-badge filled">Sudah Diisi</div>
                    <div class="action-icon filled"><i class="fas fa-check"></i></div>
                    <h4 class="action-title">Laporan Kinerja</h4>
                    <p class="action-desc">
                        Laporan Kinerja Anda untuk tahun {{ $tahunIni }} <strong>SUDAH</strong> diisi.
                    </p>
                    <a href="{{ route('laporan-kinerja.edit', ['id' => $laporanKinerja->id]) }}" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fas fa-edit me-2"></i> Edit Laporan
                    </a>
                @else
                    {{-- STATUS: BELUM DIISI --}}
                    <div class="status-badge missing">Belum Diisi</div>
                    <div class="action-icon missing"><i class="fas fa-times"></i></div>
                    <h4 class="action-title">Laporan Kinerja</h4>
                    <p class="action-desc text-danger">
                        Anda <strong>BELUM</strong> mengisi Laporan Kinerja untuk tahun {{ $tahunIni }}.
                    </p>
                    @if($user->role === 'labkesda')
                        <a href="{{ route('laporan-kinerja.create.labkesda', ['tahun' => $tahunIni]) }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow">
                            <i class="fas fa-plus me-2"></i> Buat Laporan
                        </a>
                    @else
                        <a href="{{ route('laporan-kinerja.create', ['puskesmas' => $namaUnit, 'tahun' => $tahunIni]) }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow">
                            <i class="fas fa-plus me-2"></i> Buat Laporan
                        </a>
                    @endif
                @endif
            </div>
        </div>

        {{-- KARTU 2: LAPORAN ADMIN & TU --}}
        <div class="col-md-6 col-xl-5">
            <div class="action-card">
                @if($laporanAdminTu)
                    {{-- STATUS: SUDAH DIISI --}}
                    <div class="status-badge filled">Sudah Diisi</div>
                    <div class="action-icon filled"><i class="fas fa-check"></i></div>
                    <h4 class="action-title">Laporan Administrasi & TU</h4>
                    <p class="action-desc">
                        Laporan Admin & TU Anda untuk tahun {{ $tahunIni }} <strong>SUDAH</strong> diisi.
                    </p>
                    <a href="{{ route('administrasi-tu.edit', ['puskesmas' => $namaUnit, 'tahun' => $tahunIni]) }}" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fas fa-edit me-2"></i> Edit Laporan
                    </a>
                @else
                    {{-- STATUS: BELUM DIISI --}}
                    <div class="status-badge missing">Belum Diisi</div>
                    <div class="action-icon missing"><i class="fas fa-times"></i></div>
                    <h4 class="action-title">Laporan Administrasi & TU</h4>
                    <p class="action-desc text-danger">
                        Anda <strong>BELUM</strong> mengisi Laporan Admin & TU untuk tahun {{ $tahunIni }}.
                    </p>
                    @if($user->role === 'labkesda')
                        <a href="{{ route('administrasi-tu.create.labkesda', ['tahun' => $tahunIni]) }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow">
                            <i class="fas fa-plus me-2"></i> Buat Laporan
                        </a>
                    @else
                        <a href="{{ route('administrasi-tu.create', ['puskesmas' => $namaUnit, 'tahun' => $tahunIni]) }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow">
                            <i class="fas fa-plus me-2"></i> Buat Laporan
                        </a>
                    @endif
                @endif
            </div>
        </div>

    </div>
</div>
@endsection