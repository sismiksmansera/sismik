@extends('layouts.app-guru-bk')

@section('content')
@php
    use App\Http\Controllers\GuruBK\AnggotaEkstrakurikulerController;
    $ekstraColor = AnggotaEkstrakurikulerController::getColorForEkstra($ekstra->nama_ekstrakurikuler);
    $ekstraIcon = AnggotaEkstrakurikulerController::getIconForEkstra($ekstra->nama_ekstrakurikuler);
@endphp

<div class="main-content anggota-ekstra-page">
    {{-- Toast Notifications --}}
    @if(session('success'))
    <div id="toastNotification" class="toast-notification">
        <div class="toast-content">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button class="toast-close" onclick="hideToast()">×</button>
    </div>
    @endif
    
    @if(session('error'))
    <div id="toastNotification" class="toast-notification toast-error">
        <div class="toast-content">
            <i class="fas fa-times-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button class="toast-close" onclick="hideToast()">×</button>
    </div>
    @endif

    {{-- Header --}}
    <div class="page-header-center">
        <div class="header-icon-large" style="background: linear-gradient(135deg, {{ $ekstraColor }} 0%, {{ $ekstraColor }}dd 100%); box-shadow: 0 8px 25px {{ $ekstraColor }}40;">
            <i class="fas {{ $ekstraIcon }}"></i>
        </div>
        <h1>{{ $ekstra->nama_ekstrakurikuler }}</h1>
        <p>Daftar Anggota Ekstrakurikuler</p>
    </div>

    {{-- Action Buttons --}}
    <div class="action-buttons-center">
        <a href="{{ url()->previous() }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <button onclick="openModal()" class="btn-add-member">
            <i class="fas fa-user-plus"></i> Tambah Anggota
        </button>
    </div>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>{{ count($anggota_list) }}</h3>
                <p>Total Anggota</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $ekstra->tahun_pelajaran }}</h3>
                <p>Tahun Pelajaran</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $ekstra->semester }}</h3>
                <p>Semester</p>
            </div>
        </div>
    </div>

    {{-- Members List --}}
    <div class="members-container">
        <div class="members-header">
            <div class="members-title">
                <i class="fas fa-users" style="color: {{ $ekstraColor }};"></i>
                <h2>Daftar Anggota</h2>
            </div>
            <span class="members-count" style="background: {{ $ekstraColor }}15; color: {{ $ekstraColor }};">
                {{ count($anggota_list) }} Anggota
            </span>
        </div>

        @if(count($anggota_list) == 0)
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-users"></i>
            </div>
            <h3>Belum Ada Anggota</h3>
            <p>Klik tombol "Tambah Anggota" untuk menambahkan siswa.</p>
        </div>
        @else
        <div class="members-cards-grid">
            @foreach($anggota_list as $anggota)
            @php
                $cardId = 'member_' . $anggota->id;
                $foto_exists = $anggota->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/' . $anggota->foto);
                $gender_gradient = $anggota->jk == 'Laki-laki' ? 'linear-gradient(135deg, #3b82f6, #1d4ed8)' : 'linear-gradient(135deg, #ec4899, #db2777)';
                
                $nilai_text = '';
                if (!empty($anggota->nilai)) {
                    switch($anggota->nilai) {
                        case 'A': $nilai_text = 'A - Sangat Baik'; break;
                        case 'B': $nilai_text = 'B - Baik'; break;
                        case 'C': $nilai_text = 'C - Cukup'; break;
                        case 'D': $nilai_text = 'D - Kurang'; break;
                        default: $nilai_text = $anggota->nilai;
                    }
                }
            @endphp
            <div class="member-card" data-card-id="{{ $cardId }}">
                {{-- Card Header - Collapsible Trigger --}}
                <div class="member-card-header" onclick="toggleMemberCard('{{ $cardId }}')" style="background: {{ $ekstraColor }}10;">
                    <div class="member-header-left">
                        <div class="member-avatar-photo" style="background: {{ $gender_gradient }};">
                            @if($foto_exists)
                                <img src="{{ asset('storage/siswa/' . $anggota->foto) }}" alt="{{ $anggota->nama_siswa }}">
                            @else
                                <span class="avatar-initial">{{ strtoupper(substr($anggota->nama_siswa, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="member-name-info">
                            <h4>{{ $anggota->nama_siswa }}</h4>
                            <span class="member-rombel">{{ $anggota->rombel_aktif }}</span>
                            <span class="nilai-badge-header {{ empty($anggota->nilai) ? 'empty' : '' }}" data-anggota-id="{{ $anggota->id }}">
                                <i class="fas fa-star"></i>
                                <span class="nilai-text">{{ !empty($nilai_text) ? $nilai_text : 'Belum Dinilai' }}</span>
                            </span>
                        </div>
                    </div>
                    <i class="fas fa-chevron-down expand-icon" style="color: {{ $ekstraColor }};"></i>
                </div>

                {{-- Card Body - Expandable Content --}}
                <div class="member-card-body" id="{{ $cardId }}">
                    <div class="member-details-grid">
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-id-card" style="color: {{ $ekstraColor }};"></i> NIS</span>
                            <span class="detail-value">{{ $anggota->nis }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-fingerprint" style="color: {{ $ekstraColor }};"></i> NISN</span>
                            <span class="detail-value">{{ $anggota->nisn ?? '-' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-chalkboard" style="color: {{ $ekstraColor }};"></i> Rombel</span>
                            <span class="detail-value">{{ $anggota->rombel_aktif ?? '-' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-venus-mars" style="color: {{ $ekstraColor }};"></i> JK</span>
                            <span class="detail-value">
                                @if($anggota->jk == 'Laki-laki')
                                    <span class="badge-jk badge-laki">Laki-laki</span>
                                @else
                                    <span class="badge-jk badge-perempuan">Perempuan</span>
                                @endif
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-calendar-plus" style="color: {{ $ekstraColor }};"></i> Bergabung</span>
                            <span class="detail-value">{{ \Carbon\Carbon::parse($anggota->tanggal_bergabung)->format('d/m/Y') }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-circle" style="color: {{ $ekstraColor }};"></i> Status</span>
                            <span class="detail-value">
                                <span class="badge-status badge-aktif">{{ $anggota->status ?? 'Aktif' }}</span>
                            </span>
                        </div>
                    </div>

                    {{-- Nilai & Actions --}}
                    <div class="member-actions-row">
                        <div class="nilai-input-group">
                            <label><i class="fas fa-star"></i> Nilai Ekstra</label>
                            <select class="nilai-select-card" data-anggota-id="{{ $anggota->id }}">
                                <option value="" {{ empty($anggota->nilai) ? 'selected' : '' }}>Pilih Nilai</option>
                                <option value="A" {{ ($anggota->nilai ?? '') == 'A' ? 'selected' : '' }}>A - Sangat Baik</option>
                                <option value="B" {{ ($anggota->nilai ?? '') == 'B' ? 'selected' : '' }}>B - Baik</option>
                                <option value="C" {{ ($anggota->nilai ?? '') == 'C' ? 'selected' : '' }}>C - Cukup</option>
                                <option value="D" {{ ($anggota->nilai ?? '') == 'D' ? 'selected' : '' }}>D - Kurang</option>
                            </select>
                        </div>
                        <div class="action-buttons-card">
                            <button type="button" class="btn-save-single" onclick="saveSingleNilai({{ $anggota->id }})">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <form method="POST" action="{{ route('guru_bk.anggota-ekstra.hapus', $ekstra->id) }}" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus anggota ini?')">
                                @csrf
                                <input type="hidden" name="anggota_id" value="{{ $anggota->id }}">
                                <button type="submit" class="btn-delete">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- Modal Tambah Anggota --}}
<div id="modalTambah" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> Tambah Anggota</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form method="POST" action="{{ route('guru_bk.anggota-ekstra.tambah', $ekstra->id) }}" id="formTambah">
            @csrf
            <div class="modal-body">
                <div class="search-box">
                    <input type="text" class="search-input" id="searchSiswa" placeholder="Cari nama siswa..." onkeyup="filterSiswa()">
                    <i class="fas fa-search search-icon"></i>
                </div>

                <div class="selected-preview" id="selectedPreview" style="display: none;">
                    <strong>Dipilih:</strong> <span id="selectedCount">0</span> siswa
                </div>

                <div class="siswa-list" id="siswaList">
                    @if(count($siswa_available) == 0)
                        <div class="siswa-empty">
                            <i class="fas fa-users"></i>
                            <p>Semua siswa sudah terdaftar sebagai anggota.</p>
                        </div>
                    @else
                        @foreach($siswa_available as $siswa)
                        <div class="siswa-item" data-nama="{{ strtolower($siswa->nama) }}" onclick="toggleSiswa(this, {{ $siswa->id }})">
                            <div class="siswa-item-content">
                                <input type="checkbox" name="siswa_ids[]" value="{{ $siswa->id }}" class="checkbox-siswa" onclick="event.stopPropagation()">
                                <div class="siswa-item-info">
                                    <div class="siswa-nama">{{ $siswa->nama }}</div>
                                    <div class="siswa-meta">{{ $siswa->rombel_aktif }} | {{ $siswa->nisn }}</div>
                                </div>
                                <span class="badge-jk-mini {{ $siswa->jk == 'Laki-laki' ? 'laki' : 'perempuan' }}">
                                    {{ $siswa->jk == 'Laki-laki' ? 'L' : 'P' }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<style>
:root {
    --primary: #10b981;
    --primary-dark: #059669;
    --danger: #ef4444;
    --warning: #f59e0b;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-500: #6b7280;
    --ekstra-color: {{ $ekstraColor }};
}

.anggota-ekstra-page {
    padding: 30px;
    background: #f8f9fa;
    min-height: calc(100vh - 60px);
}

/* Toast Notification */
.toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 300px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 16px 20px;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(16, 185, 129, 0.4);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    z-index: 9999;
    animation: slideIn 0.3s ease;
}

.toast-notification.toast-error {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    box-shadow: 0 10px 40px rgba(239, 68, 68, 0.4);
}

@keyframes slideIn {
    from { transform: translateX(120%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

.toast-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.toast-content i {
    font-size: 20px;
}

.toast-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

/* Header */
.page-header-center {
    text-align: center;
    margin-bottom: 25px;
}

.header-icon-large {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    color: white;
    margin: 0 auto 20px;
}

.page-header-center h1 {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 5px 0;
    color: #1f2937;
    text-transform: uppercase;
}

.page-header-center p {
    color: #6b7280;
    margin: 0;
}

/* Action Buttons */
.action-buttons-center {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 25px;
    flex-wrap: wrap;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: white;
    color: #374151;
    border: 2px solid #d1d5db;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-back:hover {
    border-color: var(--primary);
    color: var(--primary);
}

.btn-add-member {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35);
    transition: all 0.3s ease;
}

.btn-add-member:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 25px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 15px;
    border: 1px solid #e5e7eb;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
}

.stat-icon.primary { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.stat-icon.success { background: linear-gradient(135deg, #10b981, #059669); }
.stat-icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }

.stat-info h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: #1f2937;
}

.stat-info p {
    margin: 4px 0 0 0;
    color: #6b7280;
    font-size: 12px;
}

/* Members Container */
.members-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    overflow: hidden;
}

.members-header {
    padding: 20px 25px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.members-title {
    display: flex;
    align-items: center;
    gap: 10px;
}

.members-title h2 {
    margin: 0;
    font-size: 1.1rem;
    color: #1f2937;
}

.members-count {
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

/* Empty State */
.empty-state {
    padding: 60px 30px;
    text-align: center;
}

.empty-icon {
    width: 80px;
    height: 80px;
    background: #f3f4f6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.empty-icon i {
    font-size: 30px;
    color: #9ca3af;
}

.empty-state h3 {
    margin: 0 0 10px 0;
    color: #1f2937;
}

.empty-state p {
    margin: 0;
    color: #6b7280;
}

/* Members Cards Grid */
.members-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 15px;
    padding: 20px;
}

/* Member Card */
.member-card {
    background: white;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.member-card:hover {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-color: var(--ekstra-color);
}

.member-card.expanded {
    border-color: var(--ekstra-color);
}

.member-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.member-header-left {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
    min-width: 0;
}

.member-avatar-photo {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.member-avatar-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.member-avatar-photo .avatar-initial {
    color: white;
    font-weight: 700;
    font-size: 16px;
}

.member-name-info {
    flex: 1;
    min-width: 0;
}

.member-name-info h4 {
    margin: 0 0 3px 0;
    font-size: 15px;
    font-weight: 600;
    color: #1f2937;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.member-name-info .member-rombel {
    font-size: 12px;
    color: #6b7280;
    display: block;
}

.expand-icon {
    transition: transform 0.3s ease;
}

.member-card.expanded .expand-icon {
    transform: rotate(180deg);
}

/* Card Body - Collapsible */
.member-card-body {
    max-height: 0;
    overflow: hidden;
    opacity: 0;
    transition: max-height 0.4s ease, opacity 0.3s ease, padding 0.3s ease;
    padding: 0 15px;
    background: #fafafa;
}

.member-card.expanded .member-card-body {
    max-height: 500px;
    opacity: 1;
    padding: 15px;
}

/* Details Grid */
.member-details-grid {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 15px;
}

.detail-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #e5e7eb;
}

.detail-label {
    font-size: 11px;
    color: #6b7280;
    font-weight: 600;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 5px;
}

.detail-label i {
    font-size: 10px;
}

.detail-value {
    font-size: 14px;
    color: #1f2937;
    font-weight: 500;
}

/* Badges */
.badge-jk {
    padding: 3px 8px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 600;
}

.badge-laki { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.badge-perempuan { background: rgba(236, 72, 153, 0.1); color: #ec4899; }

.badge-status {
    padding: 3px 8px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 600;
}

.badge-aktif { background: rgba(16, 185, 129, 0.1); color: #10b981; }

.nilai-badge-header {
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: #1f2937;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    box-shadow: 0 2px 6px rgba(245, 158, 11, 0.3);
    margin-left: 8px;
}

.nilai-badge-header.empty {
    background: rgba(156, 163, 175, 0.2);
    color: #6b7280;
    box-shadow: none;
}

/* Actions Row */
.member-actions-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding-top: 15px;
    border-top: 1px solid #e5e7eb;
    flex-wrap: wrap;
}

.nilai-input-group {
    flex: 1;
    min-width: 150px;
}

.nilai-input-group label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
}

.nilai-input-group label i {
    color: #f59e0b;
}

.nilai-select-card {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    background: white;
    transition: all 0.2s ease;
}

.nilai-select-card:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    outline: none;
}

.action-buttons-card {
    display: flex;
    gap: 8px;
}

.btn-save-single {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-save-single:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 10px rgba(16, 185, 129, 0.3);
}

.btn-delete {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
    border: none;
    padding: 10px 15px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-delete:hover {
    background: #ef4444;
    color: white;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    border-radius: 16px;
    width: 90%;
    max-width: 800px;
    max-height: 85vh;
    overflow: hidden;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid var(--gray-200);
    background: var(--gray-100);
}

.modal-header h3 {
    margin: 0;
    color: #1f2937;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: var(--gray-500);
}

.modal-body {
    padding: 20px;
    max-height: 60vh;
    overflow-y: auto;
}

.modal-actions {
    padding: 20px;
    border-top: 1px solid var(--gray-200);
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

/* Search */
.search-box {
    position: relative;
    margin-bottom: 15px;
}

.search-input {
    width: 100%;
    padding: 12px 45px 12px 15px;
    border: 1px solid var(--gray-200);
    border-radius: 10px;
    font-size: 14px;
    box-sizing: border-box;
}

.search-icon {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-500);
}

.selected-preview {
    background: var(--gray-100);
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 15px;
}

/* Siswa List */
.siswa-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 15px;
    max-height: 400px;
    overflow-y: auto;
}

.siswa-item {
    background: var(--gray-100);
    border-radius: 10px;
    padding: 15px;
    border: 1px solid var(--gray-200);
    transition: all 0.2s ease;
    cursor: pointer;
}

.siswa-item:hover {
    background: rgba(16, 185, 129, 0.05);
    border-color: var(--primary);
}

.siswa-item.selected {
    background: rgba(16, 185, 129, 0.1);
    border-color: var(--primary);
}

.siswa-item-content {
    display: flex;
    align-items: center;
    gap: 10px;
}

.siswa-item-info {
    flex: 1;
}

.siswa-nama {
    font-weight: 600;
    font-size: 14px;
    color: #1f2937;
}

.siswa-meta {
    font-size: 12px;
    color: #6b7280;
}

.badge-jk-mini {
    padding: 3px 8px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 600;
}

.badge-jk-mini.laki {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}

.badge-jk-mini.perempuan {
    background: rgba(236, 72, 153, 0.1);
    color: #ec4899;
}

.siswa-empty {
    text-align: center;
    padding: 40px;
    color: #6b7280;
    grid-column: 1/-1;
}

.siswa-empty i {
    font-size: 40px;
    margin-bottom: 15px;
    opacity: 0.5;
}

.checkbox-siswa {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: var(--primary);
}

/* Buttons */
.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.2s ease;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
}

.btn-secondary {
    background: var(--gray-500);
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .anggota-ekstra-page {
        padding: 12px;
    }

    .header-icon-large {
        width: 60px;
        height: 60px;
        font-size: 28px;
    }

    .page-header-center h1 {
        font-size: 20px;
    }

    .action-buttons-center {
        flex-direction: column;
    }

    .btn-back, .btn-add-member {
        width: 100%;
        justify-content: center;
    }

    .stats-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
    }

    .stat-card {
        flex-direction: column;
        text-align: center;
        padding: 12px 8px;
        gap: 8px;
    }

    .stat-icon {
        width: 35px;
        height: 35px;
        font-size: 14px;
    }

    .stat-info h3 {
        font-size: 16px;
    }

    .stat-info p {
        font-size: 10px;
    }

    .members-header {
        padding: 14px 16px;
        flex-wrap: wrap;
        gap: 8px;
    }

    .members-cards-grid {
        grid-template-columns: 1fr;
        padding: 12px;
        gap: 12px;
    }

    .member-card-header {
        padding: 12px;
    }

    .member-avatar-photo {
        width: 38px;
        height: 38px;
    }

    .member-name-info h4 {
        font-size: 14px;
    }

    .member-name-info .member-rombel {
        font-size: 11px;
    }

    .nilai-badge-header {
        font-size: 10px;
        padding: 2px 8px;
    }

    .member-card.expanded .member-card-body {
        padding: 12px;
    }

    .member-actions-row {
        flex-direction: column;
        align-items: stretch;
    }

    .action-buttons-card {
        justify-content: flex-end;
    }

    .siswa-list {
        grid-template-columns: 1fr;
    }

    .modal-content {
        width: 95%;
        max-height: 90vh;
    }
}

@media (max-width: 480px) {
    .anggota-ekstra-page {
        padding: 8px;
    }

    .header-icon-large {
        width: 48px;
        height: 48px;
        font-size: 22px;
        border-radius: 14px;
    }

    .page-header-center h1 {
        font-size: 18px;
    }

    .stat-card {
        padding: 8px 4px;
    }

    .stat-icon {
        width: 28px;
        height: 28px;
        font-size: 11px;
        border-radius: 7px;
    }

    .stat-info h3 {
        font-size: 14px;
    }

    .stat-info p {
        font-size: 8px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .members-cards-grid {
        padding: 8px;
        gap: 10px;
    }

    .btn-back, .btn-add-member {
        padding: 10px 18px;
        font-size: 13px;
    }
}
</style>

<script>
const EKSTRA_ID = {{ $ekstra->id }};

// Toast
function hideToast() {
    const toast = document.getElementById('toastNotification');
    if (toast) {
        toast.style.transform = 'translateX(120%)';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }
}

// Auto-hide toast after 5 seconds
setTimeout(() => hideToast(), 5000);

// Modal
function openModal() {
    document.getElementById('modalTambah').style.display = 'flex';
}

function closeModal() {
    document.getElementById('modalTambah').style.display = 'none';
}

// Toggle siswa selection
function toggleSiswa(element, id) {
    const checkbox = element.querySelector('input[type="checkbox"]');
    checkbox.checked = !checkbox.checked;
    element.classList.toggle('selected', checkbox.checked);
    updateSelectedCount();
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.checkbox-siswa:checked').length;
    document.getElementById('selectedCount').textContent = checked;
    document.getElementById('selectedPreview').style.display = checked > 0 ? 'block' : 'none';
}

// Filter siswa
function filterSiswa() {
    const query = document.getElementById('searchSiswa').value.toLowerCase();
    const items = document.querySelectorAll('.siswa-item');
    items.forEach(item => {
        const nama = item.getAttribute('data-nama');
        item.style.display = nama.includes(query) ? 'block' : 'none';
    });
}

// Toggle member card
function toggleMemberCard(cardId) {
    const card = document.querySelector('[data-card-id="' + cardId + '"]');
    card.classList.toggle('expanded');
}

// Dynamic toast
function showToast(message, type = 'success') {
    const existingToast = document.getElementById('dynamicToast');
    if (existingToast) existingToast.remove();
    
    const toast = document.createElement('div');
    toast.id = 'dynamicToast';
    toast.className = 'toast-notification' + (type === 'error' ? ' toast-error' : '');
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas ${type === 'error' ? 'fa-times-circle' : 'fa-check-circle'}"></i>
            <span>${message}</span>
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()">×</button>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.transform = 'translateX(120%)';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

// Save single nilai via AJAX
function saveSingleNilai(anggotaId) {
    const select = document.querySelector(`select.nilai-select-card[data-anggota-id="${anggotaId}"]`);
    const nilai = select.value;
    const btn = event.target.closest('.btn-save-single');
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    
    fetch('{{ route("guru_bk.anggota-ekstra.update-nilai", $ekstra->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            anggota_id: anggotaId,
            nilai: nilai
        })
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
        
        if (data.success) {
            showToast(data.message, 'success');
            
            // Update badge header
            const badge = document.querySelector(`.nilai-badge-header[data-anggota-id="${anggotaId}"]`);
            if (badge) {
                let nilaiText = 'Belum Dinilai';
                if (nilai) {
                    switch(nilai) {
                        case 'A': nilaiText = 'A - Sangat Baik'; break;
                        case 'B': nilaiText = 'B - Baik'; break;
                        case 'C': nilaiText = 'C - Cukup'; break;
                        case 'D': nilaiText = 'D - Kurang'; break;
                    }
                    badge.classList.remove('empty');
                } else {
                    badge.classList.add('empty');
                }
                badge.querySelector('.nilai-text').textContent = nilaiText;
            }
        } else {
            showToast(data.message || 'Gagal menyimpan nilai!', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
        showToast('Terjadi kesalahan koneksi!', 'error');
    });
}

// Close modal when clicking outside
document.getElementById('modalTambah').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

// Update count on checkbox change
document.querySelectorAll('.checkbox-siswa').forEach(cb => {
    cb.addEventListener('change', updateSelectedCount);
});
</script>
@endsection
