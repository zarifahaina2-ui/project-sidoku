<?php

namespace App\Services;

use App\Models\Dokumen;
use Illuminate\Support\Facades\Cache;
use Sastrawi\Stemmer\StemmerFactory;
use Sastrawi\StopWordRemover\StopWordRemoverFactory;

class KMeansService
{
    private int $maxIter = 100;      // batas iterasi K-Means
    private int $nInit = 3;          // jumlah percobaan titik awal acak (diambil SSE terkecil)
    private float $bobotMeta = 0.3;  // bobot fitur metadata (jenis, kategori, sifat)
    private int $maxFitur = 300;     // maksimal jumlah kata (fitur TF-IDF)

    public function jalankan(?int $kPilihan = null): array
    {
        $docs = Dokumen::orderBy('id')->get();
        $n = $docs->count();

        if ($n < 6) {
            throw new \RuntimeException('Data terlalu sedikit. Minimal 6 dokumen, disarankan 100 atau lebih.');
        }

        // Tahap 1-2: preprocessing + TF-IDF + metadata menjadi angka
        [$X, $kataList, $jumlahKata] = $this->buatFitur($docs);

        // Matriks jarak antar dokumen (dipakai untuk Silhouette)
        $jarak = $this->matriksJarak($X);

        // Tahap 3: uji K = 2 sampai 10
        $kMax = min(10, $n - 1);
        $evaluasi = [];
        $semua = [];
        for ($k = 2; $k <= $kMax; $k++) {
            $r = $this->kmeansTerbaik($X, $k);
            $sil = $this->silhouette($jarak, $r['labels']);
            $evaluasi[] = [
                'k' => $k,
                'sse' => round($r['sse'], 4),
                'silhouette' => round($sil, 4),
            ];
            $semua[$k] = $r;
        }

        // Tahap 4: pilih K (manual atau otomatis dari Silhouette tertinggi)
        $kTerpilih = null;
        $mode = 'otomatis';
        if ($kPilihan !== null && isset($semua[$kPilihan])) {
            $kTerpilih = $kPilihan;
            $mode = 'manual';
                } else {
            // Pilih otomatis hanya di rentang K = 3 sampai 8.
            // Kalau selisih Silhouette tipis (0,02), pilih K yang lebih kecil agar hasil mudah dibaca.
            $batasBawah = min(3, $kMax);
            $batasAtas = min(8, $kMax);
            $kandidat = array_values(array_filter(
                $evaluasi,
                fn ($e) => $e['k'] >= $batasBawah && $e['k'] <= $batasAtas
            ));
            $terbaik = max(array_column($kandidat, 'silhouette'));
            foreach ($kandidat as $e) {
                if ($e['silhouette'] >= $terbaik - 0.02) {
                    $kTerpilih = $e['k'];
                    break;
                }
            }
        }

        $hasil = $semua[$kTerpilih];

        // Urutkan nomor cluster dari yang anggotanya terbanyak (C1 = terbesar)
        $ukuran = array_count_values($hasil['labels']);
        arsort($ukuran);
        $peta = [];
        $no = 1;
        foreach (array_keys($ukuran) as $lama) {
            $peta[$lama] = $no++;
        }
        $labels = array_map(fn ($l) => $peta[$l], $hasil['labels']);
        $centroids = [];
        foreach ($peta as $lama => $baru) {
            $centroids[$baru] = $hasil['centroids'][$lama];
        }

        // Simpan nomor cluster ke database
        $idPerCluster = [];
        foreach ($docs as $i => $d) {
            $idPerCluster[$labels[$i]][] = $d->id;
        }
        Dokumen::query()->update(['cluster' => null]);
        foreach ($idPerCluster as $c => $ids) {
            Dokumen::whereIn('id', $ids)->update(['cluster' => $c]);
        }

        // Ringkasan tiap cluster (kata kunci + ciri dominan)
        $ringkasan = [];
        foreach ($peta as $baru) {
            $anggota = array_keys(array_filter($labels, fn ($l) => $l === $baru));

            $bobot = array_slice($centroids[$baru], 0, $jumlahKata);
            arsort($bobot);
            $kunci = [];
            foreach (array_slice($bobot, 0, 6, true) as $idx => $nilai) {
                if ($nilai > 0) {
                    $kunci[] = $kataList[$idx];
                }
            }

            $ringkasan[] = [
                'no' => $baru,
                'jumlah' => count($anggota),
                'kata_kunci' => $kunci,
                'jenis' => $this->dominan($docs, $anggota, 'jenis_dokumen'),
                'kategori' => $this->dominan($docs, $anggota, 'kategori_asal_tujuan'),
                'sifat' => $this->dominan($docs, $anggota, 'sifat_surat'),
            ];
        }

        // Titik 2D untuk scatter plot (PCA)
        $titik = $this->pca2($X);
        $scatter = [];
        foreach ($titik as $i => $p) {
            $scatter[] = ['x' => round($p[0], 4), 'y' => round($p[1], 4), 'c' => $labels[$i]];
        }

        $kunciEval = collect($evaluasi)->firstWhere('k', $kTerpilih);

        $payload = [
            'waktu' => now()->format('d-m-Y H:i'),
            'jumlah_dokumen' => $n,
            'jumlah_fitur' => $jumlahKata,
            'mode' => $mode,
            'k' => $kTerpilih,
            'silhouette' => $kunciEval['silhouette'],
            'sse' => $kunciEval['sse'],
            'evaluasi' => $evaluasi,
            'cluster' => $ringkasan,
            'scatter' => $scatter,
        ];

        Cache::forever('hasil_kmeans', $payload);
                Cache::forget('nama_cluster');

        return $payload;
    }

    // ---------- Preprocessing + TF-IDF + metadata ----------
    private function buatFitur($docs): array
    {
        $stemmer = (new StemmerFactory())->createStemmer();
        $stopword = (new StopWordRemoverFactory())->createStopWordRemover();

        // Preprocessing teks perihal
        $tokenDoc = [];
        foreach ($docs as $d) {
            $teks = mb_strtolower($d->perihal);
            $teks = preg_replace('/[^a-z\s]/', ' ', $teks);   // hapus angka dan tanda baca
            $teks = $stopword->remove($teks);                 // hapus stopword
            $teks = $stemmer->stem($teks);                    // stemming
            $kata = preg_split('/\s+/', trim($teks));
            $kata = array_filter($kata, fn ($w) => strlen($w) >= 3);
            $tokenDoc[] = array_values($kata);
        }

        // Document frequency
        $n = count($tokenDoc);
        $df = [];
        foreach ($tokenDoc as $kata) {
            foreach (array_unique($kata) as $w) {
                $df[$w] = ($df[$w] ?? 0) + 1;
            }
        }

        // Pilih kata yang muncul di minimal 2 dokumen (kata langka dibuang)
        $kandidat = array_filter($df, fn ($v) => $v >= 2);
        if (count($kandidat) < 5) {
            $kandidat = $df;
        }
        arsort($kandidat);
        $kataList = array_slice(array_keys($kandidat), 0, $this->maxFitur);
        $indeks = array_flip($kataList);
        $m = count($kataList);

        // Kategori metadata untuk one-hot encoding
        $kolomMeta = ['jenis_dokumen', 'kategori_asal_tujuan', 'sifat_surat'];
        $daftarMeta = [];
        foreach ($kolomMeta as $kolom) {
            $daftarMeta[$kolom] = $docs->pluck($kolom)->unique()->values()->all();
        }

        $X = [];
        foreach ($docs as $i => $d) {
            $vec = array_fill(0, $m, 0.0);
            $jml = max(count($tokenDoc[$i]), 1);

            // TF-IDF
            foreach (array_count_values($tokenDoc[$i]) as $w => $c) {
                if (!isset($indeks[$w])) {
                    continue;
                }
                $idf = log((1 + $n) / (1 + $df[$w])) + 1;
                $vec[$indeks[$w]] = ($c / $jml) * $idf;
            }

            // Normalisasi panjang vektor teks (L2)
            $norm = 0.0;
            foreach ($vec as $v) {
                $norm += $v * $v;
            }
            $norm = sqrt($norm);
            if ($norm > 0) {
                foreach ($vec as $j => $v) {
                    $vec[$j] = $v / $norm;
                }
            }

            // Metadata (one-hot, dikalikan bobot)
            foreach ($daftarMeta as $kolom => $daftar) {
                foreach ($daftar as $nilai) {
                    $vec[] = ($d->$kolom === $nilai) ? $this->bobotMeta : 0.0;
                }
            }

            $X[] = $vec;
        }

        return [$X, $kataList, $m];
    }

    // ---------- K-Means ----------
    private function kmeansTerbaik(array $X, int $k): array
    {
        $terbaik = null;
        for ($r = 0; $r < $this->nInit; $r++) {
            $hasil = $this->kmeans($X, $k, 100 + $r * 17 + $k);
            if ($terbaik === null || $hasil['sse'] < $terbaik['sse']) {
                $terbaik = $hasil;
            }
        }
        return $terbaik;
    }

    private function kmeans(array $X, int $k, int $seed): array
    {
        mt_srand($seed);
        $n = count($X);
        $d = count($X[0]);

        // Titik pusat awal dengan K-Means++ (lebih stabil dari acak biasa)
        $centroids = [$X[mt_rand(0, $n - 1)]];
        $minJarak = array_fill(0, $n, INF);
        while (count($centroids) < $k) {
            $terakhir = end($centroids);
            $total = 0.0;
            for ($i = 0; $i < $n; $i++) {
                $dd = $this->jarak2($X[$i], $terakhir);
                if ($dd < $minJarak[$i]) {
                    $minJarak[$i] = $dd;
                }
                $total += $minJarak[$i];
            }
            $target = (mt_rand() / mt_getrandmax()) * $total;
            $acc = 0.0;
            $pilih = $n - 1;
            for ($i = 0; $i < $n; $i++) {
                $acc += $minJarak[$i];
                if ($acc >= $target) {
                    $pilih = $i;
                    break;
                }
            }
            $centroids[] = $X[$pilih];
        }

        $labels = array_fill(0, $n, -1);

        for ($iter = 0; $iter < $this->maxIter; $iter++) {
            // Langkah 1: tiap dokumen masuk ke centroid terdekat
            $berubah = false;
            for ($i = 0; $i < $n; $i++) {
                $terdekat = 0;
                $bd = INF;
                foreach ($centroids as $c => $cen) {
                    $dd = $this->jarak2($X[$i], $cen);
                    if ($dd < $bd) {
                        $bd = $dd;
                        $terdekat = $c;
                    }
                }
                if ($labels[$i] !== $terdekat) {
                    $labels[$i] = $terdekat;
                    $berubah = true;
                }
            }
            if (!$berubah) {
                break;
            }

            // Langkah 2: hitung ulang centroid = rata-rata anggota
            $sum = array_fill(0, $k, array_fill(0, $d, 0.0));
            $cnt = array_fill(0, $k, 0);
            for ($i = 0; $i < $n; $i++) {
                $c = $labels[$i];
                $cnt[$c]++;
                for ($j = 0; $j < $d; $j++) {
                    $sum[$c][$j] += $X[$i][$j];
                }
            }
            for ($c = 0; $c < $k; $c++) {
                if ($cnt[$c] > 0) {
                    for ($j = 0; $j < $d; $j++) {
                        $centroids[$c][$j] = $sum[$c][$j] / $cnt[$c];
                    }
                }
            }
        }

        // SSE = jumlah kuadrat jarak dokumen ke centroidnya
        $sse = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $sse += $this->jarak2($X[$i], $centroids[$labels[$i]]);
        }

        return ['labels' => $labels, 'centroids' => $centroids, 'sse' => $sse];
    }

    // ---------- Silhouette ----------
    private function silhouette(array $D, array $labels): float
    {
        $n = count($labels);
        $anggota = [];
        foreach ($labels as $i => $l) {
            $anggota[$l][] = $i;
        }
        if (count($anggota) < 2) {
            return -1.0;
        }

        $total = 0.0;
        foreach ($labels as $i => $l) {
            if (count($anggota[$l]) <= 1) {
                continue; // cluster 1 anggota bernilai 0
            }
            $a = 0.0;
            foreach ($anggota[$l] as $j) {
                if ($j !== $i) {
                    $a += $D[$i][$j];
                }
            }
            $a /= (count($anggota[$l]) - 1);

            $b = INF;
            foreach ($anggota as $c => $idx) {
                if ($c === $l) {
                    continue;
                }
                $s = 0.0;
                foreach ($idx as $j) {
                    $s += $D[$i][$j];
                }
                $b = min($b, $s / count($idx));
            }

            $total += ($b - $a) / max($a, $b, 1e-12);
        }

        return $total / $n;
    }

    // ---------- PCA 2 dimensi (untuk scatter plot) ----------
    private function pca2(array $X): array
    {
        $n = count($X);
        $d = count($X[0]);

        $mean = array_fill(0, $d, 0.0);
        foreach ($X as $row) {
            foreach ($row as $j => $v) {
                $mean[$j] += $v;
            }
        }
        foreach ($mean as $j => $v) {
            $mean[$j] = $v / $n;
        }
        foreach ($X as $i => $row) {
            foreach ($row as $j => $v) {
                $X[$i][$j] = $v - $mean[$j];
            }
        }

        $titik = array_fill(0, $n, [0.0, 0.0]);
        mt_srand(1);

        for ($c = 0; $c < 2; $c++) {
            $v = [];
            for ($j = 0; $j < $d; $j++) {
                $v[] = mt_rand() / mt_getrandmax() - 0.5;
            }
            $v = $this->normalisasi($v);

            for ($it = 0; $it < 60; $it++) {
                $w = array_fill(0, $d, 0.0);
                for ($i = 0; $i < $n; $i++) {
                    $u = $this->dot($X[$i], $v);
                    for ($j = 0; $j < $d; $j++) {
                        $w[$j] += $X[$i][$j] * $u;
                    }
                }
                $v = $this->normalisasi($w);
            }

            for ($i = 0; $i < $n; $i++) {
                $p = $this->dot($X[$i], $v);
                $titik[$i][$c] = $p;
                for ($j = 0; $j < $d; $j++) {
                    $X[$i][$j] -= $p * $v[$j];
                }
            }
        }

        return $titik;
    }

    // ---------- Fungsi bantu ----------
    private function matriksJarak(array $X): array
    {
        $n = count($X);
        $D = array_fill(0, $n, array_fill(0, $n, 0.0));
        for ($i = 0; $i < $n; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                $dd = sqrt($this->jarak2($X[$i], $X[$j]));
                $D[$i][$j] = $dd;
                $D[$j][$i] = $dd;
            }
        }
        return $D;
    }

    private function jarak2(array $a, array $b): float
    {
        $s = 0.0;
        $d = count($a);
        for ($i = 0; $i < $d; $i++) {
            $t = $a[$i] - $b[$i];
            $s += $t * $t;
        }
        return $s;
    }

    private function dot(array $a, array $b): float
    {
        $s = 0.0;
        $d = count($a);
        for ($i = 0; $i < $d; $i++) {
            $s += $a[$i] * $b[$i];
        }
        return $s;
    }

    private function normalisasi(array $v): array
    {
        $norm = sqrt($this->dot($v, $v));
        if ($norm == 0) {
            return $v;
        }
        return array_map(fn ($x) => $x / $norm, $v);
    }

    private function dominan($docs, array $anggota, string $kolom): array
    {
        $hitung = [];
        foreach ($anggota as $i) {
            $nilai = $docs[$i]->$kolom;
            $hitung[$nilai] = ($hitung[$nilai] ?? 0) + 1;
        }
        arsort($hitung);

        $hasil = [];
        foreach (array_slice($hitung, 0, 3, true) as $nilai => $jml) {
            $hasil[] = $nilai . ' (' . $jml . ')';
        }
        return $hasil;
    }
}