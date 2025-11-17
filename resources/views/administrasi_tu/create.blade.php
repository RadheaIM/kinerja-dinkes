@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h2 class="text-2xl font-semibold mb-4">Tambah Data Administrasi & Tata Usaha</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('administrasitu.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 bg-white p-6 rounded-lg shadow">
        @csrf

        {{-- Puskesmas --}}
        <div>
            <label class="block font-medium">Nama Puskesmas</label>
            <input type="text" name="puskesmas_name" value="{{ old('puskesmas_name') }}" class="w-full border rounded p-2" placeholder="Nama Puskesmas">
            @error('puskesmas_name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        {{-- Tahun --}}
        <div>
            <label class="block font-medium">Tahun</label>
            <input type="number" name="tahun" value="{{ old('tahun', date('Y')) }}" class="w-full border rounded p-2">
            @error('tahun')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        {{-- Jenis Laporan --}}
        <div>
            <label class="block font-medium">Jenis Laporan</label>
            <input type="text" name="jenis_laporan" value="{{ old('jenis_laporan') }}" class="w-full border rounded p-2">
            @error('jenis_laporan')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        {{-- Jenis Layanan SPM --}}
        <div>
            <label class="block font-medium">Jenis Layanan SPM</label>
            <input type="text" name="jenis_layanan_spm" value="{{ old('jenis_layanan_spm') }}" class="w-full border rounded p-2">
            @error('jenis_layanan_spm')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        {{-- Indikator --}}
        <div>
            <label class="block font-medium">Indikator</label>
            <input type="text" name="indikator" value="{{ old('indikator') }}" class="w-full border rounded p-2">
            @error('indikator')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        {{-- Target --}}
        <div>
            <label class="block font-medium">Target</label>
            <input type="text" name="target" value="{{ old('target') }}" class="w-full border rounded p-2">
            @error('target')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        {{-- Capaian Bulanan --}}
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            @for ($i = 1; $i <= 12; $i++)
                <div>
                    <label class="block font-medium">Bulan {{ $i }}</label>
                    <select name="bln_{{ $i }}" class="w-full border rounded p-2">
                        <option value="">-- Pilih --</option>
                        <option value="Ada" {{ old('bln_'.$i) == 'Ada' ? 'selected' : '' }}>Ada</option>
                        <option value="Tidak Ada" {{ old('bln_'.$i) == 'Tidak Ada' ? 'selected' : '' }}>Tidak Ada</option>
                    </select>
                </div>
            @endfor
        </div>

        {{-- Link Bukti Dukung --}}
        <div>
            <label class="block font-medium">Link Bukti Dukung (Opsional)</label>
            <input type="url" name="link_bukti_dukung[]" placeholder="https://contoh.com" class="w-full border rounded p-2 mb-2">
            <button type="button" id="add-link" class="bg-blue-500 text-white px-3 py-1 rounded">+ Tambah Link</button>
        </div>

        {{-- File Bukti Dukung --}}
        <div>
            <label class="block font-medium">File Bukti Dukung (Opsional)</label>
            <input type="file" name="file_bukti_dukung[]" class="w-full border rounded p-2" multiple>
            <p class="text-sm text-gray-500">*Format: PDF, DOCX, XLSX, JPG, PNG (Maks. 2 MB per file)</p>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700">
                Simpan Data
            </button>
        </div>
    </form>
</div>

{{-- JS untuk tambah link bukti --}}
<script>
    document.getElementById('add-link').addEventListener('click', function () {
        const input = document.createElement('input');
        input.type = 'url';
        input.name = 'link_bukti_dukung[]';
        input.placeholder = 'https://contoh.com';
        input.className = 'w-full border rounded p-2 mb-2';
        this.parentNode.insertBefore(input, this);
    });
</script>
@endsection
