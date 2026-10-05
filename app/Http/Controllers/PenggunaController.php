<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    private function khususAdmin(): void
    {
        abort_unless(auth()->user()->role === 'admin', 403, 'Halaman ini khusus administrator.');
    }

    public function index()
    {
        $this->khususAdmin();
        $pengguna = User::orderBy('name')->get();

        return view('pengguna.index', compact('pengguna'));
    }

    public function store(Request $request)
    {
        $this->khususAdmin();

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role'     => ['required', Rule::in(['admin', 'pegawai'])],
        ]);

        $u = new User();
        $u->name = $data['name'];
        $u->email = $data['email'];
        $u->password = Hash::make($data['password']);
        $u->role = $data['role'];
        $u->email_verified_at = now();
        $u->save();

        return back()->with('success', 'Akun ' . $u->name . ' berhasil dibuat.');
    }

    public function password(Request $request, User $pengguna)
    {
        $this->khususAdmin();

        $request->validate(['password' => 'required|string|min:8']);

        $pengguna->password = Hash::make($request->password);
        $pengguna->save();

        return back()->with('success', 'Password ' . $pengguna->name . ' berhasil direset.');
    }

    public function role(Request $request, User $pengguna)
    {
        $this->khususAdmin();

        if ($pengguna->id === auth()->id()) {
            return back()->withErrors(['role' => 'Peran akun sendiri tidak bisa diubah.']);
        }

        $request->validate(['role' => ['required', Rule::in(['admin', 'pegawai'])]]);

        $pengguna->role = $request->role;
        $pengguna->save();

        return back()->with('success', 'Peran ' . $pengguna->name . ' diubah menjadi ' . $pengguna->role . '.');
    }

    public function destroy(User $pengguna)
    {
        $this->khususAdmin();

        if ($pengguna->id === auth()->id()) {
            return back()->withErrors(['hapus' => 'Akun yang sedang dipakai tidak bisa dihapus.']);
        }

        $nama = $pengguna->name;
        $pengguna->delete();

        return back()->with('success', 'Akun ' . $nama . ' dihapus.');
    }
}