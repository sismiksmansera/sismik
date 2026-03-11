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

        $activities = $this->getActivities($tanggalMulai, $tanggalAkhir, $guruBK->id, $guruBK->nama);

        $stats = [
            'total' => $activities->count(),
            'bimbingan' => $activities->where('type', 'bimbingan')->count(),
            'panggilan' => $activities->where('type', 'panggilan')->count(),
            'pelanggaran' => $activities->where('type', 'pelanggaran')->count(),
            'manual' => $activities->where('type', 'manual')->count(),
            'bimbingan_wali' => $activities->where('type', 'bimbingan_wali')->count(),
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
        $sekolah = null; // Table does not exist, use defaults in view

        $tanggalMulai = $request->get('tanggal_mulai', date('Y-m-d'));
        $tanggalAkhir = $request->get('tanggal_akhir', date('Y-m-d'));

        $activities = $this->getActivities($tanggalMulai, $tanggalAkhir, $guruBK->id, $guruBK->nama);

        return view('guru-bk.jurnal-harian-print', compact(
            'guruBK', 'periodeAktif', 'sekolah', 'activities',
            'tanggalMulai', 'tanggalAkhir'
        ));
    }

    private function getActivities($tanggalMulai, $tanggalAkhir, $guruBkId, $guruNama = '')
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
            ->where('cb.guru_bk_id', $guruBkId)
            ->orderBy('cb.tanggal', 'desc')
            ->orderBy('cb.created_at', 'desc')
            ->get();

        foreach ($catatan as $item) {
            $activities->push((object)[
                'type' => 'bimbingan',
                'icon' => 'fa-clipboard-list',
                'color' => '#3b82f6',
                'label' => 'Melakukan Bimbingan Konseling',
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
            ->where('p.guru_bk_id', $guruBkId)
            ->orderBy('p.created_at', 'desc')
            ->get();

        foreach ($panggilan as $item) {
            $activities->push((object)[
                'type' => 'panggilan',
                'icon' => 'fa-phone',
                'color' => '#8b5cf6',
                'label' => 'Melakukan Pemanggilan Orang Tua',
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
            ->where('pl.guru_bk_id', $guruBkId)
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
                'label' => 'Pencatatan Pelanggaran Siswa',
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

        // 4. Jurnal Manual
        if (\Illuminate\Support\Facades\Schema::hasTable('jurnal_manual')) {
            $manual = DB::table('jurnal_manual as jm')
                ->leftJoin('siswa as s', DB::raw('jm.nisn COLLATE utf8mb4_general_ci'), '=', 's.nisn')
                ->select(
                    'jm.id', 'jm.tanggal', 'jm.waktu', 'jm.jenis_aktivitas',
                    'jm.tipe_subyek', 'jm.subyek_manual', 'jm.deskripsi',
                    'jm.keterangan', 'jm.created_at',
                    's.nama as nama_siswa', 's.jk', 's.nama_rombel'
                )
                ->where('jm.guru_bk_id', $guruBkId)
                ->whereBetween('jm.tanggal', [$tanggalMulai, $tanggalAkhir])
                ->orderBy('jm.tanggal', 'desc')
                ->orderBy('jm.waktu', 'desc')
                ->get();

            foreach ($manual as $item) {
                $subyek = $item->tipe_subyek === 'Siswa'
                    ? ($item->nama_siswa ?? '-')
                    : ($item->subyek_manual ?? '-');

                $activities->push((object)[
                    'type' => 'manual',
                    'icon' => 'fa-pen-fancy',
                    'color' => '#f59e0b',
                    'label' => $item->jenis_aktivitas,
                    'tanggal' => $item->tanggal,
                    'waktu' => $item->waktu ?? ($item->created_at ? Carbon::parse($item->created_at)->format('H:i') : '-'),
                    'nama_siswa' => $subyek,
                    'jk' => $item->jk ?? '',
                    'rombel' => $item->nama_rombel ?? '-',
                    'detail' => $item->deskripsi,
                    'sub_detail' => $item->keterangan ?? '',
                    'status' => '',
                    'guru_bk' => '',
                    'manual_id' => $item->id,
                ]);
            }
        }

        // 5. Catatan Guru Wali (Bimbingan sebagai Wali Kelas)
        if (\Illuminate\Support\Facades\Schema::hasTable('catatan_guru_wali') && $guruNama) {
            $wali = DB::table('catatan_guru_wali as cw')
                ->leftJoin('siswa as s', 'cw.siswa_id', '=', 's.id')
                ->select(
                    'cw.id', 'cw.tanggal_pencatatan', 'cw.jenis_bimbingan',
                    'cw.catatan', 'cw.perkembangan', 'cw.created_at',
                    's.nama as nama_siswa', 's.jk', 's.nama_rombel'
                )
                ->where('cw.guru_nama', $guruNama)
                ->whereBetween('cw.tanggal_pencatatan', [$tanggalMulai, $tanggalAkhir])
                ->orderBy('cw.tanggal_pencatatan', 'desc')
                ->get();

            foreach ($wali as $item) {
                $activities->push((object)[
                    'type' => 'bimbingan_wali',
                    'icon' => 'fa-chalkboard-teacher',
                    'color' => '#06b6d4',
                    'label' => 'Bimbingan Guru Wali: ' . $item->jenis_bimbingan,
                    'tanggal' => $item->tanggal_pencatatan,
                    'waktu' => $item->created_at ? Carbon::parse($item->created_at)->format('H:i') : '-',
                    'nama_siswa' => $item->nama_siswa ?? '-',
                    'jk' => $item->jk ?? '',
                    'rombel' => $item->nama_rombel ?? '-',
                    'detail' => $item->catatan,
                    'sub_detail' => $item->perkembangan ? 'Perkembangan: ' . $item->perkembangan : '',
                    'status' => '',
                    'guru_bk' => $guruNama,
                ]);
            }
        }

        return $activities->sortByDesc(function ($a) {
            return $a->tanggal . ' ' . $a->waktu;
        })->values();
    }
}


