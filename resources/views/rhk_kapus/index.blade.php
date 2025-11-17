@extends('layouts.app')

@section('title', 'Daftar RHK Kapus')
@section('page_title', 'Daftar RHK Kapus (Ringkasan)')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Data RHK Kapus per Puskesmas (Tahun)</h6>
        
        {{-- 
        ==================================================================
        === INI PERBAIKANNYA ===
        Tombol ini sekarang hanya akan muncul jika role user adalah 'admin'
        ==================================================================
        --}}
        @if(Auth::user()->role === 'admin')
            <a href="{{ route('rhk-kapus.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus fa-sm"></i> Import Massal RHK
            </a>
        @endif
    </div>
    <div class="card-body">

        {{-- Pesan Notifikasi Sukses --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        {{-- Pesan Notifikasi Error --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th>Nama Puskesmas</th>
                        <th style="width: 15%;" class="text-center">Tahun</th>
                        <th style="width: 20%;" class="text-center">Jumlah RHK Induk</th>
                        <th style="width: 20%;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Loop data ringkasan dari controller --}}
                    @forelse ($data as $index => $item)
                    <tr>
                        <td class="text-center">{{ $data->firstItem() + $index }}</td>
                        <td>{{ $item->puskesmas_name }}</td>
                        <td class="text-center">{{ $item->tahun }}</td>
                        <td class="text-center">{{ $item->jumlah_indikator }}</td>
                        <td class="text-center">
                            
                            {{-- Tombol Detail (Bisa dilihat semua) --}}
                            <a href="{{ route('rhk-kapus.show', ['puskesmas_name' => $item->puskesmas_name, 'tahun' => $item->tahun]) }}" 
                               class="btn btn-sm btn-info my-1" title="Lihat Detail Rincian">
                                <i class="fas fa-eye"></i> Detail
                            </a>

                            {{-- Tombol Hapus (Hanya untuk Admin) --}}
                            @if(Auth::user()->role === 'admin')
                                <button type="button" class="btn btn-sm btn-danger my-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteConfirmModal"
                                        data-delete-url="{{ route('rhk-kapus.destroy', ['puskesmas_name' => $item->puskesmas_name, 'tahun' => $item->tahun]) }}"
                                        data-delete-message="Apakah Anda yakin ingin menghapus data RHK Kapus untuk {{ $item->puskesmas_name }} ({{ $item->tahun }})? Ini akan menghapus semua data induk dan detailnya.">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center fst-italic text-muted">
                            @if(Auth::user()->role === 'admin')
                                Belum ada data RHK Kapus yang di-import.
                                <a href="{{ route('rhk-kapus.create') }}" class="btn btn-sm btn-outline-primary ms-2">Import Sekarang</a>
                            @else
                                Data RHK Kapus untuk unit Anda ({{ Auth::user()->puskesmas_name }}) tidak ditemukan atau belum di-import oleh Admin.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Links --}}
        <div class="mt-3 d-flex justify-content-center">
             {{ $data->links() }}
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Hapus (Universal) --}}
{{-- Modal ini hanya akan dipanggil oleh tombol admin --}}
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
{{-- Script untuk mengisi data Modal Hapus --}}
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