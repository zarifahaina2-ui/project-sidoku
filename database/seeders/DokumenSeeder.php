<?php

namespace Database\Seeders;

use App\Models\Dokumen;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DokumenSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(7);

        $pool = [
            'x' => ['Deli', 'Belawan', 'Percut', 'Padang', 'Wampu', 'Bah Bolon', 'Asahan', 'Batang Toru', 'Lau Biang', 'Serdang', 'Ular', 'Babura'],
            'y' => ['Peralatan Irigasi', 'Alat Berat', 'Peralatan Survei', 'Perlengkapan Kantor', 'Alat Pengujian Air'],
            'z' => ['Jakarta', 'Bandung', 'Palembang', 'Pekanbaru', 'Yogyakarta', 'Makassar'],
            'b' => ['Januari', 'Maret', 'Mei', 'Juli', 'September', 'November'],
        ];

        $kelompok = [
            [
                'perihal' => [
                    'Permohonan Data Debit dan Tinggi Muka Air Sungai {x}',
                    'Laporan Pemeliharaan Rutin Saluran Irigasi Daerah Irigasi {x}',
                    'Penanganan Darurat Banjir Sungai {x}',
                    'Rencana Normalisasi dan Pengerukan Sungai {x}',
                    'Pemeriksaan Kondisi Bendungan dan Embung {x}',
                    'Laporan Kerusakan Tanggul Sungai {x}',
                ],
                'jenis' => ['Surat Masuk', 'Surat Keluar', 'Laporan'],
                'kategori' => ['Pemerintah Daerah', 'Balai/UPT Lain'],
                'sifat' => ['Biasa', 'Segera', 'Penting'],
                'bidang' => 'Teknis Sumber Daya Air',
            ],
            [
                'perihal' => [
                    'Permohonan Rekomendasi Teknis Pemanfaatan Sempadan Sungai {x}',
                    'Permohonan Izin Pengambilan Air Permukaan dari Sungai {x}',
                    'Permohonan Rekomendasi Teknis Pembangunan Jembatan di Atas Sungai {x}',
                    'Permohonan Izin Penggunaan Lahan Sempadan Sungai {x} untuk Usaha',
                    'Tanggapan Permohonan Rekomendasi Teknis Bangunan di Sempadan Sungai {x}',
                ],
                'jenis' => ['Surat Masuk', 'Surat Keluar'],
                'kategori' => ['Swasta/Perusahaan', 'Masyarakat/Perorangan'],
                'sifat' => ['Biasa', 'Segera'],
                'bidang' => 'Perizinan',
            ],
            [
                'perihal' => [
                    'Surat Perintah Kerja Pengadaan Sewa {y}',
                    'Kontrak Pekerjaan Konstruksi Rehabilitasi Jaringan Irigasi {x}',
                    'Permohonan Pembayaran Termin Pekerjaan Pemeliharaan Sungai {x}',
                    'Usulan Revisi Anggaran DIPA Tahun 2025',
                    'Berita Acara Serah Terima Pekerjaan Pengadaan {y}',
                    'Pengumuman Lelang Paket Pekerjaan Jasa Konstruksi {x}',
                ],
                'jenis' => ['SPK', 'Kontrak', 'Nota Dinas', 'Berita Acara'],
                'kategori' => ['Penyedia Jasa', 'Internal'],
                'sifat' => ['Biasa', 'Penting'],
                'bidang' => 'Keuangan dan Pengadaan',
            ],
            [
                'perihal' => [
                    'Usulan Kenaikan Pangkat Pegawai Lingkup Balai',
                    'Permohonan Cuti Tahunan Pegawai Bulan {b}',
                    'Surat Tugas Perjalanan Dinas ke {z}',
                    'Penetapan Keputusan Mutasi Pegawai Lingkup Balai',
                    'Usulan Pensiun Pegawai Negeri Sipil',
                    'Laporan Absensi dan Disiplin Pegawai Bulan {b}',
                ],
                'jenis' => ['Surat Tugas', 'SPPD', 'SK', 'Nota Dinas'],
                'kategori' => ['Internal', 'Kementerian/Lembaga Pusat'],
                'sifat' => ['Biasa', 'Segera'],
                'bidang' => 'Tata Usaha',
            ],
            [
                'perihal' => [
                    'Undangan Rapat Koordinasi Pengelolaan Sumber Daya Air {x}',
                    'Undangan Sosialisasi Peraturan Pengelolaan Sungai',
                    'Undangan Workshop Penyusunan Rencana Kerja Tahun 2026',
                    'Undangan Rapat Pembahasan Penanganan Banjir {x}',
                    'Undangan Konsultasi Publik Rencana Pembangunan Bendungan',
                    'Undangan Apel Peringatan Hari Air Dunia',
                ],
                'jenis' => ['Surat Undangan'],
                'kategori' => ['Pemerintah Daerah', 'Kementerian/Lembaga Pusat', 'Balai/UPT Lain'],
                'sifat' => ['Biasa', 'Segera'],
                'bidang' => 'Tata Usaha',
            ],
        ];

        $no = 1;
        foreach ($kelompok as $kel) {
            for ($i = 0; $i < 24; $i++) {
                $perihal = $this->pilih($kel['perihal']);
                $perihal = preg_replace_callback('/\{(\w)\}/', fn ($m) => $this->pilih($pool[$m[1]]), $perihal);

                Dokumen::create([
                    'nomor_dokumen' => sprintf('CONTOH.%03d/Bbws/2025', $no++),
                    'tanggal_dokumen' => Carbon::create(2025, 1, 1)->addDays(mt_rand(0, 364)),
                    'jenis_dokumen' => $this->pilih($kel['jenis']),
                    'perihal' => $perihal,
                    'asal_tujuan' => 'Instansi Contoh ' . mt_rand(1, 20),
                    'kategori_asal_tujuan' => $this->pilih($kel['kategori']),
                    'sifat_surat' => $this->pilih($kel['sifat']),
                    'bidang' => $kel['bidang'],
                    'jumlah_lampiran' => mt_rand(0, 3),
                    'keterangan' => 'Data contoh',
                ]);
            }
        }
    }

    private function pilih(array $daftar)
    {
        return $daftar[mt_rand(0, count($daftar) - 1)];
    }
}