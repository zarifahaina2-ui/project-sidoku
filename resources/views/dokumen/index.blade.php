<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Data Dokumen</h2>
                <p class="text-sm text-slate-500">Kelola arsip surat dan dokumen kedinasan</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('dokumen.import') }}" class="rounded-lg border border-blue-700 bg-white px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-50">Impor Excel</a>
                <a href="{{ route('dokumen.create') }}" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">+ Tambah Dokumen</a>
            </div>
        </div>
    </x-slot>

    @php
        $cWarna = ['bg-blue-100 text-blue-800', 'bg-emerald-100 text-emerald-800', 'bg-amber-100 text-amber-800', 'bg-rose-100 text-rose-800', 'bg-violet-100 text-violet-800', 'bg-cyan-100 text-cyan-800', 'bg-pink-100 text-pink-800', 'bg-lime-100 text-lime-800', 'bg-orange-100 text-orange-800', 'bg-slate-200 text-slate-800'];
    @endphp

    <div class="space-y-4 px-4 py-6 sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif

        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60">
            <form method="GET" action="{{ route('dokumen.index') }}" class="flex flex-wrap items-center gap-2 border-b border-slate-100 p-4">
                <input type="text" name="q" value="{{ $q }}" placeholder="Cari nomor, perihal, atau asal/tujuan..."
                       class="w-full rounded-lg border-slate-300 text-sm shadow-sm placeholder:text-slate-400 focus:border-blue-600 focus:ring-blue-600 sm:w-80">
                <select name="jenis" class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
                    <option value="">Semua jenis</option>
                    @foreach ($jenisList as $j)
                        <option value="{{ $j }}" @selected($jenis === $j)>{{ $j }}</option>
                    @endforeach
                </select>
                <button class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">Cari</button>
                <a href="{{ route('dokumen.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Reset</a>
                <span class="ml-auto text-xs text-slate-500">
                    Menampilkan {{ $dokumen->firstItem() ?? 0 }} sampai {{ $dokumen->lastItem() ?? 0 }} dari {{ $dokumen->total() }} dokumen
                </span>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Nomor</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Jenis</th>
                            <th class="px-4 py-3">Perihal</th>
                            <th class="px-4 py-3">Asal/Tujuan</th>
                            <th class="px-4 py-3">Cluster</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($dokumen as $i => $d)
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="px-4 py-3 text-slate-500">{{ $dokumen->firstItem() + $i }}</td>
                                <td class="px-4 py-3 font-medium text-slate-800">{{ $d->nomor_dokumen }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $d->tanggal_dokumen->format('d-m-Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="whitespace-nowrap rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">{{ $d->jenis_dokumen }}</span>
                                </td>
                                <td class="max-w-md px-4 py-3 text-slate-700">{{ $d->perihal }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $d->asal_tujuan }}</td>
                                <td class="px-4 py-3">
                                    @if ($d->cluster)
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $cWarna[($d->cluster - 1) % 10] }}">C{{ $d->cluster }}</span>
@if (!empty($namaCluster[$d->cluster]))
    <div class="mt-1 max-w-[9rem] text-[11px] leading-snug text-slate-500">{{ $namaCluster[$d->cluster] }}</div>
@endif
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <a href="{{ route('dokumen.show', $d) }}" class="rounded-md bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 hover:bg-blue-100">Lihat</a>
                                    <a href="{{ route('dokumen.edit', $d) }}" class="ml-1 rounded-md bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700 hover:bg-slate-200">Edit</a>
                                    <form method="POST" action="{{ route('dokumen.destroy', $d) }}" class="inline"
                                          onsubmit="return confirm('Yakin hapus dokumen ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="ml-1 rounded-md bg-red-50 px-2.5 py-1 text-xs font-medium text-red-700 hover:bg-red-100">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center">
                                    <div class="font-medium text-slate-700">Belum ada data dokumen</div>
                                    <div class="mt-1 text-xs text-slate-500">Tambahkan dokumen atau impor dari Excel.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($dokumen->hasPages())
                <div class="border-t border-slate-100 p-4">{{ $dokumen->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>