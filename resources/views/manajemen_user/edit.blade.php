@extends('layouts.app')

@section('title', 'Edit User')
@section('page_title', 'Edit User: ' . $user->name) {{-- <-- PERBAIKAN: 'name' --}}

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Informasi Akun</h6>
        </div>
        <div class="card-body">
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops! Ada beberapa masalah:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('manajemen-user.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="name">Nama Lengkap</label> {{-- <-- PERBAIKAN: 'name' --}}
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $user->name) }}" required> {{-- <-- PERBAIKAN: 'name' --}}
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror {{-- <-- PERBAIKAN: 'name' --}}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="password">Password (Kosongkan jika tidak ingin diubah)</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" minlength="8">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                        </div>
                    </div>
                </div>
                
                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="role">Role Pengguna</label>
                            <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="">-- Pilih Role --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role }}" {{ old('role', $user->role) == $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                                @endforeach
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3" id="puskesmas_dropdown_group" style="display: none;">
                            <label for="nama_puskesmas">Nama Puskesmas/Unit</label>
                            <select name="nama_puskesmas" id="nama_puskesmas" class="form-select @error('nama_puskesmas') is-invalid @enderror">
                                <option value="">-- Pilih Unit --</option>
                                @php $currentPuskesmas = old('nama_puskesmas', $user->nama_puskesmas); @endphp
                                <option value="Labkesda" {{ $currentPuskesmas == 'Labkesda' ? 'selected' : '' }}>Labkesda</option>
                                @foreach ($puskesmasNames as $name)
                                    <option value="{{ $name }}" {{ $currentPuskesmas == $name ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('nama_puskesmas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="form-text text-muted">Hanya diisi jika role adalah Puskesmas.</small>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('manajemen-user.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-success">Perbarui User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const puskesmasGroup = document.getElementById('puskesmas_dropdown_group');
        const puskesmasSelect = document.getElementById('nama_puskesmas');

        function togglePuskesmasDropdown() {
            if (roleSelect.value === 'puskesmas') {
                puskesmasGroup.style.display = 'block';
                puskesmasSelect.setAttribute('required', 'required');
            } else {
                puskesmasGroup.style.display = 'none';
                puskesmasSelect.removeAttribute('required');
                // Tidak mengosongkan nilai di sini agar nilai lama (misal 'Labkesda') tetap ada saat edit
            }
        }

        // Jalankan saat load halaman
        togglePuskesmasDropdown(); 
        
        // Jalankan saat ada perubahan
        roleSelect.addEventListener('change', togglePuskesmasDropdown);
    });
</script>
@endpush