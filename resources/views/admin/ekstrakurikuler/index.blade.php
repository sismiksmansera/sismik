@extends('layouts.app')

@section('title', 'Manajemen Ekstrakurikuler | SISMIK')

@push('styles')
<style>
    /* Header */
    .content-header {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        border-radius: 16px;
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
    }
    .header-content { display: flex; align-items: center; gap: 1.5rem; }
    .header-icon {
        width: 60px; height: 60px;
        background: rgba(255,255,255,0.2);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
    }
    .header-text h1 { margin: 0 0 0.5rem 0; font-size: 1.75rem; font-weight: 700; }
    .header-subtitle { display: flex; gap: 1.5rem; font-size: 0.875rem; opacity: 0.9; }
    .header-actions { display: flex; gap: 0.75rem; }
    
    /* Filter Card */
    .filter-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.07);
        margin-bottom: 2rem;
    }
    .filter-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }
    .filter-title { display: flex; align-items: center; gap: 0.75rem; }
    .filter-title h3 { margin: 0; color: #1f2937; font-size: 1.125rem; }
    .filter-title i { color: #3b82f6; }
    .filter-form { display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap; }
    .filter-group label { display: block; font-size: 12px; font-weight: 600; color: #4b5563; margin-bottom: 5px; }
    .filter-control {
        padding: 10px 15px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        min-width: 180px;
    }
    
    /* Content Section */
    .content-section {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.07);
    }
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .section-title { display: flex; align-items: center; gap: 0.75rem; }
    .section-title i { color: #3b82f6; }
    .section-title h2 { margin: 0; font-size: 1.125rem; color: #1f2937; }
    .badge-count {
        background: #3b82f6;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }
    
    /* Ekstrakurikuler Grid */
    .ekstra-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.25rem;
    }
    
    /* Ekstra Card */
    .ekstra-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .ekstra-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    }
    .ekstra-card.active { border-color: #10b981; border-width: 2px; }
    
    .ekstra-status {
        padding: 8px 12px;
        background: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
    }
    .status-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .status-badge.active { background: #d1fae5; color: #10b981; }
    .status-badge.inactive { background: #f3f4f6; color: #6b7280; }
    
    .ekstra-header {
        padding: 1rem;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    .ekstra-icon {
        width: 48px; height: 48px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: white;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .ekstra-title h3 { margin: 0 0 6px 0; font-size: 1rem; font-weight: 600; color: #1f2937; }
    .ekstra-meta { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
    .meta-item { font-size: 12px; color: #6b7280; display: flex; align-items: center; gap: 4px; }
    .meta-badge {
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 500;
    }
    .semester-ganjil { background: #dbeafe; color: #1d4ed8; }
    .semester-genap { background: #fce7f3; color: #db2777; }
    
    .ekstra-info { padding: 0 1rem 1rem; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px; }
    .info-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px;
        background: #f8fafc;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .info-item:hover { background: #f1f5f9; }
    .info-icon {
        width: 36px; height: 36px;
        background: rgba(59,130,246,0.1);
        color: #3b82f6;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
    }
    .info-value { font-size: 1.125rem; font-weight: 700; color: #1f2937; }
    .info-label { font-size: 11px; color: #6b7280; }
    
    .pembina-list {
        background: #fffbeb;
        border: 1px solid #fcd34d;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 12px;
    }
    .pembina-label { color: #92400e; font-weight: 600; margin-bottom: 4px; display: flex; align-items: center; gap: 6px; }
    .pembina-names { color: #78350f; }
    
    .ekstra-actions {
        padding: 12px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }
    .btn-action {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        text-decoration: none;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }
    .btn-action:hover { transform: translateY(-1px); }
    .btn-action.btn-edit { background: #dbeafe; color: #1d4ed8; }
    .btn-action.btn-danger { background: #fee2e2; color: #dc2626; }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    .empty-state i { font-size: 64px; color: #d1d5db; margin-bottom: 20px; }
    .empty-state h3 { margin: 0 0 10px; color: #374151; }
    .empty-state p { color: #6b7280; margin-bottom: 20px; }
    
    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.5);
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    .modal-content {
        background: white;
        padding: 30px;
        border-radius: 16px;
        width: 420px;
        max-width: 90%;
        text-align: center;
    }
    .modal-icon { font-size: 48px; color: #f59e0b; margin-bottom: 15px; }
    .modal-actions { display: flex; gap: 12px; justify-content: center; margin-top: 20px; }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <!-- Header -->
        <div class="content-header">
            <div class="header-content">
                <div class="header-icon">
                    <i class="fas fa-futbol"></i>
                </div>
                <div class="header-text">
                    <h1>Manajemen Ekstrakurikuler</h1>
                    <div class="header-subtitle">
                        <span><i class="fas fa-calendar-alt"></i> Periode Aktif: <strong>{{ $tahunAktif }} - {{ $semesterAktif }}</strong></span>
                        <span><i class="fas fa-chart-line"></i> {{ $totalAktif }} Ekstrakurikuler Aktif</span>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <button type="button" id="btnSalinEkstra" class="btn btn-secondary" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
                    <i class="fas fa-copy"></i> Salin dari Semester Lain
                </button>
                <a href="{{ route('admin.ekstrakurikuler.create') }}" class="btn btn-primary" style="background: white; color: #3b82f6;">
                    <i class="fas fa-plus"></i> Tambah Baru
                </a>
            </div>
        </div>

        <!-- Filter -->
        <div class="filter-card">
            <div class="filter-header">
                <div class="filter-title">
                    <i class="fas fa-filter"></i>
                    <h3>Filter Data</h3>
                </div>
                <span class="badge-count">{{ count($ekstrakurikulerList) }} Data</span>
            </div>
            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <label><i class="fas fa-calendar"></i> Tahun Pelajaran</label>
                    <select name="tahun" class="filter-control">
                        @foreach($allYears as $y)
                            <option value="{{ $y }}" {{ $y == $filterTahun ? 'selected' : '' }}>
                                {{ $y }} {{ $y == $tahunAktif ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-layer-group"></i> Semester</label>
                    <select name="semester" class="filter-control">
                        <option value="Ganjil" {{ $filterSemester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap" {{ $filterSemester == 'Genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Terapkan Filter
                </button>
                @if($filterTahun != $tahunAktif || $filterSemester != $semesterAktif)
                    <a href="{{ route('admin.ekstrakurikuler.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Content -->
        <div class="content-section">
            <div class="section-header">
                <div class="section-title">
                    <i class="fas fa-list"></i>
                    <h2>Daftar Ekstrakurikuler</h2>
                </div>
            </div>

            @if(count($ekstrakurikulerList) > 0)
                @php
                    $icons = [
                        'Pramuka' => 'fa-campground',
                        'Paskibra' => 'fa-flag',
                        'PMR' => 'fa-heartbeat',
                        'OSIS' => 'fa-users-cog',
                        'Basket' => 'fa-basketball-ball',
                        'Futsal' => 'fa-futbol',
                        'Voli' => 'fa-volleyball-ball',
                        'Seni Musik' => 'fa-music',
                        'Seni Tari' => 'fa-gem',
                        'English Club' => 'fa-language',
                        'Japanese Club' => 'fa-language',
                        'IT Club' => 'fa-laptop-code',
                        'KIR' => 'fa-flask',
                        'Paduan Suara' => 'fa-microphone-alt'
                    ];
                    $colors = [
                        'Pramuka' => '#3b82f6',
                        'Paskibra' => '#ef4444',
                        'PMR' => '#dc2626',
                        'OSIS' => '#8b5cf6',
                        'Basket' => '#f59e0b',
                        'Futsal' => '#10b981',
                        'Voli' => '#ec4899',
                        'Seni Musik' => '#06b6d4',
                        'Seni Tari' => '#f97316',
                        'English Club' => '#6366f1',
                        'Japanese Club' => '#8b5cf6',
                        'IT Club' => '#0ea5e9',
                        'KIR' => '#84cc16',
                        'Paduan Suara' => '#d946ef'
                    ];
                @endphp
                <div class="ekstra-grid">
                    @foreach($ekstrakurikulerList as $ekstra)
                        @php
                            $isAktif = ($ekstra->tahun_pelajaran == $tahunAktif && $ekstra->semester == $semesterAktif);
                            $icon = $icons[$ekstra->nama_ekstrakurikuler] ?? 'fa-star';
                            $color = $colors[$ekstra->nama_ekstrakurikuler] ?? '#6b7280';
                            $pembinaList = array_filter([$ekstra->pembina_1, $ekstra->pembina_2, $ekstra->pembina_3]);
                        @endphp
                        <div class="ekstra-card {{ $isAktif ? 'active' : '' }}">
                            <div class="ekstra-status">
                                @if($isAktif)
                                    <span class="status-badge active"><i class="fas fa-check-circle"></i> Aktif</span>
                                @else
                                    <span class="status-badge inactive"><i class="fas fa-clock"></i> Arsip</span>
                                @endif
                            </div>
                            
                            <div class="ekstra-header">
                                <div class="ekstra-icon" style="background: {{ $color }};">
                                    <i class="fas {{ $icon }}"></i>
                                </div>
                                <div class="ekstra-title">
                                    <h3>{{ $ekstra->nama_ekstrakurikuler }}</h3>
                                    <div class="ekstra-meta">
                                        <span class="meta-item"><i class="fas fa-calendar-alt"></i> {{ $ekstra->tahun_pelajaran }}</span>
                                        <span class="meta-badge semester-{{ strtolower($ekstra->semester) }}">{{ $ekstra->semester }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="ekstra-info">
                                <div class="info-grid">
                                    <a href="{{ url('admin/anggota_ekstrakurikuler.php?id=' . $ekstra->id) }}" class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <div>
                                            <div class="info-value">{{ $ekstra->jumlah_anggota }}</div>
                                            <div class="info-label">Anggota</div>
                                        </div>
                                    </a>
                                    <a href="{{ url('admin/lihat_prestasi_admin.php?type=ekstra&id=' . $ekstra->id) }}" class="info-item">
                                        <div class="info-icon" style="background: rgba(245,158,11,0.1); color: #f59e0b;">
                                            <i class="fas fa-trophy"></i>
                                        </div>
                                        <div>
                                            <div class="info-value">{{ $ekstra->jumlah_prestasi }}</div>
                                            <div class="info-label">Prestasi</div>
                                        </div>
                                    </a>
                                </div>
                                
                                @if(!empty($pembinaList))
                                    <div class="pembina-list">
                                        <div class="pembina-label"><i class="fas fa-user-tie"></i> Pembina:</div>
                                        <div class="pembina-names">{{ implode(', ', $pembinaList) }}</div>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="ekstra-actions">
                                <a href="{{ route('admin.ekstrakurikuler.edit', $ekstra->id) }}" class="btn-action btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button onclick="confirmDelete({{ $ekstra->id }}, '{{ addslashes($ekstra->nama_ekstrakurikuler) }}')" class="btn-action btn-danger">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-futbol"></i>
                    <h3>Tidak Ada Data Ekstrakurikuler</h3>
                    <p>Belum ada data ekstrakurikuler untuk periode yang dipilih</p>
                    <a href="{{ route('admin.ekstrakurikuler.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Ekstrakurikuler
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-content">
        <div class="modal-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <h3 style="margin: 0 0 10px; color: #1f2937;">Konfirmasi Hapus</h3>
        <p style="color: #6b7280; margin-bottom: 10px;">Apakah Anda yakin ingin menghapus ekstrakurikuler ini?</p>
        <div style="background: #fef3c7; padding: 10px; border-radius: 8px; font-size: 12px; color: #92400e;">
            <i class="fas fa-exclamation-circle"></i> Penghapusan akan menghapus semua data anggota terkait.
        </div>
        <div class="modal-actions">
            <button onclick="closeDeleteModal()" class="btn btn-secondary">Batal</button>
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-primary" style="background: #ef4444;">Hapus</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Salin -->
<div class="modal-overlay" id="salinModal">
    <div class="modal-content" style="text-align: left; width: 500px;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
            <div style="width: 48px; height: 48px; background: #dbeafe; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #3b82f6;">
                <i class="fas fa-copy"></i>
            </div>
            <div>
                <h3 style="margin: 0; color: #1f2937;">Salin Ekstrakurikuler</h3>
                <p style="margin: 0; font-size: 12px; color: #6b7280;">Salin data dari semester sebelumnya ke periode aktif</p>
            </div>
            <button onclick="closeSalinModal()" style="margin-left: auto; background: none; border: none; font-size: 24px; color: #6b7280; cursor: pointer;">&times;</button>
        </div>
        
        <div style="background: #f0f9ff; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
            <strong>Periode Tujuan:</strong> {{ $tahunAktif }} - {{ $semesterAktif }}
        </div>
        
        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Pilih Periode Sumber:</label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <label style="font-size: 12px; color: #6b7280;">Tahun Pelajaran</label>
                    <select id="sourceTahun" class="filter-control" style="width: 100%; margin-top: 4px;">
                        <option value="">-- Pilih Tahun --</option>
                        @foreach($allYears as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 12px; color: #6b7280;">Semester</label>
                    <select id="sourceSemester" class="filter-control" style="width: 100%; margin-top: 4px;">
                        <option value="">-- Pilih Semester --</option>
                        <option value="Ganjil">Ganjil</option>
                        <option value="Genap">Genap</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Opsi Penyalinan:</label>
            <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; cursor: pointer;">
                <input type="checkbox" id="salinPembina" checked>
                <span>Salin Data Pembina</span>
            </label>
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" id="salinAnggota" checked>
                <span>Salin Data Anggota</span>
            </label>
        </div>
        
        <div id="previewSalin" style="display: none; background: #f8fafc; padding: 12px; border-radius: 8px; margin-bottom: 16px; max-height: 150px; overflow-y: auto;">
            <strong style="font-size: 12px;">Preview Data:</strong>
            <div id="previewContent" style="margin-top: 8px;"></div>
        </div>
        
        <div style="display: flex; gap: 12px; justify-content: flex-end;">
            <button onclick="closeSalinModal()" class="btn btn-secondary">Batal</button>
            <button id="btnConfirmSalin" class="btn btn-primary" style="background: #10b981;" disabled>
                <i class="fas fa-copy"></i> Salin ke Periode Aktif
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(id, nama) {
        document.getElementById('deleteForm').action = '{{ url("admin/ekstrakurikuler") }}/' + id;
        document.getElementById('deleteModal').style.display = 'flex';
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }
    
    document.getElementById('deleteModal').addEventListener('click', (e) => {
        if (e.target.id === 'deleteModal') closeDeleteModal();
    });
    
    // Salin Modal
    document.getElementById('btnSalinEkstra').addEventListener('click', () => {
        document.getElementById('salinModal').style.display = 'flex';
    });
    
    function closeSalinModal() {
        document.getElementById('salinModal').style.display = 'none';
    }
    
    document.getElementById('salinModal').addEventListener('click', (e) => {
        if (e.target.id === 'salinModal') closeSalinModal();
    });
    
    // Preview on select change
    document.getElementById('sourceTahun').addEventListener('change', loadPreview);
    document.getElementById('sourceSemester').addEventListener('change', loadPreview);
    
    function loadPreview() {
        const tahun = document.getElementById('sourceTahun').value;
        const semester = document.getElementById('sourceSemester').value;
        
        if (tahun && semester) {
            fetch(`{{ route('admin.ekstrakurikuler.preview-copy') }}?tahun=${encodeURIComponent(tahun)}&semester=${encodeURIComponent(semester)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.length > 0) {
                        const html = data.map(e => `<div style="font-size:12px;padding:4px 0;border-bottom:1px solid #e5e7eb;">${e.nama_ekstrakurikuler}</div>`).join('');
                        document.getElementById('previewContent').innerHTML = html;
                        document.getElementById('previewSalin').style.display = 'block';
                        document.getElementById('btnConfirmSalin').disabled = false;
                    } else {
                        document.getElementById('previewContent').innerHTML = '<div style="color:#6b7280;font-size:12px;">Tidak ada data</div>';
                        document.getElementById('previewSalin').style.display = 'block';
                        document.getElementById('btnConfirmSalin').disabled = true;
                    }
                });
        }
    }
    
    // Confirm copy
    document.getElementById('btnConfirmSalin').addEventListener('click', function() {
        const tahun = document.getElementById('sourceTahun').value;
        const semester = document.getElementById('sourceSemester').value;
        const salinPembina = document.getElementById('salinPembina').checked;
        const salinAnggota = document.getElementById('salinAnggota').checked;
        
        fetch('{{ route("admin.ekstrakurikuler.copy") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                source_tahun: tahun,
                source_semester: semester,
                salin_pembina: salinPembina,
                salin_anggota: salinAnggota
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            }
        });
    });
</script>
@endpush
