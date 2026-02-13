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
        <p>Pencatatan Pelanggaran Siswa</p>
    </div>

    {{-- Action Buttons --}}
    <div class="action-buttons-center">
        <form method="GET" action="{{ route('guru_bk.pelanggaran') }}" class="filter-form">
            <div class="date-filter-group">
                <label><i class="fas fa-calendar-alt"></i> Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()" class="date-input">
            </div>
        </form>
        <button onclick="openInputModal()" class="btn-add">
            <i class="fas fa-plus-circle"></i> Input Pelanggaran
        </button>
    </div>

    {{-- Stats --}}
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
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                @php
                    $totalSiswa = 0;
                    foreach($pelanggaranList as $p) { $totalSiswa += $p->siswa->count(); }
                @endphp
                <h3>{{ $totalSiswa }}</h3>
                <p>Siswa Terlibat</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-info">
                <h3>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d M Y') }}</h3>
                <p>Tanggal Dipilih</p>
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
                            <i class="fas fa-clock"></i> {{ $item->waktu ? \Carbon\Carbon::parse($item->waktu)->format('H:i') : '-' }}
                        </span>
                    </div>
                    <div class="card-actions">
                        <a href="{{ route('guru_bk.panggilan-ortu.create', ['nisn' => $item->siswa->first()->nisn ?? '']) }}" class="btn-icon btn-phone" title="Panggilan Orang Tua">
                            <i class="fas fa-phone"></i>
                        </a>
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
    position: fixed; top: 20px; right: 20px; min-width: 300px;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white; padding: 16px 20px; border-radius: 12px;
    box-shadow: 0 10px 40px rgba(16,185,129,0.4);
    display: flex; align-items: center; justify-content: space-between; gap: 15px;
    z-index: 9999; animation: slideIn 0.3s ease;
}
.toast-notification.toast-error {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    box-shadow: 0 10px 40px rgba(239,68,68,0.4);
}
@keyframes slideIn { from { transform: translateX(120%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
.toast-content { display: flex; align-items: center; gap: 12px; }
.toast-content i { font-size: 20px; }
.toast-close {
    background: rgba(255,255,255,0.2); border: none; color: white;
    width: 28px; height: 28px; border-radius: 50%; cursor: pointer;
    display: flex; align-items: center; justify-content: center; font-size: 18px;
}

/* Header */
.page-header-center { text-align: center; margin-bottom: 25px; }
.header-icon-large {
    width: 80px; height: 80px; border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    font-size: 36px; color: white; margin: 0 auto 20px;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    box-shadow: 0 8px 25px rgba(239,68,68,0.4);
}
.page-header-center h1 { font-size: 28px; font-weight: 700; margin: 0 0 5px; color: #1f2937; }
.page-header-center p { color: #6b7280; margin: 0; }

/* Action Buttons */
.action-buttons-center {
    display: flex; justify-content: center; gap: 15px; margin-bottom: 25px; flex-wrap: wrap; align-items: flex-end;
}
.filter-form { display: flex; align-items: flex-end; }
.date-filter-group label {
    display: block; font-size: 12px; font-weight: 600; color: #6b7280; margin-bottom: 6px;
}
.date-filter-group label i { margin-right: 4px; }
.date-input {
    padding: 12px 16px; border: 2px solid #d1d5db; border-radius: 10px;
    font-size: 14px; font-weight: 500; font-family: 'Poppins', sans-serif;
    background: white; cursor: pointer; transition: all 0.3s;
}
.date-input:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
.btn-add {
    display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px;
    background: linear-gradient(135deg, #ef4444, #dc2626); color: white;
    border: none; border-radius: 10px; font-weight: 600; cursor: pointer;
    box-shadow: 0 4px 15px rgba(239,68,68,0.35); transition: all 0.3s; font-family: 'Poppins', sans-serif;
}
.btn-add:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(239,68,68,0.4); }

/* Stats */
.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 25px; }
.stat-card {
    background: white; padding: 20px; border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 15px; border: 1px solid #e5e7eb;
}
.stat-icon {
    width: 50px; height: 50px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; font-size: 20px; color: white;
}
.stat-icon.danger { background: linear-gradient(135deg, #ef4444, #dc2626); }
.stat-icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
.stat-icon.primary { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.stat-info h3 { margin: 0; font-size: 20px; font-weight: 700; color: #1f2937; }
.stat-info p { margin: 4px 0 0 0; color: #6b7280; font-size: 12px; }

/* Pelanggaran Container */
.pelanggaran-container {
    background: white; border-radius: 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow: hidden;
}
.container-header {
    padding: 20px 25px; border-bottom: 1px solid #e5e7eb;
    display: flex; justify-content: space-between; align-items: center;
}
.container-title { display: flex; align-items: center; gap: 10px; }
.container-title i { color: var(--danger); font-size: 18px; }
.container-title h2 { margin: 0; font-size: 1.1rem; color: #1f2937; }
.count-badge {
    padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;
    background: rgba(239,68,68,0.1); color: #ef4444;
}

/* Empty State */
.empty-state { padding: 60px 30px; text-align: center; }
.empty-icon {
    width: 80px; height: 80px; background: #f0fdf4; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;
}
.empty-icon i { font-size: 30px; color: #10b981; }
.empty-state h3 { margin: 0 0 10px; color: #1f2937; }
.empty-state p { margin: 0; color: #6b7280; }

/* Pelanggaran Cards */
.pelanggaran-cards { padding: 20px; display: flex; flex-direction: column; gap: 16px; }

.pelanggaran-card {
    background: white; border: 1px solid #e5e7eb; border-radius: 14px;
    padding: 20px; transition: all 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.pelanggaran-card:hover { box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-color: #ef4444; }

.card-header-row {
    display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;
}
.card-header-left { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }

.jenis-badge {
    display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px;
    border-radius: 8px; font-size: 13px; font-weight: 600;
    background: rgba(239,68,68,0.1); color: #ef4444;
}
.jenis-badge.keterlambatan { background: rgba(245,158,11,0.1); color: #d97706; }
.jenis-badge.kriminal { background: rgba(139,92,246,0.1); color: #7c3aed; }
.jenis-badge.perusakan-fasilitas-sekolah { background: rgba(59,130,246,0.1); color: #2563eb; }
.jenis-badge.penyebaran-konten-hoax { background: rgba(249,115,22,0.1); color: #ea580c; }
.jenis-badge.penyebaran-konten-asusila { background: rgba(236,72,153,0.1); color: #db2777; }

.waktu-badge {
    font-size: 12px; color: #6b7280; display: flex; align-items: center; gap: 4px;
}
.card-actions { display: flex; gap: 8px; }
.btn-icon {
    width: 36px; height: 36px; border-radius: 8px; border: none;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all 0.2s; font-size: 14px;
}
.btn-phone {
    background: rgba(59,130,246,0.1); color: #3b82f6; text-decoration: none;
}
.btn-phone:hover { background: #3b82f6; color: white; }
.btn-trash { background: rgba(239,68,68,0.1); color: #ef4444; }
.btn-trash:hover { background: #ef4444; color: white; }

/* Siswa Tags */
.siswa-involved { margin-bottom: 12px; }
.siswa-label { font-size: 12px; font-weight: 600; color: #6b7280; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
.siswa-tags { display: flex; flex-wrap: wrap; gap: 8px; }
.siswa-tag {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 12px; background: #f3f4f6; border-radius: 20px;
    font-size: 13px; font-weight: 500; color: #374151;
}
.siswa-initial {
    width: 22px; height: 22px; border-radius: 50%; display: flex;
    align-items: center; justify-content: center; font-size: 10px;
    font-weight: 700; color: white; flex-shrink: 0;
}
.siswa-initial.laki { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.siswa-initial.perempuan { background: linear-gradient(135deg, #ec4899, #db2777); }

.detail-row { margin-bottom: 8px; }
.detail-label { font-size: 12px; font-weight: 600; color: #6b7280; display: flex; align-items: center; gap: 6px; margin-bottom: 4px; }
.detail-text { margin: 0; font-size: 14px; color: #374151; line-height: 1.5; }

/* ===================== MODALS ===================== */
.modal-overlay {
    display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);
    z-index: 10000; align-items: center; justify-content: center;
}
.modal-overlay.active { display: flex; }

.modal-container {
    background: white; border-radius: 16px; width: 560px; max-width: 95%;
    max-height: 90vh; display: flex; flex-direction: column;
    box-shadow: 0 25px 60px rgba(0,0,0,0.3); animation: modalIn 0.3s ease;
}
@keyframes modalIn {
    from { transform: scale(0.9) translateY(20px); opacity: 0; }
    to { transform: scale(1) translateY(0); opacity: 1; }
}

.modal-header {
    padding: 20px 24px; border-bottom: 1px solid #e5e7eb;
    display: flex; justify-content: space-between; align-items: center;
}
.modal-header h3 { margin: 0; font-size: 18px; font-weight: 700; color: #1f2937; display: flex; align-items: center; gap: 8px; }
.modal-header h3 i { color: #ef4444; }
.modal-header-siswa h3 i { color: #3b82f6; }
.modal-close {
    width: 32px; height: 32px; border: none; background: #f3f4f6;
    border-radius: 8px; font-size: 20px; cursor: pointer; color: #6b7280;
    display: flex; align-items: center; justify-content: center; transition: all 0.2s;
}
.modal-close:hover { background: #ef4444; color: white; }

.modal-body { padding: 24px; overflow-y: auto; flex: 1; }

.modal-footer {
    padding: 16px 24px; border-top: 1px solid #e5e7eb;
    display: flex; justify-content: flex-end; gap: 10px;
}

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-group { margin-bottom: 16px; }
.form-group label {
    display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;
}
.form-group label i { margin-right: 4px; color: #6b7280; }
.form-control {
    width: 100%; padding: 10px 14px; border: 2px solid #e5e7eb; border-radius: 10px;
    font-size: 14px; font-family: 'Poppins', sans-serif; transition: all 0.2s;
    box-sizing: border-box;
}
.form-control:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
select.form-control { cursor: pointer; appearance: auto; }
textarea.form-control { resize: vertical; }

.btn-add-siswa {
    display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white;
    border: none; border-radius: 10px; font-weight: 600; cursor: pointer;
    font-family: 'Poppins', sans-serif; transition: all 0.3s; font-size: 13px;
    box-shadow: 0 4px 12px rgba(59,130,246,0.3);
}
.btn-add-siswa:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(59,130,246,0.4); }

.selected-siswa-list { margin-top: 10px; display: flex; flex-wrap: wrap; gap: 8px; }
.no-siswa-msg { font-size: 13px; color: #9ca3af; margin: 0; }

.selected-siswa-chip {
    display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px;
    background: linear-gradient(135deg, #eff6ff, #dbeafe); border: 1px solid #93c5fd;
    border-radius: 20px; font-size: 13px; font-weight: 500; color: #1d4ed8;
}
.selected-siswa-chip .chip-remove {
    width: 18px; height: 18px; border-radius: 50%; background: rgba(239,68,68,0.2);
    border: none; color: #ef4444; cursor: pointer; font-size: 12px;
    display: flex; align-items: center; justify-content: center; transition: all 0.2s;
}
.selected-siswa-chip .chip-remove:hover { background: #ef4444; color: white; }

/* Siswa Search Modal */
.modal-siswa-overlay { z-index: 10001; }
.modal-siswa-container { width: 500px; }

.search-box { position: relative; margin-bottom: 16px; }
.search-input {
    width: 100%; padding: 12px 16px 12px 42px; border: 2px solid #e5e7eb;
    border-radius: 12px; font-size: 14px; font-family: 'Poppins', sans-serif;
    transition: all 0.2s; box-sizing: border-box;
}
.search-input:focus { border-color: #3b82f6; outline: none; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
.search-icon-abs { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9ca3af; }

.selected-count-bar {
    background: rgba(59,130,246,0.1); color: #1d4ed8; padding: 8px 14px;
    border-radius: 8px; font-size: 13px; margin-bottom: 12px;
}

.siswa-search-results { max-height: 350px; overflow-y: auto; }

.search-placeholder { padding: 40px 20px; text-align: center; color: #9ca3af; }
.search-placeholder i { font-size: 30px; margin-bottom: 10px; display: block; }

.siswa-result-item {
    display: flex; align-items: center; gap: 12px; padding: 12px;
    border-radius: 10px; cursor: pointer; transition: all 0.2s;
    border: 2px solid transparent; margin-bottom: 6px;
}
.siswa-result-item:hover { background: #f8fafc; border-color: #e2e8f0; }
.siswa-result-item.selected { background: #eff6ff; border-color: #3b82f6; }
.siswa-result-item .siswa-avatar {
    width: 40px; height: 40px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 14px; color: white; flex-shrink: 0;
}
.siswa-result-item .siswa-avatar.laki { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.siswa-result-item .siswa-avatar.perempuan { background: linear-gradient(135deg, #ec4899, #db2777); }
.siswa-result-info { flex: 1; min-width: 0; }
.siswa-result-info .siswa-nama { font-weight: 600; font-size: 14px; color: #1f2937; }
.siswa-result-info .siswa-meta { font-size: 12px; color: #6b7280; }
.siswa-result-item .check-icon {
    width: 24px; height: 24px; border: 2px solid #d1d5db; border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; color: transparent; transition: all 0.2s;
}
.siswa-result-item.selected .check-icon {
    background: #3b82f6; border-color: #3b82f6; color: white;
}

.btn-secondary {
    padding: 10px 20px; background: #f3f4f6; color: #374151;
    border: 1px solid #d1d5db; border-radius: 10px; font-weight: 600;
    cursor: pointer; font-family: 'Poppins', sans-serif; transition: all 0.2s;
}
.btn-secondary:hover { background: #e5e7eb; }
.btn-primary {
    padding: 10px 20px; background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white; border: none; border-radius: 10px; font-weight: 600;
    cursor: pointer; font-family: 'Poppins', sans-serif; transition: all 0.3s;
    display: flex; align-items: center; gap: 6px;
    box-shadow: 0 4px 12px rgba(239,68,68,0.3);
}
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(239,68,68,0.4); }

/* Responsive */
@media (max-width: 768px) {
    .pelanggaran-page { padding: 15px; }
    .stats-grid { grid-template-columns: 1fr; }
    .form-row { grid-template-columns: 1fr; }
    .modal-container { width: 100%; max-width: 95%; }
    .card-header-row { flex-direction: column; align-items: flex-start; gap: 10px; }
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

// ===== Jenis Lainnya Toggle =====
function toggleJenisLainnya() {
    const val = document.getElementById('jenisPelanggaran').value;
    const group = document.getElementById('jenisLainnyaGroup');
    const input = document.getElementById('jenisLainnyaInput');
    if (val === 'Lainnya') {
        group.style.display = 'block';
        input.required = true;
    } else {
        group.style.display = 'none';
        input.required = false;
        input.value = '';
    }
}

// ===== Siswa Search Modal =====
let selectedSiswa = {}; // { id: { nama, jk, rombel } }
let searchTimeout;

function openSiswaModal() {
    document.getElementById('modalSiswa').classList.add('active');
    document.getElementById('searchSiswaInput').focus();
    updateSearchResults();
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
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({ q: query })
    })
    .then(r => r.json())
    .then(data => {
        if (data.length === 0) {
            document.getElementById('siswaSearchResults').innerHTML =
                '<div class="search-placeholder"><i class="fas fa-user-slash"></i><p>Siswa tidak ditemukan</p></div>';
            return;
        }
        let html = '';
        data.forEach(s => {
            const isSelected = selectedSiswa[s.id] ? 'selected' : '';
            const avatarClass = s.jk === 'Laki-laki' ? 'laki' : 'perempuan';
            html += `
            <div class="siswa-result-item ${isSelected}" data-id="${s.id}" onclick="toggleSiswaSelect(${s.id}, '${escapeHtml(s.nama)}', '${s.jk || ''}', '${escapeHtml(s.rombel_aktif || '-')}')">
                <div class="siswa-avatar ${avatarClass}">${s.nama.charAt(0).toUpperCase()}</div>
                <div class="siswa-result-info">
                    <div class="siswa-nama">${escapeHtml(s.nama)}</div>
                    <div class="siswa-meta">${escapeHtml(s.rombel_aktif || '-')} | ${s.nisn || s.nis || '-'}</div>
                </div>
                <div class="check-icon"><i class="fas fa-check"></i></div>
            </div>`;
        });
        document.getElementById('siswaSearchResults').innerHTML = html;
    })
    .catch(() => {
        document.getElementById('siswaSearchResults').innerHTML =
            '<div class="search-placeholder"><i class="fas fa-exclamation-triangle"></i><p>Terjadi kesalahan</p></div>';
    });
}

function toggleSiswaSelect(id, nama, jk, rombel) {
    if (selectedSiswa[id]) {
        delete selectedSiswa[id];
    } else {
        selectedSiswa[id] = { nama, jk, rombel };
    }
    // Update visual
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
        container.innerHTML = '<p class="no-siswa-msg" id="noSiswaMsg">Belum ada siswa dipilih</p>';
        return;
    }
    let html = '';
    ids.forEach(id => {
        const s = selectedSiswa[id];
        html += `
        <div class="selected-siswa-chip">
            <input type="hidden" name="siswa_ids[]" value="${id}">
            ${escapeHtml(s.nama)}
            <button type="button" class="chip-remove" onclick="removeSiswa(${id})">×</button>
        </div>`;
    });
    container.innerHTML = html;
}

function removeSiswa(id) {
    delete selectedSiswa[id];
    updateSelectedSiswaDisplay();
    updateSelectedCount();
}

function updateSearchResults() {
    // Re-highlight selected items from stored state
    const items = document.querySelectorAll('.siswa-result-item');
    items.forEach(item => {
        const id = parseInt(item.dataset.id);
        if (selectedSiswa[id]) {
            item.classList.add('selected');
        } else {
            item.classList.remove('selected');
        }
    });
}

function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// Close modals on overlay click
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modalInput').addEventListener('click', function(e) {
        if (e.target === this) closeInputModal();
    });
    document.getElementById('modalSiswa').addEventListener('click', function(e) {
        if (e.target === this) closeSiswaModal();
    });
});
</script>
@endsection
