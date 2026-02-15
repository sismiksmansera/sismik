@extends('layouts.app')

@section('title', 'Catatan Piket KBM | SISMIK')

@push('styles')
<style>
    /* Header */
    .catatan-header {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        border-radius: 16px;
        padding: 24px;
        color: white;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
    }
    .catatan-header .header-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .catatan-header .header-icon {
        width: 56px; height: 56px;
        background: rgba(255,255,255,0.2);
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px;
    }
    .catatan-header h2 { margin: 0; font-size: 20px; font-weight: 700; }
    .catatan-header .header-sub { margin: 4px 0 0; font-size: 13px; opacity: 0.85; }
    .header-badges { display: flex; gap: 8px; flex-wrap: wrap; }
    .header-badge {
        background: rgba(255,255,255,0.2);
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .header-badge.testing { background: rgba(245,158,11,0.3); }
    .btn-back-header {
        background: rgba(255,255,255,0.2);
        color: white;
        border: none;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .btn-back-header:hover { background: rgba(255,255,255,0.3); color: white; }

    /* Piket Team */
    .piket-team-card {
        background: white;
        border-radius: 14px;
        padding: 16px 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        margin-bottom: 24px;
        border: 1px solid #e5e7eb;
    }
    .piket-team-label {
        font-size: 12px; font-weight: 600; color: #6b7280;
        text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;
    }
    .piket-team-list { display: flex; gap: 10px; flex-wrap: wrap; }
    .piket-team-item {
        display: flex; align-items: center; gap: 8px;
        background: #f8fafc; padding: 8px 14px; border-radius: 10px;
        border: 1px solid #e2e8f0; font-size: 13px; color: #1f2937;
    }
    .piket-team-item.is-me { background: #eff6ff; border-color: #93c5fd; font-weight: 600; }
    .team-dot { width: 8px; height: 8px; border-radius: 50%; }
    .team-dot.guru { background: #10b981; }
    .team-dot.guru_bk { background: #8b5cf6; }

    /* Jam Card (collapsible) */
    .jam-card {
        background: white;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        margin-bottom: 14px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
        transition: box-shadow 0.2s, background 0.3s, border-color 0.3s;
    }
    /* Jam card states */
    .jam-card.jam-none { background: #f9fafb; border-color: #d1d5db; }
    .jam-card.jam-none .jam-number { background: linear-gradient(135deg, #9ca3af, #6b7280); }
    .jam-card.jam-partial { background: #fffef5; border-color: #fbbf24; }
    .jam-card.jam-partial .jam-number { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .jam-card.jam-complete { background: #f0fdf4; border-color: #86efac; }
    .jam-card.jam-complete .jam-number { background: linear-gradient(135deg, #10b981, #059669); }
    .jam-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.1); }
    .jam-card-header {
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        user-select: none;
        transition: background 0.15s;
    }
    .jam-card-header:hover { background: #f8fafc; }
    .jam-card-header .jam-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .jam-number {
        width: 40px; height: 40px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; font-weight: 800;
    }
    .jam-title { font-size: 15px; font-weight: 600; color: #1f2937; }
    .jam-time { font-size: 12px; color: #9ca3af; margin-top: 2px; }
    .jam-stats { display: flex; gap: 8px; align-items: center; }
    .jam-stat-badge {
        font-size: 11px;
        padding: 3px 10px;
        border-radius: 12px;
        font-weight: 600;
    }
    .jam-stat-badge.rombel-count { background: #eff6ff; color: #3b82f6; }
    .jam-stat-badge.guru-count { background: #f0fdf4; color: #16a34a; }
    .jam-chevron {
        font-size: 14px;
        color: #9ca3af;
        transition: transform 0.3s;
        margin-left: 12px;
    }
    .jam-card.open .jam-chevron { transform: rotate(180deg); }
    .jam-card-body {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease;
    }
    .jam-card.open .jam-card-body { max-height: 8000px; }
    .jam-card-inner { padding: 0 20px 20px; }

    /* Rombel Block */
    .rombel-block {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        margin-bottom: 10px;
        overflow: hidden;
    }
    .rombel-block:last-child { margin-bottom: 0; }
    .rombel-header {
        padding: 12px 16px;
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #e5e7eb;
    }
    .rombel-name {
        font-size: 14px;
        font-weight: 700;
        color: #1e40af;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .rombel-name i { font-size: 13px; opacity: 0.7; }

    /* Guru Entry - Clickable Card Style */
    .guru-entry {
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: background 0.15s;
    }
    .guru-entry:last-child { border-bottom: none; }
    .guru-entry:hover { background: #f1f5f9; }

    .guru-avatar {
        width: 40px; height: 40px;
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 14px; flex-shrink: 0;
        border: 2px solid transparent;
        transition: all 0.2s;
    }

    /* Status-based avatar border */
    .guru-entry.status-hadir-tepat .guru-avatar { border-color: #059669; }
    .guru-entry.status-hadir-terlambat .guru-avatar { border-color: #7c3aed; }
    .guru-entry.status-izin .guru-avatar { border-color: #d97706; }
    .guru-entry.status-tanpa-keterangan .guru-avatar { border-color: #dc2626; }

    .guru-detail { flex: 1; min-width: 0; }
    .guru-name-text { font-size: 13px; font-weight: 600; color: #1f2937; }
    .guru-mapel-text { font-size: 11px; color: #6b7280; margin-top: 1px; }

    /* Clickable Status Area */
    .guru-status-click {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        background: rgba(255,255,255,0.8);
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
        min-width: 170px;
        justify-content: center;
    }
    .guru-status-click:hover {
        border-color: #3b82f6;
        background: white;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .guru-status-click.disabled {
        cursor: not-allowed;
        opacity: 0.85;
    }
    .guru-status-click.disabled:hover {
        border-color: #d97706;
        transform: none;
        box-shadow: none;
    }
    .status-label { font-size: 11px; color: #9ca3af; }
    .status-value { font-size: 12px; font-weight: 600; color: #6b7280; }
    .status-edit-icon { font-size: 10px; color: #9ca3af; }

    /* Status-colored value text  */
    .guru-entry.status-hadir-tepat .status-value { color: #059669; }
    .guru-entry.status-hadir-tepat .guru-status-click { border-color: #a7f3d0; background: #f0fdf4; }
    .guru-entry.status-hadir-terlambat .status-value { color: #7c3aed; }
    .guru-entry.status-hadir-terlambat .guru-status-click { border-color: #ddd6fe; background: #f5f3ff; }
    .guru-entry.status-izin .status-value { color: #d97706; }
    .guru-entry.status-izin .guru-status-click { border-color: #fde68a; background: #fffbeb; }
    .guru-entry.status-tanpa-keterangan .status-value { color: #dc2626; }
    .guru-entry.status-tanpa-keterangan .guru-status-click { border-color: #fecaca; background: #fef2f2; }

    /* Status badge (top-right corner indicator) */
    .guru-status-badge {
        font-size: 9px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 10px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: white;
        flex-shrink: 0;
    }
    .guru-status-badge.hadir-tepat { background: #059669; }
    .guru-status-badge.hadir-terlambat { background: #7c3aed; }
    .guru-status-badge.izin { background: #d97706; }
    .guru-status-badge.tanpa-keterangan { background: #dc2626; }

    /* Izin Info inline */
    .izin-inline-info {
        font-size: 11px;
        color: #92400e;
        background: #fffbeb;
        padding: 4px 10px;
        border-radius: 6px;
        border: 1px solid #fde68a;
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* MODAL STYLES */
    .piket-modal-options {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 12px;
        border: 1px solid #e9ecef;
    }
    .piket-modal-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .piket-modal-option {
        position: relative;
        cursor: pointer;
        margin: 0;
        padding: 0;
        transition: all 0.2s ease;
    }
    .modal-option-content {
        padding: 18px 12px;
        border: 2.5px solid #e9ecef;
        border-radius: 10px;
        background: white;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 100%;
    }
    .modal-option-content:hover { transform: translateY(-2px); box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1); }
    .modal-option-content i { font-size: 26px; display: block; line-height: 1; }
    .modal-option-content span { font-weight: 600; font-size: 12px; line-height: 1.3; text-align: center; }

    .piket-modal-option.hadir-tepat .modal-option-content { color: #059669; border-color: rgba(5,150,105,0.3); }
    .piket-modal-option.hadir-terlambat .modal-option-content { color: #7c3aed; border-color: rgba(124,58,237,0.3); }
    .piket-modal-option.izin .modal-option-content { color: #d97706; border-color: rgba(217,119,6,0.3); }
    .piket-modal-option.tanpa-keterangan .modal-option-content { color: #dc2626; border-color: rgba(220,38,38,0.3); }

    .piket-modal-option.selected .modal-option-content { border-width: 3px; transform: scale(0.95); }
    .piket-modal-option.hadir-tepat.selected .modal-option-content { background: #059669; color: white; border-color: #059669; }
    .piket-modal-option.hadir-terlambat.selected .modal-option-content { background: #7c3aed; color: white; border-color: #7c3aed; }
    .piket-modal-option.izin.selected .modal-option-content { background: #d97706; color: white; border-color: #d97706; }
    .piket-modal-option.tanpa-keterangan.selected .modal-option-content { background: #dc2626; color: white; border-color: #dc2626; }

    /* Modal guru info section */
    .modal-guru-info {
        text-align: center;
        padding: 15px;
        background: linear-gradient(135deg, #f8fafc, #e2e8f0);
        border-radius: 12px;
        margin-bottom: 15px;
    }
    .modal-guru-avatar {
        width: 70px; height: 70px;
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        color: white; font-size: 28px;
        margin-bottom: 10px;
        border: 3px solid #3b82f6;
    }
    .modal-guru-name { font-size: 16px; font-weight: 700; color: #1f2937; margin: 0; }
    .modal-guru-detail { font-size: 13px; color: #6b7280; margin: 4px 0 0; }

    /* Modal izin info */
    .modal-izin-box {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 10px;
        padding: 14px;
        margin-top: 12px;
    }
    .modal-izin-box .izin-row { margin-bottom: 8px; }
    .modal-izin-box .izin-row:last-child { margin-bottom: 0; }
    .modal-izin-box .izin-label-text { font-size: 11px; font-weight: 700; color: #92400e; text-transform: uppercase; letter-spacing: 0.5px; }
    .modal-izin-box .izin-value-text { font-size: 13px; color: #78350f; margin-top: 2px; }

    /* Keterangan in modal */
    .modal-keterangan-section { margin-top: 12px; }
    .modal-keterangan-label { font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px; }
    .modal-keterangan-input {
        width: 100%;
        padding: 8px 12px;
        border: 1.5px solid #d1d5db;
        border-radius: 8px;
        font-size: 13px;
        font-family: inherit;
        transition: all 0.2s;
    }
    .modal-keterangan-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
    }

    /* Empty state */
    .empty-jam {
        padding: 24px 20px;
        text-align: center;
        color: #9ca3af;
        font-size: 13px;
    }

    @media (max-width: 768px) {
        .catatan-header { flex-direction: column; text-align: center; }
        .catatan-header .header-left { flex-direction: column; }
        .jam-stats { display: none; }
        .guru-entry { flex-wrap: wrap; }
        .guru-status-click { min-width: 100%; }
        .modal-option-content { padding: 10px 12px; min-width: 70px; }
        .modal-option-content i { font-size: 18px; }
        .modal-option-content span { font-size: 10px; }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-guru')

    <div class="main-content">
        <!-- Header -->
        <div class="catatan-header">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div>
                    <h2>Catatan Piket KBM</h2>
                    <p class="header-sub">Verifikasi kehadiran guru di kelas per jam pelajaran</p>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <div class="header-badges">
                    <span class="header-badge"><i class="fas fa-calendar-day"></i> {{ $hariIni }}</span>
                    <span class="header-badge"><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($tanggalHariIni)->format('d M Y') }}</span>
                    @if($isTesting)
                        <span class="header-badge testing"><i class="fas fa-flask"></i> Testing</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <a href="{{ route('guru.tugas-tambahan') }}" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; color: #374151; background: white; border: 1px solid #d1d5db; text-decoration: none; transition: all 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('guru.catatan-piket.cetak') }}" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; color: white; background: linear-gradient(135deg, #059669, #047857); border: none; text-decoration: none; transition: all 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <i class="fas fa-print"></i> Cetak Laporan
            </a>
        </div>

        <!-- Piket Team -->
        <div class="piket-team-card">
            <div class="piket-team-label">Tim Piket Hari Ini</div>
            <div class="piket-team-list">
                @foreach($semuaPiketHariIni as $piket)
                <div class="piket-team-item {{ $piket->guru_id == $guru->id && $piket->tipe_guru == 'guru' ? 'is-me' : '' }}">
                    <span class="team-dot {{ $piket->tipe_guru }}"></span>
                    {{ $piket->nama_guru }}
                    @if($piket->guru_id == $guru->id && $piket->tipe_guru == 'guru')
                        <span style="background:#3b82f6; color:white; font-size:9px; padding:1px 6px; border-radius:6px;">ANDA</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <!-- Jam Cards -->
        @for($jam = 1; $jam <= $maxJam; $jam++)
            @php
                $rombelList = $jadwalPerJam[$jam] ?? [];
                $waktu = isset($jamSetting[$jam]) ? $jamSetting[$jam]['mulai'] . ' - ' . $jamSetting[$jam]['selesai'] : '';
                $totalRombel = count($rombelList);
                $totalGuru = 0;
                $confirmedGuru = 0;
                foreach ($rombelList as $rNama => $entries) {
                    $totalGuru += count($entries);
                    foreach ($entries as $e) {
                        $ck = $jam . '|' . $e['nama_guru'] . '|' . $rNama;
                        $ik = $e['nama_guru'] . '|' . $e['id_rombel'] . '|' . $jam;
                        if (isset($catatanHariIni[$ck]) || isset($izinGuruHariIni[$ik])) {
                            $confirmedGuru++;
                        }
                    }
                }
                $jamState = 'jam-none';
                if ($totalGuru > 0 && $confirmedGuru >= $totalGuru) $jamState = 'jam-complete';
                elseif ($confirmedGuru > 0) $jamState = 'jam-partial';
            @endphp
            <div class="jam-card {{ $jam === 1 ? 'open' : '' }} {{ $totalGuru > 0 ? $jamState : '' }}" id="jam-card-{{ $jam }}" data-total="{{ $totalGuru }}" data-confirmed="{{ $confirmedGuru }}">
                <div class="jam-card-header" onclick="toggleJam({{ $jam }})">
                    <div class="jam-left">
                        <div class="jam-number">{{ $jam }}</div>
                        <div>
                            <div class="jam-title">Jam ke-{{ $jam }}</div>
                            @if($waktu)
                                <div class="jam-time"><i class="fas fa-clock"></i> {{ $waktu }}</div>
                            @endif
                        </div>
                    </div>
                    <div style="display:flex; align-items:center;">
                        <div class="jam-stats">
                            <span class="jam-stat-badge rombel-count"><i class="fas fa-door-open"></i> {{ $totalRombel }} Rombel</span>
                            <span class="jam-stat-badge guru-count"><i class="fas fa-chalkboard-teacher"></i> {{ $totalGuru }} Guru</span>
                        </div>
                        <i class="fas fa-chevron-down jam-chevron"></i>
                    </div>
                </div>

                <div class="jam-card-body">
                    <div class="jam-card-inner">
                        @if($totalRombel > 0)
                            @foreach($rombelList as $namaRombel => $guruEntries)
                                <div class="rombel-block">
                                    <div class="rombel-header">
                                        <div class="rombel-name">
                                            <i class="fas fa-door-open"></i> {{ $namaRombel }}
                                        </div>
                                    </div>

                                    @foreach($guruEntries as $entry)
                                        @php
                                            $uniqueId = $jam . '-' . Str::slug($entry['nama_guru'] . '-' . $namaRombel);
                                            $catatanKey = $jam . '|' . $entry['nama_guru'] . '|' . $namaRombel;
                                            $existing = $catatanHariIni[$catatanKey] ?? null;
                                            $izinKey = $entry['nama_guru'] . '|' . $entry['id_rombel'] . '|' . $jam;
                                            $izinData = $izinGuruHariIni[$izinKey] ?? null;
                                            $isIzin = !is_null($izinData);

                                            // Determine current status
                                            $currentStatus = '';
                                            $statusText = 'Klik untuk konfirmasi';
                                            $statusClass = '';
                                            $badgeClass = '';

                                            if ($isIzin) {
                                                $currentStatus = 'Izin';
                                                $statusText = 'Izin (Konfirmasi Guru)';
                                                $statusClass = 'status-izin';
                                                $badgeClass = 'izin';
                                            } elseif ($existing) {
                                                $currentStatus = $existing->status_kehadiran;
                                                $statusText = $currentStatus;
                                                $statusMap = [
                                                    'Hadir Tepat Waktu' => ['class' => 'status-hadir-tepat', 'badge' => 'hadir-tepat'],
                                                    'Hadir Terlambat' => ['class' => 'status-hadir-terlambat', 'badge' => 'hadir-terlambat'],
                                                    'Izin' => ['class' => 'status-izin', 'badge' => 'izin'],
                                                    'Tanpa Keterangan' => ['class' => 'status-tanpa-keterangan', 'badge' => 'tanpa-keterangan'],
                                                ];
                                                $statusClass = $statusMap[$currentStatus]['class'] ?? '';
                                                $badgeClass = $statusMap[$currentStatus]['badge'] ?? '';
                                            }
                                        @endphp
                                        <div class="guru-entry {{ $statusClass }}" id="entry-{{ $uniqueId }}">
                                            <div class="guru-avatar">
                                                <i class="fas fa-chalkboard-teacher"></i>
                                            </div>
                                            <div class="guru-detail">
                                                <div class="guru-name-text">{{ $entry['nama_guru'] }}</div>
                                                <div class="guru-mapel-text">{{ $entry['nama_mapel'] }}</div>
                                                @if($isIzin)
                                                    <div class="izin-inline-info">
                                                        <i class="fas fa-info-circle"></i> Izin dikonfirmasi oleh guru
                                                    </div>
                                                @endif
                                            </div>

                                            @if(!empty($badgeClass))
                                                <span class="guru-status-badge {{ $badgeClass }}" id="badge-{{ $uniqueId }}">{{ $currentStatus }}</span>
                                            @else
                                                <span class="guru-status-badge" id="badge-{{ $uniqueId }}" style="display:none;"></span>
                                            @endif

                                            <div class="guru-status-click {{ $isIzin ? 'disabled' : '' }}"
                                                 onclick="openPiketModal('{{ $uniqueId }}', {{ $jam }}, '{{ addslashes($entry['nama_guru']) }}', '{{ addslashes($entry['nama_mapel']) }}', '{{ addslashes($namaRombel) }}', '{{ $currentStatus }}', '{{ $existing->keterangan ?? '' }}', {{ $isIzin ? 'true' : 'false' }}, '{{ addslashes($izinData['alasan'] ?? '') }}', '{{ addslashes($izinData['tugas'] ?? '') }}')">
                                                <div style="text-align:center;">
                                                    <div class="status-label">Status</div>
                                                    <div class="status-value" id="statusVal-{{ $uniqueId }}">{{ $statusText }}</div>
                                                </div>
                                                <i class="fas fa-pencil-alt status-edit-icon"></i>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        @else
                            <div class="empty-jam">
                                <i class="fas fa-minus-circle" style="color: #d1d5db; margin-right: 6px;"></i>
                                Tidak ada jadwal guru pada jam ini
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endfor
    </div>
</div>

<!-- Modal Konfirmasi Kehadiran Guru -->
<div class="modal fade" id="piketModal" tabindex="-1" aria-labelledby="piketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="piketModalLabel">
                    <i class="fas fa-user-check me-2"></i> Konfirmasi Kehadiran Guru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Guru Info -->
                <div class="modal-guru-info">
                    <div class="modal-guru-avatar">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h4 class="modal-guru-name" id="modalGuruName"></h4>
                    <p class="modal-guru-detail" id="modalGuruDetail"></p>
                </div>

                <!-- Status Options -->
                <div class="piket-modal-options" id="modalOptionsSection">
                    <div class="piket-modal-grid">
                        <label class="piket-modal-option hadir-tepat" onclick="selectPiketStatus('Hadir Tepat Waktu')">
                            <div class="modal-option-content" id="opt-hadir-tepat">
                                <i class="fas fa-check-circle"></i>
                                <span>Hadir Tepat Waktu</span>
                            </div>
                        </label>
                        <label class="piket-modal-option hadir-terlambat" onclick="selectPiketStatus('Hadir Terlambat')">
                            <div class="modal-option-content" id="opt-hadir-terlambat">
                                <i class="fas fa-clock"></i>
                                <span>Hadir Terlambat</span>
                            </div>
                        </label>
                        <label class="piket-modal-option izin" onclick="selectPiketStatus('Izin')">
                            <div class="modal-option-content" id="opt-izin">
                                <i class="fas fa-file-alt"></i>
                                <span>Izin</span>
                            </div>
                        </label>
                        <label class="piket-modal-option tanpa-keterangan" onclick="selectPiketStatus('Tanpa Keterangan')">
                            <div class="modal-option-content" id="opt-tanpa-keterangan">
                                <i class="fas fa-question-circle"></i>
                                <span>Tanpa Keterangan</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Izin Info Box (shown when guru already confirmed izin) -->
                <div class="modal-izin-box" id="modalIzinBox" style="display:none;">
                    <div class="izin-row">
                        <div class="izin-label-text"><i class="fas fa-info-circle"></i> Alasan Izin</div>
                        <div class="izin-value-text" id="modalIzinAlasan"></div>
                    </div>
                    <div class="izin-row" id="modalIzinTugasRow" style="display:none;">
                        <div class="izin-label-text"><i class="fas fa-tasks"></i> Tugas yang Diberikan</div>
                        <div class="izin-value-text" id="modalIzinTugas"></div>
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="modal-keterangan-section" id="modalKeteranganSection">
                    <div class="modal-keterangan-label">Keterangan (opsional)</div>
                    <input type="text" class="modal-keterangan-input" id="modalKeterangan" placeholder="Tambahkan keterangan...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanModal" onclick="savePiketStatus()">
                    <i class="fas fa-save me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let currentUniqueId = '';
    let currentJamKe = 0;
    let currentNamaGuru = '';
    let currentNamaMapel = '';
    let currentNamaRombel = '';
    let currentSelectedStatus = '';
    let isIzinMode = false;

    function toggleJam(jam) {
        document.getElementById('jam-card-' + jam).classList.toggle('open');
    }

    function openPiketModal(uniqueId, jamKe, namaGuru, namaMapel, namaRombel, currentStatus, keterangan, isIzin, izinAlasan, izinTugas) {
        currentUniqueId = uniqueId;
        currentJamKe = jamKe;
        currentNamaGuru = namaGuru;
        currentNamaMapel = namaMapel;
        currentNamaRombel = namaRombel;
        currentSelectedStatus = currentStatus;
        isIzinMode = isIzin;

        // Set guru info
        document.getElementById('modalGuruName').textContent = namaGuru;
        document.getElementById('modalGuruDetail').textContent = namaMapel + ' • ' + namaRombel + ' • Jam ke-' + jamKe;

        // Reset option selections
        document.querySelectorAll('.piket-modal-option').forEach(opt => opt.classList.remove('selected'));

        // Handle izin mode
        if (isIzin) {
            document.getElementById('modalOptionsSection').style.display = 'none';
            document.getElementById('modalIzinBox').style.display = 'block';
            document.getElementById('modalIzinAlasan').textContent = izinAlasan;
            document.getElementById('modalKeteranganSection').style.display = 'none';
            document.getElementById('btnSimpanModal').style.display = 'none';

            if (izinTugas) {
                document.getElementById('modalIzinTugasRow').style.display = 'block';
                document.getElementById('modalIzinTugas').textContent = izinTugas;
            } else {
                document.getElementById('modalIzinTugasRow').style.display = 'none';
            }
        } else {
            document.getElementById('modalOptionsSection').style.display = 'block';
            document.getElementById('modalIzinBox').style.display = 'none';
            document.getElementById('modalKeteranganSection').style.display = 'block';
            document.getElementById('btnSimpanModal').style.display = 'inline-block';
            document.getElementById('modalKeterangan').value = keterangan || '';

            // Highlight current status
            if (currentStatus) {
                const statusClassMap = {
                    'Hadir Tepat Waktu': 'hadir-tepat',
                    'Hadir Terlambat': 'hadir-terlambat',
                    'Izin': 'izin',
                    'Tanpa Keterangan': 'tanpa-keterangan'
                };
                const cls = statusClassMap[currentStatus];
                if (cls) {
                    document.querySelector('.piket-modal-option.' + cls).classList.add('selected');
                }
            }
        }

        var modal = new bootstrap.Modal(document.getElementById('piketModal'));
        modal.show();
    }

    function selectPiketStatus(status) {
        currentSelectedStatus = status;
        // Toggle selection visuals
        document.querySelectorAll('.piket-modal-option').forEach(opt => opt.classList.remove('selected'));
        const statusClassMap = {
            'Hadir Tepat Waktu': 'hadir-tepat',
            'Hadir Terlambat': 'hadir-terlambat',
            'Izin': 'izin',
            'Tanpa Keterangan': 'tanpa-keterangan'
        };
        const cls = statusClassMap[status];
        if (cls) {
            document.querySelector('.piket-modal-option.' + cls).classList.add('selected');
        }
    }

    function savePiketStatus() {
        if (!currentSelectedStatus) {
            alert('Pilih status kehadiran terlebih dahulu');
            return;
        }

        const keterangan = document.getElementById('modalKeterangan').value;
        const btn = document.getElementById('btnSimpanModal');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';

        fetch('{{ route("guru.catatan-piket.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                piket_kbm_id: {{ $piketHariIni->id }},
                tanggal: '{{ $tanggalHariIni }}',
                jam_ke: currentJamKe,
                nama_guru: currentNamaGuru,
                nama_mapel: currentNamaMapel,
                nama_rombel: currentNamaRombel,
                status_kehadiran: currentSelectedStatus,
                keterangan: keterangan,
            })
        })
        .then(res => res.json())
        .then(result => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> Simpan';

            if (result.success) {
                // Update the card UI
                updateEntryUI(currentUniqueId, currentSelectedStatus);

                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('piketModal')).hide();
            } else {
                alert('Gagal menyimpan: ' + (result.message || 'Terjadi kesalahan'));
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> Simpan';
            alert('Terjadi kesalahan jaringan');
        });
    }

    function updateEntryUI(uniqueId, status) {
        const entry = document.getElementById('entry-' + uniqueId);
        const statusVal = document.getElementById('statusVal-' + uniqueId);
        const badge = document.getElementById('badge-' + uniqueId);

        // Remove all status classes
        entry.classList.remove('status-hadir-tepat', 'status-hadir-terlambat', 'status-izin', 'status-tanpa-keterangan');

        // Add new status class
        const statusClassMap = {
            'Hadir Tepat Waktu': 'status-hadir-tepat',
            'Hadir Terlambat': 'status-hadir-terlambat',
            'Izin': 'status-izin',
            'Tanpa Keterangan': 'status-tanpa-keterangan'
        };
        const badgeClassMap = {
            'Hadir Tepat Waktu': 'hadir-tepat',
            'Hadir Terlambat': 'hadir-terlambat',
            'Izin': 'izin',
            'Tanpa Keterangan': 'tanpa-keterangan'
        };

        entry.classList.add(statusClassMap[status]);
        statusVal.textContent = status;

        // Update badge
        badge.className = 'guru-status-badge ' + badgeClassMap[status];
        badge.textContent = status;
        badge.style.display = 'inline-block';

        // Update jam card color
        updateJamCardColor(currentJamKe);
    }

    function updateJamCardColor(jamKe) {
        const jamCard = document.getElementById('jam-card-' + jamKe);
        if (!jamCard) return;
        let total = parseInt(jamCard.dataset.total) || 0;
        let confirmed = parseInt(jamCard.dataset.confirmed) || 0;
        confirmed++;
        jamCard.dataset.confirmed = confirmed;

        jamCard.classList.remove('jam-none', 'jam-partial', 'jam-complete');
        if (total > 0 && confirmed >= total) {
            jamCard.classList.add('jam-complete');
        } else if (confirmed > 0) {
            jamCard.classList.add('jam-partial');
        } else {
            jamCard.classList.add('jam-none');
        }
    }
</script>
@endpush
