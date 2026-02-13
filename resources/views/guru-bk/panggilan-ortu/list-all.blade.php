@extends('layouts.app-guru-bk')

@section('content')
<div class="main-content panggilan-list-page">
    {{-- Toast --}}
    @if(session('success'))
    <div id="toastNotification" class="toast-notification">
        <div class="toast-content"><i class="fas fa-check-circle"></i><span>{{ session('success') }}</span></div>
        <button class="toast-close" onclick="hideToast()">×</button>
    </div>
    @endif
    @if(session('error'))
    <div id="toastNotification" class="toast-notification toast-error">
        <div class="toast-content"><i class="fas fa-times-circle"></i><span>{{ session('error') }}</span></div>
        <button class="toast-close" onclick="hideToast()">×</button>
    </div>
    @endif

    {{-- Header --}}
    <div class="page-header-center">
        <div class="header-icon-large">
            <i class="fas fa-phone"></i>
        </div>
        <h1>Panggilan Orang Tua</h1>
        @if($periodeAktif)
        <div class="header-periode-badge"><i class="fas fa-calendar-alt"></i> {{ $periodeAktif->tahun_pelajaran }} - {{ $periodeAktif->semester }}</div>
        @endif
    </div>

    {{-- Buat Panggilan Baru Button --}}
    <div class="action-row">
        <a href="{{ route('guru_bk.panggilan-ortu.create-new') }}" class="btn-create-new">
            <i class="fas fa-plus-circle"></i> Buat Panggilan Baru
        </a>
    </div>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon primary"><i class="fas fa-list-alt"></i></div>
            <div class="stat-info"><h3>{{ $stats['total'] }}</h3><p>Total Panggilan</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon warning"><i class="fas fa-clock"></i></div>
            <div class="stat-info"><h3>{{ $stats['Menunggu'] }}</h3><p>Menunggu</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info"><h3>{{ $stats['Hadir'] }}</h3><p>Hadir</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon danger"><i class="fas fa-times-circle"></i></div>
            <div class="stat-info"><h3>{{ $stats['Tidak Hadir'] }}</h3><p>Tidak Hadir</p></div>
        </div>
    </div>

    {{-- List --}}
    <div class="list-container">
        <div class="container-header">
            <div class="container-title">
                <i class="fas fa-clipboard-list"></i>
                <h2>Daftar Panggilan</h2>
            </div>
            <span class="count-badge">{{ $stats['total'] }} Catatan</span>
        </div>

        @if($panggilan_list->count() == 0)
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-inbox"></i></div>
            <h3>Belum Ada Panggilan</h3>
            <p>Belum ada surat panggilan orang tua yang dibuat.</p>
        </div>
        @else
        <div class="panggilan-cards">
            @foreach($panggilan_list as $item)
            <div class="panggilan-card">
                <div class="card-top">
                    <div class="card-top-left">
                        <div class="siswa-avatar {{ ($item->jk_siswa ?? '') == 'Laki-laki' ? 'laki' : 'perempuan' }}">
                            {{ strtoupper(substr($item->nama_siswa ?? '?', 0, 1)) }}
                        </div>
                        <div class="siswa-info">
                            <div class="siswa-nama">{{ $item->nama_siswa ?? 'Siswa tidak ditemukan' }}</div>
                            <div class="siswa-meta">{{ $item->nama_rombel ?? '-' }} · NISN: {{ $item->nisn }}</div>
                            <div class="guru-bk-badge"><i class="fas fa-user-shield"></i> {{ $item->nama_guru_bk ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="status-badge status-{{ strtolower(str_replace(' ', '-', $item->status)) }}">
                        @php
                            $statusIcons = [
                                'Menunggu' => 'fa-clock',
                                'Hadir' => 'fa-check-circle',
                                'Tidak Hadir' => 'fa-times-circle',
                                'Dijadwalkan Ulang' => 'fa-redo',
                            ];
                        @endphp
                        <i class="fas {{ $statusIcons[$item->status] ?? 'fa-question' }}"></i>
                        {{ $item->status }}
                    </div>
                </div>

                <div class="card-details">
                    <div class="detail-grid">
                        <div class="detail-item">
                            <i class="fas fa-hashtag"></i>
                            <span>{{ $item->no_surat }}</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-file-alt"></i>
                            <span>{{ $item->perihal }}</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span>{{ \Carbon\Carbon::parse($item->tanggal_panggilan)->translatedFormat('d M Y') }}</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-clock"></i>
                            <span>{{ $item->jam_panggilan ? \Carbon\Carbon::parse($item->jam_panggilan)->format('H:i') : '-' }}</span>
                        </div>
                    </div>

                    @if($item->alasan)
                    <div class="alasan-row">
                        <span class="alasan-label"><i class="fas fa-align-left"></i> Alasan</span>
                        <p class="alasan-text">{{ $item->alasan }}</p>
                    </div>
                    @endif
                </div>

                <div class="card-actions">
                    <a href="{{ route('guru_bk.panggilan-ortu', $item->nisn) }}" class="btn-action btn-view" title="Lihat Riwayat">
                        <i class="fas fa-eye"></i> Riwayat
                    </a>
                    <a href="{{ route('guru_bk.panggilan-ortu.edit', $item->id) }}" class="btn-action btn-edit" title="Edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('guru_bk.panggilan-ortu.print', $item->id) }}" class="btn-action btn-print" title="Cetak" target="_blank">
                        <i class="fas fa-print"></i> Cetak
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<style>
:root {
    --primary: #3b82f6;
    --primary-dark: #1d4ed8;
    --danger: #ef4444;
    --warning: #f59e0b;
    --success: #10b981;
}

.panggilan-list-page {
    padding: 30px; background: #f8f9fa; min-height: calc(100vh - 60px);
}

/* Toast */
.toast-notification {
    position: fixed; top: 20px; right: 20px; min-width: 280px; max-width: 90vw;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white; padding: 14px 18px; border-radius: 12px;
    box-shadow: 0 10px 40px rgba(16,185,129,0.4);
    display: flex; align-items: center; justify-content: space-between; gap: 12px;
    z-index: 9999; animation: slideIn 0.3s ease;
}
.toast-notification.toast-error { background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 10px 40px rgba(239,68,68,0.4); }
@keyframes slideIn { from { transform: translateX(120%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
.toast-content { display: flex; align-items: center; gap: 10px; }
.toast-content i { font-size: 18px; }
.toast-close {
    background: rgba(255,255,255,0.2); border: none; color: white;
    width: 26px; height: 26px; border-radius: 50%; cursor: pointer;
    display: flex; align-items: center; justify-content: center; font-size: 16px;
}

/* Header */
.page-header-center { text-align: center; margin-bottom: 25px; }
.header-icon-large {
    width: 70px; height: 70px; border-radius: 18px;
    display: flex; align-items: center; justify-content: center;
    font-size: 32px; color: white; margin: 0 auto 16px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    box-shadow: 0 8px 25px rgba(59,130,246,0.4);
}
.page-header-center h1 { font-size: 24px; font-weight: 700; margin: 0 0 8px 0; color: #1f2937; }
.header-periode-badge {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(59,130,246,0.1); color: #3b82f6;
    padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 600;
    border: 1px solid rgba(59,130,246,0.2);
}

/* Stats */
.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 20px; }
.stat-card {
    background: white; padding: 14px 16px; border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; align-items: center; gap: 12px; border: 1px solid #e5e7eb;
}
.stat-icon {
    width: 42px; height: 42px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; font-size: 16px; color: white; flex-shrink: 0;
}
.stat-icon.primary { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.stat-icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
.stat-icon.success { background: linear-gradient(135deg, #10b981, #059669); }
.stat-icon.danger { background: linear-gradient(135deg, #ef4444, #dc2626); }
.stat-info h3 { margin: 0; font-size: 18px; font-weight: 700; color: #1f2937; }
.stat-info p { margin: 2px 0 0 0; color: #6b7280; font-size: 11px; }

/* Create Button */
.action-row { margin-bottom: 20px; }
.btn-create-new {
    display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px;
    background: linear-gradient(135deg, #7c3aed, #5b21b6); color: white;
    border-radius: 12px; font-weight: 600; font-size: 14px; text-decoration: none;
    transition: all 0.3s; box-shadow: 0 4px 15px rgba(124,58,237,0.3);
}
.btn-create-new:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(124,58,237,0.4); }

/* List Container */
.list-container {
    background: white; border-radius: 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow: hidden;
}
.container-header {
    padding: 16px 20px; border-bottom: 1px solid #e5e7eb;
    display: flex; justify-content: space-between; align-items: center;
}
.container-title { display: flex; align-items: center; gap: 10px; }
.container-title i { color: var(--primary); font-size: 16px; }
.container-title h2 { margin: 0; font-size: 1rem; color: #1f2937; }
.count-badge {
    padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;
    background: rgba(59,130,246,0.1); color: #3b82f6;
}

/* Empty State */
.empty-state { padding: 50px 20px; text-align: center; }
.empty-icon {
    width: 70px; height: 70px; background: #f0fdf4; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;
}
.empty-icon i { font-size: 28px; color: #10b981; }
.empty-state h3 { margin: 0 0 8px; color: #1f2937; font-size: 16px; }
.empty-state p { margin: 0; color: #6b7280; font-size: 14px; }

/* Cards */
.panggilan-cards { padding: 15px; display: flex; flex-direction: column; gap: 12px; }

.panggilan-card {
    background: white; border: 1px solid #e5e7eb; border-radius: 12px;
    padding: 16px; transition: all 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.panggilan-card:hover { box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-color: #3b82f6; }

.card-top {
    display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; gap: 10px;
}
.card-top-left { display: flex; align-items: center; gap: 10px; min-width: 0; }
.siswa-avatar {
    width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 15px; color: white;
}
.siswa-avatar.laki { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.siswa-avatar.perempuan { background: linear-gradient(135deg, #ec4899, #db2777); }
.siswa-info { min-width: 0; }
.siswa-nama { font-weight: 600; font-size: 14px; color: #1f2937; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.siswa-meta { font-size: 11px; color: #6b7280; }
.guru-bk-badge {
    display: inline-flex; align-items: center; gap: 4px; margin-top: 4px;
    font-size: 10px; font-weight: 600; color: #7c3aed;
    background: rgba(124,58,237,0.08); padding: 2px 8px; border-radius: 6px;
}
.guru-bk-badge i { font-size: 9px; }

.status-badge {
    display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px;
    border-radius: 8px; font-size: 11px; font-weight: 600; white-space: nowrap; flex-shrink: 0;
}
.status-menunggu { background: rgba(245,158,11,0.1); color: #d97706; }
.status-hadir { background: rgba(16,185,129,0.1); color: #059669; }
.status-tidak-hadir { background: rgba(239,68,68,0.1); color: #dc2626; }
.status-dijadwalkan-ulang { background: rgba(139,92,246,0.1); color: #7c3aed; }

.card-details { margin-bottom: 12px; }
.detail-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 6px 16px; margin-bottom: 8px;
}
.detail-item {
    display: flex; align-items: center; gap: 6px; font-size: 12px; color: #374151;
}
.detail-item i { color: #9ca3af; font-size: 11px; width: 14px; text-align: center; }

.alasan-row { margin-top: 6px; }
.alasan-label { font-size: 11px; font-weight: 600; color: #6b7280; display: flex; align-items: center; gap: 5px; margin-bottom: 3px; }
.alasan-text { margin: 0; font-size: 12px; color: #374151; line-height: 1.5; }

.card-actions {
    display: flex; gap: 6px; border-top: 1px solid #f3f4f6; padding-top: 10px; flex-wrap: wrap;
}
.btn-action {
    display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px;
    border-radius: 8px; font-size: 11px; font-weight: 600; text-decoration: none;
    transition: all 0.2s; border: none; cursor: pointer; font-family: 'Poppins', sans-serif;
}
.btn-view { background: rgba(59,130,246,0.1); color: #3b82f6; }
.btn-view:hover { background: #3b82f6; color: white; }
.btn-edit { background: rgba(245,158,11,0.1); color: #f59e0b; }
.btn-edit:hover { background: #f59e0b; color: white; }
.btn-print { background: rgba(16,185,129,0.1); color: #10b981; }
.btn-print:hover { background: #10b981; color: white; }

/* Responsive */
@media (max-width: 768px) {
    .panggilan-list-page { padding: 12px; }
    .header-icon-large { width: 56px; height: 56px; font-size: 26px; border-radius: 14px; margin-bottom: 12px; }
    .page-header-center h1 { font-size: 20px; }
    .btn-create-new { padding: 10px 18px; font-size: 12px; width: 100%; justify-content: center; }
    .stats-grid { display: flex; gap: 6px; }
    .stat-card { flex: 1; min-width: 0; padding: 8px 4px; flex-direction: column; gap: 2px; text-align: center; }
    .stat-icon { width: 28px; height: 28px; font-size: 11px; border-radius: 7px; margin: 0 auto; }
    .stat-info h3 { font-size: 15px; }
    .stat-info p { font-size: 8px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .container-header { padding: 12px 14px; }
    .panggilan-cards { padding: 10px; gap: 10px; }
    .panggilan-card { padding: 12px; }
    .card-top { flex-direction: column; align-items: stretch; gap: 8px; }
    .status-badge { align-self: flex-start; }
    .detail-grid { grid-template-columns: 1fr; gap: 4px; }
    .siswa-nama { white-space: normal; }
    .toast-notification { top: 10px; right: 10px; left: 10px; min-width: auto; }
}

@media (max-width: 400px) {
    .stats-grid { grid-template-columns: 1fr 1fr; }
    .card-actions { flex-direction: column; }
    .btn-action { justify-content: center; }
}
</style>

<script>
function hideToast() {
    const t = document.getElementById('toastNotification');
    if (t) { t.style.animation = 'slideIn 0.3s ease reverse'; setTimeout(() => t.remove(), 300); }
}
setTimeout(hideToast, 5000);
</script>
@endsection
