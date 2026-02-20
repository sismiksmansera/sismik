<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AjangTalenta;
use App\Models\Siswa;
use App\Models\DataPeriodik;
use Carbon\Carbon;

class PesertaAjangTalentaController extends Controller
{
    /**
     * Helper: Calculate active semester from angkatan
     */
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

    /**
     * Display peserta of an ajang talenta
     */
    public function index($id)
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

        // Calculate rombel_aktif for each
        foreach ($pesertaList as $peserta) {
            $semesterNumber = $this->calculateActiveSemester($peserta->angkatan_masuk, $tahunAktif, $semesterAktif);
            $rombelField = "rombel_semester_{$semesterNumber}";
            $peserta->rombel_aktif = $peserta->$rombelField ?? $peserta->nama_rombel ?? '-';

            $peserta->foto_url = $peserta->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/' . $peserta->foto)
                ? asset('storage/siswa/' . $peserta->foto)
                : null;
        }

        // Get available siswa (active, not yet in this ajang)
        $pesertaIds = $pesertaList->pluck('siswa_id')->toArray();
        $siswaAvailable = Siswa::where('status_siswa', 'Aktif')
            ->whereNotIn('id', $pesertaIds)
            ->orderBy('nama', 'ASC')
            ->get();

        foreach ($siswaAvailable as $siswa) {
            $semesterNumber = $this->calculateActiveSemester($siswa->angkatan_masuk, $tahunAktif, $semesterAktif);
            $rombelField = "rombel_semester_{$semesterNumber}";
            $siswa->rombel_aktif = $siswa->$rombelField ?? $siswa->nama_rombel ?? '-';
        }

        return view('admin.manajemen-talenta.peserta', compact(
            'ajang',
            'pesertaList',
            'siswaAvailable'
        ));
    }

    /**
     * Add peserta to ajang (POST)
     */
    public function tambah(Request $request, $id)
    {
        $ajang = AjangTalenta::findOrFail($id);
        $siswa_ids = $request->input('siswa_ids', []);

        if (empty($siswa_ids)) {
            return back()->with('error', 'Tidak ada siswa yang dipilih!');
        }

        $success = 0;
        $error = 0;

        foreach ($siswa_ids as $siswa_id) {
            $exists = DB::table('peserta_ajang_talenta')
                ->where('ajang_talenta_id', $id)
                ->where('siswa_id', intval($siswa_id))
                ->exists();

            if (!$exists) {
                DB::table('peserta_ajang_talenta')->insert([
                    'ajang_talenta_id' => $id,
                    'siswa_id' => intval($siswa_id),
                    'status' => 'Aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $success++;
            } else {
                $error++;
            }
        }

        if ($success > 0) {
            $msg = "Berhasil menambahkan $success peserta.";
            if ($error > 0) $msg .= " $error peserta gagal (sudah terdaftar).";
            return back()->with('success', $msg);
        }
        return back()->with('error', 'Gagal menambahkan peserta. Semua siswa sudah terdaftar.');
    }

    /**
     * Remove peserta from ajang (POST)
     */
    public function hapus(Request $request, $id)
    {
        $peserta_id = $request->input('peserta_id');

        $exists = DB::table('peserta_ajang_talenta')
            ->where('id', $peserta_id)
            ->where('ajang_talenta_id', $id)
            ->exists();

        if (!$exists) {
            return back()->with('error', 'Data peserta tidak ditemukan!');
        }

        DB::table('peserta_ajang_talenta')->where('id', $peserta_id)->delete();
        return back()->with('success', 'Peserta berhasil dihapus!');
    }

    /**
     * Cetak Surat Keterangan Kepala Sekolah
     */
    public function cetakSuratKeterangan(Request $request, $ajangId, $siswaId)
    {
        $siswa = Siswa::findOrFail($siswaId);

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
