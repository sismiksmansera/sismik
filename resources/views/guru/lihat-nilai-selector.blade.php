@extends('layouts.app')

@section('title', 'Lihat Nilai | SISMIK')

@push('styles')
<style>
    /* HEADER SECTION - Green gradient */
    .nilai-header-section {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 25px;
        color: white;
        text-align: center;
    }

    .nilai-header-section .header-icon-large {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        color: white;
        margin: 0 auto 20px;
    }

    .nilai-header-section .page-title {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 8px 0;
        color: white;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .nilai-header-section .page-subtitle {
        font-size: 14px;
        font-weight: 500;
        margin: 0;
        color: rgba(255, 255, 255, 0.9);
    }

    /* SELECTION AND INFO ROW */
    .selection-info-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-bottom: 25px;
    }

    .selection-card, .info-card {
        background: var(--bg-card, #ffffff);
        padding: 15px 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s ease;
    }

    .selection-card {
        cursor: pointer;
    }

    .selection-card:hover {
        border-color: #10b981;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.15);
    }

    .selection-card.selected {
        border: 2px solid #10b981;
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    }

    .selection-card .card-icon,
    .info-card .card-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: white;
        flex-shrink: 0;
    }

    .selection-card .card-icon.mapel-icon,
    .info-card .card-icon.mapel-icon {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    }

    .selection-card .card-icon.rombel-icon,
    .info-card .card-icon.rombel-icon {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .info-card .card-icon.period-icon {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .card-content {
        flex: 1;
        min-width: 0;
    }

    .card-content .card-value {
        font-size: 15px;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 2px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .card-content .card-placeholder {
        font-size: 14px;
        color: #9ca3af;
        margin: 0 0 2px 0;
    }

    .card-content .card-label {
        font-size: 11px;
        color: #6b7280;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin: 0;
    }

    /* MODAL STYLES */
    .modal-option-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        max-height: 400px;
        overflow-y: auto;
        padding: 10px;
    }

    .option-card {
        background: #fff;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }

    .option-card:hover {
        border-color: #10b981;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
    }

    .option-card .option-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin: 0 auto 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
    }

    .option-card .option-icon.mapel { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
    .option-card .option-icon.rombel { background: linear-gradient(135deg, #f59e0b, #d97706); }

    .option-card .option-name {
        font-size: 14px;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    /* LOADING SPINNER */
    .loading-spinner {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px;
    }

    .loading-spinner .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #e5e7eb;
        border-top-color: #10b981;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* STUDENTS SECTION */
    #studentsSection {
        display: none;
    }

    #studentsSection.show {
        display: block;
    }

    /* STUDENT CARDS */
    .students-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 20px;
    }

    .student-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        overflow: hidden;
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }

    .student-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    }

    .student-card-header {
        padding: 15px;
        display: flex;
        align-items: center;
        gap: 15px;
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: white;
    }

    .student-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 18px;
        flex-shrink: 0;
        overflow: hidden;
    }

    .student-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .student-info { flex: 1; min-width: 0; }
    .student-name {
        font-weight: 700;
        font-size: 15px;
        color: white;
        margin: 0 0 4px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .student-nisn {
        font-size: 12px;
        color: rgba(255,255,255,0.9);
        margin: 0;
    }

    .student-status {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .student-status.success { background: #d1fae5; color: #059669; }
    .student-status.warning { background: #fef3c7; color: #d97706; }

    /* STATISTICS ROW */
    .statistics-row {
        display: flex;
        gap: 10px;
        padding: 12px 15px;
        background: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
    }

    .stat-item-small {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 10px;
        background: white;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }

    .stat-icon-small {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: white;
    }

    .stat-item-small:nth-child(1) .stat-icon-small { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .stat-item-small:nth-child(2) .stat-icon-small { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-item-small:nth-child(3) .stat-icon-small { background: linear-gradient(135deg, #f59e0b, #d97706); }

    .stat-info-small { flex: 1; }
    .stat-value-small { font-size: 14px; font-weight: 700; color: #1f2937; }
    .stat-label-small { font-size: 9px; color: #6b7280; text-transform: uppercase; }

    /* NO STATISTICS */
    .no-statistics {
        padding: 20px;
        text-align: center;
        color: #9ca3af;
        background: #f8fafc;
    }

    .no-statistics i { font-size: 24px; margin-bottom: 8px; }

    /* NILAI LIST */
    .student-card-body { padding: 15px; }

    .nilai-summary-header {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 12px;
    }

    .nilai-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        max-height: 200px;
        overflow-y: auto;
    }

    .nilai-item {
        padding: 10px 12px;
        background: #f8fafc;
        border-radius: 8px;
        border-left: 3px solid #10b981;
    }

    .nilai-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }

    .nilai-date { font-size: 12px; font-weight: 600; color: #059669; display: flex; align-items: center; gap: 6px; }
    .nilai-score { font-size: 12px; color: #1f2937; }
    .score-label { color: #6b7280; }
    .score-value { font-weight: 700; }
    .nilai-materi { font-size: 11px; color: #6b7280; }

    /* EDIT BUTTON */
    .btn-edit-nilai {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 10px;
    }

    .btn-edit-nilai:hover {
        background: linear-gradient(135deg, #d97706, #b45309);
        transform: scale(1.1);
        color: white;
    }

    /* TAMBAH NILAI BUTTON */
    .student-card-footer {
        padding: 12px 15px;
        background: #f8fafc;
        border-top: 1px solid #e5e7eb;
    }

    .btn-tambah-nilai {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 12px;
        text-decoration: none;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
        transition: all 0.3s ease;
        width: 100%;
        justify-content: center;
    }

    .btn-tambah-nilai:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        color: white;
    }

    /* SECTION HEADER */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .section-header h2 {
        font-size: 18px;
        font-weight: 700;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
    }

    .section-header h2 i { color: #059669; }

    .section-header .badge {
        background: #d1fae5;
        color: #059669;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    /* EMPTY STATE */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }

    .empty-icon { font-size: 48px; color: #d1d5db; margin-bottom: 15px; }
    .empty-state h3 { margin: 0 0 10px 0; color: #1f2937; font-size: 18px; }
    .empty-state p { margin: 0; color: #6b7280; font-size: 14px; }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .nilai-header-section { padding: 20px 15px; }
        .nilai-header-section .header-icon-large {
            width: 60px; height: 60px; font-size: 28px; margin-bottom: 15px;
        }
        .nilai-header-section .page-title { font-size: 20px; }
        
        .stats-grid { grid-template-columns: repeat(3, 1fr); gap: 8px; }
        .stat-card {
            flex-direction: column; text-align: center; padding: 10px; gap: 8px;
        }
        .stat-icon { width: 35px; height: 35px; font-size: 14px; }
        .stat-info h3 { font-size: 11px; white-space: normal; }
        .stat-info p { font-size: 9px; }

        .selection-info-row {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .selection-card, .info-card {
            padding: 12px 15px;
        }

        .selection-card .card-icon,
        .info-card .card-icon {
            width: 38px;
            height: 38px;
            font-size: 16px;
        }

        .card-content .card-value {
            font-size: 13px;
        }

        .card-content .card-placeholder {
            font-size: 12px;
        }

        .card-content .card-label {
            font-size: 10px;
        }
        
        .students-cards-grid { grid-template-columns: 1fr; gap: 15px; }
        .student-avatar { width: 40px; height: 40px; font-size: 14px; }
        .student-name { font-size: 13px; }
        .student-nisn { font-size: 10px; }
        
        .statistics-row { flex-direction: row; gap: 6px; }
        .stat-item-small { flex-direction: column; text-align: center; padding: 6px; }
        .stat-icon-small { width: 22px; height: 22px; font-size: 10px; }
        .stat-value-small { font-size: 12px; }
        .stat-label-small { font-size: 8px; }

        .modal-option-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-guru')
    
    <div class="main-content">
        <div class="lihat-nilai-page">
            <!-- HEADER SECTION -->
            <div class="nilai-header-section">
                <div class="header-icon-large">
                    <i class="fas fa-eye"></i>
                </div>
                <h1 class="page-title">Lihat Nilai</h1>
                <p class="page-subtitle">{{ $tahunPelajaran }} - Semester {{ ucfirst($semesterAktif) }}</p>
            </div>

            <!-- Selection Cards Row -->
            <div class="selection-info-row">
                <div class="selection-card" id="mapelCard" onclick="openMapelModal()">
                    <div class="card-icon mapel-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="card-content">
                        <p class="card-value" id="mapelValue" style="display: none;"></p>
                        <p class="card-placeholder" id="mapelPlaceholder">Pilih Mata Pelajaran...</p>
                        <span class="card-label">Mata Pelajaran</span>
                    </div>
                </div>
                <div class="selection-card" id="rombelCard" onclick="openRombelModal()">
                    <div class="card-icon rombel-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-content">
                        <p class="card-value" id="rombelValue" style="display: none;"></p>
                        <p class="card-placeholder" id="rombelPlaceholder">Pilih Rombel...</p>
                        <span class="card-label">Rombel</span>
                    </div>
                </div>
            </div>

            <!-- Students Section (hidden initially) -->
            <div id="studentsSection">
                <!-- SECTION HEADER -->
                <div class="section-header">
                    <h2><i class="fas fa-user-graduate"></i> Daftar Nilai Siswa</h2>
                    <span class="badge" id="siswaCount">0 Siswa</span>
                </div>

                <!-- STUDENT CARDS -->
                <div class="students-cards-grid" id="studentsContainer">
                    <!-- Students will be loaded here via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pilih Mapel -->
<div class="modal fade" id="mapelModal" tabindex="-1" aria-labelledby="mapelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapelModalLabel">
                    <i class="fas fa-book me-2"></i> Pilih Mata Pelajaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="mapelOptionsContainer">
                    @if(count($mapelList) > 0)
                        <div class="modal-option-grid">
                            @foreach($mapelList as $mapel)
                                <div class="option-card" onclick="selectMapel({{ $mapel->id }}, '{{ addslashes($mapel->nama_mapel) }}')">
                                    <div class="option-icon mapel">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <p class="option-name">{{ $mapel->nama_mapel }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-book-open empty-icon"></i>
                            <h3>Tidak Ada Mata Pelajaran</h3>
                            <p>Anda belum memiliki penugasan mata pelajaran di periode ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pilih Rombel -->
<div class="modal fade" id="rombelModal" tabindex="-1" aria-labelledby="rombelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rombelModalLabel">
                    <i class="fas fa-users me-2"></i> Pilih Rombel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="rombelOptionsContainer">
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Selected data
    let selectedMapel = null;
    let selectedRombel = null;

    // Open Mapel Modal
    function openMapelModal() {
        const mapelModal = new bootstrap.Modal(document.getElementById('mapelModal'));
        mapelModal.show();
    }

    // Open Rombel Modal
    function openRombelModal() {
        if (!selectedMapel) {
            alert('Silakan pilih mata pelajaran terlebih dahulu!');
            return;
        }
        const rombelModal = new bootstrap.Modal(document.getElementById('rombelModal'));
        rombelModal.show();
        loadRombelOptions();
    }

    // Load Rombel Options
    function loadRombelOptions() {
        const container = document.getElementById('rombelOptionsContainer');
        container.innerHTML = '<div class="loading-spinner"><div class="spinner"></div></div>';

        fetch(`{{ route("guru.input-penilaian.rombel") }}?id_mapel=${selectedMapel.id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    let html = '<div class="modal-option-grid">';
                    data.data.forEach(rombel => {
                        html += `
                            <div class="option-card" onclick="selectRombel(${rombel.id}, '${escapeHtml(rombel.nama_rombel)}')">
                                <div class="option-icon rombel">
                                    <i class="fas fa-users"></i>
                                </div>
                                <p class="option-name">${escapeHtml(rombel.nama_rombel)}</p>
                            </div>
                        `;
                    });
                    html += '</div>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-users-slash empty-icon"></i>
                            <h3>Tidak Ada Rombel</h3>
                            <p>Tidak ada rombel untuk mata pelajaran ini.</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle empty-icon"></i>
                        <h3>Error</h3>
                        <p>Gagal memuat data rombel.</p>
                    </div>
                `;
            });
    }

    // Select Mapel
    function selectMapel(id, name) {
        selectedMapel = { id, name };
        selectedRombel = null;

        // Update UI
        document.getElementById('mapelCard').classList.add('selected');
        document.getElementById('mapelPlaceholder').style.display = 'none';
        document.getElementById('mapelValue').textContent = name;
        document.getElementById('mapelValue').style.display = 'block';

        // Reset rombel
        document.getElementById('rombelCard').classList.remove('selected');
        document.getElementById('rombelPlaceholder').textContent = 'Klik untuk memilih...';
        document.getElementById('rombelPlaceholder').style.display = 'block';
        document.getElementById('rombelValue').style.display = 'none';

        // Hide students section
        document.getElementById('studentsSection').classList.remove('show');

        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('mapelModal')).hide();
    }

    // Select Rombel
    function selectRombel(id, name) {
        selectedRombel = { id, name };

        // Update UI
        document.getElementById('rombelCard').classList.add('selected');
        document.getElementById('rombelPlaceholder').style.display = 'none';
        document.getElementById('rombelValue').textContent = name;
        document.getElementById('rombelValue').style.display = 'block';

        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('rombelModal')).hide();

        // Load students with nilai
        loadNilaiData();
    }

    // Load Nilai Data
    function loadNilaiData() {
        const container = document.getElementById('studentsContainer');
        const section = document.getElementById('studentsSection');
        
        section.classList.add('show');
        container.innerHTML = '<div class="loading-spinner" style="grid-column: 1 / -1;"><div class="spinner"></div></div>';

        fetch(`{{ route("guru.lihat-nilai-selector.nilai-data") }}?id_rombel=${selectedRombel.id}&mapel=${encodeURIComponent(selectedMapel.name)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    document.getElementById('siswaCount').textContent = data.total_siswa + ' Siswa';
                    
                    let html = '';
                    data.data.forEach(siswa => {
                        html += createStudentCard(siswa);
                    });
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `
                        <div class="empty-state" style="grid-column: 1 / -1;">
                            <i class="fas fa-clipboard-list empty-icon"></i>
                            <h3>Belum Ada Data Siswa</h3>
                            <p>Tidak ada siswa yang terdaftar di rombel ${data.nama_rombel || selectedRombel.name}.</p>
                        </div>
                    `;
                    document.getElementById('siswaCount').textContent = '0 Siswa';
                }
            })
            .catch(error => {
                container.innerHTML = `
                    <div class="empty-state" style="grid-column: 1 / -1;">
                        <i class="fas fa-exclamation-triangle empty-icon"></i>
                        <h3>Error</h3>
                        <p>Gagal memuat data nilai.</p>
                    </div>
                `;
            });
    }

    // Create Student Card HTML
    function createStudentCard(siswa) {
        const status = siswa.sudah_dinilai ? 'success' : 'warning';
        const statusText = siswa.sudah_dinilai ? 'Sudah Dinilai' : 'Belum Dinilai';
        const statusIcon = siswa.sudah_dinilai ? 'fa-check-circle' : 'fa-clock';
        
        const avatarContent = siswa.foto_exists
            ? `<img src="${siswa.foto_path}" alt="${escapeHtml(siswa.nama)}">`
            : siswa.initials;

        let statisticsHtml = '';
        let nilaiListHtml = '';
        
        if (siswa.sudah_dinilai && siswa.nilai.length > 0) {
            statisticsHtml = `
                <div class="statistics-row">
                    <div class="stat-item-small">
                        <div class="stat-icon-small"><i class="fas fa-chart-line"></i></div>
                        <div class="stat-info-small">
                            <div class="stat-value-small">${siswa.avg_nilai}</div>
                            <div class="stat-label-small">Rata-rata</div>
                        </div>
                    </div>
                    <div class="stat-item-small">
                        <div class="stat-icon-small"><i class="fas fa-arrow-up"></i></div>
                        <div class="stat-info-small">
                            <div class="stat-value-small">${siswa.max_nilai}</div>
                            <div class="stat-label-small">Tertinggi</div>
                        </div>
                    </div>
                    <div class="stat-item-small">
                        <div class="stat-icon-small"><i class="fas fa-arrow-down"></i></div>
                        <div class="stat-info-small">
                            <div class="stat-value-small">${siswa.min_nilai}</div>
                            <div class="stat-label-small">Terendah</div>
                        </div>
                    </div>
                </div>
            `;
            
            nilaiListHtml = `
                <div class="student-card-body">
                    <div class="nilai-summary-header">
                        <i class="fas fa-history"></i>
                        <span>Riwayat Penilaian (${siswa.nilai.length} sesi)</span>
                    </div>
                    <div class="nilai-list">
                        ${siswa.nilai.map(n => {
                            const editUrl = `{{ route('guru.edit-nilai-siswa') }}?id_rombel=${selectedRombel.id}&mapel=${encodeURIComponent(selectedMapel.name)}&tanggal=${n.tanggal}&nisn=${siswa.nisn}`;
                            return `
                            <div class="nilai-item">
                                <div class="nilai-header">
                                    <span class="nilai-date">
                                        <a href="${editUrl}" class="btn-edit-nilai" title="Edit nilai tanggal ${n.tanggal_formatted}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        ${n.tanggal_formatted}
                                    </span>
                                    <span class="nilai-score">
                                        <span class="score-label">Nilai:</span>
                                        <span class="score-value">${n.nilai}</span>
                                    </span>
                                </div>
                                ${n.materi ? `<div class="nilai-materi">Materi: ${escapeHtml(n.materi.substring(0, 40))}${n.materi.length > 40 ? '...' : ''}</div>` : ''}
                            </div>
                        `}).join('')}
                    </div>
                </div>
            `;
        } else {
            statisticsHtml = `
                <div class="no-statistics">
                    <i class="fas fa-chart-bar"></i>
                    <p>Belum ada data nilai</p>
                </div>
            `;
        }

        const tambahNilaiUrl = `{{ route('guru.tambah-nilai') }}?id_rombel=${selectedRombel.id}&mapel=${encodeURIComponent(selectedMapel.name)}&nisn=${siswa.nisn}&nama_siswa=${encodeURIComponent(siswa.nama)}`;

        return `
            <div class="student-card">
                <div class="student-card-header">
                    <div class="student-avatar">${avatarContent}</div>
                    <div class="student-info">
                        <h4 class="student-name">${escapeHtml(siswa.nama)}</h4>
                        <p class="student-nisn">NISN: ${siswa.nisn}</p>
                    </div>
                    <div class="student-status ${status}">
                        <i class="fas ${statusIcon}"></i>
                        ${statusText}
                    </div>
                </div>
                ${statisticsHtml}
                ${nilaiListHtml}
                <div class="student-card-footer">
                    <a href="${tambahNilaiUrl}" class="btn-tambah-nilai">
                        <i class="fas fa-plus"></i> Tambah Nilai
                    </a>
                </div>
            </div>
        `;
    }

    // Escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
@endpush
