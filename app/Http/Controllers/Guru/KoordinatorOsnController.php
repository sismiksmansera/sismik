<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\AjangTalenta;
use App\Models\DataPeriodik;
use App\Models\Siswa;
use Carbon\Carbon;

class KoordinatorOsnController extends Controller
{
    private function calculateActiveSemester($angkatan, $tahunAktif, $semesterAktif)
    {
        if (empty($angkatan) || empty($tahunAktif) || empty($semesterAktif)) return 1;
        $tahunParts = explode('/', $tahunAktif);
        $tahunMulai = intval($tahunParts[0] ?? 0);
        $angkatanInt = intval($angkatan);
        $selisihTahun = $tahunMulai - $angkatanInt;

        if ($selisihTahun == 0) return (strtolower($semesterAktif) == 'ganjil') ? 1 : 2;
        if ($selisihTahun == 1) return (strtolower($semesterAktif) == 'ganjil') ? 3 : 4;
        if ($selisihTahun == 2) return (strtolower($semesterAktif) == 'ganjil') ? 5 : 6;
        return 1;
    }

    public function index()
    {
        $currentYear = date('Y');

        // Get all OSN ajang talenta for current year
        $osnList = AjangTalenta::where('nama_ajang', 'LIKE', '%OSN%')
            ->where('tahun', $currentYear)
            ->orderBy('nama_ajang', 'ASC')
            ->get();

        // Load peserta count for each
        foreach ($osnList as $osn) {
            $osn->jumlah_peserta = DB::table('peserta_ajang_talenta')
                ->where('ajang_talenta_id', $osn->id)
                ->count();
        }

        return view('guru.koordinator-osn', compact('osnList', 'currentYear'));
    }

    public function peserta($id)
    {
        $ajang = AjangTalenta::findOrFail($id);

        $periodeAktif = DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? '';
        $semesterAktif = $periodeAktif->semester ?? '';

        // Get peserta list
        $pesertaList = DB::table('peserta_ajang_talenta')
            ->join('siswa', 'peserta_ajang_talenta.siswa_id', '=', 'siswa.id')
            ->where('peserta_ajang_talenta.ajang_talenta_id', $id)
            ->select(
                'peserta_ajang_talenta.id',
                'peserta_ajang_talenta.siswa_id',
                'peserta_ajang_talenta.status',
                'peserta_ajang_talenta.created_at as tanggal_bergabung',
                'siswa.nama', 'siswa.nis', 'siswa.nisn', 'siswa.jk',
                'siswa.foto', 'siswa.angkatan_masuk', 'siswa.nama_rombel',
                'siswa.agama', 'siswa.tempat_lahir', 'siswa.tgl_lahir',
                'siswa.provinsi', 'siswa.kota', 'siswa.kecamatan',
                'siswa.kelurahan', 'siswa.dusun', 'siswa.rt_rw',
                'siswa.email', 'siswa.nohp_siswa',
                'siswa.mapel_osn_2026',
                'siswa.rombel_semester_1', 'siswa.rombel_semester_2',
                'siswa.rombel_semester_3', 'siswa.rombel_semester_4',
                'siswa.rombel_semester_5', 'siswa.rombel_semester_6'
            )
            ->orderBy('siswa.nama', 'ASC')
            ->get();

        foreach ($pesertaList as $peserta) {
            $semesterNumber = $this->calculateActiveSemester($peserta->angkatan_masuk, $tahunAktif, $semesterAktif);
            $rombelField = "rombel_semester_{$semesterNumber}";
            $peserta->rombel_aktif = $peserta->$rombelField ?? $peserta->nama_rombel ?? '-';

            $peserta->foto_url = $peserta->foto && Storage::disk('public')->exists('siswa/' . $peserta->foto)
                ? asset('storage/siswa/' . $peserta->foto)
                : null;
        }

        return view('guru.peserta-osn', compact('ajang', 'pesertaList'));
    }

    /**
     * Cetak Surat Keterangan Kepala Sekolah
     */
    public function cetakSuratKeterangan(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $periodeAktif = DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? '';
        $semesterAktif = $periodeAktif->semester ?? '';

        // Rombel aktif
        $semesterNumber = $this->calculateActiveSemester($siswa->angkatan_masuk, $tahunAktif, $semesterAktif);
        $rombelField = "rombel_semester_{$semesterNumber}";
        $rombelAktif = $siswa->$rombelField ?? $siswa->nama_rombel ?? '-';

        // Kepala Sekolah
        $kepalaSekolah = $periodeAktif->nama_kepala ?? '-';
        $nipKepala = $periodeAktif->nip_kepala ?? '-';

        // From request
        $nomorSurat = $request->query('nomor_surat', '-');
        $tanggalSuratRaw = $request->query('tanggal_surat', now()->format('Y-m-d'));
        $tanggalSurat = Carbon::parse($tanggalSuratRaw)->translatedFormat('d F Y');
        $mapelOsn = $request->query('mapel', $siswa->mapel_osn_2026 ?? '-');

        return view('cetak.surat-keterangan-osn', compact(
            'siswa', 'rombelAktif', 'kepalaSekolah', 'nipKepala',
            'nomorSurat', 'tanggalSurat', 'mapelOsn'
        ));
    }
}
