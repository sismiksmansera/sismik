<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Panggilan Orang Tua - {{ $siswa->nama }}</title>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            @page {
                size: 215.9mm 330.2mm;
                margin: 10mm 15mm 5mm 15mm;
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #000;
            line-height: 1.3;
            background: #f5f5f5;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 5mm 15mm 5mm 15mm;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 1000;
        }

        .print-button:hover {
            transform: translateY(-2px);
        }

        .back-button {
            position: fixed;
            top: 20px;
            right: 140px;
            background: #64748b;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            z-index: 1000;
        }

        .letter-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 8px;
            border-bottom: 3px solid #000;
            margin-bottom: 3px;
        }

        .header-logo {
            width: 60px;
            text-align: center;
        }

        .header-logo img {
            width: 55px;
            height: 55px;
            object-fit: contain;
        }

        .header-text {
            flex: 1;
            text-align: center;
            padding: 0 10px;
            line-height: 1.0;
        }

        .header-text .gov {
            font-size: 10pt;
            font-weight: bold;
        }

        .header-text .school {
            font-size: 14pt;
            font-weight: bold;
            margin: 1px 0;
        }

        .header-text .codes {
            font-size: 8pt;
        }

        .header-text .address {
            font-size: 8pt;
        }

        .letter-border {
            border-bottom: 1px solid #000;
            margin-bottom: 12px;
        }

        .letter-number {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .letter-number-left {
            width: 55%;
        }

        .letter-number-left table td {
            padding: 1px 0;
            vertical-align: top;
            font-size: 11pt;
        }

        .letter-number-left table td:first-child {
            width: 70px;
        }

        .letter-body {
            text-align: justify;
            margin-bottom: 8px;
        }

        .letter-body p {
            margin-bottom: 6px;
            text-indent: 30px;
        }

        .student-info {
            margin: 8px 0 8px 30px;
        }

        .student-info table td {
            padding: 1px 0;
            vertical-align: top;
        }

        .student-info table td:first-child {
            width: 100px;
        }

        .meeting-info {
            margin: 8px 0 8px 30px;
        }

        .meeting-info table td {
            padding: 1px 0;
            vertical-align: top;
        }

        .meeting-info table td:first-child {
            width: 100px;
        }

        .letter-closing {
            text-align: justify;
        }

        .letter-closing p {
            margin-bottom: 6px;
            text-indent: 30px;
        }

        .signature-section {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
        }

        .signature-left {
            width: 48%;
            text-align: center;
        }

        .signature-right {
            width: 48%;
            text-align: center;
        }

        .signature-left .title,
        .signature-right .title {
            margin-bottom: 45px;
        }

        .signature-left .name,
        .signature-right .name {
            font-weight: bold;
            text-decoration: underline;
        }

        .signature-left .nip,
        .signature-right .nip {
            font-size: 10pt;
        }

        .tear-line {
            margin: 15px 0 10px 0;
            text-align: center;
            position: relative;
        }

        .tear-line::before {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            top: 50%;
            border-top: 1px dashed #000;
        }

        .tear-line span {
            background: white;
            padding: 0 10px;
            position: relative;
            font-size: 9pt;
        }

        .receipt-section {
            background: #f9f9f9;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .receipt-section h4 {
            text-align: center;
            margin-bottom: 8px;
            font-size: 10pt;
        }

        .receipt-info table {
            width: 100%;
        }

        .receipt-info table td {
            padding: 1px 0;
            font-size: 10pt;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <a href="javascript:history.back()" class="back-button no-print">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
    <button onclick="window.print()" class="print-button no-print">
        <i class="fas fa-print"></i> Cetak Surat
    </button>

    <div class="container">
        {{-- KOP SURAT --}}
        <div class="letter-header">
            <div class="header-logo">
                <img src="{{ asset('assets/images/logo-lampung.png') }}" alt="Logo Lampung" onerror="this.style.display='none'">
            </div>
            <div class="header-text">
                <p class="gov">PEMERINTAH PROVINSI LAMPUNG</p>
                <p class="gov">DINAS PENDIDIKAN DAN KEBUDAYAAN</p>
                <p class="school">{{ $sekolah->nama ?? 'SMA NEGERI 1 SEPUTIH RAMAN' }}</p>
                <p class="codes">
                    <span style="color: #dc2626; font-weight: bold;">NSS. {{ $sekolah->nss ?? '301120207036' }}</span> –
                    <span style="color: #2563eb; font-weight: bold;">NPSN {{ $sekolah->npsn ?? '10802068' }}</span> –
                    <span style="color: #dc2626; font-weight: bold;">AKREDITASI "{{ $sekolah->akreditasi ?? 'A' }}"</span>
                </p>
                <p class="address">Alamat : {{ $sekolah->alamat ?? 'JL. Raya Seputih Raman Kec. Seputih Raman Kab. Lampung Tengah' }}</p>
                <p class="address">Website : <a href="{{ $sekolah->website ?? 'http://www.sman1seputihraman.sch.id' }}" style="color: #2563eb;">{{ $sekolah->website ?? 'www.sman1seputihraman.sch.id' }}</a></p>
            </div>
            <div class="header-logo">
                <img src="{{ asset('assets/images/logo-sekolah.png') }}" alt="Logo Sekolah" onerror="this.style.display='none'">
            </div>
        </div>
        <div class="letter-border"></div>

        {{-- NOMOR SURAT --}}
        <div class="letter-number">
            <div class="letter-number-left">
                <table>
                    <tr>
                        <td>Nomor</td>
                        <td>: {{ $panggilan->no_surat ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td>Lampiran</td>
                        <td>: -</td>
                    </tr>
                    <tr>
                        <td>Perihal</td>
                        <td>: <strong>{{ $panggilan->perihal }}</strong></td>
                    </tr>
                </table>
            </div>
            <div style="text-align: right; font-size: 11pt;">{{ $sekolah->kota ?? 'Seputih Raman' }}, {{ \Carbon\Carbon::parse($panggilan->tanggal_surat)->isoFormat('D MMMM Y') }}</div>
        </div>

        {{-- TUJUAN --}}
        <div style="margin-bottom: 10px; font-size: 11pt;">
            <p>Kepada Yth.</p>
            <p><strong>Bapak/Ibu Orang Tua/Wali</strong></p>
            <p>Siswa a.n: <strong>{{ $siswa->nama }}</strong></p>
            <p>Di Tempat</p>
        </div>

        {{-- ISI SURAT --}}
        <div class="letter-body">
            <p>Dengan hormat,</p>
            <p>Melalui surat ini, kami dari pihak sekolah {{ $sekolah->nama ?? 'SMA Negeri 1 Seputih Raman' }} bermaksud mengundang Bapak/Ibu untuk hadir ke sekolah guna membahas perkembangan dan kondisi putra/putri Bapak/Ibu:</p>

            <div class="student-info">
                <table>
                    <tr>
                        <td>Nama</td>
                        <td>: {{ $siswa->nama }}</td>
                    </tr>
                    <tr>
                        <td>NIS / NISN</td>
                        <td>: {{ $siswa->nis ?? '-' }} / {{ $siswa->nisn }}</td>
                    </tr>
                    <tr>
                        <td>Kelas</td>
                        <td>: {{ $rombelAktif ?? $siswa->nama_rombel ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            @if($panggilan->alasan)
            <p style="text-indent: 0;"><strong>Alasan Pemanggilan:</strong> {{ $panggilan->alasan }}</p>
            @endif

            <p>Untuk itu, kami mohon kehadiran Bapak/Ibu pada:</p>

            <div class="meeting-info">
                <table>
                    <tr>
                        <td>Hari/Tanggal</td>
                        <td>: {{ \Carbon\Carbon::parse($panggilan->tanggal_panggilan)->isoFormat('dddd, D MMMM Y') }}</td>
                    </tr>
                    <tr>
                        <td>Pukul</td>
                        <td>: {{ $panggilan->jam_panggilan ? \Carbon\Carbon::parse($panggilan->jam_panggilan)->format('H:i') . ' WIB - Selesai' : '09:00 WIB - Selesai' }}</td>
                    </tr>
                    <tr>
                        <td>Tempat</td>
                        <td>: {{ $panggilan->tempat ?? 'Ruang BK' }}</td>
                    </tr>
                    <tr>
                        <td>Menghadap</td>
                        <td>: {{ $panggilan->menghadap_ke ?? $guruBKData->nama ?? 'Guru BK' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- PENUTUP --}}
        <div class="letter-closing">
            <p>Demikian surat panggilan ini kami sampaikan. Atas perhatian dan kehadiran Bapak/Ibu, kami ucapkan terima kasih.</p>
        </div>

        {{-- TANDA TANGAN --}}
        <div class="signature-section">
            <div class="signature-left">
                <p>Mengetahui,</p>
                <p>Kepala {{ $sekolah->nama ?? 'SMA Negeri 1 Seputih Raman' }}</p>
                <p class="title"></p>
                <p class="name">{{ $periodeAktif->nama_kepala ?? '-' }}</p>
                <p class="nip">NIP. {{ $periodeAktif->nip_kepala ?? '-' }}</p>
            </div>
            <div class="signature-right">
                <p>&nbsp;</p>
                <p>Guru Bimbingan Konseling,</p>
                <p class="title"></p>
                <p class="name">{{ $guruBKData->nama ?? '-' }}</p>
                <p class="nip">NIP. {{ $guruBKData->nip ?? '-' }}</p>
            </div>
        </div>

        {{-- GARIS POTONG --}}
        <div class="tear-line"><span>✂ Potong di sini</span></div>

        {{-- SURAT TANDA TERIMA --}}
        <div class="receipt-section">
            <h4>TANDA TERIMA SURAT PANGGILAN ORANG TUA/WALI</h4>
            <div class="receipt-info">
                <table>
                    <tr>
                        <td style="width: 100px;">Nama Siswa</td>
                        <td>: {{ $siswa->nama }}</td>
                    </tr>
                    <tr>
                        <td>Kelas</td>
                        <td>: {{ $rombelAktif ?? $siswa->nama_rombel ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>No. Surat</td>
                        <td>: {{ $panggilan->no_surat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Perihal</td>
                        <td>: {{ $panggilan->perihal }}</td>
                    </tr>
                </table>
            </div>
            <p style="margin-top: 8px; font-size: 10pt;">Yang bertanda tangan di bawah ini menyatakan telah menerima surat panggilan tersebut di atas.</p>
            <div style="display: flex; justify-content: space-between; margin-top: 10px; align-items: stretch;">
                <div style="width: 55%; font-size: 10pt;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 140px; padding: 4px 0;">Diterima pada tanggal</td>
                            <td style="padding: 4px 0;">: _______________________</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 0;">Nama Penerima</td>
                            <td style="padding: 4px 0;">: _______________________</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 0;">Hubungan dengan Siswa</td>
                            <td style="padding: 4px 0;">: _______________________</td>
                        </tr>
                    </table>
                </div>
                <div style="width: 35%; text-align: center; font-size: 10pt; display: flex; flex-direction: column; justify-content: space-between;">
                    <p style="margin: 0;">Tanda Tangan Penerima</p>
                    <div style="border-bottom: 1px solid #000; width: 80%; margin-left: auto; margin-right: auto;"></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
