<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raport Semua Siswa - {{ $rombel->nama_rombel }}</title>
    <style>
        @media print {
            .no-print { display: none !important; }
            html, body { 
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact;
                margin: 0;
                padding: 0;
            }
            @page { 
                size: A4 portrait; 
                margin: 5mm 10mm 10mm 10mm;
            }
            
            .student-header {
                background: #f8f9fa !important;
                border: 1px solid #dee2e6 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .page-break {
                page-break-after: always;
            }
            
            .container {
                padding-bottom: 0;
            }
        }

        @media screen {
            .student-header {
                background: #f8f9fa;
                border: 1px solid #dee2e6;
            }
            
            .container {
                padding-bottom: 0;
            }
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Times New Roman', serif; 
            font-size: 11pt;
            color: #333;
            line-height: 1.4;
        }
        
        .container { 
            max-width: 210mm; 
            margin: 0 auto; 
            padding: 15px; 
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 15px;
        }
        
        .nilai-table th, .nilai-table td { 
            border: 1px solid #333; 
            padding: 6px 8px; 
            text-align: center;
        }
        
        .nilai-table th { 
            background: #f0f0f0;
            font-weight: bold;
        }

        .mapel-col { text-align: left !important; }
        
        h1, h2, h3 { font-weight: bold; }
        
        .section { margin-bottom: 20px; }
        
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 8px;
            border-bottom: 1px solid #333;
            padding-bottom: 3px;
        }
        
        .info-table td { padding: 3px 5px; }
        .info-table td:first-child { width: 150px; }
    </style>
</head>
<body>
    @php
        $studentCount = 0;
        $totalStudents = count($studentsData);
    @endphp
    
    @foreach($studentsData as $data)
        @php
            $studentCount++;
            $siswa = $data['siswa'];
            $nilaiMapel = $data['nilaiMapel'];
            $totalNilai = $data['totalNilai'];
            $rataRata = $data['rataRata'];
            $presensi = $data['presensi'];
            $ekstraList = $data['ekstraList'];
        @endphp
    
    <div class="container">
        <!-- HEADER INFO SISWA -->
        <div class="student-header" style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 4px 8px; margin-bottom: 8px; border-radius: 3px;">
            <div style="font-family: 'Bookman Old Style', serif; font-size: 8pt; color: #495057;">
                {{ $rombel->nama_rombel }} &nbsp;|&nbsp; {{ strtoupper($siswa->nama) }} &nbsp;|&nbsp; NIS: {{ $siswa->nis }}
            </div>
        </div>
        
        <!-- DATA SISWA -->
        <div class="section" style="margin-bottom: 15px;">
            <table style="width: 100%; border: none; line-height: 1;">
                <tr>
                    <td style="width: 120px; padding: 1px 0; border: none;">Nama</td>
                    <td style="width: 10px; border: none;">:</td>
                    <td style="padding: 1px 0; border: none; font-weight: bold;">{{ strtoupper($siswa->nama) }}</td>
                    <td style="width: 120px; padding: 1px 0; border: none;">Kelas</td>
                    <td style="width: 10px; border: none;">:</td>
                    <td style="padding: 1px 0; border: none;">{{ $rombel->nama_rombel }}</td>
                </tr>
                <tr>
                    <td style="padding: 1px 0; border: none;">NIS/NISN</td>
                    <td style="border: none;">:</td>
                    <td style="padding: 1px 0; border: none;">{{ $siswa->nis }} / {{ $siswa->nisn }}</td>
                    <td style="padding: 1px 0; border: none;">Fase</td>
                    <td style="border: none;">:</td>
                    <td style="padding: 1px 0; border: none;">E</td>
                </tr>
                <tr>
                    <td style="padding: 1px 0; border: none;">Nama Sekolah</td>
                    <td style="border: none;">:</td>
                    <td style="padding: 1px 0; border: none;">SMAN 1 SEPUTIH RAMAN</td>
                    <td style="padding: 1px 0; border: none;">Semester</td>
                    <td style="border: none;">:</td>
                    <td style="padding: 1px 0; border: none;">{{ $periodeAktif->semester == 'Ganjil' ? '1' : '2' }}</td>
                </tr>
                <tr>
                    <td style="padding: 1px 0; border: none;">Alamat</td>
                    <td style="border: none;">:</td>
                    <td style="padding: 1px 0; border: none;">JL. RAYA SEPUTIH RAMAN</td>
                    <td style="padding: 1px 0; border: none;">Tahun Pelajaran</td>
                    <td style="border: none;">:</td>
                    <td style="padding: 1px 0; border: none;">{{ $periodeAktif->tahun_pelajaran }}</td>
                </tr>
            </table>
        </div>
        
        <!-- JUDUL -->
        <div style="text-align: center; margin-bottom: 20px;">
            <h1 style="font-size: 14pt; font-weight: bold; margin: 0;">LAPORAN HASIL BELAJAR</h1>
        </div>
        
        <!-- A. KEGIATAN INTRAKURIKULER - HALAMAN 1 (Mapel 1-14) -->
        <div class="section" style="margin-bottom: 5px;">
            <div class="section-title">A. KEGIATAN INTRAKURIKULER</div>
            <table class="nilai-table" style="margin-bottom: 5px;">
                <thead>
                    <tr style="background: #f0f0f0; color: #000;">
                        <th style="width: 30px;">No</th>
                        <th class="mapel-col" style="width: 180px;">Mata Pelajaran</th>
                        <th style="width: 50px;">Nilai Akhir</th>
                        <th>Capaian Kompetensi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                        $mapelPage1 = array_slice($nilaiMapel, 0, 14);
                    @endphp
                    @foreach($mapelPage1 as $nilai)
                    <tr style="min-height: 55px; height: 55px;">
                        <td>{{ $no++ }}</td>
                        <td class="mapel-col">{{ $nilai['mapel'] }}</td>
                        <td><strong>{{ $nilai['nilai'] !== null ? $nilai['nilai'] : '-' }}</strong></td>
                        <td style="text-align: left; font-size: 9pt; padding: 3px 5px; line-height: 1.2;">
                            Mampu menganalisis dan menerapkan konsep dengan tepat <br>
                            Diharapkan terus mengembangkan kemampuan berpikir kritis
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if(count($nilaiMapel) <= 14)
            <p style="font-size: 9pt; margin-top: 5px; margin-bottom: 0; text-align: right;">
                <strong>Jumlah Nilai: {{ $totalNilai }}</strong> &nbsp;&nbsp;|&nbsp;&nbsp; 
                <strong>Rata-rata: {{ $rataRata }}</strong>
            </p>
            @endif
        </div>
        
        <!-- PAGE BREAK - HALAMAN 2 -->
        <div style="page-break-before: always;"></div>
        
        <!-- HEADER INFO SISWA HALAMAN 2 -->
        <div class="student-header" style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 4px 8px; margin-bottom: 8px; border-radius: 3px;">
            <div style="font-family: 'Bookman Old Style', serif; font-size: 8pt; color: #495057;">
                {{ $rombel->nama_rombel }} &nbsp;|&nbsp; {{ strtoupper($siswa->nama) }} &nbsp;|&nbsp; NIS: {{ $siswa->nis }}
            </div>
        </div>
        
        <!-- LANJUTAN MAPEL (jika ada lebih dari 14) -->
        @php
            $mapelPage2 = array_slice($nilaiMapel, 14);
        @endphp
        @if(!empty($mapelPage2))
        <div class="section" style="margin-bottom: 5px; margin-top: 5px;">
            <table class="nilai-table" style="margin-bottom: 5px;">
                <thead>
                    <tr style="background: #f0f0f0; color: #000;">
                        <th style="width: 30px;">No</th>
                        <th class="mapel-col" style="width: 180px;">Mata Pelajaran</th>
                        <th style="width: 50px;">Nilai Akhir</th>
                        <th>Capaian Kompetensi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 15; @endphp
                    @foreach($mapelPage2 as $nilai)
                    <tr style="min-height: 55px; height: 55px;">
                        <td>{{ $no++ }}</td>
                        <td class="mapel-col">{{ $nilai['mapel'] }}</td>
                        <td><strong>{{ $nilai['nilai'] !== null ? $nilai['nilai'] : '-' }}</strong></td>
                        <td style="text-align: left; font-size: 9pt; padding: 3px 5px; line-height: 1.2;">
                            Mampu menganalisis dan menerapkan konsep dengan tepat <br>
                            Diharapkan terus mengembangkan kemampuan berpikir kritis
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        @if(count($nilaiMapel) > 14)
        <p style="font-size: 9pt; margin-top: 5px; margin-bottom: 0; text-align: right;">
            <strong>Jumlah Nilai: {{ $totalNilai }}</strong> &nbsp;&nbsp;|&nbsp;&nbsp; 
            <strong>Rata-rata: {{ $rataRata }}</strong>
        </p>
        @endif
        
        <!-- B. KEGIATAN KOKURIKULER -->
        <div class="section" style="margin-top: 2px;">
            <div class="section-title" style="font-size: 10pt; margin-bottom: 3px;">B. KEGIATAN KOKURIKULER</div>
            <table class="nilai-table">
                <tbody>
                    <tr>
                        <td style="height: 65px; text-align: left; padding: 5px;"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- C. KEGIATAN EKSTRAKURIKULER -->
        <div class="section" style="margin-top: 8px;">
            <div class="section-title" style="font-size: 10pt; margin-bottom: 3px;">C. KEGIATAN EKSTRAKURIKULER</div>
            <table class="nilai-table">
                <thead>
                    <tr style="background: #f0f0f0; color: #000;">
                        <th style="width: 30px;">No</th>
                        <th class="mapel-col">Nama Kegiatan</th>
                        <th style="width: 80px;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($ekstraList))
                        @php $no = 1; @endphp
                        @foreach($ekstraList as $ekstra)
                        <tr>
                            <td style="padding: 3px;">{{ $no++ }}</td>
                            <td class="mapel-col" style="padding: 3px;">{{ $ekstra['nama_ekstrakurikuler'] }}</td>
                            <td style="padding: 3px;">{{ $ekstra['nilai'] ?: '-' }}</td>
                        </tr>
                        @endforeach
                    @else
                    <tr>
                        <td colspan="3" style="text-align: center; font-style: italic; color: #666; padding: 5px;">Tidak mengikuti kegiatan ekstrakurikuler</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <!-- D. CATATAN WALI KELAS -->
        <div class="section" style="margin-top: 8px;">
            <div class="section-title" style="font-size: 10pt; margin-bottom: 3px;">D. CATATAN WALI KELAS</div>
            <table class="nilai-table">
                <tbody>
                    <tr>
                        <td style="height: 65px; text-align: left; padding: 5px; vertical-align: top;"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- E. KETIDAKHADIRAN -->
        <div class="section" style="margin-top: 8px; margin-bottom: 5px; display: flex; gap: 20px; align-items: flex-start;">
            <div>
                <div class="section-title" style="font-size: 10pt; margin-bottom: 3px;">E. KETIDAKHADIRAN</div>
                <table class="nilai-table" style="width: auto;">
                    <tbody>
                        <tr>
                            <td style="padding: 3px 8px; text-align: left;">Sakit</td>
                            <td style="padding: 3px 3px;">:</td>
                            <td style="padding: 3px 8px;">{{ $presensi->sakit ?? 0 }} hari</td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 8px; text-align: left;">Izin</td>
                            <td style="padding: 3px 3px;">:</td>
                            <td style="padding: 3px 8px;">{{ $presensi->izin ?? 0 }} hari</td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 8px; text-align: left;">Tanpa Keterangan</td>
                            <td style="padding: 3px 3px;">:</td>
                            <td style="padding: 3px 8px;">{{ $presensi->alfa ?? 0 }} hari</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            @if($periodeAktif->semester == 'Genap')
            <div style="flex: 1;">
                <div class="section-title" style="font-size: 10pt; margin-bottom: 3px;">Keterangan Kenaikan Kelas</div>
                <table class="nilai-table" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td style="height: 40px; text-align: left; padding: 5px; vertical-align: top;"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        
        <!-- TANDA TANGAN -->
        <div style="margin-top: 0; display: flex; justify-content: space-between;">
            <div style="width: 45%; text-align: center;">
                <p style="margin-bottom: 3px; font-size: 10pt;">&nbsp;</p>
                <p style="margin: 0; font-size: 10pt;">Orang Tua/Wali</p>
                <p style="margin-bottom: 40px;"></p>
                <p style="margin: 0; font-size: 10pt;">
                    ................................
                </p>
                <p style="margin: 0; font-size: 8pt; color: #666;">&nbsp;</p>
            </div>
            
            <div style="width: 45%; text-align: center;">
                <p style="margin-bottom: 3px; font-size: 10pt;">Seputih Raman, {{ $tanggalBagiRaport ?: '............... ' . date('Y') }}</p>
                <p style="margin: 0; font-size: 10pt;">Wali Kelas</p>
                <p style="margin-bottom: 40px;"></p>
                <p style="margin: 0; font-size: 10pt;">
                    <strong>{{ $rombel->wali_kelas ?: '............................' }}</strong>
                </p>
                <p style="margin: 0; font-size: 8pt; color: #666;">NIP. {{ $nipWaliKelas ?: '____________________' }}</p>
            </div>
        </div>
        
        <!-- KEPALA SEKOLAH -->
        <div style="margin-top: 0; text-align: center;">
            <p style="margin-bottom: 1px; font-size: 10pt;">Mengetahui,</p>
            <p style="margin: 0; font-size: 10pt;">Kepala Sekolah</p>
            <p style="margin-bottom: 40px;"></p>
            <p style="margin: 0; font-size: 10pt;">
                <strong>{{ $periodeAktif->nama_kepala ?: '............................' }}</strong>
            </p>
            <p style="margin: 0; font-size: 8pt; color: #666;">NIP. {{ $periodeAktif->nip_kepala ?: '____________________' }}</p>
        </div>
    </div>
    
    @if($studentCount < $totalStudents)
    <div class="page-break"></div>
    @endif
    
    @endforeach
    
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
