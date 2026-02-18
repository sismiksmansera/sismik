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

        // Get all guru and guru_bk for the modal selects
        $guruList = DB::table('guru')
            ->select('id', 'nama', 'nip')
            ->where('status', 'Aktif')
            ->orderBy('nama', 'ASC')
            ->get();

        $guruBKList = DB::table('guru_bk')
            ->select('id', 'nama', 'nip')
            ->where('status', 'Aktif')
            ->orderBy('nama', 'ASC')
            ->get();

        // Get existing tugas tambahan with joined data
        $tugasList = DB::table('tugas_tambahan_guru as t')
            ->join('jenis_tugas_tambahan_lain as j', 't.jenis_tugas_id', '=', 'j.id')
            ->select('t.*', 'j.nama_tugas as jenis_nama')
            ->orderBy('j.nama_tugas', 'ASC')
            ->orderBy('t.created_at', 'DESC')
            ->get();

        // Resolve guru names
        foreach ($tugasList as $tugas) {
            if ($tugas->tipe_guru === 'guru') {
                $guru = DB::table('guru')->where('id', $tugas->guru_id)->first();
                $tugas->nama_guru = $guru ? $guru->nama : 'Tidak ditemukan';
                $tugas->nip_guru = $guru ? $guru->nip : '-';
            } else {
                $guru = DB::table('guru_bk')->where('id', $tugas->guru_id)->first();
                $tugas->nama_guru = $guru ? $guru->nama : 'Tidak ditemukan';
                $tugas->nip_guru = $guru ? $guru->nip : '-';
            }
        }

        return view('admin.tugas-tambahan.index', compact(
            'jenisList', 'guruList', 'guruBKList', 'tugasList'
        ));
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

    /**
     * Store tugas tambahan guru (AJAX) - supports multiple guru
     */
    public function storeTugas(Request $request)
    {
        $request->validate([
            'jenis_tugas_id' => 'required|integer|exists:jenis_tugas_tambahan_lain,id',
            'guru_ids' => 'required|array|min:1',
            'guru_ids.*' => 'required|string', // format: "guru_1" or "gurubk_2"
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $jenisId = $request->input('jenis_tugas_id');
        $guruIds = $request->input('guru_ids');
        $keterangan = trim($request->input('keterangan', ''));

        $inserted = 0;
        $duplicates = 0;

        foreach ($guruIds as $guruComposite) {
            // Parse "guru_1" or "gurubk_2"
            $parts = explode('_', $guruComposite, 2);
            if (count($parts) !== 2) continue;

            $tipeGuru = $parts[0] === 'gurubk' ? 'guru_bk' : 'guru';
            $guruId = (int) $parts[1];

            // Check if already assigned
            $exists = DB::table('tugas_tambahan_guru')
                ->where('jenis_tugas_id', $jenisId)
                ->where('tipe_guru', $tipeGuru)
                ->where('guru_id', $guruId)
                ->exists();

            if ($exists) {
                $duplicates++;
                continue;
            }

            DB::table('tugas_tambahan_guru')->insert([
                'jenis_tugas_id' => $jenisId,
                'tipe_guru' => $tipeGuru,
                'guru_id' => $guruId,
                'keterangan' => $keterangan ?: null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $inserted++;
        }

        $msg = $inserted . ' tugas tambahan berhasil ditambahkan!';
        if ($duplicates > 0) {
            $msg .= ' (' . $duplicates . ' sudah ada sebelumnya)';
        }

        return response()->json([
            'success' => true,
            'message' => $msg
        ]);
    }

    /**
     * Delete a tugas tambahan guru (AJAX)
     */
    public function deleteTugas(Request $request)
    {
        $id = $request->input('id');

        $exists = DB::table('tugas_tambahan_guru')->where('id', $id)->exists();
        if (!$exists) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan!'
            ]);
        }

        DB::table('tugas_tambahan_guru')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tugas tambahan berhasil dihapus!'
        ]);
    }
}
