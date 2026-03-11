<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class JurnalManualController extends Controller
{
    public function create()
    {
        $guruBK = Auth::guard('guru_bk')->user();

        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        // Ensure table exists
        if (!Schema::hasTable('jurnal_manual')) {
            Schema::create('jurnal_manual', function ($table) {
                $table->id();
                $table->unsignedBigInteger('guru_bk_id');
                $table->date('tanggal');
                $table->time('waktu')->nullable();
                $table->string('jenis_aktivitas');
                $table->enum('tipe_subyek', ['Siswa', 'Lainnya'])->default('Lainnya');
                $table->string('nisn')->nullable();
                $table->string('subyek_manual')->nullable();
                $table->text('deskripsi');
                $table->text('keterangan')->nullable();
                $table->timestamps();
                $table->index('guru_bk_id');
                $table->index('tanggal');
            });
        }

        return view('guru-bk.jurnal-manual-create', compact('guruBK'));
    }

    public function store(Request $request)
    {
        $guruBK = Auth::guard('guru_bk')->user();

        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        $request->validate([
            'tanggal' => 'required|date',
            'jenis_aktivitas' => 'required|string',
            'tipe_subyek' => 'required|in:Siswa,Lainnya',
            'deskripsi' => 'required|string',
        ]);

        DB::table('jurnal_manual')->insert([
            'guru_bk_id' => $guruBK->id,
            'tanggal' => $request->tanggal,
            'waktu' => $request->waktu,
            'jenis_aktivitas' => $request->jenis_aktivitas,
            'tipe_subyek' => $request->tipe_subyek,
            'nisn' => $request->tipe_subyek === 'Siswa' ? $request->nisn : null,
            'subyek_manual' => $request->tipe_subyek === 'Lainnya' ? $request->subyek_manual : null,
            'deskripsi' => $request->deskripsi,
            'keterangan' => $request->keterangan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('guru_bk.jurnal-harian', [
            'tanggal_mulai' => $request->tanggal,
            'tanggal_akhir' => $request->tanggal,
        ])->with('success', 'Jurnal manual berhasil disimpan.');
    }

    public function searchSiswa(Request $request)
    {
        $search = $request->input('q', '');

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $siswa = DB::table('siswa')
            ->select('nisn', 'nama', 'nis', 'jk', 'nama_rombel')
            ->where(function ($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('nisn', 'LIKE', "%{$search}%")
                  ->orWhere('nis', 'LIKE', "%{$search}%");
            })
            ->orderBy('nama')
            ->limit(15)
            ->get();

        return response()->json($siswa);
    }

    public function edit($id)
    {
        $guruBK = Auth::guard('guru_bk')->user();

        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        $jurnal = DB::table('jurnal_manual')->where('id', $id)->where('guru_bk_id', $guruBK->id)->first();

        if (!$jurnal) {
            return back()->with('error', 'Data jurnal tidak ditemukan.');
        }

        // Get siswa name if tipe_subyek is Siswa
        $siswaData = null;
        if ($jurnal->tipe_subyek === 'Siswa' && $jurnal->nisn) {
            $siswaData = DB::table('siswa')
                ->select('nisn', 'nama', 'nis', 'jk', 'nama_rombel')
                ->where('nisn', $jurnal->nisn)
                ->first();
        }

        return view('guru-bk.jurnal-manual-edit', compact('guruBK', 'jurnal', 'siswaData'));
    }

    public function update(Request $request, $id)
    {
        $guruBK = Auth::guard('guru_bk')->user();

        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        $jurnal = DB::table('jurnal_manual')->where('id', $id)->where('guru_bk_id', $guruBK->id)->first();

        if (!$jurnal) {
            return back()->with('error', 'Data jurnal tidak ditemukan.');
        }

        $request->validate([
            'tanggal' => 'required|date',
            'jenis_aktivitas' => 'required|string',
            'tipe_subyek' => 'required|in:Siswa,Lainnya',
            'deskripsi' => 'required|string',
        ]);

        DB::table('jurnal_manual')->where('id', $id)->update([
            'tanggal' => $request->tanggal,
            'waktu' => $request->waktu,
            'jenis_aktivitas' => $request->jenis_aktivitas,
            'tipe_subyek' => $request->tipe_subyek,
            'nisn' => $request->tipe_subyek === 'Siswa' ? $request->nisn : null,
            'subyek_manual' => $request->tipe_subyek === 'Lainnya' ? $request->subyek_manual : null,
            'deskripsi' => $request->deskripsi,
            'keterangan' => $request->keterangan,
            'updated_at' => now(),
        ]);

        return redirect()->route('guru_bk.jurnal-harian', [
            'tanggal_mulai' => $request->tanggal,
            'tanggal_akhir' => $request->tanggal,
        ])->with('success', 'Jurnal manual berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $guruBK = Auth::guard('guru_bk')->user();

        if (!$guruBK) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai Guru BK.');
        }

        $jurnal = DB::table('jurnal_manual')->where('id', $id)->where('guru_bk_id', $guruBK->id)->first();

        if (!$jurnal) {
            return back()->with('error', 'Data jurnal tidak ditemukan.');
        }

        DB::table('jurnal_manual')->where('id', $id)->delete();

        return back()->with('success', 'Jurnal manual berhasil dihapus.');
    }
}
