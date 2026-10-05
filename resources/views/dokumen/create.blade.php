<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Tambah Dokumen</h2>
            <p class="text-sm text-slate-500">Isi data surat atau dokumen baru</p>
        </div>
    </x-slot>

    <div class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="max-w-4xl rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/60">
            <form method="POST" action="{{ route('dokumen.store') }}" enctype="multipart/form-data">
                @csrf
                @include('dokumen._form')
                @include('dokumen._file')

                <div class="mt-8 flex gap-2 border-t border-slate-100 pt-6">
                    <button type="submit" class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">Simpan</button>
                    <a href="{{ route('dokumen.index') }}" class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>