<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tamu;
use Illuminate\Http\Request;

class TamuController extends Controller
{
    /**
     * Display list of guests
     */
    public function index(Request $request)
    {
        $query = Tamu::query();
        
        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }
        
        // Filter by kategori
        if ($request->filled('kategori')) {
            $query->where('datang_sebagai', $request->kategori);
        }
        
        // Search by nama or bertemu_dengan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('bertemu_dengan', 'like', "%{$search}%")
                  ->orWhere('keperluan', 'like', "%{$search}%");
            });
        }
        
        $tamuList = $query->orderBy('created_at', 'desc')->paginate(20);
        $kategoriOptions = Tamu::getKategoriOptions();
        
        // Stats
        $totalHariIni = Tamu::whereDate('created_at', today())->count();
        $totalBulanIni = Tamu::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $totalSemua = Tamu::count();
        
        return view('admin.tamu.index', compact(
            'tamuList',
            'kategoriOptions',
            'totalHariIni',
            'totalBulanIni',
            'totalSemua'
        ));
    }
    
    /**
     * Delete guest record
     */
    public function destroy($id)
    {
        $tamu = Tamu::findOrFail($id);
        $tamu->delete();
        
        return redirect()->route('admin.tamu.index')
            ->with('success', 'Data tamu berhasil dihapus!');
    }
    
    /**
     * Show detail of a guest
     */
    public function show($id)
    {
        $tamu = Tamu::findOrFail($id);
        return view('admin.tamu.show', compact('tamu'));
    }
    
    /**
     * Print guest list with filters
     */
    public function print(Request $request)
    {
        $query = Tamu::query();
        
        $tanggalDari = $request->tanggal_dari;
        $tanggalSampai = $request->tanggal_sampai;
        $kategori = $request->kategori;
        
        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $tanggalDari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $tanggalSampai);
        }
        
        // Filter by kategori
        if ($request->filled('kategori')) {
            $query->where('datang_sebagai', $kategori);
        }
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('bertemu_dengan', 'like', "%{$search}%")
                  ->orWhere('keperluan', 'like', "%{$search}%");
            });
        }
        
        $tamuList = $query->orderBy('created_at', 'desc')->get();
        
        return view('admin.tamu.print', compact('tamuList', 'tanggalDari', 'tanggalSampai', 'kategori'));
    }
    
    /**
     * Export guest list to Excel
     */
    public function exportExcel(Request $request)
    {
        $query = Tamu::query();
        
        $tanggalDari = $request->tanggal_dari;
        $tanggalSampai = $request->tanggal_sampai;
        $kategori = $request->kategori;
        
        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $tanggalDari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $tanggalSampai);
        }
        
        // Filter by kategori
        if ($request->filled('kategori')) {
            $query->where('datang_sebagai', $kategori);
        }
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('bertemu_dengan', 'like', "%{$search}%")
                  ->orWhere('keperluan', 'like', "%{$search}%");
            });
        }
        
        $tamuList = $query->orderBy('created_at', 'desc')->get();
        
        // Create spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daftar Tamu');
        
        // Header styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '16A34A']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];
        
        // Set headers
        $headers = ['No', 'Tanggal', 'Waktu', 'Nama', 'No HP', 'Alamat', 'Kategori', 'Bertemu Dengan', 'Keperluan', 'Dokumen Diberikan', 'Dokumen Diminta'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(8);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(35);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(20);
        
        // Data rows
        $row = 2;
        foreach ($tamuList as $index => $tamu) {
            $dokumenDiberikan = $tamu->memberikan_dokumen 
                ? $tamu->jenis_dokumen_diberikan . ($tamu->deskripsi_dokumen_diberikan ? ': ' . $tamu->deskripsi_dokumen_diberikan : '')
                : '-';
            $dokumenDiminta = $tamu->meminta_dokumen 
                ? $tamu->jenis_dokumen_diminta . ($tamu->deskripsi_dokumen_diminta ? ': ' . $tamu->deskripsi_dokumen_diminta : '')
                : '-';
            
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $tamu->created_at->format('d/m/Y'));
            $sheet->setCellValue('C' . $row, $tamu->created_at->format('H:i'));
            $sheet->setCellValue('D' . $row, $tamu->nama);
            $sheet->setCellValue('E' . $row, $tamu->no_hp);
            $sheet->setCellValue('F' . $row, $tamu->alamat);
            $sheet->setCellValue('G' . $row, $tamu->datang_sebagai);
            $sheet->setCellValue('H' . $row, $tamu->bertemu_dengan);
            $sheet->setCellValue('I' . $row, $tamu->keperluan);
            $sheet->setCellValue('J' . $row, $dokumenDiberikan);
            $sheet->setCellValue('K' . $row, $dokumenDiminta);
            
            // Alternate row color
            if ($index % 2 == 0) {
                $sheet->getStyle('A' . $row . ':K' . $row)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F9FAFB');
            }
            
            $row++;
        }
        
        // Add border to all data cells
        $sheet->getStyle('A1:K' . ($row - 1))->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        // Generate filename
        $filename = 'Daftar_Tamu';
        if ($tanggalDari && $tanggalSampai) {
            $filename .= '_' . date('dmy', strtotime($tanggalDari)) . '-' . date('dmy', strtotime($tanggalSampai));
        } elseif ($tanggalDari) {
            $filename .= '_dari_' . date('dmy', strtotime($tanggalDari));
        } elseif ($tanggalSampai) {
            $filename .= '_sampai_' . date('dmy', strtotime($tanggalSampai));
        }
        if ($kategori) {
            $filename .= '_' . str_replace(' ', '_', $kategori);
        }
        $filename .= '.xlsx';
        
        // Create Excel file and download
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
