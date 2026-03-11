<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Riwayat Akademik Semua - {{ $rombel->nama_rombel }}</title>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            @page { size: A4 portrait; margin: 0.3cm; }
            .page-break { page-break-before: always; }
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11pt;
            color: #333;
            line-height: 1.4;
        }
        
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        
        .siswa-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 12px;
            background: #f8fafc;
            border-radius: 6px;
            border-left: 4px solid #667eea;
        }
        
        .siswa-info div { flex: 1; }
        .siswa-info p { margin: 4px 0; }
        
        .section { margin-bottom: 20px; }
        
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #d1d5db; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; font-weight: 600; color: #374151; font-size: 9pt; }
        tr:nth-child(even) { background: #fafafa; }
        
        .text-center { text-align: center; }
        
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 8pt; font-weight: 600; }
        .badge-a { background: #dcfce7; color: #15803d; }
        .badge-b { background: #dbeafe; color: #1d4ed8; }
        .badge-c { background: #fef9c3; color: #a16207; }
        .badge-d { background: #fee2e2; color: #dc2626; }
        
        .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 15px; }
        .summary-item { text-align: center; padding: 10px; background: #f8fafc; border-radius: 6px; border: 1px solid #e5e7eb; }
        .summary-item .value { font-size: 16pt; font-weight: 700; color: #1f2937; }
        .summary-item .label { font-size: 8pt; color: #6b7280; }
        
        .presensi-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 6px; }
        .presensi-item { text-align: center; padding: 8px; border-radius: 5px; }
        .presensi-item .count { font-size: 12pt; font-weight: 700; }
        .presensi-item .label { font-size: 7pt; }
        
        .signature { margin-top: 30px; display: flex; justify-content: space-between; }
        .signature div { text-align: center; width: 280px; }
        .signature .line { margin: 70px 0 5px 0; border-bottom: 1px solid #333; }
    </style>
</head>
<body>
    @foreach($allStudentsData as $index => $data)
    @php
        $siswa = $data['siswa'];
        $rekapNilai = $data['rekapNilai'];
        $rataRataKeseluruhan = $data['rataRataKeseluruhan'];
        $presensiTotal = $data['presensiTotal'];
        $persentaseKehadiran = $data['persentaseKehadiran'];
        $ekstraList = $data['ekstraList'];
        $prestasiList = $data['prestasiList'];
        $catatanBkList = $data['catatanBkList'] ?? [];
    @endphp
    <div class="container {{ $index > 0 ? 'page-break' : '' }}">
        <!-- KOP SURAT -->
        <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 10px; border-bottom: 3px solid #000; margin-bottom: 12px;">
            <div style="width: 70px; text-align: center;">
                <img src="{{ asset('assets/images/logo-lampung.png') }}" alt="Logo Lampung" style="width: 60px; height: 60px; object-fit: contain;" onerror="this.style.display='none'">
            </div>
            <div style="flex: 1; text-align: center; padding: 0 10px; line-height: 1.2;">
                <p style="margin: 0; font-size: 10pt; font-weight: 600;">PEMERINTAH PROVINSI LAMPUNG</p>
                <p style="margin: 0; font-size: 10pt; font-weight: 600;">DINAS PENDIDIKAN DAN KEBUDAYAAN</p>
                <p style="margin: 1px 0; font-size: 14pt; font-weight: 800; letter-spacing: 0.5px;">SMA NEGERI 1 SEPUTIH RAMAN</p>
                <p style="margin: 0; font-size: 8pt;">
                    <span style="color: #dc2626; font-weight: 600;">NSS. 301120207036</span> – 
                    <span style="color: #2563eb; font-weight: 600;">NPSN 10802068</span> – 
                    <span style="color: #dc2626; font-weight: 600;">AKREDITASI "A"</span>
                </p>
                <p style="margin: 0; font-size: 7pt;">Alamat : JL. Raya Seputih Raman Kecamatan Seputih Raman Kabupaten Lampung Tengah</p>
            </div>
            <div style="width: 70px; text-align: center;">
                <img src="{{ asset('assets/images/logo-sekolah.png') }}" alt="Logo SMANSA" style="width: 60px; height: 60px; object-fit: contain;" onerror="this.style.display='none'">
            </div>
        </div>
        
        <!-- JUDUL -->
        <div style="text-align: center; margin-bottom: 20px;">
            <h1 style="margin: 0; font-size: 14pt; font-weight: 700; text-decoration: underline;">RIWAYAT AKADEMIK SISWA</h1>
        </div>
        
        <!-- STUDENT INFO -->
        <div class="siswa-info">
            <div>
                <p><strong>Nama:</strong> {{ $siswa->nama }}</p>
                <p><strong>NIS/NISN:</strong> {{ $siswa->nis }} / {{ $siswa->nisn }}</p>
            </div>
            <div>
                <p><strong>Rombel:</strong> {{ $rombel->nama_rombel }}</p>
                <p><strong>Periode:</strong> {{ $periodeAktif->tahun_pelajaran }} - {{ $periodeAktif->semester }}</p>
            </div>
        </div>
        
        <!-- SUMMARY -->
        <div class="summary-grid">
            <div class="summary-item">
                <div class="value">{{ number_format($rataRataKeseluruhan, 1) }}</div>
                <div class="label">RATA-RATA NILAI</div>
            </div>
            <div class="summary-item">
                <div class="value">{{ $persentaseKehadiran }}%</div>
                <div class="label">KEHADIRAN</div>
            </div>
            <div class="summary-item">
                <div class="value">{{ count($ekstraList) }}</div>
                <div class="label">EKSTRAKURIKULER</div>
            </div>
            <div class="summary-item">
                <div class="value">{{ count($prestasiList) }}</div>
                <div class="label">PRESTASI</div>
            </div>
        </div>
        
        <!-- NILAI MATA PELAJARAN -->
        <div class="section">
            <div class="section-title">📚 Rekap Nilai Mata Pelajaran dan Keaktifan Proses Belajar</div>
            @if(count($rekapNilai) > 0)
            <table>
                <thead>
                    <tr style="background: #f3f4f6;">
                        <th rowspan="2" style="width: 5%; vertical-align: middle;">No</th>
                        <th rowspan="2" style="vertical-align: middle;">Mata Pelajaran</th>
                        <th colspan="2" class="text-center" style="background: #eff6ff;">Nilai</th>
                        <th colspan="7" class="text-center" style="background: #f0fdf4;">Kehadiran</th>
                    </tr>
                    <tr style="background: #f9fafb;">
                        <th class="text-center" style="width: 7%; font-size: 8pt;">Avg</th>
                        <th class="text-center" style="width: 5%; font-size: 8pt;">Pred</th>
                        <th class="text-center" style="width: 4%; font-size: 7pt; color: #15803d;">H</th>
                        <th class="text-center" style="width: 4%; font-size: 7pt; color: #1d4ed8;">D</th>
                        <th class="text-center" style="width: 4%; font-size: 7pt; color: #5b21b6;">I</th>
                        <th class="text-center" style="width: 4%; font-size: 7pt; color: #92400e;">S</th>
                        <th class="text-center" style="width: 4%; font-size: 7pt; color: #dc2626;">A</th>
                        <th class="text-center" style="width: 4%; font-size: 7pt; color: #4b5563;">B</th>
                        <th class="text-center" style="width: 5%; font-size: 7pt;">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapNilai as $i => $nilai)
                    @php $pres = $nilai['presensi']; @endphp
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>
                            <div style="font-size: 9pt; font-weight: 600;">{{ $nilai['nama_mapel'] }}</div>
                            <div style="font-size: 7pt; color: #666;">Guru: {{ $nilai['nama_guru'] ?? '-' }}</div>
                        </td>
                        <td class="text-center" style="font-weight: bold;">
                            {{ $nilai['total_nilai'] > 0 ? number_format($nilai['rata_rata'], 1) : '-' }}
                        </td>
                        <td class="text-center">
                            @if($nilai['total_nilai'] > 0)
                                <span class="badge badge-{{ strtolower($nilai['predikat']) }}">{{ $nilai['predikat'] }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center" style="color: #15803d; font-weight: 600; font-size: 9pt;">{{ $pres['hadir'] }}</td>
                        <td class="text-center" style="color: #1d4ed8; font-size: 9pt;">{{ $pres['dispen'] }}</td>
                        <td class="text-center" style="color: #5b21b6; font-size: 9pt;">{{ $pres['izin'] }}</td>
                        <td class="text-center" style="color: #92400e; font-size: 9pt;">{{ $pres['sakit'] }}</td>
                        <td class="text-center" style="color: #dc2626; font-size: 9pt;">{{ $pres['alfa'] }}</td>
                        <td class="text-center" style="color: #4b5563; font-size: 9pt;">{{ $pres['bolos'] }}</td>
                        <td class="text-center" style="font-weight: bold; color: {{ $pres['persentase'] >= 90 ? '#15803d' : ($pres['persentase'] >= 75 ? '#92400e' : '#dc2626') }}; font-size: 9pt;">
                            {{ $pres['total'] > 0 ? $pres['persentase'] . '%' : '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <p style="font-size: 7pt; color: #6b7280;">Keterangan: H=Hadir, D=Dispen, I=Izin, S=Sakit, A=Alfa, B=Bolos</p>
            @else
            <p style="color: #6b7280; text-align: center; padding: 20px;">Belum ada data nilai.</p>
            @endif
        </div>
        
        <!-- PRESENSI -->
        <div class="section">
            <div class="section-title">📅 Rekap Presensi</div>
            <div class="presensi-grid">
                @php $pTotal = $presensiTotal->total ?? 0; @endphp
                <div class="presensi-item" style="background: #dcfce7;">
                    <div class="count" style="color: #15803d;">{{ $pTotal > 0 ? round((($presensiTotal->hadir ?? 0) / $pTotal) * 100, 1) : 0 }}%</div>
                    <div class="label" style="color: #166534;">Hadir</div>
                </div>
                <div class="presensi-item" style="background: #dbeafe;">
                    <div class="count" style="color: #1d4ed8;">{{ $pTotal > 0 ? round((($presensiTotal->dispen ?? 0) / $pTotal) * 100, 1) : 0 }}%</div>
                    <div class="label" style="color: #1e40af;">Dispen</div>
                </div>
                <div class="presensi-item" style="background: #ede9fe;">
                    <div class="count" style="color: #5b21b6;">{{ $pTotal > 0 ? round((($presensiTotal->izin ?? 0) / $pTotal) * 100, 1) : 0 }}%</div>
                    <div class="label" style="color: #6d28d9;">Izin</div>
                </div>
                <div class="presensi-item" style="background: #fef3c7;">
                    <div class="count" style="color: #92400e;">{{ $pTotal > 0 ? round((($presensiTotal->sakit ?? 0) / $pTotal) * 100, 1) : 0 }}%</div>
                    <div class="label" style="color: #a16207;">Sakit</div>
                </div>
                <div class="presensi-item" style="background: #fee2e2;">
                    <div class="count" style="color: #dc2626;">{{ $pTotal > 0 ? round((($presensiTotal->alfa ?? 0) / $pTotal) * 100, 1) : 0 }}%</div>
                    <div class="label" style="color: #991b1b;">Alfa</div>
                </div>
                <div class="presensi-item" style="background: #f3f4f6;">
                    <div class="count" style="color: #4b5563;">{{ $pTotal > 0 ? round((($presensiTotal->bolos ?? 0) / $pTotal) * 100, 1) : 0 }}%</div>
                    <div class="label" style="color: #6b7280;">Bolos</div>
                </div>
            </div>
        </div>
        
        <!-- EKSTRAKURIKULER -->
        @if(count($ekstraList) > 0)
        <div class="section">
            <div class="section-title">⚽ Keaktifan Ekstrakurikuler</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th>Nama Ekstrakurikuler</th>
                        <th class="text-center" style="width: 15%;">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ekstraList as $i => $ekstra)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $ekstra['nama_ekstrakurikuler'] }}</td>
                        <td class="text-center">
                            @if($ekstra['nilai'])
                                <span class="badge badge-{{ strtolower($ekstra['nilai']) }}">{{ $ekstra['nilai'] }}</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        <!-- CATATAN BIMBINGAN KONSELING -->
        @if(count($catatanBkList) > 0)
        <div class="section">
            <div class="section-title">📝 Catatan Bimbingan Konseling</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 12%;">Tanggal</th>
                        <th style="width: 15%;">Jenis</th>
                        <th>Masalah</th>
                        <th>Penyelesaian</th>
                        <th style="width: 10%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($catatanBkList as $i => $catatan)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td class="text-center">{{ $catatan->tanggal ? $catatan->tanggal->format('d/m/Y') : '-' }}</td>
                        <td>{{ $catatan->jenis_bimbingan ?? '-' }}</td>
                        <td style="font-size: 9pt;">{{ Str::limit($catatan->masalah, 60) }}</td>
                        <td style="font-size: 9pt;">{{ Str::limit($catatan->penyelesaian, 60) }}</td>
                        <td class="text-center">
                            @if($catatan->status == 'Selesai')
                                <span class="badge badge-a">Selesai</span>
                            @elseif($catatan->status == 'Proses')
                                <span class="badge badge-c">Proses</span>
                            @else
                                <span class="badge badge-d">{{ $catatan->status ?? '-' }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        <!-- PRESTASI -->
        @if(count($prestasiList) > 0)
        <div class="section">
            <div class="section-title">🏆 Prestasi</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th>Nama Kompetisi</th>
                        <th style="width: 12%;">Juara</th>
                        <th style="width: 15%;">Jenjang</th>
                        <th style="width: 12%;">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prestasiList as $i => $prestasi)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $prestasi->nama_kompetisi }}</td>
                        <td class="text-center">
                            <span class="badge" style="background: #fef3c7; color: #92400e;">{{ $prestasi->juara }}</span>
                        </td>
                        <td class="text-center">{{ $prestasi->jenjang }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($prestasi->tanggal_pelaksanaan)->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        <!-- SIGNATURE -->
        <div style="text-align: center; margin-top: 30px; margin-bottom: 20px;">
            <p>Seputih Raman, {{ $tanggalBagiRaport }}</p>
        </div>
        <div class="signature">
            <div>
                <p>Kepala Sekolah,</p>
                <div class="line"></div>
                <p style="font-weight: bold;">{{ $periodeAktif->nama_kepala ?? '................................' }}</p>
                <p style="font-size: 8pt;">NIP. {{ $periodeAktif->nip_kepala ?? '________________________' }}</p>
            </div>
            <div>
                <p>Wali Kelas,</p>
                <div class="line"></div>
                <p style="font-weight: bold;">{{ $namaWaliKelas ?: '................................' }}</p>
                <p style="font-size: 8pt;">NIP. {{ $nipWaliKelas ?: '________________________' }}</p>
            </div>
        </div>
    </div>
    @endforeach
    
    <script>
        window.onload = function() { window.print(); }
    </script>
</body>
</html>
