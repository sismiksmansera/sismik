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
    .btn-back {
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
    .btn-back:hover { background: rgba(255,255,255,0.3); color: white; }

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
        transition: box-shadow 0.2s;
    }
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
    .jam-card.open .jam-card-body { max-height: 5000px; }
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

    /* Guru Entry */
    .guru-entry {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
    }
    .guru-entry:last-child { border-bottom: none; }
    .guru-info {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }
    .guru-avatar {
        width: 36px; height: 36px;
        background: linear-gradient(135deg, #10b981, #059669);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 14px; flex-shrink: 0;
    }
    .guru-detail { flex: 1; }
    .guru-name-text { font-size: 14px; font-weight: 600; color: #1f2937; }
    .guru-mapel-text { font-size: 12px; color: #6b7280; margin-top: 2px; }

    /* Status Buttons */
    .status-buttons {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }
    .status-btn {
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        border: 2px solid transparent;
        cursor: pointer;
        transition: all 0.2s;
        font-family: inherit;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #f9fafb;
        color: #6b7280;
    }
    .status-btn:hover { transform: translateY(-1px); }
    .status-btn.active { transform: scale(1.02); box-shadow: 0 2px 8px rgba(0,0,0,0.1); }

    .status-btn[data-status="Hadir Tepat Waktu"] { border-color: #d1fae5; }
    .status-btn[data-status="Hadir Tepat Waktu"]:hover,
    .status-btn[data-status="Hadir Tepat Waktu"].active { background: #059669; color: white; border-color: #059669; }

    .status-btn[data-status="Hadir Terlambat"] { border-color: #ddd6fe; }
    .status-btn[data-status="Hadir Terlambat"]:hover,
    .status-btn[data-status="Hadir Terlambat"].active { background: #7c3aed; color: white; border-color: #7c3aed; }

    .status-btn[data-status="Izin"] { border-color: #fde68a; }
    .status-btn[data-status="Izin"]:hover,
    .status-btn[data-status="Izin"].active { background: #d97706; color: white; border-color: #d97706; }

    .status-btn[data-status="Tanpa Keterangan"] { border-color: #fecaca; }
    .status-btn[data-status="Tanpa Keterangan"]:hover,
    .status-btn[data-status="Tanpa Keterangan"].active { background: #dc2626; color: white; border-color: #dc2626; }

    .status-btn:disabled {
        cursor: not-allowed;
        opacity: 0.7;
        transform: none !important;
    }

    /* Izin Info Box */
    .izin-info-box {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 10px;
        padding: 12px 14px;
        margin-top: 10px;
    }
    .izin-info-box .izin-label {
        font-size: 11px;
        font-weight: 700;
        color: #92400e;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .izin-info-box .izin-text {
        font-size: 13px;
        color: #78350f;
        line-height: 1.5;
    }
    .izin-info-box .izin-tugas {
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px dashed #fbbf24;
    }

    /* Keterangan inline */
    .keterangan-row {
        display: flex;
        gap: 8px;
        align-items: center;
        margin-top: 8px;
    }
    .keterangan-input {
        flex: 1;
        padding: 6px 12px;
        border: 1.5px solid #d1d5db;
        border-radius: 8px;
        font-size: 12px;
        font-family: inherit;
        transition: all 0.2s;
    }
    .keterangan-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
    }

    /* Save result indicator */
    .save-indicator {
        font-size: 11px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: opacity 0.3s;
    }
    .save-indicator.success { background: #d1fae5; color: #065f46; }
    .save-indicator.error { background: #fef2f2; color: #dc2626; }

    /* Empty state */
    .empty-jam {
        padding: 24px 20px;
        text-align: center;
        color: #9ca3af;
        font-size: 13px;
    }

    /* Saved badge on guru */
    .saved-check {
        color: #10b981;
        font-size: 12px;
        margin-left: 4px;
    }

    @media (max-width: 768px) {
        .catatan-header { flex-direction: column; text-align: center; }
        .catatan-header .header-left { flex-direction: column; }
        .status-buttons { flex-direction: column; }
        .status-btn { justify-content: center; }
        .jam-stats { display: none; }
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
                <a href="{{ route('guru.tugas-tambahan') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
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
                foreach ($rombelList as $entries) { $totalGuru += count($entries); }
            @endphp
            <div class="jam-card {{ $jam === 1 ? 'open' : '' }}" id="jam-card-{{ $jam }}">
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
                                            $slug = Str::slug($entry['nama_guru'] . '-' . $namaRombel);
                                            $catatanKey = $jam . '|' . $entry['nama_guru'] . '|' . $namaRombel;
                                            $existing = $catatanHariIni[$catatanKey] ?? null;
                                            $izinKey = $entry['nama_guru'] . '|' . $entry['id_rombel'] . '|' . $jam;
                                            $izinData = $izinGuruHariIni[$izinKey] ?? null;
                                            $isIzin = !is_null($izinData);
                                            $currentStatus = $existing->status_kehadiran ?? ($isIzin ? 'Izin' : '');
                                        @endphp
                                        <div class="guru-entry" id="entry-{{ $jam }}-{{ $slug }}">
                                            <div class="guru-info">
                                                <div class="guru-avatar">
                                                    <i class="fas fa-chalkboard-teacher"></i>
                                                </div>
                                                <div class="guru-detail">
                                                    <div class="guru-name-text">
                                                        {{ $entry['nama_guru'] }}
                                                        @if($existing)
                                                            <i class="fas fa-check-circle saved-check" title="Sudah dicatat"></i>
                                                        @endif
                                                    </div>
                                                    <div class="guru-mapel-text">{{ $entry['nama_mapel'] }}</div>
                                                </div>
                                                <span class="save-indicator" id="indicator-{{ $jam }}-{{ $slug }}" style="display:none;"></span>
                                            </div>

                                            <div class="status-buttons">
                                                @if($isIzin)
                                                    {{-- Guru already confirmed izin via their account --}}
                                                    <button class="status-btn active" data-status="Izin" disabled>
                                                        <i class="fas fa-file-alt"></i> Izin (Dikonfirmasi Guru)
                                                    </button>
                                                @else
                                                    <button class="status-btn {{ $currentStatus === 'Hadir Tepat Waktu' ? 'active' : '' }}"
                                                            data-status="Hadir Tepat Waktu"
                                                            onclick="selectStatus({{ $jam }}, '{{ $slug }}', 'Hadir Tepat Waktu', '{{ addslashes($entry['nama_guru']) }}', '{{ addslashes($entry['nama_mapel']) }}', '{{ addslashes($namaRombel) }}', this)">
                                                        <i class="fas fa-check-circle"></i> Hadir Tepat Waktu
                                                    </button>
                                                    <button class="status-btn {{ $currentStatus === 'Hadir Terlambat' ? 'active' : '' }}"
                                                            data-status="Hadir Terlambat"
                                                            onclick="selectStatus({{ $jam }}, '{{ $slug }}', 'Hadir Terlambat', '{{ addslashes($entry['nama_guru']) }}', '{{ addslashes($entry['nama_mapel']) }}', '{{ addslashes($namaRombel) }}', this)">
                                                        <i class="fas fa-clock"></i> Hadir Terlambat
                                                    </button>
                                                    <button class="status-btn {{ $currentStatus === 'Izin' ? 'active' : '' }}"
                                                            data-status="Izin"
                                                            onclick="selectStatus({{ $jam }}, '{{ $slug }}', 'Izin', '{{ addslashes($entry['nama_guru']) }}', '{{ addslashes($entry['nama_mapel']) }}', '{{ addslashes($namaRombel) }}', this)">
                                                        <i class="fas fa-file-alt"></i> Izin
                                                    </button>
                                                    <button class="status-btn {{ $currentStatus === 'Tanpa Keterangan' ? 'active' : '' }}"
                                                            data-status="Tanpa Keterangan"
                                                            onclick="selectStatus({{ $jam }}, '{{ $slug }}', 'Tanpa Keterangan', '{{ addslashes($entry['nama_guru']) }}', '{{ addslashes($entry['nama_mapel']) }}', '{{ addslashes($namaRombel) }}', this)">
                                                        <i class="fas fa-question-circle"></i> Tanpa Keterangan
                                                    </button>
                                                @endif
                                            </div>

                                            @if($isIzin)
                                                <div class="izin-info-box">
                                                    <div class="izin-label"><i class="fas fa-info-circle"></i> Alasan Izin</div>
                                                    <div class="izin-text">{{ $izinData['alasan'] }}</div>
                                                    @if(!empty($izinData['tugas']))
                                                        <div class="izin-tugas">
                                                            <div class="izin-label"><i class="fas fa-tasks"></i> Tugas yang Diberikan</div>
                                                            <div class="izin-text">{{ $izinData['tugas'] }}</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif

                                            @if(!$isIzin)
                                                <div class="keterangan-row">
                                                    <input type="text" class="keterangan-input" id="keterangan-{{ $jam }}-{{ $slug }}"
                                                           placeholder="Keterangan (opsional)..." value="{{ $existing->keterangan ?? '' }}">
                                                </div>
                                            @endif
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
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function toggleJam(jam) {
        const card = document.getElementById('jam-card-' + jam);
        card.classList.toggle('open');
    }

    function selectStatus(jamKe, slug, status, namaGuru, namaMapel, namaRombel, btn) {
        // Toggle active state
        const parent = btn.closest('.status-buttons');
        const buttons = parent.querySelectorAll('.status-btn');
        const wasActive = btn.classList.contains('active');

        buttons.forEach(b => b.classList.remove('active'));
        if (!wasActive) {
            btn.classList.add('active');
        }

        // Auto-save
        const keteranganEl = document.getElementById('keterangan-' + jamKe + '-' + slug);
        const keterangan = keteranganEl ? keteranganEl.value : '';
        const indicator = document.getElementById('indicator-' + jamKe + '-' + slug);

        // Show saving state
        indicator.style.display = 'inline-flex';
        indicator.className = 'save-indicator';
        indicator.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        indicator.style.color = '#6b7280';
        indicator.style.background = '#f3f4f6';

        fetch('{{ route("guru.catatan-piket.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                piket_kbm_id: {{ $piketHariIni->id }},
                tanggal: '{{ $tanggalHariIni }}',
                jam_ke: jamKe,
                nama_guru: namaGuru,
                nama_mapel: namaMapel,
                nama_rombel: namaRombel,
                status_kehadiran: wasActive ? '' : status,
                keterangan: keterangan,
            })
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                indicator.className = 'save-indicator success';
                indicator.innerHTML = '<i class="fas fa-check"></i> Tersimpan';
                setTimeout(() => { indicator.style.display = 'none'; }, 2000);
            } else {
                indicator.className = 'save-indicator error';
                indicator.innerHTML = '<i class="fas fa-times"></i> Gagal';
            }
        })
        .catch(() => {
            indicator.className = 'save-indicator error';
            indicator.innerHTML = '<i class="fas fa-times"></i> Error';
        });
    }
</script>
@endpush
