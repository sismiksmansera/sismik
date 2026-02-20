@extends('layouts.app')

@section('title', 'Koordinator OSN')

@push('styles')
<style>
/* HEADER */
.osn-header-section {
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    border-radius: 16px;
    padding: 30px 25px;
    text-align: center;
    color: white;
    margin-bottom: 25px;
    position: relative;
    overflow: hidden;
}
.osn-header-section::before {
    content: '';
    position: absolute; top: -50%; right: -30%;
    width: 300px; height: 300px;
    background: rgba(255,255,255,0.06);
    border-radius: 50%;
}
.header-icon-large {
    width: 70px; height: 70px;
    background: rgba(255,255,255,0.15);
    border-radius: 18px;
    display: flex; align-items: center; justify-content: center;
    font-size: 32px; color: white;
    margin: 0 auto 15px;
}
.osn-header-section h1 { font-size: 1.6rem; font-weight: 800; margin-bottom: 6px; }
.osn-header-section p { font-size: 0.9rem; opacity: 0.8; margin-bottom: 12px; }
.header-year-badge {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(255,255,255,0.15);
    padding: 8px 20px; border-radius: 25px;
    font-size: 13px; font-weight: 600;
}

/* BACK BUTTON */
.btn-back {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 20px; border-radius: 10px;
    background: white; color: #6d28d9;
    font-size: 13px; font-weight: 600;
    text-decoration: none; border: none; cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s;
    margin-bottom: 20px;
}
.btn-back:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.12); color: #6d28d9; }

/* STATS */
.stats-row {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 12px; margin-bottom: 25px;
}
.osn-stat-card {
    background: white; border-radius: 12px;
    padding: 18px; text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    transition: all 0.3s;
}
.osn-stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
.osn-stat-value {
    font-size: 28px; font-weight: 800; color: #7c3aed;
}
.osn-stat-label { font-size: 11px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px; }

/* CARD GRID */
.osn-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
}

.osn-card {
    background: white; border-radius: 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    overflow: hidden; transition: all 0.3s;
}
.osn-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }

.osn-card-header {
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    padding: 18px 20px;
    display: flex; align-items: center; gap: 14px;
    color: white;
}
.osn-card-icon {
    width: 44px; height: 44px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; flex-shrink: 0;
}
.osn-card-title { font-size: 15px; font-weight: 700; }
.osn-card-badge {
    display: inline-block; background: rgba(255,255,255,0.2);
    padding: 3px 10px; border-radius: 12px;
    font-size: 10px; font-weight: 600; margin-top: 4px;
}

.osn-card-body { padding: 20px; }

.osn-detail-list { display: flex; flex-direction: column; gap: 10px; margin-bottom: 16px; }
.osn-detail-item {
    display: flex; align-items: center; gap: 10px;
    font-size: 13px; color: #374151;
}
.osn-detail-item i { width: 16px; text-align: center; color: #9ca3af; font-size: 12px; }

.osn-info-grid {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 10px; padding-top: 14px;
    border-top: 1px solid #f3f4f6;
}
.osn-info-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px; border-radius: 10px;
    background: #f9fafb; transition: all 0.2s;
    text-decoration: none;
}
.osn-info-item.clickable:hover { background: #ede9fe; transform: translateY(-1px); }
.osn-info-icon {
    width: 34px; height: 34px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; flex-shrink: 0;
}
.osn-info-icon.blue { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.osn-info-icon.amber { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.osn-info-value { font-size: 18px; font-weight: 700; color: #1e293b; line-height: 1; }
.osn-info-label { font-size: 11px; color: #9ca3af; margin-top: 2px; }

/* EMPTY */
.osn-empty {
    background: white; border-radius: 16px;
    padding: 60px 30px; text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
}
.osn-empty i { font-size: 48px; color: #d1d5db; margin-bottom: 15px; }
.osn-empty h3 { font-size: 18px; color: #374151; margin-bottom: 8px; }
.osn-empty p { font-size: 13px; color: #6b7280; }

@media (max-width: 768px) {
    .osn-header-section { padding: 20px 15px; }
    .osn-header-section h1 { font-size: 1.3rem; }
    .osn-grid { grid-template-columns: 1fr; }
    .stats-row { grid-template-columns: repeat(2, 1fr); }
}
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-guru')

    <div class="main-content">
        <div style="max-width: 1100px; margin: 0 auto; padding: 20px;">

            {{-- BACK BUTTON --}}
            <a href="{{ route('guru.tugas-tambahan') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali ke Tugas Tambahan
            </a>

            {{-- HEADER --}}
            <div class="osn-header-section">
                <div class="header-icon-large">
                    <i class="fas fa-trophy"></i>
                </div>
                <h1>Koordinator OSN</h1>
                <p>Daftar Ajang Olimpiade Sains Nasional</p>
                <div class="header-year-badge">
                    <i class="fas fa-calendar-alt"></i> Tahun {{ $currentYear }}
                </div>
            </div>

            {{-- STATS --}}
            @php
                $totalPeserta = $osnList->sum('jumlah_peserta');
            @endphp
            <div class="stats-row">
                <div class="osn-stat-card">
                    <div class="osn-stat-value">{{ count($osnList) }}</div>
                    <div class="osn-stat-label">Ajang OSN</div>
                </div>
                <div class="osn-stat-card">
                    <div class="osn-stat-value">{{ $totalPeserta }}</div>
                    <div class="osn-stat-label">Total Peserta</div>
                </div>
                <div class="osn-stat-card">
                    <div class="osn-stat-value">{{ $currentYear }}</div>
                    <div class="osn-stat-label">Tahun</div>
                </div>
            </div>

            {{-- CARDS --}}
            @if(count($osnList) == 0)
            <div class="osn-empty">
                <i class="fas fa-trophy"></i>
                <h3>Belum Ada Ajang OSN</h3>
                <p>Belum ada ajang OSN yang terdaftar untuk tahun {{ $currentYear }}.</p>
            </div>
            @else
            <div class="osn-grid">
                @foreach($osnList as $osn)
                <div class="osn-card">
                    <div class="osn-card-header">
                        <div class="osn-card-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div>
                            <div class="osn-card-title">{{ $osn->nama_ajang }}</div>
                            @if($osn->jenis_ajang)
                            <div class="osn-card-badge">{{ $osn->jenis_ajang }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="osn-card-body">
                        <div class="osn-detail-list">
                            @if($osn->tahun)
                            <div class="osn-detail-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>{{ $osn->tahun }}</span>
                            </div>
                            @endif
                            @if($osn->penyelenggara)
                            <div class="osn-detail-item">
                                <i class="fas fa-building"></i>
                                <span>{{ $osn->penyelenggara }}</span>
                            </div>
                            @endif
                            @if($osn->pembina)
                            <div class="osn-detail-item">
                                <i class="fas fa-user-tie"></i>
                                <span>{{ $osn->pembina }}</span>
                            </div>
                            @endif
                        </div>

                        <div class="osn-info-grid">
                            <a href="{{ route('guru.koordinator-osn.peserta', $osn->id) }}" class="osn-info-item clickable">
                                <div class="osn-info-icon blue">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div>
                                    <div class="osn-info-value">{{ $osn->jumlah_peserta ?? 0 }}</div>
                                    <div class="osn-info-label">Peserta</div>
                                </div>
                            </a>
                            <div class="osn-info-item">
                                <div class="osn-info-icon amber">
                                    <i class="fas fa-medal"></i>
                                </div>
                                <div>
                                    <div class="osn-info-value">0</div>
                                    <div class="osn-info-label">Prestasi</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
