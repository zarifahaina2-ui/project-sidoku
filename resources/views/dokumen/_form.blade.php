@php
    $field = 'mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm placeholder:text-slate-400 focus:border-blue-600 focus:ring-blue-600';
    $label = 'block text-sm font-medium text-slate-700';
    $err = 'mt-1 text-sm text-red-600';
@endphp

<div class="space-y-8">

    <div>
        <h3 class="text-sm font-semibold text-slate-900">Identitas dokumen</h3>
        <p class="text-xs text-slate-500">Kolom bertanda * wajib diisi.</p>

        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label class="{{ $label }}">Nomor Dokumen *</label>
                <input type="text" name="nomor_dokumen" class="{{ $field }}"
                       value="{{ old('nomor_dokumen', $dokumen->nomor_dokumen ?? '') }}">
                @error('nomor_dokumen') <p class="{{ $err }}">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="{{ $label }}">Tanggal Dokumen *</label>
                <input type="date" name="tanggal_dokumen" class="{{ $field }}"
                       value="{{ old('tanggal_dokumen', isset($dokumen) ? $dokumen->tanggal_dokumen->format('Y-m-d') : '') }}">
                @error('tanggal_dokumen') <p class="{{ $err }}">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="{{ $label }}">Jenis Dokumen *</label>
                <select name="jenis_dokumen" class="{{ $field }}">
                    <option value="">-- Pilih --</option>
                    @foreach ($jenisList as $j)
                        <option value="{{ $j }}" @selected(old('jenis_dokumen', $dokumen->jenis_dokumen ?? '') === $j)>{{ $j }}</option>
                    @endforeach
                </select>
                @error('jenis_dokumen') <p class="{{ $err }}">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="{{ $label }}">Sifat Surat *</label>
                <select name="sifat_surat" class="{{ $field }}">
                    @foreach ($sifatList as $s)
                        <option value="{{ $s }}" @selected(old('sifat_surat', $dokumen->sifat_surat ?? 'Biasa') === $s)>{{ $s }}</option>
                    @endforeach
                </select>
                @error('sifat_surat') <p class="{{ $err }}">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="{{ $label }}">Perihal * <span class="font-normal text-slate-400">(salin lengkap, ini bahan utama clustering)</span></label>
                <textarea name="perihal" rows="3" class="{{ $field }}">{{ old('perihal', $dokumen->perihal ?? '') }}</textarea>
                @error('perihal') <p class="{{ $err }}">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <div class="border-t border-slate-100 pt-6">
        <h3 class="text-sm font-semibold text-slate-900">Asal, tujuan, dan keterangan</h3>

        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label class="{{ $label }}">Asal / Tujuan (nama instansi) *</label>
                <input type="text" name="asal_tujuan" class="{{ $field }}"
                       value="{{ old('asal_tujuan', $dokumen->asal_tujuan ?? '') }}">
                @error('asal_tujuan') <p class="{{ $err }}">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="{{ $label }}">Kategori Asal / Tujuan *</label>
                <select name="kategori_asal_tujuan" class="{{ $field }}">
                    <option value="">-- Pilih --</option>
                    @foreach ($kategoriList as $k)
                        <option value="{{ $k }}" @selected(old('kategori_asal_tujuan', $dokumen->kategori_asal_tujuan ?? '') === $k)>{{ $k }}</option>
                    @endforeach
                </select>
                @error('kategori_asal_tujuan') <p class="{{ $err }}">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="{{ $label }}">Bidang / Unit Terkait</label>
                <input type="text" name="bidang" class="{{ $field }}"
                       value="{{ old('bidang', $dokumen->bidang ?? '') }}">
                @error('bidang') <p class="{{ $err }}">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="{{ $label }}">Jumlah Lampiran</label>
                <input type="number" min="0" name="jumlah_lampiran" class="{{ $field }}"
                       value="{{ old('jumlah_lampiran', $dokumen->jumlah_lampiran ?? 0) }}">
                @error('jumlah_lampiran') <p class="{{ $err }}">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="{{ $label }}">Keterangan</label>
                <textarea name="keterangan" rows="2" class="{{ $field }}">{{ old('keterangan', $dokumen->keterangan ?? '') }}</textarea>
                @error('keterangan') <p class="{{ $err }}">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>
</div>