@extends('layouts.app')

@section('title', 'Catatan Guru Wali | SISMIK')

@push('styles')
<style>
    /* Header Card */
    .wali-header-card {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border-left: 4px solid #8b5cf6;
        margin-bottom: 20px;
    }
    .wali-header-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
    }
    .wali-header-details h3 { margin: 0; color: #1f2937; font-size: 18px; font-weight: 600; }
    .wali-header-details p { margin: 5px 0 0 0; color: #6b7280; font-size: 14px; }

    /* Stats Grid */
    .stats-grid-horizontal {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: nowrap;
        overflow-x: auto;
    }
    .stat-item-h {
        flex: 1;
        min-width: 90px;
        background: white;
        border-radius: 12px;
        padding: 15px 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }
    .stat-icon-h {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: white;
    }
    .stat-icon-h.total { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
    .stat-icon-h.belum { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .stat-icon-h.sesuai { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .stat-icon-h.baik { background: linear-gradient(135deg, #22c55e, #16a34a); }
    .stat-value-h { font-size: 1.25rem; font-weight: 700; color: #1f2937; }
    .stat-label-h { font-size: 0.7rem; color: #6b7280; white-space: nowrap; text-align: center; }

    /* Catatan Section */
    .catatan-section {
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
        background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
    }
    .section-header h2 { margin: 0; font-size: 1.1rem; color: #4c1d95; display: flex; align-items: center; gap: 10px; }
    .badge { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }

    /* Catatan Card */
    .catatan-list { padding: 20px; display: flex; flex-direction: column; gap: 15px; }
    .catatan-card {
        background: #f8fafc;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }
    .catatan-card:hover { transform: translateY(-2px); box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1); }
    .catatan-card::before { content: ''; display: block; height: 4px; }
    .catatan-card.jenis-akademik::before { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .catatan-card.jenis-karakter::before { background: linear-gradient(135deg, #10b981, #059669); }
    .catatan-card.jenis-sosial::before { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .catatan-card.jenis-disiplin::before { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .catatan-card.jenis-potensi::before { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
    .catatan-card.jenis-ibadah::before { background: linear-gradient(135deg, #06b6d4, #0891b2); }
    .catatan-card.jenis-lainnya::before { background: linear-gradient(135deg, #6b7280, #4b5563); }

    .catatan-card-header {
        padding: 18px 20px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 15px;
        cursor: pointer;
        background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
    }

    .catatan-info { flex: 1; }
    .catatan-info h4 { margin: 0 0 8px 0; color: #1f2937; font-size: 1rem; font-weight: 600; display: flex; align-items: center; gap: 8px; }
    .catatan-meta { display: flex; flex-wrap: wrap; gap: 12px; font-size: 0.8rem; color: #6b7280; }
    .catatan-meta span { display: flex; align-items: center; gap: 5px; }

    .perkembangan-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }
    .perkembangan-badge.belum { background: #fee2e2; color: #991b1b; }
    .perkembangan-badge.sesuai { background: #fef3c7; color: #92400e; }
    .perkembangan-badge.baik { background: #dcfce7; color: #166534; }

    .catatan-card-body {
        padding: 20px;
        background: white;
        display: none;
    }
    .catatan-card.expanded .catatan-card-body { display: block; }
    .catatan-card.expanded .toggle-icon { transform: rotate(180deg); }

    .catatan-detail { margin-bottom: 15px; }
    .catatan-detail:last-child { margin-bottom: 0; }
    .catatan-detail-label { font-weight: 600; color: #374151; font-size: 0.85rem; margin-bottom: 5px; display: flex; align-items: center; gap: 6px; }
    .catatan-detail-value { color: #4b5563; font-size: 0.9rem; line-height: 1.6; padding-left: 22px; }

    .toggle-icon { transition: transform 0.3s ease; color: #6b7280; }

    .nilai-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        font-weight: 700;
        font-size: 14px;
        color: white;
    }
    .nilai-badge.a { background: linear-gradient(135deg, #22c55e, #16a34a); }
    .nilai-badge.b { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .nilai-badge.c { background: linear-gradient(135deg, #ef4444, #dc2626); }

    /* Empty State */
    .empty-state { text-align: center; padding: 50px 30px; }
    .empty-icon { width: 80px; height: 80px; background: linear-gradient(135deg, #8b5cf6, #7c3aed); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
    .empty-icon i { font-size: 32px; color: white; }

    @media (max-width: 768px) {
        .stats-grid-horizontal { gap: 8px; }
        .stat-item-h { min-width: 75px; padding: 12px 8px; }
        .catatan-card-header { flex-direction: column; }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-siswa')

    <div class="main-content">
        <!-- Header -->
        <div class="wali-header-card">
            <div class="wali-header-icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="wali-header-details">
                <h3>{{ $siswa->nama }}</h3>
                <p>Riwayat Catatan Guru Wali</p>
                @if($periodik)
                <p style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                    Periode: {{ $tahunFilter }} - {{ $semesterFilter }}
                </p>
                @endif
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid-horizontal">
            <div class="stat-item-h">
                <div class="stat-icon-h total"><i class="fas fa-clipboard-list"></i></div>
                <div class="stat-value-h">{{ $totalCatatan }}</div>
                <div class="stat-label-h">Total Catatan</div>
            </div>
            <div class="stat-item-h">
                <div class="stat-icon-h belum"><i class="fas fa-exclamation-circle"></i></div>
                <div class="stat-value-h">{{ $perkembanganStats['Belum Berkembang'] }}</div>
                <div class="stat-label-h">Belum Berkembang</div>
            </div>
            <div class="stat-item-h">
                <div class="stat-icon-h sesuai"><i class="fas fa-thumbs-up"></i></div>
                <div class="stat-value-h">{{ $perkembanganStats['Berkembang Sesuai Harapan'] }}</div>
                <div class="stat-label-h">Sesuai Harapan</div>
            </div>
            <div class="stat-item-h">
                <div class="stat-icon-h baik"><i class="fas fa-star"></i></div>
                <div class="stat-value-h">{{ $perkembanganStats['Berkembang Sangat Baik'] }}</div>
                <div class="stat-label-h">Sangat Baik</div>
            </div>
        </div>

        <!-- Catatan Section -->
        <div class="catatan-section">
            <div class="section-header">
                <h2><i class="fas fa-chalkboard-teacher" style="color: #8b5cf6;"></i> Catatan Guru Wali</h2>
                <span class="badge">{{ $totalCatatan }} Catatan</span>
            </div>

            @if($catatanList->count() > 0)
            <div class="catatan-list">
                @foreach($catatanList as $catatan)
                    @php
                        $jenis = strtolower($catatan->jenis_bimbingan ?? 'lainnya');
                        if (str_contains($jenis, 'akademik')) $jenisClass = 'akademik';
                        elseif (str_contains($jenis, 'karakter')) $jenisClass = 'karakter';
                        elseif (str_contains($jenis, 'sosial')) $jenisClass = 'sosial';
                        elseif (str_contains($jenis, 'disiplin')) $jenisClass = 'disiplin';
                        elseif (str_contains($jenis, 'potensi')) $jenisClass = 'potensi';
                        elseif (str_contains($jenis, 'ibadah')) $jenisClass = 'ibadah';
                        else $jenisClass = 'lainnya';

                        $jenisIcon = match($jenisClass) {
                            'akademik' => 'fa-book',
                            'karakter' => 'fa-heart',
                            'sosial' => 'fa-users',
                            'disiplin' => 'fa-gavel',
                            'potensi' => 'fa-lightbulb',
                            'ibadah' => 'fa-pray',
                            default => 'fa-clipboard'
                        };

                        $perkembangan = $catatan->perkembangan ?? 'Belum Berkembang';
                        if ($perkembangan === 'Berkembang Sangat Baik') {
                            $perkClass = 'baik';
                            $perkIcon = 'fa-star';
                        } elseif ($perkembangan === 'Berkembang Sesuai Harapan') {
                            $perkClass = 'sesuai';
                            $perkIcon = 'fa-thumbs-up';
                        } else {
                            $perkClass = 'belum';
                            $perkIcon = 'fa-exclamation-circle';
                        }
                    @endphp
                    <div class="catatan-card jenis-{{ $jenisClass }}" onclick="toggleCard(this)">
                        <div class="catatan-card-header">
                            <div class="catatan-info">
                                <h4>
                                    <i class="fas {{ $jenisIcon }}" style="color: #8b5cf6;"></i>
                                    {{ $catatan->jenis_bimbingan ?? 'Bimbingan' }}
                                </h4>
                                <div class="catatan-meta">
                                    <span><i class="fas fa-calendar-alt"></i> {{ $catatan->tanggal_pencatatan ? $catatan->tanggal_pencatatan->format('d M Y') : '-' }}</span>
                                    <span><i class="fas fa-user-tie"></i> {{ $catatan->guru_nama ?? 'Guru Wali' }}</span>
                                    <span><i class="fas fa-graduation-cap"></i> {{ $catatan->semester }} {{ $catatan->tahun_pelajaran }}</span>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span class="perkembangan-badge {{ $perkClass }}">
                                    <i class="fas {{ $perkIcon }}"></i>
                                    {{ $perkembangan }}
                                </span>
                                <i class="fas fa-chevron-down toggle-icon"></i>
                            </div>
                        </div>
                        <div class="catatan-card-body">
                            <div class="catatan-detail">
                                <div class="catatan-detail-label"><i class="fas fa-pen-alt" style="color: #8b5cf6;"></i> Catatan Guru Wali</div>
                                <div class="catatan-detail-value">{!! nl2br(e($catatan->catatan ?? '-')) !!}</div>
                            </div>
                            @if($catatan->nilai_praktik_ibadah)
                            <div class="catatan-detail">
                                <div class="catatan-detail-label"><i class="fas fa-pray" style="color: #06b6d4;"></i> Nilai Praktik Ibadah</div>
                                <div class="catatan-detail-value">
                                    <span class="nilai-badge {{ strtolower($catatan->nilai_praktik_ibadah) }}">{{ $catatan->nilai_praktik_ibadah }}</span>
                                </div>
                            </div>
                            @endif
                            <div class="catatan-detail">
                                <div class="catatan-detail-label"><i class="fas fa-chart-line" style="color: #10b981;"></i> Perkembangan</div>
                                <div class="catatan-detail-value">
                                    <span class="perkembangan-badge {{ $perkClass }}" style="display: inline-flex;">
                                        <i class="fas {{ $perkIcon }}"></i> {{ $perkembangan }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @else
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <h3>Belum Ada Catatan Guru Wali</h3>
                <p>Saat ini belum ada catatan guru wali yang tercatat untuk Anda<br>pada periode {{ $tahunFilter }} - {{ $semesterFilter }}.</p>
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
