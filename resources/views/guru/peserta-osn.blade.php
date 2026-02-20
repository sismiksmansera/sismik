@extends('layouts.app')

@section('title', 'Peserta ' . $ajang->nama_ajang . ' | SISMIK')

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-guru')

<div class="main-content peserta-ajang-page">

    {{-- Header --}}
    <div class="page-header-center">
        <div class="header-icon-large">
            <i class="fas fa-trophy"></i>
        </div>
        <h1>{{ $ajang->nama_ajang }}</h1>
        <p>Daftar Peserta Ajang Talenta</p>
        @if($ajang->jenis_ajang)
        <span class="header-badge">{{ $ajang->jenis_ajang }}</span>
        @endif
    </div>

    {{-- Action Buttons --}}
    <div class="action-buttons-center">
        <a href="{{ route('guru.koordinator-osn.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon primary"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <h3>{{ count($pesertaList) }}</h3>
                <p>Total Peserta</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success"><i class="fas fa-calendar-alt"></i></div>
            <div class="stat-info">
                <h3>{{ $ajang->tahun ?? '-' }}</h3>
                <p>Tahun</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon warning"><i class="fas fa-user-tie"></i></div>
            <div class="stat-info">
                <h3 style="font-size: 14px;">{{ $ajang->pembina ?? '-' }}</h3>
                <p>Pembina</p>
            </div>
        </div>
    </div>

    {{-- Members List --}}
    <div class="members-container">
        <div class="members-header">
            <div class="members-title">
                <i class="fas fa-users" style="color: #7c3aed;"></i>
                <h2>Daftar Peserta</h2>
            </div>
            <span class="members-count">{{ count($pesertaList) }} Peserta</span>
        </div>

        @if(count($pesertaList) == 0)
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-users"></i></div>
            <h3>Belum Ada Peserta</h3>
            <p>Belum ada siswa yang terdaftar sebagai peserta ajang ini.</p>
        </div>
        @else
        <div class="members-cards-grid">
            @foreach($pesertaList as $peserta)
            @php
                $cardId = 'peserta_' . $peserta->id;
                $gender_gradient = ($peserta->jk == 'Laki-laki' || $peserta->jk == 'L')
                    ? 'linear-gradient(135deg, #3b82f6, #1d4ed8)'
                    : 'linear-gradient(135deg, #ec4899, #db2777)';
            @endphp
            <div class="member-card" data-card-id="{{ $cardId }}">
                <div class="member-card-header" onclick="toggleCard('{{ $cardId }}')">
                    <div class="member-header-left">
                        <div class="member-avatar" style="background: {{ $gender_gradient }};" onclick="event.stopPropagation(); openPhotoModal('{{ $peserta->foto_url ?? '' }}', '{{ strtoupper(substr($peserta->nama, 0, 1)) }}', '{{ addslashes($peserta->nama) }}', '{{ $peserta->mapel_osn_2026 ?? $ajang->nama_ajang }}')">
                            @if($peserta->foto_url)
                                <img src="{{ $peserta->foto_url }}" alt="{{ $peserta->nama }}">
                            @else
                                <span class="avatar-initial">{{ strtoupper(substr($peserta->nama, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="member-name-info">
                            <h4>{{ $peserta->nama }}</h4>
                            <span class="member-rombel">{{ $peserta->rombel_aktif }}</span>
                        </div>
                    </div>
                    <i class="fas fa-chevron-down expand-icon"></i>
                </div>

                <div class="member-card-body" id="{{ $cardId }}">
                    <div class="detail-section-title"><i class="fas fa-id-badge"></i> Identitas</div>
                    <div class="member-details-grid">
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-id-card"></i> NIS</span>
                            <span class="detail-value">{{ $peserta->nis }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-fingerprint"></i> NISN</span>
                            <span class="detail-value">{{ $peserta->nisn ?? '-' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-chalkboard"></i> Rombel</span>
                            <span class="detail-value">{{ $peserta->rombel_aktif }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-venus-mars"></i> JK</span>
                            <span class="detail-value">
                                @if($peserta->jk == 'Laki-laki' || $peserta->jk == 'L')
                                    <span class="badge-jk badge-laki">Laki-laki</span>
                                @else
                                    <span class="badge-jk badge-perempuan">Perempuan</span>
                                @endif
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-pray"></i> Agama</span>
                            <span class="detail-value">{{ $peserta->agama ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="detail-section-title"><i class="fas fa-map-marker-alt"></i> Tempat & Tanggal Lahir</div>
                    <div class="member-details-grid">
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-city"></i> Tempat Lahir</span>
                            <span class="detail-value">{{ $peserta->tempat_lahir ?? '-' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-calendar-alt"></i> Tanggal Lahir</span>
                            <span class="detail-value">{{ $peserta->tgl_lahir ? \Carbon\Carbon::parse($peserta->tgl_lahir)->format('d/m/Y') : '-' }}</span>
                        </div>
                    </div>

                    <div class="detail-section-title"><i class="fas fa-home"></i> Alamat</div>
                    <div class="member-details-grid">
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-globe-asia"></i> Provinsi</span>
                            <span class="detail-value">{{ $peserta->provinsi ?? '-' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-building"></i> Kota/Kab</span>
                            <span class="detail-value">{{ $peserta->kota ?? '-' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-map"></i> Kecamatan</span>
                            <span class="detail-value">{{ $peserta->kecamatan ?? '-' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-map-pin"></i> Kampung</span>
                            <span class="detail-value">{{ $peserta->kelurahan ?? '-' }}</span>
                        </div>
                        @if($peserta->dusun)
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-tree"></i> Dusun</span>
                            <span class="detail-value">{{ $peserta->dusun }}</span>
                        </div>
                        @endif
                        @if($peserta->rt_rw)
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-hashtag"></i> RT/RW</span>
                            <span class="detail-value">{{ $peserta->rt_rw }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="detail-section-title"><i class="fas fa-address-book"></i> Kontak</div>
                    <div class="member-details-grid">
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-envelope"></i> Email</span>
                            <span class="detail-value">{{ $peserta->email ?? '-' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-phone"></i> No HP</span>
                            <span class="detail-value">{{ $peserta->nohp_siswa ?? '-' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-calendar-plus"></i> Terdaftar</span>
                            <span class="detail-value">{{ \Carbon\Carbon::parse($peserta->tanggal_bergabung)->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    <div class="member-actions-row">
                        <button class="btn-download-berkas" onclick="event.stopPropagation(); openBerkasModal('{{ addslashes($peserta->nama) }}', '{{ $peserta->mapel_osn_2026 ?? $ajang->nama_ajang }}')">
                            <i class="fas fa-file-download"></i> Download Berkas
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
</div>

{{-- PHOTO VIEW MODAL --}}
<div id="photoModal" class="photo-modal" onclick="if(event.target===this)closePhotoModal()">
    <div class="photo-modal-content">
        <button class="photo-modal-close" onclick="closePhotoModal()">&times;</button>
        <div id="photoModalBody">
            <img id="photoModalImg" src="" alt="Foto" class="photo-modal-img">
            <div id="photoModalPlaceholder" class="photo-modal-placeholder"></div>
        </div>
        <div class="photo-modal-info">
            <h4 id="photoModalName"></h4>
        </div>
        <div class="photo-modal-actions">
            <button class="btn-photo-download" id="btnPhotoDownload" onclick="downloadPhoto()">
                <i class="fas fa-download"></i> Download Foto
            </button>
            <button class="btn-photo-close" onclick="closePhotoModal()">
                <i class="fas fa-times"></i> Tutup
            </button>
        </div>
    </div>
</div>

{{-- DOWNLOAD BERKAS MODAL --}}
<div id="berkasModal" class="photo-modal" onclick="if(event.target===this)closeBerkasModal()">
    <div class="photo-modal-content berkas-modal-content">
        <button class="photo-modal-close" onclick="closeBerkasModal()">&times;</button>
        <div class="berkas-header">
            <div class="berkas-icon"><i class="fas fa-file-alt"></i></div>
            <h3>Download Berkas</h3>
            <p id="berkasNama"></p>
        </div>
        <div class="berkas-list">
            <div class="berkas-item" onclick="alert('Format Pakta Integritas belum disusun.')">
                <div class="berkas-item-icon" style="background: linear-gradient(135deg, #7c3aed, #6d28d9);">
                    <i class="fas fa-file-signature"></i>
                </div>
                <div class="berkas-item-info">
                    <h4>Pakta Integritas</h4>
                    <p>Surat pernyataan integritas peserta OSN</p>
                </div>
                <i class="fas fa-download berkas-dl-icon"></i>
            </div>
            <div class="berkas-item" onclick="alert('Format Surat Keterangan belum disusun.')">
                <div class="berkas-item-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="fas fa-file-contract"></i>
                </div>
                <div class="berkas-item-info">
                    <h4>Surat Keterangan Kepala Sekolah</h4>
                    <p>Surat keterangan dari kepala sekolah</p>
                </div>
                <i class="fas fa-download berkas-dl-icon"></i>
            </div>
        </div>
        <button class="btn-photo-close" onclick="closeBerkasModal()" style="width:100%;margin-top:15px;">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>
</div>

<style>
.peserta-ajang-page { padding: 30px; background: #f8f9fa; min-height: calc(100vh - 60px); }

/* Header */
.page-header-center { text-align: center; margin-bottom: 25px; }
.header-icon-large {
    width: 80px; height: 80px; border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    font-size: 36px; color: white; margin: 0 auto 20px;
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    box-shadow: 0 8px 25px rgba(124,58,237,0.4);
}
.page-header-center h1 { font-size: 24px; font-weight: 700; margin: 0 0 5px; color: #1f2937; }
.page-header-center p { color: #6b7280; margin: 0 0 10px; }
.header-badge {
    display: inline-block; padding: 4px 14px; border-radius: 20px;
    background: #f5f3ff; color: #7c3aed; font-size: 12px; font-weight: 600;
    border: 1px solid #ddd6fe;
}

/* Buttons */
.action-buttons-center { display: flex; justify-content: center; gap: 15px; margin-bottom: 25px; flex-wrap: wrap; }
.btn-back {
    display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px;
    background: white; color: #374151; border: 2px solid #d1d5db;
    border-radius: 10px; text-decoration: none; font-weight: 600; transition: all 0.3s;
}
.btn-back:hover { border-color: #7c3aed; color: #7c3aed; }

/* Stats */
.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 25px; }
.stat-card {
    background: white; padding: 20px; border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08); display: flex;
    align-items: center; gap: 15px; border: 1px solid #e5e7eb;
}
.stat-icon {
    width: 50px; height: 50px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; color: white;
}
.stat-icon.primary { background: linear-gradient(135deg, #7c3aed, #6d28d9); }
.stat-icon.success { background: linear-gradient(135deg, #10b981, #059669); }
.stat-icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
.stat-info h3 { margin: 0; font-size: 20px; font-weight: 700; color: #1f2937; }
.stat-info p { margin: 4px 0 0; color: #6b7280; font-size: 12px; }

/* Members Container */
.members-container { background: white; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow: hidden; }
.members-header {
    padding: 20px 25px; border-bottom: 1px solid #e5e7eb;
    display: flex; justify-content: space-between; align-items: center;
}
.members-title { display: flex; align-items: center; gap: 10px; }
.members-title h2 { margin: 0; font-size: 1.1rem; color: #1f2937; }
.members-count { padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; background: rgba(124,58,237,0.1); color: #7c3aed; }

/* Empty */
.empty-state { padding: 60px 30px; text-align: center; }
.empty-icon { width: 80px; height: 80px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
.empty-icon i { font-size: 30px; color: #9ca3af; }
.empty-state h3 { margin: 0 0 10px; color: #1f2937; }
.empty-state p { margin: 0; color: #6b7280; }

/* Cards */
.members-cards-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 15px; padding: 20px;
}
.member-card {
    background: white; border-radius: 12px; border: 1px solid #e5e7eb;
    overflow: hidden; transition: all 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.member-card:hover { box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-color: #c4b5fd; }
.member-card.expanded { border-color: #7c3aed; }
.member-card-header {
    display: flex; justify-content: space-between; align-items: center;
    padding: 15px; cursor: pointer; transition: all 0.2s; background: rgba(124,58,237,0.04);
}
.member-header-left { display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0; }
.member-avatar {
    width: 45px; height: 45px; border-radius: 50%; overflow: hidden; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    border: 2px solid rgba(255,255,255,0.3); box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    cursor: pointer;
}
.member-avatar img { width: 100%; height: 100%; object-fit: cover; }
.member-avatar .avatar-initial { color: white; font-weight: 700; font-size: 16px; }
.member-name-info { flex: 1; min-width: 0; }
.member-name-info h4 { margin: 0 0 3px; font-size: 15px; font-weight: 600; color: #1f2937; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.member-name-info .member-rombel { font-size: 12px; color: #6b7280; display: block; }
.expand-icon { transition: transform 0.3s; color: #7c3aed; }
.member-card.expanded .expand-icon { transform: rotate(180deg); }

/* Card Body */
.member-card-body {
    max-height: 0; overflow: hidden; opacity: 0;
    transition: max-height 0.4s ease, opacity 0.3s ease, padding 0.3s ease;
    padding: 0 15px; background: #fafafa;
}
.member-card.expanded .member-card-body { max-height: 2000px; opacity: 1; padding: 15px; }

.detail-section-title {
    font-size: 12px; font-weight: 700; color: #7c3aed; text-transform: uppercase;
    margin: 12px 0 8px; display: flex; align-items: center; gap: 6px;
    padding-bottom: 6px; border-bottom: 2px solid rgba(124,58,237,0.15);
}
.detail-section-title:first-child { margin-top: 0; }
.detail-section-title i { font-size: 11px; }
.member-details-grid { display: flex; flex-direction: column; gap: 4px; margin-bottom: 8px; }
.detail-item { display: flex; align-items: center; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f3f4f6; }
.detail-label { font-size: 11px; color: #6b7280; font-weight: 600; text-transform: uppercase; display: flex; align-items: center; gap: 5px; }
.detail-label i { font-size: 10px; color: #7c3aed; }
.detail-value { font-size: 13px; color: #1f2937; font-weight: 500; text-align: right; max-width: 60%; }

.badge-jk { padding: 3px 8px; border-radius: 15px; font-size: 11px; font-weight: 600; }
.badge-laki { background: rgba(59,130,246,0.1); color: #3b82f6; }
.badge-perempuan { background: rgba(236,72,153,0.1); color: #ec4899; }

.member-actions-row { padding-top: 15px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
.btn-download-berkas {
    background: rgba(124,58,237,0.1); color: #7c3aed; border: none;
    padding: 10px 15px; border-radius: 8px; cursor: pointer;
    display: flex; align-items: center; gap: 6px;
    font-size: 13px; font-weight: 600; transition: all 0.2s;
}
.btn-download-berkas:hover { background: rgba(124,58,237,0.2); transform: translateY(-1px); }

/* Photo Modal */
.photo-modal {
    display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.7); backdrop-filter: blur(8px);
    z-index: 20000; align-items: center; justify-content: center; padding: 20px;
}
.photo-modal.active { display: flex; }
.photo-modal-content {
    background: white; border-radius: 18px; width: 100%; max-width: 420px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3); animation: modalSlideIn 0.3s ease;
    padding: 24px; position: relative; text-align: center;
}
@keyframes modalSlideIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
.photo-modal-close {
    position: absolute; top: 12px; right: 12px; width: 32px; height: 32px;
    border-radius: 50%; background: #f3f4f6; border: none; color: #6b7280;
    cursor: pointer; font-size: 18px; display: flex; align-items: center; justify-content: center;
    transition: all 0.2s; z-index: 1;
}
.photo-modal-close:hover { background: #fee2e2; color: #ef4444; }
.photo-modal-img {
    width: 200px; height: 200px; border-radius: 16px; object-fit: cover;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15); margin: 0 auto 15px;
}
.photo-modal-placeholder {
    width: 200px; height: 200px; border-radius: 16px;
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 64px; font-weight: 800; margin: 0 auto 15px;
}
.photo-modal-info h4 { font-size: 16px; color: #1f2937; margin-bottom: 15px; }
.photo-modal-actions { display: flex; gap: 10px; justify-content: center; }
.btn-photo-download {
    padding: 10px 20px; border-radius: 10px; border: none; cursor: pointer;
    font-size: 13px; font-weight: 600; font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white;
    display: flex; align-items: center; gap: 6px; transition: all 0.2s;
}
.btn-photo-download:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(124,58,237,0.3); }
.btn-photo-close {
    padding: 10px 20px; border-radius: 10px; border: 1px solid #d1d5db;
    background: white; color: #374151; cursor: pointer;
    font-size: 13px; font-weight: 600; font-family: 'Poppins', sans-serif;
    display: flex; align-items: center; gap: 6px; justify-content: center; transition: all 0.2s;
}
.btn-photo-close:hover { background: #f3f4f6; }

/* Download Berkas Modal */
.berkas-modal-content { max-width: 460px; text-align: left; }
.berkas-header { text-align: center; margin-bottom: 20px; }
.berkas-icon {
    width: 60px; height: 60px; border-radius: 16px; margin: 0 auto 12px;
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 24px;
}
.berkas-header h3 { font-size: 18px; color: #1f2937; margin: 0 0 4px; }
.berkas-header p { font-size: 13px; color: #6b7280; margin: 0; }
.berkas-list { display: flex; flex-direction: column; gap: 10px; }
.berkas-item {
    display: flex; align-items: center; gap: 14px; padding: 14px;
    border: 1px solid #e5e7eb; border-radius: 12px; cursor: pointer;
    transition: all 0.2s; background: #fafafa;
}
.berkas-item:hover { border-color: #c4b5fd; background: #f5f3ff; transform: translateY(-1px); }
.berkas-item-icon {
    width: 42px; height: 42px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 18px; flex-shrink: 0;
}
.berkas-item-info { flex: 1; }
.berkas-item-info h4 { font-size: 14px; color: #1f2937; margin: 0 0 2px; }
.berkas-item-info p { font-size: 11px; color: #6b7280; margin: 0; }
.berkas-dl-icon { color: #7c3aed; font-size: 16px; }

/* Responsive */
@media (max-width: 768px) {
    .peserta-ajang-page { padding: 12px; }

    /* Header */
    .page-header-center { margin-bottom: 16px; }
    .header-icon-large {
        width: 60px; height: 60px; border-radius: 16px;
        font-size: 28px; margin-bottom: 14px;
    }
    .page-header-center h1 { font-size: 18px; }
    .page-header-center p { font-size: 13px; margin-bottom: 6px; }
    .header-badge { font-size: 11px; padding: 3px 12px; }

    /* Action buttons */
    .action-buttons-center { margin-bottom: 16px; }
    .btn-back { padding: 10px 16px; font-size: 13px; border-radius: 8px; }

    /* Stats */
    .stats-grid { grid-template-columns: 1fr; gap: 10px; margin-bottom: 16px; }
    .stat-card { padding: 14px; gap: 12px; border-radius: 10px; }
    .stat-icon { width: 42px; height: 42px; border-radius: 8px; font-size: 16px; }
    .stat-info h3 { font-size: 17px; }
    .stat-info p { font-size: 11px; }

    /* Members container */
    .members-container { border-radius: 12px; }
    .members-header { padding: 14px 16px; }
    .members-title h2 { font-size: 0.95rem; }
    .members-count { padding: 4px 12px; font-size: 0.75rem; }

    /* Cards grid */
    .members-cards-grid { grid-template-columns: 1fr; gap: 10px; padding: 12px; }

    /* Card header */
    .member-card { border-radius: 10px; }
    .member-card-header { padding: 12px; }
    .member-avatar { width: 40px; height: 40px; }
    .member-avatar .avatar-initial { font-size: 14px; }
    .member-name-info h4 { font-size: 14px; }
    .member-name-info .member-rombel { font-size: 11px; }

    /* Card body */
    .member-card.expanded .member-card-body { padding: 12px; }
    .detail-section-title { font-size: 10px; margin: 8px 0 6px; }
    .detail-label { font-size: 10px; }
    .detail-value { font-size: 12px; max-width: 55%; }
    .detail-item { padding: 5px 0; }
    .badge-jk { font-size: 10px; padding: 2px 6px; }

    /* Actions */
    .member-actions-row { padding-top: 12px; }
    .btn-download-berkas { padding: 8px 12px; font-size: 12px; }

    /* Photo Modal */
    .photo-modal-content { padding: 18px; border-radius: 14px; max-width: 340px; }
    .photo-modal-img { width: 160px; height: 160px; border-radius: 12px; margin-bottom: 12px; }
    .photo-modal-placeholder { width: 160px; height: 160px; border-radius: 12px; font-size: 52px; margin-bottom: 12px; }
    .photo-modal-info h4 { font-size: 14px; margin-bottom: 12px; }
    .photo-modal-actions { flex-direction: column; gap: 8px; }
    .btn-photo-download, .btn-photo-close { width: 100%; justify-content: center; padding: 10px; font-size: 12px; }

    /* Berkas Modal */
    .berkas-modal-content { max-width: 340px; padding: 18px; }
    .berkas-icon { width: 48px; height: 48px; border-radius: 12px; font-size: 20px; }
    .berkas-header h3 { font-size: 15px; }
    .berkas-header p { font-size: 11px; }
    .berkas-item { padding: 10px; gap: 10px; }
    .berkas-item-icon { width: 36px; height: 36px; border-radius: 8px; font-size: 14px; }
    .berkas-item-info h4 { font-size: 12px; }
    .berkas-item-info p { font-size: 10px; }

    /* Empty state */
    .empty-state { padding: 40px 20px; }
    .empty-icon { width: 60px; height: 60px; }
    .empty-icon i { font-size: 24px; }
    .empty-state h3 { font-size: 15px; }
    .empty-state p { font-size: 12px; }
}

@media (max-width: 400px) {
    .peserta-ajang-page { padding: 8px; }
    .header-icon-large { width: 50px; height: 50px; font-size: 24px; }
    .page-header-center h1 { font-size: 16px; }
    .stat-card { padding: 12px; }
    .stat-info h3 { font-size: 15px; }
    .members-cards-grid { padding: 8px; }
    .member-card-header { padding: 10px; }
    .member-avatar { width: 36px; height: 36px; }
    .member-name-info h4 { font-size: 13px; }
    .photo-modal-content, .berkas-modal-content { max-width: 300px; padding: 14px; }
    .photo-modal-img, .photo-modal-placeholder { width: 140px; height: 140px; }
}
</style>

<script>
function toggleCard(cardId) {
    const card = document.querySelector(`[data-card-id="${cardId}"]`);
    if (card) card.classList.toggle('expanded');
}

// Photo Modal
let currentPhotoUrl = '';
let currentPhotoFilename = '';

function openPhotoModal(fotoUrl, initial, nama, mapel) {
    const modal = document.getElementById('photoModal');
    const img = document.getElementById('photoModalImg');
    const placeholder = document.getElementById('photoModalPlaceholder');
    const nameEl = document.getElementById('photoModalName');
    const dlBtn = document.getElementById('btnPhotoDownload');

    nameEl.textContent = nama;
    currentPhotoFilename = mapel + ' - ' + nama;

    if (fotoUrl) {
        img.src = fotoUrl;
        img.style.display = 'block';
        placeholder.style.display = 'none';
        currentPhotoUrl = fotoUrl;
        dlBtn.style.display = 'flex';
    } else {
        img.style.display = 'none';
        placeholder.style.display = 'flex';
        placeholder.textContent = initial;
        currentPhotoUrl = '';
        dlBtn.style.display = 'none';
    }

    modal.classList.add('active');
}

function closePhotoModal() {
    document.getElementById('photoModal').classList.remove('active');
}

function downloadPhoto() {
    if (!currentPhotoUrl) return;
    const a = document.createElement('a');
    a.href = currentPhotoUrl;
    a.download = currentPhotoFilename + '.jpg';
    a.target = '_blank';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

// Berkas Modal
function openBerkasModal(nama, mapel) {
    document.getElementById('berkasNama').textContent = mapel + ' - ' + nama;
    document.getElementById('berkasModal').classList.add('active');
}

function closeBerkasModal() {
    document.getElementById('berkasModal').classList.remove('active');
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closePhotoModal();
        closeBerkasModal();
    }
});
</script>
@endsection
