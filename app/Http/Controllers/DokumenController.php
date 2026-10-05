<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DokumenController extends Controller
{
    private function pilihan(): array
    {
        return [
            'jenisList' => [
                'Surat Masuk', 'Surat Keluar', 'Nota Dinas', 'Surat Undangan',
                'Surat Tugas', 'SPPD', 'Surat Edaran', 'SK', 'Berita Acara',
                'Perjanjian/MoU', 'Laporan', 'SPK', 'Kontrak',
            ],
            'kategoriList' => [
                'Kementerian/Lembaga Pusat', 'Pemerintah Daerah', 'Balai/UPT Lain',
                'Swasta/Perusahaan', 'Masyarakat/Perorangan', 'Penyedia Jasa', 'Internal',
            ],
            'sifatList' => ['Biasa', 'Segera', 'Penting', 'Rahasia'],
        ];
    }

    private function aturan(): array
    {
        return [
            'nomor_dokumen'        => 'required|string|max:255',
            'tanggal_dokumen'      => 'required|date',
            'jenis_dokumen'        => 'required|string|max:100',
            'perihal'              => 'required|string',
            'asal_tujuan'          => 'required|string|max:255',
            'kategori_asal_tujuan' => 'required|string|max:100',
            'sifat_surat'          => 'required|string|max:50',
            'bidang'               => 'nullable|string|max:255',
            'jumlah_lampiran'      => 'nullable|integer|min:0',
            'keterangan'           => 'nullable|string',
            'file_dokumen'         => 'nullable|file|mimes:pdf|max:10240',
        ];
    }

    public function index(Request $request)
    {
        $q = $request->input('q');
        $jenis = $request->input('jenis');

        $dokumen = Dokumen::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nomor_dokumen', 'like', "%{$q}%")
                        ->orWhere('perihal', 'like', "%{$q}%")
                        ->orWhere('asal_tujuan', 'like', "%{$q}%");
                });
            })
            ->when($jenis, fn ($query) => $query->where('jenis_dokumen', $jenis))
            ->orderByDesc('tanggal_dokumen')
            ->paginate(10)
            ->withQueryString();

        return view('dokumen.index', array_merge(
            compact('dokumen', 'q', 'jenis'),
            $this->pilihan()
        ));
    }

    public function create()
    {
        return view('dokumen.create', $this->pilihan());
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan());
        $data['jumlah_lampiran'] = $data['jumlah_lampiran'] ?? 0;

        if ($request->hasFile('file_dokumen')) {
            $data['file_dokumen'] = $request->file('file_dokumen')->store('dokumen', 'public');
        }

        Dokumen::create($data);

        return redirect()->route('dokumen.index')
            ->with('success', 'Dokumen berhasil ditambahkan.');
    }

    public function show(Dokumen $dokumen)
    {
        return view('dokumen.show', compact('dokumen'));
    }

    public function edit(Dokumen $dokumen)
    {
        return view('dokumen.edit', array_merge(compact('dokumen'), $this->pilihan()));
    }

    public function update(Request $request, Dokumen $dokumen)
    {
        $data = $request->validate($this->aturan());
        $data['jumlah_lampiran'] = $data['jumlah_lampiran'] ?? 0;
        unset($data['file_dokumen']);

        if ($request->hasFile('file_dokumen')) {
            if ($dokumen->file_dokumen) {
                Storage::disk('public')->delete($dokumen->file_dokumen);
            }
            $data['file_dokumen'] = $request->file('file_dokumen')->store('dokumen', 'public');
        } elseif ($request->boolean('hapus_file') && $dokumen->file_dokumen) {
            Storage::disk('public')->delete($dokumen->file_dokumen);
            $data['file_dokumen'] = null;
        }

        $dokumen->update($data);

        return redirect()->route('dokumen.index')
            ->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroy(Dokumen $dokumen)
    {
        if ($dokumen->file_dokumen) {
            Storage::disk('public')->delete($dokumen->file_dokumen);
        }
        $dokumen->delete();

        return redirect()->route('dokumen.index')
            ->with('success', 'Dokumen berhasil dihapus.');
    }
}