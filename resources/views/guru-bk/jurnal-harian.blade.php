@extends('layouts.app-guru-bk')

@section('content')
<div class="main-content jurnal-page">

    {{-- Header --}}
    <div class="page-header-center">
        <div class="header-icon-large">
            <i class="fas fa-book-open"></i>
        </div>
        <h1>Jurnal Harian</h1>
        @if($periodeAktif)
        <div class="header-periode-badge"><i class="fas fa-calendar-alt"></i> {{ $periodeAktif->tahun_pelajaran }} - {{ $periodeAktif->semester }}</div>
        @endif
        <div style="margin-top: 14px;">
            <a href="{{ route('guru_bk.jurnal-manual.create') }}" class="btn-jurnal-manual"><i class="fas fa-pen-fancy"></i> Isi Jurnal Manual</a>
        </div>
    </div>

    {{-- Date Range Filter --}}
    <form method="GET" action="{{ route('guru_bk.jurnal-harian') }}" class="filter-card">
        <div class="filter-row">
            <div class="filter-group">
                <label><i class="fas fa-calendar"></i> Dari Tanggal</label>
                <input type="date" name="tanggal_mulai" class="filter-input" value="{{ $tanggalMulai }}">
            </div>
            <div class="filter-group">
                <label><i class="fas fa-calendar"></i> Sampai Tanggal</label>
                <input type="date" name="tanggal_akhir" class="filter-input" value="{{ $tanggalAkhir }}">
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Tampilkan</button>
                <a href="{{ route('guru_bk.jurnal-harian') }}" class="btn-reset"><i class="fas fa-redo"></i></a>
                <a href="{{ route('guru_bk.jurnal-harian.print', ['tanggal_mulai' => $tanggalMulai, 'tanggal_akhir' => $tanggalAkhir]) }}" target="_blank" class="btn-print-action"><i class="fas fa-print"></i> Cetak</a>
            </div>
        </div>
        <div class="filter-info">
            @php
                $mulai = \Carbon\Carbon::parse($tanggalMulai);
                $akhir = \Carbon\Carbon::parse($tanggalAkhir);
                $isSameDay = $mulai->isSameDay($akhir);
            @endphp
            <i class="fas fa-info-circle"></i>
            @if($isSameDay)
                Menampilkan aktivitas tanggal <strong>{{ $mulai->translatedFormat('d F Y') }}</strong>
            @else
                Menampilkan aktivitas dari <strong>{{ $mulai->translatedFormat('d M Y') }}</strong> s/d <strong>{{ $akhir->translatedFormat('d M Y') }}</strong>
            @endif
        </div>
    </form>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon total"><i class="fas fa-list-alt"></i></div>
            <div class="stat-info"><h3>{{ $stats['total'] }}</h3><p>Total Aktivitas</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bimbingan"><i class="fas fa-clipboard-list"></i></div>
            <div class="stat-info"><h3>{{ $stats['bimbingan'] }}</h3><p>Bimbingan</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon panggilan"><i class="fas fa-phone"></i></div>
            <div class="stat-info"><h3>{{ $stats['panggilan'] }}</h3><p>Panggilan</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon pelanggaran"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="stat-info"><h3>{{ $stats['pelanggaran'] }}</h3><p>Pelanggaran</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon manual"><i class="fas fa-pen-fancy"></i></div>
            <div class="stat-info"><h3>{{ $stats['manual'] }}</h3><p>Manual</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bimbingan-wali"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="stat-info"><h3>{{ $stats['bimbingan_wali'] }}</h3><p>Guru Wali</p></div>
        </div>
    </div>

    {{-- Timeline --}}
    <div class="timeline-container">
        <div class="container-header">
            <div class="container-title"><i class="fas fa-stream"></i><h2>Timeline Aktivitas</h2></div>
            <span class="count-badge">{{ $stats['total'] }} Aktivitas</span>
        </div>

        @if($activities->count() == 0)
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-calendar-check"></i></div>
            <h3>Belum Ada Aktivitas</h3>
            <p>Tidak ada aktivitas yang tercatat pada rentang tanggal ini.</p>
        </div>
        @else
        <div class="timeline-list">
            @php $currentDate = ''; @endphp
            @foreach($activities as $act)
                @php $actDate = \Carbon\Carbon::parse($act->tanggal)->translatedFormat('l, d F Y'); @endphp
                @if($actDate !== $currentDate)
                    @php $currentDate = $actDate; @endphp
                    <div class="timeline-date-header">
                        <i class="fas fa-calendar-day"></i> {{ $currentDate }}
                    </div>
                @endif
                <div class="timeline-item">
                    <div class="timeline-dot" style="background: {{ $act->color }};"></div>
                    <div class="timeline-content">
                        <div class="timeline-top">
                            <span class="activity-badge" style="background: {{ $act->color }}15; color: {{ $act->color }}; border: 1px solid {{ $act->color }}30;">
                                <i class="fas {{ $act->icon }}"></i> {{ $act->label }}
                            </span>
                            <span class="timeline-time"><i class="fas fa-clock"></i> {{ $act->waktu }}</span>
                        </div>
                        <div class="timeline-body">
                            <div class="timeline-siswa">
                                <div class="siswa-avatar {{ ($act->jk == 'Laki-laki') ? 'laki' : 'perempuan' }}">
                                    {{ strtoupper(substr($act->nama_siswa, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="siswa-nama">{{ $act->nama_siswa }}</div>
                                    <div class="siswa-meta">{{ $act->rombel }}</div>
                                </div>
                            </div>
                            <p class="timeline-detail">{{ $act->detail }}</p>
                            @if($act->sub_detail)
                            <p class="timeline-sub-detail">{{ $act->sub_detail }}</p>
                            @endif
                        </div>
                        <div class="timeline-footer">
                            @if($act->guru_bk)
                            <span class="guru-bk-tag"><i class="fas fa-user-shield"></i> {{ $act->guru_bk }}</span>
                            @endif
                            @if($act->status)
                            <span class="status-tag status-{{ strtolower(str_replace(' ', '-', $act->status)) }}">{{ $act->status }}</span>
                            @endif
                            @if($act->type === 'manual' && isset($act->manual_id))
                            <div class="timeline-actions">
                                <a href="{{ route('guru_bk.jurnal-manual.edit', $act->manual_id) }}" class="btn-action-edit" title="Edit"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('guru_bk.jurnal-manual.destroy', $act->manual_id) }}" class="inline-form" onsubmit="return confirm('Yakin ingin menghapus jurnal ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-delete" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<style>
.jurnal-page { padding: 30px; background: #f8f9fa; min-height: calc(100vh - 60px); }

/* Header */
.jurnal-page .page-header-center { text-align: center; margin-bottom: 25px; }
.jurnal-page .header-icon-large {
    width: 70px; height: 70px; border-radius: 18px;
    display: flex; align-items: center; justify-content: center;
    font-size: 32px; color: white; margin: 0 auto 16px;
    background: linear-gradient(135deg, #10b981, #059669);
    box-shadow: 0 8px 25px rgba(16,185,129,0.4);
}
.jurnal-page .page-header-center h1 { font-size: 24px; font-weight: 700; margin: 0 0 8px 0; color: #1f2937; }
.header-periode-badge {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(16,185,129,0.1); color: #059669;
    padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 600;
    border: 1px solid rgba(16,185,129,0.2);
}

/* Filter */
.filter-card {
    background: white; border-radius: 16px; padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06); margin-bottom: 20px; border: 1px solid #e5e7eb;
}
.filter-row { display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; }
.filter-group { flex: 1; min-width: 150px; }
.filter-group label { display: block; font-size: 12px; font-weight: 600; color: #6b7280; margin-bottom: 6px; }
.filter-group label i { color: #10b981; margin-right: 4px; }
.filter-input {
    width: 100%; padding: 10px 14px; border: 2px solid #e5e7eb; border-radius: 10px;
    font-size: 14px; transition: all 0.3s; box-sizing: border-box;
}
.filter-input:focus { outline: none; border-color: #10b981; box-shadow: 0 0 0 4px rgba(16,185,129,0.1); }
.filter-actions { display: flex; gap: 8px; }
.btn-filter {
    padding: 10px 20px; background: linear-gradient(135deg, #10b981, #059669);
    color: white; border: none; border-radius: 10px; font-weight: 600; font-size: 13px;
    cursor: pointer; display: flex; align-items: center; gap: 6px; white-space: nowrap;
    transition: all 0.3s; box-shadow: 0 4px 12px rgba(16,185,129,0.3);
}
.btn-filter:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(16,185,129,0.4); }
.btn-reset {
    padding: 10px 14px; background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb;
    border-radius: 10px; cursor: pointer; font-size: 13px; transition: all 0.2s; text-decoration: none;
    display: flex; align-items: center;
}
.btn-reset:hover { background: #e5e7eb; }
.btn-print-action {
    padding: 10px 20px; background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white; border: none; border-radius: 10px; font-weight: 600; font-size: 13px;
    cursor: pointer; display: flex; align-items: center; gap: 6px; white-space: nowrap;
    text-decoration: none; transition: all 0.3s; box-shadow: 0 4px 12px rgba(59,130,246,0.3);
}
.btn-print-action:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(59,130,246,0.4); color: white; }
.filter-info {
    margin-top: 12px; padding-top: 12px; border-top: 1px solid #f3f4f6;
    font-size: 12px; color: #6b7280; display: flex; align-items: center; gap: 6px;
}
.filter-info i { color: #10b981; }
.btn-jurnal-manual {
    display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px;
    background: linear-gradient(135deg, #f59e0b, #d97706); color: white;
    border-radius: 10px; font-weight: 600; font-size: 13px; text-decoration: none;
    transition: all 0.3s; box-shadow: 0 4px 12px rgba(245,158,11,0.3);
}
.btn-jurnal-manual:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(245,158,11,0.4); color: white; }

/* Stats */
.stats-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 12px; margin-bottom: 20px; }
.stat-card {
    background: white; padding: 14px 16px; border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; align-items: center; gap: 12px; border: 1px solid #e5e7eb;
}
.stat-icon {
    width: 42px; height: 42px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; font-size: 16px; color: white; flex-shrink: 0;
}
.stat-icon.total { background: linear-gradient(135deg, #10b981, #059669); }
.stat-icon.bimbingan { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.stat-icon.panggilan { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.stat-icon.pelanggaran { background: linear-gradient(135deg, #ef4444, #dc2626); }
.stat-icon.manual { background: linear-gradient(135deg, #f59e0b, #d97706); }
.stat-icon.bimbingan-wali { background: linear-gradient(135deg, #06b6d4, #0891b2); }
.stat-info h3 { margin: 0; font-size: 18px; font-weight: 700; color: #1f2937; }
.stat-info p { margin: 2px 0 0 0; color: #6b7280; font-size: 11px; }

/* Timeline Container */
.timeline-container {
    background: white; border-radius: 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow: hidden;
}
.container-header {
    padding: 16px 20px; border-bottom: 1px solid #e5e7eb;
    display: flex; justify-content: space-between; align-items: center;
}
.container-title { display: flex; align-items: center; gap: 10px; }
.container-title i { color: #10b981; font-size: 16px; }
.container-title h2 { margin: 0; font-size: 1rem; color: #1f2937; }
.count-badge {
    padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;
    background: rgba(16,185,129,0.1); color: #10b981;
}

/* Timeline Actions */
.timeline-actions { display: flex; gap: 6px; margin-left: auto; }
.btn-action-edit, .btn-action-delete {
    width: 30px; height: 30px; border-radius: 8px; display: flex;
    align-items: center; justify-content: center; font-size: 12px;
    border: 1px solid #e5e7eb; cursor: pointer; transition: all 0.2s; text-decoration: none;
}
.btn-action-edit { background: #eff6ff; color: #3b82f6; border-color: #bfdbfe; }
.btn-action-edit:hover { background: #3b82f6; color: white; }
.btn-action-delete { background: #fef2f2; color: #ef4444; border-color: #fecaca; }
.btn-action-delete:hover { background: #ef4444; color: white; }
.inline-form { display: inline; }

/* Empty State */
.empty-state { padding: 50px 20px; text-align: center; }
.empty-icon {
    width: 70px; height: 70px; background: #f0fdf4; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;
}
.empty-icon i { font-size: 28px; color: #10b981; }
.empty-state h3 { margin: 0 0 8px; color: #1f2937; font-size: 16px; }
.empty-state p { margin: 0; color: #6b7280; font-size: 14px; }

/* Timeline */
.timeline-list { padding: 20px; }
.timeline-date-header {
    font-size: 13px; font-weight: 700; color: #374151; margin: 20px 0 12px;
    padding: 8px 14px; background: #f9fafb; border-radius: 8px; border-left: 4px solid #10b981;
    display: flex; align-items: center; gap: 8px;
}
.timeline-date-header:first-child { margin-top: 0; }
.timeline-date-header i { color: #10b981; }

.timeline-item {
    display: flex; gap: 16px; padding: 14px; margin-bottom: 10px;
    border-radius: 12px; border: 1px solid #f3f4f6; transition: all 0.2s;
}
.timeline-item:hover { border-color: #e5e7eb; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
.timeline-dot {
    width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; margin-top: 6px;
    box-shadow: 0 0 0 4px rgba(0,0,0,0.04);
}
.timeline-content { flex: 1; min-width: 0; }
.timeline-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; gap: 8px; flex-wrap: wrap; }
.activity-badge {
    display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px;
    border-radius: 6px; font-size: 11px; font-weight: 600;
}
.timeline-time { font-size: 11px; color: #9ca3af; display: flex; align-items: center; gap: 4px; }

.timeline-body { margin-bottom: 8px; }
.timeline-siswa { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
.siswa-avatar {
    width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 12px; color: white;
}
.siswa-avatar.laki { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.siswa-avatar.perempuan { background: linear-gradient(135deg, #ec4899, #db2777); }
.siswa-nama { font-weight: 600; font-size: 13px; color: #1f2937; }
.siswa-meta { font-size: 11px; color: #6b7280; }
.timeline-detail { margin: 0; font-size: 13px; color: #374151; line-height: 1.5; }
.timeline-sub-detail { margin: 4px 0 0; font-size: 12px; color: #6b7280; font-style: italic; }

.timeline-footer { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.guru-bk-tag {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 10px; font-weight: 600; color: #7c3aed;
    background: rgba(124,58,237,0.08); padding: 2px 8px; border-radius: 6px;
}
.guru-bk-tag i { font-size: 9px; }
.status-tag {
    font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 6px;
}
.status-tag.status-belum { background: rgba(245,158,11,0.1); color: #d97706; }
.status-tag.status-proses { background: rgba(59,130,246,0.1); color: #3b82f6; }
.status-tag.status-selesai { background: rgba(16,185,129,0.1); color: #059669; }
.status-tag.status-menunggu { background: rgba(245,158,11,0.1); color: #d97706; }
.status-tag.status-hadir { background: rgba(16,185,129,0.1); color: #059669; }
.status-tag.status-tidak-hadir { background: rgba(239,68,68,0.1); color: #dc2626; }

/* Responsive */
@media (max-width: 768px) {
    .jurnal-page { padding: 12px; }
    .jurnal-page .header-icon-large { width: 56px; height: 56px; font-size: 26px; border-radius: 14px; margin-bottom: 12px; }
    .jurnal-page .page-header-center h1 { font-size: 20px; }
    .filter-row { flex-direction: column; gap: 10px; }
    .filter-group { min-width: auto; }
    .filter-actions { width: 100%; }
    .btn-filter { flex: 1; justify-content: center; }
    .stats-grid { display: flex; gap: 6px; }
    .stat-card { flex: 1; min-width: 0; padding: 8px 4px; flex-direction: column; gap: 2px; text-align: center; }
    .stat-icon { width: 28px; height: 28px; font-size: 11px; border-radius: 7px; margin: 0 auto; }
    .stat-info h3 { font-size: 15px; }
    .stat-info p { font-size: 8px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .timeline-list { padding: 12px; }
    .timeline-item { flex-direction: column; gap: 8px; padding: 12px; }
    .timeline-dot { width: 10px; height: 10px; margin: 0; }
    .timeline-top { flex-direction: column; align-items: flex-start; gap: 4px; }
    .container-header { padding: 12px 14px; }
}
</style>
@endsection
