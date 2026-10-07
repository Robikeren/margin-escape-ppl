<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrediksiRestock extends Model
{
    protected $table = 'prediksi_restock';

    protected $fillable = [
        'jenis_kopi_id',
        'tanggal_prediksi',
        'jumlah_prediksi',
        'rekomendasi_restock',
    ];

    protected $casts = [
        'tanggal_prediksi' => 'date',
    ];

    public function jenisKopi()
    {
        return $this->belongsTo(JenisKopi::class);
    }
}