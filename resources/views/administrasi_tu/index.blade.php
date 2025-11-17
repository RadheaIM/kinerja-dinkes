@extends('layouts.app')

@section('title', 'Laporan Administrasi & TU')
@section('page_title', 'Laporan Administrasi & Tata Usaha')

@section('content')

@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Str;
    $isUserRole = (Auth::user()->role !== 'admin');
@endphp

{{-- Menampilkan Pesan Notifikasi --}}
@if (session('error_manual'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error_manual') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
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
@if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif


{{-- ========================================================== --}}
{{-- === PERBAIKAN: Filter HANYA untuk ADMIN === --}}
{{-- ========================================================== --}}
@if(!$isUserRole)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('administrasi-tu.index') }}" class="mb-4">
                <div class="row align-items-end">
                    {{-- Filter Puskesmas --}}
                    <div class="col-md-4">
                        <label for="puskesmas">Pilih Puskesmas</label>
                        <select name="puskesmas" id="puskesmas" class="form-select" required>
                            <option value="">-- Pilih Puskesmas --</option>
                            @isset($puskesmasNames)
                                @foreach ($puskesmasNames as $name)
                                    <option value="{{ $name }}" {{ request('puskesmas') == $name ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                    {{-- Filter Tahun --}}
                    <div class="col-md-3">
                        <label for="tahun">Pilih Tahun</label>
                        <select name="tahun" id="tahun" class="form-select" required>
                            @isset($availableYears)
                                @foreach ($availableYears as $year)
                                    <option value="{{ $year }}" {{ request('tahun', date('Y')) == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                    {{-- Tombol Aksi --}}
                    <div class="col-md-5 mt-3 mt-md-0 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2"><i class="fas fa-filter"></i> Tampilkan</button>
                        <a href="{{ request('puskesmas') ? route('administrasi-tu.edit', ['puskesmas' => request('puskesmas'), 'tahun' => request('tahun', date('Y'))]) : '#' }}"
                           class="btn btn-success me-2 {{ !request('puskesmas') ? 'disabled' : '' }}"
                           title="{{ !request('puskesmas') ? 'Pilih Puskesmas terlebih dahulu' : 'Buat atau Edit Laporan' }}">
                            <i class="fas fa-plus"></i> Buat/Edit Laporan
                        </a>
                        <a href="{{ route('administrasi-tu.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Reset</a>
                    </div>
                </div>
            </form>
            @if(!request('puskesmas'))
            <div class="alert alert-info">Pilih Puskesmas dan Tahun untuk melihat atau mengedit data Administrasi & TU.</div>
            @endif
        </div>
    </div>
@endif 
{{-- ========================================================== --}}
{{-- === AKHIR PERBAIKAN FILTER === --}}
{{-- ========================================================== --}}


{{-- ========================================================== --}}
{{-- === LOGIKA KAPAN MENAMPILKAN TABEL === --}}
{{-- ========================================================== --}}
@php
    // Variabel $puskesmas dan $tahun DIISI OTOMATIS oleh controller jika $isUserRole TRUE.
    $isPuskesmasSet = isset($puskesmas) && !empty($puskesmas);
    $shouldShowTable = $isPuskesmasSet && (isset($indicators) && count($indicators) > 0);
@endphp

@if($shouldShowTable)

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            Detail Laporan: {{ $puskesmas }} - Tahun {{ $tahun }}
        </h6>
        
        {{-- Tombol Aksi di bagian atas tabel --}}
        <div>
            {{-- Tombol Edit/Buat --}}
            <a href="{{ route('administrasi-tu.edit', ['puskesmas' => $puskesmas, 'tahun' => $tahun]) }}" class="btn btn-success btn-sm me-2">
                <i class="fas fa-plus"></i> {{ (isset($laporanGrouped) && !$laporanGrouped->isEmpty()) ? 'Edit Laporan Ini' : 'Buat Laporan Ini' }}
            </a>

            {{-- Tombol Hapus (hanya jika data ada) --}}
            @if(isset($laporanGrouped) && !$laporanGrouped->isEmpty())
            <button type="button" class="btn btn-danger btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteConfirmModal"
                    data-puskesmas="{{ $puskesmas }}"
                    data-tahun="{{ $tahun }}">
                <i class="fas fa-trash"></i> Hapus Laporan Ini
            </button>
            @endif
        </div>
    </div>
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-bordered table-sm" width="100%" cellspacing="0"> 
                <thead class="bg-light text-center">
                    <tr>
                        <th style="width: 4%;">No.</th>
                        <th style="width: 4%;">Jenis SPM</th>
                        <th style="width: 30%;">Indikator</th>
                        <th style="width: 15%;">Target</th>
                        @for ($i = 1; $i <= 12; $i++)
                            <th style="width: 2.5%;">{{ \Carbon\Carbon::create()->month($i)->locale('id')->shortMonthName }}</th>
                        @endfor
                        <th style="width: 10%;">Bukti Dukung</th>
                    </tr>
                </thead>
                <tbody>
                    @php $counter = 1; @endphp
                    @forelse ($indicators as $indicatorData)
                        @php
                            $indikatorName = $indicatorData['indikator'];
                            $jenisSPM = $indicatorData['jenis'];
                            $target = $indicatorData['target'];
                            $laporanItem = isset($laporanGrouped) ? $laporanGrouped->get($indikatorName) : null; 
                            $trimmedName = trim($indikatorName);
                            $isSubIndicator = Str::startsWith($trimmedName, '-');
                            $isMainIndicator = !$isSubIndicator && preg_match('/^\d+\./', $trimmedName); 
                            $isSectionTitle = !$isSubIndicator && !$isMainIndicator && preg_match('/^[A-Z]\./', $trimmedName); 
                            $isMainTitle = !$isSubIndicator && !$isMainIndicator && !$isSectionTitle && !preg_match('/^[a-z]\./', $trimmedName);
                        @endphp
                         <tr class="{{ $isMainTitle || $isSectionTitle ? 'table-secondary fw-bold' : '' }}"> 
                            <td class="text-center align-middle">
                                @if ($isMainIndicator || $isSubIndicator)
                                    {{ $counter++ }} 
                                @elseif ($isSectionTitle)
                                    {{ Str::before($trimmedName, '.') }} 
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
                            
                            @for ($i = 1; $i <= 12; $i++)
                                <td class="text-center align-middle">
                                    @if ($laporanItem && !$isMainTitle && !$isSectionTitle && $target !== null) 
                                        @php 
                                            $blnValue = $laporanItem['bln_'.$i]; 
                                            $blnValueLower = Str::lower((string)$blnValue);
                                            $isQualitativeTarget = $target && (Str::contains(Str::lower($target), ['dokumen', 'ada', 'lengkap', 'sesuai', 'sk', 'rekap']) && !Str::contains(Str::lower($target), ['jumlah', 'laporan', 'empat']));
                                        @endphp
                                        
                                        @if($isQualitativeTarget)
                                            @if($blnValueLower === 'ada' || $blnValue === '1' || $blnValue === 1)
                                                <i class="fas fa-check text-success" title="Ada"></i> 
                                            @elseif($blnValueLower === 'tidak' || $blnValue === '0' || $blnValue === 0)
                                                <i class="fas fa-times text-danger" title="Tidak"></i>
                                            @else
                                                - 
                                            @endif
                                        @else
                                            {{ $blnValue ?? '-' }}
                                        @endif
                                    @elseif (!$isMainTitle && !$isSectionTitle && $target !== null)
                                        -
                                    @endif
                                </td>
                            @endfor
                            
                            <td class="text-center align-middle">
                                @if ($laporanItem && !$isMainTitle && !$isSectionTitle && $target !== null)
                                    @php
                                        $links = $laporanItem->link_bukti_dukung ?? [];
                                        $files = $laporanItem->file_bukti_dukung ?? [];
                                    @endphp

                                    @if (empty($links) && empty($files))
                                        -
                                    @endif

                                    @foreach ($links as $link)
                                        @if($link)
                                        <a href="{{ $link }}" target="_blank" class="btn btn-sm btn-outline-primary m-1" title="Lihat Link">
                                            <i class="fas fa-link"></i>
                                        </a>
                                        @endif
                                    @endforeach

                                    @foreach ($files as $filePath)
                                        @if($filePath)
                                         <a href="{{ Storage::url($filePath) }}" target="_blank" class="btn btn-sm btn-outline-info m-1" title="Lihat File: {{ basename($filePath) }}">
                                            <i class="fas fa-file-alt"></i>
                                        </a>
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="17" class="text-center text-danger">Error: Daftar indikator (dari Controller) tidak ditemukan.</td></tr>
                    @endforelse
                    
                    {{-- Pesan jika data laporan belum ada tapi indikator ada --}}
                    @if(isset($laporanGrouped) && $laporanGrouped->isEmpty() && isset($indicators) && count($indicators) > 0)
                        <tr>
                            <td colspan="17" class="text-center fst-italic text-muted py-3">Belum ada data laporan Administrasi & TU untuk Puskesmas dan Tahun ini. Silakan klik "Buat Laporan Ini".</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus seluruh data Laporan Administrasi & TU untuk <strong id="deletePuskesmasName"></strong> tahun <strong id="deleteTahun"></strong>? Tindakan ini tidak dapat dibatalkan. File yang terupload juga akan dihapus.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" action=""> 
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Yakin, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif {{-- End of if($shouldShowTable) --}}

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteModal = document.getElementById('deleteConfirmModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const puskesmas = button.getAttribute('data-puskesmas');
                const tahun = button.getAttribute('data-tahun');

                const modalPuskesmasName = deleteModal.querySelector('#deletePuskesmasName');
                const modalTahun = deleteModal.querySelector('#deleteTahun');
                const deleteForm = deleteModal.querySelector('#deleteForm');

                // Update teks modal
                if(modalPuskesmasName) modalPuskesmasName.textContent = puskesmas;
                if(modalTahun) modalTahun.textContent = tahun;
                
                // Update action form delete
                if(deleteForm) deleteForm.action = `/administrasi-tu/${encodeURIComponent(puskesmas)}/${encodeURIComponent(tahun)}`; 
            });
        }
    });
</script>
@endpush