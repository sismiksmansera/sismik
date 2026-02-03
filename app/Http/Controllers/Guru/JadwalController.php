<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        // Get logged in guru using guru guard
        $guru = Auth::guard('guru')->user();
        if (!$guru) {
            return redirect()->route('login')->with('error', 'Data guru tidak ditemukan!');
        }

        $namaGuru = $guru->nama;

        // Day list
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $filterHari = $request->get('hari');
        if (!in_array($filterHari, $hariList)) {
            $filterHari = '';
        }

        // Get active period using DataPeriodik model
        $periodik = DataPeriodik::aktif()->first();
        
        $tahunPelajaranAktif = $periodik->tahun_pelajaran ?? date('Y') . '/' . (date('Y') + 1);
        $semesterAktif = $periodik->semester ?? 'ganjil';

        // Filters
        $tahunFilter = $request->get('tahun', $tahunPelajaranAktif);
        $semesterFilter = $request->get('semester', $semesterAktif);
        $toggleView = $request->get('view', 'my_schedule');

        // Build query condition based on toggle
        if ($toggleView === 'all_schedule') {
            $queryCondition = "";
            $viewTitle = "JADWAL PELAJARAN";
        } else {
            $queryCondition = "AND nama_guru = '$namaGuru'";
            $viewTitle = "JADWAL PELAJARAN";
        }

        // Get unique tahun pelajaran from database
        $tahunList = DB::table('jadwal_pelajaran')
            ->select('tahun_pelajaran')
            ->distinct()
            ->whereRaw("1=1 $queryCondition")
            ->orderBy('tahun_pelajaran', 'desc')
            ->pluck('tahun_pelajaran');

        // Build stats conditions
        $statsConditions = "WHERE 1=1 $queryCondition";
        if (!empty($tahunFilter)) {
            $statsConditions .= " AND tahun_pelajaran = '$tahunFilter'";
        }
        if (!empty($semesterFilter)) {
            $statsConditions .= " AND semester = '$semesterFilter'";
        }

        // Calculate statistics
        $totalJadwal = DB::selectOne("SELECT COUNT(*) as total FROM jadwal_pelajaran $statsConditions")->total ?? 0;
        $totalRombel = DB::selectOne("SELECT COUNT(DISTINCT id_rombel) as total FROM jadwal_pelajaran $statsConditions")->total ?? 0;
        $hariAktif = DB::selectOne("SELECT COUNT(DISTINCT hari) as total FROM jadwal_pelajaran $statsConditions")->total ?? 0;

        // Prepare schedule data for each day
        $jadwalPerHari = [];
        $loopHari = $filterHari ? [$filterHari] : $hariList;

        foreach ($loopHari as $hari) {
            // Count jadwal for this day
            $countConditions = "WHERE hari = '$hari' $queryCondition";
            if (!empty($tahunFilter)) {
                $countConditions .= " AND tahun_pelajaran = '$tahunFilter'";
            }
            if (!empty($semesterFilter)) {
                $countConditions .= " AND semester = '$semesterFilter'";
            }
            
            $count = DB::selectOne("SELECT COUNT(*) as total FROM jadwal_pelajaran $countConditions")->total ?? 0;
            
            if ($count == 0) {
                continue;
            }

            // Get rombel list for this day
            $rombelList = DB::select("
                SELECT DISTINCT r.id, r.nama_rombel 
                FROM rombel r 
                JOIN jadwal_pelajaran jp ON r.id = jp.id_rombel 
                WHERE jp.hari = '$hari' $queryCondition
                ORDER BY r.nama_rombel ASC
            ");

            $rombelData = [];
            foreach ($rombelList as $rombel) {
                // Build conditions for jadwal query
                $jadwalConditions = "WHERE jp.id_rombel = '{$rombel->id}' AND jp.hari = '$hari'";
                
                if ($toggleView === 'my_schedule') {
                    $jadwalConditions .= " AND jp.nama_guru = '$namaGuru'";
                }
                
                if (!empty($tahunFilter)) {
                    $jadwalConditions .= " AND jp.tahun_pelajaran = '$tahunFilter'";
                }
                if (!empty($semesterFilter)) {
                    $jadwalConditions .= " AND jp.semester = '$semesterFilter'";
                }

                // Query jadwal
                $jadwalItems = DB::select("
                    SELECT jp.jam_ke, jp.nama_guru, mp.nama_mapel, jp.tahun_pelajaran, jp.semester
                    FROM jadwal_pelajaran jp
                    JOIN mata_pelajaran mp ON jp.id_mapel = mp.id
                    $jadwalConditions
                    ORDER BY CAST(jp.jam_ke AS UNSIGNED) ASC
                ");

                if (count($jadwalItems) > 0) {
                    // Count unique teachers
                    $guruList = [];
                    foreach ($jadwalItems as $item) {
                        if (!in_array($item->nama_guru, $guruList)) {
                            $guruList[] = $item->nama_guru;
                        }
                    }

                    // Group consecutive jam with same mapel+guru
                    $groupedJadwal = $this->groupJadwal($jadwalItems);

                    $rombelData[] = [
                        'id' => $rombel->id,
                        'nama_rombel' => $rombel->nama_rombel,
                        'rombel_id' => 'rombel_' . strtolower(str_replace(' ', '_', $rombel->nama_rombel)) . '_' . strtolower($hari),
                        'jadwal_count' => count($jadwalItems),
                        'mapel_count' => count($jadwalItems),
                        'guru_count' => count($guruList),
                        'jadwal' => $groupedJadwal,
                    ];
                }
            }

            if (!empty($rombelData)) {
                $jadwalPerHari[] = [
                    'hari' => $hari,
                    'count' => $count,
                    'rombel' => $rombelData,
                ];
            }
        }

        return view('guru.jadwal', compact(
            'guru',
            'hariList',
            'filterHari',
            'tahunFilter',
            'semesterFilter',
            'toggleView',
            'viewTitle',
            'tahunList',
            'totalJadwal',
            'totalRombel',
            'hariAktif',
            'jadwalPerHari'
        ));
    }

    /**
     * Group consecutive jam with same mapel+guru
     */
    private function groupJadwal($jadwalItems)
    {
        $grouped = [];
        $items = collect($jadwalItems)->toArray();
        $i = 0;

        while ($i < count($items)) {
            $current = (array) $items[$i];
            $jamStart = (int) $current['jam_ke'];
            $jamEnd = $jamStart;

            // Check if next jam is consecutive with same mapel+guru
            while ($i + 1 < count($items)) {
                $next = (array) $items[$i + 1];
                $nextJam = (int) $next['jam_ke'];

                if ($nextJam == $jamEnd + 1 &&
                    $next['nama_mapel'] == $current['nama_mapel'] &&
                    $next['nama_guru'] == $current['nama_guru']) {
                    $jamEnd = $nextJam;
                    $i++;
                } else {
                    break;
                }
            }

            $jamText = $jamStart == $jamEnd 
                ? "Jam ke: $jamStart" 
                : "Jam ke: $jamStart - $jamEnd";

            $grouped[] = [
                'jam_start' => $jamStart,
                'jam_end' => $jamEnd,
                'jam_text' => $jamText,
                'nama_mapel' => $current['nama_mapel'],
                'nama_guru' => $current['nama_guru'],
            ];

            $i++;
        }

        return $grouped;
    }
}
