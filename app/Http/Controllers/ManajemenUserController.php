<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SasaranPuskesmas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ManajemenUserController extends Controller
{
    /**
     * Constructor untuk Cek Keamanan Admin
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || Auth::user()->role !== 'admin') {
                abort(403, 'Akses Ditolak. Anda bukan Administrator.');
            }
            return $next($request);
        });
    }

    /**
     * Tampilkan daftar semua user.
     */
    public function index()
    {
        $users = User::where('id', '!=', auth()->id())->orderBy('name')->paginate(20);
        return view('manajemen_user.index', compact('users'));
    }

    /**
     * Tampilkan form untuk membuat user baru.
     */
    public function create()
    {
        $puskesmasNames = SasaranPuskesmas::distinct()->orderBy('puskesmas')->pluck('puskesmas');
        $roles = ['puskesmas', 'labkesda', 'admin'];
        return view('manajemen_user.create', compact('puskesmasNames', 'roles'));
    }

    /**
     * Simpan user baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['puskesmas', 'labkesda', 'admin'])],
            'nama_puskesmas' => 'nullable|required_if:role,puskesmas|string|max:255',
        ]);

        $namaPuskesmas = ($validated['role'] === 'puskesmas') ? $validated['nama_puskesmas'] : null;

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'], // Mutator di Model AKAN hash ini
            'role' => $validated['role'],
            'nama_puskesmas' => $namaPuskesmas, 
        ]);

        return redirect()->route('manajemen-user.index')
                         ->with('success', 'User baru berhasil ditambahkan.');
    }

    /**
     * Tampilkan form untuk mengedit user yang ada.
     */
    public function edit(User $manajemen_user) 
    {
        $user = $manajemen_user;
        $puskesmasNames = SasaranPuskesmas::distinct()->orderBy('puskesmas')->pluck('puskesmas');
        $roles = ['puskesmas', 'labkesda', 'admin'];
        return view('manajemen_user.edit', compact('user', 'puskesmasNames', 'roles'));
    }

    /**
     * Update user di database.
     */
    public function update(Request $request, User $manajemen_user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $manajemen_user->id,
            'password' => 'nullable|string|min:8|confirmed', 
            'role' => ['required', Rule::in(['puskesmas', 'labkesda', 'admin'])],
            'nama_puskesmas' => 'nullable|required_if:role,puskesmas|string|max:255',
        ]);
        
        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        // Ini bagian penting untuk reset password
        if ($request->filled('password')) {
            $data['password'] = $validated['password']; // Mutator di Model AKAN hash ini
        }

        if ($validated['role'] === 'puskesmas') {
             $data['nama_puskesmas'] = $validated['nama_puskesmas'];
        } else {
             $data['nama_puskesmas'] = null;
        }

        $manajemen_user->update($data);

        return redirect()->route('manajemen-user.index')
                         ->with('success', 'Data user berhasil diperbarui.');
    }

    /**
     * Hapus user dari database.
     */
    public function destroy(User $manajemen_user)
    {
        $nama = $manajemen_user->name;
        $manajemen_user->delete();

        return redirect()->route('manajemen-user.index')
                         ->with('success', "User $nama berhasil dihapus.");
    }
}