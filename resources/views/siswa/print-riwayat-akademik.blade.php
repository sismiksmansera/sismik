<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Riwayat Akademik - {{ $siswa->nama }}</title>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11pt;
            color: #333;
            line-height: 1.4;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        /* KOP SURAT */
        .kop-surat {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 10px;
            border-bottom: 3px solid #000;
            margin-bottom: 15px;
        }
        .kop-logo { width: 75px; text-align: center; }
        .kop-logo img { width: 65px; height: 65px; object-fit: contain; }
        .kop-text { flex: 1; text-align: center; padding: 0 10px; line-height: 1.2; }

        /* Siswa Info */
        .siswa-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8fafc;
            border-radius: 8px;
            border-left: 4px solid #10b981;
        }
        .siswa-info div { flex: 1; }
        .siswa-info p { margin: 4px 0; }
        .siswa-info strong { color: #374151; }

        /* Section */
        .section { margin-bottom: 20px; }
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 2px solid #e5e7eb;
        }

        /* Summary Grid */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .summary-item {
            text-align: center;
            padding: 12px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        .summary-item .value { font-size: 16pt; font-weight: 700; color: #1f2937; }
        .summary-item .label { font-size: 8pt; color: #6b7280; }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #d1d5db; padding: 6px 8px; text-align: left; font-size: 10pt; }
        th { background: #f3f4f6; font-weight: 600; color: #374151; }
        tr:nth-child(even) { background: #fafafa; }
        .text-center { text-align: center; }

        /* Badge */
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 9pt; font-weight: 600; }
        .badge-a { background: #dcfce7; color: #15803d; }
        .badge-b { background: #dbeafe; color: #1d4ed8; }
        .badge-c { background: #fef9c3; color: #a16207; }
        .badge-d { background: #fee2e2; color: #dc2626; }

        /* Presensi Grid */
        .presensi-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 8px; }
        .presensi-item { text-align: center; padding: 10px; border-radius: 6px; }
        .presensi-item .count { font-size: 12pt; font-weight: 700; }
        .presensi-item .label { font-size: 8pt; }

        /* Signature */
        .signature { margin-top: 40px; display: flex; justify-content: space-between; }
        .signature div { width: 45%; text-align: center; }

        /* Footer */
        .footer { margin-top: 30px; text-align: center; font-size: 9pt; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- KOP SURAT -->
        <div class="kop-surat">
            <div class="kop-logo">
                <img src="{{ asset('assets/images/logo-lampung.png') }}" alt="Logo Lampung" onerror="this.style.display='none'">
            </div>
            <div class="kop-text">
                <p style="margin: 0; font-size: 10pt; font-weight: 600;">PEMERINTAH PROVINSI LAMPUNG</p>
                <p style="margin: 0; font-size: 10pt; font-weight: 600;">DINAS PENDIDIKAN DAN KEBUDAYAAN</p>
                <p style="margin: 1px 0; font-size: 14pt; font-weight: 800; letter-spacing: 0.5px;">SMA NEGERI 1 SEPUTIH RAMAN</p>
                <p style="margin: 0; font-size: 9pt;">
                    <span style="color: #dc2626; font-weight: 600;">NSS. 301120207036</span> –
                    <span style="color: #2563eb; font-weight: 600;">NPSN 10802068</span> –
                    <span style="color: #dc2626; font-weight: 600;">AKREDITASI "A"</span>
                </p>
                <p style="margin: 0; font-size: 8pt;">Alamat : JL. Raya Seputih Raman Kecamatan Seputih Raman Kabupaten Lampung Tengah</p>
                <p style="margin: 0; font-size: 8pt;">Website : <a href="http://www.sman1seputihraman.sch.id" style="color: #2563eb; text-decoration: underline;">www.sman1seputihraman.sch.id</a></p>
            </div>
            <div class="kop-logo">
                <img src="{{ asset('assets/images/logo-sekolah.png') }}" alt="Logo SMANSA" onerror="this.style.display='none'">
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
                <p><strong>Rombel:</strong> {{ $namaRombel ?: '-' }}</p>
                <p><strong>Periode:</strong> {{ $tahunAktif }} - {{ $semesterAktif }}</p>
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
                <div class="value">{{ count($ekskulList) }}</div>
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
                        <th class="text-center" style="width: 8%; font-size: 9pt;">Avg</th>
                        <th class="text-center" style="width: 6%; font-size: 9pt;">Pred</th>
                        <th class="text-center" style="width: 5%; font-size: 8pt; color: #15803d;">H</th>
                        <th class="text-center" style="width: 5%; font-size: 8pt; color: #1d4ed8;">D</th>
                        <th class="text-center" style="width: 5%; font-size: 8pt; color: #5b21b6;">I</th>
                        <th class="text-center" style="width: 5%; font-size: 8pt; color: #92400e;">S</th>
                        <th class="text-center" style="width: 5%; font-size: 8pt; color: #dc2626;">A</th>
                        <th class="text-center" style="width: 5%; font-size: 8pt; color: #4b5563;">B</th>
                        <th class="text-center" style="width: 6%; font-size: 8pt;">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapNilai as $index => $nilai)
                    @php $pres = $nilai['presensi']; @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <div style="font-size: 10pt; font-weight: 600;">{{ $nilai['nama_mapel'] }}</div>
                            <div style="font-size: 8pt; color: #666;">Guru: {{ $nilai['nama_guru'] ?? '-' }}</div>
                        </td>
                        <td class="text-center" style="font-weight: bold;">{{ $nilai['total_nilai'] > 0 ? number_format($nilai['rata_rata'], 1) : '-' }}</td>
                        <td class="text-center">
                            @if($nilai['total_nilai'] > 0)
                            <span class="badge badge-{{ strtolower($nilai['predikat']) }}">{{ $nilai['predikat'] }}</span>
                            @else - @endif
                        </td>
                        <td class="text-center" style="color: #15803d; font-weight: 600;">{{ $pres['hadir'] }}</td>
                        <td class="text-center" style="color: #1d4ed8;">{{ $pres['dispen'] }}</td>
                        <td class="text-center" style="color: #5b21b6;">{{ $pres['izin'] }}</td>
                        <td class="text-center" style="color: #92400e;">{{ $pres['sakit'] }}</td>
                        <td class="text-center" style="color: #dc2626;">{{ $pres['alfa'] }}</td>
                        <td class="text-center" style="color: #4b5563;">{{ $pres['bolos'] }}</td>
                        <td class="text-center" style="font-weight: bold; color: {{ $pres['persentase'] >= 90 ? '#15803d' : ($pres['persentase'] >= 75 ? '#92400e' : '#dc2626') }};">
                            {{ $pres['total'] > 0 ? $pres['persentase'] . '%' : '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <p style="font-size: 8pt; color: #6b7280; margin-top: 5px;">Keterangan: H=Hadir, D=Dispen, I=Izin, S=Sakit, A=Alfa, B=Bolos</p>
            @else
            <p style="color: #6b7280; text-align: center; padding: 20px;">Belum ada data nilai.</p>
            @endif
        </div>

        <!-- PRESENSI -->
        <div class="section">
            <div class="section-title">📅 Rekap Presensi</div>
            <div class="presensi-grid">
                <div class="presensi-item" style="background: #dcfce7;">
                    <div class="count" style="color: #15803d;">{{ $totalPresensi['hadir'] }}</div>
                    <div class="label" style="color: #166534;">Hadir</div>
                </div>
                <div class="presensi-item" style="background: #dbeafe;">
                    <div class="count" style="color: #1d4ed8;">{{ $totalPresensi['dispen'] }}</div>
                    <div class="label" style="color: #1e40af;">Dispen</div>
                </div>
                <div class="presensi-item" style="background: #ede9fe;">
                    <div class="count" style="color: #5b21b6;">{{ $totalPresensi['izin'] }}</div>
                    <div class="label" style="color: #6d28d9;">Izin</div>
                </div>
                <div class="presensi-item" style="background: #fef3c7;">
                    <div class="count" style="color: #92400e;">{{ $totalPresensi['sakit'] }}</div>
                    <div class="label" style="color: #a16207;">Sakit</div>
                </div>
                <div class="presensi-item" style="background: #fee2e2;">
                    <div class="count" style="color: #dc2626;">{{ $totalPresensi['alfa'] }}</div>
                    <div class="label" style="color: #991b1b;">Alfa</div>
                </div>
                <div class="presensi-item" style="background: #f3f4f6;">
                    <div class="count" style="color: #4b5563;">{{ $totalPresensi['bolos'] }}</div>
                    <div class="label" style="color: #6b7280;">Bolos</div>
                </div>
            </div>
        </div>

        <!-- EKSTRAKURIKULER -->
        @if(count($ekskulList) > 0)
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
                    @foreach($ekskulList as $index => $ekstra)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $ekstra->nama_ekstrakurikuler }}</td>
                        <td class="text-center">
                            @if($ekstra->nilai)
                            <span class="badge badge-{{ strtolower($ekstra->nilai) }}">{{ $ekstra->nilai }}</span>
                            @else - @endif
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
                        <th style="width: 12%;">Tanggal</th>
                        <th>Nama Kompetisi</th>
                        <th class="text-center" style="width: 12%;">Juara</th>
                        <th class="text-center" style="width: 15%;">Jenjang</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prestasiList as $index => $prestasi)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($prestasi->tanggal_pelaksanaan)->format('d/m/Y') }}</td>
                        <td>{{ $prestasi->nama_kompetisi }}</td>
                        <td class="text-center">{{ $prestasi->juara }}</td>
                        <td class="text-center">{{ $prestasi->jenjang }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- CATATAN BK -->
        @if(count($catatanBkList) > 0)
        <div class="section">
            <div class="section-title">💬 Riwayat Catatan BK</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 12%;">Tanggal</th>
                        <th style="width: 15%;">Jenis</th>
                        <th>Masalah</th>
                        <th class="text-center" style="width: 12%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($catatanBkList as $index => $catatan)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($catatan->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $catatan->jenis_bimbingan }}</td>
                        <td>{{ Str::limit($catatan->masalah, 80) }}</td>
                        <td class="text-center">{{ $catatan->status ?: 'Belum' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- TANDA TANGAN -->
        <div class="signature">
            <div>
                <p style="margin-bottom: 60px;">Kepala Sekolah</p>
                <p style="margin: 0;"><strong>{{ $namaKepala ?: '............................' }}</strong></p>
                <p style="margin: 0; border-top: 1px solid #333; display: inline-block; padding-top: 3px; min-width: 180px; font-size: 9pt; color: #666;">
                    NIP. {{ $nipKepala ?: '____________________' }}
                </p>
            </div>
            <div>
                <p style="margin-bottom: 60px;">Wali Kelas</p>
                <p style="margin: 0;"><strong>{{ $namaWaliKelas ?: '............................' }}</strong></p>
                <p style="margin: 0; border-top: 1px solid #333; display: inline-block; padding-top: 3px; min-width: 180px; font-size: 9pt; color: #666;">
                    NIP. {{ $nipWaliKelas ?: '____________________' }}
                </p>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <p>Dicetak pada: <span id="printTimestamp"></span></p>
        </div>
    </div>

    <script>
        function setTimestamp() {
            const now = new Date();
            const options = { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
            document.getElementById('printTimestamp').textContent = now.toLocaleDateString('id-ID', options);
        }
        window.onload = function () {
            setTimestamp();
            window.print();
        }
    </script>
</body>
</html>
