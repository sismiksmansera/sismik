<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pakta Integritas - {{ $siswa->nama }}</title>
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

        /* TITLE */
        .pakta-title {
            text-align: center;
            margin-bottom: 20px;
            line-height: 1.3;
        }
        .pakta-title h2 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .pakta-title p {
            font-size: 12pt;
            font-weight: bold;
        }

        /* BODY */
        .pakta-body {
            text-align: justify;
        }
        .pakta-body p {
            margin-bottom: 8px;
        }

        /* INFO TABLE */
        .info-table {
            margin: 5px 0 10px 0;
        }
        .info-table td {
            padding: 1px 0;
            vertical-align: top;
            font-size: 12pt;
        }
        .info-table td:first-child {
            width: 140px;
        }
        .info-table td:nth-child(2) {
            width: 15px;
        }

        /* ORDERED LIST */
        .pakta-list {
            margin: 10px 0 10px 0;
            padding-left: 20px;
        }
        .pakta-list > li {
            margin-bottom: 6px;
            text-align: justify;
        }
        .pakta-sublist {
            list-style-type: none;
            padding-left: 15px;
            margin-top: 3px;
        }
        .pakta-sublist li {
            margin-bottom: 2px;
        }
        .pakta-alpha-list {
            list-style-type: lower-alpha;
            padding-left: 20px;
            margin-top: 3px;
        }
        .pakta-alpha-list li {
            margin-bottom: 2px;
        }

        /* CLOSING */
        .pakta-closing {
            margin-top: 15px;
            text-align: justify;
        }

        /* SIGNATURE */
        .signature-table {
            width: 100%;
            margin-top: 25px;
        }
        .signature-table td {
            vertical-align: top;
            width: 50%;
            font-size: 12pt;
        }
        .sig-left { text-align: left; }
        .sig-right { text-align: right; }
        .sig-center { text-align: center; }
        .sig-date {
            text-align: right;
            margin-bottom: 15px;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .sig-space {
            height: 50px;
        }
        .materai-space {
            text-align: center;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- JUDUL -->
        <div class="pakta-title">
            <h2>PAKTA INTEGRITAS</h2>
            <p>OLIMPIADE SAINS NASIONAL</p>
            <p>JENJANG SMA/MA/SMK/MAK/SEDERAJAT</p>
        </div>

        <!-- ISI -->
        <div class="pakta-body">
            <p>Saya, peserta OSN tahun {{ $tahun }} dari tingkat kabupaten/kota hingga nasional dengan identitas sebagai berikut,</p>

            <table class="info-table">
                <tr>
                    <td>NISN</td>
                    <td>:</td>
                    <td>{{ $siswa->nisn ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td>{{ $siswa->nama }}</td>
                </tr>
                <tr>
                    <td>NPSN</td>
                    <td>:</td>
                    <td>10802068</td>
                </tr>
                <tr>
                    <td>Satuan pendidikan</td>
                    <td>:</td>
                    <td>SMA Negeri 1 Seputih Raman</td>
                </tr>
                <tr>
                    <td>Cabang</td>
                    <td>:</td>
                    <td>{{ $mapelOsn }}</td>
                </tr>
            </table>

            <p>Menyatakan secara sadar dan sungguh-sungguh bahwa :</p>

            <ol class="pakta-list">
                <li>Saya mengikuti OSN {{ $tahun }} atas kemauan sendiri, tanpa paksaan dari siapapun dan pihak manapun, serta telah mendapat persetujuan orang tua/wali dan sekolah.</li>
                <li>Saya bersedia mengikuti lomba dengan jujur dan penuh tanggung jawab.</li>
                <li>Saya bersedia mengikuti pembinaan dan seleksi untuk kompetisi tingkat internasional yang diselenggarakan oleh Pusat Prestasi Nasional jika memenuhi syarat. Apabila mengundurkan diri tanpa alasan yang jelas, saya akan menerima sanksi sebagai berikut :
                    <ul class="pakta-sublist">
                        <li>(1) Tidak diperbolehkan mengikuti rangkaian OSN tahun berikutnya pada cabang aja apapun.</li>
                        <li>(2) Satuan pendidikan asal saya tidak diperkenankan mengikuti rangkaian pembinaan internasional pada tahun berikutnya pada cabang ajang yang sama.</li>
                    </ul>
                </li>
                <li>Saya bersedia dan patuh mengikuti segala peraturan yang telah ditentukan panitia dan mematuhi semua keputusan tim juri dan panitia OSN.</li>
                <li>Saya tidak akan melakukan kecurangan dalam bentuk apapun. Jika dikemudian hari terbukti melakukan kecurangan pada rangkaian kompetisi OSN, saya bersedia untuk:
                    <ol class="pakta-alpha-list">
                        <li>Didiskualifikasi</li>
                        <li>Melepaskan semua penghargaan yang saya peroleh dalam rangkaian OSN tersebut.</li>
                    </ol>
                </li>
                <li>Saya memahami bahwa apabila terjadi masalah teknis menyangkut komputer/ponsel, listrik, internet/jaringan, dan sarana lainnya, maka sepenuhnya hal tersebut menjadi tanggung jawab saya.</li>
                <li>Saya tidak akan mengajukan tuntutan dalam bentuk apapun kepada panitia OSN, Balai Pengembangan Talenta Indonesia, Pusat Prestasi Nasional, Kementerian Pendidikan Dasar dan Menengah. Apabila saya tidak mematuhi ketentuan yang telah ditetapkan, saya bersedia menerima konsekuensi sesuai dengan ketentuan yang berlaku.</li>
            </ol>
        </div>

        <!-- PENUTUP -->
        <div class="pakta-closing">
            <p>Pakta integritas ini saya buat dengan sebenar-benarnya, tanpa paksaan dari pihak manapun, dan untuk digunakan sebagaimana mestinya.</p>
        </div>

        <!-- TANDA TANGAN -->
        <div class="sig-date">
            Seputih Raman, {{ $tanggalSurat }}
        </div>

        <table class="signature-table">
            <tr>
                <td class="sig-left">Kepala SMA Negeri 1 Seputih Raman</td>
                <td class="sig-right">Peserta OSN</td>
            </tr>
            <tr>
                <td colspan="2" class="materai-space">(MATERAI)</td>
            </tr>
            <tr>
                <td class="sig-left" style="height: 30px;"></td>
                <td class="sig-right"></td>
            </tr>
            <tr>
                <td class="sig-left">
                    <span class="sig-name">{{ $kepalaSekolah }}</span><br>
                    NIP. {{ $nipKepala }}
                </td>
                <td class="sig-right">
                    <span class="sig-name">({{ $siswa->nama }})</span>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
