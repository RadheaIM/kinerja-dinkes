@extends('layouts.app')

@section('title', 'Rekap Bulanan')
@section('page_title', 'Rekapitulasi Laporan Puskesmas & Labkesda')

@section('content')

{{-- Filter Card --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filter Rekapitulasi</h6>
    </div>
    <div class="card-body">
        {{-- Pesan Notifikasi Sukses --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Form Filter --}}
        <form method="GET" action="{{ route('rekap.index') }}" class="mb-4">
            {{-- Simpan ID laporan grafik agar tetap terpilih saat filter --}}
            @if(request('laporan_id'))
                <input type="hidden" name="laporan_id" value="{{ request('laporan_id') }}">
            @endif
             {{-- Simpan Tab Aktif --}}
             <input type="hidden" name="active_tab" id="active_tab_input" value="{{ request('active_tab', '#kinerja-puskesmas-tab-pane') }}">

            <div class="row align-items-end">
                {{-- Filter Bulan --}}
                <div class="col-md-3">
                    <label for="bulan" class="form-label fw-bold">Filter Bulan (Ringkasan)</label>
                    <select name="bulan" id="bulan" class="form-select">
                        <option value="">-- Akumulasi Tahun --</option>
                        @isset($bulanNames)
                            @foreach ($bulanNames as $key => $name)
                                <option value="{{ $key }}" {{ request('bulan') == $key ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        @else
                            @for ($m=1; $m<=12; $m++)
                                <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->locale('id')->monthName }}</option>
                            @endfor
                        @endisset
                    </select>
                </div>

                {{-- Filter Tahun --}}
                <div class="col-md-3">
                    <label for="tahun" class="form-label fw-bold">Filter Tahun</label>
                    <select name="tahun" id="tahun" class="form-select" required>
                        @isset($availableYears)
                            @if($availableYears->isEmpty())
                                <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                            @else
                                @foreach ($availableYears as $year)
                                    <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            @endif
                        @else
                            <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                        @endisset
                    </select>
                </div>

                {{-- Tombol Aksi --}}
                <div class="col-md-6 mt-3 mt-md-0">
                    <button type="submit" class="btn btn-primary me-2"><i class="fas fa-filter"></i> Tampilkan</button>
                    <a href="{{ route('rekap.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Reset Filter</a>
                </div>
            </div>
        </form>

        <hr>

        {{-- Navigasi Tabs (4 Tabs) --}}
        <ul class="nav nav-tabs mb-3" id="rekapTab" role="tablist">
             <li class="nav-item" role="presentation">
                 <button class="nav-link active" id="kinerja-puskesmas-tab" data-bs-toggle="tab" data-bs-target="#kinerja-puskesmas-tab-pane" type="button" role="tab" aria-controls="kinerja-puskesmas-tab-pane" aria-selected="true">Kinerja Puskesmas</button>
             </li>
             <li class="nav-item" role="presentation">
                 <button class="nav-link" id="kinerja-labkesda-tab" data-bs-toggle="tab" data-bs-target="#kinerja-labkesda-tab-pane" type="button" role="tab" aria-controls="kinerja-labkesda-tab-pane" aria-selected="false">Kinerja Labkesda</button>
             </li>
             <li class="nav-item" role="presentation">
                 <button class="nav-link" id="admin-tu-puskesmas-tab" data-bs-toggle="tab" data-bs-target="#admin-tu-puskesmas-tab-pane" type="button" role="tab" aria-controls="admin-tu-puskesmas-tab-pane" aria-selected="false">Admin TU Puskesmas</button>
             </li>
             <li class="nav-item" role="presentation">
                 <button class="nav-link" id="admin-tu-labkesda-tab" data-bs-toggle="tab" data-bs-target="#admin-tu-labkesda-tab-pane" type="button" role="tab" aria-controls="admin-tu-labkesda-tab-pane" aria-selected="false">Admin TU Labkesda</button>
             </li>
         </ul>

        {{-- Konten Tabs --}}
        <div class="tab-content" id="rekapTabContent">

            {{-- ============================================= --}}
            {{-- TAB PANE 1: REKAP KINERJA PUSKESMAS         --}}
            {{-- ============================================= --}}
            <div class="tab-pane fade show active" id="kinerja-puskesmas-tab-pane" role="tabpanel" aria-labelledby="kinerja-puskesmas-tab" tabindex="0">

                {{-- 1.A. Tabel Rekap Ringkas Kinerja Puskesmas --}}
                <h6 class="mb-3 fw-bold text-primary">Rekap Ringkas Kinerja Puskesmas ({{ $bulan && isset($bulanNames[$bulan]) ? $bulanNames[$bulan] : 'Akumulasi' }} {{ $tahun }})</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped table-hover table-sm" width="100%" cellspacing="0">
                         <thead class="bg-primary text-white text-center">
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th>Nama Puskesmas</th>
                                <th style="width: 10%;">Tahun</th>
                                <th style="width: 15%;" class="text-center">Target Total</th>
                                <th style="width: 15%;" class="text-center">Realisasi ({{ $bulan ? 'Bln Ini' : 'Akumulasi' }})</th>
                                <th style="width: 15%;" class="text-center">Persentase Capaian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rekapKinerjaPuskesmas as $index => $data)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $data['puskesmas_name'] }}</td>
                                <td class="text-center">{{ $data['tahun'] }}</td>
                                <td class="text-center">{{ number_format($data['total_target'] ?? 0, 0, ',', '.') }}</td>
                                <td class="text-center">{{ number_format($data['total_realisasi'] ?? 0, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @php $persentase = $data['persentase'] ?? 0; @endphp
                                    <span class="badge rounded-pill fs-6 {{ $persentase >= 75 ? 'bg-success' : ($persentase >= 50 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ number_format($persentase, 2, ',', '.') }}%
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center fst-italic text-muted">Tidak ada data rekapitulasi kinerja Puskesmas yang ditemukan untuk filter ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <hr>

                {{-- 1.B. Grafik Tren Kinerja Puskesmas --}}
                <h6 class="mb-1 fw-bold text-primary">
                    <i class="fas fa-chart-line me-2"></i>
                    Grafik Tren Bulanan {{ $chartTitle ? $chartTitle.' ('.$chartYear.')' : '(Pilih Laporan dari tabel di bawah)' }}
                </h6>

                {{-- Dropdown Filter Indikator --}}
                @if($chartData && !empty($chartData['datasets']))
                    <div class="row align-items-end mb-3">
                        <div class="col-md-5">
                            <label for="indikator_filter" class="form-label fw-bold">Filter Grafik Per Indikator:</label>
                            <select name="indikator_filter" id="indikator_filter" class="form-select form-select-sm">
                                <option value="all" selected>Tampilkan Semua Indikator</option>
                                @foreach($listIndikator as $indikator)
                                    <option value="{{ $indikator }}">{{ $indikator }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif

                <small class="text-muted d-block mb-3">Klik nama indikator di bawah grafik untuk menampilkan/menyembunyikan garisnya.</small>
                
                {{-- Canvas Grafik --}}
                @if($chartData && !empty($chartData['datasets']))
                    <div style="height: 450px; margin-bottom: 2rem; position: relative; width: 100%;">
                        <canvas id="kinerjaChartPuskesmas"></canvas>
                    </div>
                @elseif($selectedLaporanGrafik)
                    <div class="alert alert-warning">Data detail tidak ditemukan atau kosong untuk laporan {{ $chartTitle }} tahun {{ $chartYear }}. Grafik tidak dapat ditampilkan.</div>
                @else
                    <div class="alert alert-info">Pilih laporan dari tabel di bawah dan klik tombol <strong><i class="fas fa-chart-line"></i> Tren</strong> untuk menampilkan grafik.</div>
                @endif

                <hr>

                {{-- 1.C. Daftar Detail Laporan Kinerja Puskesmas --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="m-0 fw-bold text-primary">Daftar Laporan Kinerja Puskesmas (Tahun {{ $tahun }})</h6>
                </div>
                <div class="table-responsive">
                     <table class="table table-bordered table-striped table-hover table-sm" width="100%" cellspacing="0">
                         <thead class="bg-secondary text-white text-center">
                             <tr>
                                 <th style="width: 5%;">No</th>
                                 <th>Nama Puskesmas</th>
                                 <th style="width: 15%;">Tahun Laporan</th>
                                 <th style="width: 20%;">Terakhir Diperbarui</th>
                                 <th style="width: 25%;">Aksi</th>
                             </tr>
                         </thead>
                         <tbody>
                             @forelse ($laporansPuskesmas as $index => $laporan)
                             <tr class="{{ ($selectedLaporanGrafik && $laporan->id == $selectedLaporanGrafik->id) ? 'table-info' : '' }}">
                                 <td class="text-center">{{ $laporansPuskesmas->firstItem() + $index }}</td>
                                 <td>{{ $laporan->puskesmas_name }}</td>
                                 <td class="text-center">{{ $laporan->tahun }}</td>
                                 <td class="text-center">{{ $laporan->updated_at->isoFormat('D MMM YYYY, HH:mm') }}</td>
                                 <td class="text-center align-middle">
                                    <div class="btn-group btn-group-sm" role="group">
                                        {{-- Tombol Detail --}}
                                        <a href="{{ route('rekap.show', $laporan->id) }}" class="btn btn-primary" title="Lihat Detail Laporan">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                        {{-- Tombol Tren --}}
                                        <a href="{{ route('rekap.index', ['tahun' => $tahun, 'bulan' => $bulan, 'laporan_id' => $laporan->id, 'active_tab' => '#kinerja-puskesmas-tab-pane']) }}"
                                            class="btn btn-info text-white" title="Lihat Tren Grafik">
                                            <i class="fas fa-chart-line"></i> Tren
                                        </a>
                                        {{-- Tombol Edit --}}
                                        <a href="{{ route('laporan-kinerja.edit', $laporan->id) }}" class="btn btn-warning" title="Edit Laporan">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        {{-- Tombol Hapus --}}
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal"
                                                data-delete-url="{{ route('laporan-kinerja.destroy', $laporan->id) }}"
                                                data-delete-message="Apakah Anda yakin ingin menghapus Laporan Kinerja Puskesmas {{ $laporan->puskesmas_name }} ({{ $laporan->tahun }})?">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                             </tr>
                             @empty
                             <tr>
                                 <td colspan="5" class="text-center fst-italic text-muted">Belum ada laporan kinerja Puskesmas untuk tahun {{ $tahun }}.</td>
                             </tr>
                             @endforelse
                         </tbody>
                     </table>
                     {{-- Pagination Puskesmas --}}
                     <div class="mt-3 d-flex justify-content-center">
                          {{ $laporansPuskesmas->appends(request()->except('puskesmas_page') + ['active_tab' => '#kinerja-puskesmas-tab-pane'])->links() }}
                     </div>
                 </div>

            </div> {{-- Akhir Tab Pane Kinerja Puskesmas --}}


            {{-- ============================================= --}}
            {{-- TAB PANE 2: REKAP KINERJA LABKESDA          --}}
            {{-- ============================================= --}}
            <div class="tab-pane fade" id="kinerja-labkesda-tab-pane" role="tabpanel" aria-labelledby="kinerja-labkesda-tab" tabindex="0">
                <h6 class="mb-3 fw-bold text-success">Rekap Kinerja Labkesda ({{ $bulan && isset($bulanNames[$bulan]) ? $bulanNames[$bulan] : 'Akumulasi' }} {{ $tahun }})</h6>

                {{-- 1. Rekap Ringkas Labkesda --}}
                @if(isset($rekapKinerjaLabkesda))
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-striped table-hover table-sm" width="100%" cellspacing="0">
                            <thead class="bg-success text-white text-center">
                                <tr>
                                    <th class="align-middle">Nama Unit</th>
                                    <th style="width: 10%;" class="align-middle">Tahun</th>
                                    <th style="width: 20%;" class="align-middle">Jenis Laporan</th>
                                    <th style="width: 20%;" class="text-center align-middle">Total Realisasi ({{ $bulan ? 'Bln Ini' : 'Akumulasi' }})</th>
                                    <th style="width: 25%;" class="align-middle">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="align-middle">{{ $rekapKinerjaLabkesda['puskesmas_name'] }}</td>
                                    <td class="text-center align-middle">{{ $rekapKinerjaLabkesda['tahun'] }}</td>
                                    <td class="align-middle">{{ $rekapKinerjaLabkesda['jenis_laporan'] }}</td>
                                    <td class="text-center align-middle">{{ number_format($rekapKinerjaLabkesda['total_realisasi'] ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('rekap.show', $rekapKinerjaLabkesda['id']) }}" class="btn btn-info text-white" title="Lihat Detail Laporan">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                            <a href="{{ route('laporan-kinerja.edit', $rekapKinerjaLabkesda['id']) }}" class="btn btn-warning" title="Edit Laporan">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <button type="button" class="btn btn-danger" title="Hapus Laporan"
                                                data-bs-toggle="modal" data-bs-target="#deleteConfirmModal"
                                                data-delete-url="{{ route('laporan-kinerja.destroy', $rekapKinerjaLabkesda['id']) }}"
                                                data-delete-message="Apakah Anda yakin ingin menghapus Laporan Kinerja {{ $rekapKinerjaLabkesda['puskesmas_name'] }} ({{ $rekapKinerjaLabkesda['tahun'] }})?">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-light text-center fst-italic">
                        Belum ada laporan kinerja Labkesda untuk tahun {{ $tahun }}.
                        <a href="{{ route('laporan-kinerja.create.labkesda', ['tahun' => $tahun]) }}" class="btn btn-sm btn-outline-success ms-2">Buat Laporan Labkesda</a>
                    </div>
                @endif

                <hr>

                {{-- 2. Detail Indikator Labkesda --}}
                @if(isset($laporansLabkesda) && $laporansLabkesda && $laporansLabkesda->details->isNotEmpty())
                    <h6 class="mb-3 mt-4 fw-bold text-success">Detail Indikator Labkesda (Tahun {{ $tahun }})</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th style="width: 5%;" class="align-middle">No</th>
                                    <th class="align-middle">Indikator Capaian</th>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <th style="width: 4%;" class="align-middle">{{ \Carbon\Carbon::create()->month($i)->locale('id')->shortMonthName }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 0; @endphp
                                @foreach ($laporansLabkesda->details as $detail)
                                    {{-- ... LOGIKA TABLE ROW SEPERTI SEBELUMNYA ... --}}
                                    @php
                                        $indicatorName = $detail->indikator_name;
                                        $trimmedName = trim($indicatorName);
                                        $isMainItem = preg_match('/^\d+\./', $trimmedName);
                                        $rowClass = $isMainItem ? 'table-info fw-semibold' : '';
                                        if($isMainItem) $no++;
                                    @endphp
                                    <tr class="{{ $rowClass }}">
                                        <td class="text-center align-middle">{{ $isMainItem ? $no : '' }}</td>
                                        <td class="align-middle">{{ $trimmedName }}</td>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <td class="text-center align-middle p-1">
                                                {{ number_format($detail['bln_'.$i] ?? 0, 0, ',', '.') }}
                                            </td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div> {{-- Akhir Tab Pane Kinerja Labkesda --}}


            {{-- ============================================= --}}
            {{-- TAB PANE 3: REKAP ADMIN TU PUSKESMAS        --}}
            {{-- ============================================= --}}
            <div class="tab-pane fade" id="admin-tu-puskesmas-tab-pane" role="tabpanel" aria-labelledby="admin-tu-puskesmas-tab" tabindex="0">
                <h6 class="mb-3 fw-bold text-info">Rekap Laporan Administrasi & TU Puskesmas (Tahun {{ $tahun }})</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm" width="100%" cellspacing="0">
                        <thead class="bg-info text-white text-center">
                            <tr>
                                <th style="width: 5%;" class="align-middle">No</th>
                                <th class="align-middle">Nama Puskesmas</th>
                                <th style="width: 15%;" class="align-middle">Tahun Laporan</th>
                                <th style="width: 20%;" class="align-middle">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rekapAdminTuPuskesmas as $index => $item)
                            <tr>
                                <td class="text-center align-middle">{{ $index + 1 }}</td>
                                <td class="align-middle">{{ $item->puskesmas_name }}</td>
                                <td class="text-center align-middle">{{ $item->tahun }}</td>
                                <td class="text-center align-middle">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('administrasi-tu.index', ['puskesmas' => $item->puskesmas_name, 'tahun' => $item->tahun, 'jenis_laporan' => 'puskesmas']) }}"
                                            class="btn btn-primary" title="Lihat Detail">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                        <a href="{{ route('administrasi-tu.edit', ['puskesmas' => $item->puskesmas_name, 'tahun' => $item->tahun, 'jenis_laporan' => 'puskesmas']) }}" class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <button type="button" class="btn btn-danger" title="Hapus"
                                            data-bs-toggle="modal" data-bs-target="#deleteConfirmModal"
                                            data-delete-url="{{ route('administrasi-tu.destroy', ['puskesmas' => $item->puskesmas_name, 'tahun' => $item->tahun, 'jenis_laporan' => 'puskesmas']) }}"
                                            data-delete-message="Hapus Laporan Admin TU {{ $item->puskesmas_name }}?">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center fst-italic text-muted">Belum ada laporan Administrasi & TU Puskesmas yang masuk untuk tahun {{ $tahun }}.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>{{-- Akhir Tab Pane Admin TU Puskesmas --}}


            {{-- ============================================= --}}
            {{-- TAB PANE 4: REKAP ADMIN TU LABKESDA         --}}
            {{-- ============================================= --}}
            <div class="tab-pane fade" id="admin-tu-labkesda-tab-pane" role="tabpanel" aria-labelledby="admin-tu-labkesda-tab" tabindex="0">
                <h6 class="mb-3 fw-bold text-secondary">Rekap Laporan Administrasi & TU Labkesda (Tahun {{ $tahun }})</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm" width="100%" cellspacing="0">
                        <thead class="bg-secondary text-white text-center">
                            <tr>
                                <th class="align-middle">Nama Unit</th>
                                <th style="width: 15%;" class="align-middle">Tahun Laporan</th>
                                <th style="width: 20%;" class="align-middle">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($rekapAdminTuLabkesda)
                            <tr>
                                <td class="align-middle">{{ $rekapAdminTuLabkesda->puskesmas_name }}</td>
                                <td class="text-center align-middle">{{ $rekapAdminTuLabkesda->tahun }}</td>
                                <td class="text-center align-middle">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('administrasi-tu.index', ['puskesmas' => $rekapAdminTuLabkesda->puskesmas_name, 'tahun' => $rekapAdminTuLabkesda->tahun, 'jenis_laporan' => 'labkesda']) }}"
                                            class="btn btn-primary" title="Lihat Detail">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                        <a href="{{ route('administrasi-tu.edit', ['puskesmas' => $rekapAdminTuLabkesda->puskesmas_name, 'tahun' => $rekapAdminTuLabkesda->tahun, 'jenis_laporan' => 'labkesda']) }}" class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <button type="button" class="btn btn-danger" title="Hapus"
                                            data-bs-toggle="modal" data-bs-target="#deleteConfirmModal"
                                            data-delete-url="{{ route('administrasi-tu.destroy', ['puskesmas' => $rekapAdminTuLabkesda->puskesmas_name, 'tahun' => $rekapAdminTuLabkesda->tahun, 'jenis_laporan' => 'labkesda']) }}"
                                            data-delete-message="Hapus Laporan Admin TU Labkesda?">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @else
                            <tr>
                                <td colspan="3" class="text-center fst-italic text-muted">
                                    Belum ada laporan Administrasi & TU Labkesda yang masuk untuk tahun {{ $tahun }}.
                                    <a href="{{ route('administrasi-tu.create.labkesda', ['tahun' => $tahun]) }}" class="btn btn-sm btn-outline-secondary ms-2">Buat Laporan Labkesda</a>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>{{-- Akhir Tab Pane Admin TU Labkesda --}}


        </div> {{-- Akhir tab-content --}}

    </div> {{-- Akhir card-body --}}
</div> {{-- Akhir card --}}

{{-- Modal Konfirmasi Hapus (Universal) --}}
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus Laporan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="deleteModalMessage">
                Apakah Anda yakin ingin menghapus laporan ini? Tindakan ini tidak dapat dibatalkan.
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
{{-- Muat Chart.js dari CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Cek Ketersediaan Library
    if (typeof Chart === 'undefined') {
        console.error('Chart.js gagal dimuat. Periksa koneksi internet atau CDN.');
        alert('Gagal memuat grafik. Pastikan Anda terhubung ke internet.');
        return;
    }

    // 2. Ambil Data dari Controller (Safe JSON)
    const rawChartData = @json($chartData ?? null);
    
    // Debugging: Cek data di Console Browser (Tekan F12 -> Console)
    console.log("Data Grafik:", rawChartData);

    const ctxPuskesmas = document.getElementById('kinerjaChartPuskesmas');
    const indikatorFilter = document.getElementById('indikator_filter');
    let kinerjaChartPuskesmas = null; // Variabel chart global

    // 3. Fungsi Render Grafik
    function renderChart(filterLabel = 'all') {
        // Validasi elemen canvas dan data
        if (!ctxPuskesmas || !rawChartData || !rawChartData.datasets) return;

        // Filter Dataset
        let datasetsToDraw = rawChartData.datasets;
        if (filterLabel && filterLabel !== 'all') {
            datasetsToDraw = rawChartData.datasets.filter(ds => ds.label === filterLabel);
        }

        // Hancurkan chart lama jika ada (untuk mencegah tumpang tindih)
        if (kinerjaChartPuskesmas) {
            kinerjaChartPuskesmas.destroy();
        }

        // Buat Chart Baru
        kinerjaChartPuskesmas = new Chart(ctxPuskesmas, {
            type: 'line',
            data: {
                labels: rawChartData.labels, // Label sumbu X (Bulan)
                datasets: datasetsToDraw
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Agar mengikuti tinggi container 450px
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: { boxWidth: 15 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100, // Karena persen, maksimal 100 (opsional, bisa dihapus)
                        title: { display: true, text: 'Capaian (%)' }
                    }
                }
            }
        });
    }

    // 4. Inisialisasi Awal
    // Beri sedikit delay agar Tab Bootstrap selesai rendering layoutnya
    setTimeout(() => {
        if (rawChartData) {
            const initialFilter = indikatorFilter ? indikatorFilter.value : 'all';
            renderChart(initialFilter);
        }
    }, 300);

    // 5. Event Listener untuk Dropdown Filter
    if (indikatorFilter) {
        indikatorFilter.addEventListener('change', function() {
            renderChart(this.value);
        });
    }

    // 6. Fix Grafik Hilang saat Ganti Tab
    // Saat tab Kinerja Puskesmas diklik, render ulang grafik
    const tabEl = document.querySelector('button[data-bs-target="#kinerja-puskesmas-tab-pane"]');
    if (tabEl) {
        tabEl.addEventListener('shown.bs.tab', function (event) {
            if (rawChartData) {
                const currentFilter = indikatorFilter ? indikatorFilter.value : 'all';
                renderChart(currentFilter);
            }
        });
    }

    // --- Logika Modal Hapus ---
    const deleteModal = document.getElementById('deleteConfirmModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const deleteUrl = button.getAttribute('data-delete-url');
            const message = button.getAttribute('data-delete-message');
            const modalMessage = deleteModal.querySelector('#deleteModalMessage');
            const deleteForm = deleteModal.querySelector('#deleteForm');
            if(modalMessage) modalMessage.textContent = message;
            if(deleteForm) deleteForm.action = deleteUrl;
        });
    }
});
</script>
@endpush