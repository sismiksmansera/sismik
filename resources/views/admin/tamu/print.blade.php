<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Daftar Tamu | SISMIK</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            font-size: 12px;
            color: #1f2937;
            padding: 20px;
            background: white;
        }

        .print-header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #16a34a;
        }

        .print-header h1 {
            font-size: 20px;
            font-weight: 700;
            color: #16a34a;
            margin-bottom: 5px;
        }

        .print-header p {
            font-size: 12px;
            color: #6b7280;
        }

        .print-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 12px 15px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .print-info div {
            font-size: 12px;
        }

        .print-info strong {
            color: #16a34a;
        }

        .print-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .print-table th,
        .print-table td {
            padding: 10px 12px;
            text-align: left;
            border: 1px solid #d1d5db;
        }

        .print-table th {
            background: #16a34a;
            color: white;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
        }

        .print-table tr:nth-child(even) {
            background: #f9fafb;
        }

        .print-table td {
            font-size: 11px;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 600;
        }

        .badge-wali { background: #dbeafe; color: #1d4ed8; }
        .badge-jurnalis { background: #fef3c7; color: #b45309; }
        .badge-pt { background: #dcfce7; color: #16a34a; }
        .badge-khusus { background: #f3e8ff; color: #7c3aed; }
        .badge-umum { background: #f3f4f6; color: #374151; }

        .badge-doc {
            background: #ecfdf5;
            color: #059669;
            font-size: 9px;
            margin-right: 4px;
        }

        .print-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            font-size: 11px;
            color: #6b7280;
        }

        .print-actions {
            text-align: center;
            margin-bottom: 20px;
        }

        .btn-action {
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin: 0 5px;
            font-family: 'Poppins', sans-serif;
        }

        .btn-print {
            background: #16a34a;
            color: white;
        }

        .btn-back {
            background: #f3f4f6;
            color: #374151;
            text-decoration: none;
            display: inline-block;
        }

        .btn-excel {
            background: #059669;
            color: white;
            text-decoration: none;
            display: inline-block;
        }

        .btn-excel:hover {
            background: #047857;
        }

        @media print {
            .print-actions {
                display: none;
            }
            body {
                padding: 10px;
            }
            .print-table th {
                background: #16a34a !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .print-table tr:nth-child(even) {
                background: #f9fafb !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .badge {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <!-- Print Actions -->
    <div class="print-actions">
        <button onclick="window.print()" class="btn-action btn-print">
            <i class="fas fa-print"></i> Cetak
        </button>
        <a href="{{ route('admin.tamu.export-excel', request()->query()) }}" class="btn-action btn-excel">
            <i class="fas fa-file-excel"></i> Download Excel
        </a>
        <a href="{{ route('admin.tamu.index', request()->query()) }}" class="btn-action btn-back">
            ← Kembali
        </a>
    </div>

    <!-- Header -->
    <div class="print-header">
        <h1>DAFTAR KUNJUNGAN TAMU</h1>
        <p>SISMIK - Sistem Informasi Akademik</p>
    </div>

    <!-- Info -->
    <div class="print-info">
        <div>
            <strong>Periode:</strong> 
            @if($tanggalDari && $tanggalSampai)
                {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}
            @elseif($tanggalDari)
                Mulai {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }}
            @elseif($tanggalSampai)
                Sampai {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}
            @else
                Semua Data
            @endif
            @if($kategori)
                | <strong>Kategori:</strong> {{ $kategori }}
            @endif
        </div>
        <div>
            <strong>Dicetak:</strong> {{ now()->format('d/m/Y H:i') }} WIB
        </div>
    </div>

    <!-- Table -->
    <table class="print-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Nama</th>
                <th>No HP</th>
                <th>Kategori</th>
                <th>Bertemu</th>
                <th>Keperluan</th>
                <th>Dokumen</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tamuList as $index => $tamu)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $tamu->created_at->format('d/m/Y') }}</td>
                    <td>{{ $tamu->created_at->format('H:i') }}</td>
                    <td><strong>{{ $tamu->nama }}</strong></td>
                    <td>{{ $tamu->no_hp }}</td>
                    <td>
                        @php
                            $badgeClass = match($tamu->datang_sebagai) {
                                'Wali Murid' => 'badge-wali',
                                'Jurnalis' => 'badge-jurnalis',
                                'Perguruan Tinggi' => 'badge-pt',
                                'Tamu Khusus' => 'badge-khusus',
                                default => 'badge-umum'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $tamu->datang_sebagai }}</span>
                    </td>
                    <td>{{ $tamu->bertemu_dengan }}</td>
                    <td>{{ Str::limit($tamu->keperluan, 40) }}</td>
                    <td>
                        @if($tamu->memberikan_dokumen)
                            <span class="badge badge-doc">↑ {{ $tamu->jenis_dokumen_diberikan }}</span>
                        @endif
                        @if($tamu->meminta_dokumen)
                            <span class="badge badge-doc">↓ {{ $tamu->jenis_dokumen_diminta }}</span>
                        @endif
                        @if(!$tamu->memberikan_dokumen && !$tamu->meminta_dokumen)
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 30px; color: #6b7280;">
                        Tidak ada data tamu untuk periode yang dipilih
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Summary -->
    <div class="print-info">
        <div><strong>Total Tamu:</strong> {{ $tamuList->count() }} orang</div>
    </div>

    <!-- Footer -->
    <div class="print-footer">
        <p>Dokumen ini dicetak secara otomatis oleh sistem SISMIK</p>
    </div>
</body>
</html>
