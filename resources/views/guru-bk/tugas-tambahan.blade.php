@extends('layouts.app-guru-bk')

@section('content')
<div class="main-content tugas-tambahan-bk">

    {{-- Header Section --}}
    <div class="header-text">
        <div class="header-icon-large">
            <i class="fas fa-tasks"></i>
        </div>
        <h1 class="page-title-rekap">TUGAS TAMBAHAN</h1>
        <div class="header-periode-badge">
            <i class="fas fa-calendar-alt"></i>
            {{ $periodeAktif->tahun_pelajaran }} - {{ $periodeAktif->semester }}
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-futbol"></i>
            </div>
            <div class="stat-info">
                <h3>{{ count($tugasPembina) }}</h3>
                <p>Pembina Ekstra</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>{{ count($tugasWaliKelas) }}</h3>
                <p>Wali Kelas</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="fas fa-heart"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $totalSiswaBimbinganWali }}</h3>
                <p>Siswa Wali</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="stat-info">
                <h3>{{ count($tugasTambahanLain) }}</h3>
                <p>Tugas Lainnya</p>
            </div>
        </div>
    </div>

    @if($totalTugas > 0)

        {{-- PEMBINA EKSTRAKURIKULER --}}
        @if(count($tugasPembina) > 0)
        <div class="section-container">
            <div class="section-header">
                <div class="section-icon blue">
                    <i class="fas fa-futbol"></i>
                </div>
                <h2>Pembina Ekstrakurikuler</h2>
            </div>

            <div class="task-cards-grid">
                @foreach($tugasPembina as $ekstra)
                @php
                    $icons = [
                        'Pramuka' => 'fa-campground',
                        'Paskibra' => 'fa-flag',
                        'PMR' => 'fa-heartbeat',
                        'OSIS' => 'fa-users-cog',
                        'Basket' => 'fa-basketball-ball',
                        'Futsal' => 'fa-futbol',
                        'Voli' => 'fa-volleyball-ball',
                        'Seni Musik' => 'fa-music',
                        'Seni Tari' => 'fa-gem',
                        'English Club' => 'fa-language',
                        'IT Club' => 'fa-laptop-code',
                        'KIR' => 'fa-flask'
                    ];
                    $colors = [
                        'Pramuka' => '#3b82f6',
                        'Paskibra' => '#ef4444',
                        'PMR' => '#dc2626',
                        'OSIS' => '#8b5cf6',
                        'Basket' => '#f59e0b',
                        'Futsal' => '#10b981',
                        'Voli' => '#ec4899',
                        'Seni Musik' => '#06b6d4',
                        'Seni Tari' => '#f97316',
                        'English Club' => '#6366f1',
                        'IT Club' => '#0ea5e9',
                        'KIR' => '#84cc16'
                    ];
                    $icon = $icons[$ekstra->nama_ekstrakurikuler] ?? 'fa-star';
                    $color = $colors[$ekstra->nama_ekstrakurikuler] ?? '#6b7280';
                @endphp
                <div class="task-card">
                    {{-- Header Card --}}
                    <div class="task-card-header" style="background: linear-gradient(135deg, {{ $color }} 0%, {{ $color }}dd 100%);">
                        <div class="task-card-header-content">
                            <div class="task-card-icon">
                                <i class="fas {{ $icon }}"></i>
                            </div>
                            <div>
                                <h3>{{ $ekstra->nama_ekstrakurikuler }}</h3>
                                <span class="task-badge">
                                    <i class="fas fa-medal"></i>
                                    {{ $ekstra->posisi_pembina }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Body Card --}}
                    <div class="task-card-body">
                        <div class="task-stats-grid">
                            <a href="{{ route('guru_bk.anggota-ekstra', $ekstra->id) }}" class="task-stat-item hoverable" style="--hover-color: {{ $color }}15;">
                                <div class="task-stat-value" style="color: {{ $color }};">{{ $ekstra->jumlah_anggota }}</div>
                                <div class="task-stat-label">Anggota</div>
                            </a>
                            <a href="{{ route('guru_bk.prestasi', ['type' => 'ekstra', 'id' => $ekstra->id]) }}" class="task-stat-item hoverable trophy">
                                <div class="task-stat-value"><i class="fas fa-trophy"></i> {{ $ekstra->jumlah_prestasi }}</div>
                                <div class="task-stat-label">Prestasi</div>
                            </a>
                            <div class="task-stat-item">
                                <div class="task-stat-value-text">{{ $ekstra->semester }}</div>
                                <div class="task-stat-label">Semester</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- WALI KELAS --}}
        @if(count($tugasWaliKelas) > 0)
        <div class="section-container">
            <div class="section-header">
                <div class="section-icon green">
                    <i class="fas fa-users"></i>
                </div>
                <h2>Wali Kelas</h2>
            </div>

            <div class="task-cards-grid">
                @foreach($tugasWaliKelas as $rombel)
                <div class="task-card">
                    {{-- Header Card --}}
                    <div class="task-card-header green">
                        <div class="task-card-header-content">
                            <div class="task-card-icon">
                                <i class="fas fa-chalkboard"></i>
                            </div>
                            <div>
                                <h3>{{ $rombel->nama_rombel }}</h3>
                                <span class="task-badge">
                                    <i class="fas fa-user-tie"></i> Wali Kelas
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Body Card --}}
                    <div class="task-card-body">
                        <div class="task-stats-grid">
                            <a href="#" class="task-stat-item hoverable green-hover">
                                <div class="task-stat-value green">{{ $rombel->jumlah_siswa }}</div>
                                <div class="task-stat-label">Siswa</div>
                            </a>
                            <a href="{{ route('guru_bk.prestasi', ['type' => 'rombel', 'id' => $rombel->id]) }}" class="task-stat-item hoverable trophy">
                                <div class="task-stat-value"><i class="fas fa-trophy"></i> {{ $rombel->jumlah_prestasi }}</div>
                                <div class="task-stat-label">Prestasi</div>
                            </a>
                            <div class="task-stat-item">
                                <div class="task-stat-value-text">Kelas {{ $rombel->tingkat ?? '-' }}</div>
                                <div class="task-stat-label">Tingkat</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- GURU WALI (SISWA BIMBINGAN) --}}
        @if($totalSiswaBimbinganWali > 0)
        <div class="section-container">
            <div class="section-header">
                <div class="section-icon purple">
                    <i class="fas fa-heart"></i>
                </div>
                <h2>Guru Wali (Siswa Bimbingan)</h2>
            </div>

            <div class="task-cards-grid">
                <div class="task-card">
                    {{-- Header Card --}}
                    <div class="task-card-header purple">
                        <div class="task-card-header-content">
                            <div class="task-card-icon">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div>
                                <h3>Siswa Bimbingan</h3>
                                <span class="task-badge">
                                    <i class="fas fa-heart"></i> Guru Wali
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Body Card --}}
                    <div class="task-card-body">
                        <div class="task-stats-grid" style="grid-template-columns: 1fr 1fr;">
                            <a href="{{ route('guru_bk.siswa-wali') }}" class="task-stat-item hoverable purple-hover" style="grid-column: span 2;">
                                <div class="task-stat-value" style="color: #8b5cf6;">{{ $totalSiswaBimbinganWali }}</div>
                                <div class="task-stat-label">Total Siswa Bimbingan</div>
                            </a>
                        </div>
                        @if(count($guruWaliData) > 0)
                        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e5e7eb;">
                            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                @foreach($guruWaliData as $wali)
                                    <span style="background: #f3f4f6; padding: 6px 12px; border-radius: 20px; font-size: 12px; color: #374151;">
                                        <strong>Kelas {{ $wali['tingkat'] }}</strong>: {{ $wali['jumlah'] }} siswa
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- TUGAS TAMBAHAN LAINNYA --}}
        @if(count($tugasTambahanLain) > 0)
        <div class="section-container">
            <div class="section-header">
                <div class="section-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h2>Tugas Tambahan Lainnya</h2>
            </div>

            <div class="task-cards-grid">
                @foreach($tugasTambahanLain as $lainnya)
                <div class="task-card">
                    <div class="task-card-header lainnya">
                        <div class="task-card-header-content">
                            <div class="task-card-icon">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div>
                                <h3>{{ $lainnya->jenis_nama }}</h3>
                                <span class="task-badge">
                                    <i class="fas fa-bookmark"></i> Tugas Tambahan
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="task-card-body">
                        @if($lainnya->extra_count !== null)
                        <a href="{{ route($lainnya->extra_route) }}" style="display: flex; align-items: center; gap: 12px; background: linear-gradient(135deg, #fef3c7, #fffbeb); padding: 14px 16px; border-radius: 12px; border: 1px solid #fcd34d; text-decoration: none; margin-bottom: 12px; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(245,158,11,0.2)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                            <div style="width: 42px; height: 42px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; flex-shrink: 0;">
                                <i class="fas {{ $lainnya->extra_icon }}"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 20px; font-weight: 700; color: #92400e;">{{ $lainnya->extra_count }}</div>
                                <div style="font-size: 11px; color: #b45309; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ $lainnya->extra_label }}</div>
                            </div>
                            <i class="fas fa-chevron-right" style="color: #d97706; font-size: 14px;"></i>
                        </a>
                        @endif
                        @if($lainnya->jenis_deskripsi)
                        <div style="margin-bottom: 12px;">
                            <div style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Deskripsi Tugas</div>
                            <div style="font-size: 13px; color: #374151; line-height: 1.5;">{{ $lainnya->jenis_deskripsi }}</div>
                        </div>
                        @endif
                        @if($lainnya->keterangan)
                        <div style="background: #fffbeb; padding: 10px 14px; border-radius: 10px; border-left: 3px solid #f59e0b;">
                            <div style="font-size: 11px; font-weight: 600; color: #92400e; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px;">Keterangan</div>
                            <div style="font-size: 13px; color: #78350f;">{{ $lainnya->keterangan }}</div>
                        </div>
                        @endif
                        @if($lainnya->extra_count === null && !$lainnya->jenis_deskripsi && !$lainnya->keterangan)
                        <div style="text-align: center; color: #9ca3af; font-size: 13px; padding: 10px 0;">
                            <i class="fas fa-info-circle"></i> Tidak ada keterangan tambahan
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    @else
        {{-- EMPTY STATE --}}
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <h3>Belum Ada Tugas Tambahan</h3>
            <p>
                Anda belum memiliki tugas tambahan seperti pembina ekstrakurikuler atau wali kelas untuk periode
                <strong>{{ $periodeAktif->tahun_pelajaran }} - {{ $periodeAktif->semester }}</strong>
            </p>
            <a href="{{ route('guru_bk.dashboard') }}" class="btn-back-dashboard">
                <i class="fas fa-home"></i> Kembali ke Dashboard
            </a>
        </div>
    @endif

</div>

<style>
/* Main Content */
.tugas-tambahan-bk {
    padding: 30px;
    background: #f8f9fa;
    min-height: calc(100vh - 60px);
}

/* Header */
.tugas-tambahan-bk .header-text {
    text-align: center;
    margin-bottom: 25px;
}

.tugas-tambahan-bk .header-icon-large {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    color: white;
    margin: 0 auto 20px;
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
}

.tugas-tambahan-bk .page-title-rekap {
    font-size: 32px;
    font-weight: 700;
    margin: 0 0 8px 0;
    color: #059669;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.header-periode-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    border: 1px solid rgba(16, 185, 129, 0.2);
}

/* Stats Grid */
.tugas-tambahan-bk .stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
    margin-bottom: 25px;
}

.tugas-tambahan-bk .stat-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    gap: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #e5e7eb;
}

.tugas-tambahan-bk .stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
    border-color: #10b981;
}

.tugas-tambahan-bk .stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
    flex-shrink: 0;
}

.tugas-tambahan-bk .stat-icon.primary { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.tugas-tambahan-bk .stat-icon.success { background: linear-gradient(135deg, #10b981, #059669); }
.tugas-tambahan-bk .stat-icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
.tugas-tambahan-bk .stat-icon.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

.tugas-tambahan-bk .stat-info h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: #1f2937;
}

.tugas-tambahan-bk .stat-info p {
    margin: 4px 0 0 0;
    color: #6b7280;
    font-size: 12px;
}

/* Section Container */
.section-container {
    margin-bottom: 30px;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.section-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.section-icon.blue { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
.section-icon.green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.section-icon.purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }

.section-header h2 {
    margin: 0;
    color: #1f2937;
    font-size: 1.25rem;
}

/* Task Cards Grid */
.task-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
}

/* Task Card */
.task-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb;
}

.task-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

.task-card-header {
    padding: 20px;
    color: white;
}

.task-card-header.green {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.task-card-header.purple {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
}

.task-card-header.lainnya {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.task-card-header-content {
    display: flex;
    align-items: center;
    gap: 15px;
}

.task-card-icon {
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.task-card-header h3 {
    margin: 0 0 5px 0;
    font-size: 1.1rem;
}

.task-badge {
    background: rgba(255,255,255,0.25);
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.task-card-body {
    padding: 20px;
}

.task-stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 15px;
}

.task-stat-item {
    background: #f8fafc;
    padding: 12px;
    border-radius: 10px;
    text-align: center;
    text-decoration: none;
    transition: all 0.2s ease;
}

.task-stat-item.hoverable:hover {
    transform: translateY(-2px);
}

.task-stat-item.trophy:hover {
    background: #fef3c7;
}

.task-stat-item.green-hover:hover {
    background: rgba(16, 185, 129, 0.15);
}

.task-stat-item.purple-hover:hover {
    background: rgba(139, 92, 246, 0.15);
}

.task-stat-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: #f59e0b;
}

.task-stat-value.green { color: #10b981; }

.task-stat-value-text {
    font-size: 0.9rem;
    font-weight: 600;
    color: #1f2937;
}

.task-stat-label {
    font-size: 0.8rem;
    color: #6b7280;
    margin-top: 4px;
}

/* Empty State */
.empty-state {
    background: white;
    border-radius: 16px;
    padding: 60px 30px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.empty-state .empty-icon {
    width: 100px;
    height: 100px;
    background: #f3f4f6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 25px;
}

.empty-state .empty-icon i {
    font-size: 40px;
    color: #9ca3af;
}

.empty-state h3 {
    margin: 0 0 10px 0;
    color: #1f2937;
    font-size: 1.25rem;
}

.empty-state p {
    margin: 0 0 25px 0;
    color: #6b7280;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

.btn-back-dashboard {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #10b981;
    color: white;
    padding: 12px 25px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-back-dashboard:hover {
    background: #059669;
    transform: translateY(-2px);
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .tugas-tambahan-bk {
        padding: 20px;
    }

    .tugas-tambahan-bk .header-icon-large {
        width: 70px;
        height: 70px;
        font-size: 30px;
    }

    .tugas-tambahan-bk .page-title-rekap {
        font-size: 24px;
    }

    .tugas-tambahan-bk .stats-grid {
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }

    .tugas-tambahan-bk .stat-card {
        flex: 1;
        flex-direction: column;
        text-align: center;
        padding: 12px 8px;
        gap: 8px;
    }

    .tugas-tambahan-bk .stat-icon {
        width: 35px;
        height: 35px;
        font-size: 14px;
    }

    .task-cards-grid {
        grid-template-columns: 1fr;
    }

    .task-card-header h3 {
        font-size: 0.95rem;
    }

    .task-stat-value {
        font-size: 1rem;
    }

    .task-stat-label {
        font-size: 0.7rem;
    }
}

@media (max-width: 480px) {
    .tugas-tambahan-bk .stats-grid {
        gap: 6px;
    }

    .tugas-tambahan-bk .stat-card {
        padding: 10px 6px;
    }

    .tugas-tambahan-bk .page-title-rekap {
        font-size: 20px;
    }
}
</style>
@endsection
