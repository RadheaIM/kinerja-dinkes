@extends('layouts.app')

{{-- KUNCI: Memberi tahu layout untuk menyembunyikan sidebar dan topbar --}}
@section('hideSidebar', true)

@section('title', 'Login - Kinerja Dinkes')

@section('content')
<style>
    /* Custom CSS untuk halaman Login Kartu Tunggal */
    .login-container {
        /* Mengisi seluruh tinggi layar dan memposisikan konten di tengah */
        min-height: 100vh;
        background: linear-gradient(135deg, #3b82f6, #1e3a8a); /* Gradien Biru-Navy */
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 2rem 1rem;
    }
    .login-card {
        background: #ffffff;
        border-radius: 1rem;
        box-shadow: 0 15px 45px rgba(0, 0, 0, 0.4); /* Shadow yang lebih dalam dan mengapung */
        overflow: hidden;
        max-width: 480px; /* Batas lebar kartu tunggal */
        width: 90%; /* Responsif */
        z-index: 10;
        padding: 2.5rem;
    }
    .btn-login {
        background-color: #3b82f6;
        border-color: #3b82f6;
        transition: background-color 0.3s;
    }
    .btn-login:hover {
        background-color: #1e3a8a;
        border-color: #1e3a8a;
    }
</style>

<div class="login-container">
    {{-- Kartu Tunggal, Terpusat Sempurna --}}
    <div class="login-card">
        
        <div class="text-center mb-5">
            {{-- Logo Placeholder --}}
            <img src="{{ asset('img/logo_dinkes.png') }}" alt="Logo" class="mb-3 rounded-circle border border-primary border-4">
            <h4 class="text-primary fw-bold">SISTEM KINERJA PUSKESMAS</h4>
            <p class="text-muted mb-0">Kabupaten Garut</p>
        </div>
        
        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email/Username Field --}}
            <div class="mb-3">
                <label for="email" class="form-label text-muted">{{ __('Email Address') }}</label>
                <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" 
                       name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="contoh@gmail.com">
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
                       name="password" required autocomplete="current-password" placeholder="Masukkan Password Anda">
                @error('password')
                    <div class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>

            {{-- Remember Me & Lupa Password --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label text-muted" for="remember" style="font-size: 0.9rem;">
                        {{ __('Ingat Saya') }}
                    </label>
                </div>
                @if (Route::has('password.request'))
                    <a class="btn btn-link text-primary p-0" href="{{ route('password.request') }}" style="font-size: 0.9rem;">
                        {{ __('Lupa Password?') }}
                    </a>
                @endif
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="btn btn-login btn-primary w-100 py-2 fw-bold mb-4">
                <i class="fas fa-sign-in-alt me-2"></i> {{ __('Masuk ke Dashboard') }}
            </button>
        </form>
        
        {{-- Link Register (Jika Diperlukan) --}}
        @if (Route::has('register'))
            <div class="text-center">
                <p class="text-muted mb-2" style="font-size: 0.9rem;">Belum punya akun?</p>
                <a class="btn btn-outline-primary w-100" href="{{ route('register') }}">
                    Daftar Akun Baru
                </a>
            </div>
        @endif
    </div>
</div>
@endsection