<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Dashboard</h2>
            <p class="text-sm text-slate-500">Ringkasan dokumen dan hasil pengelompokan terakhir</p>
        </div>
    </x-slot>

    @php
        $kartu = [
            ['Total dokumen', $total, 'bg-blue-100 text-blue-700',
             'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
            ['Jenis dokumen', $perJenis->count(), 'bg-violet-100 text-violet-700',
             'M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z'],
            ['Jumlah cluster terakhir', $hasil['k'] ?? '-', 'bg-emerald-100 text-emerald-700',
             'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z'],
            ['Belum ter-cluster', $belumCluster, 'bg-amber-100 text-amber-700',
             'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z'],
        ];
        $cWarna = ['bg-blue-100 text-blue-800', 'bg-emerald-100 text-emerald-800', 'bg-amber-100 text-amber-800', 'bg-rose-100 text-rose-800', 'bg-violet-100 text-violet-800', 'bg-cyan-100 text-cyan-800', 'bg-pink-100 text-pink-800', 'bg-lime-100 text-lime-800', 'bg-orange-100 text-orange-800', 'bg-slate-200 text-slate-800'];
    @endphp

    <div class="space-y-6 px-4 py-6 sm:px-6 lg:px-8">

        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-900 via-blue-800 to-sky-600 p-6 text-white shadow-sm">
            <div class="absolute -right-10 -top-16 h-56 w-56 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-20 right-32 h-40 w-40 rounded-full bg-white/5"></div>
            <div class="relative flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="text-sm text-blue-200">Selamat datang,</div>
                    <div class="text-2xl font-bold">{{ Auth::user()->name }}</div>
                    <div class="mt-1 max-w-xl text-sm text-blue-100">Penerapan Metode K-Means Clustering untuk Pengelompokan Dokumen pada Kedinasan, BBWS Sumatera II Medan.</div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('dokumen.create') }}" class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-blue-800 hover:bg-blue-50">+ Tambah Dokumen</a>
                    <a href="{{ route('clustering.index') }}" class="rounded-lg border border-white/40 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Clustering</a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach ($kartu as [$label, $nilai, $warna, $ikon])
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/60">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-slate-500">{{ $label }}</div>
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl {{ $warna }}">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ikon }}"/></svg>
                        </span>
                    </div>
                    <div class="mt-3 text-3xl font-bold text-slate-900">{{ $nilai }}</div>
                </div>
            @endforeach
        </div>

        @if ($total > 0 && $belumCluster > 0)
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                Ada {{ $belumCluster }} dokumen yang belum masuk cluster.
                <a href="{{ route('clustering.index') }}" class="font-semibold underline">Jalankan K-Means</a> untuk memperbarui.
            </div>
        @endif

        @if ($total == 0)
            <div class="rounded-2xl bg-white p-10 text-center shadow-sm ring-1 ring-slate-200/60">
                <div class="text-lg font-semibold text-slate-800">Belum ada dokumen</div>
                <p class="mt-1 text-sm text-slate-500">Mulai dengan mengimpor dari Excel atau menambahkan satu dokumen.</p>
                <div class="mt-4 flex justify-center gap-2">
                    <a href="{{ route('dokumen.import') }}" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Impor Excel</a>
                    <a href="{{ route('dokumen.create') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Tambah manual</a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60">
                    <h3 class="mb-4 text-sm font-semibold text-slate-900">Dokumen per jenis</h3>
                    <div class="h-64"><canvas id="chartJenis"></canvas></div>
                </div>
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60">
                    <h3 class="mb-4 text-sm font-semibold text-slate-900">Dokumen per bulan</h3>
                    <div class="h-64"><canvas id="chartBulan"></canvas></div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-slate-900">Hasil clustering terakhir</h3>
                        @if ($hasil)
                            <a href="{{ route('clustering.index') }}" class="text-xs font-semibold text-blue-700 hover:underline">Lihat detail</a>
                        @endif
                    </div>
                    @if ($hasil)
                        <p class="mb-3 text-xs text-slate-500">Diproses {{ $hasil['waktu'] }} &middot; Silhouette {{ $hasil['silhouette'] }}</p>
                        <ul class="space-y-2 text-sm">
                            @foreach ($hasil['cluster'] as $c)
                                <li class="flex items-start gap-3 rounded-xl border border-slate-100 p-3">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $cWarna[($c['no'] - 1) % 10] }}">C{{ $c['no'] }}</span>
                                    <div class="min-w-0">
                                        <div class="font-medium text-slate-800">{{ $namaCluster[$c['no']] ?? implode(', ', array_slice($c['kata_kunci'], 0, 4)) }}</div>
                                        <div class="text-xs text-slate-400">{{ $c['jumlah'] }} dokumen</div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-slate-500">Belum ada hasil. <a href="{{ route('clustering.index') }}" class="font-semibold text-blue-700 underline">Proses K-Means</a>.</p>
                    @endif
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-slate-900">Dokumen terbaru</h3>
                        <a href="{{ route('dokumen.index') }}" class="text-xs font-semibold text-blue-700 hover:underline">Semua dokumen</a>
                    </div>
                    <ul class="space-y-2 text-sm">
                        @foreach ($terbaru as $d)
                            <li>
                                <a href="{{ route('dokumen.show', $d) }}" class="block rounded-xl border border-slate-100 p-3 transition hover:border-blue-200 hover:bg-blue-50/40">
                                    <div class="font-medium text-slate-800">{{ \Illuminate\Support\Str::limit($d->perihal, 80) }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ $d->jenis_dokumen }} &middot; {{ $d->nomor_dokumen }} &middot; {{ $d->tanggal_dokumen->format('d-m-Y') }}</div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
            <script>
                const perJenis = @json($perJenis);
                const perBulan = @json($perBulan);
                const warna = ['#1d4ed8', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#84cc16', '#f97316', '#64748b', '#14b8a6', '#a855f7', '#eab308'];

                new Chart(document.getElementById('chartJenis'), {
                    type: 'bar',
                    data: {
                        labels: perJenis.map(j => j.jenis_dokumen),
                        datasets: [{ label: 'Jumlah', data: perJenis.map(j => j.total), backgroundColor: perJenis.map((j, i) => warna[i % warna.length]), borderRadius: 6 }]
                    },
                    options: { maintainAspectRatio: false, plugins: { legend: { display: false } } }
                });

                new Chart(document.getElementById('chartBulan'), {
                    type: 'line',
                    data: {
                        labels: perBulan.map(b => b.bulan),
                        datasets: [{ label: 'Jumlah dokumen', data: perBulan.map(b => b.total), borderColor: '#1d4ed8', backgroundColor: 'rgba(29,78,216,0.1)', fill: true, tension: 0.3 }]
                    },
                    options: { maintainAspectRatio: false }
                });
            </script>
        @endif
    </div>
</x-app-layout>