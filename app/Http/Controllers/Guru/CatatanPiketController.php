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
            ->where('jp.tanggal_mulai', '<=', $tanggalHariIni)
            ->where(function ($q) use ($tanggalHariIni) {
                $q->whereNull('jp.tanggal_akhir')
                  ->orWhere('jp.tanggal_akhir', '>=', $tanggalHariIni);
            })
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

        // Get all piket guru for today
        $semuaPiketHariIni = PiketKbm::where('hari', $hariIni)
            ->orderBy('created_at')
            ->get();

        // Get existing catatan for today from ALL piket team members
        $allPiketIds = $semuaPiketHariIni->pluck('id')->toArray();
        $catatanHariIni = CatatanPiketKbm::where('tanggal', $tanggalHariIni)
            ->whereIn('piket_kbm_id', $allPiketIds)
            ->get()
            ->keyBy(function ($item) {
                return $item->jam_ke . '|' . $item->nama_guru . '|' . $item->nama_rombel;
            });

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
                'tanggal' => $request->tanggal,
                'jam_ke' => $request->jam_ke,
                'nama_guru' => $request->nama_guru,
                'nama_rombel' => $request->nama_rombel,
            ],
            [
                'piket_kbm_id' => $request->piket_kbm_id,
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

    public function cetak()
    {
        $guru = Auth::guard('guru')->user();
        $periodik = DataPeriodik::aktif()->first();
        $tahunAktif = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = $periodik->semester ?? '';

        $effectiveDate = EffectiveDateService::getEffectiveDate();
        $hariIni = $effectiveDate['hari'];
        $tanggalHariIni = $effectiveDate['date'];

        // Check if guru is on piket today
        $piketHariIni = PiketKbm::where('hari', $hariIni)
            ->where('guru_id', $guru->id)
            ->where('tipe_guru', 'guru')
            ->first();

        if (!$piketHariIni) {
            return redirect()->route('guru.catatan-piket');
        }

        // Get jam pelajaran settings
        $periodikId = $periodik->id ?? 0;
        $jamRow = DB::table('jam_pelajaran_setting')
            ->where('periodik_id', $periodikId)
            ->first();
        $maxJam = 0;
        if ($jamRow) {
            for ($i = 1; $i <= 11; $i++) {
                $mulai = $jamRow->{"jp_{$i}_mulai"} ?? null;
                $selesai = $jamRow->{"jp_{$i}_selesai"} ?? null;
                if ($mulai && $selesai) $maxJam = $i;
            }
        }
        if ($maxJam == 0) $maxJam = 8;

        // Get jadwal hari ini
        $jadwalHariIni = DB::table('jadwal_pelajaran as jp')
            ->join('rombel as r', 'jp.id_rombel', '=', 'r.id')
            ->join('mata_pelajaran as mp', 'jp.id_mapel', '=', 'mp.id')
            ->select('jp.jam_ke', 'jp.nama_guru', 'mp.nama_mapel', 'r.nama_rombel', 'jp.id_rombel', 'jp.id_mapel')
            ->where('jp.hari', $hariIni)
            ->where('jp.tahun_pelajaran', $tahunAktif)
            ->whereRaw("LOWER(jp.semester) = LOWER(?)", [$semesterAktif])
            ->where('jp.tanggal_mulai', '<=', $tanggalHariIni)
            ->where(function ($q) use ($tanggalHariIni) {
                $q->whereNull('jp.tanggal_akhir')
                  ->orWhere('jp.tanggal_akhir', '>=', $tanggalHariIni);
            })
            ->orderBy('r.nama_rombel')
            ->orderByRaw('CAST(jp.jam_ke AS UNSIGNED)')
            ->get();

        // Get all piket team and catatan
        $semuaPiketHariIni = PiketKbm::where('hari', $hariIni)->orderBy('created_at')->get();
        $allPiketIds = $semuaPiketHariIni->pluck('id')->toArray();

        $catatanAll = CatatanPiketKbm::where('tanggal', $tanggalHariIni)
            ->whereIn('piket_kbm_id', $allPiketIds)
            ->get()
            ->keyBy(function ($item) {
                return $item->jam_ke . '|' . $item->nama_guru . '|' . $item->nama_rombel;
            });

        // Get izin guru hari ini
        $izinGuruHariIni = [];
        if (Schema::hasTable('izin_guru')) {
            $izinRows = DB::table('izin_guru')
                ->where('tanggal_izin', $tanggalHariIni)
                ->get();
            foreach ($izinRows as $izin) {
                $jamList = explode(',', $izin->jam_ke);
                foreach ($jamList as $jk) {
                    $jk = trim($jk);
                    $key = $izin->guru . '|' . $izin->id_rombel . '|' . $jk;
                    $izinGuruHariIni[$key] = $izin->alasan_izin;
                }
            }
        }

        // Build per-rombel data: rombel => [jam_ke => status]
        $rombelJadwal = []; // rombel => [jam => ['status'=>..., 'guru'=>..., 'mapel'=>...]]
        $kbmKosong = [];

        foreach ($jadwalHariIni as $j) {
            $jamKe = (int)$j->jam_ke;
            $rombel = $j->nama_rombel;
            $key = $jamKe . '|' . $j->nama_guru . '|' . $rombel;
            $izinKey = $j->nama_guru . '|' . $j->id_rombel . '|' . $jamKe;

            if (!isset($rombelJadwal[$rombel])) {
                $rombelJadwal[$rombel] = [];
            }

            $status = 'belum'; // default
            $keterangan = '';

            if (isset($catatanAll[$key])) {
                $catatan = $catatanAll[$key];
                $statusKehadiran = $catatan->status_kehadiran;
                if ($statusKehadiran === 'Hadir Tepat Waktu') {
                    $status = 'tepat_waktu';
                } elseif ($statusKehadiran === 'Hadir Terlambat') {
                    $status = 'terlambat';
                } elseif ($statusKehadiran === 'Izin') {
                    $status = 'izin';
                    $keterangan = 'Izin';
                } elseif ($statusKehadiran === 'Tanpa Keterangan') {
                    $status = 'tanpa_keterangan';
                    $keterangan = 'Tanpa Keterangan';
                }
            } elseif (isset($izinGuruHariIni[$izinKey])) {
                $status = 'izin';
                $keterangan = 'Izin';
            }

            $rombelJadwal[$rombel][$jamKe] = [
                'status' => $status,
                'guru' => $j->nama_guru,
                'mapel' => $j->nama_mapel,
            ];

            // KBM Kosong: izin or tanpa keterangan
            if (in_array($status, ['izin', 'tanpa_keterangan'])) {
                $kbmKosongRaw[] = [
                    'rombel' => $rombel,
                    'jam_ke' => $jamKe,
                    'mapel' => $j->nama_mapel,
                    'guru' => $j->nama_guru,
                    'keterangan' => $keterangan,
                ];
            }
        }

        // Merge consecutive jams in kbmKosong with same rombel+mapel+guru+keterangan
        $kbmKosong = [];
        if (!empty($kbmKosongRaw)) {
            // Sort by rombel then jam
            usort($kbmKosongRaw, function ($a, $b) {
                $cmp = strnatcmp($a['rombel'], $b['rombel']);
                if ($cmp !== 0) return $cmp;
                return $a['jam_ke'] - $b['jam_ke'];
            });

            $current = $kbmKosongRaw[0];
            $current['jam_start'] = $current['jam_ke'];
            $current['jam_end'] = $current['jam_ke'];

            for ($i = 1; $i < count($kbmKosongRaw); $i++) {
                $row = $kbmKosongRaw[$i];
                if (
                    $row['rombel'] === $current['rombel'] &&
                    $row['mapel'] === $current['mapel'] &&
                    $row['guru'] === $current['guru'] &&
                    $row['keterangan'] === $current['keterangan'] &&
                    $row['jam_ke'] === $current['jam_end'] + 1
                ) {
                    // Extend the range
                    $current['jam_end'] = $row['jam_ke'];
                } else {
                    // Push current and start new
                    $current['jam_text'] = $current['jam_start'] == $current['jam_end']
                        ? (string)$current['jam_start']
                        : $current['jam_start'] . '-' . $current['jam_end'];
                    $kbmKosong[] = $current;
                    $current = $row;
                    $current['jam_start'] = $row['jam_ke'];
                    $current['jam_end'] = $row['jam_ke'];
                }
            }
            // Push last
            $current['jam_text'] = $current['jam_start'] == $current['jam_end']
                ? (string)$current['jam_start']
                : $current['jam_start'] . '-' . $current['jam_end'];
            $kbmKosong[] = $current;
        }

        // Natural sort rombel
        uksort($rombelJadwal, 'strnatcmp');

        // Get kepala sekolah from data_periodik
        $kepalaSekolah = $periodik->nama_kepala ?? '';
        $nipKepala = $periodik->nip_kepala ?? '';

        return view('guru.catatan-piket-cetak', compact(
            'guru',
            'hariIni',
            'tanggalHariIni',
            'maxJam',
            'rombelJadwal',
            'kbmKosong',
            'semuaPiketHariIni',
            'kepalaSekolah',
            'nipKepala'
        ));
    }
}
