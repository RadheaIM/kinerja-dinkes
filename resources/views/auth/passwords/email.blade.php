@extends('layouts.app')

{{-- KUNCI: Sembunyikan sidebar agar tampilan full screen seperti login --}}
@section('hideSidebar', true)

@section('title', 'Lupa Password - Kinerja Dinkes')

@section('content')
<style>
    /* Custom CSS: Disamakan dengan halaman Login agar konsisten */
    .login-container {
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
        box-shadow: 0 15px 45px rgba(0, 0, 0, 0.4);
        overflow: hidden;
        max-width: 480px;
        width: 90%;
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
    <div class="login-card">
        
        {{-- Header dengan Logo (Tambahan agar terlihat resmi) --}}
        <div class="text-center mb-4">
            {{-- Pastikan path logo sesuai dengan aset Anda --}}
            <img src="{{ asset('img/logo_dinkes.png') }}" alt="Logo" class="mb-3 rounded-circle border border-primary border-4" width="80">
            <h4 class="text-primary fw-bold">LUPA PASSWORD?</h4>
            <p class="text-muted mb-0">Masukkan email untuk mendapatkan link reset.</p>
        </div>

        {{-- Alert Sukses: Muncul jika email reset berhasil dikirim --}}
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-1"></i> {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            {{-- Input Email --}}
            <div class="mb-4">
                <label for="email" class="form-label text-muted">{{ __('Email Address') }}</label>
                <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" 
                       name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="contoh@gmail.com">

                @error('email')
                    <div class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>

            {{-- Tombol Kirim --}}
            <div class="mb-4">
                <button type="submit" class="btn btn-login btn-primary w-100 py-2 fw-bold">
                    <i class="fas fa-paper-plane me-2"></i> {{ __('Kirim Link Reset') }}
                </button>
            </div>

            {{-- Link Kembali ke Login --}}
            <div class="text-center">
                <a class="text-decoration-none text-muted small" href="{{ route('login') }}">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Login
                </a>
            </div>
        </form>
    </div>
</div>
@endsection