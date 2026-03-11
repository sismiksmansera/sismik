@extends('layouts.app')

@section('title', 'Pelanggaran | SISMIK')

@push('styles')
<style>
    /* Header Card */
    .pelanggaran-header-card {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border-left: 4px solid #ef4444;
        margin-bottom: 20px;
    }
    .pelanggaran-header-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        flex-shrink: 0;
    }
    .pelanggaran-header-details h3 { margin: 0; color: #1f2937; font-size: 18px; font-weight: 600; }
    .pelanggaran-header-details p { margin: 5px 0 0 0; color: #6b7280; font-size: 14px; }

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
        min-width: 100px;
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
    .stat-icon-h.total { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .stat-icon-h.jenis { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .stat-value-h { font-size: 1.25rem; font-weight: 700; color: #1f2937; }
    .stat-label-h { font-size: 0.75rem; color: #6b7280; white-space: nowrap; text-align: center; }

    /* Pelanggaran Section */
    .pelanggaran-section {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    .section-header {
        padding: 20px 25px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }
    .section-header h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-header h4 i { color: #ef4444; }
    .section-badge {
        background: #fef2f2;
        color: #ef4444;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
    }

    /* Pelanggaran Card */
    .pelanggaran-list {
        padding: 15px 20px;
    }
    .pelanggaran-card {
        background: #fafafa;
        border-radius: 12px;
        padding: 18px;
        margin-bottom: 12px;
        border: 1px solid #f0f0f0;
        transition: all 0.2s ease;
    }
    .pelanggaran-card:hover {
        border-color: #fecaca;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.08);
    }
    .pelanggaran-card:last-child { margin-bottom: 0; }

    .pelanggaran-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
        flex-wrap: wrap;
        gap: 8px;
    }
    .pelanggaran-date {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #6b7280;
    }
    .pelanggaran-date i { color: #9ca3af; }
    .pelanggaran-jenis-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .badge-terlambat { background: #fef3c7; color: #92400e; }
    .badge-seragam { background: #fce7f3; color: #9d174d; }
    .badge-gadget { background: #ede9fe; color: #5b21b6; }
    .badge-merokok { background: #fee2e2; color: #991b1b; }
    .badge-bolos { background: #ffedd5; color: #9a3412; }
    .badge-default { background: #f3f4f6; color: #374151; }

    .pelanggaran-card-body {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .pelanggaran-detail-row {
        display: flex;
        gap: 10px;
        font-size: 13.5px;
    }
    .pelanggaran-detail-row .label {
        min-width: 80px;
        color: #9ca3af;
        font-weight: 500;
        flex-shrink: 0;
    }
    .pelanggaran-detail-row .value {
        color: #374151;
        line-height: 1.5;
    }

    .sanksi-box {
        background: #fef2f2;
        border-radius: 8px;
        padding: 10px 14px;
        margin-top: 6px;
        border-left: 3px solid #ef4444;
    }
    .sanksi-box .label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #ef4444;
        font-weight: 700;
        margin-bottom: 4px;
    }
    .sanksi-box .value {
        font-size: 13px;
        color: #991b1b;
        line-height: 1.5;
    }

    .guru-bk-info {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
        font-size: 12px;
        color: #9ca3af;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #9ca3af;
    }
    .empty-icon {
        font-size: 48px;
        margin-bottom: 16px;
        color: #d1d5db;
    }
    .empty-state h4 {
        margin: 0 0 8px 0;
        font-size: 16px;
        color: #6b7280;
        font-weight: 600;
    }
    .empty-state p {
        margin: 0;
        font-size: 14px;
    }

    /* Responsive */
    @media (max-width: 640px) {
        .pelanggaran-header-card { flex-direction: column; text-align: center; }
        .section-header { flex-direction: column; align-items: flex-start; }
        .pelanggaran-card { padding: 14px; }
        .pelanggaran-detail-row { flex-direction: column; gap: 2px; }
        .pelanggaran-detail-row .label { min-width: unset; }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-siswa')

    <div class="main-content">
<!-- Header -->
<div class="pelanggaran-header-card">
    <div class="pelanggaran-header-icon">
        <i class="fas fa-exclamation-triangle"></i>
    </div>
    <div class="pelanggaran-header-details">
        <h3>Riwayat Pelanggaran</h3>
        <p>{{ $siswa->nama }} &bull; {{ $tahunAktif }} — Semester {{ $semesterAktif }}</p>
    </div>
</div>

<!-- Stats -->
<div class="stats-grid-horizontal">
    <div class="stat-item-h">
        <div class="stat-icon-h total"><i class="fas fa-exclamation-circle"></i></div>
        <div class="stat-value-h">{{ $totalPelanggaran }}</div>
        <div class="stat-label-h">Total Pelanggaran</div>
    </div>
    @php
        $topJenis = $jenisCounts->sortDesc()->take(3);
    @endphp
    @foreach($topJenis as $jenis => $count)
    <div class="stat-item-h">
        <div class="stat-icon-h jenis"><i class="fas fa-tag"></i></div>
        <div class="stat-value-h">{{ $count }}</div>
        <div class="stat-label-h">{{ Str::limit($jenis, 15) }}</div>
    </div>
    @endforeach
</div>

<!-- Pelanggaran List -->
<div class="pelanggaran-section">
    <div class="section-header">
        <h4><i class="fas fa-list-ul"></i> Daftar Pelanggaran</h4>
        <span class="section-badge">{{ $totalPelanggaran }} catatan</span>
    </div>

    <div class="pelanggaran-list">
        @forelse($pelanggaranList as $item)
            @php
                $jenis = $item->jenis_pelanggaran;
                $jenisLower = strtolower($jenis);
                if (str_contains($jenisLower, 'terlambat')) $badgeClass = 'badge-terlambat';
                elseif (str_contains($jenisLower, 'seragam') || str_contains($jenisLower, 'atribut')) $badgeClass = 'badge-seragam';
                elseif (str_contains($jenisLower, 'gadget') || str_contains($jenisLower, 'hp')) $badgeClass = 'badge-gadget';
                elseif (str_contains($jenisLower, 'merokok')) $badgeClass = 'badge-merokok';
                elseif (str_contains($jenisLower, 'bolos')) $badgeClass = 'badge-bolos';
                else $badgeClass = 'badge-default';

                $jenisDisplay = $jenis === 'Lainnya' && $item->jenis_lainnya ? $item->jenis_lainnya : $jenis;
            @endphp
            <div class="pelanggaran-card">
                <div class="pelanggaran-card-header">
                    <div class="pelanggaran-date">
                        <i class="fas fa-calendar-day"></i>
                        {{ $item->tanggal ? $item->tanggal->translatedFormat('l, d F Y') : '-' }}
                        @if($item->waktu)
                            &bull; <i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }}
                        @endif
                    </div>
                    <span class="pelanggaran-jenis-badge {{ $badgeClass }}">
                        <i class="fas fa-tag"></i> {{ $jenisDisplay }}
                    </span>
                </div>

                <div class="pelanggaran-card-body">
                    @if($item->deskripsi)
                    <div class="pelanggaran-detail-row">
                        <span class="label">Deskripsi</span>
                        <span class="value">{{ $item->deskripsi }}</span>
                    </div>
                    @endif

                    @if($item->sanksi)
                    <div class="sanksi-box">
                        <div class="label"><i class="fas fa-gavel"></i> Sanksi</div>
                        <div class="value">{{ $item->sanksi }}</div>
                    </div>
                    @endif

                    @if($item->guruBk)
                    <div class="guru-bk-info">
                        <i class="fas fa-user-tie"></i>
                        Dicatat oleh: {{ $item->guruBk->nama ?? '-' }}
                    </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-check-circle"></i></div>
                <h4>Tidak Ada Pelanggaran</h4>
                <p>Belum ada catatan pelanggaran pada periode ini. Pertahankan!</p>
            </div>
        @endforelse
    </div>
</div>
    </div>
</div>
@endsection
