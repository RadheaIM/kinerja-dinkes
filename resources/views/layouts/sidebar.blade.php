<nav class="sidebar">
    {{-- Header Sidebar --}}
    <a href="{{ route('dashboard') }}" class="text-center mb-3 d-block text-white text-decoration-none">
        <i class="fa-solid fa-heart-pulse fa-lg sidebar-icon-header"></i> 
        <br>
        <h6 class="fw-bold mb-0 sidebar-header-text">LAPORAN KINERJA <br> PUSKESMAS & LABKESDA</h6>
    </a>

    {{-- Garis pembatas --}}
    <hr class="bg-white opacity-25 mt-0 mb-2">

    @auth
    <ul class="nav flex-column">

        <li class="nav-item">
            <a href="{{ url('/home') }}"
                class="nav-link {{ request()->is('home') ? 'active' : '' }}">
                <i class="fa-solid fa-house me-2"></i> Dashboard
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('rhk-kapus.index') }}"
                class="nav-link {{ request()->routeIs('rhk-kapus.*') ? 'active' : '' }}">
                <i class="fa-solid fa-tasks me-2"></i> RHK Kapus
            </a>
        </li>

        @if (Auth::user()->role === 'admin' || Auth::user()->role === 'puskesmas')
            <li class="nav-item">
                <a href="{{ route('laporan-puskesmas.index') }}"
                    class="nav-link {{ request()->routeIs('laporan-puskesmas.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-hospital me-2"></i> Sasaran Puskesmas
                </a>
            </li>
        @endif

        @if (Auth::user()->role === 'admin')
            <li class="nav-item">
                <a href="{{ route('rekap.index') }}"
                    class="nav-link {{ request()->routeIs('rekap.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-days me-2"></i> Rekap Bulanan
                </a>
            </li>
        @endif

        {{-- Laporan Kinerja Dropdown --}}
        <li class="nav-item">
            <a data-bs-toggle="collapse" 
               data-bs-target="#laporanKinerjaMenu"
               aria-controls="laporanKinerjaMenu" 
                class="nav-link {{ request()->routeIs('laporan-kinerja.*') ? 'fw-bold' : '' }}"
                aria-expanded="{{ request()->routeIs('laporan-kinerja.*') ? 'true' : 'false' }}"
                style="cursor: pointer;">
                <i class="fa-solid fa-chart-line me-2"></i> Laporan Kinerja
                <i class="fas fa-chevron-down float-end ms-1" style="font-size: 0.7em; margin-top: 5px;"></i>
            </a>
            <div class="collapse {{ request()->routeIs('laporan-kinerja.*') ? 'show' : '' }}" id="laporanKinerjaMenu">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                    @if (Auth::user()->role === 'admin')
                        <li>
                            <a href="{{ route('laporan-kinerja.create') }}" class="nav-link {{ request()->routeIs('laporan-kinerja.create') ? 'active' : '' }}">
                                <i class="fas fa-plus fa-xs me-1"></i> Buat Lap. Puskesmas
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('laporan-kinerja.create.labkesda') }}" class="nav-link {{ request()->routeIs('laporan-kinerja.create.labkesda') ? 'active' : '' }}">
                                <i class="fas fa-plus fa-xs me-1"></i> Buat Lap. Labkesda
                            </a>
                        </li>
                    @elseif (Auth::user()->role === 'labkesda')
                        <li>
                            <a href="{{ route('laporan-kinerja.create.labkesda', ['tahun' => date('Y')]) }}" class="nav-link {{ request()->routeIs('laporan-kinerja.create.labkesda') ? 'active' : '' }}">
                                <i class="fas fa-plus fa-xs me-1"></i> Buat Laporan Saya
                            </a>
                        </li>
                    @elseif (Auth::user()->role === 'puskesmas')
                        <li>
                            <a href="{{ route('laporan-kinerja.create', ['puskesmas' => Auth::user()->puskesmas_name, 'tahun' => date('Y')]) }}" class="nav-link {{ request()->routeIs('laporan-kinerja.create') ? 'active' : '' }}">
                                <i class="fas fa-plus fa-xs me-1"></i> Buat Laporan Saya
                            </a>
                        </li>
                    @endif
                    
                    @if (Auth::user()->role === 'admin')
                        <li>
                            <a href="{{ route('laporan-kinerja.admin.index') }}" class="nav-link {{ request()->routeIs('laporan-kinerja.admin.index') ? 'active' : '' }}">
                                <i class="fas fa-list fa-xs me-1"></i> Lihat Semua Data
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('laporan-kinerja.user.index', ['tahun' => date('Y')]) }}" class="nav-link {{ request()->routeIs('laporan-kinerja.user.index') ? 'active' : '' }}">
                                <i class="fas fa-list fa-xs me-1"></i> Lihat Data Saya
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </li>

        {{-- Administrasi & TU Dropdown --}}
        <li class="nav-item">
            <a data-bs-toggle="collapse" 
               data-bs-target="#adminTuMenu"
               aria-controls="adminTuMenu" 
                class="nav-link {{ request()->routeIs('administrasi-tu.*') ? 'fw-bold' : '' }}"
                aria-expanded="{{ request()->routeIs('administrasi-tu.*') ? 'true' : 'false' }}"
                style="cursor: pointer;">
                <i class="fa-solid fa-file-invoice me-2"></i> Administrasi & TU
                <i class="fas fa-chevron-down float-end ms-1" style="font-size: 0.7em; margin-top: 5px;"></i>
            </a>
            <div class="collapse {{ request()->routeIs('administrasi-tu.*') ? 'show' : '' }}" id="adminTuMenu">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                    @if (Auth::user()->role === 'admin')
                        <li>
                            <a href="{{ route('administrasi-tu.create') }}" class="nav-link {{ request()->routeIs('administrasi-tu.create') ? 'active' : '' }}">
                                <i class="fas fa-plus fa-xs me-1"></i> Buat Lap. Puskesmas
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('administrasi-tu.create.labkesda') }}" class="nav-link {{ request()->routeIs('administrasi-tu.create.labkesda') ? 'active' : '' }}">
                                <i class="fas fa-plus fa-xs me-1"></i> Buat Lap. Labkesda
                            </a>
                        </li>
                    @elseif (Auth::user()->role === 'labkesda')
                        <li>
                            <a href="{{ route('administrasi-tu.create.labkesda', ['tahun' => date('Y')]) }}" class="nav-link {{ request()->routeIs('administrasi-tu.create.labkesda') ? 'active' : '' }}">
                                <i class="fas fa-plus fa-xs me-1"></i> Buat Laporan Saya
                            </a>
                        </li>
                    @elseif (Auth::user()->role === 'puskesmas')
                        <li>
                            <a href="{{ route('administrasi-tu.create', ['puskesmas' => Auth::user()->puskesmas_name, 'tahun' => date('Y')]) }}" class="nav-link {{ request()->routeIs('administrasi-tu.create') ? 'active' : '' }}">
                                <i class="fas fa-plus fa-xs me-1"></i> Buat Laporan Saya
                            </a>
                        </li>
                    @endif
                    
                    @if (Auth::user()->role === 'admin')
                        <li>
                            <a href="{{ route('administrasi-tu.index') }}" class="nav-link {{ request()->routeIs('administrasi-tu.index') && !request()->input('puskesmas') ? 'active' : '' }}">
                                <i class="fas fa-list fa-xs me-1"></i> Lihat Semua Data
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('administrasi-tu.index', ['puskesmas' => Auth::user()->puskesmas_name, 'tahun' => date('Y'), 'jenis_laporan' => Auth::user()->role]) }}" class="nav-link {{ request()->routeIs('administrasi-tu.index') && request()->input('puskesmas') ? 'active' : '' }}">
                                <i class="fas fa-list fa-xs me-1"></i> Lihat Data Saya
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </li>
    </ul>

    <hr class="bg-white opacity-25 my-2">

    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('profil') }}"
                class="nav-link {{ request()->is('profil') ? 'active' : '' }}">
                <i class="fa-solid fa-user me-2"></i> Profil Pengguna
            </a>
        </li>

        @if(Auth::user()->role === 'admin')
            <li class="nav-item {{ Request::routeIs('target.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('target.create') }}">
                    <i class="fas fa-fw fa-bullseye"></i>
                    <span>Manajemen Target</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('manajemen-user.index') }}">
                    <i class="fas fa-users-cog me-2"></i>
                    <span>Manajemen User</span>
                </a>
            </li>
        @endif

        <li class="nav-item">
            <a class="nav-link text-white opacity-75 hover-danger" href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                style="cursor: pointer;">
                <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
            </a>
            
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
    </ul>
    @endauth
</nav>