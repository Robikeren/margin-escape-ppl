<?php

namespace App\Http\Controllers;

use App\Models\JenisKopi;
use App\Models\StokOpname;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ForecastingController extends Controller
{
    public function rekomendasi(Request $request)
    {
        $request->validate([
            'jenis_kopi_id' => 'required|exists:jenis_kopi,id',
        ]);

        $jenisKopi = JenisKopi::find($request->jenis_kopi_id);

        $riwayat = StokOpname::where('jenis_kopi_id', $jenisKopi->id)
            ->orderBy('tanggal')
            ->get(['tanggal', 'keluar']);

        if ($riwayat->count() < 10) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data historis belum cukup untuk forecasting (minimal 10 data)',
            ], 422);
        }

        // Panggil Python microservice
        $response = Http::post('http://127.0.0.1:5000/predict', [
            'riwayat' => $riwayat->map(fn($r) => [
                'tanggal' => $r->tanggal->format('Y-m-d'),
                'keluar' => $r->keluar,
            ]),
            'hari_prediksi' => 7,
        ]);

        if ($response->failed()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghubungi service forecasting (ML)',
            ], 502);
        }

        $mlResult = $response->json('data');

        $stokSaatIni = StokOpname::where('jenis_kopi_id', $jenisKopi->id)
            ->orderBy('tanggal', 'desc')
            ->first()->stok_akhir;

        $prediksi7Hari = $mlResult['total_prediksi_periode'];
        $rataHarian = $prediksi7Hari / 7;
        $predWeekend = $mlResult['prediksi_konsumsi_hari_ramai'];

        // Estimasi tanggal habis (pakai rata-rata harian dari hasil ML)
        $rataHarian = $rataHarian ?: 1;
        $hariTersisa = $stokSaatIni / $rataHarian;
        $estimasiHabis = Carbon::today()->addDays((int) round($hariTersisa));

        // Safety stock & reorder point
        $safetyStock = 2 * $predWeekend;
        $leadTime = 1;
        $reorderPoint = ($rataHarian * $leadTime) + $safetyStock;
        $perluRestock = $stokSaatIni < $reorderPoint;

        $jumlahDisarankan = ($rataHarian * 7) - $stokSaatIni + $safetyStock;
        $jumlahDisarankan = $jumlahDisarankan > 0 ? (int) (ceil($jumlahDisarankan / 1000) * 1000) : 0;

        return response()->json([
            'status' => 'success',
            'data' => [
                'jenis_kopi' => $jenisKopi->nama,
                'stok_saat_ini' => $stokSaatIni,
                'model_ml' => $mlResult['model'],
                'prediksi_harian' => $mlResult['prediksi_harian'],
                'prediksi_kebutuhan_7_hari' => round($prediksi7Hari),
                'estimasi_habis_pada' => $estimasiHabis->format('Y-m-d'),
                'rekomendasi_restock' => [
                    'perlu_restock' => $perluRestock,
                    'jumlah_disarankan' => $jumlahDisarankan,
                ],
            ],
        ], 200);
    }
}