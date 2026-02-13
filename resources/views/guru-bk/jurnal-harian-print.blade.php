<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jurnal Harian Guru BK</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; color: #000; background: #fff; }

        .page { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 15mm 20mm; }

        /* Kop Surat */
        .kop-surat { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-nama-sekolah { font-size: 16pt; font-weight: bold; text-transform: uppercase; margin-bottom: 2px; }
        .kop-alamat { font-size: 10pt; margin-bottom: 2px; }

        /* Title */
        .judul { text-align: center; margin-bottom: 20px; }
        .judul h2 { font-size: 14pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; margin-bottom: 6px; }
        .judul .periode-info { font-size: 11pt; }

        /* Info */
        .info-section { margin-bottom: 15px; }
        .info-section table { border-collapse: collapse; }
        .info-section td { padding: 2px 8px 2px 0; font-size: 11pt; vertical-align: top; }
        .info-section td:first-child { white-space: nowrap; }

        /* Table */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td {
            border: 1px solid #000; padding: 6px 8px; text-align: left; vertical-align: top; font-size: 10pt;
        }
        .data-table th {
            background: #f0f0f0; font-weight: bold; text-align: center; font-size: 10pt;
        }
        .data-table td.center { text-align: center; }
        .data-table td.nomor { text-align: center; width: 35px; }

        /* Footer TTD */
        .ttd-section { margin-top: 30px; display: flex; justify-content: flex-end; }
        .ttd-box { text-align: center; width: 200px; float: right; }
        .ttd-box .tempat-tgl { font-size: 10pt; margin-bottom: 5px; }
        .ttd-box .jabatan { font-size: 10pt; margin-bottom: 60px; }
        .ttd-box .nama { font-size: 10pt; font-weight: bold; text-decoration: underline; }
        .ttd-box .nip { font-size: 9pt; }

        /* No print */
        .no-print { text-align: center; margin: 20px auto; }
        .btn-print {
            padding: 10px 30px; background: #10b981; color: white; border: none;
            border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;
            font-family: Arial, sans-serif;
        }
        .btn-print:hover { background: #059669; }
        .btn-back {
            padding: 10px 30px; background: #6b7280; color: white; border: none;
            border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;
            text-decoration: none; font-family: Arial, sans-serif; margin-left: 10px;
            display: inline-block;
        }

        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
            .page { width: 100%; padding: 10mm 15mm; margin: 0; }
            @page { size: A4 landscape; margin: 10mm; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button class="btn-print" onclick="window.print()"><i class="fas fa-print"></i> 🖨️ Cetak</button>
    <a href="{{ route('guru_bk.jurnal-harian', ['tanggal_mulai' => $tanggalMulai, 'tanggal_akhir' => $tanggalAkhir]) }}" class="btn-back">← Kembali</a>
</div>

<div class="page">
    {{-- Kop Surat --}}
    <div class="kop-surat">
        <div class="kop-nama-sekolah">{{ $sekolah->nama_sekolah ?? 'NAMA SEKOLAH' }}</div>
        <div class="kop-alamat">{{ $sekolah->alamat ?? '' }} {{ $sekolah->telepon ? '| Telp. ' . $sekolah->telepon : '' }}</div>
        <div class="kop-alamat">{{ $sekolah->email ?? '' }} {{ $sekolah->website ? '| ' . $sekolah->website : '' }}</div>
    </div>

    {{-- Judul --}}
    <div class="judul">
        <h2>Jurnal Harian Guru BK</h2>
        @php
            $mulai = \Carbon\Carbon::parse($tanggalMulai);
            $akhir = \Carbon\Carbon::parse($tanggalAkhir);
            $isSameDay = $mulai->isSameDay($akhir);
        @endphp
        <div class="periode-info">
            @if($isSameDay)
            Tanggal: {{ $mulai->translatedFormat('d F Y') }}
            @else
            Periode: {{ $mulai->translatedFormat('d F Y') }} s/d {{ $akhir->translatedFormat('d F Y') }}
            @endif
        </div>
    </div>

    {{-- Info --}}
    <div class="info-section">
        <table>
            <tr><td>Nama Guru BK</td><td>: {{ $guruBK->nama }}</td></tr>
            <tr><td>NIP</td><td>: {{ $guruBK->nip ?? '-' }}</td></tr>
            @if($periodeAktif)
            <tr><td>Tahun Pelajaran</td><td>: {{ $periodeAktif->tahun_pelajaran }} - {{ $periodeAktif->semester }}</td></tr>
            @endif
        </table>
    </div>

    {{-- Table --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th style="width: 130px;">Jenis Aktivitas</th>
                <th style="width: 180px;">Obyek/Subyek Aktivitas</th>
                <th>Deskripsi Aktivitas</th>
                <th style="width: 150px;">Keterangan Lain</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activities as $i => $act)
            <tr>
                <td class="nomor">{{ $i + 1 }}</td>
                <td>{{ $act->label }}</td>
                <td>
                    {{ $act->nama_siswa }}
                    @if($act->rombel && $act->rombel !== '-')
                    <br><small>({{ $act->rombel }})</small>
                    @endif
                </td>
                <td>
                    {{ $act->detail }}
                    @if($act->sub_detail)
                    <br><em>{{ $act->sub_detail }}</em>
                    @endif
                </td>
                <td>
                    @if($act->tanggal)
                    Tgl: {{ \Carbon\Carbon::parse($act->tanggal)->format('d/m/Y') }}<br>
                    @endif
                    @if($act->waktu && $act->waktu !== '-')
                    Jam: {{ $act->waktu }}<br>
                    @endif
                    @if($act->status)
                    Status: {{ $act->status }}<br>
                    @endif
                    @if($act->guru_bk && $act->guru_bk !== '-')
                    Oleh: {{ $act->guru_bk }}
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="center">Tidak ada aktivitas pada rentang tanggal ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- TTD --}}
    <div class="ttd-section">
        <div class="ttd-box">
            <div class="tempat-tgl">................., {{ now()->translatedFormat('d F Y') }}</div>
            <div class="jabatan">Guru BK,</div>
            <div class="nama">{{ $guruBK->nama }}</div>
            <div class="nip">NIP. {{ $guruBK->nip ?? '................................' }}</div>
        </div>
    </div>
</div>

</body>
</html>
