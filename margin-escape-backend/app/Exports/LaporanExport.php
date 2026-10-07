<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanExport implements FromArray, WithHeadings
{
    protected $detail;

    public function __construct($detail)
    {
        $this->detail = $detail;
    }

    public function array(): array
    {
        return collect($this->detail)->map(fn($d) => [
            $d['tanggal'],
            $d['jenis_kopi'],
            $d['stok_awal'],
            $d['keluar'],
            $d['masuk'],
            $d['stok_akhir'],
        ])->toArray();
    }

    public function headings(): array
    {
        return ['Tanggal', 'Jenis Kopi', 'Stok Awal', 'Keluar', 'Masuk', 'Stok Akhir'];
    }
}