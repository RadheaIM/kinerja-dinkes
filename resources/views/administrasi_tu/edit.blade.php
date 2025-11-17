@extends('layouts.app')

{{-- ========================================================== --}}
{{-- === PERBAIKAN: Judul dinamis berdasarkan $jenisLaporan === --}}
@section('title', ($jenisLaporan == 'labkesda') ? 'Edit Laporan Admin & TU Labkesda' : 'Edit Laporan Admin & TU Puskesmas')
@section('page_title', ($jenisLaporan == 'labkesda') ? 'Edit Laporan Administrasi & Tata Usaha Labkesda' : 'Edit Laporan Administrasi & Tata Usaha Puskesmas')
{{-- ========================================================== --}}

@push('styles')
    {{-- CSS Tambahan untuk file list --}}
    <style>
        .file-list-item { display: flex; justify-content: space-between; align-items: center; padding: 0.375rem 0.75rem; border: 1px solid #ddd; border-radius: 0.25rem; background-color: #f8f9fa; margin-bottom: 5px; }
        .file-list-item span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 70%; }
        .header-row th { background-color: #f2f2f2; color: #333; font-weight: 600; }
    </style>
@endpush

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
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if (session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
{{-- Notifikasi Sukses --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ route('administrasi-tu.update', ['puskesmas' => $selectedPuskesmas, 'tahun' => $selectedTahun]) }}" method="POST" enctype="multipart/form-data"> 
    @csrf
    @method('PUT') 
    
    {{-- Input hidden untuk jenis laporan, diambil dari controller --}}
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
                        {{-- 
                            ============================================================
                            INI ADALAH PERBAIKAN UTAMA
                            ============================================================
                            Kita cek $jenisLaporan. 
                            Jika 'labkesda', tampilkan input readonly.
                            Jika bukan (berarti 'puskesmas'), tampilkan dropdown.
                        --}}
                        @if ($jenisLaporan == 'labkesda')
                            {{-- TAMPILAN UNTUK LABKESDA --}}
                            <label for="puskesmas_name">Nama Unit</label>
                            <input type="text" name="puskesmas_name_disabled" id="puskesmas_name_disabled"
                                   class="form-control"
                                   value="{{ $selectedPuskesmas }}" readonly disabled>
                            <input type="hidden" name="puskesmas_name" value="{{ $selectedPuskesmas }}">
                        @else
                            {{-- TAMPILAN UNTUK PUSKESMAS --}}
                            <label for="puskesmas_name">Nama Puskesmas</label>
                            <select name="puskesmas_name_disabled" id="puskesmas_name_disabled"
                                    class="form-select" required disabled> 
                                <option value="{{ $selectedPuskesmas }}">{{ $selectedPuskesmas }}</option>
                            </select>
                            <input type="hidden" name="puskesmas_name" value="{{ $selectedPuskesmas }}"> 
                        @endif
                        {{-- ============================================================ --}}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="tahun">Tahun</label>
                        <input type="number" name="tahun_disabled" id="tahun_disabled"
                               class="form-control"
                               value="{{ old('tahun', $selectedTahun) }}"
                               required readonly disabled>
                         <input type="hidden" name="tahun" value="{{ $selectedTahun }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Indikator --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detail Laporan Administrasi & TU</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                 <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                       <thead class="bg-light text-center">
                           <tr>
                               <th style="width: 5%;">No.</th>
                               <th style="width: 5%;">Jenis SPM</th>
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
                                   $jenisSPM = $indicatorData['jenis'];
                                   $target = $indicatorData['target'];

                                   // Ambil data yang sudah tersimpan
                                   $data = $laporanGrouped->get($indikatorName);
                                   
                                   $existingLinks = $data->link_bukti_dukung ?? [];
                                   $oldLinkText = old("indikators.$index.link_bukti_dukung", implode("\n", $existingLinks));
                                   $existingFiles = $data->file_bukti_dukung ?? [];
                                   
                                   $trimmedName = trim($indikatorName);
                                   $isSubIndicator = Str::startsWith($trimmedName, '-');
                                   $isMainIndicator = !$isSubIndicator && preg_match('/^\d+\./', $trimmedName); 
                                   $isSectionTitle = !$isSubIndicator && !$isMainIndicator && preg_match('/^[a-z]\./', $trimmedName); 
                                   $isMainTitle = !$isSubIndicator && !$isMainIndicator && !$isSectionTitle; 
                               @endphp
                               <tr class="{{ $isMainTitle || $isSectionTitle ? 'table-secondary fw-bold' : '' }}"> 
                                   <td class="text-center align-middle">
                                       @if ($isMainIndicator || $isSubIndicator) {{-- Tampilkan nomor untuk main dan sub --}}
                                           {{ $counter++ }} 
                                       @elseif ($isSectionTitle)
                                           {{ Str::before($trimmedName, '.') }} {{-- Tampilkan Huruf Kapital --}}
                                       @endif
                                   </td>
                                   <td class="text-center align-middle">{{ $jenisSPM }}</td>
                                   <td class="align-middle">
                                       @if ($isSubIndicator)
                                           <span class="ms-4">{{ Str::after($trimmedName, '-') }}</span> 
                                       @elseif ($isMainIndicator)
                                           <span class="ms-2"><strong>{{ Str::after($trimmedName, '.') }}</strong></span> 
                                       @elseif ($isSectionTitle)
                                           <strong>{{ Str::after($trimmedName, '.') }}</strong> 
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
                                       @if (!$isMainTitle && !$isSectionTitle && $target !== null)
                                           @php
                                               $isQualitative = $target && (Str::contains(Str::lower($target), ['dokumen', 'ada', 'lengkap', 'sesuai', 'sk', 'rekap']) && !Str::contains(Str::lower($target), ['jumlah', 'laporan', 'empat']));
                                               $isQuantitative = $target && (Str::contains(Str::lower($target), ['jumlah', 'laporan', 'empat']));
                                               // Ambil old() dulu, baru $data (data dari database)
                                               $currentValue = old("indikators.$index.capaian.$i", $data ? $data['bln_'.$i] : null);
                                           @endphp
                               
                                           @if ($isQualitative)
                                               <select name="indikators[{{$index}}][capaian][{{$i}}]" 
                                                       class="form-select form-select-sm" 
                                                       style="min-width: 70px;" 
                                                       title="Petunjuk: Pilih 'Ada' jika dokumen/kegiatan ada, 'Tidak' jika tidak ada.">
                                                   <option value="" {{ (string)$currentValue === '' ? 'selected' : '' }}>-</option>
                                                   <option value="Ada" {{ (string)$currentValue === 'Ada' ? 'selected' : '' }}>Ada</option>
                                                   <option value="Tidak" {{ (string)$currentValue === 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                               </select>
                                           @else
                                               <input type="number"
                                                      name="indikators[{{$index}}][capaian][{{$i}}]" 
                                                      class="form-control form-control-sm text-center @error('indikators.'.$index.'.capaian.'.$i) is-invalid @enderror" 
                                                      value="{{ $currentValue }}" 
                                                      placeholder="Jml" 
                                                      min="0"
                                                      title="Petunjuk: Masukkan jumlah (angka) capaian.">
                                           @endif
                                       @else
                                           <input type="hidden" name="indikators[{{$index}}][capaian][{{$i}}]" value="">
                                       @endif
                                   </td>
                                   @endfor

                                   {{-- Bukti Dukung --}}
                                   <td class="align-middle" style="min-width: 250px;">
                                       @if (!$isMainTitle && !$isSectionTitle && $target !== null)
                                        <textarea name="indikators[{{$index}}][link_bukti_dukung]" 
                                                class="form-control form-control-sm mb-1 @error('indikators.'.$index.'.link_bukti_dukung') is-invalid @enderror" 
                                                placeholder="https://link-drive/satu&#10;https://link-drive/dua"
                                                rows="2" title="Masukkan satu link per baris">{{ $oldLinkText }}</textarea>
                                        @error('indikators.'.$index.'.link_bukti_dukung')
                                            <div class="invalid-feedback d-block small">{{ $message }}</div> 
                                        @enderror
                                        
                                        <label class="small text-muted mb-0 mt-1">Upload File Baru (Max: 5MB/file):</label>
                                        <input type="file" name="indikators[{{$index}}][file_bukti_dukung][]" 
                                               class="form-control form-control-sm @error('indikators.'.$index.'.file_bukti_dukung.*') is-invalid @enderror"
                                               multiple> 
                                        @error('indikators.'.$index.'.file_bukti_dukung.*')
                                            <div class="invalid-feedback d-block small">{{ $message }}</div> 
                                        @enderror

                                        {{-- Daftar File yang Sudah Ada --}}
                                        @if (!empty($existingFiles))
                                            <div class="mt-2">
                                                <small><strong>File tersimpan:</strong> (Centang untuk hapus)</small>
                                                @foreach ($existingFiles as $filePath)
                                                    @if($filePath)
                                                    <div class="form-check small text-truncate">
                                                        <input class="form-check-input" type="checkbox" name="indikators[{{$index}}][hapus_file][]" value="{{ $filePath }}" id="hapus_{{ $index }}_{{ Str::slug(basename($filePath)) . Str::random(4) }}">
                                                        <label class="form-check-label text-danger me-1" for="hapus_{{ $index }}_{{ Str::slug(basename($filePath)) . Str::random(4) }}">
                                                            Hapus
                                                        </label>
                                                        <a href="{{ Storage::url($filePath) }}" target="_blank" title="{{ basename($filePath) }}">{{ Str::limit(basename($filePath), 25) }}</a>
                                                    </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                        @else 
                                            <input type="hidden" name="indikators[{{$index}}][link_bukti_dukung]" value="{{ $oldLinkText }}">
                                        @endif
                                   </td>
                               </tr>
                           @empty
                               <tr><td colspan="17" class="text-center text-danger">Error: Daftar indikator tidak ditemukan.</td></tr>
                           @endforelse
                       </tbody>
                 </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('administrasi-tu.index', ['puskesmas' => $selectedPuskesmas, 'tahun' => $selectedTahun]) }}" class="btn btn-secondary me-2">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>

</form>
@endsection