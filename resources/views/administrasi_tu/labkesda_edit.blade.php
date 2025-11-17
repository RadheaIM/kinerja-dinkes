@extends('layouts.app')

@section('title', 'Edit Laporan Administrasi & TU Labkesda')
@section('page_title', 'Edit Laporan Administrasi & Tata Usaha Labkesda')

@push('styles')
    {{-- CSS Tambahan untuk file list --}}
    <style>
        .file-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.375rem 0.75rem;
            border: 1px solid #ddd;
            border-radius: 0.25rem;
            background-color: #f8f9fa;
            margin-bottom: 5px;
        }
        .file-list-item span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 70%;
        }
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
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if (session('info'))
    <div class="alert alert-info">{{ session('info') }}</div>
@endif
{{-- Notifikasi Sukses --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif


{{-- Route ke update --}}
<form action="{{ route('administrasi-tu.update', ['puskesmas' => $selectedPuskesmas, 'tahun' => $selectedTahun]) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    {{-- Input hidden untuk menandai jenis laporan --}}
    <input type="hidden" name="jenis_laporan" value="{{ $jenisLaporan }}">

    {{-- Header Form (Sama seperti create labkesda, readonly) --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Laporan</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group mb-3">
                        <label>Nama Unit</label>
                        <input type="text" class="form-control" value="{{ $selectedPuskesmas }}" readonly disabled>
                        <input type="hidden" name="puskesmas_name" value="{{ $selectedPuskesmas }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label>Tahun</label>
                        <input type="number" class="form-control" value="{{ $selectedTahun }}" readonly disabled>
                        <input type="hidden" name="tahun" value="{{ $selectedTahun }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Indikator --}}
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

                                   // Ambil data yang sudah ada
                                   $existingItem = $laporanGrouped->get($indikatorName);
                                   $existingLinks = $existingItem->link_bukti_dukung ?? [];
                                   $existingFiles = $existingItem->file_bukti_dukung ?? [];
                                   $oldLinkText = old("indikators.$index.link_bukti_dukung", implode("\n", $existingLinks));

                                   $trimmedName = trim($indikatorName);
                                   // Logika styling baris (sama)
                                   $isSubIndicator = Str::startsWith($trimmedName, ['-', ' ']);
                                   $isMainIndicator = !$isSubIndicator && preg_match('/^\d+\./', $trimmedName);
                                   $isSectionTitle = !$isSubIndicator && !$isMainIndicator && preg_match('/^[a-z]\./', $trimmedName);
                                   $isMainTitle = !$isSubIndicator && !$isMainIndicator && !$isSectionTitle;
                               @endphp
                               <tr class="{{ $isMainTitle || $isSectionTitle ? 'table-secondary fw-bold' : '' }}">
                                   <td class="text-center align-middle">
                                       @if ($isMainIndicator) {{ Str::before($trimmedName, '.') }} @endif
                                   </td>
                                   <td class="text-center align-middle">{{ $jenis }}</td>
                                   <td class="align-middle">
                                       {{-- Logika Indentasi (sama seperti create) --}}
                                        @if ($isSubIndicator && Str::startsWith($trimmedName, '  ')) <span class="ms-5">{{ trim($trimmedName) }}</span>
                                        @elseif ($isSubIndicator && Str::startsWith($trimmedName, ' ')) <span class="ms-4">{{ trim($trimmedName) }}</span>
                                        @elseif ($isSubIndicator && Str::startsWith($trimmedName, '-')) <span class="ms-4">{{ Str::after($trimmedName, '-') }}</span>
                                        @elseif ($isMainIndicator) <span class="ms-2"><strong>{{ trim(Str::after($trimmedName, '.')) }}</strong></span>
                                        @elseif ($isSectionTitle) <strong>{{ trim(Str::after($trimmedName, '.')) }}</strong>
                                        @elseif ($isMainTitle) <strong class="text-primary">{{ $trimmedName }}</strong>
                                        @else {{ $trimmedName }}
                                        @endif
                                   </td>
                                   <td class="align-middle">{{ $target }}</td>

                                   {{-- Input Bulanan (Ambil dari $existingItem) --}}
                                   @for ($i = 1; $i <= 12; $i++)
                                   <td class="text-center align-middle">
                                       @if (!$isMainTitle && !$isSectionTitle)
                                           @php
                                               // Logika menentukan input select/number (sama)
                                               $isQualitative = $target && (Str::contains(Str::lower($target), ['dokumen', 'ada', 'lengkap', 'sesuai', 'sk', 'rekap']) && !Str::contains(Str::lower($target), ['jumlah', 'laporan', 'empat', 'dua']));
                                               $isQuantitative = $target && (Str::contains(Str::lower($target), ['jumlah', 'laporan', 'empat', 'dua']));
                                               // Ambil old() dulu, baru $existingItem
                                               $oldValue = old("indikators.$index.capaian.$i", $existingItem ? $existingItem['bln_'.$i] : null);
                                           @endphp
                                           @if ($isQualitative)
                                               <select name="indikators[{{$index}}][capaian][{{$i}}]" class="form-select form-select-sm" style="min-width: 70px;">
                                                   <option value="" {{ (string)$oldValue === '' ? 'selected' : '' }}>-</option>
                                                   <option value="Ada" {{ (string)$oldValue === 'Ada' ? 'selected' : '' }}>Ada</option>
                                                   <option value="Tidak" {{ (string)$oldValue === 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                               </select>
                                           @else
                                               <input type="number" name="indikators[{{$index}}][capaian][{{$i}}]" class="form-control form-control-sm text-center @error('indikators.'.$index.'.capaian.'.$i) is-invalid @enderror" value="{{ $oldValue }}" placeholder="Jml" min="0">
                                               @error('indikators.'.$index.'.capaian.'.$i) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                           @endif
                                       @else
                                           <input type="hidden" name="indikators[{{$index}}][capaian][{{$i}}]" value="">
                                       @endif
                                   </td>
                                   @endfor

                                   {{-- Bukti Dukung (Dengan file lama & hapus) --}}
                                   <td class="align-middle" style="min-width: 250px;">
                                       @if (!$isMainTitle && !$isSectionTitle)
                                           {{-- Link --}}
                                           <textarea name="indikators[{{$index}}][link_bukti_dukung]" class="form-control form-control-sm mb-1 @error('indikators.'.$index.'.link_bukti_dukung') is-invalid @enderror" rows="2" placeholder="Satu link per baris">{{ $oldLinkText }}</textarea>
                                           @error('indikators.'.$index.'.link_bukti_dukung') <div class="invalid-feedback d-block small">{{ $message }}</div> @enderror

                                           {{-- File Baru --}}
                                           <label class="small text-muted mb-0">Upload File Baru (Max: 5MB/file):</label>
                                           <input type="file" name="indikators[{{$index}}][file_bukti_dukung][]" class="form-control form-control-sm @error('indikators.'.$index.'.file_bukti_dukung.*') is-invalid @enderror" multiple>
                                            @error('indikators.'.$index.'.file_bukti_dukung.*') <div class="invalid-feedback d-block small">{{ $message }}</div> @enderror

                                           {{-- File Lama & Hapus --}}
                                           @if (!empty($existingFiles))
                                               <div class="mt-2">
                                                   <small><strong>File tersimpan:</strong> (Centang untuk hapus)</small>
                                                   @foreach ($existingFiles as $filePath)
                                                       @if($filePath) {{-- Pastikan path tidak kosong --}}
                                                       <div class="form-check small text-truncate">
                                                           <input class="form-check-input" type="checkbox" name="indikators[{{$index}}][hapus_file][]" value="{{ $filePath }}" id="hapus_{{ $index }}_{{ Str::slug(basename($filePath)) . Str::random(4) }}">
                                                           <label class="form-check-label text-danger me-1" for="hapus_{{ $index }}_{{ Str::slug(basename($filePath)) . Str::random(4) }}">
                                                               Hapus
                                                           </label>
                                                           {{-- Tampilkan link ke file lama --}}
                                                           <a href="{{ Storage::url($filePath) }}" target="_blank" title="{{ basename($filePath) }}" class="ms-1">{{ Str::limit(basename($filePath), 25) }}</a>
                                                       </div>
                                                       @endif
                                                   @endforeach
                                               </div>
                                           @endif
                                       @else
                                           <input type="hidden" name="indikators[{{$index}}][link_bukti_dukung]" value="">
                                       @endif
                                   </td>
                               </tr>
                           @empty
                               <tr><td colspan="17" class="text-center text-danger">Error: Daftar indikator Labkesda tidak ditemukan.</td></tr>
                           @endforelse
                       </tbody>
                 </table>
            </div>
        </div>
    </div>

    {{-- Tombol Simpan & Batal --}}
    <div class="d-flex justify-content-end mb-4">
        {{-- Tombol Batal kembali ke index dengan filter labkesda --}}
        <a href="{{ route('administrasi-tu.index', ['jenis_laporan' => 'labkesda', 'tahun' => $selectedTahun]) }}" class="btn btn-secondary me-2">Batal</a>
        <button type="submit" class="btn btn-primary">Update Laporan Labkesda</button>
    </div>

</form>
@endsection