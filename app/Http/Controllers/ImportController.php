<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as XlsDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ImportController extends Controller
{
    private array $jenisList = [
        'Surat Masuk', 'Surat Keluar', 'Nota Dinas', 'Surat Undangan',
        'Surat Tugas', 'SPPD', 'Surat Edaran', 'SK', 'Berita Acara',
        'Perjanjian/MoU', 'Laporan', 'SPK', 'Kontrak',
    ];

    private array $kategoriList = [
        'Kementerian/Lembaga Pusat', 'Pemerintah Daerah', 'Balai/UPT Lain',
        'Swasta/Perusahaan', 'Masyarakat/Perorangan', 'Penyedia Jasa', 'Internal',
    ];

    private array $sifatList = ['Biasa', 'Segera', 'Penting', 'Rahasia'];

    public function form()
    {
        return view('dokumen.import');
    }

    // ---------- Unduh template Excel ----------
    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data');

        $header = [
            'Nomor Dokumen', 'Tanggal (YYYY-MM-DD)', 'Jenis Dokumen', 'Perihal',
            'Asal/Tujuan', 'Kategori Asal/Tujuan', 'Sifat Surat',
            'Bidang/Unit', 'Jumlah Lampiran', 'Keterangan',
        ];
        $sheet->fromArray($header, null, 'A1');
        $sheet->getStyle('A1:J1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:J1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1D4ED8');

        // Baris contoh (baris ini otomatis dilewati saat impor)
        $sheet->fromArray([
            'CONTOH/HAPUS-BARIS-INI', '2025-08-13', 'SPK',
            'Belanja Modal Sewa Peralatan Irigasi (Tahap I): Sewa Drilling Rig, Air Compressor, Mud Pump',
            'CV. Anugrah Harapan', 'Penyedia Jasa', 'Biasa',
            'PPK Air Tanah dan Air Baku I', 0, '',
        ], null, 'A2');
        $sheet->getStyle('A2:J2')->getFont()->getColor()->setRGB('9CA3AF');

        // Format kolom: nomor sebagai teks, tanggal sebagai teks agar tidak berubah otomatis
        $sheet->getStyle('A2:A2000')->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('B2:B2000')->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('D2:D2000')->getAlignment()->setWrapText(true);

        foreach (['A' => 30, 'B' => 20, 'C' => 18, 'D' => 60, 'E' => 30, 'F' => 28, 'G' => 12, 'H' => 28, 'I' => 16, 'J' => 25] as $kol => $lebar) {
            $sheet->getColumnDimension($kol)->setWidth($lebar);
        }
        $sheet->freezePane('A2');

        // Dropdown
        $this->dropdown($sheet, 'C', $this->jenisList);
        $this->dropdown($sheet, 'F', $this->kategoriList);
        $this->dropdown($sheet, 'G', $this->sifatList);

        // Sheet petunjuk
        $p = $spreadsheet->createSheet();
        $p->setTitle('Petunjuk');
        $baris = [
            ['PETUNJUK PENGISIAN'],
            ['1. Isi data mulai baris 2 di sheet "Data". Baris contoh (abu-abu) boleh dihapus atau ditimpa.'],
            ['2. Satu baris = satu dokumen. Jangan menggabungkan sel.'],
            ['3. Kolom wajib: Nomor, Tanggal, Jenis, Perihal, Asal/Tujuan, Kategori. Sifat kosong dianggap "Biasa".'],
            ['4. Tanggal ditulis YYYY-MM-DD (contoh 2025-08-13). Format 13/08/2025 juga dibaca.'],
            ['5. Jenis, Kategori, dan Sifat pilih dari dropdown. Penulisan lain akan ditolak.'],
            ['6. Perihal salin LENGKAP dari surat. Ini bahan utama clustering. Untuk SPK, pakai "Nama Pekerjaan".'],
            ['7. Nomor yang sudah ada di database dilewati (tidak dobel).'],
            [''],
            ['Jenis Dokumen: ' . implode(', ', $this->jenisList)],
            ['Kategori: ' . implode(', ', $this->kategoriList)],
            ['Sifat: ' . implode(', ', $this->sifatList)],
        ];
        $p->fromArray($baris, null, 'A1');
        $p->getStyle('A1')->getFont()->setBold(true);
        $p->getColumnDimension('A')->setWidth(130);

        $spreadsheet->setActiveSheetIndex(0);

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, 'template_dokumen_sidoku.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function dropdown($sheet, string $kolom, array $daftar): void
    {
        $v = new DataValidation();
        $v->setType(DataValidation::TYPE_LIST);
        $v->setErrorStyle(DataValidation::STYLE_STOP);
        $v->setAllowBlank(true);
        $v->setShowDropDown(false); // false = tampilkan panah dropdown
        $v->setShowErrorMessage(true);
        $v->setErrorTitle('Pilihan tidak valid');
        $v->setError('Pilih dari daftar dropdown.');
        $v->setFormula1('"' . implode(',', $daftar) . '"');

        for ($r = 2; $r <= 2000; $r++) {
            $sheet->getCell($kolom . $r)->setDataValidation(clone $v);
        }
    }

    // ---------- Proses impor ----------
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'file.required' => 'Pilih file Excel terlebih dahulu.',
            'file.mimes' => 'File harus berformat .xlsx, .xls, atau .csv.',
        ]);

        set_time_limit(300);
        ini_set('memory_limit', '512M');

        try {
            $sheet = IOFactory::load($request->file('file')->getRealPath())->getSheet(0);
        } catch (\Throwable $e) {
            return back()->with('error', 'File tidak bisa dibaca: ' . $e->getMessage());
        }

        $rows = $sheet->toArray(null, true, false, false);

        $berhasil = 0;
        $duplikat = 0;
        $dilewati = 0;
        $galat = [];

        foreach ($rows as $i => $r) {
            if ($i === 0) {
                continue; // baris judul
            }
            $baris = $i + 1;

            $nomor = trim((string) ($r[0] ?? ''));
            $perihal = trim((string) ($r[3] ?? ''));

            // baris kosong atau baris contoh
            if ($nomor === '' && $perihal === '') {
                continue;
            }
            if (stripos($nomor, 'CONTOH/HAPUS') === 0) {
                $dilewati++;
                continue;
            }

            $tanggal = $this->bacaTanggal($r[1] ?? null);
            $jenis = $this->cocok($r[2] ?? '', $this->jenisList);
            $asal = trim((string) ($r[4] ?? ''));
            $kategori = $this->cocok($r[5] ?? '', $this->kategoriList);
            $sifatMentah = trim((string) ($r[6] ?? ''));
            $sifat = $sifatMentah === '' ? 'Biasa' : $this->cocok($sifatMentah, $this->sifatList);

            $masalah = [];
            if ($nomor === '') $masalah[] = 'nomor kosong';
            if (!$tanggal) $masalah[] = 'tanggal tidak valid';
            if (!$jenis) $masalah[] = 'jenis tidak ada di daftar';
            if ($perihal === '') $masalah[] = 'perihal kosong';
            if ($asal === '') $masalah[] = 'asal/tujuan kosong';
            if (!$kategori) $masalah[] = 'kategori tidak ada di daftar';
            if (!$sifat) $masalah[] = 'sifat tidak ada di daftar';

            if ($masalah) {
                if (count($galat) < 50) {
                    $galat[] = "Baris {$baris}: " . implode(', ', $masalah);
                }
                continue;
            }

            if (Dokumen::where('nomor_dokumen', $nomor)->exists()) {
                $duplikat++;
                continue;
            }

            Dokumen::create([
                'nomor_dokumen' => $nomor,
                'tanggal_dokumen' => $tanggal,
                'jenis_dokumen' => $jenis,
                'perihal' => $perihal,
                'asal_tujuan' => $asal,
                'kategori_asal_tujuan' => $kategori,
                'sifat_surat' => $sifat,
                'bidang' => trim((string) ($r[7] ?? '')) ?: null,
                'jumlah_lampiran' => max(0, (int) ($r[8] ?? 0)),
                'keterangan' => trim((string) ($r[9] ?? '')) ?: null,
            ]);
            $berhasil++;
        }

        $pesan = "Impor selesai: {$berhasil} dokumen berhasil masuk";
        if ($duplikat) $pesan .= ", {$duplikat} dilewati karena nomor sudah ada";
        if ($galat) $pesan .= ', ' . count($galat) . ' baris bermasalah (lihat daftar di bawah)';
        $pesan .= '.';

        return redirect()->route('dokumen.import')
            ->with($berhasil > 0 ? 'success' : 'error', $pesan)
            ->with('galat', $galat);
    }

  
    // ---------- Fungsi bantu ----------
    private function cocok($nilai, array $daftar): ?string
    {
        $nilai = trim((string) $nilai);
        foreach ($daftar as $d) {
            if (strcasecmp($d, $nilai) === 0) {
                return $d;
            }
        }
        return null;
    }

    private function bacaTanggal($nilai): ?string
    {
        if ($nilai === null || $nilai === '') {
            return null;
        }
        try {
            if (is_numeric($nilai)) {
                return XlsDate::excelToDateTimeObject((float) $nilai)->format('Y-m-d');
            }
            $t = trim((string) $nilai);
            if (preg_match('#^(\d{1,2})[/\-.](\d{1,2})[/\-.](\d{4})$#', $t, $m)) {
                return Carbon::createFromDate((int) $m[3], (int) $m[2], (int) $m[1])->format('Y-m-d');
            }
            return Carbon::parse($t)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}