<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Services\KMeansService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ClusteringController extends Controller
{
    public function index(Request $request)
    {
        $hasil = Cache::get('hasil_kmeans');
        $totalDokumen = Dokumen::count();
        $pilihCluster = $request->integer('cluster');

        $daftar = null;
        if ($hasil && $pilihCluster) {
            $daftar = Dokumen::where('cluster', $pilihCluster)
                ->orderByDesc('tanggal_dokumen')
                ->get();
        }

        return view('clustering.index', compact('hasil', 'totalDokumen', 'pilihCluster', 'daftar'));
    }

    public function proses(Request $request, KMeansService $service)
    {
        $request->validate(['k' => 'nullable|integer|min:2|max:10']);

        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $adaNama = Cache::has('nama_cluster');

        try {
            $service->jalankan($request->filled('k') ? (int) $request->k : null);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $pesan = 'Proses K-Means selesai.';
        if ($adaNama) {
            $pesan .= ' Nama cluster sebelumnya direset karena nomor cluster bisa berubah. Beri nama lagi.';
        }

        return redirect()->route('clustering.index')->with('success', $pesan);
    }

    public function nama(Request $request)
    {
        $request->validate([
            'nama'   => 'nullable|array',
            'nama.*' => 'nullable|string|max:60',
        ]);

        $simpan = [];
        foreach ($request->input('nama', []) as $no => $nama) {
            $nama = trim((string) $nama);
            if ($nama !== '') {
                $simpan[(int) $no] = $nama;
            }
        }

        Cache::forever('nama_cluster', $simpan);

        return redirect()->route('clustering.index')->with('success', 'Nama cluster disimpan.');
    }
}
