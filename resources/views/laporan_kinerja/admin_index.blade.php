@extends('layouts.app')

@section('title', 'Laporan Kinerja Puskesmas')
@section('page_title', 'Laporan Kinerja - Capaian Program')

@section('content')

{{-- Bagian Chart (Grafik) --}}
@isset($chartData)
<div class="card shadow mb-4">
    <div class="card-header py-3">
         {{-- Judul dinamis --}}
        <h6 class="m-0 font-weight-bold text-primary">
            Grafik Tren Bulanan: {{ $chartTitle ?? 'Tren Indikator Utama' }} (Tahun {{ $chartYear }})
        </h6>
    </div>
    <div class="card-body">
        <div style="height: 400px;"> {{-- Beri tinggi agar canvas terlihat --}}
             <canvas id="kinerjaLineChart"></canvas>
        </div>
        <small class="text-muted mt-2 d-block">Menampilkan tren bulanan dari Puskesmas {{ $chartTitle }} untuk indikator: Pelayanan Kesehatan Ibu Hamil.</small>
    </div>
</div>
@endisset

{{-- Bagian Tabel Daftar Laporan --}}
<div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Laporan Kinerja (Capaian Program)</h6>
        <a href="{{ route('laporan-kinerja.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Buat Laporan Baru
        </a>
    </div>
    <div class="card-body">

        {{-- Pesan Notifikasi --}}
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            {{-- Tombol close Bootstrap 5 --}}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> 
        </div>
        @endif
        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
             {{-- Tombol close Bootstrap 5 --}}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>No</th>
                        <th>Nama Puskesmas</th>
                        <th>Tahun Laporan</th>
                        <th>Terakhir Diperbarui</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($laporans as $laporan)
                    <tr>
                        <td>{{ $loop->iteration + $laporans->firstItem() - 1 }}</td>
                        <td>{{ $laporan->puskesmas_name }}</td>
                        <td>{{ $laporan->tahun }}</td>
                        <td>{{ $laporan->updated_at->format('d M Y H:i') }}</td>
                        <td class="text-center" style="width: 250px;">
                            {{-- === PERUBAHAN: Tombol Lihat Tren === --}}
                            <a href="{{ route('laporan-kinerja.admin.index', ['laporan_id' => $laporan->id]) }}" class="btn btn-sm btn-info" title="Lihat Tren">
    <i class="fas fa-chart-line"></i> Lihat Tren
</a>
                            
                            <a href="{{ route('laporan-kinerja.edit', $laporan->id) }}" class="btn btn-warning btn-sm mx-1" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            <button type="button" class="btn btn-danger btn-sm mx-1"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteConfirmModal"
                                    data-id="{{ $laporan->id }}"
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>

                            <form id="delete-form-{{ $laporan->id }}" action="{{ route('laporan-kinerja.destroy', $laporan->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada laporan kinerja. Silakan buat laporan baru.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $laporans->links('pagination::bootstrap-5') }} 
        </div>

    </div>
</div>

<!-- Modal Konfirmasi Hapus (Bootstrap 5) -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus laporan ini? Seluruh data 12 bulan dan 13 indikatornya akan hilang. Tindakan ini tidak dapat dibatalkan.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Yakin, Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- 1. Load Library Chart.js dari CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Pastikan jQuery sudah dimuat oleh layouts/app.blade.php
    $(document).ready(function () {
        
        // --- Script untuk Modal Hapus (Bootstrap 5) ---
        const deleteModal = document.getElementById('deleteConfirmModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const dataId = button.getAttribute('data-id');
                const confirmButton = deleteModal.querySelector('#confirmDeleteButton');
                
                const newConfirmButton = confirmButton.cloneNode(true);
                confirmButton.parentNode.replaceChild(newConfirmButton, confirmButton);

                newConfirmButton.addEventListener('click', function() {
                     document.getElementById('delete-form-' + dataId).submit();
                });
            });
        }

        // --- Script untuk Menampilkan Chart ---
        const ctx = document.getElementById('kinerjaLineChart');
        
        // Menggunakan JSON.parse untuk membaca string JSON dari PHP
        const chartData = JSON.parse('<?php echo addslashes(json_encode($chartData ?? null)); ?>');

        if (ctx && chartData && chartData.datasets && chartData.datasets.length > 0) { // Hanya jalankan jika canvas dan data ada
            new Chart(ctx, {
                type: 'line', 
                data: chartData, 
                options: {
                    responsive: true, 
                    maintainAspectRatio: false, 
                    plugins: {
                        legend: {
                            position: 'bottom', 
                             labels: {
                                 boxWidth: 12 
                            }
                        },
                        tooltip: {
                            mode: 'index', 
                            intersect: false,
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true, 
                             title: {
                                 display: true,
                                 text: 'Jumlah Realisasi'
                             }
                        },
                         x: {
                             title: {
                                 display: true,
                                 text: 'Bulan'
                             }
                         }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        } else if (ctx) {
             // Tampilkan pesan jika tidak ada data chart
             const context = ctx.getContext('2d');
             context.textAlign = 'center';
              context.fillStyle = '#6c757d'; 
             context.font = '14px Segoe UI'; 
             context.fillText('Klik tombol "Lihat Tren" pada laporan di bawah untuk menampilkan grafik.', ctx.width / 2, ctx.height / 2);
        }

    });
</script>
@endpush