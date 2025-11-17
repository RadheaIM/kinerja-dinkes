@extends('layouts.app')

@section('title', 'Edit Data Sasaran Puskesmas')
@section('page_title', 'Edit Data Sasaran Puskesmas')

@section('content')
<div class="container">
    <h4 class="mb-4">Edit Data Sasaran Puskesmas</h4>

    {{-- 
      PERUBAHAN PENTING:
      1. 'action' mengarah ke route 'update' dengan ID ($item->id)
      2. Method adalah 'POST' tapi kita tambahkan @method('PUT') untuk Laravel
    --}}
    <form action="{{ route('laporan-puskesmas.update', $item->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label>Puskesmas</label>
                {{-- PERUBAHAN: Menambahkan 'value' untuk menampilkan data yang ada --}}
                <input type="text" name="puskesmas" class="form-control" value="{{ $item->puskesmas }}" required>
            </div>
            <div class="col-md-3">
                <label>Bumil</label>
                <input type="number" name="bumil" class="form-control" value="{{ $item->bumil }}" required>
            </div>
            <div class="col-md-3">
                <label>Bulin</label>
                <input type="number" name="bulin" class="form-control" value="{{ $item->bulin }}" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <label>BBL</label>
                <input type="number" name="bbl" class="form-control" value="{{ $item->bbl }}" required>
            </div>
            <div class="col-md-3">
                <label>Balita</label>
                <input type="text" name="balita" class="form-control" value="{{ $item->balita }}" placeholder="contoh: 400/420">
            </div>
            <div class="col-md-3">
                <label>Pendidikan Dasar</label>
                <input type="number" name="pendidikan_dasar" class="form-control" value="{{ $item->pendidikan_dasar }}">
            </div>
            <div class="col-md-3">
                <label>Uspro</label>
                <input type="number" name="uspro" class="form-control" value="{{ $item->uspro }}">
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-3">
                <label>Lansia</label>
                <input type="number" name="lansia" class="form-control" value="{{ $item->lansia }}" required>
            </div>
            <div class="col-md-3">
                <label>Hipertensi</label>
                <input type="text" name="hipertensi" class="form-control" value="{{ $item->hipertensi }}" placeholder="contoh: 400/420">
            </div>
            <div class="col-md-3">
                <label>DM</label>
                <input type="number" name="dm" class="form-control" value="{{ $item->dm }}">
            </div>
            <div class="col-md-3">
                <label>Odgj Berat</label>
                {{-- 
                  CATATAN: 
                  Pastikan 'name' di sini (odgj_berat) sesuai dengan validasi di Controller.
                  Form "Tambah Data" Anda sebelumnya mungkin menggunakan 'odgj berat' (dengan spasi), 
                  tapi 'odgj_berat' (dengan underscore) adalah yang benar.
                --}}
                <input type="number" name="odgj_berat" class="form-control" value="{{ $item->odgj_berat }}">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <label>Tb</label>
                <input type="number" name="tb" class="form-control" value="{{ $item->tb }}" required>
            </div>
            <div class="col-md-3">
                <label>Hiv</label>
                <input type="text" name="hiv" class="form-control" value="{{ $item->hiv }}" placeholder="contoh: 400/420">
            </div>
            <div class="col-md-3">
                <label>Idl</label>
                <input type="number" name="idl" class="form-control" value="{{ $item->idl }}">
            </div>
        </div>

        {{-- PERUBAHAN: Teks tombol diubah --}}
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('laporan-puskesmas.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection