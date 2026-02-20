<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan - {{ $siswa->nama }}</title>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            @page { size: A4 portrait; margin: 15mm 20mm; }
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000;
            line-height: 1.5;
            background: #f5f5f5;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 15mm 20mm;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            min-height: 297mm;
        }

        /* Print Button */
        .print-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 8px;
            z-index: 1000;
        }
        .print-btn {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(16,185,129,0.4);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .print-btn:hover { transform: translateY(-2px); }
        .print-btn.close-btn {
            background: linear-gradient(135deg, #6b7280, #4b5563);
            box-shadow: 0 4px 15px rgba(107,114,128,0.4);
        }

        /* KOP SURAT */
        .letter-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 8px;
            border-bottom: 3px solid #000;
            margin-bottom: 3px;
        }
        .header-logo { width: 60px; text-align: center; }
        .header-logo img { width: 55px; height: 55px; object-fit: contain; }
        .header-text {
            flex: 1;
            text-align: center;
            padding: 0 10px;
            line-height: 1.0;
        }
        .header-text .gov { font-size: 10pt; font-weight: bold; }
        .header-text .school { font-size: 14pt; font-weight: bold; margin: 1px 0; }
        .header-text .codes { font-size: 8pt; }
        .header-text .address { font-size: 8pt; }
        .letter-border { border-bottom: 1px solid #000; margin-bottom: 20px; }

        /* TITLE */
        .surat-title {
            text-align: center;
            margin-bottom: 5px;
        }
        .surat-title h2 {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .surat-title .nomor {
            font-size: 12pt;
        }

        /* BODY */
        .surat-body {
            margin-top: 25px;
            text-align: justify;
        }
        .surat-body p {
            margin-bottom: 10px;
        }
        .info-table {
            margin: 5px 0 10px 0;
        }
        .info-table td {
            padding: 2px 0;
            vertical-align: top;
            font-size: 12pt;
        }
        .info-table td:first-child {
            width: 120px;
        }
        .info-table td:nth-child(2) {
            width: 15px;
        }

        /* CLOSING */
        .surat-closing {
            margin-top: 15px;
            text-align: justify;
        }
        .surat-closing p {
            margin-bottom: 10px;
        }

        /* SIGNATURE */
        .signature-section {
            margin-top: 25px;
            text-align: right;
        }
        .signature-block {
            display: inline-block;
            text-align: center;
            min-width: 250px;
        }
        .signature-block .title {
            margin-bottom: 60px;
        }
        .signature-block .name {
            font-weight: bold;
            text-decoration: underline;
        }
        .signature-block .nip {
            font-size: 11pt;
        }
    </style>
</head>
<body>
    <div class="print-buttons no-print">
        <button class="print-btn" onclick="window.print()">
            🖨️ Cetak / Simpan PDF
        </button>
        <button class="print-btn close-btn" onclick="window.close()">
            ✕ Tutup
        </button>
    </div>

    <div class="container">
        <!-- KOP SURAT -->
        <div class="letter-header">
            <div class="header-logo">
                <img src="{{ asset('simas/assets/images/logo-lampung.png') }}" alt="Logo Lampung" onerror="this.style.display='none'">
            </div>
            <div class="header-text">
                <p class="gov">PEMERINTAH PROVINSI LAMPUNG</p>
                <p class="gov">DINAS PENDIDIKAN DAN KEBUDAYAAN</p>
                <p class="school">SMA NEGERI 1 SEPUTIH RAMAN</p>
                <p class="codes">
                    <span style="color: #dc2626; font-weight: bold;">NSS. 301120207036</span> –
                    <span style="color: #2563eb; font-weight: bold;">NPSN 10802068</span> –
                    <span style="color: #dc2626; font-weight: bold;">AKREDITASI "A"</span>
                </p>
                <p class="address">Alamat : JL. Raya Seputih Raman Kec. Seputih Raman Kab. Lampung Tengah</p>
                <p class="address">Website : <a href="http://www.sman1seputihraman.sch.id" style="color: #2563eb;">www.sman1seputihraman.sch.id</a></p>
            </div>
            <div class="header-logo">
                <img src="{{ asset('simas/assets/images/logo-sekolah.png') }}" alt="Logo SMANSA" onerror="this.style.display='none'">
            </div>
        </div>
        <div class="letter-border"></div>

        <!-- JUDUL SURAT -->
        <div class="surat-title">
            <h2>Surat Keterangan</h2>
            <p class="nomor">No. : {{ $nomorSurat }}</p>
        </div>

        <!-- ISI SURAT -->
        <div class="surat-body">
            <p>Yang bertanda tangan di bawah ini :</p>

            <table class="info-table">
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td>{{ $kepalaSekolah }}</td>
                </tr>
                <tr>
                    <td>NIP</td>
                    <td>:</td>
                    <td>{{ $nipKepala }}</td>
                </tr>
            </table>

            <p>Menerangkan dengan sesungguhnya bahwa :</p>

            <table class="info-table">
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td>{{ $siswa->nama }}</td>
                </tr>
                <tr>
                    <td>NISN</td>
                    <td>:</td>
                    <td>{{ $siswa->nisn ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Kelas</td>
                    <td>:</td>
                    <td>{{ $rombelAktif }}</td>
                </tr>
            </table>

            <p>Adalah peserta didik di satuan pendidikan SMA Negeri 1 Seputih Raman dan lolos seleksi OSN-S pada cabang <strong>{{ $mapelOsn }}</strong>.</p>
        </div>

        <!-- PENUTUP -->
        <div class="surat-closing">
            <p>Demikian surat keterangan ini dibuat dengan sebenarnya, sebagai persyaratan yang digunakan untuk mengikuti Olimpiade Sains Nasional Jenjang SMA/MA/SMK/MAK/Sederajat tahun {{ date('Y') }}.</p>
        </div>

        <!-- TANDA TANGAN -->
        <div class="signature-section">
            <div class="signature-block">
                <p>Seputih Raman, {{ $tanggalSurat }}</p>
                <p class="title">Kepala SMA Negeri 1 Seputih Raman</p>
                <p class="name">{{ $kepalaSekolah }}</p>
                <p class="nip">NIP. {{ $nipKepala }}</p>
            </div>
        </div>
    </div>
</body>
</html>
