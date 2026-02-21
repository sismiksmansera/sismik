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
     * Display all school prestasi overview
     */
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $filterTahun = $request->get('tahun', '');
        $filterJenjang = $request->get('jenjang', '');

        // Get all available years
        $tahunList = DB::table('prestasi_siswa')
            ->select('tahun_pelajaran')
            ->distinct()
            ->orderBy('tahun_pelajaran', 'desc')
            ->pluck('tahun_pelajaran');

        // Get statistics by jenjang
        $jenjangStats = DB::table('prestasi_siswa')
            ->select('jenjang', DB::raw('COUNT(DISTINCT nama_kompetisi, juara, jenjang, tanggal_pelaksanaan) as total'))
            ->groupBy('jenjang')
            ->orderByRaw("FIELD(jenjang, 'Internasional','Nasional','Provinsi','Kabupaten','Kecamatan','Sekolah','Kelas') ASC")
            ->get();

        // Query prestasi list
        $query = DB::table('prestasi_siswa as ps')
            ->join('siswa as s', 'ps.siswa_id', '=', 's.id')
            ->select(
                'ps.nama_kompetisi', 'ps.juara', 'ps.jenjang', 'ps.penyelenggara',
                'ps.tanggal_pelaksanaan', 'ps.tahun_pelajaran', 'ps.semester',
                'ps.sumber_prestasi', 'ps.sumber_id',
                DB::raw('MAX(ps.tipe_peserta) as tipe_peserta'),
                DB::raw("GROUP_CONCAT(DISTINCT s.nama ORDER BY s.nama SEPARATOR '||') as siswa_list"),
                DB::raw("GROUP_CONCAT(DISTINCT s.nis ORDER BY s.nama SEPARATOR '||') as nis_list"),
                DB::raw('COUNT(DISTINCT ps.siswa_id) as jumlah_siswa')
            );

        if (!empty($filterTahun)) {
            $query->where('ps.tahun_pelajaran', $filterTahun);
        }

        if (!empty($filterJenjang)) {
            $query->where('ps.jenjang', $filterJenjang);
        }

        $prestasiList = $query
            ->groupBy('ps.nama_kompetisi', 'ps.juara', 'ps.jenjang', 'ps.penyelenggara',
                       'ps.tanggal_pelaksanaan', 'ps.tahun_pelajaran', 'ps.semester',
                       'ps.sumber_prestasi', 'ps.sumber_id')
            ->orderBy('ps.tanggal_pelaksanaan', 'desc')
            ->get();

        // Process results
        foreach ($prestasiList as $prestasi) {
            $prestasi->siswa_array = explode('||', $prestasi->siswa_list ?? '');
            $prestasi->nis_array = explode('||', $prestasi->nis_list ?? '');
        }

        // Group by tahun_pelajaran
        $groupedByTahun = $prestasiList->groupBy('tahun_pelajaran');

        // Total stats
        $totalPrestasi = $prestasiList->count();
        $totalSiswa = DB::table('prestasi_siswa')->distinct()->count('siswa_id');

        return view('admin.prestasi.index', compact(
            'admin', 'groupedByTahun', 'tahunList', 'jenjangStats',
            'filterTahun', 'filterJenjang', 'totalPrestasi', 'totalSiswa'
        ));
    }

    /**
     * Export prestasi data to Excel
     */
    public function exportExcel(Request $request)
    {
        $filterTahun = $request->get('tahun', '');
        $filterJenjang = $request->get('jenjang', '');

        $query = DB::table('prestasi_siswa as ps')
            ->join('siswa as s', 'ps.siswa_id', '=', 's.id')
            ->select(
                'ps.nama_kompetisi', 'ps.juara', 'ps.jenjang', 'ps.penyelenggara',
                'ps.tanggal_pelaksanaan', 'ps.tahun_pelajaran', 'ps.semester',
                'ps.sumber_prestasi', 'ps.tipe_peserta',
                's.nama as nama_siswa', 's.nis'
            );

        if (!empty($filterTahun)) {
            $query->where('ps.tahun_pelajaran', $filterTahun);
        }
        if (!empty($filterJenjang)) {
            $query->where('ps.jenjang', $filterJenjang);
        }

        $data = $query->orderBy('ps.tahun_pelajaran', 'desc')
            ->orderByRaw("FIELD(ps.jenjang, 'Internasional','Nasional','Provinsi','Kabupaten','Kecamatan','Sekolah','Kelas') ASC")
            ->orderBy('ps.tanggal_pelaksanaan', 'desc')
            ->orderBy('ps.nama_kompetisi')
            ->orderBy('s.nama')
            ->get();

        $filename = 'Rekap_Prestasi_Sekolah';
        if ($filterTahun) $filename .= '_' . str_replace('/', '-', $filterTahun);
        if ($filterJenjang) $filename .= '_' . $filterJenjang;
        $filename .= '_' . date('Ymd') . '.xls';

        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">';
        $html .= '<head><meta charset="UTF-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>';
        $html .= '<x:Name>Prestasi</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>';
        $html .= '</x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body>';

        $html .= '<table border="1">';
        $html .= '<tr><td colspan="10" style="font-size:16pt;font-weight:bold;text-align:center;">REKAP PRESTASI SEKOLAH</td></tr>';

        $filterLabel = '';
        if ($filterTahun) $filterLabel .= 'Tahun: ' . $filterTahun . '  ';
        if ($filterJenjang) $filterLabel .= 'Tingkat: ' . $filterJenjang;
        if ($filterLabel) {
            $html .= '<tr><td colspan="10" style="text-align:center;">' . $filterLabel . '</td></tr>';
        }
        $html .= '<tr><td colspan="10"></td></tr>';

        // Header row
        $html .= '<tr style="background-color:#f59e0b;color:white;font-weight:bold;">';
        $html .= '<td style="width:30px;">No</td>';
        $html .= '<td>Tahun Pelajaran</td>';
        $html .= '<td>Semester</td>';
        $html .= '<td>Tingkat</td>';
        $html .= '<td>Nama Kompetisi</td>';
        $html .= '<td>Juara</td>';
        $html .= '<td>Penyelenggara</td>';
        $html .= '<td>Tanggal</td>';
        $html .= '<td>Tipe</td>';
        $html .= '<td>Nama Siswa (NIS)</td>';
        $html .= '</tr>';

        $no = 1;
        foreach ($data as $row) {
            $html .= '<tr>';
            $html .= '<td>' . $no++ . '</td>';
            $html .= '<td>' . $row->tahun_pelajaran . '</td>';
            $html .= '<td>' . $row->semester . '</td>';
            $html .= '<td>' . $row->jenjang . '</td>';
            $html .= '<td>' . htmlspecialchars($row->nama_kompetisi) . '</td>';
            $html .= '<td>' . $row->juara . '</td>';
            $html .= '<td>' . htmlspecialchars($row->penyelenggara ?? '') . '</td>';
            $html .= '<td>' . date('d/m/Y', strtotime($row->tanggal_pelaksanaan)) . '</td>';
            $html .= '<td>' . ($row->tipe_peserta == 'Single' ? 'Individu' : ($row->tipe_peserta ?? '-')) . '</td>';
            $html .= '<td>' . htmlspecialchars($row->nama_siswa) . ' (' . $row->nis . ')</td>';
            $html .= '</tr>';
        }

        $html .= '</table></body></html>';

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'max-age=0');
    }

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

    public function hapus(Request $request)
    {
        $type = $request->type;
        $sourceId = intval($request->source_id);
        $namaKompetisi = $request->nama_kompetisi;
        $juara = $request->juara;
        $jenjang = $request->jenjang;
        $tanggalPelaksanaan = $request->tanggal_pelaksanaan;

        if ($type == 'ekstra') {
            $sumberPrestasi = 'ekstrakurikuler';
        } elseif ($type == 'ajang_talenta') {
            $sumberPrestasi = 'ajang_talenta';
        } else {
            $sumberPrestasi = 'rombel';
        }

        $deleted = DB::table('prestasi_siswa')
            ->where('sumber_prestasi', $sumberPrestasi)
            ->where('sumber_id', $sourceId)
            ->where('nama_kompetisi', $namaKompetisi)
            ->where('juara', $juara)
            ->where('jenjang', $jenjang)
            ->where('tanggal_pelaksanaan', $tanggalPelaksanaan)
            ->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Prestasi berhasil dihapus!']);
        }

        return response()->json(['success' => false, 'message' => 'Gagal menghapus prestasi!']);
    }

    public function update(Request $request)
    {
        $type = $request->type;
        $sourceId = intval($request->source_id);

        $origNamaKompetisi = $request->orig_nama_kompetisi;
        $origJuara = $request->orig_juara;
        $origJenjang = $request->orig_jenjang;
        $origTanggal = $request->orig_tanggal;

        $namaKompetisi = trim($request->nama_kompetisi ?? '');
        $juara = trim($request->juara ?? '');
        $jenjang = $request->jenjang ?? '';
        $penyelenggara = trim($request->penyelenggara ?? '');
        $tanggalPelaksanaan = $request->tanggal_pelaksanaan ?? '';
        $tipePeserta = $request->tipe_peserta ?? 'Single';

        if (empty($namaKompetisi) || empty($juara) || empty($jenjang) || empty($penyelenggara) || empty($tanggalPelaksanaan)) {
            return response()->json(['success' => false, 'message' => 'Semua field wajib diisi']);
        }

        if ($type == 'ekstra') {
            $sumberPrestasi = 'ekstrakurikuler';
        } elseif ($type == 'ajang_talenta') {
            $sumberPrestasi = 'ajang_talenta';
        } else {
            $sumberPrestasi = 'rombel';
        }

        $updated = DB::table('prestasi_siswa')
            ->where('sumber_prestasi', $sumberPrestasi)
            ->where('sumber_id', $sourceId)
            ->where('nama_kompetisi', $origNamaKompetisi)
            ->where('juara', $origJuara)
            ->where('jenjang', $origJenjang)
            ->where('tanggal_pelaksanaan', $origTanggal)
            ->update([
                'nama_kompetisi' => $namaKompetisi,
                'juara' => $juara,
                'jenjang' => $jenjang,
                'penyelenggara' => $penyelenggara,
                'tanggal_pelaksanaan' => $tanggalPelaksanaan,
                'tipe_peserta' => $tipePeserta,
            ]);

        if ($updated !== false) {
            return response()->json(['success' => true, 'message' => 'Prestasi berhasil diperbarui!']);
        }

        return response()->json(['success' => false, 'message' => 'Gagal memperbarui prestasi!']);
    }
}
