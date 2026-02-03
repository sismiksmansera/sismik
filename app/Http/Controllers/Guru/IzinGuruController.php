<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\DataPeriodik;
use App\Models\Rombel;

class IzinGuruController extends Controller
{
    /**
     * Display izin guru form
     */
    public function index(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        $namaGuru = $guru->nama ?? '';
        
        // Get parameters
        $idRombel = $request->query('id_rombel');
        $mapel = $request->query('mapel');
        $lockedTanggal = $request->query('tanggal', '');
        $lockedJamKe = $request->query('jam_ke', '');
        $fromPage = $request->query('from', 'dashboard');
        
        // Validate required params
        if (empty($idRombel) || empty($mapel)) {
            return redirect()->route('guru.dashboard')
                ->with('error', 'Parameter tidak lengkap (id_rombel / mapel).');
        }
        
        // Get rombel data
        $rombel = Rombel::find($idRombel);
        if (!$rombel) {
            return redirect()->route('guru.dashboard')
                ->with('error', 'Data rombel tidak ditemukan.');
        }
        $namaRombel = $rombel->nama_rombel;
        
        // Get active period
        $periodik = DataPeriodik::aktif()->first();
        if (!$periodik) {
            return redirect()->route('guru.dashboard')
                ->with('error', 'Periode aktif tidak ditemukan.');
        }
        
        $tahunPelajaranAktif = $periodik->tahun_pelajaran;
        $semesterAktif = $periodik->semester;
        
        // Check if izin already exists (View/Edit Mode)
        $existingIzin = null;
        $isViewMode = false;
        
        if (!empty($lockedTanggal) && Schema::hasTable('izin_guru')) {
            $existingIzin = DB::table('izin_guru')
                ->where('tanggal_izin', $lockedTanggal)
                ->where('mapel', $mapel)
                ->where('id_rombel', $idRombel)
                ->where('guru', $namaGuru)
                ->first();
            
            if ($existingIzin) {
                $isViewMode = true;
            }
        }
        
        // Format jam for display
        $jamDisplay = !empty($lockedJamKe) ? "Jam ke-" . str_replace(',', ', ', $lockedJamKe) : '-';
        
        return view('guru.izin-guru', compact(
            'guru', 'namaGuru', 'rombel', 'namaRombel', 'mapel',
            'periodik', 'tahunPelajaranAktif', 'semesterAktif',
            'lockedTanggal', 'lockedJamKe', 'jamDisplay', 'fromPage',
            'existingIzin', 'isViewMode', 'idRombel'
        ));
    }
    
    /**
     * Store izin guru data
     */
    public function store(Request $request)
    {
        $guru = Auth::guard('guru')->user();
        $namaGuru = $guru->nama ?? '';
        
        $idRombel = $request->input('id_rombel');
        $namaRombel = $request->input('nama_rombel');
        $mapel = $request->input('mapel');
        $tanggalIzin = $request->input('tanggal_izin');
        $jamKe = $request->input('jam_ke');
        $alasanIzin = $request->input('alasan_izin');
        $materi = $request->input('materi', '');
        $uraianTugas = $request->input('uraian_tugas', '');
        $fromPage = $request->input('from', 'dashboard');
        $izinId = $request->input('izin_id');
        
        // Get active period
        $periodik = DataPeriodik::aktif()->first();
        $tahunPelajaran = $periodik->tahun_pelajaran ?? '';
        $semester = $periodik->semester ?? '';
        
        // Validate
        if (empty($alasanIzin)) {
            return back()->with('error', 'Alasan izin wajib diisi!');
        }
        
        // Ensure izin_guru table exists
        if (!Schema::hasTable('izin_guru')) {
            // Create the table
            Schema::create('izin_guru', function ($table) {
                $table->id();
                $table->string('id_rombel');
                $table->string('nama_rombel');
                $table->string('mapel');
                $table->string('guru');
                $table->date('tanggal_izin');
                $table->string('jam_ke');
                $table->text('alasan_izin');
                $table->text('materi')->nullable();
                $table->text('uraian_tugas')->nullable();
                $table->string('tahun_pelajaran');
                $table->string('semester');
                $table->timestamps();
            });
        }
        
        if (!empty($izinId)) {
            // Update existing izin
            DB::table('izin_guru')
                ->where('id', $izinId)
                ->update([
                    'alasan_izin' => $alasanIzin,
                    'materi' => $materi,
                    'uraian_tugas' => $uraianTugas,
                    'updated_at' => now(),
                ]);
            $message = "Izin guru berhasil diperbarui.";
        } else {
            // Insert new izin
            DB::table('izin_guru')->insert([
                'id_rombel' => $idRombel,
                'nama_rombel' => $namaRombel,
                'mapel' => $mapel,
                'guru' => $namaGuru,
                'tanggal_izin' => $tanggalIzin,
                'jam_ke' => $jamKe,
                'alasan_izin' => $alasanIzin,
                'materi' => $materi,
                'uraian_tugas' => $uraianTugas,
                'tahun_pelajaran' => $tahunPelajaran,
                'semester' => $semester,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $message = "Izin guru berhasil disimpan.";
        }
        
        if ($fromPage === 'dashboard') {
            return redirect()->route('guru.dashboard')
                ->with('success', $message);
        }
        
        return back()->with('success', $message);
    }
}
