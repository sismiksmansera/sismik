<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JurnalHarianController extends Controller
{
    public function index(Request $request)
    {
        $guruBK = Auth::guard('guru_bk')->user();

        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        $periodeAktif = DB::table('data_periodik')->where('aktif', 'Ya')->first();

        $tanggalMulai = $request->get('tanggal_mulai', date('Y-m-d'));
        $tanggalAkhir = $request->get('tanggal_akhir', date('Y-m-d'));

        $activities = $this->getActivities($tanggalMulai, $tanggalAkhir);

        $stats = [
            'total' => $activities->count(),
            'bimbingan' => $activities->where('type', 'bimbingan')->count(),
            'panggilan' => $activities->where('type', 'panggilan')->count(),
            'pelanggaran' => $activities->where('type', 'pelanggaran')->count(),
        ];

        return view('guru-bk.jurnal-harian', compact(
            'guruBK', 'periodeAktif', 'activities', 'stats',
            'tanggalMulai', 'tanggalAkhir'
        ));
    }

    public function print(Request $request)
    {
        $guruBK = Auth::guard('guru_bk')->user();

        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        $periodeAktif = DB::table('data_periodik')->where('aktif', 'Ya')->first();
        try { $sekolah = DB::table('profil_sekolah')->first(); } catch (\Exception $e) { $sekolah = null; }

        $tanggalMulai = $request->get('tanggal_mulai', date('Y-m-d'));
        $tanggalAkhir = $request->get('tanggal_akhir', date('Y-m-d'));

        $activities = $this->getActivities($tanggalMulai, $tanggalAkhir);

        return view('guru-bk.jurnal-harian-print', compact(
            'guruBK', 'periodeAktif', 'sekolah', 'activities',
            'tanggalMulai', 'tanggalAkhir'
        ));
    }

    private function getActivities($tanggalMulai, $tanggalAkhir)
    {
        $activities = collect();

        // 1. Catatan Bimbingan
        $catatan = DB::table('catatan_bimbingan as cb')
            ->leftJoin('siswa as s', 'cb.nisn', '=', 's.nisn')
            ->leftJoin('guru_bk as g', 'cb.guru_bk_id', '=', 'g.id')
            ->select(
                'cb.id', 'cb.tanggal', 'cb.jenis_bimbingan', 'cb.masalah',
                'cb.penyelesaian', 'cb.status', 'cb.created_at',
                's.nama as nama_siswa', 's.jk', 's.nama_rombel',
                'g.nama as nama_guru_bk'
            )
            ->whereBetween('cb.tanggal', [$tanggalMulai, $tanggalAkhir])
            ->orderBy('cb.tanggal', 'desc')
            ->orderBy('cb.created_at', 'desc')
            ->get();

        foreach ($catatan as $item) {
            $activities->push((object)[
                'type' => 'bimbingan',
                'icon' => 'fa-clipboard-list',
                'color' => '#3b82f6',
                'label' => 'Catatan Bimbingan',
                'tanggal' => $item->tanggal,
                'waktu' => $item->created_at ? Carbon::parse($item->created_at)->format('H:i') : '-',
                'nama_siswa' => $item->nama_siswa ?? '-',
                'jk' => $item->jk ?? '',
                'rombel' => $item->nama_rombel ?? '-',
                'detail' => $item->jenis_bimbingan . ': ' . $item->masalah,
                'sub_detail' => $item->penyelesaian ?? '',
                'status' => $item->status ?? 'Belum',
                'guru_bk' => $item->nama_guru_bk ?? '-',
            ]);
        }

        // 2. Panggilan Orang Tua
        $panggilan = DB::table('panggilan_ortu as p')
            ->leftJoin('siswa as s', DB::raw('p.nisn COLLATE utf8mb4_general_ci'), '=', 's.nisn')
            ->leftJoin('guru_bk as g', 'p.guru_bk_id', '=', 'g.id')
            ->select(
                'p.id', 'p.tanggal_surat', 'p.perihal', 'p.alasan',
                'p.status', 'p.created_at', 'p.tanggal_panggilan',
                's.nama as nama_siswa', 's.jk', 's.nama_rombel',
                'g.nama as nama_guru_bk'
            )
            ->whereBetween('p.created_at', [$tanggalMulai . ' 00:00:00', $tanggalAkhir . ' 23:59:59'])
            ->orderBy('p.created_at', 'desc')
            ->get();

        foreach ($panggilan as $item) {
            $activities->push((object)[
                'type' => 'panggilan',
                'icon' => 'fa-phone',
                'color' => '#8b5cf6',
                'label' => 'Panggilan Orang Tua',
                'tanggal' => Carbon::parse($item->created_at)->format('Y-m-d'),
                'waktu' => Carbon::parse($item->created_at)->format('H:i'),
                'nama_siswa' => $item->nama_siswa ?? '-',
                'jk' => $item->jk ?? '',
                'rombel' => $item->nama_rombel ?? '-',
                'detail' => $item->perihal,
                'sub_detail' => $item->alasan ?? '',
                'status' => $item->status ?? 'Menunggu',
                'guru_bk' => $item->nama_guru_bk ?? '-',
            ]);
        }

        // 3. Pelanggaran
        $pelanggaran = DB::table('pelanggaran as pl')
            ->leftJoin('guru_bk as g', 'pl.guru_bk_id', '=', 'g.id')
            ->select(
                'pl.id', 'pl.tanggal', 'pl.waktu', 'pl.jenis_pelanggaran',
                'pl.deskripsi', 'pl.sanksi', 'pl.created_at',
                'g.nama as nama_guru_bk'
            )
            ->whereBetween('pl.tanggal', [$tanggalMulai, $tanggalAkhir])
            ->orderBy('pl.tanggal', 'desc')
            ->orderBy('pl.waktu', 'desc')
            ->get();

        foreach ($pelanggaran as $item) {
            $siswaList = DB::table('pelanggaran_siswa as ps')
                ->leftJoin('siswa as s', 'ps.siswa_id', '=', 's.id')
                ->where('ps.pelanggaran_id', $item->id)
                ->select('s.nama', 's.jk', 's.nama_rombel')
                ->get();

            $namaAll = $siswaList->pluck('nama')->implode(', ');

            $activities->push((object)[
                'type' => 'pelanggaran',
                'icon' => 'fa-exclamation-triangle',
                'color' => '#ef4444',
                'label' => 'Pelanggaran Siswa',
                'tanggal' => $item->tanggal,
                'waktu' => $item->waktu ?? ($item->created_at ? Carbon::parse($item->created_at)->format('H:i') : '-'),
                'nama_siswa' => $namaAll ?: '-',
                'jk' => $siswaList->first()->jk ?? '',
                'rombel' => $siswaList->first()->nama_rombel ?? '-',
                'detail' => $item->jenis_pelanggaran . ($item->deskripsi ? ': ' . $item->deskripsi : ''),
                'sub_detail' => $item->sanksi ? 'Sanksi: ' . $item->sanksi : '',
                'status' => '',
                'guru_bk' => $item->nama_guru_bk ?? '-',
            ]);
        }

        return $activities->sortByDesc(function ($a) {
            return $a->tanggal . ' ' . $a->waktu;
        })->values();
    }
}

