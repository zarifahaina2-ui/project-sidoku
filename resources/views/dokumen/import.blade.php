<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Impor Dokumen dari Excel</h2>
            <p class="text-sm text-slate-500">Masukkan banyak dokumen sekaligus memakai template</p>
        </div>
    </x-slot>

    <div class="max-w-4xl space-y-5 px-4 py-6 sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
        @endif
        @error('file')
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $message }}</div>
        @enderror

        @if (session('galat') && count(session('galat')))
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                <div class="mb-1 font-semibold">Baris yang tidak masuk (perbaiki di Excel, lalu impor ulang):</div>
                <ul class="list-disc pl-5">
                    @foreach (session('galat') as $g)
                        <li>{{ $g }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60">
            <div class="flex items-start gap-4">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-700 text-sm font-bold text-white">1</span>
                <div class="flex-1">
                    <h3 class="font-semibold text-slate-900">Unduh template</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Template berisi kolom yang sesuai sistem, dropdown untuk Jenis, Kategori, dan Sifat, serta sheet Petunjuk.
                    </p>
                    <a href="{{ route('dokumen.template') }}"
                       class="mt-4 inline-block rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">
                        Unduh Template Excel
                    </a>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60">
            <div class="flex items-start gap-4">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-700 text-sm font-bold text-white">2</span>
                <div class="flex-1">
                    <h3 class="font-semibold text-slate-900">Unggah file yang sudah diisi</h3>
                    <p class="mt-1 text-sm text-slate-500">Format .xlsx, .xls, atau .csv, maksimal 10 MB. Nomor dokumen yang sudah ada akan dilewati.</p>

                    <form method="POST" action="{{ route('dokumen.import.proses') }}" enctype="multipart/form-data" class="mt-4"
                          onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').innerText = 'Memproses...';">
                        @csrf
                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4">
                            <input type="file" name="file" accept=".xlsx,.xls,.csv"
                                   class="block w-full text-sm text-slate-700 file:mr-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-slate-800 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-slate-700">
                        </div>
                        <div class="mt-4 flex gap-2">
                            <button type="submit" class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">Impor Sekarang</button>
                            <a href="{{ route('dokumen.index') }}" class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Kembali ke Data Dokumen</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>