<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Catatan Bimbingan - {{ $catatan->nama_siswa }}</title>
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
            line-height: 1.4;
            background: #f5f5f5;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 5mm 15mm;
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

        /* KOP SURAT */
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
            margin-bottom: 15px;
        }

        /* JUDUL */
        .document-title {
            text-align: center;
            margin-bottom: 20px;
        }

        .document-title h2 {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 3px;
        }

        /* CONTENT */
        .section {
            margin-bottom: 15px;
        }

        .section-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 8px;
            padding-bottom: 3px;
            border-bottom: 1px solid #ddd;
        }

        .section-content {
            padding-left: 10px;
        }

        .section-content p {
            margin-bottom: 5px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .info-table td:first-child {
            width: 120px;
        }

        /* SIGNATURE */
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }

        .signature-right {
            width: 45%;
            text-align: center;
        }

        .signature-right .title {
            margin-bottom: 50px;
        }

        .signature-right .name {
            font-weight: bold;
            text-decoration: underline;
        }

        .signature-right .nip {
            font-size: 10pt;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 9pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .pencatat-info {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #ccc;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <a href="javascript:history.back()" class="back-button no-print">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
    <button onclick="window.print()" class="print-button no-print">
        <i class="fas fa-print"></i> Cetak
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
                <img src="{{ asset('assets/images/logo-sekolah.png') }}" alt="Logo Sekolah" onerror="this.style.display='none'">
            </div>
        </div>
        <div class="letter-border"></div>

        {{-- JUDUL --}}
        <div class="document-title">
            <h2>CATATAN BIMBINGAN KONSELING</h2>
        </div>

        {{-- DATA SISWA --}}
        <div class="section">
            <div class="section-title">Data Siswa</div>
            <table class="info-table">
                <tr>
                    <td>Nama</td>
                    <td>: {{ $catatan->nama_siswa }}</td>
                </tr>
                <tr>
                    <td>NIS / NISN</td>
                    <td>: {{ $catatan->nis ?? '-' }} / {{ $catatan->nisn }}</td>
                </tr>
                <tr>
                    <td>Kelas</td>
                    <td>: {{ $guruBKInfo['rombel'] }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>: {{ \Carbon\Carbon::parse($catatan->tanggal)->isoFormat('D MMMM Y') }}</td>
                </tr>
                <tr>
                    <td>Jenis Bimbingan</td>
                    <td>: {{ $catatan->jenis_bimbingan }}</td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>: {{ $catatan->status }}</td>
                </tr>
            </table>
        </div>

        {{-- MASALAH --}}
        <div class="section">
            <div class="section-title">Masalah / Permasalahan</div>
            <div class="section-content">
                <p>{!! nl2br(e($catatan->masalah ?: '-')) !!}</p>
            </div>
        </div>

        {{-- PENYELESAIAN --}}
        <div class="section">
            <div class="section-title">Penyelesaian</div>
            <div class="section-content">
                <p>{!! nl2br(e($catatan->penyelesaian ?: '-')) !!}</p>
            </div>
        </div>

        {{-- TINDAK LANJUT --}}
        <div class="section">
            <div class="section-title">Tindak Lanjut</div>
            <div class="section-content">
                <p>{!! nl2br(e($catatan->tindak_lanjut ?: '-')) !!}</p>
            </div>
        </div>

        @if(!empty($catatan->keterangan))
        {{-- KETERANGAN TAMBAHAN --}}
        <div class="section">
            <div class="section-title">Keterangan Tambahan</div>
            <div class="section-content">
                <p>{!! nl2br(e($catatan->keterangan)) !!}</p>
            </div>
        </div>
        @endif

        @if(!empty($catatan->pencatat_nama))
        <div class="pencatat-info">
            <p><strong>Dicatat Oleh:</strong> {{ $catatan->pencatat_nama }}
                @if(!empty($catatan->pencatat_role) && $catatan->pencatat_role !== 'guru_bk')
                    ({{ ucwords(str_replace('_', ' ', $catatan->pencatat_role)) }})
                @endif
            </p>
        </div>
        @endif

        {{-- TANDA TANGAN --}}
        <div class="signature-section">
            <div class="signature-right">
                <p>Guru Bimbingan Konseling,</p>
                <p class="title"></p>
                <p class="name">{{ $guruBKInfo['nama'] }}</p>
                <p class="nip">NIP. {{ $guruBKInfo['nip'] }}</p>
            </div>
        </div>

        <div class="footer">
            <p>Dicetak pada: <span id="printTimestamp"></span></p>
        </div>
    </div>

    <script>
        function setTimestamp() {
            const now = new Date();
            const options = {
                day: '2-digit',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            };
            const formattedDate = now.toLocaleDateString('id-ID', options);
            document.getElementById('printTimestamp').textContent = formattedDate;
        }

        window.onload = function () {
            setTimestamp();
        }
    </script>
</body>
</html>
