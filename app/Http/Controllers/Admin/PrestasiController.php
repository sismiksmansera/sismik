<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Rombel;
use App\Models\Ekstrakurikuler;
use App\Models\PrestasiSiswa;
use App\Models\DataPeriodik;

class PrestasiController extends Controller
{
    /**
     * Display prestasi list for a rombel or ekstrakurikuler
     */
    public function lihat(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $type = $request->get('type', '');
        $id = intval($request->get('id', 0));
        
        if (empty($type) || $id <= 0) {
            return redirect()->route('admin.rombel.index')
                ->with('error', 'Parameter tidak valid!');
        }
        
        $sumberInfo = [];
        $prestasiList = [];
        $backUrl = '';
        
        if ($type == 'ekstra') {
            // Get ekstrakurikuler data
            $ekstra = Ekstrakurikuler::find($id);
            if (!$ekstra) {
                return redirect()->route('admin.ekstrakurikuler.index')
                    ->with('error', 'Data tidak ditemukan!');
            }
            
            $sumberInfo = [
                'title' => $ekstra->nama_ekstrakurikuler,
                'tahun_pelajaran' => $ekstra->tahun_pelajaran,
                'semester' => $ekstra->semester,
                'icon' => 'fa-trophy',
                'color' => '#f59e0b',
            ];
            $backUrl = route('admin.ekstrakurikuler.index');
            
            // Query prestasi grouped by competition
            $prestasiList = $this->getPrestasiList('ekstrakurikuler', $id, $ekstra->tahun_pelajaran, $ekstra->semester);
            
        } elseif ($type == 'rombel') {
            // Get rombel data
            $rombel = Rombel::find($id);
            if (!$rombel) {
                return redirect()->route('admin.rombel.index')
                    ->with('error', 'Data tidak ditemukan!');
            }
            
            $sumberInfo = [
                'title' => $rombel->nama_rombel,
                'tahun_pelajaran' => $rombel->tahun_pelajaran,
                'semester' => $rombel->semester,
                'icon' => 'fa-trophy',
                'color' => '#f59e0b',
            ];
            $backUrl = route('admin.rombel.index');
            
            // Query prestasi grouped by competition
            $prestasiList = $this->getPrestasiList('rombel', $id, $rombel->tahun_pelajaran, $rombel->semester);
        } elseif ($type == 'ajang_talenta') {
            // Get ajang talenta data
            $ajang = DB::table('ajang_talenta')->where('id', $id)->first();
            if (!$ajang) {
                return redirect()->route('admin.manajemen-talenta.index')
                    ->with('error', 'Data tidak ditemukan!');
            }

            $sumberInfo = [
                'title' => $ajang->nama_ajang,
                'tahun_pelajaran' => $ajang->tahun ?? '-',
                'semester' => '-',
                'icon' => 'fa-trophy',
                'color' => '#7c3aed',
            ];
            $backUrl = route('admin.manajemen-talenta.index');
            $defaultKompetisi = $ajang->nama_ajang . ' ' . ($ajang->tahun ?? '');
            $defaultPenyelenggara = $ajang->penyelenggara ?? '';

            // Query prestasi grouped by competition
            $prestasiList = $this->getPrestasiList('ajang_talenta', $id, $ajang->tahun ?? '', '');
        }

        $defaultKompetisi = $defaultKompetisi ?? '';
        $defaultPenyelenggara = $defaultPenyelenggara ?? '';
        
        return view('admin.prestasi.lihat', compact(
            'admin', 'type', 'sumberInfo', 'prestasiList', 'backUrl', 'id',
            'defaultKompetisi', 'defaultPenyelenggara'
        ));
    }

    public function create(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $type = $request->get('type', '');
        $id = intval($request->get('id', 0));

        if (empty($type) || $id <= 0) {
            return redirect()->route('admin.rombel.index');
        }

        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? '2024/2025';
        $semesterAktif = $periodik->semester ?? 'Ganjil';

        $sourceNama = '';
        $siswaList = collect();
        $backUrl = '';

        if ($type == 'ekstra') {
            $ekstra = Ekstrakurikuler::find($id);
            if (!$ekstra) return redirect()->route('admin.ekstrakurikuler.index');
            $sourceNama = $ekstra->nama_ekstrakurikuler;
            $backUrl = route('admin.prestasi.lihat', ['type' => 'ekstra', 'id' => $id]);

            $siswaList = DB::table('anggota_ekstrakurikuler as ae')
                ->join('siswa as s', 'ae.siswa_id', '=', 's.id')
                ->where('ae.ekstrakurikuler_id', $id)
                ->where('ae.tahun_pelajaran', $tahunPelajaran)
                ->where('ae.semester', $semesterAktif)
                ->select('s.id as siswa_id', 's.nama', 's.nis', 's.nisn')
                ->orderBy('s.nama')
                ->get();

        } elseif ($type == 'rombel') {
            $rombel = Rombel::find($id);
            if (!$rombel) return redirect()->route('admin.rombel.index');
            $sourceNama = $rombel->nama_rombel;
            $backUrl = route('admin.prestasi.lihat', ['type' => 'rombel', 'id' => $id]);

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
        } elseif ($type == 'ajang_talenta') {
        $ajang = DB::table('ajang_talenta')->where('id', $id)->first();
        if (!$ajang) return redirect()->route('admin.manajemen-talenta.index');
        $sourceNama = $ajang->nama_ajang;
        $backUrl = route('admin.prestasi.lihat', ['type' => 'ajang_talenta', 'id' => $id]);

        $siswaList = DB::table('peserta_ajang_talenta as pat')
            ->join('siswa as s', 'pat.siswa_id', '=', 's.id')
            ->where('pat.ajang_talenta_id', $id)
            ->select('s.id as siswa_id', 's.nama', 's.nis', 's.nisn')
            ->orderBy('s.nama')
            ->get();

        $defaultKompetisi = $ajang->nama_ajang . ' ' . ($ajang->tahun ?? '');
        $defaultPenyelenggara = $ajang->penyelenggara ?? '';
    } else {
        return redirect()->route('admin.rombel.index');
    }

    $defaultKompetisi = $defaultKompetisi ?? '';
    $defaultPenyelenggara = $defaultPenyelenggara ?? '';

    return view('admin.prestasi.input', compact('admin', 'type', 'id', 'sourceNama', 'siswaList', 'backUrl', 'defaultKompetisi', 'defaultPenyelenggara'));
}
    public function store(Request $request)
    {
        $admin = Auth::guard('admin')->user();

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

        if ($type == 'ekstra') {
        $sumberPrestasi = 'ekstrakurikuler';
    } elseif ($type == 'ajang_talenta') {
        $sumberPrestasi = 'ajang_talenta';
    } else {
        $sumberPrestasi = 'rombel';
    }
        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? '2024/2025';
        $semesterAktif = $periodik->semester ?? 'Ganjil';

        DB::beginTransaction();
        try {
            $successCount = 0;
            foreach ($siswaIds as $siswaId) {
                DB::table('prestasi_siswa')->insert([
                    'siswa_id' => $siswaId,
                    'guru_id' => $admin->id,
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
     * Get prestasi list grouped by competition
     */
    private function getPrestasiList($sumberPrestasi, $sumberId, $tahunPelajaran, $semester)
    {
        $query = DB::table('prestasi_siswa as ps')
            ->join('siswa as s', 'ps.siswa_id', '=', 's.id')
            ->select(
                'ps.nama_kompetisi',
                'ps.juara',
                'ps.jenjang',
                'ps.tanggal_pelaksanaan',
                'ps.penyelenggara',
                DB::raw('MAX(ps.tipe_peserta) as tipe_peserta'),
                DB::raw("GROUP_CONCAT(DISTINCT s.nama ORDER BY s.nama SEPARATOR '||') as siswa_list"),
                DB::raw("GROUP_CONCAT(DISTINCT s.nis ORDER BY s.nama SEPARATOR '||') as nis_list"),
                DB::raw('COUNT(*) as jumlah_siswa')
            )
            ->where('ps.sumber_prestasi', $sumberPrestasi)
            ->where('ps.sumber_id', $sumberId);

        // For ajang_talenta, don't filter by tahun_pelajaran/semester
        // since data is already scoped by sumber_id
        if ($sumberPrestasi != 'ajang_talenta') {
            $query->where('ps.tahun_pelajaran', $tahunPelajaran)
                  ->where('ps.semester', $semester);
        }

        $results = $query
            ->groupBy('ps.nama_kompetisi', 'ps.juara', 'ps.jenjang', 'ps.tanggal_pelaksanaan', 'ps.penyelenggara')
            ->orderBy('ps.tanggal_pelaksanaan', 'desc')
            ->get();
        
        // Process results to split siswa_list and nis_list
        $prestasiList = [];
        foreach ($results as $row) {
            $item = (array) $row;
            $item['siswa_array'] = explode('||', $row->siswa_list ?? '');
            $item['nis_array'] = explode('||', $row->nis_list ?? '');
            $prestasiList[] = $item;
        }
        
        return $prestasiList;
    }
    
    /**
     * Get jenjang color
     */
    public static function getJenjangColor($jenjang)
    {
        $colors = [
            'Kelas' => '#6b7280',
            'Sekolah' => '#3b82f6',
            'Kecamatan' => '#10b981',
            'Kabupaten' => '#8b5cf6',
            'Provinsi' => '#f59e0b',
            'Nasional' => '#ef4444',
            'Internasional' => '#ec4899',
            'Lainnya' => '#6b7280'
        ];
        return $colors[$jenjang] ?? '#6b7280';
    }
}
