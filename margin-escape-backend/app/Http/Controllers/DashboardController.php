<?php

namespace App\Http\Controllers;

use App\Models\JenisKopi;
use App\Models\StokOpname;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // API-05: Ringkasan status stok semua jenis kopi
    public function ringkasan(Request $request)
    {
        $jenisKopiList = JenisKopi::all();
        $result = [];

        foreach ($jenisKopiList as $jenisKopi) {
            // Ambil data stok opname paling baru untuk jenis kopi ini
            $latest = StokOpname::where('jenis_kopi_id', $jenisKopi->id)
                ->orderBy('tanggal', 'desc')
                ->first();

            if (! $latest) {
                $result[strtolower($jenisKopi->nama)] = [
                    'stok_saat_ini' => 0,
                    'status' => 'belum_ada_data',
                ];
                continue;
            }

            // Rata-rata konsumsi harian dari 14 data terakhir (dasar penentuan status)
            $rataRata = StokOpname::where('jenis_kopi_id', $jenisKopi->id)
                ->orderBy('tanggal', 'desc')
                ->limit(14)
                ->avg('keluar');

            $rataRata = $rataRata ?: 1; // hindari pembagian dengan nol
            $stokSaatIni = $latest->stok_akhir;

            // Rules threshold sesuai dokumen forecasting:
            // Aman   : stok > 1.5x rata-rata
            // Menipis: 1x - 1.5x rata-rata
            // Kritis : < 1x rata-rata
            if ($stokSaatIni > 1.5 * $rataRata) {
                $status = 'aman';
            } elseif ($stokSaatIni >= $rataRata) {
                $status = 'menipis';
            } else {
                $status = 'kritis';
            }

            $result[strtolower($jenisKopi->nama)] = [
                'stok_saat_ini' => $stokSaatIni,
                'rata_rata_konsumsi_harian' => round($rataRata, 1),
                'status' => $status,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $result,
        ], 200);
    }
}