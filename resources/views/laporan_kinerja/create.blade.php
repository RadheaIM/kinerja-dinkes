@extends('layouts.app')

@section('title', 'Buat Laporan Kinerja Puskesmas')
@section('page_title', 'Buat Laporan Kinerja - Capaian Program')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

              {{-- Menampilkan Pesan Sukses atau Error (dari session) --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Gagal!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Menampilkan Error Validasi (dari $errors) --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops! Ada beberapa masalah:</strong><br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('laporan-kinerja.store') }}" method="POST">
            @csrf
            <input type="hidden" name="jenis_laporan" value="{{ $jenisLaporan }}">

            {{-- Header Form --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Laporan</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label for="puskesmas_name">Nama Puskesmas</label>

                                @if(Auth::user()->role === 'admin')
                                    {{-- HANYA ADMIN YANG BISA MEMILIH PUSKESMAS --}}
                                    <select name="puskesmas_name" id="puskesmas_name"
                                            class="form-select @error('puskesmas_name') is-invalid @enderror"
                                            required>
                                        <option value="">-- Pilih Puskesmas --</option>
                                        @foreach ($puskesmasNames as $name)
                                            <option value="{{ $name }}" {{ old('puskesmas_name', request('puskesmas')) == $name ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('puskesmas_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @else
                                    {{-- ========================================================== --}}
                                    {{-- === PERBAIKAN DI SINI (puskesmas_name -> nama_puskesmas) === --}}
                                    {{-- ========================================================== --}}
                                    
                                    {{-- USER PUSKESMAS OTOMATIS TERISI DAN DIKUNCI --}}
                                    <input type="text" 
                                           id="puskesmas_name_disabled"
                                           class="form-control"
                                           value="{{ Auth::user()->nama_puskesmas }}"
                                           disabled readonly>
                                    {{-- Ini input tersembunyi yang MENGIRIMKAN data --}}
                                    <input type="hidden" 
                                           name="puskesmas_name" 
                                           id="puskesmas_name" 
                                           value="{{ Auth::user()->nama_puskesmas }}">
                                           
                                    {{-- === AKHIR PERBAIKAN === --}}
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="tahun">Tahun</label>
                                <input type="number" name="tahun" id="tahun"
                                       class="form-control @error('tahun') is-invalid @enderror"
                                       value="{{ old('tahun', request('tahun', date('Y'))) }}"
                                       required min="2020" max="2099">
                                @error('tahun')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Indikator --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Capaian Program</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                            <thead class="bg-light text-center align-middle">
                                <tr>
                                    <th rowspan="2">Indikator</th>
                                    <th rowspan="2">Target Sasaran (1 Tahun)</th>
                                    <th colspan="12">Realisasi Bulanan</th>
                                </tr>
                                <tr>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <th>{{ \Carbon\Carbon::create()->month($i)->locale('id')->shortMonthName }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($indicators as $index => $indicatorName)
                                <tr>
                                    <td>
                                        {{ $indicatorName }}
                                        <input type="hidden" name="details[{{ $index }}][indikator_name]" value="{{ $indicatorName }}">
                                    </td>
                                    <td>
                                        <input type="number" 
                                               name="details[{{ $index }}][target_sasaran]" 
                                               class="form-control form-control-sm @error('details.'.$index.'.target_sasaran') is-invalid @enderror" 
                                               value="{{ old('details.'.$index.'.target_sasaran', 0) }}"
                                               min="0">
                                    </td>
                                    @for ($i = 1; $i <= 12; $i++)
                                    <td>
                                        <input type="number" 
                                               name="details[{{ $index }}][bln_{{ $i }}]" 
                                               class="form-control form-control-sm @error('details.'.$index.'.bln_'.$i) is-invalid @enderror" 
                                               value="{{ old('details.'.$index.'.bln_'.$i, 0) }}"
                                               min="0">
                                    </td>
                                    @endfor
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mb-4">
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('laporan-kinerja.admin.index') }}" class="btn btn-secondary me-2">Batal</a>
                @else
                    <a href="{{ route('laporan-kinerja.user.index') }}" class="btn btn-secondary me-2">Batal</a>
                @endif
                <button type="submit" class="btn btn-primary">Simpan Laporan</button>
            </div>
        </form>

    </div>
</div>

@endsection