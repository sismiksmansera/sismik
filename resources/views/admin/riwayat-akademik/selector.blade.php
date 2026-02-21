@extends('layouts.app')

@section('title', 'Riwayat Akademik')

@push('styles')
<style>
/* Header */
.ra-header {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    border-radius: 16px;
    padding: 25px 30px;
    margin-bottom: 25px;
    box-shadow: 0 10px 30px rgba(124, 58, 237, 0.2);
    display: flex;
    align-items: center;
    gap: 20px;
}
.ra-header-icon {
    width: 70px; height: 70px;
    background: rgba(255,255,255,0.2);
    border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 28px; color: white; flex-shrink: 0;
}
.ra-header-info h1 { font-size: 24px; font-weight: 700; color: white; margin: 0; }
.ra-header-info p { font-size: 14px; color: rgba(255,255,255,0.8); margin: 4px 0 0; }

/* Filter Card */
.filter-card {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin-bottom: 25px;
}
.filter-card h3 {
    font-size: 16px; font-weight: 600; color: #1f2937;
    margin: 0 0 20px 0;
    display: flex; align-items: center; gap: 10px;
}
.filter-card h3 i { color: #7c3aed; }

/* Option Buttons */
.option-buttons {
    display: flex; gap: 15px; flex-wrap: wrap;
}
.option-btn {
    flex: 1; min-width: 220px;
    padding: 20px 25px;
    border: 2px solid #e5e7eb;
    border-radius: 14px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex; align-items: center; gap: 15px;
    text-decoration: none; color: inherit;
}
.option-btn:hover {
    border-color: #7c3aed;
    background: #faf5ff;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(124,58,237,0.15);
}
.option-btn.active {
    border-color: #7c3aed;
    background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
    box-shadow: 0 4px 15px rgba(124,58,237,0.15);
}
.option-btn .opt-icon {
    width: 50px; height: 50px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; flex-shrink: 0;
}
.option-btn:nth-child(1) .opt-icon { background: rgba(59,130,246,0.15); color: #3b82f6; }
.option-btn:nth-child(2) .opt-icon { background: rgba(16,185,129,0.15); color: #10b981; }
.option-btn .opt-text h4 { font-size: 15px; font-weight: 600; color: #1f2937; margin: 0 0 4px 0; }
.option-btn .opt-text p { font-size: 12px; color: #6b7280; margin: 0; }
.option-btn.disabled {
    opacity: 0.6; cursor: not-allowed;
}
.option-btn.disabled:hover {
    border-color: #e5e7eb; background: white;
    transform: none; box-shadow: none;
}

/* Search Section (hidden by default) */
.search-section {
    display: none;
    background: white;
    border-radius: 16px;
    padding: 25px 30px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin-bottom: 25px;
    animation: fadeSlideIn 0.3s ease;
}
.search-section.show { display: block; }

@keyframes fadeSlideIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.search-section h3 {
    font-size: 16px; font-weight: 600; color: #1f2937;
    margin: 0 0 15px 0;
    display: flex; align-items: center; gap: 10px;
}
.search-section h3 i { color: #10b981; }

.search-input-wrap {
    position: relative; margin-bottom: 15px;
}
.search-input-wrap input {
    width: 100%;
    padding: 14px 20px 14px 48px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 15px;
    transition: all 0.3s ease;
    font-family: inherit;
}
.search-input-wrap input:focus {
    outline: none;
    border-color: #7c3aed;
    box-shadow: 0 0 0 4px rgba(124,58,237,0.1);
}
.search-input-wrap i {
    position: absolute;
    left: 18px; top: 50%; transform: translateY(-50%);
    color: #9ca3af; font-size: 16px;
}
.search-hint {
    font-size: 12px; color: #9ca3af; margin-bottom: 15px;
}

/* Results */
.search-results {
    max-height: 400px;
    overflow-y: auto;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
}
.search-results:empty { border: none; }

.result-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 14px 18px;
    border-bottom: 1px solid #f3f4f6;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    color: inherit;
}
.result-item:last-child { border-bottom: none; }
.result-item:hover {
    background: #f5f3ff;
}
.result-avatar {
    width: 42px; height: 42px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 14px; color: white; flex-shrink: 0;
}
.result-avatar.male { background: linear-gradient(135deg, #3b82f6, #2563eb); }
.result-avatar.female { background: linear-gradient(135deg, #ec4899, #db2777); }
.result-info { flex: 1; }
.result-info .result-name { font-weight: 600; font-size: 14px; color: #1f2937; }
.result-info .result-meta { font-size: 12px; color: #6b7280; margin-top: 2px; }
.result-info .result-meta span { margin-right: 12px; }
.result-action {
    color: #7c3aed; font-size: 14px;
    display: flex; align-items: center; gap: 5px;
    font-weight: 500;
}
.result-action i { font-size: 12px; }

.search-empty {
    text-align: center; padding: 40px 20px; color: #9ca3af;
}
.search-empty i { font-size: 36px; margin-bottom: 10px; display: block; color: #d1d5db; }
.search-loading {
    text-align: center; padding: 30px; color: #6b7280;
}

/* Responsive */
@media (max-width: 768px) {
    .ra-header { padding: 20px; }
    .ra-header-icon { width: 50px; height: 50px; font-size: 22px; }
    .ra-header-info h1 { font-size: 18px; }
    .option-buttons { flex-direction: column; }
    .option-btn { min-width: unset; }
    .filter-card, .search-section { padding: 20px; }
}
</style>
@endpush

@section('content')
<div class="layout">
    @if(request()->routeIs('guru_bk.*'))
        @include('layouts.partials.sidebar-guru-bk')
    @elseif(request()->routeIs('guru.*'))
        @include('layouts.partials.sidebar-guru')
    @else
        @include('layouts.partials.sidebar-admin')
    @endif

    <div class="main-content">
        <!-- Header -->
        <div class="ra-header">
            <div class="ra-header-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="ra-header-info">
                <h1>Riwayat Akademik</h1>
                <p>Lihat riwayat akademik siswa secara lengkap</p>
            </div>
        </div>

        <!-- Filter Options -->
        <div class="filter-card">
            <h3><i class="fas fa-filter"></i> Pilih Mode Tampilan</h3>
            <div class="option-buttons">
                <div class="option-btn disabled" title="Fitur akan segera tersedia">
                    <div class="opt-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="opt-text">
                        <h4>Seluruh Riwayat Akademik</h4>
                        <p>Tampilkan semua siswa (segera hadir)</p>
                    </div>
                </div>
                <div class="option-btn" id="btnPerPeriodik" onclick="selectPerPeriodik()">
                    <div class="opt-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="opt-text">
                        <h4>Tampilkan Per Periodik</h4>
                        <p>Cari dan pilih siswa berdasarkan nama/NISN</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="search-section" id="searchSection">
            <h3><i class="fas fa-search"></i> Cari Siswa</h3>
            <div class="search-input-wrap">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Ketik nama siswa, NISN, atau NIS..." autocomplete="off">
            </div>
            <div class="search-hint">Minimal 2 karakter untuk memulai pencarian</div>
            <div class="search-results" id="searchResults"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function selectPerPeriodik() {
    document.querySelectorAll('.option-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('btnPerPeriodik').classList.add('active');
    const section = document.getElementById('searchSection');
    section.classList.add('show');
    setTimeout(() => document.getElementById('searchInput').focus(), 300);
}

let searchTimer = null;
document.getElementById('searchInput').addEventListener('input', function() {
    const query = this.value.trim();
    const results = document.getElementById('searchResults');

    clearTimeout(searchTimer);

    if (query.length < 2) {
        results.innerHTML = '';
        return;
    }

    results.innerHTML = '<div class="search-loading"><i class="fas fa-spinner fa-spin"></i> Mencari...</div>';

    searchTimer = setTimeout(() => {
        const prefix = window.location.pathname.includes('/guru-bk/') ? '/guru-bk' : 
                       (window.location.pathname.includes('/guru/') ? '/guru' : '/admin');

        fetch(prefix + '/riwayat-akademik/search-siswa?q=' + encodeURIComponent(query))
            .then(r => r.json())
            .then(data => {
                if (data.length === 0) {
                    results.innerHTML = '<div class="search-empty"><i class="fas fa-search"></i>Tidak ada siswa ditemukan</div>';
                    return;
                }

                let html = '';
                data.forEach(s => {
                    const initials = s.nama.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase();
                    const gender = (s.jk === 'Laki-laki' || s.jk === 'L') ? 'male' : 'female';
                    const url = prefix + '/riwayat-akademik?nisn=' + encodeURIComponent(s.nisn);
                    html += `<a href="${url}" class="result-item">
                        <div class="result-avatar ${gender}">${initials}</div>
                        <div class="result-info">
                            <div class="result-name">${s.nama}</div>
                            <div class="result-meta">
                                <span><i class="fas fa-barcode"></i> ${s.nisn}</span>
                                <span><i class="fas fa-chalkboard"></i> ${s.rombel}</span>
                            </div>
                        </div>
                        <div class="result-action">Lihat <i class="fas fa-chevron-right"></i></div>
                    </a>`;
                });
                results.innerHTML = html;
            })
            .catch(() => {
                results.innerHTML = '<div class="search-empty"><i class="fas fa-exclamation-triangle"></i>Gagal memuat data</div>';
            });
    }, 300);
});
</script>
@endpush
