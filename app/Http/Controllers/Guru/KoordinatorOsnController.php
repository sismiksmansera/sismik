<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AjangTalenta;

class KoordinatorOsnController extends Controller
{
    public function index()
    {
        $currentYear = date('Y');

        // Get all OSN ajang talenta for current year
        $osnList = AjangTalenta::where('nama_ajang', 'LIKE', '%OSN%')
            ->where('tahun', $currentYear)
            ->orderBy('nama_ajang', 'ASC')
            ->get();

        // Load peserta count for each
        foreach ($osnList as $osn) {
            $osn->jumlah_peserta = DB::table('peserta_ajang_talenta')
                ->where('ajang_talenta_id', $osn->id)
                ->count();
        }

        return view('guru.koordinator-osn', compact('osnList', 'currentYear'));
    }
}
