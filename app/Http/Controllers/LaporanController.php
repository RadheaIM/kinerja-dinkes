<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Laporan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    /**
     * Tampilkan daftar laporan milik pegawai yang login.
     */
    public function index()
    {
        // Hanya tampilkan laporan milik pegawai yang sedang login
        $laporans = Laporan::where('pegawai_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('laporan.index', compact('laporans'));
    }

    /**
     * Form untuk menambah laporan baru.
     */
    public function create()
    {
        return view('laporan.create');
    }

    /**
     * Simpan laporan baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'unit'  => 'required|string|max:255',
            'file'  => 'required|mimes:pdf,doc,docx,xlsx,xls|max:5120', // max 5MB
        ]);

        // Simpan file ke storage/app/public/laporan
        $path = $request->file('file')->store('laporan', 'public');

        // Simpan ke database
        Laporan::create([
            'judul'      => $request->judul,
            'unit'       => $request->unit,
            'file'       => $path,
            'pegawai_id' => Auth::id(), // gunakan pegawai_id sesuai model
        ]);

        return redirect()
            ->route('laporan.index')
            ->with('success', '✅ Laporan berhasil diupload!');
    }

    /**
     * Tampilkan detail laporan.
     */
    public function show($id)
    {
        $laporan = Laporan::findOrFail($id);
        return view('laporan.show', compact('laporan'));
    }

    /**
     * Form edit laporan.
     */
    public function edit($id)
    {
        $laporan = Laporan::findOrFail($id);
        return view('laporan.edit', compact('laporan'));
    }

    /**
     * Update laporan di database.
     */
    public function update(Request $request, $id)
    {
        $laporan = Laporan::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'unit'  => 'required|string|max:255',
            'file'  => 'nullable|mimes:pdf,doc,docx,xlsx,xls|max:5120',
        ]);

        // Jika ada file baru, hapus file lama dan upload baru
        if ($request->hasFile('file')) {
            if (Storage::disk('public')->exists($laporan->file)) {
                Storage::disk('public')->delete($laporan->file);
            }

            $path = $request->file('file')->store('laporan', 'public');
            $laporan->file = $path;
        }

        $laporan->update([
            'judul' => $request->judul,
            'unit'  => $request->unit,
            'file'  => $laporan->file,
        ]);

        return redirect()
            ->route('laporan.index')
            ->with('success', '✏️ Laporan berhasil diperbarui!');
    }

    /**
     * Download file laporan.
     */
    public function download($id)
    {
        $laporan = Laporan::findOrFail($id);

        if (!Storage::disk('public')->exists($laporan->file)) {
            return redirect()->back()->with('error', '❌ File tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($laporan->file);
    }

    /**
     * Hapus laporan dari database dan storage.
     */
    public function destroy($id)
    {
        $laporan = Laporan::findOrFail($id);

        if (Storage::disk('public')->exists($laporan->file)) {
            Storage::disk('public')->delete($laporan->file);
        }

        $laporan->delete();

        return redirect()
            ->route('laporan.index')
            ->with('success', '🗑️ Laporan berhasil dihapus!');
    }
}
