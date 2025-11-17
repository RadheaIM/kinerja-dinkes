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
            {{-- Simpan ID laporan grafik --}}
            @if(request('laporan_id'))
                <input type="hidden" name="laporan_id" value="{{ request('laporan_id') }}">
            @endif
             {{-- Simpan Tab Aktif --}}
             <input type="hidden" name="active_tab" id="active_tab_input" value="{{ request('active_tab', '#kinerja-puskesmas-tab-pane') }}">

            <div class="row align-items-end">
                {{-- Filter Bulan --}}
                <div class="col-md-3">
                    <label for="bulan">Filter Bulan (Ringkasan Kinerja)</label>
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
                    <label for="tahun">Filter Tahun</label>
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
                <h6 class="mb-3">Rekap Ringkas Kinerja Puskesmas ({{ $bulan && isset($bulanNames[$bulan]) ? $bulanNames[$bulan] : 'Akumulasi' }} {{ $tahun }})</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped table-hover table-sm" width="100%" cellspacing="0">
                         <thead class="bg-primary text-white text-center">
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th>Nama Puskesmas</th>
                                <th style="width: 10%;">Tahun</th>
                                {{-- === PERUBAHAN ALIGNMENT HEADER === --}}
                                <th style="width: 15%;" class="text-center">Target Total</th>
                                <th style="width: 15%;" class="text-center">Realisasi ({{ $bulan ? 'Bln Ini' : 'Akumulasi' }})</th>
                                <th style="width: 15%;" class="text-center">Persentase Capaian</th>
                                {{-- ================================== --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rekapKinerjaPuskesmas as $index => $data)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $data['puskesmas_name'] }}</td>
                                <td class="text-center">{{ $data['tahun'] }}</td>
                                {{-- === PERUBAHAN ALIGNMENT DATA === --}}
                                <td class="text-center">{{ number_format($data['total_target'] ?? 0, 0, ',', '.') }}</td>
                                <td class="text-center">{{ number_format($data['total_realisasi'] ?? 0, 0, ',', '.') }}</td>
                                {{-- ============================== --}}
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
                <h6 class="mb-1">Grafik Tren Bulanan {{ $chartTitle ? $chartTitle.' ('.$chartYear.')' : '(Pilih Laporan dari tabel di bawah)' }}</h6>
                <small class="text-muted d-block mb-3">Klik nama indikator di bawah grafik untuk menampilkan/menyembunyikan garisnya.</small>
                @if($chartData && !empty($chartData['datasets']))
                    <div style="height: 450px; margin-bottom: 2rem; position: relative;">
                        <canvas id="kinerjaChartPuskesmas"></canvas>
                    </div>
                @elseif($selectedLaporanGrafik)
                    <div class="alert alert-warning">Data detail tidak ditemukan atau kosong untuk laporan {{ $chartTitle }} tahun {{ $chartYear }}. Grafik tidak dapat ditampilkan.</div>
                @else
                    <div class="alert alert-info">Pilih laporan dari tabel di bawah dan klik <span class="badge bg-info"><i class="fas fa-chart-line"></i> Tren</span> untuk menampilkan grafik.</div>
                @endif

                <hr>

                 {{-- 1.C. Daftar Detail Laporan Kinerja Puskesmas --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="m-0">Daftar Laporan Kinerja Puskesmas (Tahun {{ $tahun }})</h6>
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
                                 <td class="text-center">
                                     <a href="{{ route('rekap.index', ['tahun' => $tahun, 'bulan' => $bulan, 'laporan_id' => $laporan->id, 'active_tab' => '#kinerja-puskesmas-tab-pane']) }}"
                                        class="btn btn-sm btn-info my-1 me-1" title="Lihat Tren Grafik">
                                         <i class="fas fa-chart-line"></i> Tren
                                     </a>
                                     <a href="{{ route('laporan-kinerja.edit', $laporan->id) }}" class="btn btn-sm btn-warning my-1 me-1" title="Edit Laporan">
                                         <i class="fas fa-edit"></i> Edit
                                     </a>
                                     <button type="button" class="btn btn-sm btn-danger my-1" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal"
                                             data-delete-url="{{ route('laporan-kinerja.destroy', $laporan->id) }}"
                                             data-delete-message="Apakah Anda yakin ingin menghapus Laporan Kinerja Puskesmas {{ $laporan->puskesmas_name }} ({{ $laporan->tahun }})?">
                                         <i class="fas fa-trash"></i> Hapus
                                     </button>
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
                 <h6 class="mb-3">Rekap Kinerja Labkesda ({{ $bulan && isset($bulanNames[$bulan]) ? $bulanNames[$bulan] : 'Akumulasi' }} {{ $tahun }})</h6>

                 {{-- 1. Rekap Ringkas Labkesda --}}
                 @if($rekapKinerjaLabkesda)
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-striped table-hover table-sm" width="100%" cellspacing="0">
                             <thead class="bg-success text-white text-center">
                                <tr>
                                    <th>Nama Unit</th>
                                    <th style="width: 15%;">Tahun</th>
                                    <th style="width: 25%;">Jenis Laporan</th>
                                     {{-- === PERUBAHAN ALIGNMENT HEADER === --}}
                                    <th style="width: 25%;" class="text-center">Total Realisasi ({{ $bulan ? 'Bln Ini' : 'Akumulasi' }})</th>
                                    {{-- ================================== --}}
                                    <th style="width: 20%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $rekapKinerjaLabkesda['puskesmas_name'] }}</td>
                                    <td class="text-center">{{ $rekapKinerjaLabkesda['tahun'] }}</td>
                                    <td>{{ $rekapKinerjaLabkesda['jenis_laporan'] }}</td>
                                     {{-- === PERUBAHAN ALIGNMENT DATA === --}}
                                    <td class="text-center">{{ number_format($rekapKinerjaLabkesda['total_realisasi'] ?? 0, 0, ',', '.') }}</td>
                                     {{-- ============================== --}}
                                     <td class="text-center">
                                         <a href="{{ route('laporan-kinerja.edit', $rekapKinerjaLabkesda['id']) }}" class="btn btn-sm btn-warning my-1 me-1" title="Edit Laporan">
                                             <i class="fas fa-edit"></i> Edit
                                         </a>
                                          <button type="button" class="btn btn-sm btn-danger my-1" title="Hapus Laporan"
                                                  data-bs-toggle="modal" data-bs-target="#deleteConfirmModal"
                                                  data-delete-url="{{ route('laporan-kinerja.destroy', $rekapKinerjaLabkesda['id']) }}"
                                                  data-delete-message="Apakah Anda yakin ingin menghapus Laporan Kinerja {{ $rekapKinerjaLabkesda['puskesmas_name'] }} ({{ $rekapKinerjaLabkesda['tahun'] }})?">
                                             <i class="fas fa-trash"></i> Hapus
                                         </button>
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
                 @if($laporansLabkesda && $laporansLabkesda->details->isNotEmpty())
                    <h6 class="mb-3 mt-4">Detail Indikator Labkesda (Tahun {{ $tahun }})</h6>
                    <div class="table-responsive">
                         <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                             <thead class="bg-light text-center">
                                 <tr>
                                     <th style="width: 5%;">No</th>
                                     <th>Indikator Capaian</th>
                                     @for ($i = 1; $i <= 12; $i++)
                                         <th style="width: 4%;">{{ \Carbon\Carbon::create()->month($i)->locale('id')->shortMonthName }}</th>
                                     @endfor
                                 </tr>
                             </thead>
                             <tbody>
                                 @foreach ($laporansLabkesda->details as $index => $detail)
                                    @php
                                        $indicatorName = $detail->indikator_name;
                                        $trimmedName = trim($indicatorName);
                                        $isSectionTitle = !preg_match('/^\d+\./', $trimmedName) && !Str::startsWith($trimmedName, ['-', 'a.', 'b.', 'c.', 'd.', 'e.']);
                                        $isSubItem = Str::startsWith($trimmedName, ['-', '  ']);
                                        $isSubSection = Str::startsWith($trimmedName, ['a.', 'b.', 'c.', 'd.', 'e.']);
                                        $isSubSubItem = Str::startsWith($trimmedName, '  - ');
                                        $isMainItem = preg_match('/^\d+\./', $trimmedName);
                                    @endphp
                                    <tr class="{{ $isSectionTitle || $isSubSection ? 'table-secondary fw-bold' : '' }}">
                                        <td class="text-center align-middle">
                                            @if ($isMainItem)
                                                {{ Str::before($trimmedName, '.') }}
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            @if ($isSectionTitle) <strong class="text-primary">{{ $trimmedName }}</strong>
                                            @elseif ($isSubSection) <span class="ms-1"><strong>{{ $trimmedName }}</strong></span>
                                            @elseif ($isSubSubItem) <span class="ms-5">{{ trim(Str::after($trimmedName, '- ')) }}</span>
                                            @elseif ($isSubItem) <span class="ms-3">{{ trim(Str::after($trimmedName, '- ')) }}</span>
                                            @else {{ $trimmedName }}
                                            @endif
                                        </td>
                                        {{-- Capaian Bulanan --}}
                                        @for ($i = 1; $i <= 12; $i++)
                                            <td class="text-center align-middle p-1"> {{-- Ganti ke text-center --}}
                                                 @if (!$isSectionTitle && !$isSubSection)
                                                    {{ number_format($detail['bln_'.$i] ?? 0, 0, ',', '.') }}
                                                 @else
                                                    -
                                                 @endif
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
                 <h6 class="mb-3">Rekap Laporan Administrasi & TU Puskesmas (Tahun {{ $tahun }})</h6>
                 <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm" width="100%" cellspacing="0">
                        <thead class="bg-info text-white text-center">
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th>Nama Puskesmas</th>
                                <th style="width: 15%;">Tahun Laporan</th>
                                <th style="width: 20%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rekapAdminTuPuskesmas as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $item->puskesmas_name }}</td>
                                <td class="text-center">{{ $item->tahun }}</td>
                                <td class="text-center">
                                    <a href="{{ route('administrasi-tu.index', ['puskesmas' => $item->puskesmas_name, 'tahun' => $item->tahun, 'jenis_laporan' => 'puskesmas']) }}"
                                       class="btn btn-sm btn-primary my-1 me-1" title="Lihat Detail Laporan">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                     <a href="{{ route('administrasi-tu.edit', ['puskesmas' => $item->puskesmas_name, 'tahun' => $item->tahun]) }}" class="btn btn-sm btn-warning my-1 me-1" title="Edit Laporan">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger my-1" title="Hapus Laporan"
                                            data-bs-toggle="modal" data-bs-target="#deleteConfirmModal"
                                            data-delete-url="{{ route('administrasi-tu.destroy', ['puskesmas' => $item->puskesmas_name, 'tahun' => $item->tahun]) }}"
                                            data-delete-message="Apakah Anda yakin ingin menghapus Laporan Admin TU Puskesmas {{ $item->puskesmas_name }} ({{ $item->tahun }})? File bukti dukung juga akan dihapus.">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
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
                 <h6 class="mb-3">Rekap Laporan Administrasi & TU Labkesda (Tahun {{ $tahun }})</h6>
                 <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm" width="100%" cellspacing="0">
                        <thead class="bg-secondary text-white text-center"> {{-- Warna Beda --}}
                            <tr>
                                <th>Nama Unit</th>
                                <th style="width: 15%;">Tahun Laporan</th>
                                <th style="width: 20%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($rekapAdminTuLabkesda)
                            <tr>
                                <td>{{ $rekapAdminTuLabkesda->puskesmas_name }}</td>
                                <td class="text-center">{{ $rekapAdminTuLabkesda->tahun }}</td>
                                <td class="text-center">
                                    <a href="{{ route('administrasi-tu.index', ['puskesmas' => $rekapAdminTuLabkesda->puskesmas_name, 'tahun' => $rekapAdminTuLabkesda->tahun, 'jenis_laporan' => 'labkesda']) }}"
                                       class="btn btn-sm btn-primary my-1 me-1" title="Lihat Detail Laporan">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                     <a href="{{ route('administrasi-tu.edit', ['puskesmas' => $rekapAdminTuLabkesda->puskesmas_name, 'tahun' => $rekapAdminTuLabkesda->tahun]) }}" class="btn btn-sm btn-warning my-1 me-1" title="Edit Laporan">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger my-1" title="Hapus Laporan"
                                            data-bs-toggle="modal" data-bs-target="#deleteConfirmModal"
                                            data-delete-url="{{ route('administrasi-tu.destroy', ['puskesmas' => $rekapAdminTuLabkesda->puskesmas_name, 'tahun' => $rekapAdminTuLabkesda->tahun]) }}"
                                            data-delete-message="Apakah Anda yakin ingin menghapus Laporan Admin TU Labkesda ({{ $rekapAdminTuLabkesda->tahun }})? File bukti dukung juga akan dihapus.">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
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
    {{-- ... Kode Modal Hapus (Sama seperti sebelumnya) ... --}}
     <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="deleteModalMessage">
                Apakah Anda yakin ingin menghapus laporan ini? Tindakan ini tidak dapat dibatalkan.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" action=""> {{-- Action diisi oleh JS --}}
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
{{-- Script untuk Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // === Logika untuk Grafik Kinerja Puskesmas ===
    const ctxPuskesmas = document.getElementById('kinerjaChartPuskesmas');
    @if (!empty($chartData) && !empty($chartData['datasets']))
        const chartDataPuskesmas = @json($chartData);
        if (ctxPuskesmas && chartDataPuskesmas) {
            new Chart(ctxPuskesmas.getContext('2d'), {
                type: 'line',
                data: chartDataPuskesmas,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true } },
                    plugins: { legend: { position: 'bottom' } },
                    interaction: { mode: 'index', intersect: false },
                }
            });
        }
    @endif

    // === Logika untuk Modal Hapus ===
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

   // === Logika untuk Mengingat Tab Aktif ===
    const activeTabInput = document.getElementById('active_tab_input');
    const tabTriggers = document.querySelectorAll('#rekapTab button[data-bs-toggle="tab"]');
    const defaultTabTarget = '#kinerja-puskesmas-tab-pane'; // Default tab

    // Function to show tab and update storage/input
    function showTab(targetId) {
        const triggerEl = document.querySelector(`button[data-bs-target="${targetId}"]`);
        if (triggerEl) {
            const tab = new bootstrap.Tab(triggerEl);
            tab.show();
            localStorage.setItem('activeRekapTab', targetId);
            if (activeTabInput) activeTabInput.value = targetId;
        } else {
             // Fallback to default if target doesn't exist
            showTab(defaultTabTarget);
        }
    }

    // Add event listeners to tab buttons
    tabTriggers.forEach(triggerEl => {
        triggerEl.addEventListener('shown.bs.tab', event => {
            const activeTabTarget = event.target.getAttribute('data-bs-target');
            // Update storage and hidden input when a tab is shown
             localStorage.setItem('activeRekapTab', activeTabTarget);
            if(activeTabInput) activeTabInput.value = activeTabTarget;
             // Update URL hash (opsional, bisa membantu bookmark/sharing)
             // window.location.hash = activeTabTarget;
        });
    });

    // Determine initial tab on page load
    const urlParams = new URLSearchParams(window.location.search);
    const urlTab = urlParams.get('active_tab'); // Get tab from URL parameter first
    const savedTab = localStorage.getItem('activeRekapTab'); // Then check localStorage
    const hashTab = window.location.hash; // Check URL hash last

    // Prioritize URL parameter, then hash, then localStorage, then default
    let initialTabTarget = defaultTabTarget;
    if (urlTab && document.querySelector(`button[data-bs-target="${urlTab}"]`)) {
        initialTabTarget = urlTab;
    } else if (hashTab && document.querySelector(`button[data-bs-target="${hashTab}"]`)) {
         initialTabTarget = hashTab;
    } else if (savedTab && document.querySelector(`button[data-bs-target="${savedTab}"]`)) {
        initialTabTarget = savedTab;
    }

    // Show the determined initial tab
    showTab(initialTabTarget);

});
</script>
@endpush

