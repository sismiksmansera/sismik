@extends('layouts.app')

@section('title', 'Lihat Nilai | SISMIK')

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

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }
    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
    }
    .stat-icon.primary { background: linear-gradient(135deg, #667eea, #764ba2); }
    .stat-icon.success { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .stat-info h3 { margin: 0; font-size: 28px; font-weight: 700; color: #1f2937; }
    .stat-info p { margin: 5px 0 0 0; color: #6b7280; font-size: 14px; }

    /* Mapel Section */
    .mapel-section {
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

    /* Mapel Cards */
    .mapel-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
        padding: 25px;
    }
    .mapel-card {
        background: linear-gradient(135deg, #f8faff 0%, #f5f2ff 100%);
        border: 1px solid #e1e8ff;
        border-radius: 16px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .mapel-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .mapel-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(102, 126, 234, 0.2);
        border-color: #667eea;
    }
    .mapel-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }
    .mapel-name { font-size: 1rem; font-weight: 700; color: #1f2937; margin: 0; }
    .mapel-status {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .mapel-status.kompeten { background: #dcfce7; color: #15803d; }
    .mapel-status.perlu_perbaikan { background: #fef3c7; color: #92400e; }
    .mapel-stats {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }
    .mapel-stat {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        background: white;
        border-radius: 8px;
        border: 1px solid #e1e8ff;
        font-size: 0.8rem;
    }
    .mapel-stat i { color: #667eea; }
    .mapel-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 12px;
        border-top: 1px solid #e1e8ff;
    }
    .rata-rata-badge {
        font-size: 1.5rem;
        font-weight: 700;
        padding: 8px 16px;
        border-radius: 10px;
    }
    .rata-rata-badge.high { background: #dcfce7; color: #15803d; }
    .rata-rata-badge.medium { background: #fef3c7; color: #92400e; }
    .rata-rata-badge.low { background: #fee2e2; color: #991b1b; }
    .terakhir-dinilai { font-size: 0.75rem; color: #6b7280; }

    /* Empty State */
    .empty-state { text-align: center; padding: 50px 30px; }
    .empty-icon { width: 80px; height: 80px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
    .empty-icon i { font-size: 32px; color: white; }

    @media (max-width: 768px) {
        .mapel-grid { grid-template-columns: 1fr; padding: 15px; }
        
        /* Stats grid mobile - 3 in a row */
        .stats-grid { 
            grid-template-columns: repeat(3, 1fr) !important; 
            gap: 8px; 
        }
        .stat-card {
            flex-direction: column !important;
            padding: 10px 6px !important;
            gap: 6px !important;
            text-align: center;
        }
        .stat-icon {
            width: 32px !important;
            height: 32px !important;
            font-size: 14px !important;
            margin: 0 auto;
            border-radius: 8px !important;
        }
        .stat-info h3 {
            font-size: 16px !important;
        }
        .stat-info p {
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
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="nilai-header-details">
                <h3>{{ $siswa->nama }}</h3>
                <p>Nilai Harian Saya</p>
                @if($periodik)
                <p style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                    Periode: {{ $periodik->tahun_pelajaran }} - {{ $periodik->semester }}
                </p>
                @endif
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon primary"><i class="fas fa-clipboard-list"></i></div>
                <div class="stat-info">
                    <h3>{{ $totalNilai }}</h3>
                    <p>Total Penilaian</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon success"><i class="fas fa-calculator"></i></div>
                <div class="stat-info">
                    <h3>{{ $rataRataKeseluruhan }}</h3>
                    <p>Rata-rata Nilai</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon warning"><i class="fas fa-medal"></i></div>
                <div class="stat-info">
                    <h3>{{ $mapelKompeten }}/{{ $mapelList->count() }}</h3>
                    <p>Mapel Kompeten</p>
                </div>
            </div>
        </div>

        <!-- Mapel Section -->
        <div class="mapel-section">
            <div class="section-header">
                <h2><i class="fas fa-book" style="color: #667eea;"></i> Nilai Per Mata Pelajaran</h2>
                <span class="badge">{{ $mapelList->count() }} Mapel</span>
            </div>

            @if($mapelList->count() > 0)
            <div class="mapel-grid">
                @foreach($mapelList as $mapel)
                    @php
                        $rataClass = $mapel->rata_rata >= 85 ? 'high' : ($mapel->rata_rata >= 75 ? 'medium' : 'low');
                    @endphp
                    <div class="mapel-card" onclick="window.location='{{ route('siswa.nilai') }}?mapel={{ urlencode(strtolower(str_replace(' ', '_', $mapel->mapel))) }}'">
                        <div class="mapel-card-header">
                            <h4 class="mapel-name">{{ $mapel->mapel }}</h4>
                            <span class="mapel-status {{ $mapel->status }}">
                                {{ $mapel->status == 'kompeten' ? 'Kompeten' : 'Perlu Perbaikan' }}
                            </span>
                        </div>
                        <div class="mapel-stats">
                            <div class="mapel-stat">
                                <i class="fas fa-list-ol"></i>
                                <span>{{ $mapel->total_nilai }} Nilai</span>
                            </div>
                            <div class="mapel-stat">
                                <i class="fas fa-arrow-up"></i>
                                <span>{{ $mapel->tertinggi }}</span>
                            </div>
                            <div class="mapel-stat">
                                <i class="fas fa-arrow-down"></i>
                                <span>{{ $mapel->terendah }}</span>
                            </div>
                        </div>
                        <div class="mapel-footer">
                            <span class="rata-rata-badge {{ $rataClass }}">{{ $mapel->rata_rata }}</span>
                            <span class="terakhir-dinilai">
                                <i class="fas fa-clock"></i> 
                                {{ $mapel->terakhir_dinilai ? \Carbon\Carbon::parse($mapel->terakhir_dinilai)->format('d M Y') : '-' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
            @else
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-clipboard-list"></i></div>
                <h3>Belum Ada Data Nilai</h3>
                <p>Belum ada nilai yang tercatat untuk Anda.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
