<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan - {{ $siswa->nama }}</title>
    <style>
        @page { size: A4 portrait; margin: 0; }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000;
            line-height: 1.5;
        }

        .container {
            padding: 15mm 20mm;
        }

        /* KOP SURAT */
        .letter-header {
            width: 100%;
            padding-bottom: 8px;
            border-bottom: 3px solid #000;
            margin-bottom: 3px;
        }
        .letter-header table {
            width: 100%;
        }
        .header-logo {
            width: 60px;
            text-align: center;
            vertical-align: middle;
        }
        .header-logo img {
            width: 55px;
            height: 55px;
        }
        .header-text {
            text-align: center;
            line-height: 1.0;
            vertical-align: middle;
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
            margin-left: 55%;
        }
        .signature-block {
            text-align: left;
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
    <div class="container">
        <!-- KOP SURAT -->
        <div class="letter-header">
            <table>
                <tr>
                    <td class="header-logo">
                        @if($logoLampung)
                            <img src="{{ $logoLampung }}" alt="Logo Lampung">
                        @endif
                    </td>
                    <td class="header-text">
                        <p class="gov">PEMERINTAH PROVINSI LAMPUNG</p>
                        <p class="gov">DINAS PENDIDIKAN DAN KEBUDAYAAN</p>
                        <p class="school">SMA NEGERI 1 SEPUTIH RAMAN</p>
                        <p class="codes">
                            <span style="color: #dc2626; font-weight: bold;">NSS. 301120207036</span> &ndash;
                            <span style="color: #2563eb; font-weight: bold;">NPSN 10802068</span> &ndash;
                            <span style="color: #dc2626; font-weight: bold;">AKREDITASI "A"</span>
                        </p>
                        <p class="address">Alamat : JL. Raya Seputih Raman Kec. Seputih Raman Kab. Lampung Tengah</p>
                        <p class="address">Website : www.sman1seputihraman.sch.id</p>
                    </td>
                    <td class="header-logo">
                        @if($logoSekolah)
                            <img src="{{ $logoSekolah }}" alt="Logo SMANSA">
                        @endif
                    </td>
                </tr>
            </table>
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
