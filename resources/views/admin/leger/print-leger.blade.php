<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leger Nilai Katrol - {{ $rombelNama }}</title>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            @page { size: landscape; margin: 10mm; }
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            font-size: 8pt;
            color: #333;
            line-height: 1.3;
        }
        
        .container { max-width: 1300px; margin: 0 auto; padding: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        
        th, td { 
            border: 1px solid #333; 
            padding: 5px 4px; 
            text-align: center; 
            font-size: 8pt;
        }
        
        th { background: #e5e7eb; font-weight: 600; color: #1f2937; }
        thead th { background: linear-gradient(180deg, #dc2626 0%, #b91c1c 100%); color: white; }
        
        .nama-col { text-align: left !important; padding-left: 6px !important; }
        
        tr:nth-child(even) { background: #f9fafb; }
        tr:hover { background: #fef2f2; }
        
        .nilai-a { background: #dcfce7 !important; color: #15803d; font-weight: 600; }
        .nilai-b { background: #dbeafe !important; color: #1d4ed8; font-weight: 600; }
        .nilai-c { background: #fef9c3 !important; color: #92400e; font-weight: 600; }
        .nilai-d { background: #fee2e2 !important; color: #dc2626; font-weight: 600; }
        
        .rata-rata-col { background: #fef2f2 !important; font-weight: 700; color: #dc2626; }
        .ranking-col { background: #f3e8ff !important; font-weight: 600; color: #7c3aed; }
        
        .no-print { margin-top: 15px; text-align: center; }
        .btn-print { 
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 6px;
            font-size: 10pt;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-print:hover { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); }
    </style>
</head>
<body>
    <div class="container">
        <!-- KOP SURAT -->
        <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 8px; border-bottom: 2px solid #000; margin-bottom: 10px;">
            <div style="width: 50px; text-align: center;">
                <img src="{{ asset('assets/images/logo-lampung.png') }}" alt="Logo Lampung" style="width: 45px; height: 45px; object-fit: contain;" onerror="this.style.display='none'">
            </div>
            <div style="flex: 1; text-align: center; padding: 0 8px; line-height: 1.15;">
                <p style="margin: 0; font-size: 9pt; font-weight: 600;">PEMERINTAH PROVINSI LAMPUNG</p>
                <p style="margin: 0; font-size: 9pt; font-weight: 600;">DINAS PENDIDIKAN DAN KEBUDAYAAN</p>
                <p style="margin: 1px 0; font-size: 11pt; font-weight: 800;">SMA NEGERI 1 SEPUTIH RAMAN</p>
                <p style="margin: 0; font-size: 7pt;">
                    <span style="color: #dc2626; font-weight: 600;">NSS. 301120207036</span> – 
                    <span style="color: #2563eb; font-weight: 600;">NPSN 10802068</span> – 
                    <span style="color: #dc2626; font-weight: 600;">AKREDITASI "A"</span>
                </p>
            </div>
            <div style="width: 50px; text-align: center;">
                <img src="{{ asset('assets/images/logo-sekolah.png') }}" alt="Logo SMANSA" style="width: 45px; height: 45px; object-fit: contain;" onerror="this.style.display='none'">
            </div>
        </div>
        
        <!-- JUDUL -->
        <div style="text-align: center; margin-bottom: 10px;">
            <h1 style="margin: 0; font-size: 12pt; font-weight: 700; text-decoration: underline;">LEGER NILAI SISWA <span style="color: #dc2626;">(KATROL)</span></h1>
            <p style="margin: 3px 0; font-size: 9pt;">Kelas: <strong>{{ $rombelNama }}</strong> | Tahun Pelajaran: <strong>{{ $tahun }}</strong> | Semester: <strong>{{ $semester }}</strong></p>
        </div>
        
        <!-- TABEL LEGER -->
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 25px;">No</th>
                        <th rowspan="2" style="width: 70px;">NISN</th>
                        <th rowspan="2" style="min-width: 130px;">Nama Siswa</th>
                        @foreach($mapels as $mapel)
                        <th rowspan="2" style="writing-mode: vertical-rl; text-orientation: mixed; height: 90px; min-width: 22px; font-size: 7pt; background: #3b82f6; padding: 3px 2px;">
                            {{ $mapel }}
                        </th>
                        @endforeach
                        <th rowspan="2" style="width: 40px; background: #f59e0b;">Jumlah</th>
                        <th rowspan="2" style="width: 40px; background: #dc2626;">Rata-rata</th>
                        <th rowspan="2" style="width: 35px; background: #7c3aed;">Rank</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $index => $student)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="font-size: 7pt;">{{ $student['nisn'] }}</td>
                        <td class="nama-col" style="font-size: 7pt;">{{ $student['nama_siswa'] }}</td>
                        
                        @foreach($mapels as $mapel)
                        @php
                            $nilai = $student['nilai'][$mapel] ?? '-';
                            $class = '';
                            if ($nilai !== '-' && is_numeric($nilai)) {
                                if ($nilai >= 85) $class = 'nilai-a';
                                elseif ($nilai >= 75) $class = 'nilai-b';
                                elseif ($nilai >= 65) $class = 'nilai-c';
                                else $class = 'nilai-d';
                            }
                        @endphp
                        <td class="{{ $class }}">{{ $nilai }}</td>
                        @endforeach
                        
                        <td class="total-col">{{ $student['jumlah'] }}</td>
                        <td class="rata-rata-col">{{ $student['rata_rata_display'] }}</td>
                        <td class="ranking-col">{{ $student['ranking'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- KETERANGAN -->
        <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
            <div style="font-size: 7pt;">
                <p style="margin: 0; font-weight: 600;">Keterangan Predikat:</p>
                <p style="margin: 2px 0;"><span style="display: inline-block; width: 15px; height: 10px; background: #dcfce7; border: 1px solid #15803d; vertical-align: middle;"></span> A = 85-100</p>
                <p style="margin: 2px 0;"><span style="display: inline-block; width: 15px; height: 10px; background: #dbeafe; border: 1px solid #1d4ed8; vertical-align: middle;"></span> B = 75-84</p>
                <p style="margin: 2px 0;"><span style="display: inline-block; width: 15px; height: 10px; background: #fef9c3; border: 1px solid #92400e; vertical-align: middle;"></span> C = 65-74</p>
                <p style="margin: 2px 0;"><span style="display: inline-block; width: 15px; height: 10px; background: #fee2e2; border: 1px solid #dc2626; vertical-align: middle;"></span> D = <65</p>
            </div>
            <div style="font-size: 8pt; text-align: right;">
                <p style="margin: 0;"><strong>Total Siswa:</strong> {{ count($students) }}</p>
                <p style="margin: 2px 0;"><strong>Total Mapel:</strong> {{ count($mapels) }}</p>
            </div>
        </div>
        
        <!-- FOOTER -->
        <div style="text-align: center; margin-top: 15px; font-size: 8pt; color: #6b7280;">
            <p>Dicetak pada: <span id="printTimestamp"></span></p>
        </div>
        
        <!-- PRINT BUTTON -->
        <div class="no-print">
            <button class="btn-print" onclick="window.print()"><i class="fas fa-print"></i> Print Leger</button>
            <button class="btn-print" onclick="window.close()" style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%); margin-left: 10px;">Tutup</button>
        </div>
    </div>
    
    <script>
        const now = new Date();
        const options = { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false };
        document.getElementById('printTimestamp').textContent = now.toLocaleDateString('id-ID', options);
    </script>
</body>
</html>
