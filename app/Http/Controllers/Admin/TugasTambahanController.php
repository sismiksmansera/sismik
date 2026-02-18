<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TugasTambahanController extends Controller
{
    /**
     * Display the tugas tambahan lainnya page
     */
    public function index()
    {
        $jenisList = DB::table('jenis_tugas_tambahan_lain')
            ->orderBy('nama_tugas', 'ASC')
            ->get();

        return view('admin.tugas-tambahan.index', compact('jenisList'));
    }

    /**
     * Store a new jenis tugas tambahan (AJAX)
     */
    public function storeJenis(Request $request)
    {
        $request->validate([
            'nama_tugas' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:500',
        ]);

        $nama = trim($request->input('nama_tugas'));
        $deskripsi = trim($request->input('deskripsi', ''));

        // Check duplicate
        $exists = DB::table('jenis_tugas_tambahan_lain')
            ->whereRaw('LOWER(nama_tugas) = ?', [strtolower($nama)])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis tugas tambahan "' . $nama . '" sudah ada!'
            ]);
        }

        DB::table('jenis_tugas_tambahan_lain')->insert([
            'nama_tugas' => $nama,
            'deskripsi' => $deskripsi ?: null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $jenisList = DB::table('jenis_tugas_tambahan_lain')
            ->orderBy('nama_tugas', 'ASC')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Jenis tugas tambahan berhasil ditambahkan!',
            'data' => $jenisList
        ]);
    }

    /**
     * Delete a jenis tugas tambahan (AJAX)
     */
    public function deleteJenis(Request $request)
    {
        $id = $request->input('id');

        $exists = DB::table('jenis_tugas_tambahan_lain')->where('id', $id)->exists();
        if (!$exists) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan!'
            ]);
        }

        DB::table('jenis_tugas_tambahan_lain')->where('id', $id)->delete();

        $jenisList = DB::table('jenis_tugas_tambahan_lain')
            ->orderBy('nama_tugas', 'ASC')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Jenis tugas tambahan berhasil dihapus!',
            'data' => $jenisList
        ]);
    }

    /**
     * Update a jenis tugas tambahan (AJAX)
     */
    public function updateJenis(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'nama_tugas' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:500',
        ]);

        $id = $request->input('id');
        $nama = trim($request->input('nama_tugas'));
        $deskripsi = trim($request->input('deskripsi', ''));

        $exists = DB::table('jenis_tugas_tambahan_lain')->where('id', $id)->exists();
        if (!$exists) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan!'
            ]);
        }

        // Check duplicate (exclude current)
        $duplicate = DB::table('jenis_tugas_tambahan_lain')
            ->whereRaw('LOWER(nama_tugas) = ?', [strtolower($nama)])
            ->where('id', '!=', $id)
            ->exists();

        if ($duplicate) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis tugas tambahan "' . $nama . '" sudah ada!'
            ]);
        }

        DB::table('jenis_tugas_tambahan_lain')->where('id', $id)->update([
            'nama_tugas' => $nama,
            'deskripsi' => $deskripsi ?: null,
            'updated_at' => now(),
        ]);

        $jenisList = DB::table('jenis_tugas_tambahan_lain')
            ->orderBy('nama_tugas', 'ASC')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Jenis tugas tambahan berhasil diperbarui!',
            'data' => $jenisList
        ]);
    }
}
