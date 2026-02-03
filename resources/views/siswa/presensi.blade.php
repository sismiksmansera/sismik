@extends('layouts.app')

@section('title', 'Rekap Presensi | SISMIK')

@push('styles')
<style>
    /* Header Card */
    .presensi-header-card {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border-left: 4px solid #10b981;
        margin-bottom: 20px;
    }
    .presensi-header-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
    }
    .presensi-header-details h3 { margin: 0; color: #1f2937; font-size: 18px; font-weight: 600; }
    .presensi-header-details p { margin: 5px 0 0 0; color: #6b7280; font-size: 14px; }

    /* Filter Card */
    .filter-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        margin-bottom: 20px;
    }
    .filter-form {
        display: flex;
        gap: 15px;
        align-items: flex-end;
        flex-wrap: wrap;
    }
    .filter-item { flex: 1; min-width: 130px; }
    .filter-item label { display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 0.85rem; }
    .filter-item input[type="date"] { width: 100%; padding: 10px 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 0.9rem; }
    .btn-filter {
        padding: 10px 20px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .btn-filter:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3); }

    /* Stats Grid */
    .stats-grid-horizontal {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 5px;
    }
    .stat-item-h {
        flex: 1;
        min-width: 80px;
        background: white;
        border-radius: 12px;
        padding: 12px 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .stat-item-h:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12); }
    .stat-icon-h {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        color: white;
    }
    .stat-icon-h.hadir { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-icon-h.dispen { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .stat-icon-h.izin { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
    .stat-icon-h.sakit { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .stat-icon-h.alfa { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .stat-icon-h.bolos { background: linear-gradient(135deg, #6b7280, #4b5563); }
    .stat-value-h { font-size: 1.1rem; font-weight: 700; color: #1f2937; }
    .stat-label-h { font-size: 0.7rem; color: #6b7280; white-space: nowrap; }

    /* Rekap Section */
    .rekap-section {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    .section-header {
        padding: 20px 25px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .section-header h2 { margin: 0; font-size: 1.1rem; color: #1f2937; display: flex; align-items: center; gap: 10px; }
    
    /* Mapel Card */
    .mapel-list { padding: 20px; display: grid; gap: 15px; }
    .mapel-card {
        background: #f8fafc;
        border-radius: 12px;
        padding: 15px 20px;
        border-left: 4px solid #10b981;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .mapel-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1); background: #f0f4ff !important; }
    .mapel-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
    .mapel-info { flex: 1; min-width: 200px; }
    .mapel-info h4 { margin: 0 0 8px 0; color: #1f2937; font-size: 1rem; font-weight: 600; }
    .mapel-badges { display: flex; flex-wrap: wrap; gap: 6px; }
    .badge-stat { padding: 3px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .badge-stat.hadir { background: #dcfce7; color: #15803d; }
    .badge-stat.dispen { background: #dbeafe; color: #1e40af; }
    .badge-stat.izin { background: #ede9fe; color: #5b21b6; }
    .badge-stat.sakit { background: #fef3c7; color: #92400e; }
    .badge-stat.alfa { background: #fee2e2; color: #991b1b; }
    .badge-stat.bolos { background: #f3f4f6; color: #4b5563; }
    
    .persen-circle {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
    }
    .persen-circle.high { background: #dcfce7; color: #15803d; }
    .persen-circle.medium { background: #fef3c7; color: #92400e; }
    .persen-circle.low { background: #fee2e2; color: #991b1b; }

    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    .modal-content {
        background: white;
        border-radius: 16px;
        width: 90%;
        max-width: 700px;
        max-height: 85vh;
        overflow: hidden;
    }
    .modal-header {
        background: linear-gradient(135deg, #10b981, #059669);
        padding: 20px 25px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-header h3 { margin: 0; font-size: 1.15rem; display: flex; align-items: center; gap: 10px; }
    .modal-body { max-height: 60vh; overflow-y: auto; padding: 20px 25px; }
    .modal-footer { padding: 15px 25px; border-top: 1px solid #e5e7eb; text-align: right; background: #f8fafc; }
    .btn-close {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 35px;
        height: 35px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1.2rem;
    }
    .btn-secondary { padding: 10px 20px; background: #f3f4f6; color: #4b5563; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }

    /* Detail Item */
    .detail-item {
        padding: 12px 15px;
        background: #f8fafc;
        border-radius: 10px;
        margin-bottom: 10px;
        border-left: 3px solid #10b981;
    }
    .detail-item .row { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
    .detail-mapel { font-weight: 600; color: #1f2937; }
    .detail-meta { font-size: 0.8rem; color: #6b7280; }

    /* Empty State */
    .empty-state { text-align: center; padding: 50px 30px; }
    .empty-icon { width: 80px; height: 80px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
    .empty-icon i { font-size: 32px; color: white; }

    @media (max-width: 768px) {
        .filter-form { flex-direction: column; }
        .filter-item { width: 100%; }
        .stats-grid-horizontal { gap: 8px; }
        .stat-item-h { min-width: 70px; padding: 10px 8px; }
        .stat-value-h { font-size: 0.95rem; }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-siswa')

    <div class="main-content">
        <!-- Header -->
        <div class="presensi-header-card">
            <div class="presensi-header-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="presensi-header-details">
                <h3>{{ $siswa->nama }}</h3>
                <p>Rekap Presensi Saya</p>
                @if($periodik)
                <p style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                    Periode: {{ $periodik->tahun_pelajaran }} - {{ $periodik->semester }}
                </p>
                @endif
            </div>
        </div>

        @if($periodik)
        <!-- Filter -->
        <div class="filter-card">
            <form method="GET" class="filter-form">
                <div class="filter-item">
                    <label><i class="fas fa-calendar"></i> Dari Tanggal</label>
                    <input type="date" name="tanggal_mulai" id="tanggalMulai" value="{{ $tanggalMulai }}" min="{{ $minDate }}" max="{{ $maxDate }}">
                </div>
                <div class="filter-item">
                    <label><i class="fas fa-calendar"></i> Sampai Tanggal</label>
                    <input type="date" name="tanggal_selesai" id="tanggalSelesai" value="{{ $tanggalSelesai }}" min="{{ $minDate }}" max="{{ $maxDate }}">
                </div>
                <button type="submit" class="btn-filter">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </form>
        </div>

        <!-- Stats -->
        <div class="stats-grid-horizontal">
            <div class="stat-item-h" onclick="showDetailKategori('H', 'Hadir')">
                <div class="stat-icon-h hadir"><i class="fas fa-check-circle"></i></div>
                <div class="stat-value-h">{{ $totalHadir }}</div>
                <div class="stat-label-h">Hadir</div>
            </div>
            <div class="stat-item-h" onclick="showDetailKategori('D', 'Dispen')">
                <div class="stat-icon-h dispen"><i class="fas fa-user-tie"></i></div>
                <div class="stat-value-h">{{ $totalDispen }}</div>
                <div class="stat-label-h">Dispen</div>
            </div>
            <div class="stat-item-h" onclick="showDetailKategori('I', 'Izin')">
                <div class="stat-icon-h izin"><i class="fas fa-clock"></i></div>
                <div class="stat-value-h">{{ $totalIzin }}</div>
                <div class="stat-label-h">Izin</div>
            </div>
            <div class="stat-item-h" onclick="showDetailKategori('S', 'Sakit')">
                <div class="stat-icon-h sakit"><i class="fas fa-first-aid"></i></div>
                <div class="stat-value-h">{{ $totalSakit }}</div>
                <div class="stat-label-h">Sakit</div>
            </div>
            <div class="stat-item-h" onclick="showDetailKategori('A', 'Alfa')">
                <div class="stat-icon-h alfa"><i class="fas fa-times-circle"></i></div>
                <div class="stat-value-h">{{ $totalAlfa }}</div>
                <div class="stat-label-h">Alfa</div>
            </div>
            <div class="stat-item-h" onclick="showDetailKategori('B', 'Bolos')">
                <div class="stat-icon-h bolos"><i class="fas fa-running"></i></div>
                <div class="stat-value-h">{{ $totalBolos }}</div>
                <div class="stat-label-h">Bolos</div>
            </div>
        </div>
        @endif

        <!-- Rekap Per Mapel -->
        @if($rekapMapel->count() > 0)
        <div class="rekap-section">
            <div class="section-header">
                <h2><i class="fas fa-book" style="color: #10b981;"></i> Rekap Per Mata Pelajaran</h2>
            </div>
            <div class="mapel-list">
                @foreach($rekapMapel as $mapel)
                    @php
                        $persen = $mapel->total_presensi > 0 ? round(($mapel->hadir / $mapel->total_presensi) * 100, 1) : 0;
                        $persenClass = $persen >= 90 ? 'high' : ($persen >= 75 ? 'medium' : 'low');
                    @endphp
                    <div class="mapel-card" onclick="showDetailMapel('{{ $mapel->mata_pelajaran }}')">
                        <div class="mapel-header">
                            <div class="mapel-info">
                                <h4>{{ $mapel->mata_pelajaran }}</h4>
                                <div class="mapel-badges">
                                    <span class="badge-stat hadir"><i class="fas fa-check"></i> H: {{ $mapel->hadir }}</span>
                                    <span class="badge-stat dispen"><i class="fas fa-user-tie"></i> D: {{ $mapel->dispen }}</span>
                                    <span class="badge-stat izin"><i class="fas fa-clock"></i> I: {{ $mapel->izin }}</span>
                                    <span class="badge-stat sakit"><i class="fas fa-first-aid"></i> S: {{ $mapel->sakit }}</span>
                                    <span class="badge-stat alfa"><i class="fas fa-times"></i> A: {{ $mapel->alfa }}</span>
                                    @if($mapel->bolos > 0)
                                    <span class="badge-stat bolos"><i class="fas fa-running"></i> B: {{ $mapel->bolos }}</span>
                                    @endif
                                </div>
                            </div>
                            <div style="text-align: center;">
                                <div class="persen-circle {{ $persenClass }}">{{ $persen }}%</div>
                                <div style="font-size: 0.7rem; color: #6b7280; margin-top: 4px;">Kehadiran</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="rekap-section">
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-calendar-check"></i></div>
                <h3>Belum Ada Data Presensi</h3>
                <p>Tidak ada data presensi untuk periode yang dipilih.</p>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal Detail -->
<div class="modal-overlay" id="modalDetail">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-list-alt"></i> <span id="modalTitle">Rincian Presensi</span></h3>
            <button class="btn-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body" id="modalBody">
            <p style="text-align: center; color: #6b7280;">Memuat data...</p>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeModal()">Tutup</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showDetailKategori(kode, nama) {
        document.getElementById('modalTitle').textContent = 'Rincian Presensi: ' + nama;
        document.getElementById('modalBody').innerHTML = '<p style="text-align: center; color: #6b7280;">Memuat data...</p>';
        document.getElementById('modalDetail').style.display = 'flex';
        
        fetch(`{{ route('siswa.presensi.detail') }}?filter_type=kategori&filter_value=${kode}&tanggal_mulai=${document.getElementById('tanggalMulai').value}&tanggal_selesai=${document.getElementById('tanggalSelesai').value}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    let html = '';
                    data.data.forEach(item => {
                        html += `<div class="detail-item">
                            <div class="row">
                                <div>
                                    <div class="detail-mapel">${item.mapel}</div>
                                    <div class="detail-meta"><i class="fas fa-user"></i> ${item.guru || '-'} | ${item.jam || '-'}</div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-weight: 600; color: #1f2937;">${item.hari}, ${item.tanggal}</div>
                                </div>
                            </div>
                        </div>`;
                    });
                    document.getElementById('modalBody').innerHTML = html;
                } else {
                    document.getElementById('modalBody').innerHTML = '<p style="text-align: center; color: #6b7280;">Tidak ada data.</p>';
                }
            })
            .catch(err => {
                document.getElementById('modalBody').innerHTML = '<p style="text-align: center; color: #ef4444;">Gagal memuat data.</p>';
            });
    }
    
    function showDetailMapel(mapel) {
        document.getElementById('modalTitle').textContent = 'Rincian: ' + mapel;
        document.getElementById('modalBody').innerHTML = '<p style="text-align: center; color: #6b7280;">Memuat data...</p>';
        document.getElementById('modalDetail').style.display = 'flex';
        
        fetch(`{{ route('siswa.presensi.detail') }}?filter_type=mapel&filter_value=${encodeURIComponent(mapel)}&tanggal_mulai=${document.getElementById('tanggalMulai').value}&tanggal_selesai=${document.getElementById('tanggalSelesai').value}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    let html = '';
                    data.data.forEach(item => {
                        const statusColors = {
                            'H': {bg: '#dcfce7', text: '#15803d', label: 'Hadir'},
                            'D': {bg: '#dbeafe', text: '#1e40af', label: 'Dispen'},
                            'I': {bg: '#ede9fe', text: '#5b21b6', label: 'Izin'},
                            'S': {bg: '#fef3c7', text: '#92400e', label: 'Sakit'},
                            'A': {bg: '#fee2e2', text: '#991b1b', label: 'Alfa'},
                            'B': {bg: '#f3f4f6', text: '#4b5563', label: 'Bolos'}
                        };
                        const status = statusColors[item.presensi] || {bg: '#f3f4f6', text: '#6b7280', label: item.presensi};
                        html += `<div class="detail-item" style="border-left-color: ${status.text};">
                            <div class="row">
                                <div>
                                    <div class="detail-mapel">${item.hari}, ${item.tanggal}</div>
                                    <div class="detail-meta"><i class="fas fa-user"></i> ${item.guru || '-'} | ${item.jam || '-'}</div>
                                </div>
                                <div>
                                    <span style="padding: 4px 12px; background: ${status.bg}; color: ${status.text}; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">${status.label}</span>
                                </div>
                            </div>
                        </div>`;
                    });
                    document.getElementById('modalBody').innerHTML = html;
                } else {
                    document.getElementById('modalBody').innerHTML = '<p style="text-align: center; color: #6b7280;">Tidak ada data.</p>';
                }
            })
            .catch(err => {
                document.getElementById('modalBody').innerHTML = '<p style="text-align: center; color: #ef4444;">Gagal memuat data.</p>';
            });
    }
    
    function closeModal() {
        document.getElementById('modalDetail').style.display = 'none';
    }
    
    // Close modal on outside click
    document.getElementById('modalDetail').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>
@endpush
