<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;
use App\Models\Siswa;
use Carbon\Carbon;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $siswa = Auth::guard('siswa')->user();
        $periodik = DataPeriodik::aktif()->first();
        
        if (!$periodik) {
            return view('siswa.presensi', [
                'siswa' => $siswa,
                'periodik' => null,
                'rekapMapel' => [],
                'totalHadir' => 0,
                'totalDispen' => 0,
                'totalIzin' => 0,
                'totalSakit' => 0,
                'totalAlfa' => 0,
                'totalBolos' => 0,
                'totalPresensi' => 0,
                'persentaseKehadiran' => 0,
                'tanggalMulai' => null,
                'tanggalSelesai' => null,
                'minDate' => null,
                'maxDate' => null,
            ]);
        }
        
        // Calculate date range based on semester
        $tahunAjaran = explode('/', $periodik->tahun_pelajaran);
        $tahunAwal = intval($tahunAjaran[0]);
        $tahunAkhir = intval($tahunAjaran[1] ?? $tahunAwal + 1);
        
        if ($periodik->semester == 'Ganjil') {
            $minDate = $tahunAwal . '-07-01';
            $maxDate = $tahunAwal . '-12-31';
        } else {
            $minDate = $tahunAkhir . '-01-01';
            $maxDate = $tahunAkhir . '-06-30';
        }
        
        // Filter dates with validation
        $tanggalMulai = $request->get('tanggal_mulai', $minDate);
        $tanggalSelesai = $request->get('tanggal_selesai', date('Y-m-d'));
        
        // Ensure dates are within allowed range
        if ($tanggalMulai < $minDate) $tanggalMulai = $minDate;
        if ($tanggalMulai > $maxDate) $tanggalMulai = $maxDate;
        if ($tanggalSelesai < $minDate) $tanggalSelesai = $minDate;
        if ($tanggalSelesai > $maxDate) $tanggalSelesai = $maxDate;
        
        // Get attendance summary per subject
        $rekapMapel = DB::table('presensi_siswa')
            ->select(
                'mata_pelajaran',
                DB::raw('COUNT(*) as total_presensi'),
                DB::raw("SUM(CASE WHEN presensi = 'H' THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN presensi = 'D' THEN 1 ELSE 0 END) as dispen"),
                DB::raw("SUM(CASE WHEN presensi = 'I' THEN 1 ELSE 0 END) as izin"),
                DB::raw("SUM(CASE WHEN presensi = 'S' THEN 1 ELSE 0 END) as sakit"),
                DB::raw("SUM(CASE WHEN presensi = 'A' THEN 1 ELSE 0 END) as alfa"),
                DB::raw("SUM(CASE WHEN presensi = 'B' THEN 1 ELSE 0 END) as bolos")
            )
            ->where('nisn', $siswa->nisn)
            ->where('tahun_pelajaran', $periodik->tahun_pelajaran)
            ->where('semester', $periodik->semester)
            ->whereBetween('tanggal_presensi', [$tanggalMulai, $tanggalSelesai])
            ->groupBy('mata_pelajaran')
            ->orderBy('mata_pelajaran')
            ->get();
        
        // Calculate totals
        $totalHadir = $rekapMapel->sum('hadir');
        $totalDispen = $rekapMapel->sum('dispen');
        $totalIzin = $rekapMapel->sum('izin');
        $totalSakit = $rekapMapel->sum('sakit');
        $totalAlfa = $rekapMapel->sum('alfa');
        $totalBolos = $rekapMapel->sum('bolos');
        $totalPresensi = $rekapMapel->sum('total_presensi');
        
        $persentaseKehadiran = $totalPresensi > 0 
            ? round(($totalHadir / $totalPresensi) * 100, 1) 
            : 0;
        
        return view('siswa.presensi', compact(
            'siswa',
            'periodik',
            'rekapMapel',
            'totalHadir',
            'totalDispen',
            'totalIzin',
            'totalSakit',
            'totalAlfa',
            'totalBolos',
            'totalPresensi',
            'persentaseKehadiran',
            'tanggalMulai',
            'tanggalSelesai',
            'minDate',
            'maxDate'
        ));
    }
    
    public function detail(Request $request)
    {
        $siswa = Auth::guard('siswa')->user();
        $periodik = DataPeriodik::aktif()->first();
        
        $filterType = $request->get('filter_type', ''); // 'kategori' or 'mapel'
        $filterValue = $request->get('filter_value', '');
        $tanggalMulai = $request->get('tanggal_mulai', date('Y-m-d', strtotime('-1 month')));
        $tanggalSelesai = $request->get('tanggal_selesai', date('Y-m-d'));
        
        $query = DB::table('presensi_siswa')
            ->where('nisn', $siswa->nisn)
            ->where('tahun_pelajaran', $periodik->tahun_pelajaran)
            ->where('semester', $periodik->semester)
            ->whereBetween('tanggal_presensi', [$tanggalMulai, $tanggalSelesai]);
        
        if ($filterType == 'kategori') {
            $query->where('presensi', $filterValue);
        } else if ($filterType == 'mapel') {
            $query->where('mata_pelajaran', $filterValue);
        }
        
        $data = $query->orderBy('tanggal_presensi', 'desc')
            ->orderBy('mata_pelajaran')
            ->get()
            ->map(function($row) {
                // Determine active jam pelajaran
                $jamAktif = [];
                for ($i = 1; $i <= 11; $i++) {
                    $jamKey = 'jam_ke_' . $i;
                    if (!empty($row->$jamKey) && $row->$jamKey != '-') {
                        $jamAktif[] = $i;
                    }
                }
                
                // Format jam display
                $jamDisplay = '';
                if (!empty($jamAktif)) {
                    if (count($jamAktif) == 1) {
                        $jamDisplay = 'Jam ke-' . $jamAktif[0];
                    } else {
                        $first = $jamAktif[0];
                        $last = end($jamAktif);
                        if ($last - $first + 1 == count($jamAktif)) {
                            $jamDisplay = 'Jam ke-' . $first . ' s/d ' . $last;
                        } else {
                            $jamDisplay = 'Jam ke-' . implode(', ', $jamAktif);
                        }
                    }
                }
                
                return [
                    'tanggal' => Carbon::parse($row->tanggal_presensi)->format('d M Y'),
                    'hari' => $this->getHariIndonesia(Carbon::parse($row->tanggal_presensi)->dayOfWeekIso),
                    'mapel' => $row->mata_pelajaran,
                    'presensi' => $row->presensi,
                    'guru' => $row->guru_pengajar,
                    'jam' => $jamDisplay,
                ];
            });
        
        return response()->json(['success' => true, 'data' => $data]);
    }
    
    private function getHariIndonesia(int $dayNum): string
    {
        $hari = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        return $hari[$dayNum] ?? '';
    }
}
