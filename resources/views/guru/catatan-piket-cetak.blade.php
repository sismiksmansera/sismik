<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Piket KBM - {{ \Carbon\Carbon::parse($tanggalHariIni)->format('d M Y') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #000;
            padding: 15mm 20mm;
            background: white;
        }

        /* Header */
        .report-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }
        .report-header h2 {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }
        .report-header h3 {
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .report-header p {
            font-size: 11pt;
            margin: 2px 0;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th,
        table.data-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: center;
            font-size: 10pt;
            vertical-align: middle;
        }
        table.data-table th {
            background: #f0f0f0;
            font-weight: bold;
            font-size: 10pt;
        }
        table.data-table td.rombel-cell {
            text-align: left;
            font-weight: 600;
            white-space: nowrap;
        }

        /* Status icons */
        .status-icon {
            font-size: 14pt;
            font-weight: bold;
        }
        .status-tepat { color: #059669; }
        .status-terlambat { color: #d97706; }
        .status-tanpa { color: #dc2626; }
        .status-izin { color: #ea580c; }
        .status-belum { color: #d1d5db; }

        /* Section titles */
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            margin: 25px 0 8px 0;
            text-transform: uppercase;
        }

        /* Signature */
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 45%;
            text-align: center;
        }
        .signature-box .sig-title {
            font-weight: normal;
            margin-bottom: 60px;
        }
        .signature-box .sig-name {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 200px;
            padding-bottom: 2px;
            font-weight: bold;
        }
        .signature-box .sig-nip {
            font-size: 10pt;
        }

        /* Legend */
        .legend {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
            font-size: 10pt;
            flex-wrap: wrap;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Print */
        @media print {
            body { padding: 10mm 15mm; }
            .no-print { display: none !important; }
            @page { size: landscape; margin: 10mm 15mm; }
        }
    </style>
</head>
<body>
    {{-- Print button --}}
    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 20px; background: #059669; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 600;">
            <span style="margin-right: 5px;">🖨️</span> Cetak
        </button>
        <button onclick="window.close()" style="padding: 8px 20px; background: #6b7280; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 600; margin-left: 8px;">
            ✕ Tutup
        </button>
    </div>

    {{-- Report Header --}}
    <div class="report-header">
        <h2>Laporan Piket KBM</h2>
        <h3>Catatan Kehadiran Guru</h3>
        <p>Hari: <strong>{{ $hariIni }}</strong> &nbsp;|&nbsp; Tanggal: <strong>{{ \Carbon\Carbon::parse($tanggalHariIni)->format('d F Y') }}</strong></p>
    </div>

    {{-- Legend --}}
    <div class="legend">
        <div class="legend-item"><span class="status-icon status-tepat">✔</span> = Hadir Tepat Waktu</div>
        <div class="legend-item"><span class="status-icon status-terlambat">✔</span> = Hadir Terlambat</div>
        <div class="legend-item"><span class="status-icon status-tanpa">✘</span> = Tanpa Keterangan</div>
        <div class="legend-item"><span class="status-icon status-izin">!</span> = Izin</div>
        <div class="legend-item"><span class="status-icon status-belum">—</span> = Belum Dikonfirmasi</div>
    </div>

    {{-- Table 1: Rekap Kehadiran Guru per Rombel --}}
    <div class="section-title">A. Rekap Kehadiran Guru</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th style="width: 100px;">Rombel</th>
                @for($j = 1; $j <= $maxJam; $j++)
                    <th>Jam {{ $j }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($rombelJadwal as $rombel => $jamData)
            <tr>
                <td>{{ $no++ }}</td>
                <td class="rombel-cell">{{ $rombel }}</td>
                @for($j = 1; $j <= $maxJam; $j++)
                    <td>
                        @if(isset($jamData[$j]))
                            @php $st = $jamData[$j]['status']; @endphp
                            @if($st === 'tepat_waktu')
                                <span class="status-icon status-tepat">✔</span>
                            @elseif($st === 'terlambat')
                                <span class="status-icon status-terlambat">✔</span>
                            @elseif($st === 'tanpa_keterangan')
                                <span class="status-icon status-tanpa">✘</span>
                            @elseif($st === 'izin')
                                <span class="status-icon status-izin">!</span>
                            @else
                                <span class="status-icon status-belum">—</span>
                            @endif
                        @endif
                    </td>
                @endfor
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Table 2: Daftar KBM Kosong --}}
    <div class="section-title">B. Daftar KBM Kosong</div>
    @if(count($kbmKosong) > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th>Rombel</th>
                <th style="width: 70px;">Jam ke-</th>
                <th>Mapel Kosong</th>
                <th>Guru Mapel</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kbmKosong as $idx => $k)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td class="rombel-cell">{{ $k['rombel'] }}</td>
                <td>{{ $k['jam_text'] }}</td>
                <td style="text-align: left;">{{ $k['mapel'] }}</td>
                <td style="text-align: left;">{{ $k['guru'] }}</td>
                <td>{{ $k['keterangan'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="text-align: center; padding: 10px; font-style: italic; color: #666;">Tidak ada KBM kosong pada hari ini.</p>
    @endif

    {{-- Signatures --}}
    <div style="text-align: right; margin-top: 40px; margin-bottom: 10px; font-size: 11pt;">
        Seputih Raman, {{ \Carbon\Carbon::parse($tanggalHariIni)->translatedFormat('d F Y') }}
    </div>
    <div class="signature-section">
        <div class="signature-box">
            <div class="sig-title">Kepala Sekolah,</div>
            <div class="sig-name">{{ $kepalaSekolah }}</div>
            @if($nipKepala)
                <div class="sig-nip">NIP. {{ $nipKepala }}</div>
            @endif
        </div>
        <div class="signature-box">
            <div class="sig-title">Guru Piket,</div>
            <div class="sig-name">&nbsp;</div>
        </div>
    </div>
</body>
</html>
