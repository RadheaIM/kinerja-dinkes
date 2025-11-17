@extends('layouts.app')

@section('title', 'Buat Laporan Kinerja Labkesda')
@section('page_title', 'Laporan Kinerja - Capaian Labkesda')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            {{-- Notifikasi Error --}}
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
                <input type="hidden" name="puskesmas_name" value="{{ $labkesdaName }}">
                {{-- Labkesda tidak punya target sasaran, jadi kirim null --}}
                @foreach ($indicators as $index => $indicatorName)
                     <input type="hidden" name="details[{{ $index }}][target_sasaran]" value="">
                @endforeach


                {{-- Header Form --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Informasi Laporan</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label>Nama Unit</label>
                                    <input type="text" class="form-control" value="{{ $labkesdaName }}" readonly disabled>
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
                        <h6 class="m-0 font-weight-bold text-primary">Detail Capaian Labkesda</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                <thead class="bg-light text-center align-middle">
                                    <tr>
                                        <th rowspan="2">Indikator</th>
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
                                        @php
                                            $trimmedName = trim($indicatorName);
                                            $isMainTitle = !Str::contains($trimmedName, ['.', '-']);
                                            $isSubSection = preg_match('/^[a-z]\./', $trimmedName);
                                        @endphp
                                    <tr class="{{ ($isMainTitle || $isSubSection) ? 'table-secondary fw-bold' : '' }}">
                                        <td>
                                            {{-- Logika Indentasi --}}
                                            @if (Str::startsWith($trimmedName, '   -'))
                                                <span class="ms-5">{{ Str::after($trimmedName, '-') }}</span>
                                            @elseif (Str::startsWith($trimmedName, '  -'))
                                                <span class="ms-4">{{ Str::after($trimmedName, '-') }}</span>
                                            @elseif (Str::startsWith($trimmedName, ' -'))
                                                <span class="ms-3">{{ Str::after($trimmedName, '-') }}</span>
                                            @elseif (preg_match('/^\d+\./', $trimmedName))
                                                <span class="ms-3">{{ $trimmedName }}</span>
                                            @elseif ($isSubSection)
                                                <span class="ms-2">{{ $trimmedName }}</span>
                                            @else
                                                {{ $trimmedName }}
                                            @endif
                                            
                                            <input type="hidden" name="details[{{ $index }}][indikator_name]" value="{{ $indicatorName }}">
                                        </td>
                                        
                                        @for ($i = 1; $i <= 12; $i++)
                                        <td>
                                            {{-- Jangan tampilkan input untuk judul/seksi --}}
                                            @if (!$isMainTitle && !$isSubSection)
                                                <input type="number" 
                                                       name="details[{{ $index }}][bln_{{ $i }}]" 
                                                       class="form-control form-control-sm @error('details.'.$index.'.bln_'.$i) is-invalid @enderror" 
                                                       value="{{ old('details.'.$index.'.bln_'.$i, 0) }}"
                                                       min="0">
                                            @else
                                                <input type="hidden" name="details[{{ $index }}][bln_{{ $i }}]" value="0">
                                            @endif
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
                    {{-- === INI PERBAIKANNYA ===
                         Tombol Batal sekarang mengarah ke 'user.index' (Lihat Data Saya)
                         atau 'admin.index' (Lihat Semua Data) tergantung role.
                    --}}
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
</div>
@endsection