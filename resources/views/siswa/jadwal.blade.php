@extends('layouts.app')

@section('title', 'Jadwal Pelajaran | SISMIK')

@push('styles')
<style>
    /* Header Card */
    .jadwal-header-card {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border-left: 4px solid #10b981;
        margin-bottom: 20px;
    }
    .jadwal-header-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        flex-shrink: 0;
    }
    .jadwal-header-details h3 {
        margin: 0;
        color: #1f2937;
        font-size: 18px;
        font-weight: 600;
    }
    .jadwal-header-details p {
        margin: 5px 0 0 0;
        color: #6b7280;
        font-size: 14px;
    }

    /* Section Card */
    .jadwal-section {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }
    .section-header {
        padding: 20px 25px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .section-header h2 {
        margin: 0;
        color: #1f2937;
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .badge {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    /* Jadwal Cards Grid */
    .jadwal-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
        padding: 25px;
    }

    /* Day Card */
    .day-card {
        background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
        border: 1px solid #a7f3d0;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.1);
        overflow: hidden;
        position: relative;
    }
    .day-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    .day-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 20px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        cursor: pointer;
    }
    .day-card-header:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
    }
    .day-card-header h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
    }
    .toggle-icon {
        transition: transform 0.3s ease;
    }
    .day-card.collapsed .toggle-icon {
        transform: rotate(-90deg);
    }

    /* Stats Row */
    .stats-row {
        display: flex;
        gap: 8px;
        padding: 12px 20px;
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
    }
    .stat-icon {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: white;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    .stat-value {
        font-size: 14px;
        font-weight: 700;
        color: #1f2937;
    }
    .stat-label {
        font-size: 9px;
        color: #6b7280;
        text-transform: uppercase;
    }

    /* Day Card Content */
    .day-card-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease;
        opacity: 0;
    }
    .day-card.expanded .day-card-content {
        max-height: 2000px;
        opacity: 1;
    }
    .day-card-body {
        padding: 15px 20px;
    }
    .schedule-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .schedule-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        background: white;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
    }
    .schedule-item.empty {
        opacity: 0.5;
    }
    .jam-badge {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        min-width: 70px;
        text-align: center;
    }
    .jam-badge.empty {
        background: #9ca3af;
    }
    .schedule-details {
        flex: 1;
    }
    .schedule-mapel {
        font-weight: 600;
        color: #1f2937;
        font-size: 14px;
    }
    .schedule-guru {
        font-size: 12px;
        color: #6b7280;
        margin-top: 2px;
    }
    .status-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .status-badge.active {
        background: #dcfce7;
        color: #15803d;
    }
    .status-badge.empty {
        background: #f3f4f6;
        color: #6b7280;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 50px 30px;
    }
    .empty-state .empty-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }
    .empty-state .empty-icon i {
        font-size: 32px;
        color: white;
    }
    .empty-state h3 {
        margin: 0 0 10px 0;
        color: #1f2937;
    }
    .empty-state p {
        margin: 0;
        color: #6b7280;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .jadwal-cards-grid {
            grid-template-columns: 1fr;
            padding: 15px;
            gap: 15px;
        }
        /* Stats row mobile - 3 in a row */
        .stats-row {
            flex-wrap: nowrap !important;
            gap: 6px;
            padding: 10px 12px;
        }
        .stat-item {
            flex-direction: column !important;
            padding: 8px 4px !important;
            gap: 4px !important;
            text-align: center;
            min-width: unset !important;
            flex: 1;
        }
        .stat-icon {
            width: 28px !important;
            height: 28px !important;
            font-size: 10px !important;
            margin: 0 auto;
        }
        .stat-value {
            font-size: 14px !important;
        }
        .stat-label {
            font-size: 8px !important;
        }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-siswa')

    <div class="main-content">
        <!-- Header -->
        <div class="jadwal-header-card">
            <div class="jadwal-header-icon">
                <i class="fas fa-calendar-week"></i>
            </div>
            <div class="jadwal-header-details">
                <h3>{{ $siswa->nama }}</h3>
                <p>Rombel: {{ $namaRombel ?? '-' }} | NISN: {{ $siswa->nisn }}</p>
                @if($periodik)
                <p style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                    Periode: {{ $periodik->tahun_pelajaran }} - {{ $periodik->semester }}
                </p>
                @endif
            </div>
        </div>

        <!-- Jadwal Section -->
        <div class="jadwal-section">
            <div class="section-header">
                <h2><i class="fas fa-calendar-week"></i> Jadwal Pelajaran Mingguan</h2>
                <span class="badge">{{ $totalMapel }} Mapel</span>
            </div>

            {{-- Debug Panel - Remove after fixing --}}
            @if(isset($debug) && config('app.debug'))
            <div style="background: #fef3c7; border: 1px solid #f59e0b; padding: 15px; margin: 15px; border-radius: 8px; font-size: 12px;">
                <strong style="color: #b45309;"><i class="fas fa-bug"></i> Debug Info:</strong>
                <pre style="margin: 10px 0 0 0; background: white; padding: 10px; border-radius: 4px; overflow-x: auto;">{{ json_encode($debug, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            @endif

            @if(!empty($jadwalPerHari) && $namaRombel)
                <div class="jadwal-cards-grid">
                    @foreach($hariList as $hari)
                        @php
                            $jadwalHari = $jadwalPerHari[$hari] ?? [];
                            $jumlahJam = 0;
                            $mapelHari = [];
                            $guruHari = [];
                            foreach ($jadwalHari as $jam) {
                                if ($jam !== null) {
                                    $jumlahJam++;
                                    if (!in_array($jam['mapel'], $mapelHari)) $mapelHari[] = $jam['mapel'];
                                    if (!in_array($jam['guru'], $guruHari)) $guruHari[] = $jam['guru'];
                                }
                            }
                        @endphp
                        <div class="day-card collapsed" onclick="toggleDayCard(this)">
                            <div class="day-card-header">
                                <h4>{{ $hari }}</h4>
                                <i class="fas fa-chevron-down toggle-icon"></i>
                            </div>
                            
                            <div class="stats-row">
                                <div class="stat-item">
                                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                                    <div>
                                        <div class="stat-value">{{ $jumlahJam }}</div>
                                        <div class="stat-label">Jam</div>
                                    </div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-icon"><i class="fas fa-book"></i></div>
                                    <div>
                                        <div class="stat-value">{{ count($mapelHari) }}</div>
                                        <div class="stat-label">Mapel</div>
                                    </div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);"><i class="fas fa-chalkboard-teacher"></i></div>
                                    <div>
                                        <div class="stat-value">{{ count($guruHari) }}</div>
                                        <div class="stat-label">Guru</div>
                                    </div>
                                </div>
                            </div>

                            <div class="day-card-content">
                                <div class="day-card-body">
                                    <div class="schedule-list">
                                        @for($jamKe = 1; $jamKe <= 11; $jamKe++)
                                            @php $jadwal = $jadwalHari[$jamKe] ?? null; @endphp
                                            <div class="schedule-item {{ $jadwal ? '' : 'empty' }}">
                                                <div class="jam-badge {{ $jadwal ? '' : 'empty' }}">Jam {{ $jamKe }}</div>
                                                <div class="schedule-details">
                                                    @if($jadwal)
                                                        <div class="schedule-mapel">{{ $jadwal['mapel'] }}</div>
                                                        <div class="schedule-guru"><i class="fas fa-user"></i> {{ $jadwal['guru'] }}</div>
                                                    @else
                                                        <div class="schedule-mapel" style="color: #9ca3af;">Tidak ada jadwal</div>
                                                    @endif
                                                </div>
                                                <span class="status-badge {{ $jadwal ? 'active' : 'empty' }}">
                                                    {{ $jadwal ? 'Aktif' : 'Kosong' }}
                                                </span>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <h3>{{ $namaRombel ? 'Belum Ada Jadwal' : 'Rombel Tidak Ditemukan' }}</h3>
                    <p>
                        @if($namaRombel)
                            Tidak ada jadwal pelajaran yang ditemukan untuk rombel <strong>{{ $namaRombel }}</strong>.
                        @else
                            Tidak dapat menampilkan jadwal karena rombel tidak ditemukan. Silakan hubungi administrator.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleDayCard(card) {
        card.classList.toggle('collapsed');
        card.classList.toggle('expanded');
    }
</script>
@endpush
