@extends('layouts.app')

@section('title', 'Tambah Data Sasaran Puskesmas')
@section('page_title', 'Tambah Data Sasaran Puskesmas')

@section('content')
<div class="container">
    <h4 class="mb-4">Tambah Data Sasaran Puskesmas</h4>

    <form action="{{ route('laporan-puskesmas.store') }}" method="POST">
        @csrf
        <div class="row mb-3">
            <div class="col-md-6">
                <label>Puskesmas</label>
                <input type="text" name="puskesmas" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label>Bumil</label>
                <input type="number" name="bumil" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label>Bulin</label>
                <input type="number" name="bulin" class="form-control" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <label>BBL</label>
                <input type="number" name="bbl" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label>Balita</label>
                <input type="text" name="balita" class="form-control" placeholder="contoh: 400/420">
            </div>
            <div class="col-md-3">
                <label>Pendidikan Dasar</label>
                <input type="number" name="pendidikan_dasar" class="form-control">
            </div>
            <div class="col-md-3">
                <label>Uspro</label>
                <input type="number" name="uspro" class="form-control">
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-3">
                <label>Lansia</label>
                <input type="number" name="lansia" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label>Hipertensi</label>
                <input type="text" name="hipertensi" class="form-control" placeholder="contoh: 400/420">
            </div>
            <div class="col-md-3">
                <label>DM</label>
                <input type="number" name="dm" class="form-control">
            </div>
            <div class="col-md-3">
                <label>Odgj Berat</label>
                <input type="number" name="odgj berat" class="form-control">
            </div>
       

         <div class="row mb-3">
            <div class="col-md-3">
                <label>Tb</label>
                <input type="number" name="tb" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label>Hiv</label>
                <input type="text" name="hiv" class="form-control" placeholder="contoh: 400/420">
            </div>
            <div class="col-md-3">
                <label>Idl</label>
                <input type="number" name="idl" class="form-control">
            </div>
             </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('laporan-puskesmas.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
