@extends('layouts.app')

@section('title', 'Piket KBM | SISMIK')

@push('styles')
<style>
    /* Page Header */
    .page-header-card {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border-left: 4px solid #3b82f6;
        margin-bottom: 24px;
    }
    .page-header-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        flex-shrink: 0;
    }
    .page-header-details h3 { margin: 0; color: #1f2937; font-size: 18px; font-weight: 600; }
    .page-header-details p { margin: 5px 0 0 0; color: #6b7280; font-size: 14px; }

    /* Day Blocks Grid */
    .days-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 20px;
    }

    /* Day Block */
    .day-block {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        transition: box-shadow 0.2s;
    }
    .day-block:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    .day-block-header {
        padding: 16px 20px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .day-block-header h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .day-block-header .count-badge {
        background: rgba(255, 255, 255, 0.25);
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .day-block-body {
        padding: 16px 20px;
    }

    /* Guru Piket Item */
    .piket-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        background: #f8fafc;
        border-radius: 10px;
        margin-bottom: 8px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
    }
    .piket-item:hover {
        border-color: #93c5fd;
        background: #eff6ff;
    }
    .piket-item:last-child { margin-bottom: 0; }

    .piket-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .piket-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 600;
        color: white;
    }
    .piket-avatar.guru { background: linear-gradient(135deg, #10b981, #059669); }
    .piket-avatar.guru_bk { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

    .piket-name {
        font-size: 13.5px;
        font-weight: 500;
        color: #1f2937;
    }
    .piket-nip {
        font-size: 11px;
        color: #9ca3af;
    }
    .piket-role-badge {
        font-size: 10px;
        padding: 2px 8px;
        border-radius: 10px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .piket-role-badge.guru { background: #d1fae5; color: #065f46; }
    .piket-role-badge.guru_bk { background: #ede9fe; color: #5b21b6; }

    .piket-delete {
        background: none;
        border: none;
        color: #ef4444;
        cursor: pointer;
        font-size: 14px;
        padding: 6px;
        border-radius: 6px;
        transition: all 0.2s;
    }
    .piket-delete:hover {
        background: #fef2f2;
    }

    /* Empty State */
    .empty-piket {
        text-align: center;
        padding: 20px;
        color: #9ca3af;
        font-size: 13px;
    }
    .empty-piket i { font-size: 28px; margin-bottom: 8px; display: block; color: #d1d5db; }

    /* Add Button */
    .add-piket-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
        padding: 10px;
        margin-top: 12px;
        background: #eff6ff;
        border: 2px dashed #93c5fd;
        border-radius: 10px;
        color: #3b82f6;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .add-piket-btn:hover {
        background: #dbeafe;
        border-color: #3b82f6;
    }

    /* ========== MODAL ========== */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 2000;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .modal-overlay.show {
        display: flex;
    }
    .modal-box {
        background: white;
        border-radius: 16px;
        width: 100%;
        max-width: 520px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        animation: modalSlideIn 0.25s ease;
        overflow: hidden;
    }
    @keyframes modalSlideIn {
        from { opacity: 0; transform: translateY(20px) scale(0.97); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .modal-header {
        padding: 20px 24px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .modal-header h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .modal-close {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .modal-close:hover { background: rgba(255,255,255,0.35); }

    .modal-body {
        padding: 24px;
    }
    .modal-body .form-group {
        margin-bottom: 18px;
    }
    .modal-body .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }
    .modal-body .form-group select {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid #d1d5db;
        border-radius: 10px;
        font-size: 14px;
        font-family: inherit;
        background: white;
        color: #1f2937;
        transition: all 0.2s;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        padding-right: 36px;
    }
    .modal-body .form-group select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }
    .modal-day-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eff6ff;
        color: #2563eb;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 18px;
    }

    .modal-footer {
        padding: 16px 24px;
        background: #f9fafb;
        border-top: 1px solid #e5e7eb;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }
    .modal-btn {
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        border: none;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .modal-btn-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
    }
    .modal-btn-primary:hover { opacity: 0.9; }
    .modal-btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }
    .modal-btn-secondary {
        background: #e5e7eb;
        color: #374151;
    }
    .modal-btn-secondary:hover { background: #d1d5db; }

    /* Guru list preview in modal */
    .guru-preview {
        display: none;
        margin-top: 10px;
        padding: 12px;
        background: #f8fafc;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }
    .guru-preview.show { display: flex; align-items: center; gap: 10px; }
    .guru-preview .preview-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: white;
    }
    .guru-preview .preview-avatar.guru { background: linear-gradient(135deg, #10b981, #059669); }
    .guru-preview .preview-avatar.guru_bk { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
    .guru-preview .preview-name { font-size: 14px; font-weight: 500; color: #1f2937; }
    .guru-preview .preview-nip { font-size: 12px; color: #9ca3af; }

    @media (max-width: 768px) {
        .days-grid { grid-template-columns: 1fr; }
        .page-header-card { flex-direction: column; text-align: center; }
        .modal-box { max-width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <!-- Header -->
        <div class="page-header-card">
            <div class="page-header-icon">
                <i class="fas fa-user-clock"></i>
            </div>
            <div class="page-header-details">
                <h3>Manajemen Piket KBM</h3>
                <p>Kelola jadwal guru piket KBM untuk setiap hari</p>
            </div>
        </div>

        <!-- Day Blocks -->
        <div class="days-grid">
            @foreach($hariList as $hari)
            @php $piketHari = $piketData[$hari]; @endphp
            <div class="day-block" id="day-{{ $hari }}">
                <div class="day-block-header">
                    <h4><i class="fas fa-calendar-day"></i> {{ $hari }}</h4>
                    <span class="count-badge">{{ $piketHari->count() }} guru</span>
                </div>
                <div class="day-block-body">
                    <div id="list-{{ $hari }}">
                        @forelse($piketHari as $piket)
                        <div class="piket-item" id="piket-{{ $piket->id }}">
                            <div class="piket-info">
                                <div class="piket-avatar {{ $piket->tipe_guru }}">
                                    <i class="fas {{ $piket->tipe_guru === 'guru_bk' ? 'fa-user-graduate' : 'fa-chalkboard-teacher' }}"></i>
                                </div>
                                <div>
                                    <div class="piket-name">{{ $piket->nama_guru }}</div>
                                    <div class="piket-nip">{{ $piket->nip ?? '-' }} &bull; <span class="piket-role-badge {{ $piket->tipe_guru }}">{{ $piket->tipe_guru === 'guru_bk' ? 'Guru BK' : 'Guru' }}</span></div>
                                </div>
                            </div>
                            <button class="piket-delete" onclick="deletePiket({{ $piket->id }})" title="Hapus">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        @empty
                        <div class="empty-piket">
                            <i class="fas fa-user-slash"></i>
                            Belum ada guru piket
                        </div>
                        @endforelse
                    </div>

                    <!-- Add Button -->
                    <button class="add-piket-btn" onclick="openModal('{{ $hari }}')">
                        <i class="fas fa-plus-circle"></i> Tambah Guru Piket
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- ========== MODAL TAMBAH GURU PIKET ========== -->
<div class="modal-overlay" id="modalTambahPiket">
    <div class="modal-box">
        <div class="modal-header">
            <h4><i class="fas fa-user-plus"></i> Tambah Guru Piket</h4>
            <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="modal-day-badge">
                <i class="fas fa-calendar-day"></i>
                Hari: <strong id="modalHariLabel">-</strong>
            </div>

            <div class="form-group">
                <label><i class="fas fa-user-tag" style="color: #3b82f6;"></i> Tipe Guru</label>
                <select id="modalTipe" onchange="onTipeChange()">
                    <option value="guru">Guru Mata Pelajaran</option>
                    <option value="guru_bk">Guru BK</option>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fas fa-user" style="color: #3b82f6;"></i> Pilih Guru</label>
                <select id="modalGuru" onchange="onGuruChange()">
                    <option value="">-- Pilih Guru --</option>
                </select>
            </div>

            <!-- Preview -->
            <div class="guru-preview" id="guruPreview">
                <div class="preview-avatar" id="previewAvatar">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div>
                    <div class="preview-name" id="previewName">-</div>
                    <div class="preview-nip" id="previewNip">-</div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="modal-btn modal-btn-secondary" onclick="closeModal()">Batal</button>
            <button class="modal-btn modal-btn-primary" id="btnSimpan" onclick="savePiket()" disabled>
                <i class="fas fa-save"></i> Simpan
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const guruData = @json($guruList);
    const guruBkData = @json($guruBkList);

    let selectedHari = '';

    function openModal(hari) {
        selectedHari = hari;
        document.getElementById('modalHariLabel').textContent = hari;
        document.getElementById('modalTipe').value = 'guru';
        document.getElementById('guruPreview').classList.remove('show');
        document.getElementById('btnSimpan').disabled = true;
        populateGuruDropdown('guru');
        document.getElementById('modalTambahPiket').classList.add('show');
    }

    function closeModal() {
        document.getElementById('modalTambahPiket').classList.remove('show');
        selectedHari = '';
    }

    // Close modal on overlay click
    document.getElementById('modalTambahPiket').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    function onTipeChange() {
        const tipe = document.getElementById('modalTipe').value;
        populateGuruDropdown(tipe);
        document.getElementById('guruPreview').classList.remove('show');
        document.getElementById('btnSimpan').disabled = true;
    }

    function populateGuruDropdown(tipe) {
        const select = document.getElementById('modalGuru');
        select.innerHTML = '<option value="">-- Pilih Guru --</option>';
        const data = tipe === 'guru_bk' ? guruBkData : guruData;
        data.forEach(g => {
            const opt = document.createElement('option');
            opt.value = g.id;
            opt.textContent = g.nama + (g.nip ? ' (' + g.nip + ')' : '');
            opt.dataset.nama = g.nama;
            opt.dataset.nip = g.nip || '-';
            select.appendChild(opt);
        });
    }

    function onGuruChange() {
        const select = document.getElementById('modalGuru');
        const preview = document.getElementById('guruPreview');
        const btn = document.getElementById('btnSimpan');

        if (select.value) {
            const opt = select.options[select.selectedIndex];
            const tipe = document.getElementById('modalTipe').value;

            document.getElementById('previewName').textContent = opt.dataset.nama;
            document.getElementById('previewNip').textContent = 'NIP: ' + opt.dataset.nip;

            const avatar = document.getElementById('previewAvatar');
            avatar.className = 'preview-avatar ' + tipe;
            avatar.innerHTML = tipe === 'guru_bk'
                ? '<i class="fas fa-user-graduate"></i>'
                : '<i class="fas fa-chalkboard-teacher"></i>';

            preview.classList.add('show');
            btn.disabled = false;
        } else {
            preview.classList.remove('show');
            btn.disabled = true;
        }
    }

    function savePiket() {
        const tipe = document.getElementById('modalTipe').value;
        const guruId = document.getElementById('modalGuru').value;
        const btn = document.getElementById('btnSimpan');

        if (!guruId || !selectedHari) return;

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

        fetch('{{ route("admin.piket-kbm.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                hari: selectedHari,
                guru_id: guruId,
                tipe_guru: tipe
            })
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                location.reload();
            } else {
                alert(result.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
            }
        })
        .catch(err => {
            alert('Error: ' + err.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
        });
    }

    function deletePiket(id) {
        if (!confirm('Yakin hapus guru piket ini?')) return;

        fetch('{{ url("admin/piket-kbm") }}/' + id, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                location.reload();
            } else {
                alert(result.message);
            }
        })
        .catch(err => alert('Error: ' + err.message));
    }
</script>
@endpush
