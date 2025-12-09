@extends('layouts.app')

@section('title', 'Edit Laporan Kinerja Puskesmas')
@section('page_title', 'Edit Laporan Kinerja - Capaian Program Puskesmas')

@section('content')

{{-- Notifikasi Error Validasi --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Whoops! Ada beberapa masalah dengan input Anda.</strong><br><br>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Notifikasi Error dari Controller --}}
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
 @if (session('info'))
    <div class="alert alert-info">{{ session('info') }}</div>
@endif

<form action="{{ route('laporan-kinerja.update', $laporan->id) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="jenis_laporan" value="{{ $laporan->jenis_laporan }}">
    {{-- Nama Puskesmas dan Tahun sekarang dikirim via form, bukan hidden --}}

    {{-- Bagian Header Laporan (Readonly) --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Laporan</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group mb-3">
                        <label for="puskesmas_name_disabled">Nama Puskesmas</label>
                         {{-- Input Teks Readonly --}}
                        <input type="text" id="puskesmas_name_disabled"
                                class="form-control"
                                value="{{ $laporan->puskesmas_name }}" readonly disabled>
                         {{-- Hidden input untuk mengirim nilai --}}
                        <input type="hidden" name="puskesmas_name" value="{{ $laporan->puskesmas_name }}">
                        @error('puskesmas_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="tahun_disabled">Tahun</label>
                         {{-- Input Teks Readonly --}}
                        <input type="number" id="tahun_disabled"
                                class="form-control"
                                value="{{ $laporan->tahun }}" readonly disabled>
                        {{-- Hidden input untuk mengirim nilai --}}
                        <input type="hidden" name="tahun" value="{{ $laporan->tahun }}">
                        @error('tahun')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bagian Detail Indikator --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detail Capaian Program</h6>
        </div>
        <div class="card-body">
            {{-- Peringatan jika data master target kosong --}}
            @if(isset($savedTargets) && empty($savedTargets))
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> Data target tidak ditemukan di database master untuk unit <strong>{{ $laporan->puskesmas_name }}</strong> tahun <strong>{{ $laporan->tahun }}</strong>. Kolom target akan bernilai 0.
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="capaianTable" style="min-width: 1500px;">
                    <thead class="bg-light text-center">
                        <tr>
                            <th style="width: 25%; vertical-align: middle;">Indikator Capaian</th>
                            <th style="width: 10%; vertical-align: middle;">Target Sasaran</th>
                            <th style="width: 5%;">Jan</th>
                            <th style="width: 5%;">Feb</th>
                            <th style="width: 5%;">Mar</th>
                            <th style="width: 5%;">Apr</th>
                            <th style="width: 5%;">Mei</th>
                            <th style="width: 5%;">Jun</th>
                            <th style="width: 5%;">Jul</th>
                            <th style="width: 5%;">Ags</th>
                            <th style="width: 5%;">Sep</th>
                            <th style="width: 5%;">Okt</th>
                            <th style="width: 5%;">Nov</th>
                            <th style="width: 5%;">Des</th>
                        </tr>
                    </thead>
                    <tbody>
                         @php
                            // Buat collection dari details agar mudah dicari by indikator_name
                            $details = $laporan->details->keyBy('indikator_name');
                         @endphp
                         @isset($indicators)
                            @foreach ($indicators as $index => $indicatorName)
                                @php
                                    // Cari detail yang cocok berdasarkan nama indikator
                                    $detail = $details->get($indicatorName);

                                    // --- LOGIKA PENENTUAN TARGET (LOCKED & AUTO-FILL) ---
                                    $targetVal = 0;
                                    
                                    // 1. Prioritas: Ambil dari Master Data ($savedTargets) yang dikirim Controller
                                    if (isset($savedTargets) && !empty($savedTargets)) {
                                        if (isset($savedTargets[$indicatorName])) {
                                            $targetVal = $savedTargets[$indicatorName];
                                        } 
                                        // Fuzzy search sederhana jika key tidak persis sama
                                        else {
                                             foreach($savedTargets as $key => $val) {
                                                 // Cek jika nama indikator mengandung key atau sebaliknya (case insensitive)
                                                 if (str_contains(strtolower($indicatorName), strtolower($key)) || str_contains(strtolower($key), strtolower($indicatorName))) {
                                                     $targetVal = $val;
                                                     break;
                                                 }
                                             }
                                        }
                                    } 
                                    // 2. Fallback: Jika Master kosong, pakai data lama yang tersimpan di database Laporan
                                    elseif ($detail) {
                                        $targetVal = $detail->target_sasaran;
                                    }
                                @endphp
                                <tr>
                                    <td style="vertical-align: middle;">
                                        {{ $indicatorName }}
                                        {{-- Simpan ID detail jika ada, penting untuk proses update --}}
                                        <input type="hidden" name="details[{{ $index }}][id]" value="{{ $detail ? $detail->id : '' }}">
                                        <input type="hidden" name="details[{{ $index }}][indikator_name]" value="{{ $indicatorName }}">
                                    </td>
                                    <td>
                                        {{-- INPUT TARGET DIKUNCI (READONLY) --}}
                                        {{-- Style background abu-abu ditambahkan agar terlihat 'disabled' --}}
                                        <input type="number" 
                                               class="form-control form-control-sm text-center bg-gray-200" 
                                               style="background-color: #eaecf4; color: #6e707e; font-weight: bold; cursor: not-allowed;"
                                               name="details[{{ $index }}][target_sasaran]"
                                               value="{{ $targetVal }}"
                                               readonly
                                               tabindex="-1">
                                    </td>

                                    {{-- Loop untuk 12 bulan --}}
                                    @for ($i = 1; $i <= 12; $i++)
                                    <td>
                                        <input type="number" class="form-control form-control-sm text-center @error('details.'.$index.'.bln_'.$i) is-invalid @enderror"
                                               name="details[{{ $index }}][bln_{{ $i }}]"
                                               value="{{ old('details.'.$index.'.bln_'.$i, $detail ? $detail['bln_'.$i] : 0) }}"
                                               placeholder="0"
                                               min="0">
                                    </td>
                                    @endfor
                                </tr>
                            @endforeach
                         @else
                             <tr><td colspan="14" class="text-danger">Error: Daftar indikator tidak ditemukan.</td></tr>
                         @endisset
                     </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Bagian Keterangan Tambahan --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Keterangan Tambahan</h6>
        </div>
        <div class="card-body">
            <div class="form-group">
                <textarea name="keterangan" id="keterangan" rows="4" class="form-control @error('keterangan') is-invalid @enderror" placeholder="Masukkan keterangan tambahan jika ada...">{{ old('keterangan', $laporan->keterangan) }}</textarea>
                @error('keterangan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('rekap.index') }}" class="btn btn-secondary me-2">Batal</a>
        <button type="submit" class="btn btn-primary">Update Laporan</button>
    </div>

</form>
@endsection