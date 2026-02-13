<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PanggilanOrtuController extends Controller
{
    /**
     * Display all panggilan ortu list (all students)
     */
    public function listAll()
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        $panggilan_list = DB::table('panggilan_ortu as p')
            ->leftJoin('siswa as s', DB::raw('p.nisn COLLATE utf8mb4_general_ci'), '=', 's.nisn')
            ->leftJoin('guru_bk as g', 'p.guru_bk_id', '=', 'g.id')
            ->select(
                'p.*',
                's.nama as nama_siswa',
                's.jk as jk_siswa',
                's.nama_rombel',
                'g.nama as nama_guru_bk'
            )
            ->where('p.guru_bk_id', $guruBK->id)
            ->orderBy('p.tanggal_surat', 'desc')
            ->orderBy('p.created_at', 'desc')
            ->get();

        $stats = [
            'total' => $panggilan_list->count(),
            'Menunggu' => $panggilan_list->where('status', 'Menunggu')->count(),
            'Hadir' => $panggilan_list->where('status', 'Hadir')->count(),
            'Tidak Hadir' => $panggilan_list->where('status', 'Tidak Hadir')->count(),
            'Dijadwalkan Ulang' => $panggilan_list->where('status', 'Dijadwalkan Ulang')->count(),
        ];

        return view('guru-bk.panggilan-ortu.list-all', compact('guruBK', 'panggilan_list', 'stats'));
    }

    /**
     * Display panggilan ortu list for a student
     */
    public function index($nisn)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        // Get student data
        $siswa = DB::table('siswa')
            ->where('nisn', $nisn)
            ->first();

        if (!$siswa) {
            return redirect()->route('guru_bk.siswa-bimbingan')
                ->with('error', 'Data siswa tidak ditemukan.');
        }

        // Get panggilan ortu list (only created by this guru BK)
        $panggilan_list = DB::table('panggilan_ortu as p')
            ->leftJoin('guru_bk as g', 'p.guru_bk_id', '=', 'g.id')
            ->select('p.*', 'g.nama as nama_guru_bk')
            ->where('p.nisn', $nisn)
            ->where('p.guru_bk_id', $guruBK->id)
            ->orderBy('p.tanggal_surat', 'desc')
            ->get();

        $total_panggilan = $panggilan_list->count();

        // Calculate statistics
        $stats = [
            'Menunggu' => 0,
            'Hadir' => 0,
            'Tidak Hadir' => 0,
            'Dijadwalkan Ulang' => 0
        ];

        foreach ($panggilan_list as $p) {
            if (isset($stats[$p->status])) {
                $stats[$p->status]++;
            }
        }

        return view('guru-bk.panggilan-ortu.index', compact(
            'siswa',
            'nisn',
            'panggilan_list',
            'total_panggilan',
            'stats'
        ));
    }

    /**
     * Show form to create new panggilan ortu
     */
    public function create(Request $request, $nisn)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        // Get student data
        $siswa = DB::table('siswa')
            ->where('nisn', $nisn)
            ->first();

        if (!$siswa) {
            return redirect()->route('guru_bk.siswa-bimbingan')
                ->with('error', 'Data siswa tidak ditemukan.');
        }

        // Get sekolah data for surat (table may not exist)
        $sekolah = null;

        return view('guru-bk.panggilan-ortu.create', compact(
            'siswa',
            'nisn',
            'sekolah',
            'guruBK'
        ));
    }

    /**
     * Store new panggilan ortu
     */
    public function store(Request $request, $nisn)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        // Validate request
        $request->validate([
            'tanggal_surat' => 'required|date',
            'perihal' => 'required|string|max:255',
            'tanggal_panggilan' => 'required|date',
        ]);

        try {
            DB::table('panggilan_ortu')->insert([
                'nisn' => $nisn,
                'guru_bk_id' => $guruBK->id,
                'tanggal_surat' => $request->tanggal_surat,
                'no_surat' => $request->no_surat ?? '',
                'perihal' => $request->perihal,
                'alasan' => $request->alasan ?? '',
                'menghadap_ke' => $request->menghadap_ke ?? 'Guru BK',
                'tanggal_panggilan' => $request->tanggal_panggilan,
                'jam_panggilan' => $request->jam_panggilan ?? null,
                'tempat' => $request->tempat ?? 'Ruang BK',
                'status' => $request->status ?? 'Menunggu',
                'catatan' => $request->catatan ?? '',
                'created_at' => now(),
            ]);

            return redirect()->route('guru_bk.panggilan-ortu', ['nisn' => $nisn])
                ->with('success', 'Surat panggilan orang tua berhasil dibuat.');

        } catch (\Exception $e) {
            Log::error("Error storing panggilan ortu: " . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show form to edit panggilan ortu
     */
    public function edit($id)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        // Get panggilan data
        $panggilan = DB::table('panggilan_ortu')
            ->where('id', $id)
            ->first();

        if (!$panggilan) {
            return redirect()->route('guru_bk.siswa-bimbingan')
                ->with('error', 'Data panggilan tidak ditemukan.');
        }

        // Get student data
        $siswa = DB::table('siswa')
            ->where('nisn', $panggilan->nisn)
            ->first();

        // Get sekolah data for surat (table may not exist)
        $sekolah = null;

        return view('guru-bk.panggilan-ortu.edit', compact(
            'panggilan',
            'siswa',
            'sekolah',
            'guruBK'
        ));
    }

    /**
     * Update panggilan ortu
     */
    public function update(Request $request, $id)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        // Validate request
        $request->validate([
            'tanggal_surat' => 'required|date',
            'perihal' => 'required|string|max:255',
            'tanggal_panggilan' => 'required|date',
            'foto_dokumentasi' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:500',
        ]);

        try {
            // Get panggilan for nisn
            $panggilan = DB::table('panggilan_ortu')->where('id', $id)->first();
            
            if (!$panggilan) {
                return back()->with('error', 'Data panggilan tidak ditemukan.');
            }

            $updateData = [
                'tanggal_surat' => $request->tanggal_surat,
                'no_surat' => $request->no_surat ?? '',
                'perihal' => $request->perihal,
                'alasan' => $request->alasan ?? '',
                'menghadap_ke' => $request->menghadap_ke ?? 'Guru BK',
                'tanggal_panggilan' => $request->tanggal_panggilan,
                'jam_panggilan' => $request->jam_panggilan ?? null,
                'tempat' => $request->tempat ?? 'Ruang BK',
                'status' => $request->status ?? 'Menunggu',
                'catatan' => $request->catatan ?? '',
                'updated_at' => now(),
            ];

            // Handle photo upload
            if ($request->hasFile('foto_dokumentasi')) {
                $file = $request->file('foto_dokumentasi');
                $filename = 'panggilan_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
                
                // Delete old photo if exists
                if ($panggilan->foto_dokumentasi) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete('panggilan_ortu/' . $panggilan->foto_dokumentasi);
                }
                
                // Store new photo
                $file->storeAs('panggilan_ortu', $filename, 'public');
                $updateData['foto_dokumentasi'] = $filename;
            }

            // Handle photo deletion
            if ($request->has('hapus_foto') && $request->hapus_foto == '1') {
                if ($panggilan->foto_dokumentasi) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete('panggilan_ortu/' . $panggilan->foto_dokumentasi);
                }
                $updateData['foto_dokumentasi'] = null;
            }

            DB::table('panggilan_ortu')
                ->where('id', $id)
                ->update($updateData);

            return redirect()->route('guru_bk.panggilan-ortu', ['nisn' => $panggilan->nisn])
                ->with('success', 'Surat panggilan orang tua berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error("Error updating panggilan ortu: " . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }


    /**
     * Delete panggilan ortu
     */
    public function delete(Request $request)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        $id = $request->input('id');
        $nisn = $request->input('nisn');

        try {
            // Verify ownership
            $panggilan = DB::table('panggilan_ortu')
                ->where('id', $id)
                ->where('guru_bk_id', $guruBK->id)
                ->first();

            if (!$panggilan) {
                return back()->with('error', 'Data tidak ditemukan atau Anda tidak memiliki akses.');
            }

            DB::table('panggilan_ortu')->where('id', $id)->delete();

            return redirect()->route('guru_bk.panggilan-ortu', ['nisn' => $nisn])
                ->with('success', 'Data panggilan orang tua berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error("Error deleting panggilan ortu: " . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Print panggilan ortu letter
     */
    public function print($id)
    {
        $guruBK = Auth::guard('guru_bk')->user();
        
        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        // Get panggilan data
        $panggilan = DB::table('panggilan_ortu')
            ->where('id', $id)
            ->first();

        if (!$panggilan) {
            return back()->with('error', 'Data panggilan tidak ditemukan.');
        }

        // Get student data
        $siswa = DB::table('siswa')
            ->where('nisn', $panggilan->nisn)
            ->first();

        // Get sekolah data for surat (table may not exist)
        $sekolah = null;

        // Get guru BK data
        $guruBKData = DB::table('guru_bk')
            ->where('id', $panggilan->guru_bk_id)
            ->first();

        // Get periode aktif (kepala sekolah data)
        $periodeAktif = DB::table('data_periodik')
            ->where('aktif', 'Ya')
            ->first();

        // Calculate active semester for proper rombel
        $rombelAktif = $this->getRombelAktif($siswa, $periodeAktif);

        return view('guru-bk.panggilan-ortu.print', compact(
            'panggilan',
            'siswa',
            'sekolah',
            'guruBKData',
            'periodeAktif',
            'rombelAktif'
        ));
    }

    /**
     * Calculate active semester and get proper rombel
     */
    private function getRombelAktif($siswa, $periodeAktif)
    {
        if (!$siswa || !$periodeAktif) {
            return $siswa->nama_rombel ?? '-';
        }

        $angkatan = $siswa->angkatan_masuk ?? null;
        $tahunPelajaran = $periodeAktif->tahun_pelajaran ?? null;
        $semester = $periodeAktif->semester ?? null;

        if (empty($angkatan) || empty($tahunPelajaran) || empty($semester)) {
            return $siswa->nama_rombel ?? '-';
        }

        // Parse tahun pelajaran (e.g., "2024/2025")
        $tahunParts = explode('/', $tahunPelajaran);
        $tahunMulai = intval($tahunParts[0]);
        $angkatanInt = intval($angkatan);
        $selisihTahun = $tahunMulai - $angkatanInt;

        // Calculate semester aktif
        if ($selisihTahun == 0) {
            $semesterAktif = ($semester == 'Ganjil') ? 1 : 2;
        } elseif ($selisihTahun == 1) {
            $semesterAktif = ($semester == 'Ganjil') ? 3 : 4;
        } elseif ($selisihTahun == 2) {
            $semesterAktif = ($semester == 'Ganjil') ? 5 : 6;
        } else {
            $semesterAktif = 1;
        }

        // Get rombel for active semester
        $kolomRombel = 'rombel_semester_' . $semesterAktif;
        
        return $siswa->$kolomRombel ?? $siswa->nama_rombel ?? '-';
    }
}
