@extends('layouts.app')

@section('title', 'Detail Laporan Kinerja')
@section('page_title', 'Detail Laporan Kinerja ' . $laporan->puskesmas_name . ' (' . $laporan->tahun . ')')

@section('content')

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Informasi Dasar Laporan</h6>
        
        <div>
            {{-- Tombol Download PDF --}}
            <a href="{{ route('rekap.download', $laporan->id) }}" class="btn btn-danger btn-sm me-2" title="Unduh Laporan dalam format PDF">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
            
            {{-- Tombol Edit --}}
            <a href="{{ route('laporan-kinerja.edit', $laporan->id) }}" class="btn btn-warning btn-sm me-2" title="Edit Laporan Ini">
                <i class="fas fa-edit"></i> Edit Laporan
            </a>

            {{-- Tombol Kembali --}}
            <a href="{{ route('rekap.index') }}" class="btn btn-secondary btn-sm" title="Kembali ke Halaman Rekapitulasi">
                <i class="fas fa-arrow-left"></i> Kembali ke Rekap
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <p><strong>Puskesmas/Unit:</strong> {{ $laporan->puskesmas_name }}</p>
                <p><strong>Tahun Laporan:</strong> {{ $laporan->tahun }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Jenis Laporan:</strong> {{ ucwords(str_replace('_', ' ', $laporan->jenis_laporan)) }}</p>
                <p><strong>Terakhir Diperbarui:</strong> {{ $laporan->updated_at->isoFormat('D MMMM YYYY, HH:mm') }}</p>
            </div>
        </div>

        <hr>

        {{-- Tabel Detail Capaian Indikator Bulanan --}}
        <h6 class="mt-4 mb-3">Detail Capaian Indikator Bulanan</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover table-sm" width="100%" cellspacing="0">
                <thead class="bg-info text-white text-center">
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 25%;">Indikator Capaian</th>
                        <th style="width: 8%;">Target</th>
                        @php
                            // Membuat array bulan dari Carbon untuk header
                            $months = [];
                            for ($i = 1; $i <= 12; $i++) {
                                $months[$i] = \Carbon\Carbon::create()->month($i)->locale('id')->shortMonthName;
                            }
                        @endphp
                        @foreach($months as $key => $name)
                            <th>{{ $name }}</th> {{-- Jan, Feb, Mar, dst --}}
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($laporan->details as $index => $detail)
                        <tr>
                            <td class="text-center align-middle">{{ $index + 1 }}</td>
                            <td class="align-middle">{{ $detail->indikator_name }}</td>
                            
                            {{-- Target --}}
                            <td class="text-center align-middle">
                                @php
                                    $targetValue = $detail->target_sasaran ?? null;
                                    $displayTarget = ($targetValue === null || $targetValue === 0) 
                                        ? '-' 
                                        : number_format($targetValue, 0, ',', '.');
                                @endphp
                                {{ $displayTarget }}
                            </td>
                            
                            {{-- Capaian Bulanan --}}
                            @for ($m = 1; $m <= 12; $m++)
                                <td class="text-center align-middle">
                                    @php
                                        $monthlyValue = $detail['bln_'.$m] ?? null;
                                        $displayValue = ($monthlyValue === null || $monthlyValue === 0) 
                                            ? '-' 
                                            : number_format($monthlyValue, 0, ',', '.');
                                    @endphp
                                    {{ $displayValue }}
                                </td>
                            @endfor
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" class="text-center fst-italic text-muted">Tidak ada detail indikator capaian untuk laporan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection