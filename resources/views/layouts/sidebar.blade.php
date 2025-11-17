<nav class="sidebar"> {{-- Hapus p-3 dari sini jika sudah ada di style --}}
    <a href="{{ route('dashboard') }}" class="text-center mb-4 d-block text-white text-decoration-none">
        <i class="fa-solid fa-heart-pulse fa-xl mb-2"></i> <br> {{-- Ikon Detak Jantung --}}
        <h5 class="fw-bold mb-0">LAPORAN KINERJA UNIT PUSKESMAS DAN LABKESDA</h5>
    </a>

    {{-- Tampilkan semua menu HANYA JIKA USER SUDAH LOGIN --}}
    @auth

    <ul class="nav flex-column">

        {{-- Dashboard (Bisa dilihat semua orang) --}}
        <li class="nav-item mb-2">
            <a href="{{ url('/home') }}"
                class="nav-link {{ request()->is('home') ? 'active' : '' }}">
                <i class="fa-solid fa-house me-2"></i> Dashboard
            </a>
        </li>
        
        
        {{-- RHK Kapus (Bisa dilihat semua role) --}}
        <li class="nav-item mb-2">
            <a href="{{ route('rhk-kapus.index') }}"
                class="nav-link {{ request()->routeIs('rhk-kapus.*') ? 'active' : '' }}">
                <i class="fa-solid fa-tasks me-2"></i> RHK Kapus
            </a>
        </li>

        {{-- 
        ============================================================
        === INI PERBAIKANNYA ===
        Menu Sasaran Puskesmas sekarang HANYA terlihat
        jika role adalah 'admin' ATAU 'puskesmas'
        ============================================================
        --}}
         @if (Auth::user()->role === 'admin' || Auth::user()->role === 'puskesmas')
             <li class="nav-item mb-2">
                <a href="{{ route('laporan-puskesmas.index') }}"
                    class="nav-link {{ request()->routeIs('laporan-puskesmas.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-hospital me-2"></i> Sasaran Puskesmas
                </a>
            </li>
         @endif
        {{-- ============================================================ --}}


        {{-- 
        ============================================================
        MENU KHUSUS ADMIN ('admin')
        ============================================================
        --}}
        @if (Auth::user()->role === 'admin')
            {{-- Rekap Bulanan (Tetap Admin Only) --}}
            <li class="nav-item mb-2">
                <a href="{{ route('rekap.index') }}"
                    class="nav-link {{ request()->routeIs('rekap.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-days me-2"></i> Rekap Bulanan
                </a>
            </li>
        @endif
        {{-- ============================================================ --}}


        {{-- 
        ============================================================
        MENU LAPORAN (UNTUK SEMUA ROLE)
        ============================================================
        --}}

        {{-- Laporan Kinerja Dropdown --}}
        <li class="nav-item mb-2">
            <a href="#laporanKinerjaMenu" data-bs-toggle="collapse"
                class="nav-link {{ request()->routeIs('laporan-kinerja.*') ? 'fw-bold' : '' }}"
                aria-expanded="{{ request()->routeIs('laporan-kinerja.*') ? 'true' : 'false' }}">
                <i class="fa-solid fa-chart-line me-2"></i> Laporan Kinerja
                <i class="fas fa-chevron-down float-end ms-1"></i>
            </a>
            <div class="collapse {{ request()->routeIs('laporan-kinerja.*') ? 'show' : '' }}" id="laporanKinerjaMenu">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                    
                    @if (Auth::user()->role === 'admin')
                        <li>
                            <a href="{{ route('laporan-kinerja.create') }}" class="nav-link py-1 {{ request()->routeIs('laporan-kinerja.create') ? 'active' : '' }}">
                                <i class="fas fa-plus fa-sm me-1"></i> Buat Lap. Puskesmas
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('laporan-kinerja.create.labkesda') }}" class="nav-link py-1 {{ request()->routeIs('laporan-kinerja.create.labkesda') ? 'active' : '' }}">
                                <i class="fas fa-plus fa-sm me-1"></i> Buat Lap. Labkesda
                            </a>
                        </li>
                    
                    @elseif (Auth::user()->role === 'labkesda')
                        <li>
                            <a href="{{ route('laporan-kinerja.create.labkesda', ['tahun' => date('Y')]) }}" class="nav-link py-1 {{ request()->routeIs('laporan-kinerja.create.labkesda') ? 'active' : '' }}">
                                <i class="fas fa-plus fa-sm me-1"></i> Buat Laporan Saya
                            </a>
                        </li>
                    
                    @elseif (Auth::user()->role === 'puskesmas')
                        <li>
                            <a href="{{ route('laporan-kinerja.create', ['puskesmas' => Auth::user()->puskesmas_name, 'tahun' => date('Y')]) }}" class="nav-link py-1 {{ request()->routeIs('laporan-kinerja.create') ? 'active' : '' }}">
                                <i class="fas fa-plus fa-sm me-1"></i> Buat Laporan Saya
                            </a>
                        </li>
                    @endif
                    
                    @if (Auth::user()->role === 'admin')
                        <li>
                            <a href="{{ route('laporan-kinerja.admin.index') }}" class="nav-link py-1 {{ request()->routeIs('laporan-kinerja.admin.index') ? 'active' : '' }}">
                                <i class="fas fa-list fa-sm me-1"></i> Lihat Semua Data
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('laporan-kinerja.user.index', ['tahun' => date('Y')]) }}" 
                           class="nav-link py-1 {{ request()->routeIs('laporan-kinerja.user.index') ? 'active' : '' }}">
                                <i class="fas fa-list fa-sm me-1"></i> Lihat Data Saya
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </li>

        {{-- Administrasi & TU Dropdown --}}
        <li class="nav-item mb-2">
            <a href="#adminTuMenu" data-bs-toggle="collapse"
                class="nav-link {{ request()->routeIs('administrasi-tu.*') ? 'fw-bold' : '' }}"
                aria-expanded="{{ request()->routeIs('administrasi-tu.*') ? 'true' : 'false' }}">
                <i class="fa-solid fa-file-invoice me-2"></i> Administrasi & TU
                <i class="fas fa-chevron-down float-end ms-1"></i>
            </a>
            <div class="collapse {{ request()->routeIs('administrasi-tu.*') ? 'show' : '' }}" id="adminTuMenu">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                    
                    @if (Auth::user()->role === 'admin')
                        <li>
                            <a href="{{ route('administrasi-tu.create') }}" class="nav-link py-1 {{ request()->routeIs('administrasi-tu.create') ? 'active' : '' }}">
                                <i class="fas fa-plus fa-sm me-1"></i> Buat Lap. Puskesmas
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('administrasi-tu.create.labkesda') }}" class="nav-link py-1 {{ request()->routeIs('administrasi-tu.create.labkesda') ? 'active' : '' }}">
                                <i class="fas fa-plus fa-sm me-1"></i> Buat Lap. Labkesda
                            </a>
                        </li>

                    @elseif (Auth::user()->role === 'labkesda')
                        <li>
                            <a href="{{ route('administrasi-tu.create.labkesda', ['tahun' => date('Y')]) }}" class="nav-link py-1 {{ request()->routeIs('administrasi-tu.create.labkesda') ? 'active' : '' }}">
                                <i class="fas fa-plus fa-sm me-1"></i> Buat Laporan Saya
                            </a>
                        </li>
                    
                    @elseif (Auth::user()->role === 'puskesmas')
                        <li>
                            <a href="{{ route('administrasi-tu.create', ['puskesmas' => Auth::user()->puskesmas_name, 'tahun' => date('Y')]) }}" class="nav-link py-1 {{ request()->routeIs('administrasi-tu.create') ? 'active' : '' }}">
                                <i class="fas fa-plus fa-sm me-1"></i> Buat Laporan Saya
                            </a>
                        </li>
                    @endif
                    
                    @if (Auth::user()->role === 'admin')
                        <li>
                            <a href="{{ route('administrasi-tu.index') }}" class="nav-link py-1 {{ request()->routeIs('administrasi-tu.index') && !request()->input('puskesmas') ? 'active' : '' }}">
                                <i class="fas fa-list fa-sm me-1"></i> Lihat Semua Data
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('administrasi-tu.index', ['puskesmas' => Auth::user()->puskesmas_name, 'tahun' => date('Y'), 'jenis_laporan' => Auth::user()->role]) }}" 
                           class="nav-link py-1 {{ request()->routeIs('administrasi-tu.index') && request()->input('puskesmas') ? 'active' : '' }}">
                                <i class="fas fa-list fa-sm me-1"></i> Lihat Data Saya
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </li>
    </ul>

    <hr class="bg-white opacity-25">

    <ul class="nav flex-column">
        {{-- Profil (Bisa dilihat semua orang) --}}
        <li class="nav-item mb-2">
            <a href="{{ route('profil') }}"
                class="nav-link {{ request()->is('profil') ? 'active' : '' }}">
                <i class="fa-solid fa-user me-2"></i> Profil Pengguna
            </a>
        </li>

            <!-- Bagian Manajemem User (Hanya untuk Admin) -->
    @if(Auth::user()->role === 'admin')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('manajemen-user.index') }}">
                <i class="fas fa-users-cog"></i>
                <span>Manajemen User</span>
            </a>
        </li>
    @endif

        {{-- Logout (Bisa dilihat semua orang) --}}
        <li class="nav-item">
            <a href="#" class="nav-link text-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
            </a>
        </li>
    </ul>

    @endauth {{-- AKHIR DARI @auth --}}
</nav>