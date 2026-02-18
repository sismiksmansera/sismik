@extends($layout ?? 'layouts.app')

@section('title', 'Tugas Tambahan Lainnya | SISMIK')

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        {{-- Header --}}
        <div class="tt-header">
            <div class="tt-header-content">
                <div class="tt-header-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="tt-header-text">
                    <h1>Tugas Tambahan Lainnya</h1>
                    <p>Kelola jenis tugas tambahan guru di luar KBM</p>
                </div>
            </div>
        </div>

        {{-- Action Button --}}
        <div class="tt-actions">
            <button onclick="openJenisModal()" class="btn-jenis-tugas">
                <i class="fas fa-plus-circle"></i> Jenis Tugas Tambahan Lainnya
            </button>
        </div>

        {{-- Placeholder Content --}}
        <div class="tt-content-section">
            <div class="tt-empty-state">
                <div class="tt-empty-icon">
                    <i class="fas fa-hard-hat"></i>
                </div>
                <h3>Halaman Dalam Pengembangan</h3>
                <p>Fitur pengelolaan tugas tambahan lainnya akan segera tersedia.</p>
            </div>
        </div>
    </div>
</div>

{{-- Modal Jenis Tugas Tambahan --}}
<div id="jenisModal" class="tt-modal">
    <div class="tt-modal-content">
        <div class="tt-modal-header">
            <h3><i class="fas fa-list-alt"></i> Jenis Tugas Tambahan Lainnya</h3>
            <button class="tt-modal-close" onclick="closeJenisModal()">&times;</button>
        </div>
        <div class="tt-modal-body">
            {{-- Input Form --}}
            <div class="tt-input-section">
                <div class="tt-input-group">
                    <label for="inputNamaTugas"><i class="fas fa-tag"></i> Nama Jenis Tugas</label>
                    <input type="text" id="inputNamaTugas" placeholder="Contoh: Wali Kelas, Pembina OSIS..." maxlength="255">
                </div>
                <div class="tt-input-group">
                    <label for="inputDeskripsi"><i class="fas fa-align-left"></i> Deskripsi <span class="optional">(opsional)</span></label>
                    <textarea id="inputDeskripsi" placeholder="Deskripsi singkat tugas..." rows="2" maxlength="500"></textarea>
                </div>
                <button type="button" id="btnSimpanJenis" onclick="simpanJenis()">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>

            {{-- Divider --}}
            <div class="tt-divider">
                <span><i class="fas fa-list"></i> Daftar Jenis Tugas Tambahan</span>
            </div>

            {{-- List --}}
            <div class="tt-jenis-list" id="jenisListContainer">
                @if(count($jenisList) == 0)
                    <div class="tt-jenis-empty" id="jenisEmptyState">
                        <i class="fas fa-inbox"></i>
                        <p>Belum ada jenis tugas tambahan.</p>
                    </div>
                @else
                    @foreach($jenisList as $jenis)
                    <div class="tt-jenis-item" data-id="{{ $jenis->id }}">
                        <div class="tt-jenis-info">
                            <div class="tt-jenis-nama">{{ $jenis->nama_tugas }}</div>
                            @if($jenis->deskripsi)
                                <div class="tt-jenis-desc">{{ $jenis->deskripsi }}</div>
                            @endif
                        </div>
                        <div class="tt-jenis-actions">
                            <button class="tt-jenis-edit" onclick="editJenis({{ $jenis->id }}, '{{ addslashes($jenis->nama_tugas) }}', '{{ addslashes($jenis->deskripsi ?? '') }}')" title="Edit">
                                <i class="fas fa-pen"></i>
                            </button>
                            <button class="tt-jenis-delete" onclick="hapusJenis({{ $jenis->id }}, '{{ addslashes($jenis->nama_tugas) }}')" title="Hapus">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Toast --}}
<div id="ttToast" class="tt-toast"></div>

<style>
/* ===== PAGE LAYOUT ===== */
.tt-header {
    background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
    border-radius: 16px;
    padding: 1.5rem 2rem;
    margin-bottom: 1.5rem;
    color: white;
    box-shadow: 0 10px 25px rgba(139, 92, 246, 0.3);
}

.tt-header-content {
    display: flex;
    align-items: center;
    gap: 1.25rem;
}

.tt-header-icon {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.2);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.tt-header-text h1 {
    margin: 0 0 0.25rem 0;
    font-size: 1.6rem;
    font-weight: 700;
}

.tt-header-text p {
    margin: 0;
    font-size: 0.85rem;
    opacity: 0.9;
}

/* ===== ACTIONS ===== */
.tt-actions {
    margin-bottom: 1.5rem;
}

.btn-jenis-tugas {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 28px;
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.35);
    transition: all 0.3s ease;
}

.btn-jenis-tugas:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(139, 92, 246, 0.45);
}

.btn-jenis-tugas i {
    font-size: 17px;
}

/* ===== CONTENT SECTION ===== */
.tt-content-section {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.07);
}

.tt-empty-state {
    text-align: center;
    padding: 60px 20px;
}

.tt-empty-icon {
    width: 90px;
    height: 90px;
    background: linear-gradient(135deg, #f3e8ff, #ede9fe);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.tt-empty-icon i {
    font-size: 36px;
    color: #8b5cf6;
}

.tt-empty-state h3 {
    margin: 0 0 10px;
    color: #374151;
    font-size: 1.2rem;
}

.tt-empty-state p {
    color: #6b7280;
    margin: 0;
}

/* ===== MODAL ===== */
.tt-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(4px);
    z-index: 10000;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.tt-modal.active {
    display: flex;
    animation: ttFadeIn 0.25s ease;
}

@keyframes ttFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.tt-modal-content {
    background: white;
    border-radius: 16px;
    width: 100%;
    max-width: 560px;
    max-height: 85vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 25px 60px rgba(0,0,0,0.25);
    animation: ttSlideUp 0.3s ease;
}

@keyframes ttSlideUp {
    from { transform: translateY(30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.tt-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    border-radius: 16px 16px 0 0;
    color: white;
}

.tt-modal-header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
}

.tt-modal-close {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    font-size: 22px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.tt-modal-close:hover {
    background: rgba(255,255,255,0.35);
}

.tt-modal-body {
    padding: 24px;
    overflow-y: auto;
    flex: 1;
}

/* ===== INPUT SECTION ===== */
.tt-input-section {
    background: #f9fafb;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #e5e7eb;
}

.tt-input-group {
    margin-bottom: 14px;
}

.tt-input-group label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
}

.tt-input-group label i {
    color: #8b5cf6;
    margin-right: 4px;
}

.tt-input-group .optional {
    font-weight: 400;
    color: #9ca3af;
    font-size: 11px;
}

.tt-input-group input,
.tt-input-group textarea {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid #d1d5db;
    border-radius: 10px;
    font-size: 14px;
    font-family: inherit;
    transition: all 0.2s;
    background: white;
    box-sizing: border-box;
}

.tt-input-group input:focus,
.tt-input-group textarea:focus {
    border-color: #8b5cf6;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.12);
    outline: none;
}

.tt-input-group textarea {
    resize: vertical;
    min-height: 60px;
}

#btnSimpanJenis {
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s ease;
    box-shadow: 0 3px 10px rgba(139, 92, 246, 0.3);
}

#btnSimpanJenis:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(139, 92, 246, 0.4);
}

#btnSimpanJenis:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

/* ===== DIVIDER ===== */
.tt-divider {
    display: flex;
    align-items: center;
    margin: 24px 0 16px;
}

.tt-divider::before,
.tt-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #e5e7eb;
}

.tt-divider span {
    padding: 0 14px;
    font-size: 12px;
    font-weight: 700;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
}

.tt-divider span i {
    margin-right: 5px;
    color: #8b5cf6;
}

/* ===== JENIS LIST ===== */
.tt-jenis-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    max-height: 280px;
    overflow-y: auto;
}

.tt-jenis-list::-webkit-scrollbar {
    width: 5px;
}

.tt-jenis-list::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.tt-jenis-list::-webkit-scrollbar-thumb {
    background: #c4b5fd;
    border-radius: 10px;
}

.tt-jenis-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    transition: all 0.2s ease;
}

.tt-jenis-item:hover {
    border-color: #c4b5fd;
    box-shadow: 0 2px 8px rgba(139, 92, 246, 0.1);
}

.tt-jenis-info {
    flex: 1;
    min-width: 0;
}

.tt-jenis-nama {
    font-size: 14px;
    font-weight: 600;
    color: #1f2937;
}

.tt-jenis-desc {
    font-size: 12px;
    color: #6b7280;
    margin-top: 3px;
    line-height: 1.4;
}

.tt-jenis-actions {
    display: flex;
    gap: 6px;
    flex-shrink: 0;
    margin-left: 10px;
}

.tt-jenis-edit,
.tt-jenis-delete {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    transition: all 0.2s;
}

.tt-jenis-edit {
    background: rgba(139, 92, 246, 0.08);
    color: #8b5cf6;
}

.tt-jenis-edit:hover {
    background: rgba(139, 92, 246, 0.18);
    transform: scale(1.1);
}

.tt-jenis-delete {
    background: rgba(239, 68, 68, 0.08);
    color: #ef4444;
}

.tt-jenis-delete:hover {
    background: rgba(239, 68, 68, 0.15);
    transform: scale(1.1);
}

/* ===== INLINE EDIT MODE ===== */
.tt-jenis-item.editing {
    border-color: #8b5cf6;
    box-shadow: 0 2px 12px rgba(139, 92, 246, 0.15);
    background: #faf5ff;
}

.tt-edit-form {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
    min-width: 0;
}

.tt-edit-form input,
.tt-edit-form textarea {
    width: 100%;
    padding: 8px 12px;
    border: 1.5px solid #d1d5db;
    border-radius: 8px;
    font-size: 13px;
    font-family: inherit;
    box-sizing: border-box;
    transition: all 0.2s;
}

.tt-edit-form input:focus,
.tt-edit-form textarea:focus {
    border-color: #8b5cf6;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
    outline: none;
}

.tt-edit-form textarea {
    resize: vertical;
    min-height: 40px;
}

.tt-edit-actions {
    display: flex;
    gap: 6px;
    flex-shrink: 0;
    margin-left: 10px;
    align-self: flex-start;
}

.tt-edit-save,
.tt-edit-cancel {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    transition: all 0.2s;
}

.tt-edit-save {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.tt-edit-save:hover {
    background: rgba(16, 185, 129, 0.2);
    transform: scale(1.1);
}

.tt-edit-cancel {
    background: rgba(107, 114, 128, 0.1);
    color: #6b7280;
}

.tt-edit-cancel:hover {
    background: rgba(107, 114, 128, 0.2);
    transform: scale(1.1);
}

/* ===== EMPTY STATE (JENIS) ===== */
.tt-jenis-empty {
    text-align: center;
    padding: 30px 20px;
    color: #9ca3af;
}

.tt-jenis-empty i {
    font-size: 28px;
    margin-bottom: 8px;
    display: block;
}

.tt-jenis-empty p {
    margin: 0;
    font-size: 13px;
}

/* ===== TOAST ===== */
.tt-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 280px;
    padding: 16px 20px;
    border-radius: 12px;
    color: white;
    font-size: 14px;
    font-weight: 600;
    display: none;
    align-items: center;
    gap: 10px;
    z-index: 20000;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    animation: ttSlideInRight 0.3s ease;
}

@keyframes ttSlideInRight {
    from { transform: translateX(120%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

.tt-toast.success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.tt-toast.error {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.tt-toast.show {
    display: flex;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .tt-header {
        padding: 1.25rem;
    }

    .tt-header-text h1 {
        font-size: 1.3rem;
    }

    .tt-header-icon {
        width: 48px;
        height: 48px;
        font-size: 1.2rem;
    }

    .tt-modal-content {
        max-height: 90vh;
    }

    .tt-modal-body {
        padding: 16px;
    }
}
</style>

<script>
/* ===== MODAL ===== */
function openJenisModal() {
    document.getElementById('jenisModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeJenisModal() {
    document.getElementById('jenisModal').classList.remove('active');
    document.body.style.overflow = '';
}

// Close on backdrop click
document.getElementById('jenisModal').addEventListener('click', function(e) {
    if (e.target === this) closeJenisModal();
});

/* ===== TOAST ===== */
function showToast(message, type = 'success') {
    const toast = document.getElementById('ttToast');
    toast.innerHTML = '<i class="fas ' + (type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle') + '"></i> ' + message;
    toast.className = 'tt-toast ' + type + ' show';
    setTimeout(() => { toast.classList.remove('show'); }, 3000);
}

/* ===== SIMPAN JENIS ===== */
function simpanJenis() {
    const namaTugas = document.getElementById('inputNamaTugas').value.trim();
    const deskripsi = document.getElementById('inputDeskripsi').value.trim();

    if (!namaTugas) {
        showToast('Nama jenis tugas tidak boleh kosong!', 'error');
        document.getElementById('inputNamaTugas').focus();
        return;
    }

    const btn = document.getElementById('btnSimpanJenis');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    fetch('{{ route("admin.tugas-tambahan.jenis.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ nama_tugas: namaTugas, deskripsi: deskripsi })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            document.getElementById('inputNamaTugas').value = '';
            document.getElementById('inputDeskripsi').value = '';
            renderJenisList(data.data);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(err => {
        showToast('Terjadi kesalahan, coba lagi!', 'error');
        console.error(err);
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
    });
}

/* ===== HAPUS JENIS ===== */
function hapusJenis(id, nama) {
    if (!confirm('Yakin ingin menghapus "' + nama + '"?')) return;

    fetch('{{ route("admin.tugas-tambahan.jenis.delete") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ id: id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            renderJenisList(data.data);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(err => {
        showToast('Terjadi kesalahan, coba lagi!', 'error');
        console.error(err);
    });
}

/* ===== EDIT JENIS ===== */
function editJenis(id, nama, deskripsi) {
    const item = document.querySelector('.tt-jenis-item[data-id="' + id + '"]');
    if (!item || item.classList.contains('editing')) return;

    item.classList.add('editing');
    item.innerHTML = 
        '<div class="tt-edit-form">' +
        '  <input type="text" class="edit-nama" value="' + escapeAttr(nama) + '" placeholder="Nama tugas..." maxlength="255">' +
        '  <textarea class="edit-desc" placeholder="Deskripsi (opsional)..." rows="2" maxlength="500">' + escapeHtml(deskripsi) + '</textarea>' +
        '</div>' +
        '<div class="tt-edit-actions">' +
        '  <button class="tt-edit-save" onclick="updateJenis(' + id + ')" title="Simpan"><i class="fas fa-check"></i></button>' +
        '  <button class="tt-edit-cancel" onclick="cancelEdit(' + id + ')" title="Batal"><i class="fas fa-times"></i></button>' +
        '</div>';

    item.querySelector('.edit-nama').focus();
    item.querySelector('.edit-nama').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); updateJenis(id); }
        if (e.key === 'Escape') cancelEdit(id);
    });
}

function cancelEdit(id) {
    // Re-render the whole list from the last known data
    location.reload();
}

function updateJenis(id) {
    const item = document.querySelector('.tt-jenis-item[data-id="' + id + '"]');
    const nama = item.querySelector('.edit-nama').value.trim();
    const desc = item.querySelector('.edit-desc').value.trim();

    if (!nama) {
        showToast('Nama jenis tugas tidak boleh kosong!', 'error');
        item.querySelector('.edit-nama').focus();
        return;
    }

    const saveBtn = item.querySelector('.tt-edit-save');
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    saveBtn.disabled = true;

    fetch('{{ route("admin.tugas-tambahan.jenis.update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ id: id, nama_tugas: nama, deskripsi: desc })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            renderJenisList(data.data);
        } else {
            showToast(data.message, 'error');
            saveBtn.innerHTML = '<i class="fas fa-check"></i>';
            saveBtn.disabled = false;
        }
    })
    .catch(err => {
        showToast('Terjadi kesalahan, coba lagi!', 'error');
        saveBtn.innerHTML = '<i class="fas fa-check"></i>';
        saveBtn.disabled = false;
    });
}

/* ===== RENDER LIST ===== */
function renderJenisList(items) {
    const container = document.getElementById('jenisListContainer');
    if (!items || items.length === 0) {
        container.innerHTML = '<div class="tt-jenis-empty" id="jenisEmptyState"><i class="fas fa-inbox"></i><p>Belum ada jenis tugas tambahan.</p></div>';
        return;
    }

    let html = '';
    items.forEach(item => {
        const nameSafe = escapeAttr(item.nama_tugas);
        const descSafe = escapeAttr(item.deskripsi || '');
        html += '<div class="tt-jenis-item" data-id="' + item.id + '">';
        html += '  <div class="tt-jenis-info">';
        html += '    <div class="tt-jenis-nama">' + escapeHtml(item.nama_tugas) + '</div>';
        if (item.deskripsi) {
            html += '    <div class="tt-jenis-desc">' + escapeHtml(item.deskripsi) + '</div>';
        }
        html += '  </div>';
        html += '  <div class="tt-jenis-actions">';
        html += '    <button class="tt-jenis-edit" onclick="editJenis(' + item.id + ', \'' + nameSafe.replace(/'/g, "\\'") + '\', \'' + descSafe.replace(/'/g, "\\'") + '\')" title="Edit"><i class="fas fa-pen"></i></button>';
        html += '    <button class="tt-jenis-delete" onclick="hapusJenis(' + item.id + ', \'' + nameSafe.replace(/'/g, "\\'") + '\')" title="Hapus"><i class="fas fa-trash-alt"></i></button>';
        html += '  </div>';
        html += '</div>';
    });
    container.innerHTML = html;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function escapeAttr(text) {
    return String(text).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

// Allow Enter key to submit in nama_tugas input
document.getElementById('inputNamaTugas').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        simpanJenis();
    }
});
</script>
@endsection
