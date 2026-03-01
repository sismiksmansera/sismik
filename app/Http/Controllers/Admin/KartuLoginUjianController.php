<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KartuLoginUjian;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class KartuLoginUjianController extends Controller
{
    /**
     * Display the import page + existing data table.
     */
    public function index()
    {
        $data = KartuLoginUjian::orderBy('kelas')->orderBy('nama_siswa')->get();
        $loginSettings = \App\Models\LoginSettings::first();
        return view('admin.kartu-login-ujian.index', compact('data', 'loginSettings'));
    }

    /**
     * Download Excel template.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Kartu Login Ujian');

        // Headers
        $headers = ['Nama Siswa', 'Kelas', 'NISN (Username)', 'Password D-Smart', 'Password Bimasoft', 'Password Aksi Jihan'];
        foreach ($headers as $i => $header) {
            $col = chr(65 + $i); // A-F
            $sheet->setCellValue($col . '1', $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '7C3AED'],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        // Example data
        $sheet->setCellValueExplicit('A2', 'Ahmad Fauzi', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('B2', '7A', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('C2', '1234567890', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('D2', 'pass123', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('E2', 'bima456', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('F2', 'jihan789', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_kartu_login_ujian.xlsx';
        $temp = tempnam(sys_get_temp_dir(), 'tpl');
        $writer->save($temp);

        return response()->download($temp, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Import Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls|max:10240',
        ]);

        try {
            $file = $request->file('file_excel');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Remove header row
            array_shift($rows);

            $imported = 0;
            $updated = 0;
            $skipped = 0;

            foreach ($rows as $row) {
                $nama = trim($row[0] ?? '');
                $kelas = trim($row[1] ?? '');
                $nisn = trim($row[2] ?? '');

                if (empty($nama) || empty($nisn)) {
                    $skipped++;
                    continue;
                }

                $existing = KartuLoginUjian::where('nisn', $nisn)->first();
                
                $data = [
                    'nama_siswa' => $nama,
                    'kelas' => $kelas,
                    'nisn' => $nisn,
                    'password_dsmart' => trim($row[3] ?? ''),
                    'password_bimasoft' => trim($row[4] ?? ''),
                    'password_aksi_jihan' => trim($row[5] ?? ''),
                ];

                if ($existing) {
                    $existing->update($data);
                    $updated++;
                } else {
                    KartuLoginUjian::create($data);
                    $imported++;
                }
            }

            $message = "Import berhasil! {$imported} data baru ditambahkan";
            if ($updated > 0) $message .= ", {$updated} data diupdate";
            if ($skipped > 0) $message .= ", {$skipped} data dilewati";

            return redirect()->route('admin.kartu-login-ujian.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.kartu-login-ujian.index')
                ->withErrors('Gagal import: ' . $e->getMessage());
        }
    }

    /**
     * Delete single record.
     */
    public function destroy($id)
    {
        $record = KartuLoginUjian::findOrFail($id);
        $record->delete();

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
    }

    /**
     * Delete all records.
     */
    public function destroyAll()
    {
        KartuLoginUjian::truncate();

        return redirect()->route('admin.kartu-login-ujian.index')
            ->with('success', 'Semua data kartu login ujian berhasil dihapus');
    }
}
