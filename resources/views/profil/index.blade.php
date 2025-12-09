@extends('layouts.app')

@section('title', 'Profil Pengguna')
@section('page_title', 'Profil Pengguna')

@section('content')
<style>
    /* Style Khusus untuk menyamakan nuansa dengan Login */
    .profile-card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .profile-header {
        background: linear-gradient(135deg, #3b82f6, #1e3a8a); /* Gradient Biru-Navy */
        padding: 2rem;
        text-align: center;
        color: white;
    }
    .profile-avatar {
        width: 100px;
        height: 100px;
        background-color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 3rem;
        color: #3b82f6;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    .profile-info-label {
        font-size: 0.9rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }
    .profile-info-value {
        font-size: 1.1rem;
        font-weight: 500;
        color: #333;
        margin-bottom: 1.5rem;
    }
</style>

<div class="container-fluid">
    
    {{-- Menampilkan pesan sukses --}}
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card profile-card">
                {{-- Header dengan Gradient --}}
                <div class="profile-header">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ Auth::user()->name }}</h4>
                    <p class="mb-0 opacity-75">{{ Auth::user()->email }}</p>
                </div>

                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <span class="badge bg-primary px-3 py-2 rounded-pill">
                            {{ Str::upper(Auth::user()->role) }}
                        </span>
                    </div>

                    {{-- Detail Informasi --}}
                    <div class="row">
                        <div class="col-12 text-center">
                            <div class="profile-info-label">Nama Lengkap</div>
                            <div class="profile-info-value">{{ Auth::user()->name }}</div>
                        </div>
                        <div class="col-12 text-center">
                            <div class="profile-info-label">Email Terdaftar</div>
                            <div class="profile-info-value">{{ Auth::user()->email }}</div>
                        </div>
                        <div class="col-12 text-center">
                            <div class="profile-info-label">Role Akun</div>
                            <div class="profile-info-value">{{ Str::ucfirst(Auth::user()->role) }}</div>
                        </div>
                    </div>

                    {{-- Tombol Edit --}}
                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('profil.edit') }}" class="btn btn-outline-primary py-2 fw-bold" style="border-radius: 0.5rem;">
                            <i class="fas fa-edit me-2"></i> Ubah Profil & Password
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection