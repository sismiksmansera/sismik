<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DataPeriodik;
use App\Models\Pelanggaran;

class PelanggaranController extends Controller
{
    public function index()
    {
        $siswa = Auth::guard('siswa')->user();
        $periodik = DataPeriodik::aktif()->first();

        $tahunAktif = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = $periodik->semester ?? '';

        // Get pelanggaran for this siswa in active period
        // Pelanggaran uses many-to-many via pelanggaran_siswa pivot
        $pelanggaranList = Pelanggaran::with('guruBk')
            ->whereHas('siswa', function ($q) use ($siswa) {
                $q->where('siswa.id', $siswa->id);
            })
            ->when($periodik, function ($q) use ($periodik) {
                // Filter by date range of the active academic year
                $tahun = $periodik->tahun_pelajaran; // e.g. "2025/2026"
                $semester = $periodik->semester;

                if ($tahun) {
                    $years = explode('/', $tahun);
                    if (count($years) === 2) {
                        if (strtolower($semester) === 'ganjil') {
                            $startDate = $years[0] . '-07-01';
                            $endDate = $years[0] . '-12-31';
                        } else {
                            $startDate = $years[1] . '-01-01';
                            $endDate = $years[1] . '-06-30';
                        }
                        $q->whereBetween('tanggal', [$startDate, $endDate]);
                    }
                }
            })
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu', 'desc')
            ->get();

        // Stats
        $totalPelanggaran = $pelanggaranList->count();
        $jenisCounts = $pelanggaranList->groupBy('jenis_pelanggaran')->map->count();

        return view('siswa.pelanggaran', compact(
            'siswa',
            'periodik',
            'pelanggaranList',
            'totalPelanggaran',
            'jenisCounts',
            'tahunAktif',
            'semesterAktif'
        ));
    }
}
