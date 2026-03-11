@extends('layouts.app')

@section('title', 'Detail Nilai {{ $mapel }} | SISMIK')

@push('styles')
<style>
    /* Header Card */
    .nilai-header-card {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border-left: 4px solid #667eea;
        margin-bottom: 20px;
    }
    .nilai-header-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
    }
    .nilai-header-details h3 { margin: 0; color: #1f2937; font-size: 18px; font-weight: 600; }
    .nilai-header-details p { margin: 5px 0 0 0; color: #6b7280; font-size: 14px; }

    /* Action Buttons */
    .action-buttons { margin-bottom: 20px; }
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: #6b7280;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .btn-back:hover { background: #4b5563; transform: translateY(-2px); }

    /* Stats Row */
    .stats-row {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .stat-item {
        flex: 1;
        min-width: 120px;
        background: white;
        padding: 15px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
    }
    .stat-icon.primary { background: linear-gradient(135deg, #667eea, #764ba2); }
    .stat-icon.success { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .stat-value { font-size: 1.25rem; font-weight: 700; color: #1f2937; }
    .stat-label { font-size: 0.75rem; color: #6b7280; }

    /* Nilai Section */
    .nilai-section {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    .section-header {
        padding: 20px 25px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .section-header h2 { margin: 0; font-size: 1.1rem; color: #1f2937; display: flex; align-items: center; gap: 10px; }
    .badge { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }

    /* Nilai List */
    .nilai-list { padding: 20px; display: flex; flex-direction: column; gap: 12px; }
    .nilai-item {
        background: #f8faff;
        border: 1px solid #e1e8ff;
        border-radius: 12px;
        padding: 15px;
        transition: all 0.3s ease;
    }
    .nilai-item:hover { background: #f0f4ff; border-color: #667eea; }
    .nilai-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e1e8ff;
    }
    .nilai-date { color: #667eea; font-weight: 600; font-size: 0.9rem; }
    .nilai-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 700;
        color: white;
        font-size: 0.9rem;
    }
    .nilai-badge.excellent { background: linear-gradient(135deg, #10b981, #059669); }
    .nilai-badge.good { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .nilai-badge.average { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .nilai-badge.poor { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .nilai-details { display: flex; flex-direction: column; gap: 6px; }
    .nilai-row { display: flex; gap: 8px; font-size: 0.9rem; }
    .nilai-label { color: #6b7280; min-width: 80px; }
    .nilai-value { color: #1f2937; flex: 1; }

    /* Empty State */
    .empty-state { text-align: center; padding: 50px 30px; }
    .empty-icon { width: 80px; height: 80px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
    .empty-icon i { font-size: 32px; color: white; }

    @media (max-width: 768px) {
        /* Stats row mobile - 3 in a row */
        .stats-row { 
            flex-direction: row !important; 
            gap: 6px; 
        }
        .stat-item {
            flex-direction: column !important;
            padding: 10px 6px !important;
            gap: 4px !important;
            text-align: center;
            min-width: unset !important;
        }
        .stat-icon {
            width: 32px !important;
            height: 32px !important;
            font-size: 12px !important;
            margin: 0 auto;
        }
        .stat-value {
            font-size: 14px !important;
        }
        .stat-label {
            font-size: 9px !important;
        }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-siswa')

    <div class="main-content">
        <!-- Header -->
        <div class="nilai-header-card">
            <div class="nilai-header-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <div class="nilai-header-details">
                <h3>{{ $mapel }}</h3>
                <p>Detail Nilai Mata Pelajaran</p>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="stats-row">
            <div class="stat-item">
                <div class="stat-icon primary"><i class="fas fa-calculator"></i></div>
                <div>
                    <div class="stat-value">{{ $rataRata }}</div>
                    <div class="stat-label">Rata-rata</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon success"><i class="fas fa-arrow-up"></i></div>
                <div>
                    <div class="stat-value">{{ $tertinggi }}</div>
                    <div class="stat-label">Tertinggi</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon warning"><i class="fas fa-arrow-down"></i></div>
                <div>
                    <div class="stat-value">{{ $terendah }}</div>
                    <div class="stat-label">Terendah</div>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="action-buttons">
            <a href="{{ route('siswa.nilai') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <!-- Nilai Section -->
        <div class="nilai-section">
            <div class="section-header">
                <h2><i class="fas fa-history" style="color: #667eea;"></i> Riwayat Penilaian</h2>
                <span class="badge">{{ $totalNilai }} Nilai</span>
            </div>

            @if($nilaiData->count() > 0)
            <div class="nilai-list">
                @foreach($nilaiData as $nilai)
                    @php
                        $badgeClass = 'poor';
                        if ($nilai->nilai >= 85) $badgeClass = 'excellent';
                        elseif ($nilai->nilai >= 75) $badgeClass = 'good';
                        elseif ($nilai->nilai >= 65) $badgeClass = 'average';
                    @endphp
                    <div class="nilai-item">
                        <div class="nilai-header">
                            <span class="nilai-date">
                                <i class="fas fa-calendar-alt"></i> 
                                {{ \Carbon\Carbon::parse($nilai->tanggal_penilaian)->format('d M Y') }}
                            </span>
                            <span class="nilai-badge {{ $badgeClass }}">{{ $nilai->nilai }}</span>
                        </div>
                        <div class="nilai-details">
                            <div class="nilai-row">
                                <span class="nilai-label">Materi:</span>
                                <span class="nilai-value">{{ $nilai->materi }}</span>
                            </div>
                            @if($nilai->keterangan)
                            <div class="nilai-row">
                                <span class="nilai-label">Keterangan:</span>
                                <span class="nilai-value">{{ $nilai->keterangan }}</span>
                            </div>
                            @endif
                            <div class="nilai-row">
                                <span class="nilai-label">Guru:</span>
                                <span class="nilai-value">{{ $nilai->guru }}</span>
                            </div>
                            <div class="nilai-row">
                                <span class="nilai-label">Periode:</span>
                                <span class="nilai-value">{{ $nilai->tahun_pelajaran }} - {{ $nilai->semester }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @else
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-clipboard-list"></i></div>
                <h3>Belum Ada Data Nilai</h3>
                <p>Tidak ada nilai yang tercatat untuk mata pelajaran {{ $mapel }}.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
