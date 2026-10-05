<div class="mt-8 border-t border-slate-100 pt-6">
    <h3 class="text-sm font-semibold text-slate-900">File dokumen</h3>
    <p class="text-xs text-slate-500">Opsional. Hanya PDF, maksimal 10 MB. File dipakai sebagai arsip, bukan untuk clustering.</p>

    <div class="mt-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4">
        <input type="file" name="file_dokumen" accept="application/pdf"
               class="block w-full text-sm text-slate-700 file:mr-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-blue-700 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-blue-800">
        @error('file_dokumen') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror

        @isset($dokumen)
            @if ($dokumen->file_dokumen)
                <div class="mt-4 flex flex-wrap items-center gap-4 rounded-lg bg-white p-3 text-sm ring-1 ring-slate-200">
                    <a href="{{ asset('storage/'.$dokumen->file_dokumen) }}" target="_blank" class="font-medium text-blue-700 hover:underline">Lihat PDF saat ini</a>
                    <label class="flex items-center gap-2 text-slate-600">
                        <input type="checkbox" name="hapus_file" value="1" class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                        Hapus PDF ini
                    </label>
                </div>
                <p class="mt-2 text-xs text-slate-500">Memilih file baru akan menggantikan PDF lama.</p>
            @endif
        @endisset
    </div>
</div>