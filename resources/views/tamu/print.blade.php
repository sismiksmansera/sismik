<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Kunjungan Tamu | SISMIK</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1a5d1a 0%, #2e8b2e 50%, #3cb371 100%);
            padding: 30px 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        .success-banner {
            background: linear-gradient(135deg, #16a34a, #22c55e);
            color: white;
            padding: 25px;
            border-radius: 16px 16px 0 0;
            text-align: center;
        }

        .success-banner .icon {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 35px;
        }

        .success-banner h1 {
            font-size: 22px;
            margin-bottom: 5px;
        }

        .success-banner p {
            opacity: 0.9;
            font-size: 14px;
        }

        .receipt-card {
            background: white;
            border-radius: 0 0 16px 16px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .receipt-header {
            background: #f8fafc;
            padding: 20px 25px;
            border-bottom: 2px dashed #e5e7eb;
            text-align: center;
        }

        .receipt-id {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .receipt-id span {
            font-weight: 700;
            color: #16a34a;
            font-size: 16px;
        }

        .receipt-datetime {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 10px;
        }

        .receipt-datetime div {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            color: #374151;
        }

        .receipt-datetime i {
            color: #16a34a;
        }

        .receipt-body {
            padding: 25px;
        }

        .info-row {
            display: flex;
            border-bottom: 1px solid #f3f4f6;
            padding: 12px 0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            width: 140px;
            font-size: 13px;
            color: #6b7280;
            flex-shrink: 0;
        }

        .info-value {
            flex: 1;
            font-size: 14px;
            color: #1f2937;
            font-weight: 500;
        }

        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #16a34a;
            text-transform: uppercase;
            margin: 20px 0 10px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-category {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-yes {
            background: #dcfce7;
            color: #16a34a;
        }

        .badge-no {
            background: #f3f4f6;
            color: #6b7280;
        }

        .receipt-footer {
            background: #f8fafc;
            padding: 20px 25px;
            border-top: 2px dashed #e5e7eb;
            text-align: center;
        }

        .receipt-footer p {
            font-size: 12px;
            color: #6b7280;
        }

        .receipt-footer .school-name {
            font-weight: 700;
            color: #16a34a;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 25px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
        }

        .btn-print {
            background: linear-gradient(135deg, #16a34a, #22c55e);
            color: white;
        }

        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(22, 163, 74, 0.3);
        }

        .btn-back {
            background: white;
            color: #374151;
            border: 2px solid #e5e7eb;
        }

        .btn-back:hover {
            background: #f3f4f6;
            transform: translateY(-1px);
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .container {
                max-width: 100%;
            }

            .success-banner {
                background: #16a34a !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .action-buttons {
                display: none;
            }

            .receipt-card {
                box-shadow: none;
            }
        }

        @media (max-width: 500px) {
            .info-row {
                flex-direction: column;
                gap: 4px;
            }
            .info-label {
                width: 100%;
            }
            .receipt-datetime {
                flex-direction: column;
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Success Banner -->
        <div class="success-banner">
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1>Data Kunjungan Berhasil Disimpan!</h1>
            <p>Silakan cetak bukti ini sebagai tanda kunjungan</p>
        </div>

        <!-- Receipt Card -->
        <div class="receipt-card">
            <div class="receipt-header">
                <div class="receipt-id">
                    Nomor Kunjungan: <span>#{{ str_pad($tamu->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="receipt-datetime">
                    <div>
                        <i class="fas fa-calendar-day"></i>
                        {{ $tamu->hari }}
                    </div>
                    <div>
                        <i class="fas fa-calendar-alt"></i>
                        {{ $tamu->tanggal_formatted }}
                    </div>
                    <div>
                        <i class="fas fa-clock"></i>
                        {{ $tamu->waktu }} WIB
                    </div>
                </div>
            </div>

            <div class="receipt-body">
                <div class="section-title">Data Tamu</div>
                
                <div class="info-row">
                    <div class="info-label">Nama</div>
                    <div class="info-value">{{ $tamu->nama }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Alamat</div>
                    <div class="info-value">{{ $tamu->alamat }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">No. HP</div>
                    <div class="info-value">{{ $tamu->no_hp }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Datang Sebagai</div>
                    <div class="info-value">
                        <span class="badge badge-category">{{ $tamu->datang_sebagai }}</span>
                    </div>
                </div>

                <div class="section-title">Tujuan Kunjungan</div>

                <div class="info-row">
                    <div class="info-label">Bertemu Dengan</div>
                    <div class="info-value">{{ $tamu->bertemu_dengan }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Keperluan</div>
                    <div class="info-value">{{ $tamu->keperluan }}</div>
                </div>

                @if($tamu->memberikan_dokumen || $tamu->meminta_dokumen)
                    <div class="section-title">Informasi Dokumen</div>

                    @if($tamu->memberikan_dokumen)
                        <div class="info-row">
                            <div class="info-label">Memberikan</div>
                            <div class="info-value">
                                <span class="badge badge-yes"><i class="fas fa-check"></i> Ya</span>
                                <div style="margin-top: 8px; font-size: 13px;">
                                    <strong>{{ $tamu->jenis_dokumen_diberikan }}</strong><br>
                                    {{ $tamu->deskripsi_dokumen_diberikan }}
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($tamu->meminta_dokumen)
                        <div class="info-row">
                            <div class="info-label">Meminta</div>
                            <div class="info-value">
                                <span class="badge badge-yes"><i class="fas fa-check"></i> Ya</span>
                                <div style="margin-top: 8px; font-size: 13px;">
                                    <strong>{{ $tamu->jenis_dokumen_diminta }}</strong><br>
                                    {{ $tamu->deskripsi_dokumen_diminta }}
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            <div class="receipt-footer">
                <p class="school-name">SISMIK - Sistem Informasi Akademik</p>
                <p>Terima kasih atas kunjungan Anda</p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button onclick="window.print()" class="btn btn-print">
                <i class="fas fa-print"></i> Cetak Bukti
            </button>
            <a href="{{ route('login') }}" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</body>
</html>
