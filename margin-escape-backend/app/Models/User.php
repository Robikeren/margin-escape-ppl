<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role', // 'owner' atau 'staff'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relasi: satu staff mencatat banyak stok opname
    public function stokOpname()
    {
        return $this->hasMany(StokOpname::class);
    }

    // Relasi: satu owner membuat banyak laporan
    public function laporan()
    {
        return $this->hasMany(Laporan::class);
    }

    // Relasi many-to-many: user menerima banyak notifikasi
    public function notifikasi()
    {
        return $this->belongsToMany(Notifikasi::class, 'notifikasi_user');
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }
}