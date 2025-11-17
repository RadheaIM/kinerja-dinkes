@extends('layouts.app')

@section('title', 'Laporan Sasaran Puskesmas')
@section('page_title', 'Laporan Sasaran Tiap Puskesmas')

@section('content')

<div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Data Sasaran Puskesmas</h6>
        <div class="d-flex">
            {{-- Tombol untuk Import Data Excel/CSV (untuk 67 Puskesmas) --}}
            <a href="{{ route('laporan-puskesmas.import.form') }}" class="btn btn-success btn-sm mr-2">
                <i class="fas fa-file-excel"></i> Import Excel
            </a>
            {{-- Tombol untuk Tambah Data Manual --}}
            <a href="{{ route('laporan-puskesmas.create') }}" class="btn btn-primary btn-sm mr-2">
                <i class="fas fa-plus"></i> Tambah Data
            </a>
            {{-- Tombol untuk Export ke PDF --}}
            <a href="{{ route('laporan-puskesmas.export-pdf') }}" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>
    <div class="card-body">
        {{-- Pesan Notifikasi Sukses dari Controller --}}
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        {{-- Pesan Notifikasi Error dari Controller --}}
        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>Puskesmas</th>
                        <th>Bumil</th>
                        <th>Bulin</th>
                        <th>BBL</th>
                        <th>Balita</th>
                        <th>Pendidikan Dasar</th>
                        <th>Uspro</th>
                        <th>Lansia</th>
                        <th>Hipertensi</th>
                        <th>DM</th>
                        <th>ODGJ Berat</th>
                        <th>TB</th>
                        <th>HIV</th>
                        <th>IDL</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Perulangan Data --}}
                    @forelse ($data as $item)
                    <tr>
                        <td>{{ $item->puskesmas }}</td>
                        <td>{{ $item->bumil }}</td>
                        <td>{{ $item->bulin }}</td>
                        <td>{{ $item->bbl }}</td>
                        <td>{{ $item->balita }}</td>
                        <td>{{ $item->pendidikan_dasar }}</td>
                        <td>{{ $item->uspro }}</td>
                        <td>{{ $item->lansia }}</td>
                        <td>{{ $item->hipertensi }}</td>
                        <td>{{ $item->dm }}</td>
                        <td>{{ $item->odgj_berat }}</td>
                        <td>{{ $item->tb }}</td>
                        <td>{{ $item->hiv }}</td>
                        <td>{{ $item->idl }}</td>
                        {{-- KOLOM AKSI (Edit dan Hapus) --}}
                        <td class="text-center" style="width: 120px;">
                            <a href="{{ route('laporan-puskesmas.edit', $item->id) }}" class="btn btn-warning btn-sm mx-1" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            {{-- PERBAIKAN: Tombol Hapus diganti menggunakan Modal Bootstrap --}}
                            <button type="button" class="btn btn-danger btn-sm mx-1" 
                                    data-toggle="modal" 
                                    data-target="#deleteConfirmModal" 
                                    data-id="{{ $item->id }}" 
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>

                            {{-- Form DELETE tersembunyi yang dipanggil oleh Modal --}}
                            <form id="delete-form-{{ $item->id }}" action="{{ route('laporan-puskesmas.destroy', $item->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="15" class="text-center">Belum ada data. Silakan impor atau tambah data baru.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- ======================================= -->
        <!-- == KODE TAMBAHAN: MENAMPILKAN LINK PAGINASI == -->
        <!-- ======================================= -->
        <div class="d-flex justify-content-center mt-3">
            {{ $data->links() }}
        </div>
        <!-- ======================================= -->

    </div>
</div>

<!-- =========================================== -->
<!-- == MODAL BARU: Konfirmasi Hapus == -->
<!-- =========================================== -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                {{-- Tombol ini akan memicu submit form delete --}}
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Yakin, Hapus</button>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- Script untuk modal (diletakkan di luar @section('content')) --}}
@push('scripts')
<script>
    // Pastikan jQuery sudah dimuat oleh layouts/app.blade.php
    $(document).ready(function () {
        // Tangkap event saat modal delete akan ditampilkan
        $('#deleteConfirmModal').on('show.bs.modal', function (event) {
            // Ambil tombol yang memicu modal
            var button = $(event.relatedTarget); 
            // Ambil ID data dari atribut 'data-id' di tombol
            var id = button.data('id'); 
            
            // Cari tombol "Yakin, Hapus" di dalam modal
            var modal = $(this);
            var confirmButton = modal.find('#confirmDeleteButton');
            
            // Set atribut onclick pada tombol "Yakin, Hapus"
            // agar mensubmit form delete yang benar (sesuai ID)
            confirmButton.off('click').on('click', function () {
                document.getElementById('delete-form-' + id).submit();
            });
        });
    });
</script>
@endpush