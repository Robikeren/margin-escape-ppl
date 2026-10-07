<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokOpname extends Model
{
    protected $table = 'stok_opname';

    protected $fillable = [
        'jenis_kopi_id',
        'user_id',
        'tanggal',
        'stok_awal',
        'keluar',
        'masuk',
        'stok_akhir',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function jenisKopi()
    {
        return $this->belongsTo(JenisKopi::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}