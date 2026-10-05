<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Kelola Pengguna</h2>
            <p class="text-sm text-slate-500">Tambah akun pegawai, atur peran, dan reset password</p>
        </div>
    </x-slot>

    @php
        $field = 'block w-full rounded-lg border-slate-300 text-sm shadow-sm placeholder:text-slate-400 focus:border-blue-600 focus:ring-blue-600';
    @endphp

    <div class="space-y-6 px-4 py-6 sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60">
            <h3 class="font-semibold text-slate-900">Tambah akun baru</h3>
            <p class="mt-1 text-xs text-slate-500">Berikan email dan password awal ke pegawai. Pegawai bisa menggantinya sendiri lewat menu Profil.</p>

            <form method="POST" action="{{ route('pengguna.store') }}" class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-5">
                @csrf
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama lengkap" class="{{ $field }}">
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" class="{{ $field }}">
                <input type="text" name="password" placeholder="Password awal (min. 8)" class="{{ $field }}">
                <select name="role" class="{{ $field }}">
                    <option value="pegawai" @selected(old('role') === 'pegawai')>Pegawai</option>
                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                </select>
                <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">Tambah akun</button>
            </form>
        </div>

        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60">
            <div class="border-b border-slate-100 p-6">
                <h3 class="font-semibold text-slate-900">Daftar akun ({{ $pengguna->count() }})</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Peran</th>
                            <th class="px-4 py-3">Reset password</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($pengguna as $u)
                            <tr class="align-middle hover:bg-slate-50/70">
                                <td class="px-4 py-3 font-medium text-slate-800">
                                    {{ $u->name }}
                                    @if ($u->id === auth()->id())
                                        <span class="ml-1 rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800">Anda</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $u->email }}</td>
                                <td class="px-4 py-3">
                                    @if ($u->id === auth()->id())
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $u->role === 'admin' ? 'bg-violet-100 text-violet-800' : 'bg-slate-100 text-slate-700' }}">{{ ucfirst($u->role) }}</span>
                                    @else
                                        <form method="POST" action="{{ route('pengguna.role', $u) }}">
                                            @csrf
                                            @method('PUT')
                                            <select name="role" onchange="this.form.submit()" class="rounded-lg border-slate-300 py-1 text-xs shadow-sm focus:border-blue-600 focus:ring-blue-600">
                                                <option value="pegawai" @selected($u->role === 'pegawai')>Pegawai</option>
                                                <option value="admin" @selected($u->role === 'admin')>Admin</option>
                                            </select>
                                        </form>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('pengguna.password', $u) }}" class="flex gap-1">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="password" placeholder="Password baru" class="w-36 rounded-lg border-slate-300 py-1 text-xs shadow-sm focus:border-blue-600 focus:ring-blue-600">
                                        <button class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700 hover:bg-slate-200">Simpan</button>
                                    </form>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if ($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('pengguna.destroy', $u) }}" class="inline"
                                              onsubmit="return confirm('Hapus akun {{ $u->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-md bg-red-50 px-2.5 py-1 text-xs font-medium text-red-700 hover:bg-red-100">Hapus</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-300">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>