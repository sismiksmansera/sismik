<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Admin\CekPresensiController as AdminCekPresensiController;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;

class CekPresensiController extends AdminCekPresensiController
{
    /**
     * Display the selector page (Guru BK version)
     */
    public function index()
    {
        // Get active period
        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? date('Y') . '/' . (date('Y') + 1);
        $semesterAktif = $periodik->semester ?? 'Ganjil';

        // Get all rombel for active period (unique only)
        $rombelList = DB::table('rombel')
            ->selectRaw('MIN(id) as id, nama_rombel, MIN(tingkat) as tingkat')
            ->where('tahun_pelajaran', $tahunPelajaran)
            ->where('semester', $semesterAktif)
            ->groupBy('nama_rombel')
            ->orderByRaw('MIN(tingkat)')
            ->orderByRaw("CAST(REGEXP_SUBSTR(nama_rombel, '[0-9]+$') AS UNSIGNED)")
            ->orderBy('nama_rombel')
            ->get();

        $routePrefix = 'guru_bk';
        $layout = 'layouts.app-guru-bk';

        return view('admin.cek-presensi', compact('tahunPelajaran', 'semesterAktif', 'rombelList', 'routePrefix', 'layout'));
    }
}
