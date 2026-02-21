@extends('layouts.app')

@section('title', 'Input Prestasi Siswa')

@push('styles')
<style>
.page-wrapper { padding: 30px; background: #f8f9fa; min-height: calc(100vh - 60px); }
.page-header {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    border-radius: 16px; padding: 25px 30px; margin-bottom: 25px; color: white;
    box-shadow: 0 10px 40px rgba(245, 158, 11, 0.3);
}
.header-row { display: flex; align-items: center; gap: 20px; }
.btn-back { width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; text-decoration: none; color: white; font-size: 18px; transition: all 0.2s; }
.btn-back:hover { background: rgba(255,255,255,0.3); color: white; }
.header-row h1 { margin: 0 0 8px; font-size: 1.5rem; font-weight: 700; }
.header-row p { margin: 0; opacity: 0.9; font-size: 0.9rem; }

/* FORM */
.form-card { background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden; }
.form-card-header { padding: 25px 30px; border-bottom: 1px solid #e5e7eb; }
.form-card-header h2 { margin: 0; font-size: 1.1rem; color: #1f2937; display: flex; align-items: center; gap: 10px; }
.form-card-header h2 i { color: #f59e0b; }
.form-body { padding: 30px; }

.form-group { margin-bottom: 25px; }
.form-group label { display: block; font-weight: 600; color: #374151; margin-bottom: 10px; font-size: 0.9rem; }
.form-group label i { color: #6b7280; margin-right: 3px; }
.form-group label .required { color: #ef4444; }
.form-input {
    width: 100%; padding: 12px 15px; border: 2px solid #e5e7eb; border-radius: 10px;
    font-size: 0.95rem; background: #fafafa; transition: all 0.2s;
}
.form-input:focus { outline: none; border-color: #f59e0b; background: white; box-shadow: 0 0 0 3px rgba(245,158,11,0.1); }

.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px; }

/* PILIH SISWA */
.selected-preview {
    min-height: 50px; border: 2px dashed #e5e7eb; border-radius: 10px;
    padding: 15px; margin-bottom: 10px; background: #fafafa;
}
.no-siswa-text { margin: 0; color: #9ca3af; text-align: center; font-size: 0.9rem; }
.siswa-chips { display: flex; flex-wrap: wrap; gap: 8px; }
.siswa-chip {
    display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white; border-radius: 20px; font-size: 0.85rem; font-weight: 500;
}
.siswa-chip .remove-chip { cursor: pointer; opacity: 0.8; }
.siswa-chip .remove-chip:hover { opacity: 1; }

.btn-pilih-siswa {
    padding: 10px 20px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white; border: none; border-radius: 8px; font-weight: 600;
    cursor: pointer; display: inline-flex; align-items: center; gap: 8px;
    transition: all 0.2s;
}
.btn-pilih-siswa:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.2); }

/* RADIO BUTTONS */
.radio-group { display: flex; gap: 20px; }
.radio-option {
    display: flex; align-items: center; gap: 8px; cursor: pointer;
    padding: 12px 20px; border: 2px solid #e5e7eb; border-radius: 10px;
    background: #fafafa; transition: all 0.2s;
}
.radio-option:has(input:checked) { border-color: #f59e0b; background: #fffbeb; }
.radio-option input { width: 18px; height: 18px; accent-color: #f59e0b; }

/* FORM ACTIONS */
.form-actions {
    display: flex; gap: 15px; justify-content: flex-end;
    padding-top: 20px; border-top: 1px solid #e5e7eb;
}
.btn-cancel {
    padding: 12px 25px; background: #f3f4f6; color: #4b5563; border-radius: 10px;
    text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;
}
.btn-simpan {
    padding: 12px 25px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white; border: none; border-radius: 10px; font-weight: 600;
    cursor: pointer; display: inline-flex; align-items: center; gap: 8px;
    transition: all 0.2s;
}
.btn-simpan:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.2); }
.btn-simpan:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }

/* MODAL */
.modal-overlay {
    display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;
}
.modal-overlay.active { display: flex; }
.modal-box {
    background: white; border-radius: 16px; width: 90%; max-width: 600px;
    max-height: 80vh; overflow: hidden; box-shadow: 0 25px 50px rgba(0,0,0,0.25);
}
.modal-header {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    padding: 20px 25px; color: white; display: flex; justify-content: space-between; align-items: center;
}
.modal-header h3 { margin: 0; font-size: 1.1rem; }
.modal-header p { margin: 5px 0 0; font-size: 0.85rem; opacity: 0.9; }
.modal-close { background: rgba(255,255,255,0.2); border: none; color: white; width: 35px; height: 35px; border-radius: 8px; cursor: pointer; font-size: 1.2rem; }
.modal-search { padding: 15px 25px; border-bottom: 1px solid #e5e7eb; }
.modal-search input { width: 100%; padding: 10px 15px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 0.95rem; }
.modal-selectall { padding: 10px 25px; border-bottom: 1px solid #e5e7eb; background: #f8fafc; }
.modal-selectall label { display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 600; color: #374151; }
.modal-selectall input { width: 18px; height: 18px; accent-color: #3b82f6; }
.siswa-list-container { max-height: 350px; overflow-y: auto; padding: 10px 25px; }
.siswa-item { display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; cursor: pointer; margin-bottom: 5px; transition: background 0.2s; }
.siswa-item:hover { background: #f0f9ff; }
.siswa-item input { width: 18px; height: 18px; accent-color: #3b82f6; }
.modal-footer { padding: 15px 25px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; }
.btn-modal-cancel { padding: 10px 20px; background: #f3f4f6; color: #4b5563; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
.btn-modal-confirm { padding: 10px 20px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }

/* RESPONSIVE */
@media (max-width: 768px) {
    .page-wrapper { padding: 15px; }
    .form-grid { grid-template-columns: 1fr; }
    .radio-group { flex-direction: column; }
    .form-actions { flex-direction: column-reverse; }
    .btn-cancel, .btn-simpan { width: 100%; justify-content: center; }
}
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-guru')

    <div class="main-content">
        <div class="page-wrapper">

            <!-- HEADER -->
            <div class="page-header">
                <div class="header-row">
                    <a href="{{ url('guru/lihat-prestasi?type=' . $type . '&id=' . $sourceId) }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1><i class="fas fa-trophy"></i> Input Prestasi Siswa</h1>
                        <p>{{ $type == 'ekstra' ? 'Ekstrakurikuler' : 'Wali Kelas' }}: <strong>{{ $sourceNama }}</strong></p>
                    </div>
                </div>
            </div>

            <!-- FORM -->
            <div class="form-card">
                <div class="form-card-header">
                    <h2><i class="fas fa-edit"></i> Form Input Prestasi</h2>
                </div>

                <form id="formPrestasi" class="form-body">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="hidden" name="source_id" value="{{ $sourceId }}">
                    <input type="hidden" name="siswa_ids" id="siswaIdsInput" value="">

                    <!-- Pilih Siswa -->
                    <div class="form-group">
                        <label><i class="fas fa-user-graduate"></i> Siswa yang Memperoleh Prestasi <span class="required">*</span></label>
                        <div class="selected-preview">
                            <p class="no-siswa-text" id="noSiswaText"><i class="fas fa-info-circle"></i> Belum ada siswa dipilih</p>
                            <div class="siswa-chips" id="siswaChips"></div>
                        </div>
                        <button type="button" class="btn-pilih-siswa" id="btnPilihSiswa">
                            <i class="fas fa-users"></i> Pilih Siswa ({{ count($siswaList) }} tersedia)
                        </button>
                    </div>

                    <div class="form-grid">
                        <!-- Juara Ke -->
                        <div class="form-group">
                            <label><i class="fas fa-medal"></i> Juara Ke- <span class="required">*</span></label>
                            <input type="text" name="juara" class="form-input" required placeholder="Contoh: 1, 2, 3, Harapan 1">
                        </div>
                        <!-- Jenjang -->
                        <div class="form-group">
                            <label><i class="fas fa-layer-group"></i> Jenjang <span class="required">*</span></label>
                            <select name="jenjang" class="form-input" required>
                                <option value="">-- Pilih Jenjang --</option>
                                <option value="Kelas">Kelas</option>
                                <option value="Sekolah">Sekolah</option>
                                <option value="Kecamatan">Kecamatan</option>
                                <option value="Kabupaten">Kabupaten/Kota</option>
                                <option value="Provinsi">Provinsi</option>
                                <option value="Nasional">Nasional</option>
                                <option value="Internasional">Internasional</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tipe Peserta -->
                    <div class="form-group">
                        <label><i class="fas fa-user-friends"></i> Tipe Peserta <span class="required">*</span></label>
                        <div class="radio-group">
                            <label class="radio-option">
                                <input type="radio" name="tipe_peserta" value="Single" checked>
                                <i class="fas fa-user" style="color: #3b82f6;"></i>
                                <span>Single (Perorangan)</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="tipe_peserta" value="Tim">
                                <i class="fas fa-users" style="color: #10b981;"></i>
                                <span>Tim (Beregu)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Nama Kompetisi -->
                    <div class="form-group">
                        <label><i class="fas fa-trophy"></i> Nama Kompetisi/Perlombaan <span class="required">*</span></label>
                        <input type="text" name="nama_kompetisi" class="form-input" required placeholder="Contoh: Olimpiade Matematika SMA 2026">
                    </div>

                    <div class="form-grid">
                        <!-- Penyelenggara -->
                        <div class="form-group">
                            <label><i class="fas fa-building"></i> Penyelenggara <span class="required">*</span></label>
                            <input type="text" name="penyelenggara" class="form-input" required placeholder="Contoh: Dinas Pendidikan Provinsi">
                        </div>
                        <!-- Tanggal -->
                        <div class="form-group">
                            <label><i class="fas fa-calendar-alt"></i> Tanggal Pelaksanaan <span class="required">*</span></label>
                            <input type="date" name="tanggal_pelaksanaan" class="form-input" required>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="form-actions">
                        <a href="{{ url('guru/lihat-prestasi?type=' . $type . '&id=' . $sourceId) }}" class="btn-cancel">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" class="btn-simpan" id="btnSimpan">
                            <i class="fas fa-save"></i> Simpan Prestasi
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- MODAL PILIH SISWA -->
<div class="modal-overlay" id="modalPilihSiswa">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <h3><i class="fas fa-users"></i> Pilih Siswa</h3>
                <p>Centang siswa yang memperoleh prestasi</p>
            </div>
            <button class="modal-close" id="btnCloseModal">&times;</button>
        </div>
        <div class="modal-search">
            <input type="text" id="searchSiswa" placeholder="Cari nama siswa...">
        </div>
        <div class="modal-selectall">
            <label>
                <input type="checkbox" id="selectAllSiswa">
                <span>Pilih Semua (<span id="totalSiswaCount">{{ count($siswaList) }}</span> siswa)</span>
            </label>
        </div>
        <div class="siswa-list-container" id="siswaListContainer">
            @foreach($siswaList as $s)
            <label class="siswa-item" data-nama="{{ strtolower($s->nama) }}">
                <input type="checkbox" class="siswa-checkbox" value="{{ $s->siswa_id ?? $s->id }}" data-nama="{{ $s->nama }}">
                <div style="flex: 1;">
                    <div style="font-weight: 600; color: #1f2937;">{{ $s->nama }}</div>
                    <div style="font-size: 0.8rem; color: #6b7280;">NIS: {{ $s->nis }}</div>
                </div>
            </label>
            @endforeach
            @if(count($siswaList) == 0)
            <p style="text-align: center; color: #9ca3af; padding: 30px;">Tidak ada siswa terdaftar</p>
            @endif
        </div>
        <div class="modal-footer">
            <span id="selectedCount" style="font-size: 0.9rem; color: #6b7280;">0 siswa dipilih</span>
            <div style="display: flex; gap: 10px;">
                <button class="btn-modal-cancel" id="btnCancelModal">Batal</button>
                <button class="btn-modal-confirm" id="btnConfirmSiswa"><i class="fas fa-check"></i> Konfirmasi</button>
            </div>
        </div>
    </div>
</div>

<script>
let selectedSiswa = [];
const modal = document.getElementById('modalPilihSiswa');

document.getElementById('btnPilihSiswa').addEventListener('click', () => {
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    updateCheckboxesFromSelection();
    updateSelectedCount();
});

function closeModal() {
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
    document.getElementById('searchSiswa').value = '';
    filterSiswa('');
}

document.getElementById('btnCloseModal').addEventListener('click', closeModal);
document.getElementById('btnCancelModal').addEventListener('click', closeModal);
modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

document.getElementById('searchSiswa').addEventListener('input', function() {
    filterSiswa(this.value.toLowerCase());
});

function filterSiswa(q) {
    document.querySelectorAll('.siswa-item').forEach(el => {
        el.style.display = el.dataset.nama.includes(q) ? 'flex' : 'none';
    });
}

document.getElementById('selectAllSiswa').addEventListener('change', function() {
    document.querySelectorAll('.siswa-item:not([style*="none"]) .siswa-checkbox').forEach(cb => cb.checked = this.checked);
    updateSelectedCount();
});

document.querySelectorAll('.siswa-checkbox').forEach(cb => {
    cb.addEventListener('change', () => { updateSelectedCount(); updateSelectAllState(); });
});

function updateSelectedCount() {
    document.getElementById('selectedCount').textContent = document.querySelectorAll('.siswa-checkbox:checked').length + ' siswa dipilih';
}
function updateSelectAllState() {
    const all = document.querySelectorAll('.siswa-checkbox');
    const checked = document.querySelectorAll('.siswa-checkbox:checked');
    document.getElementById('selectAllSiswa').checked = all.length > 0 && all.length === checked.length;
}
function updateCheckboxesFromSelection() {
    document.querySelectorAll('.siswa-checkbox').forEach(cb => { cb.checked = selectedSiswa.some(s => s.id == cb.value); });
}

document.getElementById('btnConfirmSiswa').addEventListener('click', () => {
    selectedSiswa = [];
    document.querySelectorAll('.siswa-checkbox:checked').forEach(cb => {
        selectedSiswa.push({ id: cb.value, nama: cb.dataset.nama });
    });
    updatePreview();
    closeModal();
});

function updatePreview() {
    const chips = document.getElementById('siswaChips');
    const noText = document.getElementById('noSiswaText');
    chips.innerHTML = '';
    if (selectedSiswa.length === 0) {
        noText.style.display = 'block';
        document.getElementById('siswaIdsInput').value = '';
    } else {
        noText.style.display = 'none';
        document.getElementById('siswaIdsInput').value = selectedSiswa.map(s => s.id).join(',');
        selectedSiswa.forEach(s => {
            const chip = document.createElement('span');
            chip.className = 'siswa-chip';
            chip.innerHTML = `${s.nama} <span class="remove-chip" data-id="${s.id}">&times;</span>`;
            chips.appendChild(chip);
        });
        document.querySelectorAll('.remove-chip').forEach(btn => {
            btn.addEventListener('click', function() {
                selectedSiswa = selectedSiswa.filter(s => s.id != this.dataset.id);
                updatePreview();
            });
        });
    }
}

// Form submit
document.getElementById('formPrestasi').addEventListener('submit', function(e) {
    e.preventDefault();
    if (selectedSiswa.length === 0) { showToast('Silakan pilih minimal 1 siswa!', 'error'); return; }

    const btn = document.getElementById('btnSimpan');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    const formData = new FormData(this);

    fetch('{{ url("guru/input-prestasi/store") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => {
                window.location.href = '{{ url("guru/lihat-prestasi") }}?type={{ $type }}&id={{ $sourceId }}';
            }, 1500);
        } else {
            showToast(data.message || 'Gagal menyimpan prestasi!', 'error');
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Simpan Prestasi';
    })
    .catch(() => {
        showToast('Terjadi kesalahan koneksi!', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Simpan Prestasi';
    });
});

function showToast(message, type) {
    document.querySelectorAll('.toast-msg').forEach(t => t.remove());
    const toast = document.createElement('div');
    toast.className = 'toast-msg';
    toast.style.cssText = `position: fixed; top: 20px; right: 20px; background: ${type === 'success' ? '#10b981' : '#ef4444'}; color: white; padding: 15px 25px; border-radius: 10px; z-index: 99999; box-shadow: 0 10px 30px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 10px;`;
    toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i> ${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>
@endsection
