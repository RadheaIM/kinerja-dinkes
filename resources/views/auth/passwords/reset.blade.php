@extends('layouts.app')

{{-- Menyembunyikan sidebar agar tampilan full screen seperti login --}}
@section('hideSidebar', true)

@section('title', 'Reset Password - Kinerja Dinkes')

@section('content')
<style>
    /* Custom CSS (Disamakan dengan Login) */
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
        
        {{-- Header Logo & Judul --}}
        <div class="text-center mb-4">
            <img src="{{ asset('img/logo_dinkes.png') }}" alt="Logo" class="mb-3 rounded-circle border border-primary border-4" width="80">
            <h4 class="text-primary fw-bold">RESET PASSWORD</h4>
            <p class="text-muted mb-0">Silakan buat kata sandi baru Anda</p>
        </div>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            {{-- Token Reset Password (Wajib Ada) --}}
            <input type="hidden" name="token" value="{{ $token }}">

            {{-- Email Address --}}
            <div class="mb-3">
                <label for="email" class="form-label text-muted">{{ __('Email Address') }}</label>
                <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" 
                       name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus readonly>
                @error('email')
                    <div class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>

            {{-- Password Baru --}}
            <div class="mb-3">
                <label for="password" class="form-label text-muted">{{ __('Password Baru') }}</label>
                <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" 
                       name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter">
                @error('password')
                    <div class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>

            {{-- Konfirmasi Password --}}
            <div class="mb-4">
                <label for="password-confirm" class="form-label text-muted">{{ __('Konfirmasi Password') }}</label>
                <input id="password-confirm" type="password" class="form-control form-control-lg" 
                       name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password baru">
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="btn btn-login btn-primary w-100 py-2 fw-bold mb-3">
                <i class="fas fa-key me-2"></i> {{ __('Ubah Password') }}
            </button>
            
            {{-- Tombol Kembali ke Login --}}
            <div class="text-center">
                 <a class="text-decoration-none text-muted small" href="{{ route('login') }}">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Login
                </a>
            </div>

        </form>
    </div>
</div>
@endsection