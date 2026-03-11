<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;

class PrestasiController extends Controller
{
    /**
     * Display prestasi list for ekstrakurikuler or rombel
     */
    public function index(Request $request)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        $guru_nama = $guruBK->nama;
        $type = $request->query('type', '');
        $id = intval($request->query('id', 0));

        if (empty($type) || $id <= 0) {
            return redirect()->route('guru_bk.tugas-tambahan')
                ->with('error', 'Parameter tidak valid!');
        }

        $sumber_info = null;
        $prestasi_list = [];

        if ($type == 'ekstra') {
            $result = $this->getPrestasiEkstra($id, $guru_nama);
        } elseif ($type == 'rombel') {
            $result = $this->getPrestasiRombel($id, $guru_nama);
        } else {
            return redirect()->route('guru_bk.tugas-tambahan')
                ->with('error', 'Tipe tidak valid!');
        }

        if (isset($result['error'])) {
            return redirect()->route('guru_bk.tugas-tambahan')
                ->with('error', $result['error']);
        }

        $sumber_info = $result['sumber_info'];
        $prestasi_list = $result['prestasi_list'];

        return view('guru-bk.prestasi', compact(
            'type',
            'id',
            'sumber_info',
            'prestasi_list'
        ));
    }

    public function create(Request $request)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }
        $guruNama = $guruBK->nama;

        $type = $request->get('type');
        $sourceId = intval($request->get('id', 0));

        if (empty($type) || $sourceId <= 0) {
            return redirect()->route('guru_bk.tugas-tambahan');
        }

        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? '2024/2025';
        $semesterAktif = $periodik->semester ?? 'Ganjil';

        $sourceNama = '';
        $siswaList = collect();

        if ($type == 'ekstra') {
            $ekstra = DB::table('ekstrakurikuler')
                ->where('id', $sourceId)
                ->where(function($q) use ($guruNama) {
                    $q->where('pembina_1', $guruNama)
                      ->orWhere('pembina_2', $guruNama)
                      ->orWhere('pembina_3', $guruNama);
                })
                ->first();

            if (!$ekstra) return redirect()->route('guru_bk.tugas-tambahan');
            $sourceNama = $ekstra->nama_ekstrakurikuler;

            $siswaList = DB::table('anggota_ekstrakurikuler as ae')
                ->join('siswa as s', 'ae.siswa_id', '=', 's.id')
                ->where('ae.ekstrakurikuler_id', $sourceId)
                ->where('ae.tahun_pelajaran', $tahunPelajaran)
                ->where('ae.semester', $semesterAktif)
                ->select('s.id as siswa_id', 's.nama', 's.nis', 's.nisn')
                ->orderBy('s.nama')
                ->get();

        } elseif ($type == 'rombel') {
            $rombel = DB::table('rombel')
                ->where('id', $sourceId)
                ->where('wali_kelas', $guruNama)
                ->first();

            if (!$rombel) return redirect()->route('guru_bk.tugas-tambahan');
            $sourceNama = $rombel->nama_rombel;

            $tahunAjaran = explode('/', $tahunPelajaran);
            $tahunAwal = intval($tahunAjaran[0]);

            $siswaList = DB::table('siswa')
                ->where(function($q) use ($tahunAwal, $sourceNama, $semesterAktif) {
                    for ($offset = 0; $offset <= 2; $offset++) {
                        $angkatan = $tahunAwal - $offset;
                        $semNum = ($offset * 2) + ($semesterAktif == 'Ganjil' ? 1 : 2);
                        $col = 'rombel_semester_' . $semNum;
                        $q->orWhere(function($sub) use ($angkatan, $col, $sourceNama) {
                            $sub->where('angkatan_masuk', $angkatan)->where($col, $sourceNama);
                        });
                    }
                })
                ->select('id as siswa_id', 'nama', 'nis', 'nisn')
                ->orderBy('nama')
                ->get();
        } else {
            return redirect()->route('guru_bk.tugas-tambahan');
        }

        return view('guru-bk.input-prestasi', compact('guruBK', 'type', 'sourceId', 'sourceNama', 'siswaList'));
    }

    public function store(Request $request)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        if (!$guruBK) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $type = $request->type;
        $sourceId = intval($request->source_id);
        $siswaIds = array_filter(array_map('intval', explode(',', $request->siswa_ids ?? '')));
        $juara = trim($request->juara ?? '');
        $jenjang = $request->jenjang ?? '';
        $namaKompetisi = trim($request->nama_kompetisi ?? '');
        $penyelenggara = trim($request->penyelenggara ?? '');
        $tanggalPelaksanaan = $request->tanggal_pelaksanaan ?? '';
        $tipePeserta = $request->tipe_peserta ?? 'Single';

        if (empty($siswaIds) || empty($juara) || empty($jenjang) || empty($namaKompetisi) || empty($penyelenggara) || empty($tanggalPelaksanaan)) {
            return response()->json(['success' => false, 'message' => 'Semua field wajib diisi']);
        }

        $sumberPrestasi = ($type == 'ekstra') ? 'ekstrakurikuler' : 'rombel';
        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? '2024/2025';
        $semesterAktif = $periodik->semester ?? 'Ganjil';

        DB::beginTransaction();
        try {
            $successCount = 0;
            foreach ($siswaIds as $siswaId) {
                DB::table('prestasi_siswa')->insert([
                    'siswa_id' => $siswaId,
                    'guru_id' => $guruBK->id,
                    'sumber_prestasi' => $sumberPrestasi,
                    'sumber_id' => $sourceId,
                    'juara' => $juara,
                    'jenjang' => $jenjang,
                    'tipe_peserta' => $tipePeserta,
                    'nama_kompetisi' => $namaKompetisi,
                    'penyelenggara' => $penyelenggara,
                    'tanggal_pelaksanaan' => $tanggalPelaksanaan,
                    'tahun_pelajaran' => $tahunPelajaran,
                    'semester' => $semesterAktif,
                ]);
                $successCount++;
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => "Prestasi berhasil disimpan untuk $successCount siswa!"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Get prestasi for ekstrakurikuler
     */
    private function getPrestasiEkstra($id, $guru_nama)
    {
        // Get ekstrakurikuler data
        $ekstra = DB::table('ekstrakurikuler')
            ->where('id', $id)
            ->first();

        if (!$ekstra) {
            return ['error' => 'Data ekstrakurikuler tidak ditemukan!'];
        }

        $sumber_info = (object) [
            'id' => $ekstra->id,
            'title' => $ekstra->nama_ekstrakurikuler,
            'tahun_pelajaran' => $ekstra->tahun_pelajaran,
            'semester' => $ekstra->semester,
            'icon' => 'fa-trophy',
            'color' => '#f59e0b'
        ];

        // Query prestasi
        $prestasi_list = DB::table('prestasi_siswa as ps')
            ->join('siswa as s', 'ps.siswa_id', '=', 's.id')
            ->select(
                'ps.nama_kompetisi',
                'ps.juara',
                'ps.jenjang',
                'ps.tanggal_pelaksanaan',
                DB::raw("GROUP_CONCAT(DISTINCT s.nama SEPARATOR ', ') as peserta")
            )
            ->where('ps.sumber_prestasi', 'ekstrakurikuler')
            ->where('ps.sumber_id', $id)
            ->where('ps.tahun_pelajaran', $ekstra->tahun_pelajaran)
            ->where('ps.semester', $ekstra->semester)
            ->groupBy('ps.nama_kompetisi', 'ps.juara', 'ps.jenjang', 'ps.tanggal_pelaksanaan')
            ->orderBy('ps.tanggal_pelaksanaan', 'DESC')
            ->get();

        return [
            'sumber_info' => $sumber_info,
            'prestasi_list' => $prestasi_list
        ];
    }

    /**
     * Get prestasi for rombel
     */
    private function getPrestasiRombel($id, $guru_nama)
    {
        // Get rombel data
        $rombel = DB::table('rombel')
            ->where('id', $id)
            ->first();

        if (!$rombel) {
            return ['error' => 'Data rombel tidak ditemukan!'];
        }

        // Verify access
        if ($rombel->wali_kelas != $guru_nama) {
            return ['error' => 'Anda tidak memiliki akses ke kelas ini!'];
        }

        $sumber_info = (object) [
            'id' => $rombel->id,
            'title' => $rombel->nama_rombel,
            'tahun_pelajaran' => $rombel->tahun_pelajaran,
            'semester' => $rombel->semester,
            'icon' => 'fa-trophy',
            'color' => '#f59e0b'
        ];

        // Query prestasi
        $prestasi_list = DB::table('prestasi_siswa as ps')
            ->join('siswa as s', 'ps.siswa_id', '=', 's.id')
            ->select(
                'ps.nama_kompetisi',
                'ps.juara',
                'ps.jenjang',
                'ps.tanggal_pelaksanaan',
                DB::raw("GROUP_CONCAT(DISTINCT s.nama SEPARATOR ', ') as peserta")
            )
            ->where('ps.sumber_prestasi', 'rombel')
            ->where('ps.sumber_id', $id)
            ->where('ps.tahun_pelajaran', $rombel->tahun_pelajaran)
            ->where('ps.semester', $rombel->semester)
            ->groupBy('ps.nama_kompetisi', 'ps.juara', 'ps.jenjang', 'ps.tanggal_pelaksanaan')
            ->orderBy('ps.tanggal_pelaksanaan', 'DESC')
            ->get();

        return [
            'sumber_info' => $sumber_info,
            'prestasi_list' => $prestasi_list
        ];
    }

    /**
     * Get medal color based on juara ranking
     */
    public static function getMedalColor($juara)
    {
        $juara_lower = strtolower($juara);
        
        if (strpos($juara_lower, '1') !== false || strpos($juara_lower, 'pertama') !== false) {
            return '#f59e0b'; // Gold
        } elseif (strpos($juara_lower, '2') !== false || strpos($juara_lower, 'kedua') !== false) {
            return '#9ca3af'; // Silver
        } elseif (strpos($juara_lower, '3') !== false || strpos($juara_lower, 'ketiga') !== false) {
            return '#cd7f32'; // Bronze
        }
        
        return '#6b7280'; // Default gray
    }
}
