<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\DataPeriodik;
use App\Models\PiketKbm;
use App\Models\CatatanPiketKbm;
use App\Services\EffectiveDateService;

class CatatanPiketController extends Controller
{
    public function index()
    {
        $guru = Auth::guard('guru')->user();
        $periodik = DataPeriodik::aktif()->first();
        $tahunAktif = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = $periodik->semester ?? '';

        // Get effective date
        $effectiveDate = EffectiveDateService::getEffectiveDate();
        $hariIni = $effectiveDate['hari'];
        $tanggalHariIni = $effectiveDate['date'];
        $isTesting = $effectiveDate['is_testing'];

        // Check if guru is on piket today
        $piketHariIni = PiketKbm::where('hari', $hariIni)
            ->where('guru_id', $guru->id)
            ->where('tipe_guru', 'guru')
            ->first();

        if (!$piketHariIni) {
            return redirect()->route('guru.tugas-tambahan')
                ->with('error', 'Anda tidak bertugas piket hari ini.');
        }

        // Get jam pelajaran settings
        $periodikId = $periodik->id ?? 0;
        $jamRow = DB::table('jam_pelajaran_setting')
            ->where('periodik_id', $periodikId)
            ->first();

        $jamSetting = [];
        $maxJam = 0;
        if ($jamRow) {
            for ($i = 1; $i <= 11; $i++) {
                $mulai = $jamRow->{"jp_{$i}_mulai"} ?? null;
                $selesai = $jamRow->{"jp_{$i}_selesai"} ?? null;
                if ($mulai && $selesai) {
                    $jamSetting[$i] = [
                        'mulai' => $mulai,
                        'selesai' => $selesai,
                    ];
                    $maxJam = $i;
                }
            }
        }
        if ($maxJam == 0) $maxJam = 8;

        // Get ALL jadwal for today grouped by jam -> rombel -> guru
        $jadwalHariIni = DB::table('jadwal_pelajaran as jp')
            ->join('rombel as r', 'jp.id_rombel', '=', 'r.id')
            ->join('mata_pelajaran as mp', 'jp.id_mapel', '=', 'mp.id')
            ->select('jp.jam_ke', 'jp.nama_guru', 'mp.nama_mapel', 'r.nama_rombel', 'jp.id_rombel', 'jp.id_mapel')
            ->where('jp.hari', $hariIni)
            ->where('jp.tahun_pelajaran', $tahunAktif)
            ->whereRaw("LOWER(jp.semester) = LOWER(?)", [$semesterAktif])
            ->orderByRaw('CAST(jp.jam_ke AS UNSIGNED)')
            ->orderBy('r.nama_rombel')
            ->orderBy('jp.nama_guru')
            ->get();

        // Structure: jam -> rombel -> [guru entries]
        $jadwalPerJam = [];
        foreach ($jadwalHariIni as $j) {
            $jamKe = (int) $j->jam_ke;
            $rombel = $j->nama_rombel;
            if (!isset($jadwalPerJam[$jamKe])) {
                $jadwalPerJam[$jamKe] = [];
            }
            if (!isset($jadwalPerJam[$jamKe][$rombel])) {
                $jadwalPerJam[$jamKe][$rombel] = [];
            }
            $jadwalPerJam[$jamKe][$rombel][] = [
                'nama_guru' => $j->nama_guru,
                'nama_mapel' => $j->nama_mapel,
                'id_rombel' => $j->id_rombel,
                'id_mapel' => $j->id_mapel,
            ];
        }

        // Natural sort rombel names within each jam (so X.10 comes after X.9)
        foreach ($jadwalPerJam as $jamKe => &$rombelData) {
            uksort($rombelData, 'strnatcmp');
        }
        unset($rombelData);

        // Get izin guru for today
        $izinGuruHariIni = [];
        if (Schema::hasTable('izin_guru')) {
            $izinRows = DB::table('izin_guru')
                ->where('tanggal_izin', $tanggalHariIni)
                ->get();
            foreach ($izinRows as $izin) {
                // Key: guru-rombel-jam combination
                $jamList = explode(',', $izin->jam_ke);
                foreach ($jamList as $jk) {
                    $jk = trim($jk);
                    $key = $izin->guru . '|' . $izin->id_rombel . '|' . $jk;
                    $izinGuruHariIni[$key] = [
                        'alasan' => $izin->alasan_izin,
                        'tugas' => $izin->uraian_tugas ?? '',
                    ];
                }
            }
        }

        // Get existing catatan for today
        $catatanHariIni = CatatanPiketKbm::where('tanggal', $tanggalHariIni)
            ->where('piket_kbm_id', $piketHariIni->id)
            ->get()
            ->keyBy(function ($item) {
                return $item->jam_ke . '|' . $item->nama_guru . '|' . $item->nama_rombel;
            });

        // Get all piket guru for today
        $semuaPiketHariIni = PiketKbm::where('hari', $hariIni)
            ->orderBy('created_at')
            ->get();

        return view('guru.catatan-piket', compact(
            'guru',
            'hariIni',
            'tanggalHariIni',
            'isTesting',
            'jamSetting',
            'maxJam',
            'jadwalPerJam',
            'catatanHariIni',
            'izinGuruHariIni',
            'piketHariIni',
            'semuaPiketHariIni',
            'tahunAktif',
            'semesterAktif'
        ));
    }

    public function store(Request $request)
    {
        $guru = Auth::guard('guru')->user();

        $request->validate([
            'piket_kbm_id' => 'required|integer',
            'tanggal' => 'required|date',
            'jam_ke' => 'required|integer',
            'nama_guru' => 'required|string',
            'status_kehadiran' => 'required|in:Hadir Tepat Waktu,Hadir Terlambat,Izin,Tanpa Keterangan',
        ]);

        CatatanPiketKbm::updateOrCreate(
            [
                'piket_kbm_id' => $request->piket_kbm_id,
                'tanggal' => $request->tanggal,
                'jam_ke' => $request->jam_ke,
                'nama_guru' => $request->nama_guru,
                'nama_rombel' => $request->nama_rombel,
            ],
            [
                'nama_mapel' => $request->nama_mapel,
                'status_kehadiran' => $request->status_kehadiran,
                'keterangan' => $request->keterangan,
                'dicatat_oleh' => $guru->nama,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Catatan piket berhasil disimpan'
        ]);
    }
}
