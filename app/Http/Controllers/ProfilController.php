<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password; // Untuk validasi password yang kuat

class ProfilController extends Controller
{
    /**
     * Tampilkan halaman profil pengguna.
     * (Method ini mungkin sudah ada jika Anda mengikuti struktur sebelumnya)
     */
    public function index()
    {
        return view('profil.index'); // Tampilkan view profil/index.blade.php
    }

    /**
     * Tampilkan form untuk mengedit profil pengguna.
     */
    public function edit()
    {
        $user = Auth::user(); // Ambil data user yang sedang login
        return view('profil.edit', compact('user')); // Kirim data user ke view edit
    }

    /**
     * Update data profil pengguna.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // 1. Validasi Input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id, // Email unik kecuali untuk user ini sendiri
            'current_password' => 'nullable|required_with:new_password|current_password', // Password saat ini wajib jika ingin ganti password
            'new_password' => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()], // Validasi password baru (jika diisi)
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
            'current_password.required_with' => 'Password saat ini wajib diisi jika ingin mengubah password.',
            'current_password.current_password' => 'Password saat ini salah.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            // Pesan validasi Password::min dll akan otomatis dari Laravel
        ]);

        // 2. Update Data User
        $user->name = $request->name;
        $user->email = $request->email;

        // 3. Update Password (jika diisi)
        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
        }

        // 4. Simpan Perubahan
        $user->save();

        // 5. Redirect kembali ke halaman profil dengan pesan sukses
        return redirect()->route('profil')->with('status', 'Profil berhasil diperbarui!');
    }
}
