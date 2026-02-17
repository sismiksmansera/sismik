@extends($layout ?? 'layouts.app')

@section('title', 'Download Presensi | SISMIK')

@push('styles')
<style>
/* HEADER */
.dl-header {
    background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 20px;
    text-align: center;
    color: white;
    box-shadow: 0 10px 40px rgba(16, 185, 129, 0.3);
}
.dl-header .header-icon-large {
    width: 80px; height: 80px;
    background: rgba(255,255,255,0.2);
    border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    font-size: 36px; color: white;
    margin: 0 auto 20px;
}
.dl-header .page-title {
    font-size: 28px; font-weight: 700; margin: 0 0 8px 0;
    text-transform: uppercase; letter-spacing: 1px;
}
.dl-header .page-subtitle {
    font-size: 14px; font-weight: 500; margin: 0;
    opacity: 0.9;
}

/* SELECTION CARDS */
.dl-selection-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 16px;
    margin-bottom: 20px;
}
.dl-option-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    cursor: pointer;
    border: 2px solid transparent;
    box-shadow: 0 4px 15px rgba(0,0,0,0.06);
    transition: all 0.3s ease;
    text-align: center;
}
.dl-option-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}
.dl-option-card.active {
    border-color: #10b981;
    background: linear-gradient(to bottom, #ecfdf5, white);
}
.dl-option-icon {
    width: 60px; height: 60px;
    border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; color: white;
    margin: 0 auto 14px;
}
.dl-option-icon.green { background: linear-gradient(135deg, #10b981, #059669); }
.dl-option-icon.blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }
.dl-option-icon.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.dl-option-name { font-size: 15px; font-weight: 700; color: #1f2937; margin: 0 0 6px 0; }
.dl-option-desc { font-size: 12px; color: #6b7280; margin: 0; line-height: 1.4; }

/* FORM SECTION */
.dl-form-section {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.06);
    display: none;
}
.dl-form-section.active { display: block; }
.dl-form-section h3 {
    font-size: 18px; font-weight: 700; color: #1f2937;
    margin: 0 0 16px 0;
    display: flex; align-items: center; gap: 8px;
}
.dl-form-section h3 i { color: #10b981; }

/* Form inputs */
.dl-form-group {
    margin-bottom: 16px;
}
.dl-form-group label {
    display: block; font-size: 13px; font-weight: 600;
    color: #374151; margin-bottom: 6px;
}
.dl-form-group select, .dl-form-group input {
    width: 100%; padding: 10px 14px;
    border: 1px solid #d1d5db; border-radius: 10px;
    font-size: 14px; color: #374151;
    background: #f9fafb;
    transition: border-color 0.2s;
}
.dl-form-group select:focus, .dl-form-group input:focus {
    outline: none; border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

/* Download button */
.dl-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 24px;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white; border: none; border-radius: 10px;
    font-size: 14px; font-weight: 600;
    cursor: pointer; transition: all 0.3s ease;
}
.dl-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
}
.dl-btn:disabled {
    opacity: 0.5; cursor: not-allowed; transform: none;
    box-shadow: none;
}
.dl-btn i { font-size: 16px; }

/* Info badge */
.dl-info {
    background: #ecfdf5; border: 1px solid #a7f3d0;
    border-radius: 10px; padding: 12px 16px;
    font-size: 12px; color: #065f46;
    margin-bottom: 16px;
}
.dl-info i { margin-right: 6px; }

/* Coming soon badge */
.coming-soon-badge {
    display: inline-block;
    background: #fef3c7; color: #92400e;
    font-size: 10px; font-weight: 700;
    padding: 3px 8px; border-radius: 6px;
    margin-top: 8px;
}

/* Loading overlay */
.dl-loading {
    display: none;
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    align-items: center; justify-content: center;
}
.dl-loading.show { display: flex; }
.dl-loading-box {
    background: white; border-radius: 16px;
    padding: 30px 40px; text-align: center;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}
.dl-loading-box .spinner {
    width: 40px; height: 40px;
    border: 3px solid #e5e7eb;
    border-top: 3px solid #10b981;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin: 0 auto 12px;
}
@keyframes spin { to { transform: rotate(360deg); } }
.dl-loading-box p { font-size: 14px; color: #374151; margin: 0; }

/* Toast */
.dl-toast {
    position: fixed; top: 20px; right: 20px;
    padding: 12px 20px; border-radius: 10px;
    font-size: 13px; font-weight: 600; color: white;
    z-index: 99999; transform: translateX(120%);
    transition: transform 0.3s ease;
}
.dl-toast.show { transform: translateX(0); }
.dl-toast.success { background: #10b981; }
.dl-toast.error { background: #ef4444; }

@media (max-width: 768px) {
    .dl-header .page-title { font-size: 20px !important; }
    .dl-header .header-icon-large { width: 60px; height: 60px; font-size: 28px; }
    .dl-selection-grid { grid-template-columns: 1fr; }
    .dl-option-card { padding: 16px; }
}
</style>
@endpush

@section('content')
@if(($routePrefix ?? 'admin') === 'admin')
<div class="layout">
    @include('layouts.partials.sidebar-admin')
@else
<div class="layout">
    @include('layouts.partials.sidebar-guru-bk')
@endif

    <div class="main-content">
<!-- Header -->
<div class="dl-header">
    <div class="header-icon-large"><i class="fas fa-download"></i></div>
    <h1 class="page-title">Download Presensi</h1>
    <p class="page-subtitle">{{ $tahunPelajaran }} — Semester {{ $semesterAktif }}</p>
</div>

<!-- Selection Cards -->
<div class="dl-selection-grid">
    <div class="dl-option-card" onclick="selectOption('blangko')" id="optBlangko">
        <div class="dl-option-icon green"><i class="fas fa-file-excel"></i></div>
        <p class="dl-option-name">Download Blangko Presensi</p>
        <p class="dl-option-desc">Template blangko presensi kosong per rombel per tanggal dalam format Excel (.xlsx)</p>
    </div>
    <div class="dl-option-card" onclick="selectOption('rincian')" id="optRincian">
        <div class="dl-option-icon blue"><i class="fas fa-table"></i></div>
        <p class="dl-option-name">Download Rincian per Rombel</p>
        <p class="dl-option-desc">Rincian presensi siswa per rombel dalam rentang tanggal tertentu (.xlsx)</p>
    </div>
    <div class="dl-option-card" onclick="selectOption('rincian-mapel')" id="optRincianMapel">
        <div class="dl-option-icon purple"><i class="fas fa-clipboard-list"></i></div>
        <p class="dl-option-name">Download Rincian per Rombel per Mapel</p>
        <p class="dl-option-desc">Rincian presensi siswa per rombel per mata pelajaran (.xlsx)</p>
        <span class="coming-soon-badge">Segera Hadir</span>
    </div>
</div>

<!-- FORM: Blangko -->
<div class="dl-form-section" id="formBlangko">
    <h3><i class="fas fa-file-excel"></i> Download Blangko Presensi</h3>
    <div class="dl-info">
        <i class="fas fa-info-circle"></i>
        Blangko presensi kosong akan di-generate untuk <strong>semua rombel</strong> aktif. Setiap rombel ditempatkan pada sheet terpisah di file Excel.
    </div>
    <button class="dl-btn" onclick="downloadBlangko()">
        <i class="fas fa-download"></i> Download Blangko
    </button>
</div>

<!-- FORM: Rincian per Rombel -->
<div class="dl-form-section" id="formRincian">
    <h3><i class="fas fa-table"></i> Download Rincian per Rombel</h3>
    <div class="dl-info">
        <i class="fas fa-info-circle"></i>
        Download rincian presensi siswa per rombel dalam rentang tanggal tertentu. Data ditampilkan per tanggal per jam pelajaran.
    </div>
    <div class="dl-form-group">
        <label>Rombel</label>
        <select id="rincianRombel">
            <option value="">— Pilih Rombel —</option>
            @foreach($rombelList as $r)
                <option value="{{ $r->id }}" data-nama="{{ $r->nama_rombel }}">{{ $r->nama_rombel }}</option>
            @endforeach
        </select>
    </div>
    <div style="display:flex;gap:12px;">
        <div class="dl-form-group" style="flex:1;">
            <label>Tanggal Mulai</label>
            <input type="date" id="rincianStart">
        </div>
        <div class="dl-form-group" style="flex:1;">
            <label>Tanggal Selesai</label>
            <input type="date" id="rincianEnd">
        </div>
    </div>
    <button class="dl-btn" onclick="downloadRincian()">
        <i class="fas fa-download"></i> Download Rincian
    </button>
</div>

<!-- FORM: Rincian per Rombel per Mapel -->
<div class="dl-form-section" id="formRincianMapel">
    <h3><i class="fas fa-clipboard-list"></i> Download Rincian per Rombel per Mapel</h3>
    <div class="dl-info">
        <i class="fas fa-clock"></i>
        Fitur ini masih dalam tahap pengembangan dan akan segera tersedia.
    </div>
    <div class="dl-form-group">
        <label>Rombel</label>
        <select id="rincianMapelRombel" disabled>
            <option value="">— Pilih Rombel —</option>
            @foreach($rombelList as $r)
                <option value="{{ $r->id }}">{{ $r->nama_rombel }}</option>
            @endforeach
        </select>
    </div>
    <div class="dl-form-group">
        <label>Mata Pelajaran</label>
        <select id="rincianMapelMapel" disabled>
            <option value="">— Pilih Mata Pelajaran —</option>
        </select>
    </div>
    <button class="dl-btn" disabled>
        <i class="fas fa-download"></i> Download Rincian
    </button>
</div>

<!-- Loading Overlay -->
<div class="dl-loading" id="dlLoading">
    <div class="dl-loading-box">
        <div class="spinner"></div>
        <p id="dlLoadingText">Mengunduh blangko...</p>
    </div>
</div>

<!-- Toast -->
<div class="dl-toast" id="dlToast"></div>
    </div><!-- /.main-content -->
</div><!-- /.layout -->
@endsection

@push('scripts')
<script>
const csrfToken = '{{ csrf_token() }}';
const downloadBlangkoUrl = '{{ route("{$routePrefix}.download-presensi.blangko") }}';
const downloadRincianUrl = '{{ route("{$routePrefix}.download-presensi.rincian-rombel") }}';

let currentOption = null;

function selectOption(opt) {
    currentOption = opt;
    document.querySelectorAll('.dl-option-card').forEach(c => c.classList.remove('active'));
    document.querySelectorAll('.dl-form-section').forEach(f => f.classList.remove('active'));

    if (opt === 'blangko') {
        document.getElementById('optBlangko').classList.add('active');
        document.getElementById('formBlangko').classList.add('active');
    } else if (opt === 'rincian') {
        document.getElementById('optRincian').classList.add('active');
        document.getElementById('formRincian').classList.add('active');
    } else if (opt === 'rincian-mapel') {
        document.getElementById('optRincianMapel').classList.add('active');
        document.getElementById('formRincianMapel').classList.add('active');
    }
}

function showDlLoading(msg) {
    document.getElementById('dlLoadingText').textContent = msg;
    document.getElementById('dlLoading').classList.add('show');
}
function hideDlLoading() {
    document.getElementById('dlLoading').classList.remove('show');
}

function showDlToast(msg, type) {
    const t = document.getElementById('dlToast');
    t.textContent = msg;
    t.className = 'dl-toast ' + type + ' show';
    setTimeout(() => t.classList.remove('show'), 3000);
}

function downloadBlangko() {
    showDlLoading('Membuat blangko presensi...');

    fetch(downloadBlangkoUrl, {
        method: 'GET',
        headers: { 'X-CSRF-TOKEN': csrfToken }
    })
    .then(response => {
        if (!response.ok) throw new Error('Server error');
        return response.blob();
    })
    .then(blob => {
        hideDlLoading();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'Blangko_Presensi.xlsx';
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);
        showDlToast('Blangko berhasil diunduh ✓', 'success');
    })
    .catch(() => {
        hideDlLoading();
        showDlToast('Gagal mengunduh blangko', 'error');
    });
}

function downloadRincian() {
    const rombelSelect = document.getElementById('rincianRombel');
    const idRombel = rombelSelect.value;
    const namaRombel = rombelSelect.options[rombelSelect.selectedIndex]?.dataset?.nama || '';
    const start = document.getElementById('rincianStart').value;
    const end = document.getElementById('rincianEnd').value;

    if (!idRombel) {
        showDlToast('Pilih rombel terlebih dahulu', 'error');
        return;
    }
    if (!start || !end) {
        showDlToast('Pilih tanggal mulai dan selesai', 'error');
        return;
    }
    if (start > end) {
        showDlToast('Tanggal mulai tidak boleh lebih dari tanggal selesai', 'error');
        return;
    }

    showDlLoading('Membuat rincian presensi...');

    fetch(downloadRincianUrl + '?id_rombel=' + idRombel + '&start=' + start + '&end=' + end, {
        method: 'GET',
        headers: { 'X-CSRF-TOKEN': csrfToken }
    })
    .then(response => {
        if (!response.ok) throw new Error('Server error');
        return response.blob();
    })
    .then(blob => {
        hideDlLoading();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'Rincian_Presensi_' + namaRombel.replace(/\s+/g, '_') + '.xlsx';
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);
        showDlToast('Rincian presensi berhasil diunduh ✓', 'success');
    })
    .catch(() => {
        hideDlLoading();
        showDlToast('Gagal mengunduh rincian presensi', 'error');
    });
}
</script>
@endpush
