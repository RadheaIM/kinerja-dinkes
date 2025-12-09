@extends('layouts.app')

@section('title', 'Ubah Profil')
@section('page_title', 'Ubah Profil Pengguna')

@section('content')
<style>
    .edit-profile-card {
        border: none;
        border-radius: 0.8rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08); /* Shadow lebih halus */
    }
    .form-label {
        font-weight: 500;
        color: #555;
        font-size: 0.9rem; /* Ukuran font label diperkecil */
        margin-bottom: 0.3rem;
    }
    .section-title {
        color: #3b82f6;
        font-weight: bold;
        border-bottom: 2px solid #eef2f7;
        padding-bottom: 0.3rem;
        margin-bottom: 1rem;
        font-size: 1rem; /* Judul section lebih kecil */
    }
    .input-group-text {
        font-size: 0.9rem;
    }
    .form-control {
        font-size: 0.95rem; /* Ukuran font input pas */
        padding: 0.5rem 0.75rem; /* Padding input standar */
    }
</style>

<div class="container-fluid">
    <div class="row justify-content-center">
        {{-- Lebar kolom diperkecil (col-md-7 col-lg-5) agar kartu tidak terlalu lebar --}}
        <div class="col-md-7 col-lg-5">
            <div class="card edit-profile-card">
                {{-- Padding card diperkecil (p-3 p-md-4) --}}
                <div class="card-body p-3 p-md-4">
                    
                    <div class="d-flex align-items-center mb-3">
                        <a href="{{ route('profil') }}" class="text-decoration-none text-muted me-3">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h5 class="m-0 fw-bold text-primary">Edit Profil</h5>
                    </div>

                    {{-- Notifikasi Error --}}
                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 py-2 px-3 mb-3">
                            <ul class="mb-0 ps-3 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profil.update') }}">
                        @csrf
                        @method('PUT')

                        {{-- Bagian 1: Informasi Dasar --}}
                        <div class="mb-3">
                            <h6 class="section-title">Informasi Dasar</h6>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                {{-- Hapus form-control-lg --}}
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus>
                                @error('name')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Alamat Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Bagian 2: Keamanan --}}
                        <div class="mb-3">
                            <h6 class="section-title">Keamanan (Ubah Password)</h6>
                            <p class="text-muted small mb-2" style="font-size: 0.8rem;">Kosongkan jika tidak ingin mengubah password.</p>

                            <div class="mb-3">
                                <label for="current_password" class="form-label">Password Saat Ini</label>
                                <div class="input-group input-group-sm"> {{-- input-group-sm opsional, tapi standar sudah cukup --}}
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted small"></i></span>
                                    <input type="password" class="form-control border-start-0 @error('current_password') is-invalid @enderror" 
                                           id="current_password" name="current_password" placeholder="Password lama">
                                    @error('current_password')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label">Password Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-key text-muted small"></i></span>
                                    <input type="password" class="form-control border-start-0 @error('new_password') is-invalid @enderror" 
                                           id="new_password" name="new_password" placeholder="Min 8 karakter">
                                    @error('new_password')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label">Konfirmasi Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-check-double text-muted small"></i></span>
                                    <input type="password" class="form-control border-start-0" 
                                           id="new_password_confirmation" name="new_password_confirmation" placeholder="Ulangi password">
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Aksi (Ukuran Normal) --}}
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ route('profil') }}" class="btn btn-light px-4 fw-bold me-md-2" style="border-radius: 0.5rem; font-size: 0.9rem;">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold" style="border-radius: 0.5rem; background: linear-gradient(135deg, #3b82f6, #1e3a8a); border:none; font-size: 0.9rem;">
                                <i class="fas fa-save me-2"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection