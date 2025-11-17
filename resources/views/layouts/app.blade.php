<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Kinerja Dinkes') | Kinerja Dinkes</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    {{-- Font Google (Opsional) --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Muat CSS dan JS yang sudah di-compile oleh Vite (termasuk Bootstrap JS) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <style>
        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', sans-serif;
            /* Mencegah scroll horizontal yang tidak perlu di body */
            overflow-x: hidden; 
        }
        /* Style Sidebar (Pastikan sesuai dengan file sidebar Anda) */
        .sidebar {
            min-height:100vh;
            background: linear-gradient(180deg, #3b82f6, #1e3a8a);
            color:white;
            width:240px;
            padding: 1rem;
            flex-shrink: 0; /* Mencegah sidebar menyusut */
        }
         .sidebar .nav-link.active {
            font-weight: 600;
            background-color: #ffffff;
            color: #1e3a8a;
            border-radius: 0.375rem;
         }
         .sidebar .nav-link:not(.active) {
            color: white;
         }
         .sidebar .nav-link:hover:not(.active) {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 0.375rem;
         }
         .sidebar .btn-toggle-nav a {
            color: rgba(255, 255, 255, 0.85);
            padding-left: 2.5rem !important;
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

        /* Style Topbar dan Konten */
        .topbar {
            background-color: #fff;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
            padding: 10px 25px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between; 
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .user-info img {
            border: 2px solid #e2e8f0;
            width: 40px;
            height: 40px;
        }
        .user-info span {
            font-weight: 500;
            color: #5a5c69;
        }
        
        /* ================================================== */
        /* === PERBAIKAN CSS KONTEN WRAPPER === */
        /* ================================================== */
        .content-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: 100%; /* <-- KEMBALIKAN BARIS INI */
            overflow-x: hidden; /* <-- TAMBAHKAN BARIS INI (PENTING) */
        }
        /* ================================================== */

        .main-content {
            padding: 30px;
            flex-grow: 1;
        }
        footer {
            font-size: 14px;
            padding: 20px 0;
            background-color: #ffffff;
            border-top: 1px solid #e3e6f0;
            text-align: center;
            color: #858796;
            margin-top: auto;
        }
        .pagination { justify-content: center; }
        .page-link { color: #0d6efd; }
        .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; color: white;}
        .page-item.disabled .page-link { color: #868e96; }
        .dropdown-menu {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
            border: 1px solid #e3e6f0;
        }
        .dropdown-item i.fa-fw {
            color: #858796;
            margin-right: 0.5rem;
            width: 1.25em;
            text-align: center;
        }
        .dropdown-item:active {
            background-color: #4e73df;
            color: #fff;
        }
        .dropdown-item:active i.fa-fw {
            color: rgba(255, 255, 255, 0.5);
        }
         .dropdown-toggle.user-info::after {
            display: none;
         }
        .dropdown-divider {
            border-top: 1px solid #e3e6f0;
        }
         .text-gray-800 {
            color: #5a5c69 !important;
         }
         
         .offcanvas-start {
            width: 240px;
         }
         .offcanvas-body {
            padding: 0;
            overflow-y: auto;
         }
         .offcanvas-body .sidebar {
            min-height: 100%;
         }
         
    </style>

    @stack('styles')

</head>
<body class="font-sans antialiased">

    {{-- 1. SIDEBAR UNTUK MOBILE (Offcanvas, tersembunyi by default) --}}
    <div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="sidebarMobile" aria-labelledby="sidebarMobileLabel" data-bs-scroll="true">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="sidebarMobileLabel">Menu Navigasi</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            @include('layouts.sidebar')
        </div>
    </div>

    {{-- 2. WRAPPER UTAMA (Sidebar + Konten) --}}
    <div class="d-flex">

        {{-- 3. SIDEBAR UNTUK DESKTOP (Statis, selalu terlihat) --}}
        <div class="d-none d-md-flex">
            @include('layouts.sidebar')
        </div>


        {{-- 4. KONTEN UTAMA WRAPPER --}}
        <div class="content-wrapper">

            {{-- Topbar (Navbar Atas) --}}
            <nav class="topbar">
                
                {{-- TOMBOL HAMBURGER (HANYA Muncul di Mobile) --}}
                <button class="btn btn-link d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMobile" aria-controls="sidebarMobile">
                    <i class="fas fa-bars fa-lg text-primary"></i>
                </button>

                {{-- User Info Dropdown (Pindah ke kanan otomatis) --}}
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle user-info" href="#" id="userDropdown" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="d-none d-lg-inline">{{ Auth::user()->name ?? 'Guest' }}</span>
                            <img src="https://cdn-icons-png.flaticon.com/512/9131/9131529.png"
                                class="rounded-circle">
                        </a>
                        {{-- Dropdown Menu --}}
                        <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in mt-2" aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="{{ route('profil') }}">
                                <i class="fas fa-user fa-sm fa-fw"></i>
                                Profil Pengguna
                            </a>
                            <div class="dropdown-div    ider"></div>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw"></i>
                                Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>

            {{-- Main Content Area --}}
            <div class="main-content">
                <h3 class="fw-semibold mb-4 text-gray-800">@yield('page_title', 'Halaman')</h3>
                @yield('content')
            </div>

            {{-- Footer --}}
            <footer>
                Hak Cipta &copy; {{ date('Y') }} Dinas Kesehatan Kabupaten Garut
            </footer>
        </div> {{-- End of content-wrapper --}}
    </div> {{-- End of d-flex --}}


    {{-- Logout Modal --}}
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

    {{-- Script JQuery (Penting) --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    {{-- ================================================================== --}}
    {{-- === PERBAIKAN: HAPUS SISA KOMENTAR '-->' YANG RUSAK === --}}
    {{-- ================================================================== --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    {{-- ================================================================== --}}
    
    @stack('scripts')

</body>
</html>