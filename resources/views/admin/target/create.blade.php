@extends('layouts.app')

@section('title', 'Manajemen Target Sasaran')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Target Sasaran Puskesmas</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary">
            <h6 class="m-0 font-weight-bold text-white">Form Input Target Sasaran (1 Tahun)</h6>
        </div>
        <div class="card-body">
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('target.store') }}" method="POST" id="targetForm">
                @csrf
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="puskesmas_name" class="form-label fw-bold">Pilih Puskesmas <span class="text-danger">*</span></label>
                        <select name="puskesmas_name" id="puskesmas_name" class="form-select select2" required>
                            <option value="">-- Cari Nama Puskesmas --</option>
                            @foreach($listPuskesmas as $pusk)
                                {{-- Gunakan puskesmas_name jika ada (karena di DB user kita simpan puskesmas_name) --}}
                                @php $val = $pusk->puskesmas_name ?? $pusk->name; @endphp
                                <option value="{{ $val }}" {{ request('puskesmas_name') == $val ? 'selected' : '' }}>{{ $val }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih unit kerja yang ingin di-set targetnya.</small>
                    </div>
                    <div class="col-md-3">
                        <label for="tahun" class="form-label fw-bold">Tahun Sasaran <span class="text-danger">*</span></label>
                        <input type="number" name="tahun" id="tahun" class="form-control" value="{{ date('Y') }}" required>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" id="btnLoadData" class="btn btn-info text-white w-100">
                            <i class="fas fa-sync-alt me-1"></i> Cek Data Tersimpan
                        </button>
                    </div>
                </div>

                <div class="alert alert-info border-left-info">
                    <i class="fas fa-info-circle me-1"></i> 
                    Masukkan angka target untuk masing-masing indikator. Angka ini akan <strong>otomatis muncul</strong> di form laporan Puskesmas terkait.
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th>Nama Indikator Program</th>
                                <th style="width: 250px;">Target Sasaran (1 Tahun)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($listIndikator as $index => $indikator)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    {{ $indikator }}
                                    {{-- Hidden input untuk memastikan nama indikator terkirim sebagai key --}}
                                </td>
                                <td>
                                    <input type="number" 
                                           name="targets[{{ $indikator }}]" 
                                           class="form-control target-input" 
                                           placeholder="0" 
                                           min="0">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 mb-5 text-end">
                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                        <i class="fas fa-save me-2"></i> Simpan Target
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnLoad = document.getElementById('btnLoadData');
        const selectPuskesmas = document.getElementById('puskesmas_name');
        const inputTahun = document.getElementById('tahun');

        // Fungsi Load Data via AJAX
        function loadExistingTargets() {
            const pusk = selectPuskesmas.value;
            const thn = inputTahun.value;

            if(!pusk || !thn) {
                alert('Pilih Puskesmas dan Tahun terlebih dahulu!');
                return;
            }

            // Ubah tombol jadi loading
            const originalText = btnLoad.innerHTML;
            btnLoad.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            btnLoad.disabled = true;

            // Reset semua input dulu
            document.querySelectorAll('.target-input').forEach(inp => inp.value = '');

            // Fetch Data
            fetch(`{{ route('api.get-targets') }}?puskesmas_name=${encodeURIComponent(pusk)}&tahun=${thn}`)
                .then(response => response.json())
                .then(data => {
                    // Data berupa object { "Nama Indikator": 100, ... }
                    if(Object.keys(data).length === 0) {
                        alert('Belum ada data target tersimpan untuk unit ini di tahun ' + thn);
                    } else {
                        // Isi form
                        for (const [indikator, nilai] of Object.entries(data)) {
                            // Cari input dengan name="targets[Nama Indikator]"
                            const input = document.querySelector(`input[name="targets[${indikator}]"]`);
                            if(input) {
                                input.value = nilai;
                            }
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Gagal memuat data.');
                })
                .finally(() => {
                    btnLoad.innerHTML = originalText;
                    btnLoad.disabled = false;
                });
        }

        btnLoad.addEventListener('click', loadExistingTargets);
        
        // Opsional: Load otomatis saat ganti puskesmas
        selectPuskesmas.addEventListener('change', function() {
            if(this.value && inputTahun.value) {
                // loadExistingTargets(); // Dihapus agar tidak auto-load setiap kali ganti, user harus klik "Cek Data"
            }
        });
        
        // Opsional: Load otomatis saat ganti tahun
        inputTahun.addEventListener('change', function() {
            if(selectPuskesmas.value && this.value) {
                // loadExistingTargets(); // Dihapus agar tidak auto-load setiap kali ganti, user harus klik "Cek Data"
            }
        });
    });
</script>
@endpush