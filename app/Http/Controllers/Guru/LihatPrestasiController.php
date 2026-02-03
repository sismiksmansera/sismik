<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;

class LihatPrestasiController extends Controller
{
    public function index(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        if (!$guru) {
            return redirect()->route('login')->with('error', 'Data guru tidak ditemukan!');
        }
        $guruNama = $guru->nama;

        $type = $request->get('type'); // 'ekstra' or 'rombel'
        $sourceId = intval($request->get('id', 0));

        if (empty($type) || $sourceId <= 0) {
            return redirect()->route('guru.tugas-tambahan');
        }

        // Get active period
        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? '2024/2025';
        $semesterAktif = $periodik->semester ?? 'Ganjil';

        $sourceNama = '';
        $sumberPrestasi = '';

        if ($type == 'ekstra') {
            $sumberPrestasi = 'ekstrakurikuler';
            
            $ekstra = DB::table('ekstrakurikuler')
                ->where('id', $sourceId)
                ->where(function($q) use ($guruNama) {
                    $q->where('pembina_1', $guruNama)
                      ->orWhere('pembina_2', $guruNama)
                      ->orWhere('pembina_3', $guruNama);
                })
                ->first();

            if ($ekstra) {
                $sourceNama = $ekstra->nama_ekstrakurikuler;
            } else {
                return redirect()->route('guru.tugas-tambahan');
            }
        } elseif ($type == 'rombel') {
            $sumberPrestasi = 'rombel';
            
            $rombel = DB::table('rombel')
                ->where('id', $sourceId)
                ->where('wali_kelas', $guruNama)
                ->first();

            if ($rombel) {
                $sourceNama = $rombel->nama_rombel;
            } else {
                return redirect()->route('guru.tugas-tambahan');
            }
        } else {
            return redirect()->route('guru.tugas-tambahan');
        }

        // Get prestasi grouped by category
        $prestasiGrouped = DB::table('prestasi_siswa as ps')
            ->join('siswa as s', 'ps.siswa_id', '=', 's.id')
            ->where('ps.sumber_prestasi', $sumberPrestasi)
            ->where('ps.sumber_id', $sourceId)
            ->where('ps.tahun_pelajaran', $tahunPelajaran)
            ->where('ps.semester', $semesterAktif)
            ->select(
                'ps.nama_kompetisi', 'ps.juara', 'ps.jenjang', 'ps.penyelenggara', 'ps.tanggal_pelaksanaan',
                DB::raw('MAX(ps.tipe_peserta) as tipe_peserta'),
                DB::raw("GROUP_CONCAT(s.nama ORDER BY s.nama SEPARATOR '||') as siswa_list"),
                DB::raw("GROUP_CONCAT(s.nis ORDER BY s.nama SEPARATOR '||') as nis_list"),
                DB::raw('COUNT(*) as jumlah_siswa')
            )
            ->groupBy('ps.nama_kompetisi', 'ps.juara', 'ps.jenjang', 'ps.penyelenggara', 'ps.tanggal_pelaksanaan')
            ->orderBy('ps.tanggal_pelaksanaan', 'desc')
            ->get();

        // Parse arrays
        foreach ($prestasiGrouped as $prestasi) {
            $prestasi->siswa_array = explode('||', $prestasi->siswa_list);
            $prestasi->nis_array = explode('||', $prestasi->nis_list);
        }

        return view('guru.lihat-prestasi', compact(
            'guru', 'type', 'sourceId', 'sourceNama', 'sumberPrestasi',
            'tahunPelajaran', 'semesterAktif', 'prestasiGrouped'
        ));
    }

    public function hapus(Request $request)
    {
        $type = $request->type;
        $sourceId = intval($request->source_id);
        $namaKompetisi = $request->nama_kompetisi;
        $juara = $request->juara;
        $jenjang = $request->jenjang;
        $tanggalPelaksanaan = $request->tanggal_pelaksanaan;

        $sumberPrestasi = ($type == 'ekstra') ? 'ekstrakurikuler' : 'rombel';

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
