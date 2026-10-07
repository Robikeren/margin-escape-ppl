<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $fillable = [
        'jenis_kopi_id',
        'tanggal',
        'pesan',
        'status_dibaca',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'status_dibaca' => 'boolean',
    ];

    public function jenisKopi()
    {
        return $this->belongsTo(JenisKopi::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'notifikasi_user');
    }
}