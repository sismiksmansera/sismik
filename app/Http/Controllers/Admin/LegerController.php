<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DataPeriodik;
use App\Models\Rombel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LegerController extends Controller
{
    /**
     * Display leger page with filters
     */
    public function index()
    {
        // Get unique tahun pelajaran
        $tahunList = DB::table('data_periodik')
            ->select('tahun_pelajaran')
            ->distinct()
            ->orderBy('tahun_pelajaran', 'desc')
            ->pluck('tahun_pelajaran');
        
        return view('admin.leger.index', compact('tahunList'));
    }
    
    /**
     * Get semesters for selected tahun pelajaran (AJAX)
     */
    public function getSemesters(Request $request)
    {
        $tahun = $request->tahun_pelajaran;
        $semesters = DB::table('data_periodik')
            ->where('tahun_pelajaran', $tahun)
            ->pluck('semester')
            ->unique()
            ->values();
        
        return response()->json($semesters);
    }
    
    /**
     * Get rombels for selected tahun and semester (AJAX)
     */
    public function getRombels(Request $request)
    {
        $tahun = $request->tahun_pelajaran;
        $semester = $request->semester;
        
        $rombels = Rombel::where('tahun_pelajaran', $tahun)
            ->where('semester', $semester)
            ->select('id', 'nama_rombel')
            ->get()
            ->sortBy('nama_rombel', SORT_NATURAL)
            ->values();
        return response()->json($rombels);
    }
    
    /**
     * Get leger data for display (AJAX)
     */
    public function getLegerData(Request $request)
    {
        try {
            $rombelId = $request->query('rombel_id');
            $tahun = $request->query('tahun');
            $semester = $request->query('semester');
            
            // Query using rombel_id directly (nama_rombel and ranking columns don't exist)
            $katrolData = DB::table('katrol_nilai_leger')
                ->where('rombel_id', $rombelId)
                ->where('tahun_pelajaran', $tahun)
                ->where('semester', $semester)
                ->orderBy('nama_siswa', 'asc') // Sort alphabetically by student name
                ->get();
            
            if ($katrolData->isEmpty()) {
                return response()->json(['students' => [], 'mapels' => []]);
            }
            
            $allMapelColumns = [
                'bahasa_indonesia', 'bahasa_inggris', 'bahasa_inggris_lanjut', 'bahasa_lampung',
                'biologi', 'ekonomi', 'fisika', 'geografi', 'informatika', 'kimia', 'kka',
                'matematika', 'matematika_lanjut', 'pendidikan_agama_buddha', 'pendidikan_agama_hindu',
                'pendidikan_agama_islam', 'pendidikan_agama_katholik', 'pendidikan_agama_kristen',
                'pendidikan_kewarganegaraan', 'pjok', 'prakarya_dan_kewirausahaan',
                'sejarah', 'seni_budaya', 'sosiologi', 'ipa', 'ips'
            ];
            
            $activeMapels = [];
            foreach ($allMapelColumns as $col) {
                foreach ($katrolData as $row) {
                    if (property_exists($row, $col) && $row->$col !== null && $row->$col !== '') {
                        $activeMapels[] = $col;
                        break;
                    }
                }
            }
            
            $mapelDisplayNames = [];
            foreach ($activeMapels as $col) {
                $mapelDisplayNames[] = ucwords(str_replace('_', ' ', $col));
            }
            
            
            // Format student data with calculations
            $students = [];
            foreach ($katrolData as $row) {
                $nilai = [];
                $totalNilai = 0;
                $countNilai = 0;
                
                foreach ($activeMapels as $col) {
                    $displayName = ucwords(str_replace('_', ' ', $col));
                    $nilaiVal = property_exists($row, $col) ? ($row->$col ?? '-') : '-';
                    $nilai[$displayName] = $nilaiVal;
                    
                    // Calculate total and count for average (exclude IPA/IPS averages)
                    if ($nilaiVal !== '-' && $nilaiVal !== null && is_numeric($nilaiVal) && !in_array($col, ['ipa', 'ips'])) {
                        $totalNilai += floatval($nilaiVal);
                        $countNilai++;
                    }
                }
                
                // Calculate rata-rata
                $rataRata = $countNilai > 0 ? $totalNilai / $countNilai : 0;
                
                $students[] = [
                    'nisn' => property_exists($row, 'nisn') ? $row->nisn : '-',
                    'nama_siswa' => property_exists($row, 'nama_siswa') ? $row->nama_siswa : 'Unknown',
                    'nilai' => $nilai,
                    'jumlah' => $totalNilai, // Total sum
                    'rata_rata' => $rataRata, // Average for sorting
                    'rata_rata_display' => number_format($rataRata, 2), // Formatted for display
                    'ranking' => 0 // Will be assigned after sorting
                ];
            }
            
            // Sort by rata-rata descending to assign ranking
            usort($students, function($a, $b) {
                return $b['rata_rata'] <=> $a['rata_rata'];
            });
            
            // Assign ranking
            $currentRank = 1;
            $previousAverage = null;
            $sameRankCount = 0;
            
            foreach ($students as $index => &$student) {
                if ($previousAverage !== null && $student['rata_rata'] < $previousAverage) {
                    $currentRank += $sameRankCount;
                    $sameRankCount = 1;
                } else {
                    $sameRankCount++;
                }
                
                $student['ranking'] = $currentRank;
                $previousAverage = $student['rata_rata'];
            }
            unset($student); // Break reference
            
            // Re-sort alphabetically by name for display
            usort($students, function($a, $b) {
                return strcmp($a['nama_siswa'], $b['nama_siswa']);
            });

            
            return response()->json(['students' => $students, 'mapels' => $mapelDisplayNames]);
        } catch (\Exception $e) {
            \Log::error('Leger Data Error: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Debug endpoint - check raw data
     */
    public function debugLegerData(Request $request)
    {
        $rombelId = $request->query('rombel_id');
        $tahun = $request->query('tahun');
        $semester = $request->query('semester');
        
        $rombel = \App\Models\Rombel::find($rombelId);
        
        $rawData = DB::table('katrol_nilai_leger')
            ->where('nama_rombel', $rombel->nama_rombel)
            ->where('tahun_pelajaran', $tahun)
            ->where('semester', $semester)
            ->get();
        
        return response()->json([
            'rombel_id' => $rombelId,
            'rombel_name' => $rombel->nama_rombel ?? 'NOT FOUND',
            'tahun' => $tahun,
            'semester' => $semester,
            'query_result_count' => $rawData->count(),
            'raw_data' => $rawData->take(2),
            'columns' => $rawData->isNotEmpty() ? array_keys((array)$rawData->first()) : []
        ]);
    }
    
    /**
     * Print leger view
     */
    public function printLeger(Request $request)
    {
        $rombelId = $request->query('rombel_id');
        $tahun = $request->query('tahun');
        $semester = $request->query('semester');
        
        // Get rombel info
        $rombel = \App\Models\Rombel::find($rombelId);
        if (!$rombel) {
            abort(404, 'Rombel tidak ditemukan');
        }
        
        // Get leger data (reuse same logic as getLegerData)
        $katrolData = DB::table('katrol_nilai_leger')
            ->where('rombel_id', $rombelId)
            ->where('tahun_pelajaran', $tahun)
            ->where('semester', $semester)
            ->orderBy('nama_siswa', 'asc') // Sort alphabetically by student name
            ->get();
        
        // Same mapel detection logic
        $allMapelColumns = [
            'bahasa_indonesia', 'bahasa_inggris', 'bahasa_inggris_lanjut', 'bahasa_lampung',
            'biologi', 'ekonomi', 'fisika', 'geografi', 'informatika', 'kimia', 'kka',
            'matematika', 'matematika_lanjut', 'pendidikan_agama_buddha', 'pendidikan_agama_hindu',
            'pendidikan_agama_islam', 'pendidikan_agama_katholik', 'pendidikan_agama_kristen',
            'pendidikan_kewarganegaraan', 'pjok', 'prakarya_dan_kewirausahaan',
            'sejarah', 'seni_budaya', 'sosiologi', 'ipa', 'ips'
        ];
        
        $activeMapels = [];
        foreach ($allMapelColumns as $col) {
            foreach ($katrolData as $row) {
                if (property_exists($row, $col) && $row->$col !== null && $row->$col !== '') {
                    $activeMapels[] = $col;
                    break;
                }
            }
        }
        
        $mapelDisplayNames = [];
        foreach ($activeMapels as $col) {
            $mapelDisplayNames[] = ucwords(str_replace('_', ' ', $col));
        }
        
        // Format student data with calculations
        $students = [];
        foreach ($katrolData as $row) {
            $nilai = [];
            $totalNilai = 0;
            $countNilai = 0;
            
            foreach ($activeMapels as $col) {
                $displayName = ucwords(str_replace('_', ' ', $col));
                $nilaiVal = property_exists($row, $col) ? ($row->$col ?? '-') : '-';
                $nilai[$displayName] = $nilaiVal;
                
                if ($nilaiVal !== '-' && $nilaiVal !== null && is_numeric($nilaiVal) && !in_array($col, ['ipa', 'ips'])) {
                    $totalNilai += floatval($nilaiVal);
                    $countNilai++;
                }
            }
            
            $rataRata = $countNilai > 0 ? $totalNilai / $countNilai : 0;
            
            $students[] = [
                'nisn' => property_exists($row, 'nisn') ? $row->nisn : '-',
                'nama_siswa' => property_exists($row, 'nama_siswa') ? $row->nama_siswa : 'Unknown',
                'nilai' => $nilai,
                'jumlah' => $totalNilai,
                'rata_rata' => $rataRata,
                'rata_rata_display' => number_format($rataRata, 2),
                'ranking' => 0
            ];
        }
        
        // Sort by rata-rata descending and assign ranking
        usort($students, function($a, $b) {
            return $b['rata_rata'] <=> $a['rata_rata'];
        });
        
        $currentRank = 1;
        $previousAverage = null;
        $sameRankCount = 0;
        
        foreach ($students as $index => &$student) {
            if ($previousAverage !== null && $student['rata_rata'] < $previousAverage) {
                $currentRank += $sameRankCount;
                $sameRankCount = 1;
            } else {
                $sameRankCount++;
            }
            
            $student['ranking'] = $currentRank;
            $previousAverage = $student['rata_rata'];
        }
        unset($student);
        
        // Re-sort alphabetically by name for display
        usort($students, function($a, $b) {
            return strcmp($a['nama_siswa'], $b['nama_siswa']);
        });
        
        return view('admin.leger.print-leger', [
            'rombelNama' => $rombel->nama_rombel,
            'tahun' => $tahun,
            'semester' => $semester,
            'mapels' => $mapelDisplayNames,
            'students' => $students
        ]);
    }
    
    /**
     * Export leger to XLSX
     */
    public function exportLeger(Request $request)
    {
        $rombelId = $request->query('rombel_id');
        $tahun = $request->query('tahun');
        $semester = $request->query('semester');
        
        $rombel = Rombel::find($rombelId);
        if (!$rombel) {
            abort(404, 'Rombel tidak ditemukan');
        }
        
        // Get data (same logic as getLegerData)
        $katrolData = DB::table('katrol_nilai_leger')
            ->where('rombel_id', $rombelId)
            ->where('tahun_pelajaran', $tahun)
            ->where('semester', $semester)
            ->orderBy('nama_siswa', 'asc')
            ->get();
        
        $allMapelColumns = [
            'bahasa_indonesia', 'bahasa_inggris', 'bahasa_inggris_lanjut', 'bahasa_lampung',
            'biologi', 'ekonomi', 'fisika', 'geografi', 'informatika', 'kimia', 'kka',
            'matematika', 'matematika_lanjut', 'pendidikan_agama_buddha', 'pendidikan_agama_hindu',
            'pendidikan_agama_islam', 'pendidikan_agama_katholik', 'pendidikan_agama_kristen',
            'pendidikan_kewarganegaraan', 'pjok', 'prakarya_dan_kewirausahaan',
            'sejarah', 'seni_budaya', 'sosiologi', 'ipa', 'ips'
        ];
        
        $activeMapels = [];
        foreach ($allMapelColumns as $col) {
            foreach ($katrolData as $row) {
                if (property_exists($row, $col) && $row->$col !== null && $row->$col !== '') {
                    $activeMapels[] = $col;
                    break;
                }
            }
        }
        
        // Build students with calculations
        $students = [];
        foreach ($katrolData as $row) {
            $nilai = [];
            $totalNilai = 0;
            $countNilai = 0;
            
            foreach ($activeMapels as $col) {
                $nilaiVal = property_exists($row, $col) ? ($row->$col ?? null) : null;
                $nilai[$col] = $nilaiVal;
                
                if ($nilaiVal !== null && is_numeric($nilaiVal) && !in_array($col, ['ipa', 'ips'])) {
                    $totalNilai += floatval($nilaiVal);
                    $countNilai++;
                }
            }
            
            $rataRata = $countNilai > 0 ? $totalNilai / $countNilai : 0;
            
            $students[] = [
                'nisn' => property_exists($row, 'nisn') ? $row->nisn : '-',
                'nama_siswa' => property_exists($row, 'nama_siswa') ? $row->nama_siswa : 'Unknown',
                'nilai' => $nilai,
                'jumlah' => $totalNilai,
                'rata_rata' => $rataRata,
                'ranking' => 0
            ];
        }
        
        // Assign ranking
        usort($students, function($a, $b) { return $b['rata_rata'] <=> $a['rata_rata']; });
        $rank = 1; $prevAvg = null; $sameCount = 0;
        foreach ($students as &$s) {
            if ($prevAvg !== null && $s['rata_rata'] < $prevAvg) { $rank += $sameCount; $sameCount = 1; } else { $sameCount++; }
            $s['ranking'] = $rank;
            $prevAvg = $s['rata_rata'];
        }
        unset($s);
        
        // Re-sort by name
        usort($students, function($a, $b) { return strcmp($a['nama_siswa'], $b['nama_siswa']); });
        
        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Leger Nilai');
        
        // Title rows
        $lastColIndex = count($activeMapels) + 6; // No, NISN, Nama, mapels..., Jumlah, Rata-rata, Ranking
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColIndex);
        
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', 'LEGER NILAI SISWA (KATROL)');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->setCellValue('A2', "Kelas: {$rombel->nama_rombel} | Tahun Pelajaran: {$tahun} | Semester: {$semester}");
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Header row
        $headerRow = 4;
        $col = 1;
        $headers = ['No', 'NISN', 'Nama Siswa'];
        foreach ($headers as $h) {
            $sheet->getCell([$col, $headerRow])->setValue($h);
            $col++;
        }
        foreach ($activeMapels as $mapelCol) {
            $sheet->getCell([$col, $headerRow])->setValue(ucwords(str_replace('_', ' ', $mapelCol)));
            $col++;
        }
        $sheet->getCell([$col, $headerRow])->setValue('Jumlah'); $col++;
        $sheet->getCell([$col, $headerRow])->setValue('Rata-rata'); $col++;
        $sheet->getCell([$col, $headerRow])->setValue('Ranking');
        
        // Header styling
        $headerRange = "A{$headerRow}:{$lastCol}{$headerRow}";
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DC2626');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Data rows
        $dataRow = 5;
        foreach ($students as $index => $student) {
            $col = 1;
            $sheet->getCell([$col++, $dataRow])->setValue($index + 1);
            $sheet->getCell([$col, $dataRow])->setValueExplicit($student['nisn'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $col++;
            $sheet->getCell([$col++, $dataRow])->setValue($student['nama_siswa']);
            
            foreach ($activeMapels as $mapelCol) {
                $val = $student['nilai'][$mapelCol] ?? '-';
                if (is_numeric($val)) {
                    $sheet->getCell([$col, $dataRow])->setValue(floatval($val));
                    // Color code
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                    if ($val >= 85) {
                        $sheet->getStyle("{$colLetter}{$dataRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DCFCE7');
                    } elseif ($val >= 75) {
                        $sheet->getStyle("{$colLetter}{$dataRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DBEAFE');
                    } elseif ($val >= 65) {
                        $sheet->getStyle("{$colLetter}{$dataRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEF9C3');
                    } else {
                        $sheet->getStyle("{$colLetter}{$dataRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEE2E2');
                    }
                } else {
                    $sheet->getCell([$col, $dataRow])->setValue('-');
                }
                $col++;
            }
            
            // Jumlah
            $sheet->getCell([$col, $dataRow])->setValue($student['jumlah']);
            $jCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->getStyle("{$jCol}{$dataRow}")->getFont()->setBold(true);
            $sheet->getStyle("{$jCol}{$dataRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEF3C7');
            $col++;
            
            // Rata-rata
            $sheet->getCell([$col, $dataRow])->setValue(round($student['rata_rata'], 2));
            $rCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->getStyle("{$rCol}{$dataRow}")->getFont()->setBold(true);
            $sheet->getStyle("{$rCol}{$dataRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEF2F2');
            $col++;
            
            // Ranking
            $sheet->getCell([$col, $dataRow])->setValue($student['ranking']);
            $rkCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->getStyle("{$rkCol}{$dataRow}")->getFont()->setBold(true);
            $sheet->getStyle("{$rkCol}{$dataRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F3E8FF');
            
            $dataRow++;
        }
        
        // Borders for data
        $dataRange = "A{$headerRow}:{$lastCol}" . ($dataRow - 1);
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($dataRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Left-align nama column
        $sheet->getStyle('C5:C' . ($dataRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        
        // Auto-width columns
        for ($i = 1; $i <= $lastColIndex; $i++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }
        
        // Output
        $safeRombel = preg_replace('/[\/\\\\:*?"<>|]/', '_', $rombel->nama_rombel);
        $safeRombel = str_replace(' ', '_', $safeRombel);
        $safeTahun = str_replace('/', '-', $tahun);
        $filename = "Leger_{$safeRombel}_{$safeTahun}_Smt{$semester}.xlsx";
        
        $writer = new Xlsx($spreadsheet);
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
    /**
     * Print Leger Nilai (Original grades)
     */
    public function printNilai(Request $request)
    {
        $rombelId = $request->query('rombel_id');
        $tahunPelajaran = $request->query('tahun');
        $semester = $request->query('semester');
        
        if (!$rombelId || !$tahunPelajaran || !$semester) {
            return response('<script>alert("Parameter tidak lengkap!"); window.close();</script>');
        }
        
        // Get rombel data
        $rombel = Rombel::find($rombelId);
        if (!$rombel) {
            return response('<script>alert("Data rombel tidak ditemukan!"); window.close();</script>');
        }
        
        // Get wali kelas NIP
        $namaWaliKelas = $rombel->wali_kelas ?? '';
        $nipWaliKelas = '';
        if ($namaWaliKelas) {
            $guru = DB::table('guru')->where('nama', $namaWaliKelas)->first();
            $nipWaliKelas = $guru->nip ?? '';
        }
        
        // Get active period & kepala sekolah
        $periodeAktif = DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? $tahunPelajaran;
        $semesterAktif = $periodeAktif->semester ?? $semester;
        $namaKepala = $periodeAktif->nama_kepala ?? '';
        $nipKepala = $periodeAktif->nip_kepala ?? '';
        
        // Build student query based on semester
        $tahunAjaran = explode('/', $tahunPelajaran);
        $tahunAwal = intval($tahunAjaran[0]);
        $rombelNama = $rombel->nama_rombel;
        
        if (strtolower($semester) == 'ganjil') {
            $siswaList = DB::table('siswa')
                ->where(function($q) use ($tahunAwal, $rombelNama) {
                    $q->where(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal)->where('rombel_semester_1', $rombelNama);
                    })
                    ->orWhere(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal - 1)->where('rombel_semester_3', $rombelNama);
                    })
                    ->orWhere(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal - 2)->where('rombel_semester_5', $rombelNama);
                    });
                })
                ->orderBy('nama')
                ->get();
        } else {
            $siswaList = DB::table('siswa')
                ->where(function($q) use ($tahunAwal, $rombelNama) {
                    $q->where(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal)->where('rombel_semester_2', $rombelNama);
                    })
                    ->orWhere(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal - 1)->where('rombel_semester_4', $rombelNama);
                    })
                    ->orWhere(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal - 2)->where('rombel_semester_6', $rombelNama);
                    });
                })
                ->orderBy('nama')
                ->get();
        }
        
        if ($siswaList->isEmpty()) {
            return response('<script>alert("Tidak ada siswa dalam rombel ini!"); window.close();</script>');
        }
        
        // Get mapel list from jadwal (only PAI for agama, skip other agama subjects)
        $semesterJadwal = strtolower($semesterAktif);
        $mapelList = DB::table('jadwal_pelajaran as jp')
            ->join('mata_pelajaran as mp', 'jp.id_mapel', '=', 'mp.id')
            ->where('jp.id_rombel', $rombelId)
            ->where('jp.tahun_pelajaran', $tahunAktif)
            ->where('jp.semester', $semesterJadwal)
            ->select('mp.id', 'mp.nama_mapel')
            ->distinct()
            ->orderBy('mp.id')
            ->get()
            ->filter(function($mapel) {
                if (str_contains($mapel->nama_mapel, 'Pendidikan Agama')) {
                    return $mapel->nama_mapel === 'Pendidikan Agama Islam';
                }
                return true;
            })->values();
        
        // Calculate grades for each student
        $legerData = [];
        foreach ($siswaList as $siswa) {
            $nilaiMapel = [];
            $total = 0;
            $count = 0;
            $allNilai = [];
            
            foreach ($mapelList as $mapel) {
                $mapelNama = $mapel->nama_mapel;
                
                // For agama subjects, check student's religion
                if (str_contains($mapelNama, 'Pendidikan Agama')) {
                    $agamaMapel = 'Pendidikan Agama ' . $siswa->agama;
                    $nilaiResult = DB::table('penilaian')
                        ->where('nisn', $siswa->nisn)
                        ->where('mapel', $agamaMapel)
                        ->avg('nilai');
                } else {
                    $nilaiResult = DB::table('penilaian')
                        ->where('nisn', $siswa->nisn)
                        ->where('mapel', $mapelNama)
                        ->avg('nilai');
                }
                
                $nilai = $nilaiResult ? round($nilaiResult, 1) : null;
                $nilaiMapel[$mapel->id] = $nilai;
                $allNilai[] = $nilai;
                
                if ($nilai !== null) {
                    $total += $nilai;
                    $count++;
                }
            }
            
            // Get attendance
            $presensi = DB::table('presensi_siswa')
                ->where('nisn', $siswa->nisn)
                ->where('tahun_pelajaran', $tahunAktif)
                ->where('semester', strtolower($semesterAktif))
                ->selectRaw("COUNT(*) as total, SUM(CASE WHEN presensi = 'H' THEN 1 ELSE 0 END) as hadir")
                ->first();
            $kehadiran = $presensi && $presensi->total > 0 
                ? round(($presensi->hadir / $presensi->total) * 100, 1) : 0;
            
            // Calculate priority score (sum of top half grades)
            $validNilai = array_filter($allNilai, fn($v) => $v !== null);
            sort($validNilai);
            $half = ceil(count($validNilai) / 2);
            $topHalf = array_slice($validNilai, -$half);
            $skorPrioritas = array_sum($topHalf);
            
            $legerData[] = [
                'id' => $siswa->id,
                'nis' => $siswa->nis,
                'nisn' => $siswa->nisn,
                'nama' => $siswa->nama,
                'jk' => $siswa->jk,
                'agama' => $siswa->agama,
                'nilai_mapel' => $nilaiMapel,
                'total' => round($total, 1),
                'rata_rata' => $count > 0 ? round($total / $count, 1) : 0,
                'kehadiran' => $kehadiran,
                'skor_prioritas' => $skorPrioritas
            ];
        }
        
        // Sort for ranking
        usort($legerData, function($a, $b) {
            if ($a['rata_rata'] != $b['rata_rata']) return $b['rata_rata'] <=> $a['rata_rata'];
            if ($a['kehadiran'] != $b['kehadiran']) return $b['kehadiran'] <=> $a['kehadiran'];
            return $b['skor_prioritas'] <=> $a['skor_prioritas'];
        });
        
        foreach ($legerData as $i => &$data) {
            $data['ranking'] = $i + 1;
        }
        unset($data);
        
        // Sort by name for display
        usort($legerData, fn($a, $b) => strcmp($a['nama'], $b['nama']));
        
        return view('admin.leger.print-nilai', compact(
            'rombel', 'rombelNama', 'mapelList', 'legerData',
            'tahunAktif', 'semesterAktif', 'namaWaliKelas', 'nipWaliKelas',
            'namaKepala', 'nipKepala'
        ));
    }
    
    /**
     * Print Leger Katrol (Adjusted grades from nilai_katrol table)
     */
    public function printKatrol(Request $request)
    {
        $rombelId = $request->query('rombel_id');
        $tahunPelajaran = $request->query('tahun');
        $semester = $request->query('semester');
        
        if (!$rombelId || !$tahunPelajaran || !$semester) {
            return response('<script>alert("Parameter tidak lengkap!"); window.close();</script>');
        }
        
        // Get rombel data
        $rombel = Rombel::find($rombelId);
        if (!$rombel) {
            return response('<script>alert("Data rombel tidak ditemukan!"); window.close();</script>');
        }
        
        // Get active period
        $periodeAktif = DataPeriodik::where('aktif', 'Ya')->first();
        $tahunAktif = $periodeAktif->tahun_pelajaran ?? $tahunPelajaran;
        $semesterAktif = $periodeAktif->semester ?? $semester;
        
        // Check if katrol data exists in new leger table
        $katrolCount = DB::table('katrol_nilai_leger')
            ->where('rombel_id', $rombelId)
            ->where('tahun_pelajaran', $tahunAktif)
            ->where('semester', $semesterAktif)
            ->count();
        
        if ($katrolCount == 0) {
            return response('<script>alert("Belum ada data nilai katrol untuk rombel ini!"); window.close();</script>');
        }
        
        // Get wali kelas NIP
        $namaWaliKelas = $rombel->wali_kelas ?? '';
        $nipWaliKelas = '';
        if ($namaWaliKelas) {
            $guru = DB::table('guru')->where('nama', $namaWaliKelas)->first();
            $nipWaliKelas = $guru->nip ?? '';
        }
        
        $namaKepala = $periodeAktif->nama_kepala ?? '';
        $nipKepala = $periodeAktif->nip_kepala ?? '';
        $tingkatRombel = $rombel->tingkat ?? '';
        
        // Build student query
        $tahunAjaran = explode('/', $tahunPelajaran);
        $tahunAwal = intval($tahunAjaran[0]);
        $rombelNama = $rombel->nama_rombel;
        
        if (strtolower($semester) == 'ganjil') {
            $siswaList = DB::table('siswa')
                ->where(function($q) use ($tahunAwal, $rombelNama) {
                    $q->where(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal)->where('rombel_semester_1', $rombelNama);
                    })
                    ->orWhere(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal - 1)->where('rombel_semester_3', $rombelNama);
                    })
                    ->orWhere(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal - 2)->where('rombel_semester_5', $rombelNama);
                    });
                })
                ->orderBy('nama')
                ->get();
        } else {
            $siswaList = DB::table('siswa')
                ->where(function($q) use ($tahunAwal, $rombelNama) {
                    $q->where(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal)->where('rombel_semester_2', $rombelNama);
                    })
                    ->orWhere(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal - 1)->where('rombel_semester_4', $rombelNama);
                    })
                    ->orWhere(function($q2) use ($tahunAwal, $rombelNama) {
                        $q2->where('angkatan_masuk', $tahunAwal - 2)->where('rombel_semester_6', $rombelNama);
                    });
                })
                ->orderBy('nama')
                ->get();
        }
        
        if ($siswaList->isEmpty()) {
            return response('<script>alert("Tidak ada siswa dalam rombel ini!"); window.close();</script>');
        }
        
        // Get mapel list from jadwal
        $semesterJadwal = strtolower($semesterAktif);
        $mapelList = DB::table('jadwal_pelajaran as jp')
            ->join('mata_pelajaran as mp', 'jp.id_mapel', '=', 'mp.id')
            ->where('jp.id_rombel', $rombelId)
            ->where('jp.tahun_pelajaran', $tahunAktif)
            ->where('jp.semester', $semesterJadwal)
            ->select('mp.id', 'mp.nama_mapel')
            ->distinct()
            ->orderBy('mp.id')
            ->get()
            ->filter(function($mapel) {
                if (str_contains($mapel->nama_mapel, 'Pendidikan Agama')) {
                    return $mapel->nama_mapel === 'Pendidikan Agama Islam';
                }
                return true;
            })->values();
        
        // For Kelas X: add IPA and IPS columns
        $ipaMapel = ['Biologi', 'Fisika', 'Kimia'];
        $ipsMapel = ['Sejarah', 'Ekonomi', 'Sosiologi', 'Geografi'];
        $isKelasX = ($tingkatRombel == 'X');
        
        if ($isKelasX) {
            $newMapelList = collect();
            foreach ($mapelList as $mapel) {
                $newMapelList->push($mapel);
                if ($mapel->nama_mapel == 'Kimia') {
                    $newMapelList->push((object)['id' => 'IPA', 'nama_mapel' => 'IPA', 'is_grouped' => true]);
                }
                if ($mapel->nama_mapel == 'Geografi') {
                    $newMapelList->push((object)['id' => 'IPS', 'nama_mapel' => 'IPS', 'is_grouped' => true]);
                }
            }
            $mapelList = $newMapelList;
        }
        
        // Get katrol grades from leger table (wide format)
        $legerData = [];
        foreach ($siswaList as $siswa) {
            // Fetch this student's row from katrol_nilai_leger
            $katrolRow = DB::table('katrol_nilai_leger')
                ->where('rombel_id', $rombelId)
                ->where('tahun_pelajaran', $tahunAktif)
                ->where('semester', $semesterAktif)
                ->where('nisn', $siswa->nisn)
                ->first();
            
            $nilaiMapel = [];
            $total = 0;
            $count = 0;
            
            // Extract nilai from columns
            foreach ($mapelList as $mapel) {
                $mapelNama = $mapel->nama_mapel;
                
                // Convert mapel name to column name (lowercase, replace space with underscore)
                $columnName = strtolower(str_replace(' ', '_', $mapelNama));
                
                // Get value from the column
                $nilai = null;
                if ($katrolRow && isset($katrolRow->$columnName)) {
                    $nilai = floatval($katrolRow->$columnName);
                }
                
                $nilaiMapel[$mapelNama] = $nilai;
                
                // Skip IPA/IPS component subjects for total (Kelas X)
                if ($isKelasX && in_array($mapelNama, $ipaMapel)) continue;
                if ($isKelasX && in_array($mapelNama, $ipsMapel)) continue;
                
                if ($nilai !== null) {
                    $total += $nilai;
                    $count++;
                }
            }
            
            // Get attendance
            $presensi = DB::table('presensi_siswa')
                ->where('nisn', $siswa->nisn)
                ->where('tahun_pelajaran', $tahunAktif)
                ->where('semester', strtolower($semesterAktif))
                ->selectRaw("COUNT(*) as total, SUM(CASE WHEN presensi = 'H' THEN 1 ELSE 0 END) as hadir")
                ->first();
            $kehadiran = $presensi && $presensi->total > 0 
                ? round(($presensi->hadir / $presensi->total) * 100, 1) : 0;
            
            $legerData[] = [
                'id' => $siswa->id,
                'nis' => $siswa->nis,
                'nisn' => $siswa->nisn,
                'nama' => $siswa->nama,
                'jk' => $siswa->jk,
                'agama' => $siswa->agama,
                'nilai_mapel' => $nilaiMapel,
                'total' => round($total, 1),
                'rata_rata' => $count > 0 ? round($total / $count, 1) : 0,
                'kehadiran' => $kehadiran
            ];
        }
        
        // Sort for ranking
        usort($legerData, function($a, $b) {
            if ($a['rata_rata'] != $b['rata_rata']) return $b['rata_rata'] <=> $a['rata_rata'];
            return $b['kehadiran'] <=> $a['kehadiran'];
        });
        
        foreach ($legerData as $i => &$data) {
            $data['ranking'] = $i + 1;
        }
        unset($data);
        
        // Sort by name for display
        usort($legerData, fn($a, $b) => strcmp($a['nama'], $b['nama']));
        
        return view('admin.leger.print-katrol', compact(
            'rombel', 'rombelNama', 'mapelList', 'legerData',
            'tahunAktif', 'semesterAktif', 'namaWaliKelas', 'nipWaliKelas',
            'namaKepala', 'nipKepala', 'isKelasX', 'ipaMapel', 'ipsMapel'
        ));
    }
}
