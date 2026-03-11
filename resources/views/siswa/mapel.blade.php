@extends('layouts.app')

@section('title', 'Mata Pelajaran | SISMIK')

@push('styles')
<style>
    /* Header Card */
    .mapel-header {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border-left: 4px solid #10b981;
        margin-bottom: 25px;
    }
    .mapel-header-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
    }
    .mapel-header-details h3 { margin: 0; color: #1f2937; font-size: 18px; font-weight: 600; }
    .mapel-header-details p { margin: 5px 0 0 0; color: #6b7280; font-size: 14px; }

    /* Stats Grid */
    .stats-mini {
        display: flex;
        gap: 15px;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }
    .stat-mini-card {
        background: white;
        padding: 18px 22px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        gap: 15px;
        flex: 1;
        min-width: 180px;
    }
    .stat-mini-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: white;
    }
    .stat-mini-icon.primary { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .stat-mini-icon.success { background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); }
    .stat-mini-info h4 { margin: 0; font-size: 1.5rem; font-weight: 700; color: #1f2937; }
    .stat-mini-info p { margin: 3px 0 0 0; color: #6b7280; font-size: 0.85rem; }

    /* Mapel Section */
    .mapel-section {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .section-header {
        padding: 20px 25px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .section-header h2 { margin: 0; color: #1f2937; font-size: 1.1rem; font-weight: 600; display: flex; align-items: center; gap: 10px; }
    .badge { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }

    /* Mapel Cards */
    .mapel-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 20px;
        padding: 25px;
    }

    .mapel-card {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 1px solid #bbf7d0;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.1);
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
    }
    .mapel-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .mapel-card:hover { transform: translateY(-4px); box-shadow: 0 8px 30px rgba(16, 185, 129, 0.2); border-color: #10b981; }

    .mapel-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 20px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        cursor: pointer;
    }
    .mapel-card-header:hover { background: linear-gradient(135deg, #0d9488 0%, #047857 100%); }
    .mapel-card-header h4 { margin: 0 0 4px 0; font-size: 1rem; font-weight: 700; }
    .mapel-card-header .guru { font-size: 0.8rem; opacity: 0.9; }
    .toggle-icon { transition: transform 0.3s ease; margin-left: auto; }
    .mapel-card.expanded .toggle-icon { transform: rotate(180deg); }

    /* Stats Row */
    .stats-row {
        display: flex;
        gap: 8px;
        padding: 15px 20px;
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border-bottom: 1px solid #a7f3d0;
    }
    .stat-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: white;
        border-radius: 8px;
        border: 1px solid #a7f3d0;
        flex: 1;
        min-width: 0;
    }
    .stat-item-icon {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: white;
        flex-shrink: 0;
    }
    .stat-item:nth-child(1) .stat-item-icon { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .stat-item:nth-child(2) .stat-item-icon { background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); }
    .stat-item:nth-child(3) .stat-item-icon { background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); }
    .stat-item-info .value { font-size: 14px; font-weight: 700; color: #1f2937; }
    .stat-item-info .label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }

    /* Card Content */
    .mapel-card-content { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; }
    .mapel-card.expanded .mapel-card-content { max-height: 500px; }
    .mapel-card-body { padding: 20px; background: white; }

    .jadwal-header { display: flex; align-items: center; gap: 8px; padding-bottom: 10px; border-bottom: 1px solid #d1fae5; color: #059669; font-weight: 600; font-size: 14px; margin-bottom: 15px; }
    .jadwal-list { display: flex; flex-direction: column; gap: 10px; }
    .jadwal-item {
        background: #f0fdf4;
        border: 1px solid #a7f3d0;
        border-radius: 10px;
        padding: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .jadwal-item:hover { background: #dcfce7; border-color: #10b981; }
    .jadwal-hari { font-weight: 600; color: #1f2937; }
    .jadwal-jam { padding: 5px 10px; background: #10b981; color: white; border-radius: 15px; font-size: 0.8rem; font-weight: 600; }

    .mapel-card-footer {
        padding: 15px 20px;
        background: #f8fafc;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
    }
    .btn-lihat-nilai {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 18px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn-lihat-nilai:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3); }

    /* Empty State */
    .empty-state { text-align: center; padding: 60px 30px; }
    .empty-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
    }
    .empty-icon i { font-size: 40px; color: white; }

    @media (max-width: 768px) {
        .mapel-grid { grid-template-columns: 1fr; }
        
        /* Stats mini mobile - 2 in a row */
        .stats-mini { 
            flex-direction: row !important; 
            gap: 8px;
        }
        .stat-mini-card {
            flex-direction: column !important;
            padding: 10px 6px !important;
            gap: 6px !important;
            text-align: center;
            min-width: unset !important;
            flex: 1;
        }
        .stat-mini-icon {
            width: 32px !important;
            height: 32px !important;
            font-size: 14px !important;
            margin: 0 auto;
        }
        .stat-mini-info h4 {
            font-size: 16px !important;
        }
        .stat-mini-info p {
            font-size: 9px !important;
        }
        
        /* Stats row inside mapel card - 3 in a row */
        .stats-row {
            flex-wrap: nowrap !important;
            gap: 4px;
            padding: 10px 12px;
        }
        .stat-item {
            flex-direction: column !important;
            padding: 6px 4px !important;
            gap: 3px !important;
            text-align: center;
        }
        .stat-item-icon {
            width: 24px !important;
            height: 24px !important;
            font-size: 10px !important;
            margin: 0 auto;
        }
        .stat-item-info .value {
            font-size: 12px !important;
        }
        .stat-item-info .label {
            font-size: 7px !important;
        }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-siswa')

    <div class="main-content">
        <!-- Header -->
        <div class="mapel-header">
            <div class="mapel-header-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="mapel-header-details">
                <h3>{{ $siswa->nama }}</h3>
                <p>Rombel: {{ $namaRombel ?: '-' }} | NISN: {{ $siswa->nisn }}</p>
                <p style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                    Periode: {{ $tahunAktif }} - {{ $semesterAktif }}
                </p>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-mini">
            <div class="stat-mini-card">
                <div class="stat-mini-icon primary"><i class="fas fa-book"></i></div>
                <div class="stat-mini-info">
                    <h4>{{ $totalMapel }}</h4>
                    <p>Mata Pelajaran</p>
                </div>
            </div>
            <div class="stat-mini-card">
                <div class="stat-mini-icon success"><i class="fas fa-clock"></i></div>
                <div class="stat-mini-info">
                    <h4>{{ $totalJamMinggu }}</h4>
                    <p>Jam/Minggu</p>
                </div>
            </div>
        </div>

        <!-- Mapel Section -->
        <div class="mapel-section">
            <div class="section-header">
                <h2><i class="fas fa-book-open" style="color: #10b981;"></i> Daftar Mata Pelajaran</h2>
                <span class="badge">{{ $totalMapel }} Mapel</span>
            </div>

            @if($idRombel && $mapelList->count() > 0)
            <div class="mapel-grid">
                @foreach($mapelList as $mapel)
                <div class="mapel-card" onclick="toggleCard(this)">
                    <div class="mapel-card-header">
                        <div>
                            <h4>{{ $mapel->nama_mapel }}</h4>
                            <div class="guru">Guru: {{ $mapel->nama_guru }}</div>
                        </div>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>

                    <div class="stats-row">
                        <div class="stat-item">
                            <div class="stat-item-icon"><i class="fas fa-calendar"></i></div>
                            <div class="stat-item-info">
                                <div class="value">{{ $mapel->total_hari }}</div>
                                <div class="label">Hari</div>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-item-icon"><i class="fas fa-clock"></i></div>
                            <div class="stat-item-info">
                                <div class="value">{{ $mapel->total_jam }}</div>
                                <div class="label">Jam/Minggu</div>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-item-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                            <div class="stat-item-info">
                                <div class="value">{{ count($mapel->jadwal_detail) }}</div>
                                <div class="label">Sesi</div>
                            </div>
                        </div>
                    </div>

                    <div class="mapel-card-content">
                        <div class="mapel-card-body">
                            <div class="jadwal-header">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Jadwal Pelajaran</span>
                            </div>
                            <div class="jadwal-list">
                                @forelse($mapel->jadwal_detail as $jadwal)
                                <div class="jadwal-item">
                                    <span class="jadwal-hari">{{ $jadwal->hari }}</span>
                                    <span class="jadwal-jam">Jam ke-{{ $jadwal->jam_list }}</span>
                                </div>
                                @empty
                                <div class="jadwal-item">
                                    <span style="color: #6b7280;">Belum ada jadwal</span>
                                </div>
                                @endforelse
                            </div>
                        </div>
                        <div class="mapel-card-footer">
                            <a href="{{ route('siswa.nilai', ['mapel' => strtolower(str_replace(' ', '_', $mapel->nama_mapel))]) }}" class="btn-lihat-nilai">
                                <i class="fas fa-chart-line"></i> Lihat Nilai
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @elseif(!$idRombel)
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <h3>Rombel Tidak Ditemukan</h3>
                <p>Tidak dapat menentukan rombel aktif untuk siswa <strong>{{ $siswa->nama }}</strong>.<br>
                <small style="color: #6b7280;">Silakan hubungi administrator untuk verifikasi data rombel.</small></p>
            </div>
            @else
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-book"></i></div>
                <h3>Belum Ada Mata Pelajaran</h3>
                <p>Tidak ada mata pelajaran aktif untuk rombel <strong>{{ $namaRombel }}</strong> pada periode {{ $tahunAktif }} - {{ $semesterAktif }}.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleCard(card) {
    card.classList.toggle('expanded');
}
</script>
@endpush
