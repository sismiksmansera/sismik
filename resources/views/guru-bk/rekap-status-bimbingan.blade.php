@extends('layouts.app-guru-bk')

@section('content')
<div class="main-content rekap-status-page">
    {{-- HEADER SECTION --}}
    <div class="bk-page-header">
        <div class="header-content-wrapper">
            <div class="header-icon-box status-{{ $status_bg }}">
                <i class="fas {{ $status_icon }}"></i>
            </div>
            <div class="header-info">
                <div class="header-greeting">
                    <span class="greeting-text">Rekap Status Bimbingan</span>
                    <h1>{{ $status }}</h1>
                </div>
                <div class="header-details">
                    @if(!empty($tahun))
                    <span class="detail-badge"><i class="fas fa-calendar-alt"></i> {{ $tahun }}</span>
                    @endif
                    @if(!empty($semester))
                    <span class="detail-badge"><i class="fas fa-clock"></i> {{ $semester }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="header-actions-box">
            <button type="button" onclick="window.print()" class="btn-action-header btn-primary-header">
                <i class="fas fa-print"></i> <span class="btn-text">Cetak</span>
            </button>
            <a href="{{ route('guru_bk.siswa-bimbingan') }}" class="btn-action-header btn-secondary-header">
                <i class="fas fa-arrow-left"></i> <span class="btn-text">Kembali</span>
            </a>
        </div>
    </div>

    {{-- MOBILE ACTION BUTTONS --}}
    <div class="mobile-actions-wrapper">
        <button type="button" onclick="window.print()" class="btn-mobile-action btn-mobile-primary">
            <i class="fas fa-print"></i> Cetak
        </button>
        <a href="{{ route('guru_bk.siswa-bimbingan') }}" class="btn-mobile-action btn-mobile-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($total_data > 0)
        @if($status === 'Belum Ada Catatan')
        {{-- TABLE VIEW FOR STUDENTS WITHOUT NOTES --}}
        <div class="rekap-table-container">
            <div class="table-responsive">
                <table class="rekap-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Rombel</th>
                            <th>JK</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data_rekap as $index => $item)
                        @php
                            $siswa = $item['data'];
                            $foto_exists = $siswa->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/' . $siswa->foto);
                            $initials = collect(explode(' ', $siswa->nama))
                                ->map(fn($p) => strtoupper(substr($p, 0, 1)))
                                ->take(2)
                                ->join('');
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">
                                <div class="student-avatar-rekap">
                                    @if($foto_exists)
                                        <img src="{{ asset('storage/siswa/' . $siswa->foto) }}" alt="{{ $siswa->nama }}" class="student-avatar-img-rekap">
                                    @else
                                        <div class="student-avatar-initial-rekap {{ $siswa->jk == 'L' ? 'male' : 'female' }}">
                                            {{ $initials ?: 'S' }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $siswa->nis }}</td>
                            <td>
                                <div class="siswa-name">
                                    <strong>{{ $siswa->nama }}</strong>
                                    <small class="nisn">NISN: {{ $siswa->nisn }}</small>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="kelas-badge">Kelas {{ $item['kelas'] }}</span>
                            </td>
                            <td>{{ $item['rombel_aktif'] }}</td>
                            <td class="text-center">{{ $siswa->jk == 'L' ? 'L' : 'P' }}</td>
                            <td class="action-buttons">
                                <a href="{{ route('guru_bk.catatan-bimbingan.create', ['nisn' => $siswa->nisn, 'tahun' => $tahun, 'semester' => $semester]) }}" 
                                   class="btn-action-small success" title="Tambah Catatan">
                                    <i class="fas fa-plus"></i> Tambah
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @else
        {{-- COLLAPSIBLE CARDS VIEW FOR CATATAN --}}
        <div class="catatan-rekap-container">
            @foreach($data_rekap as $index => $item)
            @php
                $siswa = $item['siswa'];
                $catatan_list = $item['catatan'];
                $siswa_id = 'siswa_' . $siswa['nisn'];
                $catatan_count = count($catatan_list);
                $foto_exists = !empty($siswa['foto']) && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/' . $siswa['foto']);
                $initials = collect(explode(' ', $siswa['nama']))
                    ->map(fn($p) => strtoupper(substr($p, 0, 1)))
                    ->take(2)
                    ->join('');
            @endphp
            
            <div class="siswa-rekap-card" data-siswa="{{ $siswa_id }}">
                <div class="siswa-rekap-header" onclick="toggleSiswaRekap('{{ $siswa_id }}')">
                    <div class="siswa-rekap-info">
                        <div class="student-avatar-rekap">
                            @if($foto_exists)
                                <img src="{{ asset('storage/siswa/' . $siswa['foto']) }}" alt="{{ $siswa['nama'] }}" class="student-avatar-img-rekap">
                            @else
                                <div class="student-avatar-initial-rekap {{ $siswa['jk'] == 'L' ? 'male' : 'female' }}">
                                    {{ $initials ?: 'S' }}
                                </div>
                            @endif
                        </div>
                        <div class="siswa-rekap-details">
                            <div class="siswa-rekap-name-row">
                                <h3>{{ $siswa['nama'] }}</h3>
                                <span class="siswa-catatan-count">
                                    <i class="fas fa-clipboard-list"></i> {{ $catatan_count }} catatan
                                </span>
                            </div>
                            <div class="siswa-rekap-meta">
                                <span class="siswa-rombel">
                                    <i class="fas fa-chalkboard-teacher"></i> {{ $item['rombel_aktif'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="siswa-rekap-right">
                        <div class="siswa-rekap-status">
                            <span class="status-badge-rekap {{ strtolower(str_replace(' ', '-', $status)) }}">
                                <i class="fas {{ $status_icon }}"></i> {{ $status }}
                            </span>
                        </div>
                        <div class="siswa-rekap-toggle">
                            <i class="fas fa-chevron-down toggle-icon"></i>
                        </div>
                    </div>
                </div>

                <div class="catatan-rekap-content">
                    @foreach($catatan_list as $catatan)
                    @php
                        $catatan_id = 'catatan_' . $catatan['id'];
                        $jenis_icons = [
                            'pribadi' => 'fa-user',
                            'sosial' => 'fa-users',
                            'belajar' => 'fa-book',
                            'karir' => 'fa-briefcase',
                        ];
                        $jenis_icon = $jenis_icons[strtolower($catatan['jenis_bimbingan'])] ?? 'fa-clipboard';
                    @endphp
                    
                    <div class="catatan-rekap-item" data-catatan="{{ $catatan_id }}">
                        <div class="catatan-top-bar {{ $status_bg }}"></div>
                        
                        <div class="catatan-rekap-item-header">
                            <div class="catatan-rekap-item-info">
                                <div class="catatan-rekap-item-icon {{ $status_bg }}">
                                    <i class="fas {{ $jenis_icon }}"></i>
                                </div>
                                <div class="catatan-rekap-item-details">
                                    <h4>{{ $catatan['jenis_bimbingan'] }}</h4>
                                    <div class="catatan-rekap-item-meta">
                                        <span class="catatan-date">
                                            <i class="fas fa-calendar"></i>
                                            {{ \Carbon\Carbon::parse($catatan['tanggal'])->format('d M Y') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="catatan-rekap-item-actions">
                                <a href="{{ route('guru_bk.catatan-bimbingan.edit', $catatan['id']) }}" class="btn-action-mini edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('guru_bk.catatan-bimbingan.print', $catatan['id']) }}" class="btn-action-mini print" title="Cetak" target="_blank">
                                    <i class="fas fa-print"></i>
                                </a>
                                <button type="button" class="btn-action-mini delete" title="Hapus" onclick="confirmDelete({{ $catatan['id'] }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="catatan-rekap-item-body">
                            <div class="catatan-section">
                                <label><i class="fas fa-exclamation-triangle"></i> Masalah</label>
                                <p>{{ $catatan['masalah'] ?: '-' }}</p>
                            </div>
                            <div class="catatan-section">
                                <label><i class="fas fa-lightbulb"></i> Penyelesaian</label>
                                <p>{{ $catatan['penyelesaian'] ?: '-' }}</p>
                            </div>
                            @if(!empty($catatan['tindak_lanjut']))
                            <div class="catatan-section">
                                <label><i class="fas fa-forward"></i> Tindak Lanjut</label>
                                <p>{{ $catatan['tindak_lanjut'] }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif
    @else
    {{-- EMPTY STATE --}}
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas {{ $status_icon }}"></i>
        </div>
        <h3>Tidak Ada Data</h3>
        <p>Tidak ada siswa dengan status "{{ $status }}" pada periode ini.</p>
        <a href="{{ route('guru_bk.siswa-bimbingan') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    @endif
</div>

{{-- DELETE MODAL --}}
<div id="deleteModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle text-danger"></i> Konfirmasi Hapus</h3>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus catatan bimbingan ini?</p>
            <p class="text-muted">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Batal</button>
            <button type="button" class="btn btn-danger" onclick="executeDelete()">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </div>
    </div>
</div>
</div>

<style>
/* Main Content */
.main-content.rekap-status-page {
    padding: 25px;
    background-color: #f8f9fa;
    min-height: calc(100vh - 60px);
}

/* ============== HEADER STYLES ============== */
.rekap-status-page .bk-page-header {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    border-radius: 16px;
    padding: 25px 30px;
    margin-bottom: 20px;
    box-shadow: 0 10px 30px rgba(124, 58, 237, 0.2);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.rekap-status-page .header-content-wrapper {
    display: flex;
    align-items: center;
    gap: 20px;
}

.rekap-status-page .header-icon-box {
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
}

.rekap-status-page .header-icon-box.status-danger { background: #ef4444; }
.rekap-status-page .header-icon-box.status-warning { background: #f59e0b; }
.rekap-status-page .header-icon-box.status-success { background: #10b981; }
.rekap-status-page .header-icon-box.status-secondary { background: #6b7280; }

.rekap-status-page .header-info { flex: 1; }

.rekap-status-page .header-greeting .greeting-text {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.8);
    font-weight: 500;
    display: block;
    margin-bottom: 4px;
}

.rekap-status-page .header-greeting h1 {
    font-size: 24px;
    font-weight: 700;
    color: white;
    margin: 0;
}

.rekap-status-page .header-details {
    display: flex;
    gap: 15px;
    margin-top: 10px;
    flex-wrap: wrap;
}

.rekap-status-page .detail-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.15);
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    color: rgba(255, 255, 255, 0.9);
}

.rekap-status-page .header-actions-box {
    display: flex;
    gap: 10px;
}

.rekap-status-page .btn-action-header {
    padding: 12px 20px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.rekap-status-page .btn-primary-header {
    background: white;
    color: #7c3aed;
}

.rekap-status-page .btn-secondary-header {
    background: rgba(255, 255, 255, 0.15);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

/* Mobile Actions */
.rekap-status-page .mobile-actions-wrapper {
    display: none;
}

.rekap-status-page .btn-mobile-action {
    flex: 1;
    padding: 10px 15px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.rekap-status-page .btn-mobile-primary {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    color: white;
}

.rekap-status-page .btn-mobile-secondary {
    background: #f3f4f6;
    color: #374151;
}

/* ============== TABLE STYLES ============== */
.rekap-table-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    overflow: hidden;
}

.table-responsive {
    overflow-x: auto;
}

.rekap-table {
    width: 100%;
    border-collapse: collapse;
}

.rekap-table thead th {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    color: white;
    padding: 15px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    white-space: nowrap;
}

.rekap-table tbody td {
    padding: 12px 15px;
    border-bottom: 1px solid #f3f4f6;
    font-size: 13px;
}

.rekap-table tbody tr:hover {
    background: #f9fafb;
}

.student-avatar-rekap {
    width: 40px;
    height: 40px;
    cursor: pointer;
}

.student-avatar-img-rekap {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.student-avatar-initial-rekap {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
    color: white;
}

.student-avatar-initial-rekap.male { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.student-avatar-initial-rekap.female { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); }

.siswa-name { display: flex; flex-direction: column; gap: 2px; }
.siswa-name strong { color: #1f2937; }
.siswa-name .nisn { color: #9ca3af; font-size: 11px; }

.kelas-badge {
    background: #e0e7ff;
    color: #4f46e5;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

.btn-action-small {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-action-small.success {
    background: #10b981;
    color: white;
}

.btn-action-small.success:hover {
    background: #059669;
}

/* ============== COLLAPSIBLE CARDS ============== */
.catatan-rekap-container {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.siswa-rekap-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    overflow: hidden;
}

.siswa-rekap-header {
    padding: 20px;
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.siswa-rekap-header:hover {
    background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
}

.siswa-rekap-info {
    display: flex;
    align-items: center;
    gap: 15px;
    flex: 1;
}

.siswa-rekap-card .student-avatar-rekap {
    width: 50px;
    height: 50px;
}

.siswa-rekap-details { flex: 1; }

.siswa-rekap-name-row {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.siswa-rekap-name-row h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.siswa-catatan-count {
    background: rgba(255,255,255,0.2);
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
}

.siswa-rekap-meta {
    display: flex;
    gap: 15px;
    margin-top: 6px;
    font-size: 12px;
    opacity: 0.9;
    flex-wrap: wrap;
}

.siswa-rekap-meta span {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.siswa-rekap-right {
    display: flex;
    align-items: center;
    gap: 15px;
}

.status-badge-rekap {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.status-badge-rekap.belum-ditangani { background: #fee2e2; color: #dc2626; }
.status-badge-rekap.dalam-proses { background: #fef3c7; color: #d97706; }
.status-badge-rekap.selesai { background: #d1fae5; color: #059669; }

.siswa-rekap-toggle {
    width: 32px;
    height: 32px;
    background: rgba(255,255,255,0.2);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.siswa-rekap-card.expanded .toggle-icon {
    transform: rotate(180deg);
}

.toggle-icon { transition: transform 0.3s ease; }

.catatan-rekap-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.5s ease;
    padding: 0 20px;
}

.siswa-rekap-card.expanded .catatan-rekap-content {
    max-height: none;
    padding: 20px;
}

/* Catatan Item */
.catatan-rekap-item {
    background: #f9fafb;
    border-radius: 12px;
    margin-bottom: 12px;
    overflow: hidden;
}

.catatan-top-bar {
    height: 4px;
}

.catatan-top-bar.danger { background: #ef4444; }
.catatan-top-bar.warning { background: #f59e0b; }
.catatan-top-bar.success { background: #10b981; }

.catatan-rekap-item-header {
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e5e7eb;
}

.catatan-rekap-item-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.catatan-rekap-item-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.catatan-rekap-item-icon.danger { background: #ef4444; }
.catatan-rekap-item-icon.warning { background: #f59e0b; }
.catatan-rekap-item-icon.success { background: #10b981; }

.catatan-rekap-item-details h4 {
    margin: 0 0 4px 0;
    font-size: 14px;
    font-weight: 600;
    color: #1f2937;
}

.catatan-rekap-item-meta {
    font-size: 12px;
    color: #6b7280;
}

.catatan-rekap-item-actions {
    display: flex;
    gap: 6px;
}

.btn-action-mini {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-action-mini.edit { background: #e0e7ff; color: #4f46e5; }
.btn-action-mini.print { background: #d1fae5; color: #059669; }
.btn-action-mini.delete { background: #fee2e2; color: #dc2626; }

.btn-action-mini:hover { transform: scale(1.1); }

.catatan-rekap-item-body {
    padding: 15px;
}

.catatan-section {
    margin-bottom: 12px;
}

.catatan-section:last-child { margin-bottom: 0; }

.catatan-section label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 4px;
}

.catatan-section p {
    margin: 0;
    font-size: 13px;
    color: #374151;
    line-height: 1.5;
}

/* Empty State */
.empty-state {
    background: white;
    border-radius: 16px;
    padding: 60px 30px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.empty-state .empty-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.empty-state .empty-icon i { font-size: 32px; color: white; }
.empty-state h3 { margin: 0 0 10px; color: #1f2937; font-weight: 600; }
.empty-state p { margin: 0 0 20px; color: #6b7280; }

/* Modal */
.modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(4px);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    border-radius: 16px;
    width: 90%;
    max-width: 400px;
    overflow: hidden;
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.modal-body { padding: 20px; }
.modal-footer {
    padding: 15px 20px;
    background: #f9fafb;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

/* Alerts */
.alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 15px;
    font-size: 14px;
}

.alert-success { background: #d1fae5; color: #059669; }
.alert-danger { background: #fee2e2; color: #dc2626; }

/* ============== MOBILE RESPONSIVE ============== */
@media (max-width: 768px) {
    .main-content.rekap-status-page { padding: 15px; }
    
    .rekap-status-page .bk-page-header {
        padding: 15px;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .rekap-status-page .header-content-wrapper {
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }
    
    .rekap-status-page .header-icon-box {
        width: 60px;
        height: 60px;
        font-size: 24px;
        border-radius: 50%;
    }
    
    .rekap-status-page .header-info { text-align: center; }
    .rekap-status-page .header-greeting h1 { font-size: 16px; }
    .rekap-status-page .header-details { justify-content: center; gap: 6px; }
    .rekap-status-page .detail-badge { font-size: 10px; padding: 4px 8px; }
    .rekap-status-page .header-actions-box { display: none; }
    
    .rekap-status-page .mobile-actions-wrapper {
        display: flex;
        gap: 8px;
        margin-bottom: 12px;
    }
    
    .rekap-table thead th,
    .rekap-table tbody td {
        padding: 10px;
        font-size: 11px;
    }
    
    .siswa-rekap-header { padding: 15px; flex-wrap: wrap; }
    .siswa-rekap-info { gap: 10px; }
    .siswa-rekap-card .student-avatar-rekap { width: 40px; height: 40px; }
    .siswa-rekap-name-row h3 { font-size: 14px; }
    .siswa-rekap-meta { gap: 8px; font-size: 10px; }
    .siswa-rekap-right { flex-wrap: wrap; gap: 8px; }
    .status-badge-rekap { font-size: 10px; padding: 4px 8px; }
    
    .catatan-rekap-item-header { flex-direction: column; gap: 10px; align-items: flex-start; }
    .catatan-rekap-item-actions { width: 100%; justify-content: flex-end; }
}

/* Print Styles */
@media print {
    .header-actions-box, .mobile-actions-wrapper, 
    .catatan-rekap-item-actions, .btn-action-small,
    .action-buttons, #deleteModal { display: none !important; }
    
    .siswa-rekap-card.expanded .catatan-rekap-content,
    .catatan-rekap-content { max-height: none !important; padding: 15px !important; }
    
    .bk-page-header { background: #4f46e5 !important; -webkit-print-color-adjust: exact; }
}
</style>

<script>
let deleteId = null;

function toggleSiswaRekap(siswaId) {
    const card = document.querySelector(`[data-siswa="${siswaId}"]`);
    if (card) {
        card.classList.toggle('expanded');
    }
}

function confirmDelete(id) {
    deleteId = id;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    deleteId = null;
    document.getElementById('deleteModal').style.display = 'none';
}

function executeDelete() {
    if (!deleteId) return;
    
    fetch(`{{ url('/guru-bk/rekap-catatan') }}/${deleteId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Gagal menghapus catatan.');
        }
        closeDeleteModal();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan.');
        closeDeleteModal();
    });
}

// Expand all on desktop
document.addEventListener('DOMContentLoaded', function() {
    if (window.innerWidth > 768) {
        document.querySelectorAll('.siswa-rekap-card').forEach(card => {
            card.classList.add('expanded');
        });
    }
});
</script>
@endsection
