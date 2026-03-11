<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Siswa;
use App\Models\SiswaKeluar;
use App\Models\Rombel;
use App\Models\GuruBK;
use App\Models\Guru;
use App\Models\DataPeriodik;
use Illuminate\Support\Facades\DB;
use App\Services\NameCascadeService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SiswaController extends Controller
{
    /**
     * Helper: Calculate active semester based on angkatan
     */
    private function calculateActiveSemester($angkatan, $tahunAktif, $semesterAktif)
    {
        if (empty($angkatan) || empty($tahunAktif) || empty($semesterAktif)) {
            return 1;
        }
        
        $tahunParts = explode('/', $tahunAktif);
        $tahunMulai = intval($tahunParts[0] ?? 0);
        $angkatanInt = intval($angkatan);
        $selisihTahun = $tahunMulai - $angkatanInt;
        
        if ($selisihTahun == 0) {
            return (strtolower($semesterAktif) == 'ganjil') ? 1 : 2;
        } elseif ($selisihTahun == 1) {
            return (strtolower($semesterAktif) == 'ganjil') ? 3 : 4;
        } elseif ($selisihTahun == 2) {
            return (strtolower($semesterAktif) == 'ganjil') ? 5 : 6;
        }
        return 1;
    }

    /**
     * Display list of students
     */
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        // Get active period
        $periodeAktif = DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? '';
        $semesterAktif = $periodeAktif->semester ?? '';
        
        // Get all students with search
        $query = Siswa::query();
        
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('nis', 'like', "%$search%")
                  ->orWhere('nisn', 'like', "%$search%");
            });
        }
        
        $perPage = $request->get('per_page', 10);
        $siswaList = $query->orderBy('nama', 'asc')->paginate($perPage)->appends($request->query());
        
        // Get rombel list for dropdowns
        $rombelList = Rombel::select('nama_rombel')
            ->distinct()
            ->orderBy('nama_rombel')
            ->pluck('nama_rombel');
        
        // Get guru BK list
        $guruBKList = GuruBK::where('status', 'Aktif')->orderBy('nama')->get();
        
        // Get list of all Guru + Guru BK for Wali Kelas dropdown
        $guruList = Guru::where('status', 'Aktif')->orderBy('nama')->get(['nama']);
        $guruWaliList = $guruList->pluck('nama')
            ->merge($guruBKList->pluck('nama'))
            ->unique()
            ->sort()
            ->values();
        
        // Get angkatan list
        $angkatanList = range(date('Y') - 3, date('Y') + 1);
        
        return view('admin.siswa.index', compact(
            'admin',
            'siswaList',
            'rombelList',
            'guruBKList',
            'guruWaliList',
            'angkatanList',
            'tahunAktif',
            'semesterAktif'
        ));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $rombelList = Rombel::select('nama_rombel')->distinct()->orderBy('nama_rombel')->pluck('nama_rombel');
        $guruBKList = GuruBK::where('status', 'Aktif')->orderBy('nama')->get();
        $angkatanList = range(date('Y') - 3, date('Y') + 1);
        
        return view('admin.siswa.create', compact('rombelList', 'guruBKList', 'angkatanList'));
    }

    /**
     * Store new student
     */
    public function store(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string|max:20|unique:siswa,nisn',
            'nama' => 'required|string|max:100',
            'password' => 'required|string|min:6',
        ]);

        Siswa::create([
            'nis' => $request->nis,
            'nisn' => $request->nisn,
            'nama' => $request->nama,
            'jk' => $request->jk,
            'agama' => $request->agama,
            'tempat_lahir' => $request->tempat_lahir,
            'tgl_lahir' => $request->tgl_lahir ?: null,
            'nohp_siswa' => $request->nohp_siswa,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'provinsi' => $request->provinsi,
            'kota' => $request->kota,
            'kecamatan' => $request->kecamatan,
            'kelurahan' => $request->kelurahan,
            'nama_bapak' => $request->nama_bapak,
            'pekerjaan_bapak' => $request->pekerjaan_bapak,
            'nohp_bapak' => $request->nohp_bapak,
            'nama_ibu' => $request->nama_ibu,
            'pekerjaan_ibu' => $request->pekerjaan_ibu,
            'nohp_ibu' => $request->nohp_ibu,
            'jml_saudara' => $request->jml_saudara ?: 0,
            'anak_ke' => $request->anak_ke ?: 0,
            'asal_sekolah' => $request->asal_sekolah,
            'nilai_skl' => $request->nilai_skl,
            'angkatan_masuk' => $request->angkatan_masuk,
            'nama_rombel' => $request->nama_rombel,
        ]);

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan!');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $siswa = Siswa::findOrFail($id);
        $rombelList = Rombel::select('nama_rombel')->distinct()->orderBy('nama_rombel')->pluck('nama_rombel');
        $guruBKList = GuruBK::where('status', 'Aktif')->orderBy('nama')->get();
        $angkatanList = range(date('Y') - 3, date('Y') + 1);
        
        return view('admin.siswa.edit', compact('siswa', 'rombelList', 'guruBKList', 'angkatanList'));
    }

    /**
     * Update student
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nisn' => 'required|string|max:20|unique:siswa,nisn,' . $id,
            'nama' => 'required|string|max:100',
        ]);

        $siswa = Siswa::findOrFail($id);
        $oldName = $siswa->nama; // Store old name for cascade update
        $nisnForCascade = $siswa->nisn; // Store NISN for accurate cascade
        
        $data = [
            'nis' => $request->nis,
            'nisn' => $request->nisn,
            'nama' => $request->nama,
            'jk' => $request->jk,
            'agama' => $request->agama,
            'tempat_lahir' => $request->tempat_lahir,
            'tgl_lahir' => $request->tgl_lahir ?: null,
            'nohp_siswa' => $request->nohp_siswa,
            'email' => $request->email,
            'provinsi' => $request->provinsi,
            'kota' => $request->kota,
            'kecamatan' => $request->kecamatan,
            'kelurahan' => $request->kelurahan,
            'nama_bapak' => $request->nama_bapak,
            'pekerjaan_bapak' => $request->pekerjaan_bapak,
            'nohp_bapak' => $request->nohp_bapak,
            'nama_ibu' => $request->nama_ibu,
            'pekerjaan_ibu' => $request->pekerjaan_ibu,
            'nohp_ibu' => $request->nohp_ibu,
            'jml_saudara' => $request->jml_saudara ?: 0,
            'anak_ke' => $request->anak_ke ?: 0,
            'asal_sekolah' => $request->asal_sekolah,
            'nilai_skl' => $request->nilai_skl,
            'cita_cita' => $request->cita_cita,
            'mapel_fav1' => $request->mapel_fav1,
            'mapel_fav2' => $request->mapel_fav2,
            'harapan' => $request->harapan,
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Handle foto upload with compression
        if ($request->hasFile('foto')) {
            // Delete old foto
            if ($siswa->foto && Storage::disk('public')->exists('siswa/' . $siswa->foto)) {
                Storage::disk('public')->delete('siswa/' . $siswa->foto);
            }
            
            $file = $request->file('foto');
            $filename = 'siswa_' . $id . '_' . time() . '.jpg';
            $uploadPath = storage_path('app/public/siswa/' . $filename);
            
            // Ensure directory exists
            if (!file_exists(storage_path('app/public/siswa'))) {
                mkdir(storage_path('app/public/siswa'), 0755, true);
            }
            
            // Compress image to max 250KB
            $this->compressImage($file->getPathname(), $uploadPath, 250);
            
            $data['foto'] = $filename;
        }

        $siswa->update($data);

        // Cascade name update if name changed
        if ($oldName !== $request->nama) {
            NameCascadeService::updateSiswaName($oldName, $request->nama, $nisnForCascade);
        }

        // Redirect back to edit page if only uploading foto
        if ($request->hasFile('foto') && !$request->filled('password') && !$request->has('full_update')) {
            return redirect()->route('admin.siswa.edit', $id)->with('success', 'Foto berhasil diperbarui!');
        }

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui!');
    }

    /**
     * Delete student
     */
    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil dihapus!');
    }

    /**
     * Delete multiple students (AJAX)
     */
    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih']);
        }

        $deleted = Siswa::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "$deleted data siswa berhasil dihapus",
            'deleted' => $deleted
        ]);
    }

    /**
     * Update angkatan (AJAX)
     */
    public function updateAngkatan(Request $request)
    {
        $nisn = $request->input('nisn');
        $angkatan = $request->input('angkatan');

        if (empty($nisn)) {
            return response()->json(['success' => false, 'msg' => 'NISN kosong']);
        }

        $siswa = Siswa::where('nisn', $nisn)->first();
        if (!$siswa) {
            return response()->json(['success' => false, 'msg' => 'Siswa tidak ditemukan']);
        }

        $siswa->update(['angkatan_masuk' => $angkatan ?: null]);

        return response()->json(['success' => true]);
    }

    /**
     * Update rombel per semester (AJAX)
     */
    public function updateRombelSemester(Request $request)
    {
        $nisn = $request->input('nisn');
        $semester = intval($request->input('semester'));
        $namaRombel = $request->input('nama_rombel');

        if (empty($nisn) || $semester < 1 || $semester > 6) {
            return response()->json(['success' => false, 'msg' => 'Data tidak valid']);
        }

        $column = "rombel_semester_$semester";
        $siswa = Siswa::where('nisn', $nisn)->first();
        
        if (!$siswa) {
            return response()->json(['success' => false, 'msg' => 'Siswa tidak ditemukan']);
        }

        $siswa->update([$column => $namaRombel ?: null]);

        return response()->json(['success' => true]);
    }

    /**
     * Update guru BK per semester (AJAX)
     */
    public function updateBKSemester(Request $request)
    {
        $nisn = $request->input('nisn');
        $semester = intval($request->input('semester'));
        $namaGuruBK = $request->input('nama_guru_bk');

        if (empty($nisn) || $semester < 1 || $semester > 6) {
            return response()->json(['success' => false, 'msg' => 'Data tidak valid']);
        }

        $column = "bk_semester_$semester";
        $siswa = Siswa::where('nisn', $nisn)->first();
        
        if (!$siswa) {
            return response()->json(['success' => false, 'msg' => 'Siswa tidak ditemukan']);
        }

        $siswa->update([$column => $namaGuruBK ?: null]);

        return response()->json(['success' => true]);
    }

    /**
     * Update guru wali per semester (AJAX)
     */
    public function updateWaliSemester(Request $request)
    {
        $nisn = $request->input('nisn');
        $semester = intval($request->input('semester'));
        $namaGuruWali = $request->input('nama_guru_wali');

        if (empty($nisn) || $semester < 1 || $semester > 6) {
            return response()->json(['success' => false, 'msg' => 'Data tidak valid']);
        }

        $column = "guru_wali_sem_$semester";
        $siswa = Siswa::where('nisn', $nisn)->first();
        
        if (!$siswa) {
            return response()->json(['success' => false, 'msg' => 'Siswa tidak ditemukan']);
        }

        $siswa->update([$column => $namaGuruWali ?: null]);

        return response()->json(['success' => true]);
    }

    /**
     * Get rombel by semester (AJAX)
     */
    public function getRombelBySemester(Request $request)
    {
        $tahun = $request->input('tahun_ajaran');
        $semester = $request->input('semester');

        if (empty($tahun) || empty($semester)) {
            return response()->json(['error' => 'Data tidak lengkap']);
        }

        $rombelList = Rombel::where('tahun_pelajaran', $tahun)
            ->whereRaw('LOWER(semester) = ?', [strtolower($semester)])
            ->orderBy('nama_rombel')
            ->pluck('nama_rombel');

        return response()->json($rombelList);
    }

    /**
     * Show import form
     */
    public function showImport()
    {
        return view('admin.siswa.import');
    }

    /**
     * Process CSV import
     */
    public function processImport(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('file_csv');
        $path = $file->getRealPath();
        
        $imported = 0;
        $skipped = 0;
        $errors = [];

        if (($handle = fopen($path, 'r')) !== false) {
            $header = fgetcsv($handle, 0, ',');
            
            while (($data = fgetcsv($handle, 0, ',')) !== false) {
                if (count($data) < 3) continue; // Minimal: NISN, NIS, Nama
                
                try {
                    // Check if NISN exists
                    $nisn = trim($data[0] ?? '');
                    if (empty($nisn)) {
                        $skipped++;
                        continue;
                    }

                    $exists = Siswa::where('nisn', $nisn)->exists();
                    if ($exists) {
                        $skipped++;
                        continue;
                    }

                    Siswa::create([
                        'nisn' => $nisn,
                        'nis' => trim($data[1] ?? ''),
                        'nama' => trim($data[2] ?? ''),
                        'jk' => trim($data[3] ?? ''),
                        'agama' => trim($data[4] ?? ''),
                        'tempat_lahir' => trim($data[5] ?? ''),
                        'tgl_lahir' => !empty($data[6]) ? date('Y-m-d', strtotime($data[6])) : null,
                        'password' => Hash::make($nisn), // Default password = NISN
                        'angkatan_masuk' => trim($data[7] ?? date('Y')),
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris: " . ($imported + $skipped + 2) . " - " . $e->getMessage();
                }
            }
            fclose($handle);
        }

        $message = "Import selesai: $imported data berhasil, $skipped data dilewati.";
        if (!empty($errors)) {
            $message .= " (" . count($errors) . " error)";
        }

        return redirect()->route('admin.siswa.index')
            ->with('success', $message);
    }

    /**
     * Download periodic data template (Excel XLSX)
     */
    public function downloadPeriodicTemplate()
    {
        // Get active period
        $periodeAktif = DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? '';
        $semesterAktif = $periodeAktif->semester ?? '';
        
        // Get all active students
        $siswaList = Siswa::where('status_siswa', 'Aktif')
            ->orderBy('nama')
            ->get();
        
        // Create Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Periodik');
        
        // Header row with styling
        $headers = ['Nama', 'NISN', 'Tahun Pelajaran', 'Semester', 'Rombel', 'Guru BK', 'Guru Wali'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
        
        // Data rows
        $row = 2;
        foreach ($siswaList as $siswa) {
            $semesterNumber = $this->calculateActiveSemester($siswa->angkatan_masuk, $tahunAktif, $semesterAktif);
            
            $rombelCol = "rombel_semester_{$semesterNumber}";
            $bkCol = "bk_semester_{$semesterNumber}";
            $waliCol = "guru_wali_sem_{$semesterNumber}";
            
            $sheet->setCellValue('A' . $row, $siswa->nama);
            $sheet->setCellValue('B' . $row, $siswa->nisn);
            $sheet->setCellValue('C' . $row, $tahunAktif);
            $sheet->setCellValue('D' . $row, $semesterNumber);
            $sheet->setCellValue('E' . $row, $siswa->$rombelCol ?? '');
            $sheet->setCellValue('F' . $row, $siswa->$bkCol ?? '');
            $sheet->setCellValue('G' . $row, $siswa->$waliCol ?? '');
            $row++;
        }
        
        // Generate file
        $filename = 'template_data_periodik_' . date('Y-m-d') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        
        // Output to browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Import periodic data (Rombel, Guru BK, Guru Wali) from XLSX
     */
    public function importPeriodicData(Request $request)
    {
        $request->validate([
            'file_periodic' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        $file = $request->file('file_periodic');
        $path = $file->getRealPath();
        
        $updated = 0;
        $skipped = 0;
        $errors = [];

        try {
            $spreadsheet = IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            
            // Skip header row
            array_shift($rows);
            
            foreach ($rows as $index => $data) {
                if (count($data) < 7) {
                    $skipped++;
                    continue;
                }
                
                try {
                    $nisn = trim($data[1] ?? '');
                    $semester = intval(trim($data[3] ?? 0));
                    $rombel = trim($data[4] ?? '');
                    $guruBK = trim($data[5] ?? '');
                    $guruWali = trim($data[6] ?? '');
                    
                    if (empty($nisn) || $semester < 1 || $semester > 6) {
                        $skipped++;
                        continue;
                    }
                    
                    $siswa = Siswa::where('nisn', $nisn)->first();
                    if (!$siswa) {
                        $skipped++;
                        continue;
                    }
                    
                    $updateData = [];
                    
                    if (!empty($rombel)) {
                        $updateData["rombel_semester_{$semester}"] = $rombel;
                    }
                    if (!empty($guruBK)) {
                        $updateData["bk_semester_{$semester}"] = $guruBK;
                    }
                    if (!empty($guruWali)) {
                        $updateData["guru_wali_sem_{$semester}"] = $guruWali;
                    }
                    
                    if (!empty($updateData)) {
                        $siswa->update($updateData);
                        $updated++;
                    } else {
                        $skipped++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.siswa.import')
                ->withErrors(['file_periodic' => 'Gagal membaca file: ' . $e->getMessage()]);
        }

        $message = "Import data periodik selesai: $updated data berhasil diupdate, $skipped data dilewati.";
        if (!empty($errors)) {
            $message .= " (" . count($errors) . " error)";
        }

        return redirect()->route('admin.siswa.import')
            ->with('success', $message);
    }

    /**
     * Export all siswa data to XLSX
     */
    public function exportSiswa()
    {
        $siswaList = Siswa::orderBy('nama', 'asc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa');

        // Define export columns (excluding password, foto fields, timestamps)
        $columns = [
            'nisn' => 'NISN',
            'nis' => 'NIS',
            'nama' => 'Nama',
            'jk' => 'Jenis Kelamin',
            'agama' => 'Agama',
            'tempat_lahir' => 'Tempat Lahir',
            'tgl_lahir' => 'Tanggal Lahir',
            'angkatan_masuk' => 'Angkatan Masuk',
            'nohp_siswa' => 'No HP Siswa',
            'email' => 'Email',
            'provinsi' => 'Provinsi',
            'kota' => 'Kota',
            'kecamatan' => 'Kecamatan',
            'kelurahan' => 'Kelurahan',
            'nama_bapak' => 'Nama Bapak',
            'pekerjaan_bapak' => 'Pekerjaan Bapak',
            'nohp_bapak' => 'No HP Bapak',
            'nama_ibu' => 'Nama Ibu',
            'pekerjaan_ibu' => 'Pekerjaan Ibu',
            'nohp_ibu' => 'No HP Ibu',
            'jml_saudara' => 'Jumlah Saudara',
            'anak_ke' => 'Anak Ke',
            'asal_sekolah' => 'Asal Sekolah',
            'nilai_skl' => 'Nilai SKL',
            'cita_cita' => 'Cita-cita',
            'mapel_fav1' => 'Mapel Favorit 1',
            'mapel_fav2' => 'Mapel Favorit 2',
            'harapan' => 'Harapan',
            'nama_rombel' => 'Nama Rombel',
            'status_siswa' => 'Status Siswa',
            'rombel_semester_1' => 'Rombel Semester 1',
            'rombel_semester_2' => 'Rombel Semester 2',
            'rombel_semester_3' => 'Rombel Semester 3',
            'rombel_semester_4' => 'Rombel Semester 4',
            'rombel_semester_5' => 'Rombel Semester 5',
            'rombel_semester_6' => 'Rombel Semester 6',
            'bk_semester_1' => 'BK Semester 1',
            'bk_semester_2' => 'BK Semester 2',
            'bk_semester_3' => 'BK Semester 3',
            'bk_semester_4' => 'BK Semester 4',
            'bk_semester_5' => 'BK Semester 5',
            'bk_semester_6' => 'BK Semester 6',
            'guru_wali_sem_1' => 'Guru Wali Sem 1',
            'guru_wali_sem_2' => 'Guru Wali Sem 2',
            'guru_wali_sem_3' => 'Guru Wali Sem 3',
            'guru_wali_sem_4' => 'Guru Wali Sem 4',
            'guru_wali_sem_5' => 'Guru Wali Sem 5',
            'guru_wali_sem_6' => 'Guru Wali Sem 6',
        ];

        // Header row
        $colIndex = 1;
        foreach ($columns as $field => $label) {
            $sheet->getCell([$colIndex, 1])->setValue($label);
            $colIndex++;
        }

        // Header styling
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($columns));
        $headerRange = "A1:{$lastCol}1";
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle($headerRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('7C3AED');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Data rows
        $row = 2;
        foreach ($siswaList as $siswa) {
            $colIndex = 1;
            foreach ($columns as $field => $label) {
                $val = $siswa->$field;

                // Format date
                if ($field === 'tgl_lahir' && $val) {
                    $val = $val instanceof \Carbon\Carbon ? $val->format('Y-m-d') : $val;
                }

                // NISN & NIS as text
                if (in_array($field, ['nisn', 'nis', 'nohp_siswa', 'nohp_bapak', 'nohp_ibu'])) {
                    $sheet->getCell([$colIndex, $row])->setValueExplicit((string)$val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                } else {
                    $sheet->getCell([$colIndex, $row])->setValue($val);
                }
                $colIndex++;
            }
            $row++;
        }

        // Borders for data
        if ($row > 2) {
            $dataRange = "A1:{$lastCol}" . ($row - 1);
            $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        }

        // Auto-width
        for ($i = 1; $i <= count($columns); $i++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Freeze header row
        $sheet->freezePane('A2');

        $filename = 'Data_Siswa_' . date('Y-m-d') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Import siswa data from XLSX (upsert: update if NISN exists, insert if new)
     */
    public function importSiswaXlsx(Request $request)
    {
        $request->validate([
            'file_siswa_xlsx' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $file = $request->file('file_siswa_xlsx');
        $path = $file->getRealPath();

        $inserted = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        // Column map: header label => database field
        $columnMap = [
            'NISN' => 'nisn',
            'NIS' => 'nis',
            'Nama' => 'nama',
            'Jenis Kelamin' => 'jk',
            'Agama' => 'agama',
            'Tempat Lahir' => 'tempat_lahir',
            'Tanggal Lahir' => 'tgl_lahir',
            'Angkatan Masuk' => 'angkatan_masuk',
            'No HP Siswa' => 'nohp_siswa',
            'Email' => 'email',
            'Provinsi' => 'provinsi',
            'Kota' => 'kota',
            'Kecamatan' => 'kecamatan',
            'Kelurahan' => 'kelurahan',
            'Nama Bapak' => 'nama_bapak',
            'Pekerjaan Bapak' => 'pekerjaan_bapak',
            'No HP Bapak' => 'nohp_bapak',
            'Nama Ibu' => 'nama_ibu',
            'Pekerjaan Ibu' => 'pekerjaan_ibu',
            'No HP Ibu' => 'nohp_ibu',
            'Jumlah Saudara' => 'jml_saudara',
            'Anak Ke' => 'anak_ke',
            'Asal Sekolah' => 'asal_sekolah',
            'Nilai SKL' => 'nilai_skl',
            'Cita-cita' => 'cita_cita',
            'Mapel Favorit 1' => 'mapel_fav1',
            'Mapel Favorit 2' => 'mapel_fav2',
            'Harapan' => 'harapan',
            'Nama Rombel' => 'nama_rombel',
            'Status Siswa' => 'status_siswa',
            'Rombel Semester 1' => 'rombel_semester_1',
            'Rombel Semester 2' => 'rombel_semester_2',
            'Rombel Semester 3' => 'rombel_semester_3',
            'Rombel Semester 4' => 'rombel_semester_4',
            'Rombel Semester 5' => 'rombel_semester_5',
            'Rombel Semester 6' => 'rombel_semester_6',
            'BK Semester 1' => 'bk_semester_1',
            'BK Semester 2' => 'bk_semester_2',
            'BK Semester 3' => 'bk_semester_3',
            'BK Semester 4' => 'bk_semester_4',
            'BK Semester 5' => 'bk_semester_5',
            'BK Semester 6' => 'bk_semester_6',
            'Guru Wali Sem 1' => 'guru_wali_sem_1',
            'Guru Wali Sem 2' => 'guru_wali_sem_2',
            'Guru Wali Sem 3' => 'guru_wali_sem_3',
            'Guru Wali Sem 4' => 'guru_wali_sem_4',
            'Guru Wali Sem 5' => 'guru_wali_sem_5',
            'Guru Wali Sem 6' => 'guru_wali_sem_6',
        ];

        try {
            $spreadsheet = IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (empty($rows)) {
                return redirect()->route('admin.siswa.import')
                    ->withErrors(['file_siswa_xlsx' => 'File kosong']);
            }

            // Parse header row to determine column positions
            $headerRow = array_shift($rows);
            $fieldPositions = []; // position => db field
            foreach ($headerRow as $pos => $headerLabel) {
                $label = trim($headerLabel ?? '');
                if (isset($columnMap[$label])) {
                    $fieldPositions[$pos] = $columnMap[$label];
                }
            }

            if (empty($fieldPositions)) {
                return redirect()->route('admin.siswa.import')
                    ->withErrors(['file_siswa_xlsx' => 'Header tidak dikenali. Gunakan file hasil export sebagai template.']);
            }

            // Find NISN column position
            $nisnPos = array_search('nisn', $fieldPositions);
            if ($nisnPos === false) {
                return redirect()->route('admin.siswa.import')
                    ->withErrors(['file_siswa_xlsx' => 'Kolom NISN tidak ditemukan di header.']);
            }

            foreach ($rows as $index => $rowData) {
                try {
                    $nisn = trim($rowData[$nisnPos] ?? '');
                    if (empty($nisn)) {
                        $skipped++;
                        continue;
                    }

                    // Build data array from row
                    $data = [];
                    foreach ($fieldPositions as $pos => $field) {
                        if ($field === 'nisn') continue; // NISN handled separately
                        $val = trim($rowData[$pos] ?? '');

                        // Handle date field
                        if ($field === 'tgl_lahir') {
                            if (!empty($val)) {
                                try {
                                    $val = date('Y-m-d', strtotime($val));
                                    if ($val === '1970-01-01') $val = null;
                                } catch (\Exception $e) {
                                    $val = null;
                                }
                            } else {
                                $val = null;
                            }
                        }

                        // Handle integer fields
                        if (in_array($field, ['angkatan_masuk', 'jml_saudara', 'anak_ke'])) {
                            $val = !empty($val) ? intval($val) : null;
                        }

                        // Handle decimal fields
                        if ($field === 'nilai_skl') {
                            $val = !empty($val) ? floatval($val) : null;
                        }

                        // Empty string to null for non-required fields
                        if ($val === '' && !in_array($field, ['nama'])) {
                            $val = null;
                        }

                        $data[$field] = $val;
                    }

                    // Check if exists
                    $existingSiswa = Siswa::where('nisn', $nisn)->first();

                    if ($existingSiswa) {
                        // Update existing (don't overwrite password)
                        $existingSiswa->update($data);
                        $updated++;
                    } else {
                        // Insert new
                        $data['nisn'] = $nisn;
                        $data['password'] = Hash::make($nisn); // Default password = NISN
                        if (empty($data['status_siswa'])) {
                            $data['status_siswa'] = 'Aktif';
                        }
                        Siswa::create($data);
                        $inserted++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.siswa.import')
                ->withErrors(['file_siswa_xlsx' => 'Gagal membaca file: ' . $e->getMessage()]);
        }

        $message = "Import selesai: $inserted data baru, $updated data diupdate, $skipped dilewati.";
        if (!empty($errors)) {
            $message .= " (" . count($errors) . " error)";
        }

        return redirect()->route('admin.siswa.import')
            ->with('success', $message);
    }

    /**
     * Upload photo
     */
    public function uploadPhoto(Request $request, $id)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $siswa = Siswa::findOrFail($id);

        // Delete old photo
        if ($siswa->foto && Storage::disk('public')->exists('siswa/' . $siswa->foto)) {
            Storage::disk('public')->delete('siswa/' . $siswa->foto);
        }

        // Store new photo
        $filename = 'siswa_' . $id . '_' . time() . '.' . $request->file('foto')->extension();
        $request->file('foto')->storeAs('siswa', $filename, 'public');

        $siswa->update(['foto' => $filename]);

        return back()->with('success', 'Foto berhasil diupload!');
    }

    /**
     * Keluarkan Siswa (AJAX) - Move to siswa_keluar table then delete
     */
    public function keluarkan(Request $request)
    {
        $siswaId = intval($request->input('siswa_id', 0));
        $tanggalKeluar = $request->input('tanggal_keluar', '');
        $jenisKeluar = $request->input('jenis_keluar', '');
        $keterangan = $request->input('keterangan', '');

        if (empty($siswaId) || empty($tanggalKeluar) || empty($jenisKeluar)) {
            return response()->json(['success' => false, 'message' => 'Data tidak lengkap']);
        }

        // Validate jenis_keluar
        $validJenis = ['Mutasi', 'Dikeluarkan', 'Lulus'];
        if (!in_array($jenisKeluar, $validJenis)) {
            return response()->json(['success' => false, 'message' => 'Jenis keluar tidak valid']);
        }

        $siswa = Siswa::find($siswaId);
        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Siswa tidak ditemukan']);
        }

        DB::beginTransaction();
        try {
            // Handle null/invalid date
            $tglLahir = ($siswa->tgl_lahir === '0000-00-00' || empty($siswa->tgl_lahir)) ? null : $siswa->tgl_lahir;

            // Insert into siswa_keluar
            SiswaKeluar::create([
                'siswa_id' => $siswa->id,
                'nisn' => $siswa->nisn,
                'nis' => $siswa->nis,
                'nama' => $siswa->nama,
                'jk' => $siswa->jk,
                'agama' => $siswa->agama,
                'tempat_lahir' => $siswa->tempat_lahir,
                'tgl_lahir' => $tglLahir,
                'nohp_siswa' => $siswa->nohp_siswa,
                'email' => $siswa->email,
                'provinsi' => $siswa->provinsi,
                'kota' => $siswa->kota,
                'kecamatan' => $siswa->kecamatan,
                'kelurahan' => $siswa->kelurahan,
                'nama_bapak' => $siswa->nama_bapak,
                'pekerjaan_bapak' => $siswa->pekerjaan_bapak,
                'nohp_bapak' => $siswa->nohp_bapak,
                'nama_ibu' => $siswa->nama_ibu,
                'pekerjaan_ibu' => $siswa->pekerjaan_ibu,
                'nohp_ibu' => $siswa->nohp_ibu,
                'jml_saudara' => $siswa->jml_saudara,
                'anak_ke' => $siswa->anak_ke,
                'asal_sekolah' => $siswa->asal_sekolah,
                'nilai_skl' => $siswa->nilai_skl,
                'cita_cita' => $siswa->cita_cita,
                'mapel_fav1' => $siswa->mapel_fav1,
                'mapel_fav2' => $siswa->mapel_fav2,
                'harapan' => $siswa->harapan,
                'angkatan_masuk' => $siswa->angkatan_masuk,
                'rombel_semester_1' => $siswa->rombel_semester_1,
                'rombel_semester_2' => $siswa->rombel_semester_2,
                'rombel_semester_3' => $siswa->rombel_semester_3,
                'rombel_semester_4' => $siswa->rombel_semester_4,
                'rombel_semester_5' => $siswa->rombel_semester_5,
                'rombel_semester_6' => $siswa->rombel_semester_6,
                'bk_semester_1' => $siswa->bk_semester_1,
                'bk_semester_2' => $siswa->bk_semester_2,
                'bk_semester_3' => $siswa->bk_semester_3,
                'bk_semester_4' => $siswa->bk_semester_4,
                'bk_semester_5' => $siswa->bk_semester_5,
                'bk_semester_6' => $siswa->bk_semester_6,
                'tanggal_keluar' => $tanggalKeluar,
                'jenis_keluar' => $jenisKeluar,
                'keterangan' => $keterangan,
            ]);

            // Delete from siswa table
            $siswa->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Siswa ' . $siswa->nama . ' berhasil dikeluarkan dengan status ' . $jenisKeluar
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    /**
     * Siswa Keluar - List page
     */
    public function siswaKeluarIndex(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        // Filters
        $filterJenis = $request->get('jenis', '');
        $filterTahun = $request->get('tahun', '');
        $search = $request->get('search', '');

        $query = SiswaKeluar::query();

        if (!empty($filterJenis)) {
            $query->where('jenis_keluar', $filterJenis);
        }
        if (!empty($filterTahun)) {
            $query->whereYear('tanggal_keluar', $filterTahun);
        }
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $siswaKeluarList = $query->orderBy('tanggal_keluar', 'desc')
            ->orderBy('nama', 'asc')
            ->get();

        // Stats
        $stats = [
            'Mutasi' => SiswaKeluar::where('jenis_keluar', 'Mutasi')->count(),
            'Dikeluarkan' => SiswaKeluar::where('jenis_keluar', 'Dikeluarkan')->count(),
            'Lulus' => SiswaKeluar::where('jenis_keluar', 'Lulus')->count(),
        ];
        $totalKeluar = array_sum($stats);

        // Tahun list for filter
        $tahunList = SiswaKeluar::selectRaw('DISTINCT YEAR(tanggal_keluar) as tahun')
            ->whereNotNull('tanggal_keluar')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        // Rombel list for restore
        $rombelList = Rombel::select('nama_rombel')
            ->distinct()
            ->orderBy('nama_rombel')
            ->pluck('nama_rombel');

        return view('admin.siswa.keluar', compact(
            'admin',
            'siswaKeluarList',
            'stats',
            'totalKeluar',
            'tahunList',
            'rombelList',
            'filterJenis',
            'filterTahun',
            'search'
        ));
    }

    /**
     * Kembalikan Siswa (AJAX) - Restore from siswa_keluar back to siswa
     */
    public function kembalikanSiswa(Request $request)
    {
        $siswaKeluarId = intval($request->input('siswa_keluar_id', 0));
        $rombelKembali = $request->input('rombel_kembali', '');

        if (empty($siswaKeluarId)) {
            return response()->json(['success' => false, 'message' => 'ID siswa tidak valid']);
        }

        $sk = SiswaKeluar::find($siswaKeluarId);
        if (!$sk) {
            return response()->json(['success' => false, 'message' => 'Data siswa tidak ditemukan']);
        }

        DB::beginTransaction();
        try {
            // Handle invalid date
            $tglLahir = ($sk->tgl_lahir === '0000-00-00' || empty($sk->tgl_lahir)) ? null : $sk->tgl_lahir;

            // Rombel semester 1 override if rombel_kembali provided
            $rombel1 = !empty($rombelKembali) ? $rombelKembali : $sk->rombel_semester_1;

            // Insert back to siswa
            Siswa::create([
                'nisn' => $sk->nisn,
                'nis' => $sk->nis,
                'nama' => $sk->nama,
                'jk' => $sk->jk,
                'agama' => $sk->agama,
                'tempat_lahir' => $sk->tempat_lahir,
                'tgl_lahir' => $tglLahir,
                'nohp_siswa' => $sk->nohp_siswa,
                'email' => $sk->email,
                'provinsi' => $sk->provinsi,
                'kota' => $sk->kota,
                'kecamatan' => $sk->kecamatan,
                'kelurahan' => $sk->kelurahan,
                'nama_bapak' => $sk->nama_bapak,
                'pekerjaan_bapak' => $sk->pekerjaan_bapak,
                'nohp_bapak' => $sk->nohp_bapak,
                'nama_ibu' => $sk->nama_ibu,
                'pekerjaan_ibu' => $sk->pekerjaan_ibu,
                'nohp_ibu' => $sk->nohp_ibu,
                'jml_saudara' => $sk->jml_saudara,
                'anak_ke' => $sk->anak_ke,
                'asal_sekolah' => $sk->asal_sekolah,
                'nilai_skl' => $sk->nilai_skl,
                'cita_cita' => $sk->cita_cita,
                'mapel_fav1' => $sk->mapel_fav1,
                'mapel_fav2' => $sk->mapel_fav2,
                'harapan' => $sk->harapan,
                'angkatan_masuk' => $sk->angkatan_masuk,
                'password' => Hash::make($sk->nisn), // Default password = NISN
                'rombel_semester_1' => $rombel1,
                'rombel_semester_2' => $sk->rombel_semester_2,
                'rombel_semester_3' => $sk->rombel_semester_3,
                'rombel_semester_4' => $sk->rombel_semester_4,
                'rombel_semester_5' => $sk->rombel_semester_5,
                'rombel_semester_6' => $sk->rombel_semester_6,
                'bk_semester_1' => $sk->bk_semester_1,
                'bk_semester_2' => $sk->bk_semester_2,
                'bk_semester_3' => $sk->bk_semester_3,
                'bk_semester_4' => $sk->bk_semester_4,
                'bk_semester_5' => $sk->bk_semester_5,
                'bk_semester_6' => $sk->bk_semester_6,
            ]);

            // Delete from siswa_keluar
            $sk->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Siswa ' . $sk->nama . ' berhasil dikembalikan ke data siswa aktif'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    /**
     * Impersonate a student (Login as Siswa)
     */
    public function impersonate($nisn)
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin) {
            return redirect()->route('login')->with('error', 'Unauthorized');
        }

        $siswa = Siswa::where('nisn', $nisn)->first();
        
        if (!$siswa) {
            return back()->with('error', 'Siswa tidak ditemukan');
        }

        // Store admin session for returning later
        session([
            'impersonating' => true,
            'original_admin_id' => $admin->id,
            'original_admin_username' => $admin->username,
        ]);

        // Login as siswa
        Auth::guard('siswa')->login($siswa);

        return redirect()->route('siswa.dashboard')->with('success', 'Anda sekarang login sebagai ' . $siswa->nama);
    }

    /**
     * Compress image to target size (max KB)
     */
    private function compressImage($source, $destination, $maxSizeKB = 250)
    {
        $maxSizeBytes = $maxSizeKB * 1024;
        
        $info = getimagesize($source);
        if (!$info) {
            copy($source, $destination);
            return false;
        }
        
        $image = null;
        switch ($info['mime']) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($source);
                break;
            case 'image/png':
                $image = imagecreatefrompng($source);
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($source);
                break;
            default:
                copy($source, $destination);
                return false;
        }
        
        if (!$image) {
            copy($source, $destination);
            return false;
        }
        
        // Resize if width > 800px
        $width = imagesx($image);
        $height = imagesy($image);
        $maxWidth = 800;
        
        if ($width > $maxWidth) {
            $newHeight = intval(($maxWidth / $width) * $height);
            $newImage = imagecreatetruecolor($maxWidth, $newHeight);
            
            if ($info['mime'] === 'image/png' || $info['mime'] === 'image/gif') {
                imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
            }
            
            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $maxWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $newImage;
        }
        
        // Compress with decreasing quality
        $quality = 85;
        $tempPath = sys_get_temp_dir() . '/compressed_siswa_' . time() . '.jpg';
        
        do {
            imagejpeg($image, $tempPath, $quality);
            $currentSize = filesize($tempPath);
            
            if ($currentSize <= $maxSizeBytes || $quality <= 20) {
                break;
            }
            
            $quality -= 10;
        } while ($quality > 20);
        
        if (copy($tempPath, $destination)) {
            imagedestroy($image);
            @unlink($tempPath);
            return true;
        }
        
        imagedestroy($image);
        @unlink($tempPath);
        return false;
    }
}
