@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard Sistem Informasi Kinerja')

@section('content')
<style>
    /* --- CSS MODERN DASHBOARD --- */
    
    /* 1. Hero Section (Gradient Banner) */
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
    
    /* Ornamen background abstrak */
    .dashboard-hero::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }
    .dashboard-hero::after {
        content: '';
        position: absolute;
        bottom: -30px;
        right: 80px;
        width: 120px;
        height: 120px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .hero-btn {
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(5px);
        transition: all 0.3s ease;
    }
    .hero-btn:hover {
        background-color: white;
        color: #1e3a8a;
        transform: translateY(-2px);
    }

    /* 2. Stat Cards (Untuk Admin) */
    .stat-card {
        border: none;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        height: 100%;
        padding: 1.5rem;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    .icon-box {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-right: 1rem;
    }
    .icon-box.blue { background-color: #eff6ff; color: #3b82f6; }
    .icon-box.green { background-color: #f0fdf4; color: #22c55e; }
    .icon-box.cyan { background-color: #ecfeff; color: #06b6d4; }
    .icon-box.orange { background-color: #fff7ed; color: #f97316; }

    .stat-label { font-size: 0.85rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-value { font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 0; }

    /* 3. Action Cards (Untuk Puskesmas/User) */
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
    /* Status Badge di pojok kanan atas */
    .status-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 0.75rem;
        padding: 0.35rem 0.8rem;
        border-radius: 50px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .status-badge.missing { background-color: #fef2f2; color: #ef4444; border: 1px solid #fecaca; }
    .status-badge.filled { background-color: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }

    .action-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin-bottom: 1.5rem;
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .action-card:hover .action-icon { transform: scale(1.1); }
    
    .action-icon.missing { background-color: #fee2e2; color: #ef4444; } /* Merah */
    .action-icon.filled { background-color: #dcfce7; color: #16a34a; } /* Hijau */

    .action-title { font-weight: 700; color: #1e293b; margin-bottom: 0.5rem; font-size: 1.2rem; }
    .action-desc { color: #64748b; font-size: 0.95rem; margin-bottom: 1.5rem; min-height: 3rem; }
</style>

<div class="container-fluid px-4">

    {{-- ============================================================== --}}
    {{-- BAGIAN 1: DASHBOARD KHUSUS ADMIN --}}
    {{-- ============================================================== --}}
    @if(Auth::user()->role === 'admin')
        
        {{-- Hero Admin --}}
        <div class="card dashboard-hero mb-5">
            <div class="row align-items-center relative" style="z-index: 2;">
                <div class="col-lg-8">
                    <h2 class="fw-bold mb-2">Selamat Datang, Administrator! 👋</h2>
                    <p class="mb-4 opacity-75 fs-6" style="line-height: 1.6;">
                        Selamat datang di Dashboard Admin. Anda dapat memantau seluruh data kinerja dan administrasi Puskesmas serta Labkesda dari halaman ini.
                    </p>
                    <a href="{{ route('rekap.index') }}" class="btn hero-btn px-4 py-2 rounded-pill fw-bold">
                        <i class="fas fa-chart-pie me-2"></i> Lihat Rekapitulasi
                    </a>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-center">
                    <i class="fas fa-user-shield text-white" style="font-size: 8rem; opacity: 0.2;"></i>
                </div>
            </div>
        </div>

        {{-- Widget Statistik Admin --}}
        @if (isset($dataRingkasan))
            <div class="d-flex align-items-center mb-4">
                <h5 class="fw-bold text-dark m-0">
                    <i class="fas fa-chart-simple me-2 text-primary"></i>
                    Ringkasan Data Tahun {{ $tahunIni ?? date('Y') }}
                </h5>
            </div>

            <div class="row g-4">
                {{-- Puskesmas --}}
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon-box blue"><i class="fas fa-chart-line"></i></div>
                            <div>
                                <div class="stat-label">Kinerja Puskesmas</div>
                                <div class="stat-value">{{ $dataRingkasan['countKinerjaPusk'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon-box green"><i class="fas fa-file-invoice"></i></div>
                            <div>
                                <div class="stat-label">Admin & TU (Puskesmas)</div>
                                <div class="stat-value">{{ $dataRingkasan['countAdminPusk'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Labkesda --}}
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon-box cyan"><i class="fas fa-flask"></i></div>
                            <div>
                                <div class="stat-label">Kinerja Labkesda</div>
                                <div class="stat-value">{{ $dataRingkasan['countKinerjaLab'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon-box orange"><i class="fas fa-file-archive"></i></div>
                            <div>
                                <div class="stat-label">Admin & TU (Labkesda)</div>
                                <div class="stat-value">{{ $dataRingkasan['countAdminLab'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    {{-- ============================================================== --}}
    {{-- BAGIAN 2: DASHBOARD USER (PUSKESMAS / LABKESDA) --}}
    {{-- ============================================================== --}}
    @else

        {{-- Hero User --}}
        <div class="card dashboard-hero mb-5">
            <div class="row align-items-center relative" style="z-index: 2;">
                <div class="col-lg-8">
                    <h2 class="fw-bold mb-2">Selamat Datang, {{ Auth::user()->name }}!</h2>
                    <p class="mb-0 opacity-75 fs-6" style="line-height: 1.6;">
                        Ini adalah dashboard pribadi Anda untuk unit <strong>{{ Auth::user()->puskesmas_name ?? Auth::user()->name }}</strong>. 
                        Anda dapat melihat status pelaporan tahunan dan mengambil tindakan cepat dari halaman ini.
                    </p>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-center">
                    <i class="fas fa-hospital-user text-white" style="font-size: 8rem; opacity: 0.2;"></i>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center mb-4">
            <h5 class="fw-bold text-dark m-0">
                <i class="fas fa-list-check me-2 text-primary"></i>
                Status Laporan Anda (Tahun {{ $tahunIni ?? date('Y') }})
            </h5>
        </div>

        <div class="row g-4 justify-content-center">
            
            {{-- KARTU 1: LAPORAN KINERJA --}}
            <div class="col-md-6 col-xl-5">
                <div class="action-card">
                    {{-- Cek apakah variabel laporanKinerja ada (dikirim controller) --}}
                    @if(isset($laporanKinerja) && $laporanKinerja)
                        {{-- STATUS: SUDAH MENGISI --}}
                        <div class="status-badge filled">Sudah Diisi</div>
                        <div class="action-icon filled"><i class="fas fa-check"></i></div>
                        <h4 class="action-title">Laporan Kinerja</h4>
                        <p class="action-desc">
                            Terima kasih, Anda <strong>SUDAH</strong> mengisi Laporan Kinerja untuk tahun {{ $tahunIni ?? date('Y') }}.
                        </p>
                        <a href="{{ route('laporan-kinerja.user.index', ['tahun' => $tahunIni ?? date('Y')]) }}" class="btn btn-outline-success rounded-pill px-4 fw-bold">
                            <i class="fas fa-eye me-2"></i> Lihat Data Saya
                        </a>
                    @else
                        {{-- STATUS: BELUM MENGISI --}}
                        <div class="status-badge missing">Belum Diisi</div>
                        <div class="action-icon missing"><i class="fas fa-times"></i></div>
                        <h4 class="action-title">Laporan Kinerja</h4>
                        <p class="action-desc text-danger">
                            Anda <strong>BELUM</strong> mengisi Laporan Kinerja untuk tahun {{ $tahunIni ?? date('Y') }}.
                        </p>
                        @php
                            // Helper route logic tergantung role
                            $createRoute = Auth::user()->role === 'labkesda' 
                                ? route('laporan-kinerja.create.labkesda', ['tahun' => date('Y')])
                                : route('laporan-kinerja.create', ['puskesmas' => Auth::user()->puskesmas_name, 'tahun' => date('Y')]);
                        @endphp
                        <a href="{{ $createRoute }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow">
                            <i class="fas fa-plus me-2"></i> Buat Laporan Kinerja
                        </a>
                    @endif
                </div>
            </div>

            {{-- KARTU 2: LAPORAN ADMIN & TU --}}
            <div class="col-md-6 col-xl-5">
                <div class="action-card">
                    @if(isset($laporanAdminTu) && $laporanAdminTu)
                        {{-- STATUS: SUDAH MENGISI --}}
                        <div class="status-badge filled">Sudah Diisi</div>
                        <div class="action-icon filled"><i class="fas fa-check"></i></div>
                        <h4 class="action-title">Laporan Administrasi & TU</h4>
                        <p class="action-desc">
                            Terima kasih, Anda <strong>SUDAH</strong> mengisi Laporan Administrasi & TU untuk tahun {{ $tahunIni ?? date('Y') }}.
                        </p>
                        <a href="{{ route('administrasi-tu.index', ['puskesmas' => Auth::user()->puskesmas_name, 'tahun' => $tahunIni ?? date('Y'), 'jenis_laporan' => Auth::user()->role]) }}" class="btn btn-outline-success rounded-pill px-4 fw-bold">
                            <i class="fas fa-eye me-2"></i> Lihat Data Saya
                        </a>
                    @else
                        {{-- STATUS: BELUM MENGISI --}}
                        <div class="status-badge missing">Belum Diisi</div>
                        <div class="action-icon missing"><i class="fas fa-times"></i></div>
                        <h4 class="action-title">Laporan Administrasi & TU</h4>
                        <p class="action-desc text-danger">
                            Anda <strong>BELUM</strong> mengisi Laporan Administrasi & TU untuk tahun {{ $tahunIni ?? date('Y') }}.
                        </p>
                        @php
                            $createAdminRoute = Auth::user()->role === 'labkesda'
                                ? route('administrasi-tu.create.labkesda', ['tahun' => date('Y')])
                                : route('administrasi-tu.create', ['puskesmas' => Auth::user()->puskesmas_name, 'tahun' => date('Y')]);
                        @endphp
                        <a href="{{ $createAdminRoute }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow">
                            <i class="fas fa-plus me-2"></i> Buat Laporan Admin & TU
                        </a>
                    @endif
                </div>
            </div>

        </div>

    @endif

</div>
@endsection