@extends('layouts.app')

{{-- KUNCI: Memberi tahu layout untuk menyembunyikan sidebar dan topbar --}}
@section('hideSidebar', true)

@section('title', 'Daftar - Kinerja Dinkes')

@section('content')
<style>
    /* Custom CSS untuk halaman Register Kartu Tunggal */
    .register-container {
        /* Mengisi seluruh tinggi layar dan memposisikan konten di tengah */
        min-height: 100vh;
        background: linear-gradient(135deg, #3b82f6, #1e3a8a); /* Gradien Biru-Navy */
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 2rem 1rem;
    }
    .register-card {
        background: #ffffff;
        border-radius: 1rem;
        box-shadow: 0 15px 45px rgba(0, 0, 0, 0.4); /* Shadow yang lebih dalam dan mengapung */
        overflow: hidden;
        max-width: 480px; /* Batas lebar kartu tunggal */
        width: 90%; /* Responsif */
        z-index: 10;
        padding: 2.5rem;
    }
    .btn-register {
        background-color: #3b82f6;
        border-color: #3b82f6;
        transition: background-color 0.3s;
    }
    .btn-register:hover {
        background-color: #1e3a8a;
        border-color: #1e3a8a;
    }
</style>

<div class="register-container">
    {{-- Kartu Tunggal, Terpusat Sempurna --}}
    <div class="register-card">
        
        <div class="text-center mb-5">
            {{-- Logo Placeholder --}}
            <img src="{{ asset('img/logo_dinkes.png') }}" alt="Logo" class="mb-3 rounded-circle border border-primary border-4">
            <h4 class="text-primary fw-bold">DAFTAR AKUN </h4>
            <p class="text-muted mb-0">Sistem Informasi Kinerja Puskesmas & Labkesda Kabupaten Garut</p>
        </div>
        
        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- Nama Field --}}
            <div class="mb-3">
                <label for="name" class="form-label text-muted">{{ __('Nama Lengkap') }}</label>
                <input id="name" type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" 
                       name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Nama Lengkap Unit Anda">
                @error('name')
                    <div class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>
            
            {{-- Email Field --}}
            <div class="mb-3">
                <label for="email" class="form-label text-muted">{{ __('Alamat Email') }}</label>
                <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" 
                       name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="contoh@gmail.com  ">
                @error('email')
                    <div class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>

            {{-- Password Field --}}
            <div class="mb-3">
                <label for="password" class="form-label text-muted">{{ __('Password') }}</label>
                <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" 
                       name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter">
                @error('password')
                    <div class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>

            {{-- Confirm Password Field --}}
            <div class="mb-4">
                <label for="password-confirm" class="form-label text-muted">{{ __('Konfirmasi Password') }}</label>
                <input id="password-confirm" type="password" class="form-control form-control-lg" 
                       name="password_confirmation" required autocomplete="new-password" placeholder="Ketik ulang password">
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="btn btn-register btn-primary w-100 py-2 fw-bold mb-4">
                <i class="fas fa-user-plus me-2"></i> {{ __('Daftar Akun') }}
            </button>
        </form>
        
        <hr class="my-4">
        
        {{-- Link Login --}}
        <div class="text-center">
            <p class="text-muted mb-2" style="font-size: 0.9rem;">Sudah punya akun?</p>
            <a class="btn btn-outline-primary w-100" href="{{ route('login') }}">
                Masuk ke Akun Anda
            </a>
        </div>
    </div>
</div>
@endsection