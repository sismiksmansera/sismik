@extends('layouts.app-guru-bk')

@section('content')
<div class="main-content panggilan-form-page">
    {{-- Header --}}
    <div class="bk-page-header">
        <div class="header-content-wrapper">
            <div class="header-icon-box"><i class="fas fa-plus-circle"></i></div>
            <div class="header-info">
                <div class="header-greeting">
                    <span class="greeting-text">Buat Panggilan Baru</span>
                    <h1>Panggilan Orang Tua</h1>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="action-buttons-row">
        <a href="{{ route('guru_bk.panggilan-ortu.list') }}" class="btn-action-header btn-secondary-header">
            <i class="fas fa-arrow-left"></i> <span>Kembali</span>
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('error'))
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    {{-- Pilih Siswa --}}
    <div class="siswa-selection-card">
        <div class="selection-header">
            <i class="fas fa-users"></i>
            <h3>Pilih Siswa</h3>
        </div>
        <div class="selection-body">
            <div class="search-box">
                <input type="text" class="search-input" id="searchSiswaInput" placeholder="Ketik nama / NISN / NIS..." oninput="searchDebounced()">
                <i class="fas fa-search search-icon-abs"></i>
            </div>
            <div class="search-results" id="searchResults">
                <div class="search-placeholder"><i class="fas fa-search"></i><p>Ketik nama siswa untuk mencari...</p></div>
            </div>
            <div class="selected-siswa-section" id="selectedSection" style="display:none;">
                <div class="selected-label"><i class="fas fa-check-circle"></i> Siswa Terpilih (<span id="selectedCount">0</span>)</div>
                <div class="selected-list" id="selectedList"></div>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <div class="form-card" id="formCard" style="display:none;">
        <div class="form-header">
            <i class="fas fa-file-alt"></i>
            <h2>Form Surat Panggilan Orang Tua</h2>
        </div>
        
        <form method="POST" action="{{ route('guru_bk.panggilan-ortu.store-batch') }}" class="form-body" id="panggilanForm">
            @csrf
            <input type="hidden" name="nisn_list" id="nisnListInput" value="">

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-hashtag"></i> Nomor Surat</label>
                    <input type="text" name="no_surat" class="form-control"
                           value="{{ old('no_surat', 'SPO/' . date('Ymd') . '/' . rand(100, 999)) }}">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-calendar"></i> Tanggal Surat <span class="required">*</span></label>
                    <input type="date" name="tanggal_surat" class="form-control" required
                           value="{{ old('tanggal_surat', date('Y-m-d')) }}">
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-clipboard"></i> Perihal <span class="required">*</span></label>
                <input type="text" name="perihal" class="form-control" required
                       placeholder="Contoh: Undangan Pertemuan Orang Tua/Wali Siswa"
                       value="{{ old('perihal') }}">
            </div>

            <div class="form-group">
                <label><i class="fas fa-info-circle"></i> Alasan / Keterangan</label>
                <textarea name="alasan" class="form-control" rows="3"
                          placeholder="Jelaskan alasan pemanggilan orang tua...">{{ old('alasan') }}</textarea>
            </div>

            <div class="form-group">
                <label><i class="fas fa-user-tie"></i> Menghadap Ke</label>
                <input type="text" name="menghadap_ke" class="form-control readonly-field"
                       value="{{ $guruBK->nama }}" readonly>
            </div>

            <div class="form-row three-cols">
                <div class="form-group">
                    <label><i class="fas fa-calendar-check"></i> Tanggal Panggilan <span class="required">*</span></label>
                    <input type="date" name="tanggal_panggilan" class="form-control" required
                           value="{{ old('tanggal_panggilan', date('Y-m-d')) }}">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-clock"></i> Jam Panggilan</label>
                    <input type="time" name="jam_panggilan" class="form-control"
                           value="{{ old('jam_panggilan', '09:00') }}">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt"></i> Tempat</label>
                    <input type="text" name="tempat" class="form-control" placeholder="Ruang BK"
                           value="{{ old('tempat', 'Ruang BK') }}">
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('guru_bk.panggilan-ortu.list') }}" class="btn-cancel">
                    <i class="fas fa-times"></i> Batal
                </a>
                <button type="submit" class="btn-submit" id="btnSubmit">
                    <i class="fas fa-save"></i> <span id="btnSubmitText">Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.main-content.panggilan-form-page { padding: 25px; background: #f9fafb; min-height: calc(100vh - 70px); }

.panggilan-form-page .bk-page-header {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    border-radius: 16px; padding: 25px 30px; margin-bottom: 20px;
    box-shadow: 0 10px 30px rgba(124, 58, 237, 0.2);
}
.panggilan-form-page .header-content-wrapper { display: flex; align-items: center; gap: 20px; }
.panggilan-form-page .header-icon-box {
    width: 70px; height: 70px; background: rgba(255,255,255,0.2);
    border-radius: 16px; display: flex; align-items: center; justify-content: center;
    font-size: 28px; color: white; flex-shrink: 0;
}
.panggilan-form-page .header-info { flex: 1; }
.panggilan-form-page .header-greeting .greeting-text { font-size: 14px; color: rgba(255,255,255,0.8); font-weight: 500; display: block; margin-bottom: 4px; }
.panggilan-form-page .header-greeting h1 { font-size: 22px; font-weight: 700; color: white; margin: 0; }

.action-buttons-row { display: flex; gap: 10px; margin-bottom: 20px; }
.btn-action-header {
    padding: 12px 20px; border-radius: 12px; font-weight: 600; font-size: 14px;
    display: inline-flex; align-items: center; gap: 8px; text-decoration: none;
    border: none; cursor: pointer; transition: all 0.3s;
}
.btn-secondary-header { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
.btn-secondary-header:hover { background: #e5e7eb; }

.alert { padding: 15px 20px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: flex-start; gap: 10px; }
.alert-danger { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
.alert ul { margin: 0; padding-left: 20px; }

/* Siswa Selection */
.siswa-selection-card {
    background: white; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    overflow: hidden; margin-bottom: 20px;
}
.selection-header {
    background: linear-gradient(135deg, #f5f3ff, #ede9fe);
    padding: 14px 20px; display: flex; align-items: center; gap: 10px;
    border-bottom: 1px solid #e9d5ff;
}
.selection-header i { color: #7c3aed; font-size: 16px; }
.selection-header h3 { margin: 0; font-size: 14px; font-weight: 600; color: #5b21b6; }
.selection-body { padding: 16px; }

.search-box { position: relative; margin-bottom: 12px; }
.search-input {
    width: 100%; padding: 12px 15px 12px 40px; border: 2px solid #e5e7eb;
    border-radius: 10px; font-size: 14px; transition: all 0.3s; background: white; box-sizing: border-box;
}
.search-input:focus { outline: none; border-color: #7c3aed; box-shadow: 0 0 0 4px rgba(124,58,237,0.1); }
.search-icon-abs { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 14px; }

.search-results { max-height: 250px; overflow-y: auto; border-radius: 8px; }
.search-placeholder { padding: 30px; text-align: center; color: #9ca3af; }
.search-placeholder i { font-size: 24px; margin-bottom: 8px; display: block; }
.search-placeholder p { margin: 0; font-size: 13px; }

.siswa-result-item {
    display: flex; align-items: center; gap: 10px; padding: 10px 12px;
    border-radius: 8px; cursor: pointer; transition: all 0.2s; border: 2px solid transparent;
}
.siswa-result-item:hover { background: #f5f3ff; }
.siswa-result-item.selected { background: #f5f3ff; border-color: #7c3aed; }
.siswa-result-item .check-icon {
    margin-left: auto; width: 24px; height: 24px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    background: #e5e7eb; color: white; font-size: 11px; transition: all 0.2s; flex-shrink: 0;
}
.siswa-result-item.selected .check-icon { background: #7c3aed; }

.siswa-avatar {
    width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 13px; color: white;
}
.siswa-avatar.laki { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.siswa-avatar.perempuan { background: linear-gradient(135deg, #ec4899, #db2777); }
.siswa-result-info { min-width: 0; }
.siswa-nama { font-weight: 600; font-size: 13px; color: #1f2937; }
.siswa-meta { font-size: 11px; color: #6b7280; }

/* Selected Section */
.selected-siswa-section { margin-top: 12px; border-top: 1px solid #f3f4f6; padding-top: 12px; }
.selected-label {
    font-size: 12px; font-weight: 600; color: #7c3aed; margin-bottom: 8px;
    display: flex; align-items: center; gap: 6px;
}
.selected-list { display: flex; flex-wrap: wrap; gap: 6px; }
.selected-chip {
    display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px 4px 12px;
    background: linear-gradient(135deg, #f5f3ff, #ede9fe); border-radius: 20px;
    font-size: 11px; font-weight: 600; color: #5b21b6; border: 1px solid #e9d5ff;
}
.selected-chip .remove-chip {
    width: 18px; height: 18px; border-radius: 50%; border: none;
    background: rgba(124,58,237,0.15); color: #7c3aed; cursor: pointer;
    display: flex; align-items: center; justify-content: center; font-size: 10px;
    transition: all 0.2s;
}
.selected-chip .remove-chip:hover { background: #ef4444; color: white; }

/* Form */
.form-card { background: white; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; }
.form-header {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    padding: 18px 25px; display: flex; align-items: center; gap: 12px;
}
.form-header i { font-size: 20px; color: white; }
.form-header h2 { margin: 0; color: white; font-size: 18px; font-weight: 600; }
.form-body { padding: 25px; }

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.form-row.three-cols { grid-template-columns: 1fr 1fr 1fr; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px; }
.form-group label i { color: #7c3aed; margin-right: 6px; }
.required { color: #ef4444; }
.form-control {
    width: 100%; padding: 12px 15px; border: 2px solid #e5e7eb; border-radius: 10px;
    font-size: 14px; transition: all 0.3s; background: white; box-sizing: border-box;
}
.form-control:focus { outline: none; border-color: #7c3aed; box-shadow: 0 0 0 4px rgba(124,58,237,0.1); }
.readonly-field { background: #f8fafc; color: #6b7280; }
textarea.form-control { resize: vertical; min-height: 100px; }

.form-actions {
    display: flex; gap: 15px; justify-content: space-between;
    padding-top: 25px; border-top: 1px solid #e5e7eb; margin-top: 10px;
}
.btn-cancel, .btn-submit {
    padding: 12px 24px; border-radius: 10px; font-weight: 600; font-size: 14px;
    display: flex; align-items: center; gap: 8px; cursor: pointer;
    transition: all 0.3s; text-decoration: none; border: none;
}
.btn-cancel { background: #f3f4f6; color: #64748b; }
.btn-cancel:hover { background: #e5e7eb; }
.btn-submit {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    color: white; box-shadow: 0 4px 15px rgba(124,58,237,0.3);
}
.btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(124,58,237,0.4); }

@media (max-width: 768px) {
    .main-content.panggilan-form-page { padding: 15px; }
    .panggilan-form-page .bk-page-header { padding: 15px; border-radius: 12px; }
    .panggilan-form-page .header-content-wrapper { flex-direction: column; align-items: center; text-align: center; gap: 10px; }
    .panggilan-form-page .header-icon-box { width: 60px; height: 60px; font-size: 24px; border-radius: 50%; }
    .panggilan-form-page .header-greeting .greeting-text { font-size: 11px; }
    .panggilan-form-page .header-greeting h1 { font-size: 16px; }
    .btn-action-header { padding: 10px 15px; font-size: 12px; }
    .form-header { padding: 15px 18px; }
    .form-header h2 { font-size: 14px; }
    .form-body { padding: 18px; }
    .form-row, .form-row.three-cols { grid-template-columns: 1fr; gap: 0; }
    .form-group { margin-bottom: 15px; }
    .form-group label { font-size: 13px; }
    .form-control { padding: 10px 12px; font-size: 13px; }
    .form-actions { flex-direction: row; gap: 10px; }
    .btn-cancel, .btn-submit { flex: 1; justify-content: center; padding: 10px 15px; font-size: 12px; }
}
</style>

<script>
const selectedSiswa = {}; // { nisn: { nama, jk, rombel } }
let searchTimeout;

function escapeHtml(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}

function searchDebounced() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(doSearch, 300);
}

function doSearch() {
    const q = document.getElementById('searchSiswaInput').value.trim();
    if (q.length < 2) {
        document.getElementById('searchResults').innerHTML =
            '<div class="search-placeholder"><i class="fas fa-search"></i><p>Ketik minimal 2 karakter...</p></div>';
        return;
    }

    document.getElementById('searchResults').innerHTML =
        '<div class="search-placeholder"><i class="fas fa-spinner fa-spin"></i><p>Mencari...</p></div>';

    fetch('{{ route("guru_bk.pelanggaran.search-siswa") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ q: q })
    })
    .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
    .then(data => {
        if (data.length === 0) {
            document.getElementById('searchResults').innerHTML =
                '<div class="search-placeholder"><i class="fas fa-user-slash"></i><p>Siswa tidak ditemukan</p></div>';
            return;
        }
        let html = '';
        data.forEach(s => {
            const nisn = s.nisn || '';
            const isSelected = selectedSiswa[nisn] ? 'selected' : '';
            const avatarClass = s.jk === 'Laki-laki' ? 'laki' : 'perempuan';
            const initial = s.nama ? s.nama.charAt(0).toUpperCase() : '?';
            html += '<div class="siswa-result-item ' + isSelected + '" data-nisn="' + nisn + '" onclick="toggleSiswa(\'' + nisn + '\', \'' + escapeHtml(s.nama) + '\', \'' + (s.jk || '') + '\', \'' + escapeHtml(s.rombel_aktif || '-') + '\')">' +
                '<div class="siswa-avatar ' + avatarClass + '">' + initial + '</div>' +
                '<div class="siswa-result-info">' +
                    '<div class="siswa-nama">' + escapeHtml(s.nama) + '</div>' +
                    '<div class="siswa-meta">' + escapeHtml(s.rombel_aktif || '-') + ' | NISN: ' + (nisn || '-') + '</div>' +
                '</div>' +
                '<div class="check-icon"><i class="fas fa-check"></i></div>' +
            '</div>';
        });
        document.getElementById('searchResults').innerHTML = html;
    })
    .catch(err => {
        console.error('Search error:', err);
        document.getElementById('searchResults').innerHTML =
            '<div class="search-placeholder"><i class="fas fa-exclamation-triangle"></i><p>Terjadi kesalahan</p></div>';
    });
}

function toggleSiswa(nisn, nama, jk, rombel) {
    if (!nisn) return;
    if (selectedSiswa[nisn]) {
        delete selectedSiswa[nisn];
    } else {
        selectedSiswa[nisn] = { nama, jk, rombel };
    }
    updateUI();
}

function removeSiswa(nisn) {
    delete selectedSiswa[nisn];
    updateUI();
}

function updateUI() {
    const keys = Object.keys(selectedSiswa);
    const count = keys.length;

    // Update search result highlights
    document.querySelectorAll('.siswa-result-item').forEach(el => {
        el.classList.toggle('selected', !!selectedSiswa[el.dataset.nisn]);
    });

    // Update selected section
    const section = document.getElementById('selectedSection');
    const listEl = document.getElementById('selectedList');
    const countEl = document.getElementById('selectedCount');

    if (count > 0) {
        section.style.display = '';
        countEl.textContent = count;
        let chips = '';
        keys.forEach(nisn => {
            const s = selectedSiswa[nisn];
            chips += '<span class="selected-chip">' + escapeHtml(s.nama) +
                '<button type="button" class="remove-chip" onclick="removeSiswa(\'' + nisn + '\')"><i class="fas fa-times"></i></button>' +
            '</span>';
        });
        listEl.innerHTML = chips;
    } else {
        section.style.display = 'none';
    }

    // Show/hide form
    const formCard = document.getElementById('formCard');
    formCard.style.display = count > 0 ? '' : 'none';

    // Update hidden input and button text
    document.getElementById('nisnListInput').value = keys.join(',');
    document.getElementById('btnSubmitText').textContent = count > 1 ? 'Simpan untuk ' + count + ' Siswa' : 'Simpan Surat';
}
</script>
@endsection
