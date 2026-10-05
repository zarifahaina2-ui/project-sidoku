<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Detail Dokumen</h2>
                <p class="text-sm text-slate-500">{{ $dokumen->nomor_dokumen }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('dokumen.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Kembali</a>
                <a href="{{ route('dokumen.edit', $dokumen) }}" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">Edit</a>
            </div>
        </div>
    </x-slot>

    @php
        $cWarna = ['bg-blue-100 text-blue-800', 'bg-emerald-100 text-emerald-800', 'bg-amber-100 text-amber-800', 'bg-rose-100 text-rose-800', 'bg-violet-100 text-violet-800', 'bg-cyan-100 text-cyan-800', 'bg-pink-100 text-pink-800', 'bg-lime-100 text-lime-800', 'bg-orange-100 text-orange-800', 'bg-slate-200 text-slate-800'];
        $baris = [
            'Nomor dokumen' => $dokumen->nomor_dokumen,
            'Tanggal' => $dokumen->tanggal_dokumen->format('d-m-Y'),
            'Jenis dokumen' => $dokumen->jenis_dokumen,
            'Sifat surat' => $dokumen->sifat_surat,
            'Asal / tujuan' => $dokumen->asal_tujuan,
            'Kategori asal / tujuan' => $dokumen->kategori_asal_tujuan,
            'Bidang / unit' => $dokumen->bidang ?: '-',
            'Jumlah lampiran' => $dokumen->jumlah_lampiran,
            'Keterangan' => $dokumen->keterangan ?: '-',
        ];
    @endphp

    <div class="max-w-5xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <h3 class="max-w-3xl text-lg font-semibold leading-snug text-slate-900">{{ $dokumen->perihal }}</h3>
                @if ($dokumen->cluster)
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $cWarna[($dokumen->cluster - 1) % 10] }}">Cluster C{{ $dokumen->cluster }}@if (!empty($namaCluster[$dokumen->cluster])): {{ $namaCluster[$dokumen->cluster] }}@endif</span>
                @else
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-500">Belum di-cluster</span>
                @endif
            </div>

            <dl class="mt-6 grid grid-cols-1 gap-x-8 gap-y-4 text-sm md:grid-cols-2">
                @foreach ($baris as $label => $nilai)
                    <div class="border-b border-slate-100 pb-3">
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">{{ $label }}</dt>
                        <dd class="mt-0.5 font-medium text-slate-800">{{ $nilai }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="font-semibold text-slate-900">File dokumen</h3>
                @if ($dokumen->file_dokumen)
                    <a href="{{ asset('storage/'.$dokumen->file_dokumen) }}" download
                       class="rounded-lg border border-blue-700 px-3 py-1.5 text-sm font-semibold text-blue-700 hover:bg-blue-50">Unduh PDF</a>
                @endif
            </div>

            @if ($dokumen->file_dokumen)
                <iframe src="{{ asset('storage/'.$dokumen->file_dokumen) }}" class="h-[700px] w-full rounded-xl border border-slate-200"></iframe>
            @else
                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm text-slate-500">
                    Belum ada file PDF.
                    <a href="{{ route('dokumen.edit', $dokumen) }}" class="font-semibold text-blue-700 underline">Unggah lewat Edit</a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>