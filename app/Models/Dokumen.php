<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    protected $table = 'dokumen';

    protected $fillable = [
        'nomor_dokumen',
        'tanggal_dokumen',
        'jenis_dokumen',
        'perihal',
        'asal_tujuan',
        'kategori_asal_tujuan',
        'sifat_surat',
        'bidang',
        'jumlah_lampiran',
        'keterangan',
                'file_dokumen',
        'cluster',
    ];

    protected $casts = [
        'tanggal_dokumen' => 'date',
    ];
}