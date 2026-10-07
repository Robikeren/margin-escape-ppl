<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $table = 'laporan';

    protected $fillable = [
        'user_id',
        'periode_mulai',
        'periode_akhir',
        'format',
    ];

    protected $casts = [
        'periode_mulai' => 'date',
        'periode_akhir' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}