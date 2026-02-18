<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;

class AnggotaEkstrakurikulerController extends Controller
{
    private function getColorForEkstra($nama)
    {
        $colors = [
            'Pramuka' => '#3b82f6', 'Paskibra' => '#ef4444', 'PMR' => '#dc2626',
            'OSIS' => '#8b5cf6', 'Basket' => '#f59e0b', 'Futsal' => '#10b981',
            'Voli' => '#ec4899', 'Seni Musik' => '#06b6d4', 'Seni Tari' => '#f97316',
            'English Club' => '#6366f1', 'Japanese Club' => '#8b5cf6', 'IT Club' => '#0ea5e9',
            'KIR' => '#84cc16', 'Paduan Suara' => '#d946ef'
        ];
        return $colors[$nama] ?? '#6b7280';
    }

    private function getIconForEkstra($nama)
    {
        $icons = [
            'Pramuka' => 'fa-campground', 'Paskibra' => 'fa-flag', 'PMR' => 'fa-heartbeat',
            'OSIS' => 'fa-users-cog', 'Basket' => 'fa-basketball-ball', 'Futsal' => 'fa-futbol',
            'Voli' => 'fa-volleyball-ball', 'Seni Musik' => 'fa-music', 'Seni Tari' => 'fa-gem',
            'English Club' => 'fa-language', 'IT Club' => 'fa-laptop-code', 'KIR' => 'fa-flask'
        ];
        return $icons[$nama] ?? 'fa-star';
    }

    private function getBestRombelForDisplay($studentData, $tahunPelajaran, $semester, $angkatan)
    {
        if (!empty($angkatan)) {
            $tahunParts = explode('/', $tahunPelajaran);
            $tahunAwal = intval($tahunParts[0]);
            $angkatan = intval($angkatan);
            if ($angkatan <= $tahunAwal) {
                $tahunKe = $tahunAwal - $angkatan + 1;
                if ($tahunKe > 3) $tahunKe = 3;
                $semesterNum = (strtolower($semester) == 'ganjil') ? 1 : 2;
                $semesterTotal = (($tahunKe - 1) * 2) + $semesterNum;
                if ($semesterTotal < 1) $semesterTotal = 1;
                if ($semesterTotal > 6) $semesterTotal = 6;
                $column = "rombel_semester_" . $semesterTotal;
                if (!empty($studentData->$column)) {
                    return $studentData->$column;
                }
            }
        }
        
        for ($i = 6; $i >= 1; $i--) {
            $col = "rombel_semester_$i";
            if (!empty($studentData->$col)) return $studentData->$col;
        }
        return "-";
    }

    public function index(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        if (!$guru) {
            return redirect()->route('login')->with('error', 'Data guru tidak ditemukan!');
        }
        $guruNama = $guru->nama;

        $ekstraId = $request->get('id');
        if (!$ekstraId) {
            return redirect()->route('guru.tugas-tambahan')->with('error', 'ID ekstrakurikuler tidak ditemukan!');
        }

        // Get ekstrakurikuler data
        $ekstra = DB::table('ekstrakurikuler')->where('id', $ekstraId)->first();
        if (!$ekstra) {
            return redirect()->route('guru.tugas-tambahan')->with('error', 'Data ekstrakurikuler tidak ditemukan!');
        }

        // Verify guru is pembina OR is Koordinator Ekstrakurikuler
        $isPembina = ($ekstra->pembina_1 == $guruNama || $ekstra->pembina_2 == $guruNama || $ekstra->pembina_3 == $guruNama);
        
        // Check if user is Koordinator Ekstrakurikuler
        $isKoordinator = DB::table('tugas_tambahan_guru as t')
            ->join('jenis_tugas_tambahan_lain as j', 't.jenis_tugas_id', '=', 'j.id')
            ->where('t.tipe_guru', 'guru')
            ->where('t.guru_id', $guru->id)
            ->where('j.nama_tugas', 'like', '%ekstrakurikuler%')
            ->exists();
        
        if (!$isPembina && !$isKoordinator) {
            return redirect()->route('guru.tugas-tambahan')->with('error', 'Anda tidak memiliki akses ke ekstrakurikuler ini!');
        }

        // Get members
        $anggotaList = DB::table('anggota_ekstrakurikuler as ae')
            ->join('siswa as s', 'ae.siswa_id', '=', 's.id')
            ->where('ae.ekstrakurikuler_id', $ekstraId)
            ->where('ae.tahun_pelajaran', $ekstra->tahun_pelajaran)
            ->where('ae.semester', $ekstra->semester)
            ->select('ae.*', 's.nama as nama_siswa', 's.nis', 's.nisn', 's.angkatan_masuk as angkatan', 
                     's.jk', 's.email', 's.foto', 's.rombel_semester_1', 's.rombel_semester_2', 
                     's.rombel_semester_3', 's.rombel_semester_4', 's.rombel_semester_5', 's.rombel_semester_6')
            ->orderBy('s.nama', 'asc')
            ->get();

        // Add rombel_aktif to each member
        foreach ($anggotaList as $anggota) {
            $anggota->rombel_aktif = $this->getBestRombelForDisplay($anggota, $ekstra->tahun_pelajaran, $ekstra->semester, $anggota->angkatan);
        }

        $ekstraColor = $this->getColorForEkstra($ekstra->nama_ekstrakurikuler);
        $ekstraIcon = $this->getIconForEkstra($ekstra->nama_ekstrakurikuler);

        // Determine pembina position
        $posisiPembina = '';
        if ($ekstra->pembina_1 == $guruNama) $posisiPembina = 'Pembina Utama';
        elseif ($ekstra->pembina_2 == $guruNama) $posisiPembina = 'Pembina Kedua';
        elseif ($ekstra->pembina_3 == $guruNama) $posisiPembina = 'Pembina Ketiga';

        return view('guru.anggota-ekstrakurikuler', compact(
            'guru', 'ekstra', 'anggotaList', 'ekstraColor', 'ekstraIcon', 'posisiPembina'
        ));
    }

    public function tambahAnggota(Request $request)
    {
        $ekstraId = $request->id;
        $siswaIds = $request->siswa_ids ?? [];

        if (empty($siswaIds)) {
            return back()->with('error', 'Tidak ada siswa yang dipilih.');
        }

        $ekstra = DB::table('ekstrakurikuler')->where('id', $ekstraId)->first();
        if (!$ekstra) {
            return back()->with('error', 'Ekstrakurikuler tidak ditemukan.');
        }

        $successCount = 0;
        $errorCount = 0;
        $tanggalBergabung = now();

        foreach ($siswaIds as $siswaId) {
            $siswaId = intval($siswaId);
            
            // Check if already exists
            $exists = DB::table('anggota_ekstrakurikuler')
                ->where('ekstrakurikuler_id', $ekstraId)
                ->where('siswa_id', $siswaId)
                ->where('tahun_pelajaran', $ekstra->tahun_pelajaran)
                ->where('semester', $ekstra->semester)
                ->exists();

            if (!$exists) {
                DB::table('anggota_ekstrakurikuler')->insert([
                    'ekstrakurikuler_id' => $ekstraId,
                    'siswa_id' => $siswaId,
                    'tahun_pelajaran' => $ekstra->tahun_pelajaran,
                    'semester' => $ekstra->semester,
                    'tanggal_bergabung' => $tanggalBergabung,
                    'status' => 'Aktif'
                ]);
                $successCount++;
            } else {
                $errorCount++;
            }
        }

        $message = "Berhasil menambahkan $successCount anggota.";
        if ($errorCount > 0) {
            $message .= " $errorCount anggota gagal (sudah terdaftar).";
        }

        return back()->with('success', $message);
    }

    public function hapusAnggota(Request $request)
    {
        $anggotaId = $request->anggota_id;
        $ekstraId = $request->ekstra_id;

        $deleted = DB::table('anggota_ekstrakurikuler')
            ->where('id', $anggotaId)
            ->where('ekstrakurikuler_id', $ekstraId)
            ->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Anggota berhasil dihapus!']);
        }

        return response()->json(['success' => false, 'message' => 'Gagal menghapus anggota!']);
    }

    public function updateNilai(Request $request)
    {
        $anggotaId = $request->anggota_id;
        $nilai = $request->nilai;

        DB::table('anggota_ekstrakurikuler')
            ->where('id', $anggotaId)
            ->update(['nilai' => $nilai]);

        return response()->json(['success' => true, 'message' => 'Nilai berhasil disimpan!']);
    }

    public function cariSiswa(Request $request)
    {
        $ekstraId = $request->id;
        $search = $request->search ?? '';
        $kelas = $request->kelas ?? '';

        $ekstra = DB::table('ekstrakurikuler')->where('id', $ekstraId)->first();
        if (!$ekstra) {
            return response()->json(['siswa' => []]);
        }

        // Get existing member IDs
        $existingIds = DB::table('anggota_ekstrakurikuler')
            ->where('ekstrakurikuler_id', $ekstraId)
            ->where('tahun_pelajaran', $ekstra->tahun_pelajaran)
            ->where('semester', $ekstra->semester)
            ->pluck('siswa_id')
            ->toArray();

        // Search siswa
        $query = DB::table('siswa')
            ->whereNotIn('id', $existingIds)
            ->orderBy('nama', 'asc');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('nis', 'like', "%$search%")
                  ->orWhere('nisn', 'like', "%$search%");
            });
        }

        $siswaList = $query->limit(50)->get();

        // Add rombel_aktif to each
        foreach ($siswaList as $siswa) {
            $siswa->rombel_aktif = $this->getBestRombelForDisplay($siswa, $ekstra->tahun_pelajaran, $ekstra->semester, $siswa->angkatan_masuk);
        }

        return response()->json(['siswa' => $siswaList]);
    }
}
