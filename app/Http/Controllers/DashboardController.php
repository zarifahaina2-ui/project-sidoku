<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $total = Dokumen::count();

        $perJenis = Dokumen::select('jenis_dokumen', DB::raw('count(*) as total'))
            ->groupBy('jenis_dokumen')->orderByDesc('total')->get();

        $perBulan = Dokumen::select(DB::raw("DATE_FORMAT(tanggal_dokumen, '%Y-%m') as bulan"), DB::raw('count(*) as total'))
            ->groupBy('bulan')->orderBy('bulan')->get();

        $terbaru = Dokumen::latest('id')->take(5)->get();
        $belumCluster = Dokumen::whereNull('cluster')->count();
        $hasil = Cache::get('hasil_kmeans');

        return view('dashboard', compact('total', 'perJenis', 'perBulan', 'terbaru', 'belumCluster', 'hasil'));
    }
}