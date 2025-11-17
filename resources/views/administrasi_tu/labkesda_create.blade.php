@extends('layouts.app')

@section('title', 'Buat Laporan Administrasi & TU Labkesda')
@section('page_title', 'Buat Laporan Administrasi & Tata Usaha Labkesda')

@section('content')

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
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if (session('info'))
    <div class="alert alert-info">{{ session('info') }}</div>
@endif

<form action="{{ route('administrasi-tu.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    {{-- Input hidden untuk menandai jenis laporan --}}
    <input type="hidden" name="jenis_laporan" value="{{ $jenisLaporan }}">

    {{-- Header Form --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Laporan</h6>
        </div>
        <div class="card-body">
            <div class="row">
                {{-- Nama Unit (fixed Labkesda) --}}
                <div class="col-md-8">
                    <div class="form-group mb-3">
                        <label for="puskesmas_name">Nama Unit</label>
                        <input type="text" name="puskesmas_name_disabled" id="puskesmas_name_disabled"
                               class="form-control"
                               value="{{ $selectedPuskesmas }}" readonly disabled>
                        {{-- Input hidden untuk mengirim nama 'Labkesda' --}}
                        <input type="hidden" name="puskesmas_name" value="{{ $selectedPuskesmas }}">
                        @error('puskesmas_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                {{-- Tahun --}}
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="tahun">Tahun</label>
                        <input type="number" name="tahun" id="tahun"
                               class="form-control @error('tahun') is-invalid @enderror"
                               value="{{ old('tahun', $selectedTahun ?? date('Y')) }}"
                               placeholder="Contoh: 2025" required min="2020" max="2099">
                        @error('tahun')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
             <p class="text-muted small mt-2">Pastikan Tahun sudah benar sebelum mengisi detail di bawah.</p>
        </div>
    </div>

    {{-- 
        ============================================================
        INI BAGIAN YANG SEBELUMNYA TIDAK MUNCUL KARENA CACHE
        ============================================================
    --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detail Laporan Administrasi & TU Labkesda</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                 <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                       <thead class="bg-light text-center">
                           <tr>
                               <th style="width: 5%;">No.</th>
                               <th style="width: 5%;">Jenis</th>
                               <th style="width: 30%;">Indikator</th>
                               <th style="width: 15%;">Target</th>
                               @for ($i = 1; $i <= 12; $i++)
                                   <th style="width: 3%;">{{ \Carbon\Carbon::create()->month($i)->locale('id')->shortMonthName }}</th>
                               @endfor
                               <th style="width: 15%;">Bukti Dukung (Link/File)</th>
                           </tr>
                       </thead>
                       <tbody>
                           @php $counter = 1; @endphp
                           @forelse ($indicators as $index => $indicatorData)
                               @php
                                   $indikatorName = $indicatorData['indikator'];
                                   $jenis = $indicatorData['jenis'];
                                   $target = $indicatorData['target'];
                                   $oldCapaian = old("indikators.$index.capaian", []);
                                   $oldLink = old("indikators.$index.link_bukti_dukung");

                                   $trimmedName = trim($indikatorName);
                                   $isSubIndicator = Str::startsWith($trimmedName, ['-', ' ']);
                                   $isMainIndicator = !$isSubIndicator && preg_match('/^\d+\./', $trimmedName);
                                   $isSectionTitle = !$isSubIndicator && !$isMainIndicator && preg_match('/^[a-z]\./', $trimmedName);
                                   $isMainTitle = !$isSubIndicator && !$isMainIndicator && !$isSectionTitle;
                               @endphp
                               <tr class="{{ $isMainTitle || $isSectionTitle ? 'table-secondary fw-bold' : '' }}">
                                   <td class="text-center align-middle">
                                       @if ($isMainIndicator)
                                           {{ Str::before($trimmedName, '.') }}
                                       @endif
                                   </td>
                                   <td class="text-center align-middle">{{ $jenis }}</td>
                                   <td class="align-middle">
                                        @if ($isSubIndicator && Str::startsWith($trimmedName, '  '))
                                           <span class="ms-5">{{ trim($trimmedName) }}</span>
                                        @elseif ($isSubIndicator && Str::startsWith($trimmedName, ' '))
                                           <span class="ms-4">{{ trim($trimmedName) }}</span>
                                        @elseif ($isSubIndicator && Str::startsWith($trimmedName, '-'))
                                           <span class="ms-4">{{ Str::after($trimmedName, '-') }}</span>
                                        @elseif ($isMainIndicator)
                                            <span class="ms-2"><strong>{{ trim(Str::after($trimmedName, '.')) }}</strong></span>
                                        @elseif ($isSectionTitle)
                                            <strong>{{ trim(Str::after($trimmedName, '.')) }}</strong>
                                        @elseif ($isMainTitle)
                                            <strong class="text-primary">{{ $trimmedName }}</strong>
                                        @else
                                           {{ $trimmedName }}
                                        @endif
                                   </td>
                                   <td class="align-middle">{{ $target }}</td>

                                   {{-- Input Bulanan --}}
                                   @for ($i = 1; $i <= 12; $i++)
                                   <td class="text-center align-middle">
                                       @if (!$isMainTitle && !$isSectionTitle)
                                           @php
                                               $isQualitative = $target && (Str::contains(Str::lower($target), ['dokumen', 'ada', 'lengkap', 'sesuai', 'sk', 'rekap']) && !Str::contains(Str::lower($target), ['jumlah', 'laporan', 'empat', 'dua']));
                                               $isQuantitative = $target && (Str::contains(Str::lower($target), ['jumlah', 'laporan', 'empat', 'dua']));
                                               $oldValue = $oldCapaian[$i] ?? '';
                                           @endphp

                                           @if ($isQualitative)
                                               <select name="indikators[{{$index}}][capaian][{{$i}}]"
                                                       class="form-select form-select-sm"
                                                       style="min-width: 70px;"
                                                       title="Pilih 'Ada' atau 'Tidak'">
                                                   <option value="" {{ (string)$oldValue === '' ? 'selected' : '' }}>-</option>
                                                   <option value="Ada" {{ (string)$oldValue === 'Ada' ? 'selected' : '' }}>Ada</option>
                                                   <option value="Tidak" {{ (string)$oldValue === 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                               </select>
                                           @else
                                               <input type="number"
                                                      name="indikators[{{$index}}][capaian][{{$i}}]"
                                                      class="form-control form-control-sm text-center @error('indikators.'.$index.'.capaian.'.$i) is-invalid @enderror"
                                                      value="{{ $oldValue }}"
                                                      placeholder="Jml"
                                                      min="0"
                                                      title="Masukkan jumlah (angka)">
                                           @endif
                                       @else
                                           <input type="hidden" name="indikators[{{$index}}][capaian][{{$i}}]" value="">
                                       @endif
                                   </td>
                                   @endfor

                                   {{-- Bukti Dukung --}}
                                   <td class="align-middle" style="min-width: 250px;">
                                       @if (!$isMainTitle && !$isSectionTitle)
                                           {{-- Link --}}
                                           <textarea name="indikators[{{$index}}][link_bukti_dukung]"
                                                     class="form-control form-control-sm mb-1 @error('indikators.'.$index.'.link_bukti_dukung') is-invalid @enderror"
                                                     placeholder="https://link-drive/satu&#10;https://link-drive/dua"
                                                     rows="2" title="Masukkan satu link per baris">{{ $oldLink }}</textarea>
                                           @error('indikators.'.$index.'.link_bukti_dukung')
                                               <div class="invalid-feedback d-block small">{{ $message }}</div>
                                           @enderror

                                           {{-- File --}}
                                           <label class="small text-muted mb-0">Upload File (Max: 5MB/file):</label>
                                           <input type="file" name="indikators[{{$index}}][file_bukti_dukung][]"
                                                  class="form-control form-control-sm @error('indikators.'.$index.'.file_bukti_dukung.*') is-invalid @enderror"
                                                  multiple>
                                           @error('indikators.'.$index.'.file_bukti_dukung.*')
                                               <div class="invalid-feedback d-block small">{{ $message }}</div>
                                           @enderror
                                       @else
                                           <input type="hidden" name="indikators[{{$index}}][link_bukti_dukung]" value="">
                                       @endif
                                   </td>
                               </tr>
                           @empty
                               <tr><td colspan="17" class="text-center text-danger">Error: Daftar indikator Labkesda tidak ditemukan.</td></tr>
                           @endforelse
                           {{-- AKHIR BAGIAN YANG HILANG --}}
                       </tbody>
                 </table>
            </div>
        </div>
    </div>
    {{-- ============================================================= --}}

    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('administrasi-tu.index', ['jenis_laporan' => 'labkesda', 'tahun' => ($selectedTahun ?? date('Y')) ]) }}" class="btn btn-secondary me-2">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Laporan Labkesda</button>
    </div>

</form>
@endsection