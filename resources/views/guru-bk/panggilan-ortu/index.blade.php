@extends('layouts.app-guru-bk')

@section('content')
<div class="main-content panggilan-ortu-page">
    {{-- HEADER SECTION - Dashboard Style --}}
    @php
        $foto_exists = $siswa->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/' . $siswa->foto);
        $initials = collect(explode(' ', $siswa->nama))
            ->map(fn($p) => strtoupper(substr($p, 0, 1)))
            ->take(2)
            ->join('');
    @endphp
    
    <div class="bk-page-header">
        <div class="header-content-wrapper">
            <div class="header-icon-box clickable-avatar" onclick="showPhotoModal('{{ $siswa->nama }}', '{{ $foto_exists ? asset('storage/siswa/' . $siswa->foto) : '' }}', '{{ $initials ?: 'S' }}', '{{ $siswa->jk }}')" title="Lihat Foto">
                @if($foto_exists)
                    <img src="{{ asset('storage/siswa/' . $siswa->foto) }}" alt="{{ $siswa->nama }}" class="header-photo-img">
                @else
                    <i class="fas fa-phone"></i>
                @endif
                <div class="avatar-overlay"><i class="fas fa-search-plus"></i></div>
            </div>
            <div class="header-info">
                <div class="header-greeting">
                    <span class="greeting-text">Panggilan Orang Tua</span>
                    <h1>{{ $siswa->nama }}</h1>
                </div>
                <div class="header-details">
                    <span class="detail-badge"><i class="fas fa-id-card"></i> {{ $siswa->nisn }}</span>
                    <span class="detail-badge"><i class="fas fa-chalkboard-teacher"></i> {{ $siswa->nama_rombel ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
    @endif

    {{-- STATS ROW - Single Row --}}
    <div class="stats-row-container">
        <div class="stats-row">
            <div class="stat-card-mini primary">
                <div class="stat-icon"><i class="fas fa-envelope"></i></div>
                <div>
                    <h3>{{ $total_panggilan }}</h3>
                    <p>Total</p>
                </div>
            </div>
            <div class="stat-card-mini warning">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div>
                    <h3>{{ $stats['Menunggu'] ?? 0 }}</h3>
                    <p>Menunggu</p>
                </div>
            </div>
            <div class="stat-card-mini success">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div>
                    <h3>{{ $stats['Hadir'] ?? 0 }}</h3>
                    <p>Hadir</p>
                </div>
            </div>
            <div class="stat-card-mini danger">
                <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
                <div>
                    <h3>{{ $stats['Tidak Hadir'] ?? 0 }}</h3>
                    <p>Tidak Hadir</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ACTION BUTTONS ROW --}}
    <div class="action-buttons-row">
        <a href="{{ route('guru_bk.siswa-bimbingan') }}" class="btn-action-header btn-secondary-header">
            <i class="fas fa-arrow-left"></i> <span class="btn-text">Kembali</span>
        </a>
        <a href="{{ route('guru_bk.panggilan-ortu.create', $nisn) }}" class="btn-action-header btn-primary-header">
            <i class="fas fa-plus"></i> <span class="btn-text">Buat Surat Panggilan</span>
        </a>
    </div>

    {{-- RIWAYAT PANGGILAN - Collapsible Cards --}}
    <div class="section-header">
        <h2><i class="fas fa-history"></i> Riwayat Panggilan Orang Tua</h2>
    </div>

    @if($panggilan_list->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-envelope-open"></i>
        </div>
        <h3>Belum Ada Panggilan</h3>
        <p>Belum ada surat panggilan orang tua untuk siswa ini.</p>
        <a href="{{ route('guru_bk.panggilan-ortu.create', $nisn) }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Buat Surat Panggilan Pertama
        </a>
    </div>
    @else
    <div class="panggilan-cards-container">
        @foreach($panggilan_list as $index => $p)
        @php
            $status_config = [
                'Menunggu' => ['icon' => 'fa-clock', 'color' => '#f59e0b', 'bg' => 'warning'],
                'Hadir' => ['icon' => 'fa-check-circle', 'color' => '#22c55e', 'bg' => 'success'],
                'Tidak Hadir' => ['icon' => 'fa-times-circle', 'color' => '#ef4444', 'bg' => 'danger'],
                'Dijadwalkan Ulang' => ['icon' => 'fa-calendar-alt', 'color' => '#3b82f6', 'bg' => 'info']
            ];
            $sc = $status_config[$p->status] ?? $status_config['Menunggu'];
            $card_id = 'panggilan_' . $p->id;
        @endphp
        
        <div class="panggilan-card {{ $index === 0 ? 'expanded' : '' }}" data-card="{{ $card_id }}">
            <div class="panggilan-card-header" onclick="togglePanggilanCard('{{ $card_id }}')">
                <div class="panggilan-header-left">
                    <div class="panggilan-icon {{ $sc['bg'] }}">
                        <i class="fas {{ $sc['icon'] }}"></i>
                    </div>
                    <div class="panggilan-header-info">
                        <div class="panggilan-title-row">
                            <h3>{{ $p->perihal }}</h3>
                            <span class="status-badge {{ $sc['bg'] }}">
                                <i class="fas {{ $sc['icon'] }}"></i> {{ $p->status }}
                            </span>
                        </div>
                        <div class="panggilan-meta">
                            <span class="no-surat"><i class="fas fa-file-alt"></i> {{ $p->no_surat ?: '-' }}</span>
                            <span class="tgl-panggilan"><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($p->tanggal_panggilan)->format('d M Y') }}</span>
                            @if($p->jam_panggilan)
                            <span class="jam-panggilan"><i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($p->jam_panggilan)->format('H:i') }} WIB</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="panggilan-header-right">
                    <div class="panggilan-toggle">
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                </div>
            </div>

            <div class="panggilan-card-content">
                <div class="panggilan-detail-grid">
                    <div class="detail-item">
                        <label><i class="fas fa-calendar-alt"></i> Tanggal Surat</label>
                        <span>{{ \Carbon\Carbon::parse($p->tanggal_surat)->format('d F Y') }}</span>
                    </div>
                    <div class="detail-item">
                        <label><i class="fas fa-user-tie"></i> Menghadap Ke</label>
                        <span>{{ $p->menghadap_ke ?? 'Guru BK' }}</span>
                    </div>
                    <div class="detail-item">
                        <label><i class="fas fa-map-marker-alt"></i> Tempat</label>
                        <span>{{ property_exists($p, 'tempat') && $p->tempat ? $p->tempat : 'Ruang BK' }}</span>
                    </div>
                    <div class="detail-item">
                        <label><i class="fas fa-info-circle"></i> Status</label>
                        <span class="status-text {{ $sc['bg'] }}">{{ $p->status }}</span>
                    </div>
                </div>

                @if(property_exists($p, 'keterangan') && $p->keterangan)
                <div class="panggilan-keterangan">
                    <label><i class="fas fa-sticky-note"></i> Keterangan</label>
                    <p>{{ $p->keterangan }}</p>
                </div>
                @endif

                <div class="panggilan-actions">
                    <a href="{{ route('guru_bk.panggilan-ortu.edit', $p->id) }}" class="btn-action-card edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('guru_bk.panggilan-ortu.print', $p->id) }}" target="_blank" class="btn-action-card print">
                        <i class="fas fa-print"></i> Cetak
                    </a>
                    <button type="button" class="btn-action-card delete" onclick="confirmDelete({{ $p->id }}, '{{ $nisn }}')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- PHOTO MODAL --}}
<div id="photoModal" class="photo-modal-overlay" style="display: none;">
    <div class="photo-modal-content">
        <button type="button" class="photo-modal-close" onclick="closePhotoModal()">
            <i class="fas fa-times"></i>
        </button>
        <div class="photo-modal-body">
            <div id="photoModalImage" class="photo-modal-image"></div>
            <div class="photo-modal-name" id="photoModalName"></div>
        </div>
    </div>
</div>

{{-- DELETE CONFIRMATION MODAL --}}
<div id="deleteModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-icon warning">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3>Konfirmasi Hapus</h3>
        <p>Apakah yakin Anda akan menghapus data panggilan ini?</p>
        <form id="deleteForm" method="POST" action="{{ route('guru_bk.panggilan-ortu.delete') }}">
            @csrf
            <input type="hidden" name="id" id="deleteId">
            <input type="hidden" name="nisn" id="deleteNisn">
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Batal</button>
                <button type="submit" class="btn-confirm-delete">Hapus</button>
            </div>
        </form>
    </div>
</div>

<style>
.main-content.panggilan-ortu-page {
    padding: 25px;
    background: #f9fafb;
    min-height: calc(100vh - 70px);
}

/* ============== HEADER - Dashboard Style ============== */
.panggilan-ortu-page .bk-page-header {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    border-radius: 16px;
    padding: 25px 30px;
    margin-bottom: 20px;
    box-shadow: 0 10px 30px rgba(124, 58, 237, 0.2);
}

.panggilan-ortu-page .header-content-wrapper {
    display: flex;
    align-items: center;
    gap: 20px;
}

.panggilan-ortu-page .header-icon-box {
    width: 70px;
    height: 70px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: white;
    flex-shrink: 0;
    position: relative;
    overflow: hidden;
}

.panggilan-ortu-page .header-icon-box.clickable-avatar {
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.panggilan-ortu-page .header-icon-box.clickable-avatar:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(255, 255, 255, 0.3);
}

.panggilan-ortu-page .header-photo-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.panggilan-ortu-page .avatar-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.panggilan-ortu-page .header-icon-box.clickable-avatar:hover .avatar-overlay {
    opacity: 1;
}

.panggilan-ortu-page .avatar-overlay i {
    color: white;
    font-size: 18px;
}

.panggilan-ortu-page .header-info { flex: 1; }

.panggilan-ortu-page .header-greeting .greeting-text {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.8);
    font-weight: 500;
    display: block;
    margin-bottom: 4px;
}

.panggilan-ortu-page .header-greeting h1 {
    font-size: 22px;
    font-weight: 700;
    color: white;
    margin: 0;
}

.panggilan-ortu-page .header-details {
    display: flex;
    gap: 12px;
    margin-top: 10px;
    flex-wrap: wrap;
}

.panggilan-ortu-page .detail-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.15);
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    color: rgba(255, 255, 255, 0.9);
}

/* ============== STATS ROW ============== */
.stats-row-container {
    background: white;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.stats-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.stat-card-mini {
    flex: 1;
    min-width: 100px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    border-radius: 12px;
    text-decoration: none;
}

.stat-card-mini.primary {
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
}

.stat-card-mini.warning {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
}

.stat-card-mini.success {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
}

.stat-card-mini.danger {
    background: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
}

.stat-card-mini .stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    color: white;
}

.stat-card-mini.primary .stat-icon { background: #0ea5e9; color: white; }
.stat-card-mini.warning .stat-icon { background: #f59e0b; color: white; }
.stat-card-mini.success .stat-icon { background: #10b981; color: white; }
.stat-card-mini.danger .stat-icon { background: #ef4444; color: white; }

.stat-card-mini h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: #1f2937;
}

.stat-card-mini p {
    margin: 0;
    font-size: 11px;
    color: #6b7280;
}

/* ============== ACTION BUTTONS ROW ============== */
.action-buttons-row {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 20px;
}

.btn-action-header {
    padding: 12px 20px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary-header {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);
}

.btn-primary-header:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(124, 58, 237, 0.4);
}

.btn-secondary-header {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #e5e7eb;
}

.btn-secondary-header:hover {
    background: #e5e7eb;
}

/* ============== SECTION HEADER ============== */
.section-header {
    margin-bottom: 15px;
}

.section-header h2 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-header h2 i {
    color: #7c3aed;
}

/* ============== PANGGILAN CARDS ============== */
.panggilan-cards-container {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.panggilan-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    overflow: hidden;
}

.panggilan-card-header {
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    transition: background 0.3s ease;
}

.panggilan-card-header:hover {
    background: #f9fafb;
}

.panggilan-header-left {
    display: flex;
    align-items: center;
    gap: 15px;
    flex: 1;
}

.panggilan-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: white;
    flex-shrink: 0;
}

.panggilan-icon.warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.panggilan-icon.success { background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); }
.panggilan-icon.danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
.panggilan-icon.info { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }

.panggilan-header-info { flex: 1; }

.panggilan-title-row {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.panggilan-title-row h3 {
    margin: 0;
    font-size: 15px;
    font-weight: 600;
    color: #1f2937;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 600;
}

.status-badge.warning { background: #fef3c7; color: #d97706; }
.status-badge.success { background: #d1fae5; color: #059669; }
.status-badge.danger { background: #fee2e2; color: #dc2626; }
.status-badge.info { background: #dbeafe; color: #2563eb; }

.panggilan-meta {
    display: flex;
    gap: 15px;
    margin-top: 5px;
    font-size: 12px;
    color: #6b7280;
    flex-wrap: wrap;
}

.panggilan-meta span {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.panggilan-toggle {
    width: 32px;
    height: 32px;
    background: #f3f4f6;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
}

.panggilan-card.expanded .toggle-icon {
    transform: rotate(180deg);
}

.toggle-icon {
    transition: transform 0.3s ease;
}

/* Card Content */
.panggilan-card-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s ease, opacity 0.3s ease;
    opacity: 0;
    border-top: 1px solid #f3f4f6;
}

.panggilan-card.expanded .panggilan-card-content {
    max-height: 500px;
    opacity: 1;
    padding: 20px;
}

.panggilan-detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 15px;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.detail-item label {
    font-size: 11px;
    color: #9ca3af;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 5px;
}

.detail-item span {
    font-size: 14px;
    color: #1f2937;
    font-weight: 500;
}

.detail-item .status-text {
    display: inline-flex;
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
    width: fit-content;
}

.detail-item .status-text.warning { background: #fef3c7; color: #d97706; }
.detail-item .status-text.success { background: #d1fae5; color: #059669; }
.detail-item .status-text.danger { background: #fee2e2; color: #dc2626; }
.detail-item .status-text.info { background: #dbeafe; color: #2563eb; }

.panggilan-keterangan {
    background: #f9fafb;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 15px;
}

.panggilan-keterangan label {
    font-size: 11px;
    color: #9ca3af;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 5px;
    margin-bottom: 5px;
}

.panggilan-keterangan p {
    margin: 0;
    font-size: 13px;
    color: #374151;
    line-height: 1.5;
}

.panggilan-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    padding-top: 15px;
    border-top: 1px solid #f3f4f6;
}

.btn-action-card {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-action-card.edit {
    background: #e0e7ff;
    color: #4f46e5;
}

.btn-action-card.print {
    background: #d1fae5;
    color: #059669;
}

.btn-action-card.delete {
    background: #fee2e2;
    color: #dc2626;
}

.btn-action-card:hover {
    transform: translateY(-2px);
}

/* ============== ALERTS ============== */
.alert {
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
}

.alert-success {
    background: #f0fdf4;
    color: #059669;
    border: 1px solid #a7f3d0;
}

.alert-danger {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

/* ============== EMPTY STATE ============== */
.empty-state {
    background: white;
    padding: 60px 20px;
    text-align: center;
    border-radius: 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
}

.empty-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.empty-icon i {
    font-size: 32px;
    color: white;
}

.empty-state h3 {
    margin: 0 0 10px 0;
    color: #1f2937;
    font-size: 18px;
    font-weight: 600;
}

.empty-state p {
    margin: 0 0 20px 0;
    color: #6b7280;
    font-size: 14px;
}

/* ============== PHOTO MODAL ============== */
.photo-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(8px);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.photo-modal-content {
    position: relative;
    max-width: 90%;
    max-height: 90%;
    animation: zoomIn 0.3s ease;
}

@keyframes zoomIn {
    from { transform: scale(0.8); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

.photo-modal-close {
    position: absolute;
    top: -15px;
    right: -15px;
    width: 40px;
    height: 40px;
    background: white;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 18px;
    color: #374151;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
    z-index: 10001;
}

.photo-modal-close:hover {
    background: #ef4444;
    color: white;
}

.photo-modal-body { text-align: center; }

.photo-modal-image {
    width: 280px;
    height: 280px;
    border-radius: 50%;
    overflow: hidden;
    border: 5px solid white;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
    margin: 0 auto 20px;
}

.photo-modal-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.photo-modal-image .modal-avatar-initial {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 100px;
    font-weight: 700;
    color: white;
}

.photo-modal-image .modal-avatar-initial.male {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.photo-modal-image .modal-avatar-initial.female {
    background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
}

.photo-modal-name {
    color: white;
    font-size: 1.5rem;
    font-weight: 600;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
}

/* ============== DELETE MODAL ============== */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.modal-content {
    background: white;
    padding: 30px;
    border-radius: 16px;
    width: 400px;
    text-align: center;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}

.modal-icon {
    font-size: 48px;
    margin-bottom: 15px;
}

.modal-icon.warning { color: #f59e0b; }

.modal-content h3 {
    margin: 0 0 10px 0;
    color: #1f2937;
    font-size: 20px;
    font-weight: 600;
}

.modal-content p {
    margin: 0 0 20px 0;
    color: #6b7280;
    font-size: 14px;
}

.modal-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
}

.btn-cancel, .btn-confirm-delete {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-cancel {
    background: #6b7280;
    color: white;
}

.btn-confirm-delete {
    background: #ef4444;
    color: white;
}

.btn-confirm-delete:hover {
    background: #dc2626;
}

/* ============== RESPONSIVE ============== */
@media (max-width: 768px) {
    .main-content.panggilan-ortu-page { padding: 15px; }
    
    /* Header Mobile - Centered */
    .panggilan-ortu-page .bk-page-header {
        padding: 15px;
        border-radius: 12px;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .panggilan-ortu-page .header-content-wrapper {
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 10px;
    }
    
    .panggilan-ortu-page .header-icon-box {
        width: 60px;
        height: 60px;
        font-size: 24px;
        border-radius: 50%;
    }
    
    .panggilan-ortu-page .header-info { text-align: center; }
    
    .panggilan-ortu-page .header-greeting .greeting-text { font-size: 11px; }
    .panggilan-ortu-page .header-greeting h1 { font-size: 16px; }
    
    .panggilan-ortu-page .header-details { 
        justify-content: center; 
        gap: 6px;
        margin-top: 8px;
    }
    
    .panggilan-ortu-page .detail-badge {
        font-size: 9px;
        padding: 4px 8px;
    }
    
    /* Stats Cards Mobile - Single Horizontal Row */
    .stats-row-container { margin-bottom: 12px; }
    
    .stats-row {
        display: flex;
        flex-wrap: nowrap;
        gap: 6px;
        overflow-x: auto;
        padding-bottom: 5px;
        -webkit-overflow-scrolling: touch;
    }
    
    .stat-card-mini {
        flex: 1;
        min-width: 70px;
        padding: 8px 6px;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 4px;
    }
    
    .stat-card-mini .stat-icon {
        width: 28px;
        height: 28px;
        font-size: 12px;
        border-radius: 8px;
    }
    
    .stat-card-mini h3 { font-size: 16px; }
    .stat-card-mini p { font-size: 9px; white-space: nowrap; }
    
    /* Action Buttons Mobile - Side by Side */
    .action-buttons-row { 
        flex-direction: row;
        gap: 8px;
        margin-bottom: 15px;
    }
    
    .btn-action-header { 
        flex: 1;
        justify-content: center;
        padding: 10px 12px;
        font-size: 11px;
    }
    
    /* Section Header Mobile */
    .section-header { margin-bottom: 10px; }
    .section-header h2 { font-size: 14px; }
    
    /* Panggilan Cards Mobile */
    .panggilan-cards-container { gap: 8px; }
    
    .panggilan-card-header { padding: 12px 15px; }
    
    .panggilan-icon {
        width: 38px;
        height: 38px;
        font-size: 14px;
        border-radius: 10px;
    }
    
    .panggilan-header-left { gap: 10px; }
    
    .panggilan-title-row { 
        flex-direction: column; 
        align-items: flex-start; 
        gap: 6px; 
    }
    
    .panggilan-title-row h3 { font-size: 13px; }
    
    .status-badge { font-size: 9px; padding: 3px 8px; }
    
    .panggilan-meta { 
        flex-direction: column; 
        gap: 3px;
        font-size: 10px;
    }
    
    .panggilan-toggle {
        width: 28px;
        height: 28px;
    }
    
    /* Card Content Mobile */
    .panggilan-card.expanded .panggilan-card-content {
        padding: 15px;
    }
    
    .panggilan-detail-grid { 
        grid-template-columns: 1fr; 
        gap: 10px;
    }
    
    .detail-item label { font-size: 10px; }
    .detail-item span { font-size: 12px; }
    
    .panggilan-keterangan { padding: 10px; }
    .panggilan-keterangan label { font-size: 10px; }
    .panggilan-keterangan p { font-size: 12px; }
    
    .panggilan-actions { 
        justify-content: center;
        flex-wrap: wrap;
        gap: 6px;
        padding-top: 10px;
    }
    
    .btn-action-card {
        padding: 6px 12px;
        font-size: 11px;
        flex: 1;
        min-width: 80px;
        justify-content: center;
    }
    
    /* Empty State Mobile */
    .empty-state { padding: 40px 15px; }
    .empty-icon { width: 60px; height: 60px; }
    .empty-icon i { font-size: 24px; }
    .empty-state h3 { font-size: 16px; }
    .empty-state p { font-size: 12px; }
    
    /* Photo Modal Mobile */
    .photo-modal-image { width: 200px; height: 200px; }
    .photo-modal-image .modal-avatar-initial { font-size: 60px; }
    .photo-modal-name { font-size: 1.2rem; }
    
    /* Delete Modal Mobile */
    .modal-content { width: 90%; padding: 20px; }
    .modal-icon { font-size: 36px; }
    .modal-content h3 { font-size: 16px; }
    .modal-content p { font-size: 12px; }
    .modal-actions { flex-direction: column; gap: 8px; }
    .btn-cancel, .btn-confirm-delete { width: 100%; }
}
</style>

<script>
function togglePanggilanCard(cardId) {
    const card = document.querySelector(`[data-card="${cardId}"]`);
    if (!card) return;

    const content = card.querySelector('.panggilan-card-content');
    const isExpanding = !card.classList.contains('expanded');

    if (isExpanding) {
        card.classList.add('expanded');
        content.style.maxHeight = content.scrollHeight + 'px';
        content.style.opacity = '1';
        setTimeout(() => {
            if (card.classList.contains('expanded')) {
                content.style.maxHeight = 'none';
            }
        }, 400);
    } else {
        content.style.maxHeight = content.scrollHeight + 'px';
        content.offsetHeight;
        content.style.maxHeight = '0';
        content.style.opacity = '0';
        setTimeout(() => {
            card.classList.remove('expanded');
        }, 300);
    }
}

function confirmDelete(id, nisn) {
    document.getElementById('deleteId').value = id;
    document.getElementById('deleteNisn').value = nisn;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// Photo Modal Functions
function showPhotoModal(nama, fotoUrl, initials, jk) {
    const modal = document.getElementById('photoModal');
    const imageContainer = document.getElementById('photoModalImage');
    const nameElement = document.getElementById('photoModalName');
    
    nameElement.textContent = nama;
    
    if (fotoUrl && fotoUrl.trim() !== '') {
        imageContainer.innerHTML = `<img src="${fotoUrl}" alt="${nama}">`;
    } else {
        const genderClass = jk === 'Laki-laki' || jk === 'L' ? 'male' : 'female';
        imageContainer.innerHTML = `<div class="modal-avatar-initial ${genderClass}">${initials}</div>`;
    }
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closePhotoModal() {
    document.getElementById('photoModal').style.display = 'none';
    document.body.style.overflow = '';
}

// Close modals on overlay click
document.addEventListener('click', function(e) {
    if (e.target.id === 'deleteModal') {
        closeDeleteModal();
    }
    if (e.target.id === 'photoModal') {
        closePhotoModal();
    }
});

// Close on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
        closePhotoModal();
    }
});

// Auto-expand first card on load
document.addEventListener('DOMContentLoaded', function() {
    const firstCard = document.querySelector('.panggilan-card');
    if (firstCard && !firstCard.classList.contains('expanded')) {
        const content = firstCard.querySelector('.panggilan-card-content');
        firstCard.classList.add('expanded');
        if (content) {
            content.style.maxHeight = 'none';
            content.style.opacity = '1';
        }
    }
});
</script>
@endsection
