<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Clustering Dokumen (K-Means)</h2>
            <p class="text-sm text-slate-500">Pengelompokan dokumen berdasarkan kemiripan perihal dan metadata</p>
        </div>
    </x-slot>

    @php
        $cWarna = ['bg-blue-100 text-blue-800', 'bg-emerald-100 text-emerald-800', 'bg-amber-100 text-amber-800', 'bg-rose-100 text-rose-800', 'bg-violet-100 text-violet-800', 'bg-cyan-100 text-cyan-800', 'bg-pink-100 text-pink-800', 'bg-lime-100 text-lime-800', 'bg-orange-100 text-orange-800', 'bg-slate-200 text-slate-800'];
    @endphp

    <div class="space-y-6 px-4 py-6 sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
        @endif
        @error('k')
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $message }}</div>
        @enderror

        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60">
            <p class="mb-4 text-sm text-slate-600">
                Total dokumen tersimpan: <b>{{ $totalDokumen }}</b>. Sistem mengelompokkan dokumen berdasarkan kemiripan perihal (TF-IDF)
                dan metadata (jenis, kategori asal/tujuan, sifat surat).
            </p>
            <form method="POST" action="{{ route('clustering.proses') }}" class="flex flex-wrap items-center gap-3"
                  onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').innerText = 'Memproses... mohon tunggu';">
                @csrf
                <label class="text-sm font-medium text-slate-700">Jumlah cluster (K):</label>
                <select name="k" class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
                    <option value="">Otomatis (K 3 sampai 8, Silhouette terbaik)</option>
                    @for ($i = 2; $i <= 10; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
                <button type="submit" class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">
                    Proses K-Means
                </button>
            </form>
        </div>

        @if ($hasil)
            <div class="grid grid-cols-2 gap-4 md:grid-cols-5">
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/60">
                    <div class="text-xs text-slate-500">Jumlah dokumen</div>
                    <div class="mt-1 text-2xl font-bold text-slate-900">{{ $hasil['jumlah_dokumen'] }}</div>
                </div>
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/60">
                    <div class="text-xs text-slate-500">K terpilih ({{ $hasil['mode'] }})</div>
                    <div class="mt-1 text-2xl font-bold text-slate-900">{{ $hasil['k'] }}</div>
                </div>
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/60">
                    <div class="text-xs text-slate-500">Silhouette</div>
                    <div class="mt-1 text-2xl font-bold text-slate-900">{{ $hasil['silhouette'] }}</div>
                </div>
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/60">
                    <div class="text-xs text-slate-500">Jumlah kata (fitur)</div>
                    <div class="mt-1 text-2xl font-bold text-slate-900">{{ $hasil['jumlah_fitur'] }}</div>
                </div>
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/60">
                    <div class="text-xs text-slate-500">Diproses pada</div>
                    <div class="mt-2 text-sm font-semibold text-slate-900">{{ $hasil['waktu'] }}</div>
                </div>
            </div>

            @if ($hasil['jumlah_dokumen'] != $totalDokumen)
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    Jumlah dokumen sekarang ({{ $totalDokumen }}) berbeda dari saat clustering terakhir
                    ({{ $hasil['jumlah_dokumen'] }}). Klik <b>Proses K-Means</b> lagi untuk memperbarui hasil.
                </div>
            @endif

            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60">
                <div class="border-b border-slate-100 p-6">
                    <h3 class="font-semibold text-slate-900">Karakteristik tiap cluster</h3>
                    <p class="mt-1 text-xs text-slate-500">
                        Kata kunci adalah kata dasar (hasil stemming) dengan bobot TF-IDF tertinggi pada centroid.
                        Gunakan kata kunci dan ciri dominan untuk memberi nama tiap cluster di laporan.
                    </p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Cluster</th>
                                <th class="px-4 py-3">Jumlah</th>
                                <th class="px-4 py-3">Kata kunci</th>
                                <th class="px-4 py-3">Jenis dominan</th>
                                <th class="px-4 py-3">Kategori dominan</th>
                                <th class="px-4 py-3">Sifat dominan</th>
                                <th class="px-4 py-3">Dokumen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 align-top">
                            @foreach ($hasil['cluster'] as $c)
                                <tr class="{{ $pilihCluster == $c['no'] ? 'bg-blue-50/60' : '' }}">
                                    <td class="px-4 py-3">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $cWarna[($c['no'] - 1) % 10] }}">C{{ $c['no'] }}</span>
@if (!empty($namaCluster[$c['no']]))
    <div class="mt-1 max-w-[9rem] text-xs font-medium text-slate-700">{{ $namaCluster[$c['no']] }}</div>
@endif
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-slate-800">{{ $c['jumlah'] }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex max-w-xs flex-wrap gap-1">
                                            @foreach ($c['kata_kunci'] as $kata)
                                                <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs text-slate-700">{{ $kata }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">{!! implode('<br>', array_map('e', $c['jenis'])) !!}</td>
                                    <td class="px-4 py-3 text-slate-600">{!! implode('<br>', array_map('e', $c['kategori'])) !!}</td>
                                    <td class="px-4 py-3 text-slate-600">{!! implode('<br>', array_map('e', $c['sifat'])) !!}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('clustering.index', ['cluster' => $c['no']]) }}#daftar"
                                           class="rounded-md bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 hover:bg-blue-100">Lihat</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60">
                <h3 class="font-semibold text-slate-900">Beri nama cluster</h3>
                <p class="mt-1 text-xs text-slate-500">
                    Baca kata kunci dan ciri dominan di tabel atas, lalu beri nama yang menggambarkan isi kelompoknya
                    (contoh: "Perizinan dan rekomendasi teknis"). Nama akan tampil di Dashboard dan daftar dokumen.
                </p>

                <form method="POST" action="{{ route('clustering.nama') }}" class="mt-4 space-y-2">
                    @csrf
                    @foreach ($hasil['cluster'] as $c)
                        <div class="flex items-center gap-3">
                            <span class="w-10 shrink-0 rounded-full px-2.5 py-1 text-center text-xs font-semibold {{ $cWarna[($c['no'] - 1) % 10] }}">C{{ $c['no'] }}</span>
                            <input type="text" name="nama[{{ $c['no'] }}]" value="{{ $namaCluster[$c['no']] ?? '' }}" maxlength="60"
                                   placeholder="Kata kunci: {{ implode(', ', array_slice($c['kata_kunci'], 0, 3)) }}"
                                   class="block w-full rounded-lg border-slate-300 text-sm shadow-sm placeholder:text-slate-400 focus:border-blue-600 focus:ring-blue-600">
                        </div>
                    @endforeach
                    <button type="submit" class="mt-2 rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">Simpan nama cluster</button>
                </form>
            </div>
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60">
                    <h3 class="mb-4 text-sm font-semibold text-slate-900">Metode Elbow (SSE tiap K)</h3>
                    <div class="h-64"><canvas id="chartElbow"></canvas></div>
                </div>
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60">
                    <h3 class="mb-4 text-sm font-semibold text-slate-900">Silhouette Score tiap K</h3>
                    <div class="h-64"><canvas id="chartSil"></canvas></div>
                </div>
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60">
                    <h3 class="mb-4 text-sm font-semibold text-slate-900">Jumlah dokumen per cluster</h3>
                    <div class="h-64"><canvas id="chartJumlah"></canvas></div>
                </div>
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60">
                    <h3 class="mb-4 text-sm font-semibold text-slate-900">Sebaran dokumen (PCA 2D)</h3>
                    <div class="h-64"><canvas id="chartScatter"></canvas></div>
                </div>
            </div>

            <div id="daftar" class="scroll-mt-6 rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/60">
                <div class="border-b border-slate-100 p-6">
                    <h3 class="font-semibold text-slate-900">
                        @if ($daftar)
                            Dokumen pada Cluster C{{ $pilihCluster }} ({{ $daftar->count() }})
                        @else
                            Daftar dokumen per cluster
                        @endif
                    </h3>
                </div>

                @if ($daftar)
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Nomor</th>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Jenis</th>
                                    <th class="px-4 py-3">Perihal</th>
                                    <th class="px-4 py-3">Asal/Tujuan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($daftar as $d)
                                    <tr class="hover:bg-slate-50/70">
                                        <td class="px-4 py-3 font-medium text-slate-800">{{ $d->nomor_dokumen }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $d->tanggal_dokumen->format('d-m-Y') }}</td>
                                        <td class="px-4 py-3">
                                            <span class="whitespace-nowrap rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">{{ $d->jenis_dokumen }}</span>
                                        </td>
                                        <td class="max-w-md px-4 py-3 text-slate-700">{{ $d->perihal }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ $d->asal_tujuan }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="p-6 text-sm text-slate-500">Klik <b>Lihat</b> pada tabel cluster di atas untuk menampilkan dokumennya.</p>
                @endif
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
            <script>
                const evaluasi = @json($hasil['evaluasi']);
                const klaster = @json($hasil['cluster']);
                const titik = @json($hasil['scatter']);
                const warna = ['#4f46e5', '#ef4444', '#10b981', '#f59e0b', '#06b6d4', '#8b5cf6', '#ec4899', '#84cc16', '#f97316', '#64748b'];
                const opsi = { maintainAspectRatio: false };

                new Chart(document.getElementById('chartElbow'), {
                    type: 'line',
                    data: {
                        labels: evaluasi.map(e => 'K=' + e.k),
                        datasets: [{ label: 'SSE', data: evaluasi.map(e => e.sse), borderColor: '#1d4ed8', backgroundColor: 'rgba(29,78,216,0.1)', fill: true, tension: 0.2 }]
                    },
                    options: opsi
                });

                new Chart(document.getElementById('chartSil'), {
                    type: 'line',
                    data: {
                        labels: evaluasi.map(e => 'K=' + e.k),
                        datasets: [{ label: 'Silhouette', data: evaluasi.map(e => e.silhouette), borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.1)', fill: true, tension: 0.2 }]
                    },
                    options: opsi
                });

                new Chart(document.getElementById('chartJumlah'), {
                    type: 'bar',
                    data: {
                        labels: klaster.map(c => 'C' + c.no),
                        datasets: [{ label: 'Jumlah dokumen', data: klaster.map(c => c.jumlah), backgroundColor: klaster.map((c, i) => warna[i % warna.length]), borderRadius: 6 }]
                    },
                    options: opsi
                });

                new Chart(document.getElementById('chartScatter'), {
                    type: 'scatter',
                    data: {
                        datasets: klaster.map((c, i) => ({
                            label: 'C' + c.no,
                            data: titik.filter(t => t.c === c.no).map(t => ({ x: t.x, y: t.y })),
                            backgroundColor: warna[i % warna.length]
                        }))
                    },
                    options: opsi
                });
            </script>
        @else
            <div class="rounded-2xl bg-white p-10 text-center shadow-sm ring-1 ring-slate-200/60">
                <div class="font-semibold text-slate-800">Belum ada hasil clustering</div>
                <p class="mt-1 text-sm text-slate-500">Klik <b>Proses K-Means</b> di atas untuk memulai.</p>
            </div>
        @endif
    </div>
</x-app-layout>