<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Kinerja Dinkes') | Kinerja Dinkes</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    {{-- Font Google --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Muat CSS yang sudah di-compile oleh Vite --}}
    @vite(['resources/css/app.css'])

    <style>
        /* Tentukan tinggi topbar */
        :root {
            --topbar-height: 70px;
            --sidebar-width: 240px;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', sans-serif;
            overflow-x: hidden; 
            padding-top: @hasSection('hideSidebar') 0px @else var(--topbar-height) @endif; 
        }
        
        /* ---------------------------------------------------- */
        /* === SIDEBAR STYLE === */
        /* ---------------------------------------------------- */
        .sidebar {
            background: linear-gradient(180deg, #3b82f6, #1e3a8a);
            color:white;
            width:var(--sidebar-width);
            padding: 1rem 0.75rem; /* Padding dikurangi sedikit agar compact */
            flex-shrink: 0;
            transition: margin-left 0.3s ease-in-out; 
            position: fixed; 
            top: var(--topbar-height); 
            bottom: 0;
            z-index: 999;
            overflow-y: auto; 
        }

        /* --- COMPACT SIDEBAR STYLES (Agar Muat Banyak Menu) --- */
        .sidebar .nav-link {
            font-size: 0.9rem !important; 	/* Perkecil font menu */
            padding-top: 0.5rem !important; 	/* Kurangi padding atas */
            padding-bottom: 0.5rem !important; 	/* Kurangi padding bawah */
        }
        
        .sidebar .nav-item {
            margin-bottom: 0.25rem !important; 	/* Kurangi jarak antar menu */
        }

        .sidebar .sidebar-header-text {
            font-size: 0.85rem !important; 	
            line-height: 1.2;
        }

        .sidebar .sidebar-icon-header {
            font-size: 1.2rem !important; 	
            margin-bottom: 0.5rem !important;
        }

        /* Submenu lebih rapat */
        .sidebar .btn-toggle-nav li a {
            padding-top: 0.3rem !important;
            padding-bottom: 0.3rem !important;
            font-size: 0.85rem !important;
        }
        
        /* Scrollbar Style */
        .sidebar::-webkit-scrollbar {
            width: 6px; 
        }
        .sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        /* ---------------------------------------------------- */

        .content-wrapper {
            flex-grow: 1;
            transition: width 0.3s ease-in-out;
            min-height: calc(100vh - var(--topbar-height)); 
            display: flex; 
            flex-direction: column; 
            overflow-x: hidden; 
        }
        
        /* Layout Logic */
        .full-width { width: 100%; }
        .full-width .content-wrapper { min-height: 100vh; }
        .full-width .topbar, .full-width .d-md-flex { display: none !important; }

        @media (min-width: 768px) {
            .sidebar-toggled .sidebar {
                margin-left: calc(0px - var(--sidebar-width)); 
            }
            .sidebar-toggled .content-wrapper {
                margin-left: 0; 
            }
            {{-- .sidebar-toggled .d-md-flex {
                display: none !important; 
            } --}}
        }
        
        /* Sidebar Active/Hover States */
        .sidebar .nav-link.active {
            font-weight: 600;
            background-color: #ffffff;
            color: #1e3a8a;
            border-radius: 0.375rem;
        }
        .sidebar .nav-link:not(.active) { color: white; }
        .sidebar .nav-link:hover:not(.active) {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 0.375rem;
        }
        .sidebar .btn-toggle-nav a {
            color: rgba(255, 255, 255, 0.85);
            padding-left: 2.2rem !important;
        }
        .sidebar .btn-toggle-nav a.active {
            background-color: #ffffff;
            color: #1e3a8a;
            border-radius: 0.375rem;
        }
        .sidebar .btn-toggle-nav a:hover:not(.active) {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 0.375rem;
        }

        /* Topbar & Content */
        .topbar {
            background-color: #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            padding: 10px 25px;
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            justify-content: space-between; 
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
        }

        /* --- STYLE BARU UNTUK TOMBOL TOGGLE (STYLE TOMBOL) --- */
        .btn-toggle-custom {
            background-color: #eff6ff; /* Latar biru muda */
            color: #3b82f6; /* Ikon biru */
            border-radius: 12px; /* Sudut tumpul modern */
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .btn-toggle-custom:hover {
            background-color: #dbeafe; /* Lebih gelap saat hover */
            color: #1e3a8a;
            transform: scale(1.05); /* Sedikit membesar */
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        /* ----------------------------------------------------------- */
        
        .main-content {
            padding: 25px; 
            flex-grow: 1; 
            margin-left: @hasSection('hideSidebar') 0px @else var(--sidebar-width) @endif; 
            transition: margin-left 0.3s ease-in-out;
        }
        
        @media (min-width: 768px) {
             .sidebar-toggled .main-content { margin-left: 0; }
        }

        /* ===== START PERBAIKAN RESPONSIVE MOBILE (Garis Putih & Margin) ===== */
        @media (max-width: 767.98px) {
            /* Pastikan main-content tidak ada margin kiri di layar kecil */
            .main-content {
                margin-left: 0 !important;
                padding-top: 10px; 
            }
        }
        .offcanvas-body { 
            padding: 0 !important; /* <--- Hapus padding offcanvas body (Fix Garis Putih) */
            overflow-y: auto; 
        }
        .offcanvas-body .sidebar { 
            width: 100%; /* <--- Pastikan sidebar memenuhi body offcanvas */
        }
        /* ===== END PERBAIKAN RESPONSIVE MOBILE ===== */

        .user-info { display: flex; align-items: center; gap: 10px; }
        .user-info img { border: 2px solid #e2e8f0; width: 35px; height: 35px; }
        .user-info span { font-weight: 500; color: #5a5c69; font-size: 0.9rem; }
        
        footer {
            font-size: 13px;
            padding: 15px 0;
            background-color: #ffffff;
            border-top: 1px solid #e3e6f0;
            text-align: center;
            color: #858796;
            margin-top: auto;
        }

        .offcanvas-start { width: var(--sidebar-width); }
        .offcanvas-body { padding: 0; overflow-y: auto; }
        .offcanvas-body .sidebar { min-height: 100%; position: relative; top: 0; }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased @if(View::hasSection('hideSidebar')) full-width @endif">

    @unless(View::hasSection('hideSidebar'))
    <div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="sidebarMobile" aria-labelledby="sidebarMobileLabel" data-bs-scroll="true">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="sidebarMobileLabel">Menu Navigasi</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            @include('layouts.sidebar')
        </div>
    </div>
    @endunless

    <div id="wrapper">
        @unless(View::hasSection('hideSidebar'))
        <div class="d-none d-md-flex">
            @include('layouts.sidebar')
        </div>
        @endunless

        <div class="content-wrapper">
            @unless(View::hasSection('hideSidebar'))
            <nav class="topbar">
                <div class="d-flex align-items-center">
                    {{-- TOMBOL TOGGLE DESKTOP (GANTI IKON JADI LAYOUT/GRID) --}}
                    {{-- Menggunakan class btn-toggle-custom dan icon fa-table-columns (lebih elegan dari garis 3) --}}
                    <button class="btn-toggle-custom d-none d-md-flex" id="sidebarToggle" type="button" title="Sembunyikan Sidebar">
                        <i class="fa-solid fa-table-columns fa-lg"></i>
                    </button>

                    {{-- TOMBOL TOGGLE MOBILE (GANTI IKON JADI LAYOUT/GRID) --}}
                    <button class="btn-toggle-custom d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMobile" aria-controls="sidebarMobile">
                        <i class="fa-solid fa-table-columns fa-lg"></i>
                    </button>
                </div>

                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle user-info" href="#" id="userDropdown" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="d-none d-lg-inline">{{ Auth::user()->name ?? 'Guest' }}</span>
                            <img src="https://cdn-icons-png.flaticon.com/512/9131/9131529.png" class="rounded-circle">
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in mt-2" aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="{{ route('profil') }}">
                                <i class="fas fa-user fa-sm fa-fw me-2"></i> Profil Pengguna
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw me-2"></i> Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
            @endunless

            <div class="main-content">
                @yield('content')
            </div>

            <footer>
                Hak Cipta &copy; {{ date('Y') }} Dinas Kesehatan Kabupaten Garut
            </footer>
        </div>
    </div>

    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Apakah Anda yakin ingin keluar dari sesi ini?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
                    <form id="logout-form-modal" action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    
    {{-- 1. JQUERY (Untuk compatibility, biasanya harus di atas) --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    
    {{-- 2. BOOTSTRAP JS (PENTING! UNTUK DROPDOWN, MODAL, COLLAPSE) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    {{-- 3. APP JS dari Vite --}}
    @vite(['resources/js/app.js']) 
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.getElementById('sidebarToggle');
            const body = document.querySelector('body');
            
            if (localStorage.getItem('sidebarStatus') === 'toggled') {
                body.classList.add('sidebar-toggled');
            }

            if (toggleButton) {
                toggleButton.addEventListener('click', function() {
                    body.classList.toggle('sidebar-toggled');
                    if (body.classList.contains('sidebar-toggled')) {
                        localStorage.setItem('sidebarStatus', 'toggled');
                    } else {
                        localStorage.removeItem('sidebarStatus');
                    }
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>