<?php

namespace App\Http\Controllers;

use App\Models\KartuLoginUjian;
use Illuminate\Http\Request;

class CariLoginUjianController extends Controller
{
    /**
     * Display the public search page.
     */
    public function index()
    {
        $loginSettings = \App\Models\LoginSettings::first();
        return view('cari-login-ujian', compact('loginSettings'));
    }

    /**
     * AJAX search by student name.
     */
    public function search(Request $request)
    {
        $query = trim($request->input('query', ''));

        if (empty($query) || strlen($query) < 2) {
            return response()->json(['data' => [], 'message' => 'Masukkan minimal 2 karakter']);
        }

        $results = KartuLoginUjian::where('nama_siswa', 'LIKE', "%{$query}%")
            ->orderBy('kelas')
            ->orderBy('nama_siswa')
            ->limit(50)
            ->get();

        return response()->json([
            'data' => $results,
            'count' => $results->count(),
        ]);
    }
}
