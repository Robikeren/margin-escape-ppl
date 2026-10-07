<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisKopi extends Model
{
    protected $table = 'jenis_kopi';

    protected $fillable = [
        'nama',
        'satuan',
    ];

    public function stokOpname()
    {
        return $this->hasMany(StokOpname::class);
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class);
    }

    public function prediksiRestock()
    {
        return $this->hasMany(PrediksiRestock::class);
    }
}