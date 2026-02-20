@extends($layout ?? 'layouts.app')

@section('title', 'Peserta ' . $ajang->nama_ajang . ' | SISMIK')

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

<div class="main-content peserta-ajang-page">
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
        <a href="{{ route('admin.manajemen-talenta.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <button onclick="openModal()" class="btn-add-member">
            <i class="fas fa-user-plus"></i> Tambah Peserta
        </button>
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
                <i class="fas fa-users" style="color: #3b82f6;"></i>
                <h2>Daftar Peserta</h2>
            </div>
            <span class="members-count">{{ count($pesertaList) }} Peserta</span>
        </div>

        @if(count($pesertaList) == 0)
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-users"></i></div>
            <h3>Belum Ada Peserta</h3>
            <p>Klik tombol "Tambah Peserta" untuk menambahkan siswa.</p>
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
                        <div class="member-avatar" style="background: {{ $gender_gradient }};">
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
                            <span class="detail-label"><i class="fas fa-calendar-plus"></i> Terdaftar</span>
                            <span class="detail-value">{{ \Carbon\Carbon::parse($peserta->tanggal_bergabung)->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    <div class="member-actions-row">
                        <form method="POST" action="{{ route('admin.manajemen-talenta.ajang.peserta.hapus', $ajang->id) }}" onsubmit="return confirm('Yakin ingin menghapus peserta ini?')">
                            @csrf
                            <input type="hidden" name="peserta_id" value="{{ $peserta->id }}">
                            <button type="submit" class="btn-delete">
                                <i class="fas fa-trash"></i> Hapus Peserta
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
</div>

{{-- Modal Tambah Peserta --}}
<div id="modalTambah" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> Tambah Peserta</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.manajemen-talenta.ajang.peserta.tambah', $ajang->id) }}" id="formTambah">
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
                    @if(count($siswaAvailable) == 0)
                        <div class="siswa-empty">
                            <i class="fas fa-users"></i>
                            <p>Semua siswa sudah terdaftar sebagai peserta.</p>
                        </div>
                    @else
                        @foreach($siswaAvailable as $siswa)
                        <div class="siswa-item" data-nama="{{ strtolower($siswa->nama) }}" onclick="toggleSiswa(this, {{ $siswa->id }})">
                            <div class="siswa-item-content">
                                <input type="checkbox" name="siswa_ids[]" value="{{ $siswa->id }}" class="checkbox-siswa" onclick="event.stopPropagation()">
                                <div class="siswa-item-info">
                                    <div class="siswa-nama">{{ $siswa->nama }}</div>
                                    <div class="siswa-meta">{{ $siswa->rombel_aktif }} | {{ $siswa->nisn }}</div>
                                </div>
                                <span class="badge-jk-mini {{ ($siswa->jk == 'Laki-laki' || $siswa->jk == 'L') ? 'laki' : 'perempuan' }}">
                                    {{ ($siswa->jk == 'Laki-laki' || $siswa->jk == 'L') ? 'L' : 'P' }}
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
.peserta-ajang-page { padding: 30px; background: #f8f9fa; min-height: calc(100vh - 60px); }

/* Toast */
.toast-notification {
    position: fixed; top: 20px; right: 20px; min-width: 300px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;
    padding: 16px 20px; border-radius: 12px;
    box-shadow: 0 10px 40px rgba(16,185,129,0.4);
    display: flex; align-items: center; justify-content: space-between; gap: 15px;
    z-index: 9999; animation: slideIn 0.3s ease;
}
.toast-notification.toast-error {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    box-shadow: 0 8px 25px rgba(59,130,246,0.4);
}
.page-header-center h1 { font-size: 24px; font-weight: 700; margin: 0 0 5px; color: #1f2937; }
.page-header-center p { color: #6b7280; margin: 0 0 10px; }
.header-badge {
    display: inline-block; padding: 4px 14px; border-radius: 20px;
    background: #eff6ff; color: #2563eb; font-size: 12px; font-weight: 600;
    border: 1px solid #bfdbfe;
}

/* Buttons */
.action-buttons-center { display: flex; justify-content: center; gap: 15px; margin-bottom: 25px; flex-wrap: wrap; }
.btn-back {
    display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px;
    background: white; color: #374151; border: 2px solid #d1d5db;
    border-radius: 10px; text-decoration: none; font-weight: 600; transition: all 0.3s;
}
.btn-back:hover { border-color: #3b82f6; color: #3b82f6; }
.btn-add-member {
    display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;
    border: none; border-radius: 10px; font-weight: 600; cursor: pointer;
    box-shadow: 0 4px 15px rgba(16,185,129,0.35); transition: all 0.3s;
}
.btn-add-member:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(16,185,129,0.4); }

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
.stat-icon.primary { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
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
.members-count { padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; background: rgba(59,130,246,0.1); color: #3b82f6; }

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
.member-card:hover { box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-color: #93c5fd; }
.member-card.expanded { border-color: #3b82f6; }
.member-card-header {
    display: flex; justify-content: space-between; align-items: center;
    padding: 15px; cursor: pointer; transition: all 0.2s; background: rgba(59,130,246,0.04);
}
.member-header-left { display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0; }
.member-avatar {
    width: 45px; height: 45px; border-radius: 50%; overflow: hidden; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    border: 2px solid rgba(255,255,255,0.3); box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
.member-avatar img { width: 100%; height: 100%; object-fit: cover; }
.member-avatar .avatar-initial { color: white; font-weight: 700; font-size: 16px; }
.member-name-info { flex: 1; min-width: 0; }
.member-name-info h4 { margin: 0 0 3px; font-size: 15px; font-weight: 600; color: #1f2937; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.member-name-info .member-rombel { font-size: 12px; color: #6b7280; display: block; }
.expand-icon { transition: transform 0.3s; color: #3b82f6; }
.member-card.expanded .expand-icon { transform: rotate(180deg); }

/* Card Body */
.member-card-body {
    max-height: 0; overflow: hidden; opacity: 0;
    transition: max-height 0.4s ease, opacity 0.3s ease, padding 0.3s ease;
    padding: 0 15px; background: #fafafa;
}
.member-card.expanded .member-card-body { max-height: 500px; opacity: 1; padding: 15px; }
.member-details-grid { display: flex; flex-direction: column; gap: 12px; margin-bottom: 15px; }
.detail-item { display: flex; align-items: center; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
.detail-label { font-size: 11px; color: #6b7280; font-weight: 600; text-transform: uppercase; display: flex; align-items: center; gap: 5px; }
.detail-label i { font-size: 10px; color: #3b82f6; }
.detail-value { font-size: 14px; color: #1f2937; font-weight: 500; }

.badge-jk { padding: 3px 8px; border-radius: 15px; font-size: 11px; font-weight: 600; }
.badge-laki { background: rgba(59,130,246,0.1); color: #3b82f6; }
.badge-perempuan { background: rgba(236,72,153,0.1); color: #ec4899; }

.member-actions-row { padding-top: 15px; border-top: 1px solid #e5e7eb; }
.btn-delete {
    background: rgba(239,68,68,0.1); color: #ef4444; border: none;
    padding: 10px 15px; border-radius: 8px; cursor: pointer;
    display: flex; align-items: center; gap: 6px;
    font-size: 13px; font-weight: 600; transition: all 0.2s;
}
.btn-delete:hover { background: #fee2e2; transform: translateY(-1px); }

/* Modal */
.modal {
    display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);
    z-index: 10000; align-items: center; justify-content: center; padding: 20px;
}
.modal.active { display: flex; }
.modal-content {
    background: white; border-radius: 18px; width: 100%; max-width: 520px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2); animation: modalSlide 0.3s ease;
}
@keyframes modalSlide { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
.modal-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 20px 24px; border-bottom: 1px solid #e5e7eb;
}
.modal-header h3 { margin: 0; font-size: 16px; font-weight: 700; color: #1f2937; display: flex; align-items: center; gap: 10px; }
.modal-header h3 i { color: #3b82f6; }
.modal-close {
    width: 32px; height: 32px; border-radius: 8px; background: #f3f4f6;
    border: none; color: #6b7280; cursor: pointer; font-size: 20px;
    display: flex; align-items: center; justify-content: center; transition: all 0.2s;
}
.modal-close:hover { background: #fee2e2; color: #ef4444; }
.modal-body { padding: 20px 24px; }
.search-box { position: relative; margin-bottom: 15px; }
.search-input {
    width: 100%; padding: 12px 15px 12px 40px; border: 1px solid #e5e7eb;
    border-radius: 10px; font-size: 14px; font-family: 'Poppins', sans-serif;
    transition: all 0.2s; background: #f8fafc;
}
.search-input:focus { outline: none; border-color: #93c5fd; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
.search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9ca3af; }
.selected-preview {
    padding: 10px 15px; background: #eff6ff; border-radius: 8px;
    margin-bottom: 15px; font-size: 13px; color: #2563eb;
}
.siswa-list { max-height: 350px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 10px; }
.siswa-empty { padding: 40px 20px; text-align: center; color: #9ca3af; }
.siswa-empty i { font-size: 30px; margin-bottom: 10px; display: block; }
.siswa-item {
    padding: 12px 15px; border-bottom: 1px solid #f3f4f6;
    cursor: pointer; transition: background 0.2s;
}
.siswa-item:hover { background: #f8fafc; }
.siswa-item.selected { background: #eff6ff; border-left: 3px solid #3b82f6; }
.siswa-item-content { display: flex; align-items: center; gap: 12px; }
.checkbox-siswa { width: 18px; height: 18px; accent-color: #3b82f6; cursor: pointer; }
.siswa-item-info { flex: 1; }
.siswa-nama { font-size: 14px; font-weight: 600; color: #1f2937; }
.siswa-meta { font-size: 11px; color: #6b7280; margin-top: 2px; }
.badge-jk-mini {
    width: 24px; height: 24px; border-radius: 50%; display: flex;
    align-items: center; justify-content: center; font-size: 11px; font-weight: 700;
}
.badge-jk-mini.laki { background: rgba(59,130,246,0.1); color: #3b82f6; }
.badge-jk-mini.perempuan { background: rgba(236,72,153,0.1); color: #ec4899; }

.modal-actions {
    padding: 16px 24px; border-top: 1px solid #e5e7eb;
    display: flex; justify-content: flex-end; gap: 10px;
}
.btn { padding: 10px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; font-family: 'Poppins', sans-serif; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
.btn-secondary { background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; }
.btn-secondary:hover { background: #e5e7eb; }
.btn-primary { background: #3b82f6; color: white; }
.btn-primary:hover { background: #2563eb; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(59,130,246,0.3); }

/* Responsive */
@media (max-width: 768px) {
    .peserta-ajang-page { padding: 15px; }
    .stats-grid { grid-template-columns: 1fr; }
    .members-cards-grid { grid-template-columns: 1fr; }
}
</style>

<script>
function hideToast() {
    const t = document.getElementById('toastNotification');
    if (t) { t.style.animation = 'slideIn 0.3s ease reverse'; setTimeout(() => t.remove(), 300); }
}
setTimeout(() => { const t = document.getElementById('toastNotification'); if (t) hideToast(); }, 4000);

function toggleCard(cardId) {
    const card = document.querySelector(`[data-card-id="${cardId}"]`);
    if (card) card.classList.toggle('expanded');
}

function openModal() { document.getElementById('modalTambah').classList.add('active'); }
function closeModal() { document.getElementById('modalTambah').classList.remove('active'); }

function filterSiswa() {
    const q = document.getElementById('searchSiswa').value.toLowerCase();
    document.querySelectorAll('.siswa-item').forEach(item => {
        item.style.display = item.dataset.nama.includes(q) ? '' : 'none';
    });
}

function toggleSiswa(el, id) {
    const cb = el.querySelector('.checkbox-siswa');
    cb.checked = !cb.checked;
    el.classList.toggle('selected', cb.checked);
    updateSelectedCount();
}

function updateSelectedCount() {
    const count = document.querySelectorAll('.checkbox-siswa:checked').length;
    const preview = document.getElementById('selectedPreview');
    document.getElementById('selectedCount').textContent = count;
    preview.style.display = count > 0 ? 'block' : 'none';
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
</script>
@endsection
