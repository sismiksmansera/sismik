<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Admin\DownloadPresensiController as AdminDownloadPresensiController;
use App\Models\DataPeriodik;
use Illuminate\Support\Facades\DB;

class DownloadPresensiController extends AdminDownloadPresensiController
{
    /**
     * Display the download presensi page (Guru BK version)
     */
    public function index()
    {
        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? date('Y') . '/' . (date('Y') + 1);
        $semesterAktif = $periodik->semester ?? 'Ganjil';

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

        return view('admin.download-presensi', compact('tahunPelajaran', 'semesterAktif', 'rombelList', 'routePrefix', 'layout'));
    }
}
