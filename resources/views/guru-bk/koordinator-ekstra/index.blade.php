@extends('layouts.app-guru-bk')

@section('title', 'Manajemen Ekstrakurikuler | SISMIK')

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-guru-bk')

    <div class="main-content ekstra-page">
        {{-- Header --}}
        <div class="page-header-center">
            <div class="header-icon-large">
                <i class="fas fa-futbol"></i>
            </div>
            <h1>Manajemen Ekstrakurikuler</h1>
            <div class="header-periode-badge"><i class="fas fa-calendar-alt"></i> {{ $tahunAktif }} - {{ $semesterAktif }}</div>
            <div class="header-stats-row">
                <span class="header-stat"><i class="fas fa-chart-line"></i> {{ $totalAktif }} Aktif</span>
                <span class="header-stat"><i class="fas fa-database"></i> {{ count($ekstrakurikulerList) }} Data</span>
            </div>
            <div class="header-actions-row">
                <a href="{{ route('guru_bk.koordinator-ekstra.create') }}" class="btn-tambah-baru"><i class="fas fa-plus"></i> Tambah Baru</a>
                <a href="{{ url()->previous() }}" class="btn-kembali"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        </div>

        {{-- Content --}}
        <div class="content-section">
            <div class="section-header">
                <div class="section-title">
                    <i class="fas fa-list"></i>
                    <h2>Daftar Ekstrakurikuler</h2>
                </div>
                <span class="badge-count">{{ count($ekstrakurikulerList) }} Data</span>
            </div>

            @if(count($ekstrakurikulerList) > 0)
                @php
                    $icons = [
                        'Pramuka' => 'fa-campground', 'Paskibra' => 'fa-flag', 'PMR' => 'fa-heartbeat',
                        'OSIS' => 'fa-users-cog', 'Basket' => 'fa-basketball-ball', 'Futsal' => 'fa-futbol',
                        'Voli' => 'fa-volleyball-ball', 'Seni Musik' => 'fa-music', 'Seni Tari' => 'fa-gem',
                        'English Club' => 'fa-language', 'Japanese Club' => 'fa-language', 'IT Club' => 'fa-laptop-code',
                        'KIR' => 'fa-flask', 'Paduan Suara' => 'fa-microphone-alt'
                    ];
                    $colors = [
                        'Pramuka' => '#3b82f6', 'Paskibra' => '#ef4444', 'PMR' => '#dc2626',
                        'OSIS' => '#8b5cf6', 'Basket' => '#f59e0b', 'Futsal' => '#10b981',
                        'Voli' => '#ec4899', 'Seni Musik' => '#06b6d4', 'Seni Tari' => '#f97316',
                        'English Club' => '#6366f1', 'Japanese Club' => '#8b5cf6', 'IT Club' => '#0ea5e9',
                        'KIR' => '#84cc16', 'Paduan Suara' => '#d946ef'
                    ];
                @endphp
                <div class="ekstra-grid">
                    @foreach($ekstrakurikulerList as $ekstra)
                        @php
                            $isAktif = ($ekstra->tahun_pelajaran == $tahunAktif && $ekstra->semester == $semesterAktif);
                            $icon = $icons[$ekstra->nama_ekstrakurikuler] ?? 'fa-star';
                            $color = $colors[$ekstra->nama_ekstrakurikuler] ?? '#6b7280';
                            $pembinaList = array_filter([$ekstra->pembina_1, $ekstra->pembina_2, $ekstra->pembina_3]);
                        @endphp
                        <div class="ekstra-card {{ $isAktif ? 'active' : '' }}">
                            <div class="ekstra-status">
                                @if($isAktif)
                                    <span class="status-badge active"><i class="fas fa-check-circle"></i> Aktif</span>
                                @else
                                    <span class="status-badge inactive"><i class="fas fa-clock"></i> Arsip</span>
                                @endif
                            </div>
                            
                            <div class="ekstra-header">
                                <div class="ekstra-icon" style="background: {{ $color }};">
                                    <i class="fas {{ $icon }}"></i>
                                </div>
                                <div class="ekstra-title">
                                    <h3>{{ $ekstra->nama_ekstrakurikuler }}</h3>
                                    <div class="ekstra-meta">
                                        <span class="meta-item"><i class="fas fa-calendar-alt"></i> {{ $ekstra->tahun_pelajaran }}</span>
                                        <span class="meta-badge semester-{{ strtolower($ekstra->semester) }}">{{ $ekstra->semester }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="ekstra-info">
                                <div class="info-grid">
                                    <a href="{{ route('guru_bk.anggota-ekstra', $ekstra->id) }}" class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <div>
                                            <div class="info-value">{{ $ekstra->jumlah_anggota }}</div>
                                            <div class="info-label">Anggota</div>
                                        </div>
                                    </a>
                                    <a href="{{ route('guru_bk.tugas-tambahan') }}" class="info-item">
                                        <div class="info-icon" style="background: rgba(245,158,11,0.1); color: #f59e0b;">
                                            <i class="fas fa-trophy"></i>
                                        </div>
                                        <div>
                                            <div class="info-value">{{ $ekstra->jumlah_prestasi }}</div>
                                            <div class="info-label">Prestasi</div>
                                        </div>
                                    </a>
                                </div>
                                
                                @if(!empty($pembinaList))
                                    <div class="pembina-list">
                                        <div class="pembina-label"><i class="fas fa-user-tie"></i> Pembina:</div>
                                        <div class="pembina-names">{{ implode(', ', $pembinaList) }}</div>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="ekstra-actions">
                                <a href="{{ route('guru_bk.koordinator-ekstra.edit', $ekstra->id) }}" class="btn-action btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button onclick="confirmDelete({{ $ekstra->id }}, '{{ addslashes($ekstra->nama_ekstrakurikuler) }}')" class="btn-action btn-danger">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon"><i class="fas fa-futbol"></i></div>
                    <h3>Tidak Ada Data Ekstrakurikuler</h3>
                    <p>Belum ada data ekstrakurikuler untuk periode aktif</p>
                    <a href="{{ route('guru_bk.koordinator-ekstra.create') }}" class="btn-tambah-baru" style="margin-top: 16px;">
                        <i class="fas fa-plus"></i> Tambah Ekstrakurikuler
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-content">
        <div class="modal-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <h3 style="margin: 0 0 10px; color: #1f2937;">Konfirmasi Hapus</h3>
        <p style="color: #6b7280; margin-bottom: 10px;">Apakah Anda yakin ingin menghapus ekstrakurikuler ini?</p>
        <div style="background: #fef3c7; padding: 10px; border-radius: 8px; font-size: 12px; color: #92400e;">
            <i class="fas fa-exclamation-circle"></i> Penghapusan akan menghapus semua data anggota terkait.
        </div>
        <div class="modal-actions">
            <button onclick="closeDeleteModal()" class="btn btn-secondary">Batal</button>
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-primary" style="background: #ef4444;">Hapus</button>
            </form>
        </div>
    </div>
</div>

<style>
/* Page */
.ekstra-page { padding: 30px; background: #f8f9fa; min-height: calc(100vh - 60px); }

/* Header - Jurnal Harian Style */
.ekstra-page .page-header-center { text-align: center; margin-bottom: 25px; }
.ekstra-page .header-icon-large {
    width: 70px; height: 70px; border-radius: 18px;
    display: flex; align-items: center; justify-content: center;
    font-size: 32px; color: white; margin: 0 auto 16px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    box-shadow: 0 8px 25px rgba(59,130,246,0.4);
}
.ekstra-page .page-header-center h1 { font-size: 24px; font-weight: 700; margin: 0 0 8px 0; color: #1f2937; }
.ekstra-page .header-periode-badge {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(59,130,246,0.1); color: #1d4ed8;
    padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 600;
    border: 1px solid rgba(59,130,246,0.2);
}
.header-stats-row {
    display: flex; justify-content: center; gap: 16px; margin-top: 10px;
}
.header-stat {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 13px; color: #6b7280; font-weight: 500;
}
.header-stat i { color: #3b82f6; }
.btn-tambah-baru {
    display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px;
    background: linear-gradient(135deg, #10b981, #059669); color: white;
    border-radius: 10px; font-weight: 600; font-size: 13px; text-decoration: none;
    transition: all 0.3s; box-shadow: 0 4px 12px rgba(16,185,129,0.3);
}
.btn-tambah-baru:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(16,185,129,0.4); color: white; }
.header-actions-row {
    display: flex; justify-content: center; gap: 10px; margin-top: 14px; flex-wrap: wrap;
}
.btn-kembali {
    display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px;
    background: white; color: #374151; border: 2px solid #d1d5db;
    border-radius: 10px; font-weight: 600; font-size: 13px; text-decoration: none;
    transition: all 0.3s;
}
.btn-kembali:hover { background: #f3f4f6; transform: translateY(-2px); color: #1f2937; }

/* Content Section */
.content-section {
    background: white; border-radius: 16px; padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06); border: 1px solid #e5e7eb;
}
.section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
.section-title { display: flex; align-items: center; gap: 0.75rem; }
.section-title i { color: #3b82f6; }
.section-title h2 { margin: 0; font-size: 1.125rem; color: #1f2937; }
.badge-count { background: #3b82f6; color: white; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; }

/* Ekstra Grid */
.ekstra-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.25rem; }

/* Ekstra Card */
.ekstra-card {
    background: white; border-radius: 12px; border: 1px solid #e5e7eb;
    overflow: hidden; transition: all 0.3s ease;
}
.ekstra-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.1); }
.ekstra-card.active { border-color: #10b981; border-width: 2px; }
.ekstra-status { padding: 8px 12px; background: #f8fafc; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: flex-end; }
.status-badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; }
.status-badge.active { background: #d1fae5; color: #10b981; }
.status-badge.inactive { background: #f3f4f6; color: #6b7280; }
.ekstra-header { padding: 1rem; display: flex; align-items: flex-start; gap: 12px; }
.ekstra-icon { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.25rem; flex-shrink: 0; }
.ekstra-title h3 { margin: 0 0 6px 0; font-size: 1rem; font-weight: 600; color: #1f2937; }
.ekstra-meta { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.meta-item { font-size: 12px; color: #6b7280; display: flex; align-items: center; gap: 4px; }
.meta-badge { padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 500; }
.semester-ganjil { background: #dbeafe; color: #1d4ed8; }
.semester-genap { background: #fce7f3; color: #db2777; }
.ekstra-info { padding: 0 1rem 1rem; }
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px; }
.info-item { display: flex; align-items: center; gap: 10px; padding: 10px; background: #f8fafc; border-radius: 8px; text-decoration: none; transition: all 0.2s ease; }
.info-item:hover { background: #f1f5f9; }
.info-icon { width: 36px; height: 36px; background: rgba(59,130,246,0.1); color: #3b82f6; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
.info-value { font-size: 1.125rem; font-weight: 700; color: #1f2937; }
.info-label { font-size: 11px; color: #6b7280; }
.pembina-list { background: #fffbeb; border: 1px solid #fcd34d; border-radius: 8px; padding: 10px 12px; font-size: 12px; }
.pembina-label { color: #92400e; font-weight: 600; margin-bottom: 4px; display: flex; align-items: center; gap: 6px; }
.pembina-names { color: #78350f; }
.ekstra-actions { padding: 12px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 8px; }
.btn-action { padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 500; display: inline-flex; align-items: center; gap: 5px; text-decoration: none; transition: all 0.2s ease; border: none; cursor: pointer; }
.btn-action:hover { transform: translateY(-1px); }
.btn-action.btn-edit { background: #dbeafe; color: #1d4ed8; }
.btn-action.btn-danger { background: #fee2e2; color: #dc2626; }

/* Empty State */
.empty-state { text-align: center; padding: 60px 20px; }
.empty-icon { width: 70px; height: 70px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
.empty-icon i { font-size: 28px; color: #d1d5db; }
.empty-state h3 { margin: 0 0 10px; color: #374151; }
.empty-state p { color: #6b7280; margin-bottom: 0; }

/* Modal */
.modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 9999; }
.modal-content { background: white; padding: 30px; border-radius: 16px; width: 420px; max-width: 90%; text-align: center; }
.modal-icon { font-size: 48px; color: #f59e0b; margin-bottom: 15px; }
.modal-actions { display: flex; gap: 12px; justify-content: center; margin-top: 20px; }

/* Mobile Responsive */
@media (max-width: 768px) {
    .ekstra-page { padding: 12px; }
    .ekstra-page .header-icon-large { width: 56px; height: 56px; font-size: 26px; border-radius: 14px; margin-bottom: 12px; }
    .ekstra-page .page-header-center h1 { font-size: 20px; }
    .header-stats-row { gap: 10px; }
    .header-stat { font-size: 12px; }
    .ekstra-grid { grid-template-columns: 1fr; gap: 1rem; }
    .content-section { padding: 1rem; }
    .section-header { flex-wrap: wrap; gap: 8px; }
    .ekstra-header { padding: 0.75rem; }
    .ekstra-icon { width: 40px; height: 40px; font-size: 1rem; }
    .ekstra-title h3 { font-size: 0.9rem; }
    .info-grid { gap: 8px; }
    .info-item { padding: 8px; gap: 8px; }
    .info-icon { width: 30px; height: 30px; font-size: 12px; }
    .info-value { font-size: 1rem; }
    .pembina-list { font-size: 11px; padding: 8px 10px; }
    .ekstra-actions { padding: 10px; }
    .modal-content { padding: 20px; }
}

@media (max-width: 480px) {
    .ekstra-page { padding: 8px; }
    .ekstra-page .header-icon-large { width: 48px; height: 48px; font-size: 22px; border-radius: 12px; }
    .ekstra-page .page-header-center h1 { font-size: 18px; }
    .header-periode-badge { font-size: 11px; padding: 4px 12px; }
    .header-stats-row { flex-direction: column; gap: 4px; }
    .btn-tambah-baru { font-size: 12px; padding: 8px 18px; }
    .content-section { padding: 0.75rem; border-radius: 12px; }
    .section-title h2 { font-size: 0.95rem; }
    .badge-count { font-size: 11px; padding: 3px 10px; }
}
</style>
@endsection

@push('scripts')
<script>
    function confirmDelete(id, nama) {
        document.getElementById('deleteForm').action = '{{ url("guru-bk/koordinator-ekstra") }}/' + id;
        document.getElementById('deleteModal').style.display = 'flex';
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }
    
    document.getElementById('deleteModal').addEventListener('click', (e) => {
        if (e.target.id === 'deleteModal') closeDeleteModal();
    });
</script>
@endpush
