@extends('layouts.app-guru-bk')

@section('content')
<div class="main-content pelanggaran-page">
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
        <div class="header-icon-large">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h1>Pelanggaran Siswa</h1>
    </div>

    {{-- Action Buttons --}}
    <div class="action-buttons-center">
        <button onclick="openInputModal()" class="btn-add">
            <i class="fas fa-plus-circle"></i> Input Pelanggaran
        </button>
    </div>

    {{-- Stats - Only total pelanggaran --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ count($pelanggaranList) }}</h3>
                <p>Total Pelanggaran</p>
            </div>
        </div>
    </div>

    {{-- Pelanggaran List --}}
    <div class="pelanggaran-container">
        <div class="container-header">
            <div class="container-title">
                <i class="fas fa-clipboard-list"></i>
                <h2>Daftar Pelanggaran</h2>
            </div>
            <span class="count-badge">{{ count($pelanggaranList) }} Catatan</span>
        </div>

        @if(count($pelanggaranList) == 0)
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3>Tidak Ada Pelanggaran</h3>
            <p>Belum ada pelanggaran tercatat pada tanggal ini.</p>
        </div>
        @else
        <div class="pelanggaran-cards">
            @foreach($pelanggaranList as $item)
            <div class="pelanggaran-card">
                <div class="card-header-row">
                    <div class="card-header-left">
                        <div class="jenis-badge {{ strtolower(str_replace(' ', '-', $item->jenis_pelanggaran)) }}">
                            @php
                                $jenisIcons = [
                                    'Keterlambatan' => 'fa-clock',
                                    'Perusakan Fasilitas Sekolah' => 'fa-tools',
                                    'Penyebaran Konten Hoax' => 'fa-newspaper',
                                    'Penyebaran Konten Asusila' => 'fa-ban',
                                    'Kriminal' => 'fa-gavel',
                                    'Lainnya' => 'fa-ellipsis-h',
                                ];
                                $icon = $jenisIcons[$item->jenis_pelanggaran] ?? 'fa-exclamation';
                            @endphp
                            <i class="fas {{ $icon }}"></i>
                            {{ $item->jenis_pelanggaran === 'Lainnya' && $item->jenis_lainnya ? $item->jenis_lainnya : $item->jenis_pelanggaran }}
                        </div>
                        <span class="waktu-badge">
                            <i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}
                        </span>
                        <span class="waktu-badge">
                            <i class="fas fa-clock"></i> {{ $item->waktu ? \Carbon\Carbon::parse($item->waktu)->format('H:i') : '-' }}
                        </span>
                    </div>
                    <div class="card-actions">
                        <button type="button" class="btn-icon btn-edit" title="Edit" data-pelanggaran="{{ json_encode($item->only(['id','tanggal','waktu','jenis_pelanggaran','jenis_lainnya','deskripsi','sanksi'])) }}" onclick="openEditModal(JSON.parse(this.dataset.pelanggaran))">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn-icon btn-phone" title="Panggilan Orang Tua" data-siswa-list="{{ json_encode($item->siswa->map(fn($s) => ['nisn' => $s->nisn, 'nama' => $s->nama, 'jk' => $s->jk])->values()) }}" onclick="openPanggilanModal(JSON.parse(this.dataset.siswaList))">
                            <i class="fas fa-phone"></i>
                        </button>
                        <form method="POST" action="{{ route('guru_bk.pelanggaran.destroy', $item->id) }}" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus catatan pelanggaran ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon btn-trash" title="Hapus">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Students involved --}}
                <div class="siswa-involved">
                    <div class="siswa-label"><i class="fas fa-users"></i> Siswa Terlibat ({{ $item->siswa->count() }})</div>
                    <div class="siswa-tags">
                        @foreach($item->siswa as $s)
                        <span class="siswa-tag">
                            <span class="siswa-initial {{ $s->jk == 'Laki-laki' ? 'laki' : 'perempuan' }}">{{ strtoupper(substr($s->nama, 0, 1)) }}</span>
                            {{ $s->nama }}
                        </span>
                        @endforeach
                    </div>
                </div>

                @if($item->deskripsi)
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-align-left"></i> Deskripsi</span>
                    <p class="detail-text">{{ $item->deskripsi }}</p>
                </div>
                @endif

                @if($item->sanksi)
                <div class="detail-row">
                    <span class="detail-label"><i class="fas fa-balance-scale"></i> Sanksi</span>
                    <p class="detail-text">{{ $item->sanksi }}</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- Modal Input Pelanggaran --}}
<div id="modalInput" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-plus-circle"></i> Input Pelanggaran</h3>
            <button class="modal-close" onclick="closeInputModal()">×</button>
        </div>
        <form method="POST" action="{{ route('guru_bk.pelanggaran.store') }}" id="formPelanggaran">
            @csrf
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-calendar"></i> Tanggal</label>
                        <input type="date" name="tanggal" id="inputTanggal" value="{{ date('Y-m-d') }}" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-clock"></i> Waktu</label>
                        <input type="time" name="waktu" id="inputWaktu" value="{{ date('H:i') }}" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Jenis Pelanggaran</label>
                    <select name="jenis_pelanggaran" id="jenisPelanggaran" required class="form-control" onchange="toggleJenisLainnya()">
                        <option value="">-- Pilih Jenis --</option>
                        <option value="Keterlambatan">Keterlambatan</option>
                        <option value="Perusakan Fasilitas Sekolah">Perusakan Fasilitas Sekolah</option>
                        <option value="Penyebaran Konten Hoax">Penyebaran Konten Hoax</option>
                        <option value="Penyebaran Konten Asusila">Penyebaran Konten Asusila</option>
                        <option value="Kriminal">Kriminal</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="form-group" id="jenisLainnyaGroup" style="display: none;">
                    <label><i class="fas fa-pen"></i> Jenis Lainnya</label>
                    <input type="text" name="jenis_lainnya" id="jenisLainnyaInput" placeholder="Masukkan jenis pelanggaran..." class="form-control">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Deskripsi Pelanggaran</label>
                    <textarea name="deskripsi" rows="3" placeholder="Jelaskan detail pelanggaran..." class="form-control"></textarea>
                </div>

                {{-- Tambah Siswa --}}
                <div class="form-group">
                    <label><i class="fas fa-users"></i> Siswa Terlibat</label>
                    <button type="button" onclick="openSiswaModal()" class="btn-add-siswa">
                        <i class="fas fa-user-plus"></i> Tambah Siswa
                    </button>
                    <div class="selected-siswa-list" id="selectedSiswaList">
                        <p class="no-siswa-msg" id="noSiswaMsg">Belum ada siswa dipilih</p>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-balance-scale"></i> Sanksi</label>
                    <textarea name="sanksi" rows="2" placeholder="Sanksi yang diberikan..." class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeInputModal()">Batal</button>
                <button type="submit" class="btn-primary" id="btnSubmit">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Pelanggaran --}}
<div id="modalEdit" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-edit" style="color: #f59e0b;"></i> Edit Pelanggaran</h3>
            <button class="modal-close" onclick="closeEditModal()">×</button>
        </div>
        <form method="POST" action="" id="formEditPelanggaran">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-calendar"></i> Tanggal</label>
                        <input type="date" name="tanggal" id="editTanggal" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-clock"></i> Waktu</label>
                        <input type="time" name="waktu" id="editWaktu" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Jenis Pelanggaran</label>
                    <select name="jenis_pelanggaran" id="editJenisPelanggaran" required class="form-control" onchange="toggleEditJenisLainnya()">
                        <option value="">-- Pilih Jenis --</option>
                        <option value="Keterlambatan">Keterlambatan</option>
                        <option value="Perusakan Fasilitas Sekolah">Perusakan Fasilitas Sekolah</option>
                        <option value="Penyebaran Konten Hoax">Penyebaran Konten Hoax</option>
                        <option value="Penyebaran Konten Asusila">Penyebaran Konten Asusila</option>
                        <option value="Kriminal">Kriminal</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="form-group" id="editJenisLainnyaGroup" style="display: none;">
                    <label><i class="fas fa-pen"></i> Jenis Lainnya</label>
                    <input type="text" name="jenis_lainnya" id="editJenisLainnyaInput" placeholder="Masukkan jenis pelanggaran..." class="form-control">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Deskripsi Pelanggaran</label>
                    <textarea name="deskripsi" id="editDeskripsi" rows="3" placeholder="Jelaskan detail pelanggaran..." class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-balance-scale"></i> Sanksi</label>
                    <textarea name="sanksi" id="editSanksi" rows="2" placeholder="Sanksi yang diberikan..." class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn-primary btn-primary-warning">
                    <i class="fas fa-save"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Pencarian Siswa --}}
<div id="modalSiswa" class="modal-overlay modal-siswa-overlay">
    <div class="modal-container modal-siswa-container">
        <div class="modal-header modal-header-siswa">
            <h3><i class="fas fa-search"></i> Cari Siswa</h3>
            <button class="modal-close" onclick="closeSiswaModal()">×</button>
        </div>
        <div class="modal-body">
            <div class="search-box">
                <input type="text" class="search-input" id="searchSiswaInput" placeholder="Ketik nama / NISN / NIS..." oninput="searchSiswaDebounced()">
                <i class="fas fa-search search-icon-abs"></i>
            </div>
            <div class="selected-count-bar" id="selectedCountBar" style="display: none;">
                <strong>Dipilih:</strong> <span id="selectedCountText">0</span> siswa
            </div>
            <div class="siswa-search-results" id="siswaSearchResults">
                <div class="search-placeholder">
                    <i class="fas fa-search"></i>
                    <p>Ketik nama siswa untuk mencari...</p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeSiswaModal()">Batal</button>
            <button type="button" class="btn-primary" onclick="confirmSiswaSelection()">
                <i class="fas fa-check"></i> Pilih Siswa
            </button>
        </div>
    </div>
</div>

{{-- Modal Pilih Siswa untuk Panggilan Orang Tua --}}
<div id="modalPanggilan" class="modal-overlay">
    <div class="modal-container modal-panggilan-container">
        <div class="modal-header">
            <h3><i class="fas fa-phone" style="color: #3b82f6;"></i> Panggilan Orang Tua</h3>
            <button class="modal-close" onclick="closePanggilanModal()">×</button>
        </div>
        <div class="modal-body">
            <p class="panggilan-info"><i class="fas fa-info-circle"></i> Pilih siswa yang akan dibuatkan surat panggilan orang tua:</p>
            <div class="panggilan-select-all">
                <label class="panggilan-checkbox-label">
                    <input type="checkbox" id="panggilanSelectAll" onchange="toggleSelectAllPanggilan()">
                    <span>Pilih Semua</span>
                </label>
            </div>
            <div class="panggilan-siswa-list" id="panggilanSiswaList">
                {{-- Populated by JS --}}
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closePanggilanModal()">Batal</button>
            <button type="button" class="btn-primary" id="btnBuatPanggilan" onclick="navigatePanggilan()" disabled>
                <i class="fas fa-paper-plane"></i> Buat Surat Panggilan
            </button>
        </div>
    </div>
</div>

<style>
:root {
    --primary: #3b82f6;
    --primary-dark: #1d4ed8;
    --danger: #ef4444;
    --danger-dark: #dc2626;
    --warning: #f59e0b;
    --success: #10b981;
}

.pelanggaran-page {
    padding: 30px;
    background: #f8f9fa;
    min-height: calc(100vh - 60px);
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
.toast-notification.toast-error {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    box-shadow: 0 10px 40px rgba(239,68,68,0.4);
}
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
    background: linear-gradient(135deg, #ef4444, #dc2626);
    box-shadow: 0 8px 25px rgba(239,68,68,0.4);
}
.page-header-center h1 { font-size: 24px; font-weight: 700; margin: 0; color: #1f2937; }

/* Action Buttons */
.action-buttons-center {
    display: flex; justify-content: center; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; align-items: flex-end;
}
.filter-form { display: flex; align-items: flex-end; }
.date-filter-group label {
    display: block; font-size: 12px; font-weight: 600; color: #6b7280; margin-bottom: 6px;
}
.date-filter-group label i { margin-right: 4px; }
.date-input {
    padding: 10px 14px; border: 2px solid #d1d5db; border-radius: 10px;
    font-size: 14px; font-weight: 500; font-family: 'Poppins', sans-serif;
    background: white; cursor: pointer; transition: all 0.3s;
}
.date-input:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
.btn-add {
    display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px;
    background: linear-gradient(135deg, #ef4444, #dc2626); color: white;
    border: none; border-radius: 10px; font-weight: 600; cursor: pointer;
    box-shadow: 0 4px 15px rgba(239,68,68,0.35); transition: all 0.3s; font-family: 'Poppins', sans-serif; font-size: 13px;
}
.btn-add:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(239,68,68,0.4); }

/* Stats */
.stats-grid { display: grid; grid-template-columns: 1fr; gap: 15px; margin-bottom: 20px; max-width: 280px; margin-left: auto; margin-right: auto; }
.stat-card {
    background: white; padding: 16px 20px; border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 15px; border: 1px solid #e5e7eb;
}
.stat-icon {
    width: 46px; height: 46px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; font-size: 18px; color: white;
}
.stat-icon.danger { background: linear-gradient(135deg, #ef4444, #dc2626); }
.stat-info h3 { margin: 0; font-size: 20px; font-weight: 700; color: #1f2937; }
.stat-info p { margin: 4px 0 0 0; color: #6b7280; font-size: 12px; }

/* Pelanggaran Container */
.pelanggaran-container {
    background: white; border-radius: 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow: hidden;
}
.container-header {
    padding: 16px 20px; border-bottom: 1px solid #e5e7eb;
    display: flex; justify-content: space-between; align-items: center;
}
.container-title { display: flex; align-items: center; gap: 10px; }
.container-title i { color: var(--danger); font-size: 16px; }
.container-title h2 { margin: 0; font-size: 1rem; color: #1f2937; }
.count-badge {
    padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;
    background: rgba(239,68,68,0.1); color: #ef4444;
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

/* Pelanggaran Cards */
.pelanggaran-cards { padding: 15px; display: flex; flex-direction: column; gap: 12px; }

.pelanggaran-card {
    background: white; border: 1px solid #e5e7eb; border-radius: 12px;
    padding: 16px; transition: all 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.pelanggaran-card:hover { box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-color: #ef4444; }

.card-header-row {
    display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; gap: 10px;
}
.card-header-left { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

.jenis-badge {
    display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px;
    border-radius: 8px; font-size: 12px; font-weight: 600;
    background: rgba(239,68,68,0.1); color: #ef4444;
}
.jenis-badge.keterlambatan { background: rgba(245,158,11,0.1); color: #d97706; }
.jenis-badge.kriminal { background: rgba(139,92,246,0.1); color: #7c3aed; }
.jenis-badge.perusakan-fasilitas-sekolah { background: rgba(59,130,246,0.1); color: #2563eb; }
.jenis-badge.penyebaran-konten-hoax { background: rgba(249,115,22,0.1); color: #ea580c; }
.jenis-badge.penyebaran-konten-asusila { background: rgba(236,72,153,0.1); color: #db2777; }

.waktu-badge {
    font-size: 11px; color: #6b7280; display: flex; align-items: center; gap: 4px;
}
.card-actions { display: flex; gap: 6px; flex-shrink: 0; }
.btn-icon {
    width: 34px; height: 34px; border-radius: 8px; border: none;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all 0.2s; font-size: 13px;
}
.btn-edit { background: rgba(245,158,11,0.1); color: #f59e0b; }
.btn-edit:hover { background: #f59e0b; color: white; }
.btn-phone {
    background: rgba(59,130,246,0.1); color: #3b82f6; text-decoration: none;
}
.btn-phone:hover { background: #3b82f6; color: white; }
.btn-trash { background: rgba(239,68,68,0.1); color: #ef4444; }
.btn-trash:hover { background: #ef4444; color: white; }

/* Siswa Tags */
.siswa-involved { margin-bottom: 10px; }
.siswa-label { font-size: 11px; font-weight: 600; color: #6b7280; margin-bottom: 6px; display: flex; align-items: center; gap: 5px; }
.siswa-tags { display: flex; flex-wrap: wrap; gap: 6px; }
.siswa-tag {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; background: #f3f4f6; border-radius: 16px;
    font-size: 12px; font-weight: 500; color: #374151;
}
.siswa-initial {
    width: 20px; height: 20px; border-radius: 50%; display: flex;
    align-items: center; justify-content: center; font-size: 9px;
    font-weight: 700; color: white; flex-shrink: 0;
}
.siswa-initial.laki { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.siswa-initial.perempuan { background: linear-gradient(135deg, #ec4899, #db2777); }

.detail-row { margin-bottom: 6px; }
.detail-label { font-size: 11px; font-weight: 600; color: #6b7280; display: flex; align-items: center; gap: 5px; margin-bottom: 3px; }
.detail-text { margin: 0; font-size: 13px; color: #374151; line-height: 1.5; }

/* ===================== MODALS ===================== */
.modal-overlay {
    display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);
    z-index: 10000; align-items: flex-start; justify-content: center;
    overflow-y: auto; padding: 20px 0;
}
.modal-overlay.active { display: flex; }

.modal-container {
    background: white; border-radius: 16px; width: 520px; max-width: 95%;
    display: flex; flex-direction: column; margin: auto;
    box-shadow: 0 25px 60px rgba(0,0,0,0.3); animation: modalIn 0.3s ease;
    flex-shrink: 0;
}
@keyframes modalIn {
    from { transform: scale(0.9) translateY(20px); opacity: 0; }
    to { transform: scale(1) translateY(0); opacity: 1; }
}

.modal-header {
    padding: 16px 20px; border-bottom: 1px solid #e5e7eb;
    display: flex; justify-content: space-between; align-items: center;
}
.modal-header h3 { margin: 0; font-size: 16px; font-weight: 700; color: #1f2937; display: flex; align-items: center; gap: 8px; }
.modal-header h3 i { color: #ef4444; }
.modal-header-siswa h3 i { color: #3b82f6; }
.modal-close {
    width: 30px; height: 30px; border: none; background: #f3f4f6;
    border-radius: 8px; font-size: 18px; cursor: pointer; color: #6b7280;
    display: flex; align-items: center; justify-content: center; transition: all 0.2s;
}
.modal-close:hover { background: #ef4444; color: white; }

.modal-body { padding: 20px; overflow-y: auto; flex: 1; }

.modal-footer {
    padding: 14px 20px; border-top: 1px solid #e5e7eb;
    display: flex; justify-content: flex-end; gap: 8px;
}

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.form-group { margin-bottom: 14px; }
.form-group label {
    display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 5px;
}
.form-group label i { margin-right: 4px; color: #6b7280; }
.form-control {
    width: 100%; padding: 9px 12px; border: 2px solid #e5e7eb; border-radius: 10px;
    font-size: 13px; font-family: 'Poppins', sans-serif; transition: all 0.2s;
    box-sizing: border-box;
}
.form-control:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
select.form-control { cursor: pointer; appearance: auto; }
textarea.form-control { resize: vertical; }

.btn-add-siswa {
    display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white;
    border: none; border-radius: 10px; font-weight: 600; cursor: pointer;
    font-family: 'Poppins', sans-serif; transition: all 0.3s; font-size: 12px;
    box-shadow: 0 4px 12px rgba(59,130,246,0.3);
}
.btn-add-siswa:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(59,130,246,0.4); }

.selected-siswa-list { margin-top: 8px; display: flex; flex-wrap: wrap; gap: 6px; }
.no-siswa-msg { font-size: 12px; color: #9ca3af; margin: 0; }

.selected-siswa-chip {
    display: inline-flex; align-items: center; gap: 5px; padding: 5px 10px;
    background: linear-gradient(135deg, #eff6ff, #dbeafe); border: 1px solid #93c5fd;
    border-radius: 16px; font-size: 12px; font-weight: 500; color: #1d4ed8;
}
.selected-siswa-chip .chip-remove {
    width: 16px; height: 16px; border-radius: 50%; background: rgba(239,68,68,0.2);
    border: none; color: #ef4444; cursor: pointer; font-size: 11px;
    display: flex; align-items: center; justify-content: center; transition: all 0.2s;
}
.selected-siswa-chip .chip-remove:hover { background: #ef4444; color: white; }

/* Siswa Search Modal */
.modal-siswa-overlay { z-index: 10001; }
.modal-siswa-container { width: 480px; }

.search-box { position: relative; margin-bottom: 14px; }
.search-input {
    width: 100%; padding: 10px 14px 10px 38px; border: 2px solid #e5e7eb;
    border-radius: 12px; font-size: 13px; font-family: 'Poppins', sans-serif;
    transition: all 0.2s; box-sizing: border-box;
}
.search-input:focus { border-color: #3b82f6; outline: none; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
.search-icon-abs { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 13px; }

.selected-count-bar {
    background: rgba(59,130,246,0.1); color: #1d4ed8; padding: 7px 12px;
    border-radius: 8px; font-size: 12px; margin-bottom: 10px;
}

.siswa-search-results { max-height: 300px; overflow-y: auto; }

.search-placeholder { padding: 30px 20px; text-align: center; color: #9ca3af; }
.search-placeholder i { font-size: 26px; margin-bottom: 8px; display: block; }
.search-placeholder p { font-size: 13px; }

.siswa-result-item {
    display: flex; align-items: center; gap: 10px; padding: 10px;
    border-radius: 10px; cursor: pointer; transition: all 0.2s;
    border: 2px solid transparent; margin-bottom: 4px;
}
.siswa-result-item:hover { background: #f8fafc; border-color: #e2e8f0; }
.siswa-result-item.selected { background: #eff6ff; border-color: #3b82f6; }
.siswa-result-item .siswa-avatar {
    width: 36px; height: 36px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 13px; color: white; flex-shrink: 0;
}
.siswa-result-item .siswa-avatar.laki { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.siswa-result-item .siswa-avatar.perempuan { background: linear-gradient(135deg, #ec4899, #db2777); }
.siswa-result-info { flex: 1; min-width: 0; }
.siswa-result-info .siswa-nama { font-weight: 600; font-size: 13px; color: #1f2937; }
.siswa-result-info .siswa-meta { font-size: 11px; color: #6b7280; }
.siswa-result-item .check-icon {
    width: 22px; height: 22px; border: 2px solid #d1d5db; border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; color: transparent; transition: all 0.2s;
}
.siswa-result-item.selected .check-icon {
    background: #3b82f6; border-color: #3b82f6; color: white;
}

.btn-secondary {
    padding: 9px 18px; background: #f3f4f6; color: #374151;
    border: 1px solid #d1d5db; border-radius: 10px; font-weight: 600;
    cursor: pointer; font-family: 'Poppins', sans-serif; transition: all 0.2s; font-size: 13px;
}
.btn-secondary:hover { background: #e5e7eb; }
.btn-primary {
    padding: 9px 18px; background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white; border: none; border-radius: 10px; font-weight: 600;
    cursor: pointer; font-family: 'Poppins', sans-serif; transition: all 0.3s;
    display: flex; align-items: center; gap: 6px; font-size: 13px;
    box-shadow: 0 4px 12px rgba(239,68,68,0.3);
}
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(239,68,68,0.4); }
.btn-primary-warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    box-shadow: 0 4px 12px rgba(245,158,11,0.3);
}
.btn-primary-warning:hover { box-shadow: 0 6px 16px rgba(245,158,11,0.4); }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; transform: none !important; }

/* Panggilan Ortu Modal */
.modal-panggilan-container { width: 460px; }
.panggilan-info {
    font-size: 13px; color: #6b7280; margin: 0 0 14px 0; display: flex; align-items: center; gap: 6px;
}
.panggilan-info i { color: #3b82f6; }
.panggilan-select-all {
    padding: 8px 12px; background: #f8fafc; border-radius: 8px;
    border-bottom: 1px solid #e5e7eb; margin-bottom: 8px;
}
.panggilan-checkbox-label {
    display: flex; align-items: center; gap: 10px; cursor: pointer;
    font-size: 13px; font-weight: 600; color: #374151;
}
.panggilan-checkbox-label input[type="checkbox"] {
    width: 18px; height: 18px; accent-color: #3b82f6; cursor: pointer;
}
.panggilan-siswa-list { max-height: 300px; overflow-y: auto; }
.panggilan-siswa-item {
    display: flex; align-items: center; gap: 10px; padding: 10px 12px;
    border-radius: 8px; cursor: pointer; transition: all 0.2s;
    border: 2px solid transparent;
}
.panggilan-siswa-item:hover { background: #f8fafc; }
.panggilan-siswa-item.checked { background: #eff6ff; border-color: #93c5fd; }
.panggilan-siswa-item input[type="checkbox"] {
    width: 18px; height: 18px; accent-color: #3b82f6; cursor: pointer; flex-shrink: 0;
}
.panggilan-siswa-item .siswa-avatar {
    width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 13px; color: white;
}
.panggilan-siswa-item .siswa-avatar.laki { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.panggilan-siswa-item .siswa-avatar.perempuan { background: linear-gradient(135deg, #ec4899, #db2777); }
.panggilan-siswa-item .siswa-nama { font-weight: 600; font-size: 13px; color: #1f2937; }
.panggilan-siswa-item .siswa-nisn { font-size: 11px; color: #6b7280; }

/* ===================== RESPONSIVE ===================== */
@media (max-width: 768px) {
    .pelanggaran-page { padding: 12px; }
    .page-header-center { margin-bottom: 16px; }
    .header-icon-large { width: 56px; height: 56px; font-size: 26px; border-radius: 14px; margin-bottom: 12px; }
    .page-header-center h1 { font-size: 20px; }
    .action-buttons-center { gap: 8px; margin-bottom: 14px; }
    .date-input { padding: 9px 10px; font-size: 13px; }
    .btn-add { padding: 9px 14px; font-size: 12px; }
    .stats-grid { max-width: 100%; }
    .stat-card { padding: 12px 14px; }
    .stat-icon { width: 40px; height: 40px; font-size: 16px; }
    .stat-info h3 { font-size: 18px; }
    .container-header { padding: 12px 14px; }
    .container-title h2 { font-size: 0.9rem; }
    .pelanggaran-cards { padding: 10px; gap: 10px; }
    .pelanggaran-card { padding: 12px; border-radius: 10px; }
    .card-header-row { flex-direction: column; align-items: stretch; gap: 8px; }
    .card-header-left { justify-content: space-between; }
    .card-actions { justify-content: flex-end; }
    .btn-icon { width: 32px; height: 32px; font-size: 12px; }
    .siswa-tags { gap: 4px; }
    .siswa-tag { padding: 3px 8px; font-size: 11px; }
    .siswa-initial { width: 18px; height: 18px; font-size: 8px; }
    .form-row { grid-template-columns: 1fr; gap: 0; }
    .modal-container { width: 100%; max-width: 96%; border-radius: 14px; }
    .modal-header { padding: 14px 16px; }
    .modal-header h3 { font-size: 15px; }
    .modal-body { padding: 16px; }
    .modal-footer { padding: 12px 16px; }
    .jenis-badge { font-size: 11px; padding: 4px 10px; }
    .detail-text { font-size: 12px; }
    .toast-notification { top: 10px; right: 10px; left: 10px; min-width: auto; }
}

@media (max-width: 400px) {
    .pelanggaran-page { padding: 8px; }
    .action-buttons-center { flex-direction: column; align-items: stretch; }
    .filter-form { width: 100%; }
    .date-input { width: 100%; }
    .btn-add { justify-content: center; width: 100%; }
}
</style>

<script>
// ===== Toast =====
function hideToast() {
    const toast = document.getElementById('toastNotification');
    if (toast) { toast.style.animation = 'slideIn 0.3s ease reverse'; setTimeout(() => toast.remove(), 300); }
}
setTimeout(hideToast, 5000);

// ===== Input Modal =====
function openInputModal() {
    document.getElementById('modalInput').classList.add('active');
}
function closeInputModal() {
    document.getElementById('modalInput').classList.remove('active');
}

// ===== Edit Modal =====
function openEditModal(data) {
    const form = document.getElementById('formEditPelanggaran');
    form.action = '{{ url("guru-bk/pelanggaran") }}/' + data.id;
    document.getElementById('editTanggal').value = data.tanggal || '';
    document.getElementById('editWaktu').value = data.waktu || '';
    document.getElementById('editJenisPelanggaran').value = data.jenis_pelanggaran || '';
    document.getElementById('editJenisLainnyaInput').value = data.jenis_lainnya || '';
    document.getElementById('editDeskripsi').value = data.deskripsi || '';
    document.getElementById('editSanksi').value = data.sanksi || '';
    toggleEditJenisLainnya();
    document.getElementById('modalEdit').classList.add('active');
}
function closeEditModal() {
    document.getElementById('modalEdit').classList.remove('active');
}

// ===== Jenis Lainnya Toggle =====
function toggleJenisLainnya() {
    const val = document.getElementById('jenisPelanggaran').value;
    const group = document.getElementById('jenisLainnyaGroup');
    const input = document.getElementById('jenisLainnyaInput');
    if (val === 'Lainnya') { group.style.display = 'block'; input.required = true; }
    else { group.style.display = 'none'; input.required = false; input.value = ''; }
}
function toggleEditJenisLainnya() {
    const val = document.getElementById('editJenisPelanggaran').value;
    const group = document.getElementById('editJenisLainnyaGroup');
    const input = document.getElementById('editJenisLainnyaInput');
    if (val === 'Lainnya') { group.style.display = 'block'; input.required = true; }
    else { group.style.display = 'none'; input.required = false; }
}

// ===== Siswa Search Modal =====
let selectedSiswa = {};
let siswaDataMap = {};
let searchTimeout;

function openSiswaModal() {
    document.getElementById('modalSiswa').classList.add('active');
    document.getElementById('searchSiswaInput').focus();
}
function closeSiswaModal() {
    document.getElementById('modalSiswa').classList.remove('active');
}

function searchSiswaDebounced() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(searchSiswaAjax, 300);
}

function searchSiswaAjax() {
    const query = document.getElementById('searchSiswaInput').value.trim();
    if (query.length < 2) {
        document.getElementById('siswaSearchResults').innerHTML =
            '<div class="search-placeholder"><i class="fas fa-search"></i><p>Ketik minimal 2 karakter...</p></div>';
        return;
    }

    document.getElementById('siswaSearchResults').innerHTML =
        '<div class="search-placeholder"><i class="fas fa-spinner fa-spin"></i><p>Mencari...</p></div>';

    fetch('{{ route("guru_bk.pelanggaran.search-siswa") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({ q: query })
    })
    .then(r => {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
    })
    .then(data => {
        if (data.length === 0) {
            document.getElementById('siswaSearchResults').innerHTML =
                '<div class="search-placeholder"><i class="fas fa-user-slash"></i><p>Siswa tidak ditemukan</p></div>';
            return;
        }
        let html = '';
        data.forEach(s => {
            siswaDataMap[s.id] = { nama: s.nama, jk: s.jk || '', rombel: s.rombel_aktif || '-' };
            const isSelected = selectedSiswa[s.id] ? 'selected' : '';
            const avatarClass = s.jk === 'Laki-laki' ? 'laki' : 'perempuan';
            const initial = s.nama ? s.nama.charAt(0).toUpperCase() : '?';
            html += '<div class="siswa-result-item ' + isSelected + '" data-id="' + s.id + '" onclick="toggleSiswaById(' + s.id + ')">' +
                '<div class="siswa-avatar ' + avatarClass + '">' + initial + '</div>' +
                '<div class="siswa-result-info">' +
                    '<div class="siswa-nama">' + escapeHtml(s.nama) + '</div>' +
                    '<div class="siswa-meta">' + escapeHtml(s.rombel_aktif || '-') + ' | ' + (s.nisn || s.nis || '-') + '</div>' +
                '</div>' +
                '<div class="check-icon"><i class="fas fa-check"></i></div>' +
            '</div>';
        });
        document.getElementById('siswaSearchResults').innerHTML = html;
    })
    .catch(err => {
        console.error('Search error:', err);
        document.getElementById('siswaSearchResults').innerHTML =
            '<div class="search-placeholder"><i class="fas fa-exclamation-triangle"></i><p>Terjadi kesalahan saat mencari</p></div>';
    });
}

function toggleSiswaById(id) {
    if (selectedSiswa[id]) {
        delete selectedSiswa[id];
    } else {
        const data = siswaDataMap[id];
        if (data) {
            selectedSiswa[id] = { nama: data.nama, jk: data.jk, rombel: data.rombel };
        }
    }
    const items = document.querySelectorAll('.siswa-result-item');
    items.forEach(item => {
        if (parseInt(item.dataset.id) === id) {
            item.classList.toggle('selected');
        }
    });
    updateSelectedCount();
}

function updateSelectedCount() {
    const count = Object.keys(selectedSiswa).length;
    const bar = document.getElementById('selectedCountBar');
    if (count > 0) {
        bar.style.display = 'block';
        document.getElementById('selectedCountText').textContent = count;
    } else {
        bar.style.display = 'none';
    }
}

function confirmSiswaSelection() {
    closeSiswaModal();
    updateSelectedSiswaDisplay();
}

function updateSelectedSiswaDisplay() {
    const container = document.getElementById('selectedSiswaList');
    const ids = Object.keys(selectedSiswa);
    if (ids.length === 0) {
        container.innerHTML = '<p class="no-siswa-msg">Belum ada siswa dipilih</p>';
        return;
    }
    let html = '';
    ids.forEach(id => {
        const s = selectedSiswa[id];
        html += '<div class="selected-siswa-chip">' +
            '<input type="hidden" name="siswa_ids[]" value="' + id + '">' +
            escapeHtml(s.nama) +
            '<button type="button" class="chip-remove" onclick="removeSiswa(' + id + ')">×</button>' +
        '</div>';
    });
    container.innerHTML = html;
}

function removeSiswa(id) {
    delete selectedSiswa[id];
    updateSelectedSiswaDisplay();
    updateSelectedCount();
}

function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// ===== Panggilan Orang Tua Modal =====
let panggilanSiswaData = [];

function openPanggilanModal(siswaList) {
    panggilanSiswaData = siswaList;
    const container = document.getElementById('panggilanSiswaList');
    let html = '';
    siswaList.forEach((s, idx) => {
        const avatarClass = s.jk === 'Laki-laki' ? 'laki' : 'perempuan';
        const initial = s.nama ? s.nama.charAt(0).toUpperCase() : '?';
        html += '<div class="panggilan-siswa-item" onclick="togglePanggilanSiswa(this, ' + idx + ')">' +
            '<input type="checkbox" data-idx="' + idx + '" data-nisn="' + (s.nisn || '') + '" onclick="event.stopPropagation(); updatePanggilanBtn();">' +
            '<div class="siswa-avatar ' + avatarClass + '">' + initial + '</div>' +
            '<div>' +
                '<div class="siswa-nama">' + escapeHtml(s.nama) + '</div>' +
                '<div class="siswa-nisn">NISN: ' + (s.nisn || '-') + '</div>' +
            '</div>' +
        '</div>';
    });
    container.innerHTML = html;
    document.getElementById('panggilanSelectAll').checked = false;
    updatePanggilanBtn();
    document.getElementById('modalPanggilan').classList.add('active');
}

function closePanggilanModal() {
    document.getElementById('modalPanggilan').classList.remove('active');
}

function togglePanggilanSiswa(el, idx) {
    const cb = el.querySelector('input[type="checkbox"]');
    cb.checked = !cb.checked;
    el.classList.toggle('checked', cb.checked);
    updatePanggilanBtn();
}

function toggleSelectAllPanggilan() {
    const checked = document.getElementById('panggilanSelectAll').checked;
    document.querySelectorAll('#panggilanSiswaList input[type="checkbox"]').forEach(cb => {
        cb.checked = checked;
        cb.closest('.panggilan-siswa-item').classList.toggle('checked', checked);
    });
    updatePanggilanBtn();
}

function updatePanggilanBtn() {
    const checked = document.querySelectorAll('#panggilanSiswaList input[type="checkbox"]:checked');
    const btn = document.getElementById('btnBuatPanggilan');
    btn.disabled = checked.length === 0;
    if (checked.length > 0) {
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Buat Surat (' + checked.length + ' siswa)';
    } else {
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Buat Surat Panggilan';
    }
}

function navigatePanggilan() {
    const checked = document.querySelectorAll('#panggilanSiswaList input[type="checkbox"]:checked');
    const nisnList = [];
    checked.forEach(cb => {
        const nisn = cb.dataset.nisn;
        if (nisn) nisnList.push(nisn);
    });
    if (nisnList.length === 0) return;
    // Navigate to first student's create page, pass rest as query params
    const firstNisn = nisnList[0];
    let url = '{{ url("guru-bk/panggilan-ortu/create") }}/' + firstNisn;
    if (nisnList.length > 1) {
        url += '?siswa_lain=' + encodeURIComponent(nisnList.join(','));
    }
    window.location.href = url;
}

// Close modals on overlay click
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modalInput').addEventListener('click', function(e) { if (e.target === this) closeInputModal(); });
    document.getElementById('modalEdit').addEventListener('click', function(e) { if (e.target === this) closeEditModal(); });
    document.getElementById('modalSiswa').addEventListener('click', function(e) { if (e.target === this) closeSiswaModal(); });
    document.getElementById('modalPanggilan').addEventListener('click', function(e) { if (e.target === this) closePanggilanModal(); });
});
</script>
@endsection
