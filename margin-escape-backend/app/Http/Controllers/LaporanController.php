<?php

namespace App\Http\Controllers;

use App\Exports\LaporanExport;
use App\Models\JenisKopi;
use App\Models\Laporan;
use App\Models\StokOpname;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    // API-08: Generate laporan periodik (PDF atau Excel)
    public function generate(Request $request)
    {
        $request->validate([
            'periode_mulai' => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_mulai',
            'format' => 'required|in:pdf,excel',
        ]);

        // FR-11: khusus Owner
        if (! $request->user()->isOwner()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya Owner yang dapat mengakses fitur laporan',
            ], 403);
        }

        $stokOpname = StokOpname::with('jenisKopi:id,nama')
            ->whereDate('tanggal', '>=', $request->periode_mulai)
            ->whereDate('tanggal', '<=', $request->periode_akhir)
            ->orderBy('tanggal')
            ->get();

        if ($stokOpname->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada data stok opname pada periode ini',
            ], 422);
        }

        // Detail per baris
        $detail = $stokOpname->map(fn($item) => [
            'tanggal' => $item->tanggal->format('Y-m-d'),
            'jenis_kopi' => $item->jenisKopi->nama,
            'stok_awal' => $item->stok_awal,
            'keluar' => $item->keluar,
            'masuk' => $item->masuk,
            'stok_akhir' => $item->stok_akhir,
        ]);

        // Ringkasan per jenis kopi
        $ringkasan = $stokOpname->groupBy('jenis_kopi_id')->map(function ($group) {
            $terakhir = $group->sortByDesc('tanggal')->first();
            return [
                'jenis_kopi' => $terakhir->jenisKopi->nama,
                'total_keluar' => $group->sum('keluar'),
                'total_masuk' => $group->sum('masuk'),
                'rata_rata' => $group->avg('keluar'),
                'stok_akhir_terbaru' => $terakhir->stok_akhir,
            ];
        })->values();

        $namaFile = 'laporan-' . $request->periode_mulai . '-sd-' . $request->periode_akhir;

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('laporan.pdf', [
                'periodeMulai' => $request->periode_mulai,
                'periodeAkhir' => $request->periode_akhir,
                'ringkasan' => $ringkasan,
                'detail' => $detail,
            ]);

            $path = 'laporan/' . $namaFile . '.pdf';
            \Storage::disk('public')->put($path, $pdf->output());
        } else {
            $path = 'laporan/' . $namaFile . '.xlsx';
            Excel::store(new LaporanExport($detail), $path, 'public');
        }

        // Simpan record laporan ke database (FR-11)
        Laporan::create([
            'user_id' => $request->user()->id,
            'periode_mulai' => $request->periode_mulai,
            'periode_akhir' => $request->periode_akhir,
            'format' => $request->format,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'url_unduh' => asset('storage/' . $path),
            ],
        ], 200);
    }
}