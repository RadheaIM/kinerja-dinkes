@extends('layouts.app')

@section('title', 'Profil Pengguna')
@section('page_title', 'Profil Pengguna')

@section('content')

{{-- Menampilkan pesan sukses setelah update --}}
@if (session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Informasi Akun</h6>
    </div>
    <div class="card-body">
        <p><strong>Nama:</strong> {{ Auth::user()->name }}</p>
        <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
        {{-- <p><strong>Username:</strong> {{ Auth::user()->username ?? '-' }}</p> --}} {{-- PERBAIKAN: Baris ini dihapus karena tidak relevan --}}
        <p><strong>Role:</strong> {{ Str::ucfirst(Auth::user()->role) }}</p>
        
            
        
        <a href="{{ route('profil.edit') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-edit"></i> Ubah Profil & Password
        </a>
    </div>
</div>
@endsection