<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PanggilanOrtu;
use App\Models\Siswa;
use App\Models\GuruBK;

class PanggilanOrtuController extends Controller
{
    /**
     * Display panggilan ortu for a specific siswa
     */
    public function index(Request $request)
    {
        $nisn = $request->get('nisn', '');
        $guruBkId = $request->get('guru_bk_id', 0);
        
        if (empty($nisn)) {
            return redirect()->route('admin.guru-bk.index')
                ->with('error', 'Parameter tidak valid!');
        }
        
        // Get siswa data
        $siswa = Siswa::where('nisn', $nisn)->first();
        
        if (!$siswa) {
            return redirect()->route('admin.guru-bk.index')
                ->with('error', 'Data siswa tidak ditemukan!');
        }
        
        // Get guru BK data if provided
        $guruBK = null;
        if ($guruBkId > 0) {
            $guruBK = GuruBK::find($guruBkId);
        }
        
        // Get panggilan list
        $panggilanList = PanggilanOrtu::where('nisn', $nisn)
            ->leftJoin('guru_bk', 'panggilan_ortu.guru_bk_id', '=', 'guru_bk.id')
            ->select('panggilan_ortu.*', 'guru_bk.nama as nama_guru_bk')
            ->orderBy('tanggal_surat', 'desc')
            ->get();
        
        // Calculate stats
        $totalPanggilan = $panggilanList->count();
        $stats = [
            'Menunggu' => 0,
            'Hadir' => 0,
            'Tidak Hadir' => 0,
            'Dijadwalkan Ulang' => 0,
        ];
        
        foreach ($panggilanList as $p) {
            if (isset($stats[$p->status])) {
                $stats[$p->status]++;
            }
        }
        
        // Check foto siswa
        $hasFoto = !empty($siswa->foto) && file_exists(public_path('storage/siswa/' . $siswa->foto));
        
        return view('admin.panggilan-ortu.index', compact(
            'siswa', 'guruBK', 'guruBkId', 'panggilanList', 'totalPanggilan', 'stats', 'hasFoto'
        ));
    }
    
    /**
     * Delete a panggilan
     */
    public function destroy(Request $request, $id)
    {
        $nisn = $request->get('nisn', '');
        $guruBkId = $request->get('guru_bk_id', 0);
        
        $panggilan = PanggilanOrtu::find($id);
        
        if ($panggilan) {
            $panggilan->delete();
        }
        
        return redirect()->route('admin.panggilan-ortu.index', [
            'nisn' => $nisn,
            'guru_bk_id' => $guruBkId
        ])->with('success', 'Data panggilan berhasil dihapus!');
    }
    
    /**
     * Show form for creating new panggilan
     */
    public function create(Request $request)
    {
        $nisn = $request->get('nisn', '');
        $guruBkId = $request->get('guru_bk_id', 0);
        
        if (empty($nisn)) {
            return redirect()->route('admin.guru-bk.index')
                ->with('error', 'Parameter tidak valid!');
        }
        
        $siswa = Siswa::where('nisn', $nisn)->first();
        
        if (!$siswa) {
            return redirect()->route('admin.guru-bk.index')
                ->with('error', 'Data siswa tidak ditemukan!');
        }
        
        // Get guru BK list for dropdown
        $guruBKList = GuruBK::where('status', 'Aktif')->orderBy('nama', 'asc')->get();
        
        // Check foto siswa
        $hasFoto = !empty($siswa->foto) && file_exists(public_path('storage/siswa/' . $siswa->foto));
        
        // Default values
        $defaultNoSurat = 'SPO/' . date('Ymd') . '/' . rand(100, 999);
        $defaultTanggal = date('Y-m-d');
        $defaultJam = '09:00';
        
        return view('admin.panggilan-ortu.form', compact(
            'siswa', 'guruBkId', 'guruBKList', 'hasFoto',
            'defaultNoSurat', 'defaultTanggal', 'defaultJam'
        ));
    }
    
    /**
     * Show form for editing existing panggilan
     */
    public function edit(Request $request, $id)
    {
        $panggilan = PanggilanOrtu::findOrFail($id);
        $nisn = $panggilan->nisn;
        $guruBkId = $request->get('guru_bk_id', $panggilan->guru_bk_id);
        
        $siswa = Siswa::where('nisn', $nisn)->first();
        
        if (!$siswa) {
            return redirect()->route('admin.guru-bk.index')
                ->with('error', 'Data siswa tidak ditemukan!');
        }
        
        // Get guru BK list for dropdown
        $guruBKList = GuruBK::where('status', 'Aktif')->orderBy('nama', 'asc')->get();
        
        // Check foto siswa
        $hasFoto = !empty($siswa->foto) && file_exists(public_path('storage/siswa/' . $siswa->foto));
        
        return view('admin.panggilan-ortu.form', compact(
            'siswa', 'guruBkId', 'guruBKList', 'hasFoto', 'panggilan'
        ));
    }
    
    /**
     * Store new panggilan
     */
    public function store(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string',
            'guru_bk_id' => 'required|integer',
            'tanggal_surat' => 'required|date',
            'perihal' => 'required|string|max:255',
            'tanggal_panggilan' => 'required|date',
        ]);
        
        $guruBkIdParam = $request->input('guru_bk_id_param', 0);
        
        PanggilanOrtu::create([
            'nisn' => $request->nisn,
            'guru_bk_id' => $request->guru_bk_id,
            'tanggal_surat' => $request->tanggal_surat,
            'no_surat' => $request->no_surat,
            'perihal' => $request->perihal,
            'alasan' => $request->alasan,
            'menghadap_ke' => 'Guru BK',
            'tanggal_panggilan' => $request->tanggal_panggilan,
            'jam_panggilan' => $request->jam_panggilan,
            'tempat' => $request->tempat ?? 'Ruang BK',
            'status' => 'Menunggu',
        ]);
        
        return redirect()->route('admin.panggilan-ortu.index', [
            'nisn' => $request->nisn,
            'guru_bk_id' => $guruBkIdParam
        ])->with('success', 'Surat panggilan berhasil dibuat!');
    }
    
    /**
     * Update existing panggilan
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'guru_bk_id' => 'required|integer',
            'tanggal_surat' => 'required|date',
            'perihal' => 'required|string|max:255',
            'tanggal_panggilan' => 'required|date',
        ]);
        
        $panggilan = PanggilanOrtu::findOrFail($id);
        $guruBkIdParam = $request->input('guru_bk_id_param', 0);
        
        $panggilan->update([
            'guru_bk_id' => $request->guru_bk_id,
            'tanggal_surat' => $request->tanggal_surat,
            'no_surat' => $request->no_surat,
            'perihal' => $request->perihal,
            'alasan' => $request->alasan,
            'tanggal_panggilan' => $request->tanggal_panggilan,
            'jam_panggilan' => $request->jam_panggilan,
            'tempat' => $request->tempat ?? 'Ruang BK',
            'status' => $request->status ?? 'Menunggu',
            'catatan' => $request->catatan,
        ]);
        
        return redirect()->route('admin.panggilan-ortu.index', [
            'nisn' => $panggilan->nisn,
            'guru_bk_id' => $guruBkIdParam
        ])->with('success', 'Surat panggilan berhasil diperbarui!');
    }
    
    /**
     * Print surat panggilan
     */
    public function print($id)
    {
        $panggilan = PanggilanOrtu::leftJoin('guru_bk', 'panggilan_ortu.guru_bk_id', '=', 'guru_bk.id')
            ->select('panggilan_ortu.*', 'guru_bk.nama as nama_guru_bk', 'guru_bk.nip as nip_guru_bk')
            ->where('panggilan_ortu.id', $id)
            ->first();
        
        if (!$panggilan) {
            return redirect()->back()->with('error', 'Data tidak ditemukan!');
        }
        
        $siswa = Siswa::where('nisn', $panggilan->nisn)->first();
        
        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan!');
        }
        
        // Get periode aktif
        $periodeAktif = \App\Models\DataPeriodik::where('aktif', 'Ya')->first();
        $tahunPelajaran = $periodeAktif->tahun_pelajaran ?? date('Y') . '/' . (date('Y') + 1);
        $semester = $periodeAktif->semester ?? 'Ganjil';
        $namaKepala = $periodeAktif->nama_kepala ?? '-';
        $nipKepala = $periodeAktif->nip_kepala ?? '-';
        
        // Calculate semester aktif for rombel
        $semesterAktif = $this->calculateSemesterAktif($siswa->angkatan_masuk, $tahunPelajaran, $semester);
        $rombelAktif = $siswa->{'rombel_semester_' . $semesterAktif} ?? $siswa->nama_rombel ?? '-';
        
        // Format dates
        $tanggalSurat = $this->formatTanggalIndonesia($panggilan->tanggal_surat);
        $tanggalPanggilan = $this->formatTanggalIndonesia($panggilan->tanggal_panggilan);
        
        $hariNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $hariPanggilan = $hariNames[date('w', strtotime($panggilan->tanggal_panggilan))];
        
        $jamPanggilan = $panggilan->jam_panggilan 
            ? date('H:i', strtotime($panggilan->jam_panggilan)) . ' WIB' 
            : '09:00 WIB';
        
        return view('admin.panggilan-ortu.print', compact(
            'panggilan', 'siswa', 'rombelAktif',
            'tanggalSurat', 'tanggalPanggilan', 'hariPanggilan', 'jamPanggilan',
            'namaKepala', 'nipKepala'
        ));
    }
    
    /**
     * Calculate semester aktif
     */
    private function calculateSemesterAktif($angkatan, $tahunPelajaran, $semester)
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
    
    /**
     * Format tanggal Indonesia
     */
    private function formatTanggalIndonesia($tanggal)
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $d = date('j', strtotime($tanggal));
        $m = intval(date('n', strtotime($tanggal)));
        $y = date('Y', strtotime($tanggal));
        
        return $d . ' ' . $bulan[$m] . ' ' . $y;
    }
}
