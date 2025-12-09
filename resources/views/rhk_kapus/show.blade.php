@extends('layouts.app')

@section('title', 'Daftar RHK Kapus')
@section('page_title', 'Perjanjian Kinerja (PERKIN) Kapus')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Dokumen Perkin Kapus (Berlaku untuk Semua Puskesmas)</h6>
        
        @if(Auth::user()->role === 'admin')
            <a href="{{ route('rhk-kapus.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus fa-sm"></i> Import Ulang Perkin
            </a>
        @endif
    </div>
    <div class="card-body">
        
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Filter Tahun --}}
        <form method="GET" action="{{ route('rhk-kapus.index') }}" class="mb-3">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label for="tahun" class="form-label">Tampilkan Perkin Tahun:</label>
                    <select name="tahun" id="tahun" class="form-select">
                        @forelse($tahunOptions as $th)
                            <option value="{{ $th }}" {{ (string)$th === (string)$tahun ? 'selected' : '' }}>{{ $th }}</option>
                        @empty
                            <option value="{{ $tahun }}">{{ $tahun }}</option>
                        @endforelse
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                </div>
            </div>
        </form>

        <div class="alert alert-info">
            Menampilkan dokumen master untuk tahun <strong>{{ $tahun }}</strong>.
        </div>

        {{-- 
        ==================================================================
        === INI PERBAIKANNYA (Kembali ke Tampilan Rowspan / Merge) ===
        ==================================================================
        --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                <thead class="bg-primary text-white text-center align-middle">
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th>RHK Kepala Dinas</th>
                        <th>RHK Kapus</th>
                        <th>Indikator Kinerja</th>
                        <th style="width: 10%;">Aspek</th>
                        <th style="width: 15%;">Target Tahunan</th>
                        <th>Rencana Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- 
                    Looping Ganda:
                    1. Loop luar untuk data INDUK ($data_induk)
                    2. Loop dalam untuk data DETAIL ($induk->details)
                    --}}
                    @forelse ($data_induk as $parentIndex => $induk)
                        
                        {{-- Cek jika data detail ada dan tidak kosong --}}
                        @if ($induk->details && $induk->details->count() > 0)
                            
                            {{-- Loop data detail (anak) --}}
                            @foreach ($induk->details as $detailIndex => $detail)
                                <tr>
                                    {{-- 
                                    Kolom INDUK (No, RHK Kadis, RHK Kapus)
                                    HANYA ditampilkan pada baris PERTAMA dari grup ini
                                    --}}
                                    @if ($loop->first) {{-- $loop->first sama dengan $detailIndex == 0 --}}
                                        <td class="text-center align-middle" rowspan="{{ $induk->details->count() }}">
                                            {{ $data_induk->firstItem() + $parentIndex }}
                                        </td>
                                        <td class="align-middle" rowspan="{{ $induk->details->count() }}">
                                            {{ $induk->rhk_kadis }}
                                        </td>
                                        <td class="align-middle" rowspan="{{ $induk->details->count() }}">
                                            {{ $induk->rhk_kapus }}
                                        </td>
                                    @endif

                                    {{-- Kolom DETAIL (Indikator, Aspek, dll) --}}
                                    {{-- Kolom ini ditampilkan di SETIAP baris --}}
                                    <td class="align-middle">{{ $detail->indikator_kinerja }}</td>
                                    <td class="text-center align-middle">{{ $detail->aspek }}</td>
                                    <td class="text-center align-middle">{{ $detail->target_tahunan }}</td>
                                    <td class="align-middle">{{ $detail->rencana_aksi }}</td>
                                </tr>
                            @endforeach
                            
                        @else 
                            {{-- Jika data Induk ada, tapi data Detail-nya kosong (seharusnya tidak terjadi) --}}
                            <tr>
                                <td class="text-center">{{ $data_induk->firstItem() + $parentIndex }}</td>
                                <td>{{ $induk->rhk_kadis }}</td>
                                <td>{{ $induk->rhk_kapus }}</td>
                                <td colspan="4" class="text-center fst-italic text-muted">Data detail tidak ditemukan</td>
                            </tr>
                        @endif

                    @empty
                        {{-- Jika tidak ada data INDUK sama sekali --}}
                        <tr>
                            <td colspan="7" class="text-center fst-italic text-muted">
                                @if(Auth::user()->role === 'admin')
                                    Data RHK Kapus Master untuk tahun {{ $tahun }} tidak ditemukan.
                                    <a href="{{ route('rhk-kapus.create') }}" class="btn btn-sm btn-outline-primary ms-2">Import Sekarang</a>
                                @else
                                    Data RHK Kapus Master untuk tahun {{ $tahun }} belum di-import oleh Admin.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Links (sekarang mem-paginate data Induk) --}}
        <div class="mt-3 d-flex justify-content-center">
            {{ $data_induk->appends(request()->query())->links() }}
        </div>

        {{-- Tombol Hapus Master (Hanya Admin) --}}
        @if(Auth::user()->role === 'admin' && $data_induk->isNotEmpty())
            <hr>
            <div class="text-end">
                 <button type="button" class="btn btn-danger my-1" 
                        data-bs-toggle="modal" 
                        data-bs-target="#deleteConfirmModal"
                        data-delete-url="{{ route('rhk-kapus.destroy', ['rhk_kapu' => 'PERKIN_MASTER_DOCUMENT', 'tahun' => $tahun]) }}"
                        data-delete-message="Apakah Anda yakin ingin menghapus data RHK Kapus Master untuk tahun {{ $tahun }}? Ini akan menghapus semua data untuk tahun ini.">
                    <i class="fas fa-trash"></i> Hapus Dokumen Perkin Tahun {{ $tahun }}
                </button>
            </div>
        @endif

    </div>
</div>

{{-- Modal Konfirmasi Hapus --}}
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="deleteModalMessage">
                Apakah Anda yakin ingin menghapus data ini?
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
@endsection

@push('scripts')
{{-- Script untuk Modal Hapus --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteConfirmModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const deleteUrl = button.getAttribute('data-delete-url');
            const message = button.getAttribute('data-delete-message');
            const modalMessage = deleteModal.querySelector('#deleteModalMessage');
            const deleteForm = deleteModal.querySelector('#deleteForm');
            if(modalMessage) modalMessage.textContent = message || 'Apakah Anda yakin ingin menghapus laporan ini?';
            if(deleteForm) deleteForm.action = deleteUrl || '#';
        });
    }
});
</script>
@endpush