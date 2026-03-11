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
        
        .container { max-width: 100%; margin: 0 auto; padding: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        
        th, td { 
            border: 1px solid #333; 
            padding: 4px 3px; 
            text-align: center; 
            font-size: 7pt;
        }
        
        th { background: #e5e7eb; font-weight: 600; color: #1f2937; }
        thead th { background: linear-gradient(180deg, #dc2626 0%, #b91c1c 100%); color: white; }
        
        .nama-col { text-align: left !important; padding-left: 5px !important; white-space: nowrap; }
        
        tr:nth-child(even) { background: #f9fafb; }
        tr:hover { background: #fef2f2; }
        
        .nilai-a { background: #dcfce7 !important; color: #15803d; font-weight: 600; }
        .nilai-b { background: #dbeafe !important; color: #1d4ed8; font-weight: 600; }
        .nilai-c { background: #fef9c3 !important; color: #92400e; font-weight: 600; }
        .nilai-d { background: #fee2e2 !important; color: #dc2626; font-weight: 600; }
        
        .ranking-gold { background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%) !important; color: white; font-weight: 700; }
        .ranking-silver { background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%) !important; color: white; font-weight: 700; }
        .ranking-bronze { background: linear-gradient(135deg, #f97316 0%, #ea580c 100%) !important; color: white; font-weight: 700; }
        
        .total-col { background: #fef3c7 !important; font-weight: 700; color: #92400e; }
        .rata-rata-col { background: #fef2f2 !important; font-weight: 700; color: #dc2626; }
        .kehadiran-col { background: #dcfce7 !important; font-weight: 600; color: #15803d; }
        .kehadiran-low { background: #fee2e2 !important; color: #dc2626; }
        .kehadiran-med { background: #fef3c7 !important; color: #92400e; }
        
        .ipa-col { background: #d1fae5 !important; font-weight: bold; border-left: 2px solid #059669 !important; border-right: 2px solid #059669 !important; }
        .ips-col { background: #ede9fe !important; font-weight: bold; border-left: 2px solid #7c3aed !important; border-right: 2px solid #7c3aed !important; }
        .dimmed-col { opacity: 0.7; background: #f3f4f6 !important; }
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
            <p style="margin: 3px 0; font-size: 9pt;">Kelas: <strong>{{ $rombelNama }}</strong> | Tahun Pelajaran: <strong>{{ $tahunAktif }}</strong> | Semester: <strong>{{ $semesterAktif }}</strong></p>
            <p style="margin: 0; font-size: 8pt; color: #666;">Wali Kelas: {{ $namaWaliKelas ?: '-' }}</p>
        </div>
        
        <!-- TABEL LEGER -->
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 25px;">No</th>
                        <th rowspan="2" style="width: 55px;">NIS</th>
                        <th rowspan="2" style="min-width: 110px;">Nama Siswa</th>
                        <th rowspan="2" style="width: 20px;">JK</th>
                        <th colspan="{{ count($mapelList) }}" style="background: #ef4444;">Mata Pelajaran</th>
                        <th rowspan="2" style="width: 35px; background: #f59e0b;">Jumlah</th>
                        <th rowspan="2" style="width: 35px; background: #dc2626;">Rata²</th>
                        <th rowspan="2" style="width: 35px; background: #10b981;">Hadir</th>
                        <th rowspan="2" style="width: 30px; background: #7c3aed;">Rank</th>
                    </tr>
                    <tr>
                        @foreach($mapelList as $mapel)
                        @php
                            $mapelNama = $mapel->nama_mapel;
                            $headerBg = '#3b82f6';
                            $fontStyle = 'font-weight: 600;';
                            
                            if ($isKelasX && (in_array($mapelNama, $ipaMapel) || in_array($mapelNama, $ipsMapel))) {
                                $headerBg = '#9ca3af';
                                $fontStyle = 'font-weight: 400; opacity: 0.8;';
                            }
                        @endphp
                        <th style="writing-mode: vertical-rl; text-orientation: mixed; height: 90px; min-width: 20px; font-size: 6pt; background: {{ $headerBg }}; padding: 3px 2px; {{ $fontStyle }}">
                            {{ $mapel->nama_mapel }}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach($legerData as $data)
                    @php
                        $kehadiranClass = 'kehadiran-col';
                        if ($data['kehadiran'] < 75) $kehadiranClass = 'kehadiran-low';
                        elseif ($data['kehadiran'] < 90) $kehadiranClass = 'kehadiran-med';
                    @endphp
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td style="font-size: 6pt;">{{ $data['nis'] }}</td>
                        <td class="nama-col" style="font-size: 6pt;">{{ $data['nama'] }}</td>
                        <td>{{ $data['jk'] == 'Laki-laki' ? 'L' : 'P' }}</td>
                        
                        @foreach($mapelList as $mapel)
                        @php
                            $mapelNama = $mapel->nama_mapel;
                            $nilai = $data['nilai_mapel'][$mapelNama] ?? null;
                            $class = '';
                            $specialStyle = '';
                            
                            if (isset($mapel->is_grouped) && $mapel->is_grouped) {
                                if ($mapelNama == 'IPA') $class = 'ipa-col';
                                elseif ($mapelNama == 'IPS') $class = 'ips-col';
                            } else {
                                if ($nilai !== null) {
                                    if ($nilai >= 85) $class = 'nilai-a';
                                    elseif ($nilai >= 75) $class = 'nilai-b';
                                    elseif ($nilai >= 65) $class = 'nilai-c';
                                    else $class = 'nilai-d';
                                }
                            }
                        @endphp
                        <td class="{{ $class }}" style="{{ $specialStyle }}">{{ $nilai !== null ? $nilai : '-' }}</td>
                        @endforeach
                        
                        <td class="total-col">{{ $data['total'] }}</td>
                        <td class="rata-rata-col">{{ $data['rata_rata'] }}</td>
                        <td class="{{ $kehadiranClass }}">{{ $data['kehadiran'] }}%</td>
                        <td class="{{ $data['ranking'] == 1 ? 'ranking-gold' : ($data['ranking'] == 2 ? 'ranking-silver' : ($data['ranking'] == 3 ? 'ranking-bronze' : '')) }}">
                            {{ $data['ranking'] }}
                        </td>
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
                <p style="margin: 2px 0;"><span style="display: inline-block; width: 15px; height: 10px; background: #fee2e2; border: 1px solid #dc2626; vertical-align: middle;"></span> D = &lt;65</p>
            </div>
            <div style="font-size: 8pt; text-align: right;">
                <p style="margin: 0;"><strong>Total Siswa:</strong> {{ count($legerData) }}</p>
                <p style="margin: 2px 0;"><strong>Total Mapel:</strong> {{ count($mapelList) }}</p>
            </div>
        </div>
        
        <!-- TANDA TANGAN -->
        <div style="margin-top: 25px; display: flex; justify-content: space-between;">
            <div style="width: 45%; text-align: center;">
                <p style="margin-bottom: 45px; font-size: 8pt;">Mengetahui,<br>Kepala Sekolah</p>
                <p style="margin: 0; font-size: 8pt;">
                    <strong>{{ $namaKepala ?: '............................' }}</strong>
                </p>
                <p style="margin: 0; border-top: 1px solid #333; display: inline-block; padding-top: 2px; min-width: 150px; font-size: 7pt; color: #666;">NIP. {{ $nipKepala ?: '____________________' }}</p>
            </div>
            
            <div style="width: 45%; text-align: center;">
                <p style="margin-bottom: 45px; font-size: 8pt;">Wali Kelas</p>
                <p style="margin: 0; font-size: 8pt;">
                    <strong>{{ $namaWaliKelas ?: '............................' }}</strong>
                </p>
                <p style="margin: 0; border-top: 1px solid #333; display: inline-block; padding-top: 2px; min-width: 150px; font-size: 7pt; color: #666;">NIP. {{ $nipWaliKelas ?: '____________________' }}</p>
            </div>
        </div>
        
        <!-- FOOTER -->
        <div style="text-align: center; margin-top: 15px; font-size: 8pt; color: #6b7280;">
            <p>Dicetak pada: <span id="printTimestamp"></span></p>
        </div>
    </div>
    
    <script>
        const now = new Date();
        const options = { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false };
        document.getElementById('printTimestamp').textContent = now.toLocaleDateString('id-ID', options);
        
        window.onload = function() { window.print(); }
    </script>
</body>
</html>
