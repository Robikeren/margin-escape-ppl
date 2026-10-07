<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\StokOpname;
use App\Models\User;
use Illuminate\Http\Request;

class StokOpnameController extends Controller
{
    // API-03: Input stok opname harian
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_kopi_id' => 'required|exists:jenis_kopi,id',
            'tanggal'       => 'required|date',
            'stok_awal'     => 'required|integer|min:0',
            'keluar'        => 'required|integer|min:0',
            'masuk'         => 'nullable|integer|min:0',
        ]);

        // FR-05: cegah input ganda untuk tanggal + jenis kopi yang sama
        $sudahAda = StokOpname::where('jenis_kopi_id', $validated['jenis_kopi_id'])
            ->whereDate('tanggal', $validated['tanggal'])
            ->exists();

        if ($sudahAda) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data untuk tanggal ini sudah pernah diinput',
            ], 422);
        }

        $masuk = $validated['masuk'] ?? 0;

        // FR-04: hitung stok akhir otomatis
        $stokAkhir = $validated['stok_awal'] - $validated['keluar'] + $masuk;

        $stokOpname = StokOpname::create([
            'jenis_kopi_id' => $validated['jenis_kopi_id'],
            'user_id'       => $request->user()->id, // staff yang login, diambil dari token
            'tanggal'       => $validated['tanggal'],
            'stok_awal'     => $validated['stok_awal'],
            'keluar'        => $validated['keluar'],
            'masuk'         => $masuk,
            'stok_akhir'    => $stokAkhir,
        ]);

        // Jalankan logika pengecekan dan pengiriman notifikasi otomatis
        $this->cekDanKirimNotifikasi($stokOpname, $validated['jenis_kopi_id']);

        return response()->json([
            'status' => 'success',
            'data'   => $stokOpname,
        ], 201);
    }

    private function cekDanKirimNotifikasi($stokOpname, $jenisKopiId)
    {
        // Rata-rata konsumsi 14 hari terakhir sebagai acuan threshold
        $rataRata = StokOpname::where('jenis_kopi_id', $jenisKopiId)
            ->orderBy('tanggal', 'desc')
            ->limit(14)
            ->avg('keluar') ?: 1;

        // Rules threshold: kritis kalau stok < 1x rata-rata konsumsi
        $status = $stokOpname->stok_akhir < $rataRata ? 'kritis' : 'aman_atau_menipis';

        if ($status !== 'kritis') {
            return; // hanya kirim notifikasi kalau kritis
        }

        // Cegah notifikasi berulang di hari yang sama untuk jenis kopi yang sama
        $sudahAda = Notifikasi::where('jenis_kopi_id', $jenisKopiId)
            ->whereDate('tanggal', $stokOpname->tanggal)
            ->exists();

        if ($sudahAda) {
            return;
        }

        $jenisKopi = $stokOpname->jenisKopi;

        $notifikasi = Notifikasi::create([
            'jenis_kopi_id' => $jenisKopiId,
            'tanggal'       => $stokOpname->tanggal,
            'pesan'         => "Stok {$jenisKopi->nama} kritis (tersisa {$stokOpname->stok_akhir} gram), segera lakukan restock",
            'status_dibaca' => false,
        ]);

        // Kirim ke semua Owner dan Staff (sesuai FR-10)
        $userIds = User::whereIn('role', ['owner', 'staff'])->pluck('id');
        $notifikasi->users()->attach($userIds);
    }

    // API-04: Riwayat stok opname (dengan filter)
    public function index(Request $request)
    {
        $query = StokOpname::with('jenisKopi:id,nama');

        if ($request->has('jenis_kopi_id')) {
            $query->where('jenis_kopi_id', $request->jenis_kopi_id);
        }

        if ($request->has('start')) {
            $query->whereDate('tanggal', '>=', $request->start);
        }

        if ($request->has('end')) {
            $query->whereDate('tanggal', '<=', $request->end);
        }

        $data = $query->orderBy('tanggal', 'desc')->get()->map(function ($item) {
            return [
                'id'         => $item->id,
                'tanggal'    => $item->tanggal->format('Y-m-d'),
                'jenis_kopi' => $item->jenisKopi->nama,
                'stok_awal'  => $item->stok_awal,
                'keluar'     => $item->keluar,
                'masuk'      => $item->masuk,
                'stok_akhir' => $item->stok_akhir,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ], 200);
    }
}