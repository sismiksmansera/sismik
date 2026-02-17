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

/* METHOD SECTION */
.dl-method-section {
    margin-bottom: 20px;
}
.dl-method-section h3 {
    font-size: 15px; font-weight: 700; color: #1f2937;
    margin: 0 0 12px 0;
    display: flex; align-items: center; gap: 8px;
}
.dl-method-section h3 i { color: #10b981; }

/* METHOD CARD (clickable selector) */
.dl-method-card {
    background: white;
    padding: 24px 20px;
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 2px solid #e5e7eb;
    cursor: pointer;
    text-align: center;
    transition: all 0.3s ease;
}
.dl-method-card:hover {
    border-color: #10b981;
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(16,185,129,0.2);
}
.dl-method-card.active {
    border-color: #10b981;
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
}
.dl-method-icon {
    width: 50px; height: 50px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; color: white; margin: 0 auto 12px;
}
.dl-method-title {
    font-size: 15px; font-weight: 700; color: #1f2937; margin: 0 0 4px 0;
}
.dl-method-desc {
    font-size: 12px; color: #6b7280; margin: 0;
}
.dl-method-badge {
    display: inline-block;
    font-size: 11px; font-weight: 600; color: #10b981;
    margin-top: 8px;
}
.dl-method-badge i { font-size: 10px; }

/* MODAL */
.dl-modal-overlay {
    display: none; position: fixed; top: 0; left: 0;
    width: 100%; height: 100%; background: rgba(0,0,0,0.5);
    z-index: 9999; justify-content: center; align-items: center;
}
.dl-modal-overlay.show { display: flex; }
.dl-modal {
    background: white; border-radius: 16px; width: 90%; max-width: 500px;
    max-height: 80vh; overflow-y: auto;
    animation: dlSlideIn 0.3s ease;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}
@keyframes dlSlideIn {
    from { opacity: 0; transform: translateY(30px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
.dl-modal-header {
    display: flex; justify-content: space-between; align-items: center;
    padding: 20px 24px; border-bottom: 1px solid #f3f4f6;
}
.dl-modal-header h3 {
    font-size: 16px; font-weight: 700; color: #1f2937; margin: 0;
    display: flex; align-items: center; gap: 8px;
}
.dl-modal-header .close-btn {
    width: 32px; height: 32px; border-radius: 8px; border: none;
    background: #f3f4f6; font-size: 18px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.2s;
}
.dl-modal-header .close-btn:hover { background: #e5e7eb; }
.dl-modal-body { padding: 20px 24px; }

/* Option cards inside modal */
.dl-modal-option-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}
.dl-option-card {
    background: #f9fafb; border: 2px solid #e5e7eb; border-radius: 12px;
    padding: 18px; cursor: pointer; text-align: center;
    transition: all 0.3s ease;
}
.dl-option-card:hover {
    border-color: #10b981; transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16,185,129,0.15);
}
.dl-option-card.disabled {
    opacity: 0.5; cursor: not-allowed;
}
.dl-option-card.disabled:hover {
    border-color: #e5e7eb; transform: none;
    box-shadow: none;
}
.dl-option-card .option-icon {
    width: 45px; height: 45px; border-radius: 50%;
    margin: 0 auto 10px; display: flex; align-items: center;
    justify-content: center; font-size: 18px; color: white;
}
.dl-option-card .option-name {
    font-size: 13px; font-weight: 700; color: #1f2937; margin: 0;
}

/* FORM SECTION */
.dl-form-section {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.06);
    display: none;
    animation: dlSlideIn 0.3s ease;
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
    margin-left: 8px;
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
    .dl-modal-option-grid { grid-template-columns: 1fr; }
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

<!-- METHOD SELECTION (clickable card that opens modal) -->
<div class="dl-method-section">
    <h3><i class="fas fa-th-large"></i> Pilih Jenis Download</h3>
    <div class="dl-method-card" id="dlMethodSelector" onclick="document.getElementById('dlMethodModal').classList.add('show')">
        <div class="dl-method-icon" style="background: linear-gradient(135deg, #10b981, #059669);" id="dlMethodIcon">
            <i class="fas fa-list-ul" id="dlMethodIconI"></i>
        </div>
        <p class="dl-method-title" id="dlSelectedLabel">Klik untuk memilih jenis download</p>
        <p class="dl-method-desc" id="dlSelectedDesc">Download Blangko, Rincian per Rombel, atau Rincian per Mapel</p>
        <span class="dl-method-badge"><i class="fas fa-chevron-down"></i></span>
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

<!-- FORM: Rincian per Rombel per Mapel (Coming Soon) -->
<div class="dl-form-section" id="formRincianMapel">
    <h3><i class="fas fa-clipboard-list"></i> Download Rincian per Rombel per Mapel <span class="coming-soon-badge">Segera Hadir</span></h3>
    <div class="dl-info">
        <i class="fas fa-clock"></i>
        Fitur ini masih dalam tahap pengembangan dan akan segera tersedia.
    </div>
</div>

<!-- METHOD MODAL -->
<div class="dl-modal-overlay" id="dlMethodModal">
    <div class="dl-modal">
        <div class="dl-modal-header">
            <h3><i class="fas fa-th-large" style="color:#10b981;"></i> Pilih Jenis Download</h3>
            <button class="close-btn" onclick="document.getElementById('dlMethodModal').classList.remove('show')">&times;</button>
        </div>
        <div class="dl-modal-body">
            <div class="dl-modal-option-grid">
                <div class="dl-option-card" onclick="selectDownloadType('blangko')">
                    <div class="option-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="fas fa-file-excel"></i>
                    </div>
                    <p class="option-name">Blangko Presensi</p>
                    <p style="font-size:11px; color:#6b7280; margin:4px 0 0;">Template presensi kosong per rombel</p>
                </div>
                <div class="dl-option-card" onclick="selectDownloadType('rincian')">
                    <div class="option-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                        <i class="fas fa-table"></i>
                    </div>
                    <p class="option-name">Rincian per Rombel</p>
                    <p style="font-size:11px; color:#6b7280; margin:4px 0 0;">Rincian presensi per rombel & tanggal</p>
                </div>
                <div class="dl-option-card disabled" onclick="return false;">
                    <div class="option-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <p class="option-name">Rincian per Mapel</p>
                    <p style="font-size:11px; color:#6b7280; margin:4px 0 0;">Segera Hadir</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="dl-loading" id="dlLoading">
    <div class="dl-loading-box">
        <div class="spinner"></div>
        <p id="dlLoadingText">Mengunduh...</p>
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

const dlTypeConfig = {
    blangko: {
        label: 'Download Blangko Presensi',
        desc: 'Template presensi kosong untuk semua rombel aktif',
        icon: 'fas fa-file-excel',
        gradient: 'linear-gradient(135deg, #10b981, #059669)',
        formId: 'formBlangko'
    },
    rincian: {
        label: 'Download Rincian per Rombel',
        desc: 'Rincian presensi per rombel dalam rentang tanggal',
        icon: 'fas fa-table',
        gradient: 'linear-gradient(135deg, #3b82f6, #2563eb)',
        formId: 'formRincian'
    },
    'rincian-mapel': {
        label: 'Download Rincian per Rombel per Mapel',
        desc: 'Segera Hadir',
        icon: 'fas fa-clipboard-list',
        gradient: 'linear-gradient(135deg, #8b5cf6, #7c3aed)',
        formId: 'formRincianMapel'
    }
};

function selectDownloadType(type) {
    const cfg = dlTypeConfig[type];
    if (!cfg) return;

    // Update the method card
    document.getElementById('dlSelectedLabel').textContent = cfg.label;
    document.getElementById('dlSelectedDesc').textContent = cfg.desc;
    document.getElementById('dlMethodIcon').style.background = cfg.gradient;
    document.getElementById('dlMethodIconI').className = cfg.icon;
    document.getElementById('dlMethodSelector').classList.add('active');

    // Hide all forms, show selected
    document.querySelectorAll('.dl-form-section').forEach(f => f.classList.remove('active'));
    document.getElementById(cfg.formId).classList.add('active');

    // Close modal
    document.getElementById('dlMethodModal').classList.remove('show');
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
