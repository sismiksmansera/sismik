<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengaduan;
use App\Models\DataPeriodik;
use App\Models\GuruBK;
use App\Models\Rombel;
use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PengaduanController extends Controller
{
    /**
     * Display listing of pengaduan
     */
    public function index(Request $request)
    {
        $periodik = DataPeriodik::aktif()->first();
        $tahunAktif = $periodik->tahun_pelajaran ?? '';
        $semesterAktif = $periodik->semester ?? '';

        // Get filter parameters
        $statusFilter = $request->get('status', '');
        $kategoriFilter = $request->get('kategori', '');
        $search = $request->get('search', '');

        // Build query
        $query = Pengaduan::query()
            ->where('tahun_pelajaran', $tahunAktif)
            ->where('semester', $semesterAktif);

        if (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }
        if (!empty($kategoriFilter)) {
            $query->where('kategori', $kategoriFilter);
        }
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_pelapor', 'like', "%$search%")
                  ->orWhere('subyek_terlapor', 'like', "%$search%")
                  ->orWhere('deskripsi', 'like', "%$search%");
            });
        }

        $pengaduanList = $query->orderBy('created_at', 'desc')->get();

        // Enrich with student data
        foreach ($pengaduanList as $item) {
            $siswa = Siswa::where('nisn', $item->nisn)->first();
            $waliKelas = '';
            $rombelAktif = $item->rombel_pelapor;
            $guruBkSiswa = '';
            
            if ($siswa) {
                $semesterAktifSiswa = $this->calculateActiveSemester(
                    $siswa->angkatan_masuk,
                    $tahunAktif,
                    $semesterAktif
                );
                $rombelCol = 'rombel_semester_' . $semesterAktifSiswa;
                $bkCol = 'bk_semester_' . $semesterAktifSiswa;
                
                $item->semester_aktif_siswa = $semesterAktifSiswa;
                $rombelAktif = !empty($siswa->$rombelCol) ? $siswa->$rombelCol : $item->rombel_pelapor;
                $guruBkSiswa = $siswa->$bkCol ?? '';
            }
            
            // Get wali kelas - try with rombel_aktif first, then rombel_pelapor
            $rombelToCheck = [$rombelAktif];
            if ($rombelAktif !== $item->rombel_pelapor && !empty($item->rombel_pelapor)) {
                $rombelToCheck[] = $item->rombel_pelapor;
            }
            
            foreach ($rombelToCheck as $namaRombel) {
                if (empty($waliKelas)) {
                    $rombel = Rombel::where('nama_rombel', $namaRombel)
                        ->where('tahun_pelajaran', $tahunAktif)
                        ->where('semester', $semesterAktif)
                        ->first();
                    if ($rombel && !empty($rombel->wali_kelas)) {
                        $waliKelas = $rombel->wali_kelas;
                        break;
                    }
                }
            }
            
            $item->rombel_aktif = $rombelAktif;
            $item->guru_bk_siswa = $guruBkSiswa;
            $item->wali_kelas = $waliKelas;
            
            // Check if new (within 24 hours)
            $item->is_new = $item->created_at && $item->created_at->diffInHours(now()) < 24;
        }

        // Get statistics
        $statusStats = [
            'Menunggu' => 0,
            'Diproses' => 0,
            'Ditangani' => 0,
            'Ditutup' => 0
        ];
        
        $statsQuery = Pengaduan::where('tahun_pelajaran', $tahunAktif)
            ->where('semester', $semesterAktif)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();
        
        foreach ($statsQuery as $stat) {
            if (isset($statusStats[$stat->status])) {
                $statusStats[$stat->status] = $stat->total;
            }
        }
        
        $totalPengaduan = array_sum($statusStats);

        // Get Waka data
        $wakaData = [
            ['role' => 'Waka Kurikulum', 'nama' => $periodik->waka_kurikulum ?? ''],
            ['role' => 'Waka Kesiswaan', 'nama' => $periodik->waka_kesiswaan ?? ''],
            ['role' => 'Waka Sarpras', 'nama' => $periodik->waka_sarpras ?? ''],
            ['role' => 'Waka Humas', 'nama' => $periodik->waka_humas ?? '']
        ];

        // Get Guru BK list
        $guruBkList = GuruBK::where('status', 'Aktif')
            ->orderBy('nama', 'asc')
            ->get(['id', 'nama', 'nip']);

        return view('admin.pengaduan.index', compact(
            'periodik',
            'tahunAktif',
            'semesterAktif',
            'pengaduanList',
            'statusFilter',
            'kategoriFilter',
            'search',
            'statusStats',
            'totalPengaduan',
            'wakaData',
            'guruBkList'
        ));
    }

    /**
     * Get detail pengaduan (AJAX)
     */
    public function getDetail(Request $request)
    {
        $id = $request->input('id');
        $pengaduan = Pengaduan::find($id);
        
        if (!$pengaduan) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        // Get student info
        $siswa = Siswa::where('nisn', $pengaduan->nisn)->first();
        
        return response()->json([
            'success' => true,
            'data' => $pengaduan,
            'siswa' => $siswa
        ]);
    }

    /**
     * Update pengaduan status and tanggapan (AJAX)
     */
    public function update(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');
        $tanggapan = $request->input('tanggapan', '');
        
        $pengaduan = Pengaduan::find($id);
        
        if (!$pengaduan) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        $adminId = Auth::guard('admin')->id();
        
        $pengaduan->update([
            'status' => $status,
            'tanggapan' => $tanggapan,
            'ditangani_oleh' => $adminId,
            'ditanggapi_oleh' => 'Admin Sekolah'
        ]);

        return response()->json(['success' => true, 'message' => 'Pengaduan berhasil diperbarui']);
    }

    /**
     * Forward pengaduan to someone (AJAX)
     */
    public function teruskan(Request $request)
    {
        $id = $request->input('id');
        $tujuan = $request->input('tujuan');
        
        $pengaduan = Pengaduan::find($id);
        
        if (!$pengaduan) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        $pengaduan->update([
            'diteruskan_ke' => $tujuan,
            'status' => 'Diproses'
        ]);

        // Create notification if notifikasi table exists
        if (\Schema::hasTable('notifikasi')) {
            $tujuanNip = $request->input('tujuan_nip', '');
            $judul = "Pengaduan Baru Diteruskan kepada Anda";
            $isi = "Pengaduan kategori '{$pengaduan->kategori}' dari {$pengaduan->nama_pelapor} ({$pengaduan->rombel_pelapor}) telah diteruskan kepada Anda.";
            
            DB::table('notifikasi')->insert([
                'penerima_nip' => $tujuanNip,
                'penerima_nama' => $tujuan,
                'judul' => $judul,
                'isi' => $isi,
                'kategori' => 'pengaduan',
                'referensi_id' => $id,
                'created_at' => now()
            ]);
        }

        return response()->json(['success' => true, 'message' => "Pengaduan berhasil diteruskan kepada $tujuan"]);
    }

    /**
     * Delete pengaduan (AJAX)
     */
    public function destroy(Request $request)
    {
        $id = $request->input('id');
        
        $pengaduan = Pengaduan::find($id);
        
        if (!$pengaduan) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        // Delete bukti file if exists
        if ($pengaduan->bukti_pendukung) {
            $filePath = 'pengaduan/' . $pengaduan->bukti_pendukung;
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
        }

        $pengaduan->delete();

        return response()->json(['success' => true, 'message' => 'Pengaduan berhasil dihapus']);
    }

    /**
     * Calculate active semester for student
     */
    private function calculateActiveSemester($angkatan, $tahunPelajaran, $semester)
    {
        if (empty($angkatan) || empty($tahunPelajaran) || empty($semester)) {
            return 1;
        }
        
        $tahunParts = explode('/', $tahunPelajaran);
        $tahunMulai = intval($tahunParts[0] ?? 0);
        $angkatanInt = intval($angkatan);
        $selisihTahun = $tahunMulai - $angkatanInt;
        
        if ($selisihTahun == 0) {
            return ($semester == 'Ganjil') ? 1 : 2;
        } elseif ($selisihTahun == 1) {
            return ($semester == 'Ganjil') ? 3 : 4;
        } elseif ($selisihTahun == 2) {
            return ($semester == 'Ganjil') ? 5 : 6;
        }
        
        return 1;
    }
}
