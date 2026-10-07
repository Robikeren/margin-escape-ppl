<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    // API-07: Daftar notifikasi untuk user yang sedang login
    public function index(Request $request)
    {
        $user = $request->user();

        $data = $user->notifikasi()
            ->with('jenisKopi:id,nama')
            ->select('notifikasi.*')
            ->orderBy('notifikasi.tanggal', 'desc')
            ->get()
            ->map(fn($n) => [
                'id' => $n->id,
                'jenis_kopi' => $n->jenisKopi->nama,
                'pesan' => $n->pesan,
                'status_dibaca' => $n->status_dibaca,
                'tanggal' => $n->tanggal->format('Y-m-d'),
            ]);

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], 200);
    }

    // Tandai notifikasi sudah dibaca
    public function tandaiDibaca(Request $request, $id)
    {
        $notifikasi = Notifikasi::findOrFail($id);
        $notifikasi->update(['status_dibaca' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Notifikasi ditandai sudah dibaca',
        ], 200);
    }
}