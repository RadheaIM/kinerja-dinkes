@extends('layouts.app')

{{-- Judul dinamis berdasarkan $jenisLaporan --}}
@section('title', ($jenisLaporan == 'labkesda') ? 'Form Admin & TU Labkesda' : 'Form Admin & TU Puskesmas')
@section('page_title', ($jenisLaporan == 'labkesda') ? 'Laporan Administrasi & Tata Usaha Labkesda' : 'Laporan Administrasi & Tata Usaha Puskesmas')

@section('content')

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
@if (session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        {{ session('info') }}
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


@php
    $isEdit = !empty($laporanGrouped) && $laporanGrouped->isNotEmpty();
    if ($isEdit) {
        // Jika ini mode edit, action-nya ke 'update'
        $actionRoute = route('administrasi-tu.update', ['puskesmas' => $selectedPuskesmas, 'tahun' => $selectedTahun]);
    } else {
        // Jika ini mode create, action-nya ke 'store'
        $actionRoute = route('administrasi-tu.store');
    }
@endphp

<form action="{{ $actionRoute }}" method="POST" enctype="multipart/form-data"> 
    @csrf
    
    @if ($isEdit)
        {{-- Wajib ada @method('PUT') untuk form update --}}
        @method('PUT')
    @endif
    
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
                        <label for="puskesmas_name">Nama Puskesmas / Unit</label>
                        
                        @if(Auth::user()->role === 'admin')
                            {{-- HANYA ADMIN YANG BISA MEMILIH PUSKESMAS --}}
                            <select name="puskesmas_name" id="puskesmas_name"
                                    class="form-select @error('puskesmas_name') is-invalid @enderror"
                                    {{ $isEdit ? 'disabled' : '' }} {{-- Kunci jika mode edit --}}
                                    required>
                                <option value="">-- Pilih Puskesmas --</option>
                                @if ($jenisLaporan == 'labkesda')
                                      <option value="Labkesda" {{ (old('puskesmas_name', $selectedPuskesmas ?? '') == 'Labkesda') ? 'selected' : '' }}>Labkesda</option>
                                @else
                                    @foreach ($puskesmasNames as $name)
                                        <option value="{{ $name }}" {{ (old('puskesmas_name', $selectedPuskesmas ?? '') == $name) ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('puskesmas_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($isEdit)
                                {{-- Jika mode edit, input disabled tidak kirim data. Kirim via hidden input --}}
                                <input type="hidden" name="puskesmas_name" value="{{ $selectedPuskesmas }}">
                            @endif

                        @else
                            {{-- USER PUSKESMAS / LABKESDA OTOMATIS TERISI DAN DIKUNCI --}}
                            @php
                                // PERBAIKAN: Menggunakan nama_puskesmas
                                $userPuskesmasName = (Auth::user()->role === 'labkesda') ? 'Labkesda' : Auth::user()->nama_puskesmas;
                            @endphp
                            <input type="text" 
                                  id="puskesmas_name_disabled"
                                  class="form-control"
                                  value="{{ $userPuskesmasName }}"
                                  disabled readonly>
                            {{-- Ini input tersembunyi yang MENGIRIMKAN data --}}
                            <input type="hidden" 
                                  name="puskesmas_name" 
                                  id="puskesmas_name" 
                                  value="{{ $userPuskesmasName }}">
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="tahun">Tahun</label>
                        <input type="number" name="tahun" id="tahun"
                               class="form-control @error('tahun') is-invalid @enderror"
                               value="{{ old('tahun', $selectedTahun ?? date('Y')) }}"
                               placeholder="Contoh: 2025" required min="2020" max="2099"
                               {{ $isEdit ? 'disabled' : '' }} {{-- Kunci jika mode edit --}}>
                        @error('tahun')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($isEdit)
                            {{-- Jika mode edit, input disabled tidak kirim data. Kirim via hidden input --}}
                            <input type="hidden" name="tahun" value="{{ $selectedTahun }}">
                        @endif
                    </div>
                </div>
            </div>
             <p class="text-muted small mt-2">Pastikan Puskesmas dan Tahun sudah benar sebelum mengisi detail di bawah.</p>
        </div>
    </div>

    {{-- Detail Indikator --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detail Laporan Administrasi & TU</h6>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm" width="100%" cellspacing="0" style="font-size: 0.9rem;">
                    
                    <thead class="bg-light text-center align-middle">
                        <tr>
                            <th rowspan="2" style="width: 3%;">No.</th>
                            <th rowspan="2" style="width: 15%;">Jenis Layanan SPM</th>
                            <th rowspan="2" style="width: 30%;">INDIKATOR</th>
                            <th rowspan="2" style="width: 10%;">Target</th>
                            <th colspan="12">Capaian Bulan Ke -</th>
                            <th rowspan="2" style="width: 15%;">Bukti Dukung (Link/File)</th>
                        </tr>
                        <tr>
                            @for ($i = 1; $i <= 12; $i++)
                                <th style="width: 2.5%;">{{ \Carbon\Carbon::create()->month($i)->locale('id')->shortMonthName }}</th>
                            @endfor
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $groupedIndicators = collect($indicators)->groupBy('jenis');
                        @endphp

                        @forelse ($groupedIndicators as $jenisSPM => $groupItems)
                            @php
                                $titleRow = $groupItems->firstWhere(fn($item) => is_null($item['deskripsi']) && is_null($item['target']));
                                $dataRows = $groupItems->filter(fn($item) => !is_null($item['deskripsi']) || !is_null($item['target']));
                                $rowspan = $dataRows->count();
                                if ($rowspan === 0) {
                                    $rowspan = 1;
                                }
                            @endphp

                            @foreach ($dataRows as $itemIndex => $indicatorData)
                                @php
                                    $index = $itemIndex; 
                                    $indikatorName = $indicatorData['indikator'];
                                    $deskripsi = $indicatorData['deskripsi'] ?? null;
                                    $target = $indicatorData['target'] ?? null;
                                    $item = $laporanGrouped[$indikatorName] ?? null; 
                                    $oldLink = old("indikators.$index.link_bukti_dukung", $item ? implode("\n", $item->link_bukti_dukung ?? []) : '');
                                    $oldFiles = $item ? $item->file_bukti_dukung : [];
                                    $trimmedName = trim($indikatorName);
                                @endphp
                                <tr>
                                    @if ($loop->first)
                                        <td class="text-center align-middle" rowspan="{{ $rowspan }}">
                                            <strong>{{ $jenisSPM }}</strong>
                                        </td>
                                        <td class="align-middle" rowspan="{{ $rowspan }}">
                                            <strong>{{ $titleRow ? $titleRow['indikator'] : 'N/A' }}</strong>
                                        </td>
                                    @endif
                                    
                                    <td class="align-middle">
                                        {{ $trimmedName }}
                                        @if (!empty($deskripsi))
                                            <br>
                                            <small class="fst-italic text-muted">{{ $deskripsi }}</small>
                                        @endif
                                    </td>

                                    <td class="align-middle">{{ $target }}</td>
                                    
                                    @for ($i = 1; $i <= 12; $i++)
                                        <td class="text-center align-middle">
                                            @php
                                                $isQualitative = $target && (Str::contains(Str::lower($target), ['dokumen', 'ada', 'lengkap', 'sesuai', 'sk', 'rekap']) && !Str::contains(Str::lower($target), ['jumlah', 'laporan', 'empat']));
                                                $isQuantitative = $target && (Str::contains(Str::lower($target), ['jumlah', 'laporan', 'empat']));
                                                $oldValue = old("indikators.$index.capaian.$i", $item ? $item['bln_'.$i] : '');
                                            @endphp
             
                                            @if ($isQualitative)
                                                <select name="indikators[{{$index}}][capaian][{{$i}}]" 
                                                        class="form-select form-select-sm" 
                                                        style="min-width: 70px;" 
                                                        title="Petunjuk: Pilih 'Ada' jika dokumen/kegiatan ada, 'Tidak' jika tidak ada.">
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
                                                       title="Petunjuk: Masukkan jumlah (angka) capaian.">
                                            @endif
                                        </td>
                                    @endfor

                                    <td class="align-middle">
                                        <textarea name="indikators[{{$index}}][link_bukti_dukung]" 
                                                  class="form-control form-control-sm mb-1 @error('indikators.'.$index.'.link_bukti_dukung') is-invalid @enderror" 
                                                  placeholder="https://link-drive/satu&#10;https://link-drive/dua"
                                                  rows="2" title="Masukkan satu link per baris">{{ $oldLink }}</textarea>
                                        @error('indikators.'.$index.'.link_bukti_dukung')
                                         <div class="invalid-feedback d-block small">{{ $message }}</div> 
                                        @enderror
                                        
                                        <label class="small text-muted mb-0">Upload File (Max: 5MB/file):</label>
                                        <input type="file" name="indikators[{{$index}}][file_bukti_dukung][]"
                                               class="form-control form-control-sm @error('indikators.'.$index.'.file_bukti_dukung.*') is-invalid @enderror"
                                               multiple>
                                        @error('indikators.'.$index.'.file_bukti_dukung.*')
                                         <div class="invalid-feedback d-block small">{{ $message }}</div> 
                                        @enderror

                                        @if (!empty($oldFiles))
                                         <div class="mt-2 small">
                                             <strong>File terlampir:</strong>
                                             @foreach ($oldFiles as $fileKey => $filePath)
                                                 @if($filePath)
                                                 <div class="d-flex justify-content-between align-items-center">
                                                     <a href="{{ Storage::url($filePath) }}" target="_blank" title="{{ $filePath }}">
                                                         File {{ $loop->iteration }} ({{ Str::limit(basename($filePath), 20) }})
                                                     </a>
                                                     <div class="form-check">
                                                         <input class="form-check-input" type="checkbox" name="indikators[{{$index}}][hapus_file][{{ $fileKey }}]" value="{{ $filePath }}" id="hapus_file_{{$index}}_{{$fileKey}}">
                                                         <label class="form-check-label text-danger" for="hapus_file_{{$index}}_{{$fileKey}}">
                                                             Hapus
                                                         </label>
                                                     </div>
                                                 </div>
                                                 @endif
                                             @endforeach
                                         </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            {{-- Fallback jika $indicators kosong --}}
                            <tr><td colspan="17" class="text-center text-danger">Error: Daftar indikator tidak ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                    
                </table>
            </div>
            
        </div>
    </div>

    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('administrasi-tu.index', ['tahun' => $selectedTahun ?? date('Y'), 'puskesmas' => $selectedPuskesmas ?? '']) }}" class="btn btn-secondary me-2">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Laporan</button>
    </div>

</form>
@endsection