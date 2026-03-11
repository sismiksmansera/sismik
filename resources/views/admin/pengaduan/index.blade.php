@extends('layouts.app')

@section('title', 'Kelola Pengaduan | SISMIK')

@push('styles')
<style>
    /* HEADER */
    .pengaduan-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 30px;
        color: white;
        margin-bottom: 25px;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    }
    .header-content { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
    .header-icon { width: 70px; height: 70px; background: rgba(255,255,255,0.2); border-radius: 16px; display: flex; align-items: center; justify-content: center; }
    .header-icon i { font-size: 32px; }
    .header-title { margin: 0 0 8px 0; font-size: 1.75rem; font-weight: 700; }
    .header-subtitle { margin: 0; opacity: 0.9; }

    /* STAT CARDS */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 15px; margin-bottom: 25px; }
    .stat-card {
        padding: 20px; border-radius: 12px; color: white; text-align: center;
        transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        text-decoration: none; display: block;
    }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); color: white; }
    .stat-card.active { box-shadow: 0 0 0 4px rgba(255,255,255,0.5), 0 8px 25px rgba(0,0,0,0.2); }
    .stat-card .stat-icon { font-size: 28px; margin-bottom: 10px; opacity: 0.9; }
    .stat-card .stat-value { font-size: 2rem; font-weight: 700; }
    .stat-card .stat-label { font-size: 0.85rem; opacity: 0.9; margin-top: 5px; }
    .stat-card.primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .stat-card.warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .stat-card.info { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
    .stat-card.success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .stat-card.secondary { background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%); }

    /* FILTER BAR */
    .filter-bar { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 25px; }
    .filter-form { display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; }
    .filter-group { flex: 1; min-width: 200px; }
    .filter-label { display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 0.85rem; }
    .filter-input, .filter-select { width: 100%; padding: 10px 14px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 0.9rem; }
    .filter-btn { padding: 10px 20px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; }
    .filter-btn.primary { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; }
    .filter-btn.secondary { background: #6b7280; color: white; text-decoration: none; }

    /* DATA TABLE */
    .data-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
    .data-header { padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; }
    .data-header h3 { margin: 0; color: #1f2937; font-size: 1.1rem; display: flex; align-items: center; gap: 10px; }
    .data-badge { background: #3b82f6; color: white; padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
    .data-table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
    .data-table thead tr { background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); }
    .data-table th { padding: 15px; text-align: left; font-weight: 600; color: #374151; border-bottom: 2px solid #e5e7eb; }
    .data-table th.center { text-align: center; }
    .data-table td { padding: 15px; border-bottom: 1px solid #f3f4f6; }
    .data-table tbody tr { transition: background 0.2s; }
    .data-table tbody tr:hover { background: #f8fafc; }
    .data-table tbody tr.new-row { background: #fffbeb; }
    .data-table tbody tr.new-row:hover { background: #fef3c7; }

    /* BADGES */
    .badge-new { display: inline-block; background: #ef4444; color: white; font-size: 0.6rem; padding: 2px 6px; border-radius: 10px; margin-left: 5px; animation: pulse 2s infinite; }
    .badge-status { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
    .badge-kategori { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }

    /* ACTION BUTTONS */
    .btn-action { width: 32px; height: 32px; border-radius: 8px; border: none; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease; color: white; font-size: 12px; }
    .btn-action:hover { transform: translateY(-2px); }
    .btn-action.btn-info { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .btn-action.btn-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .btn-action.btn-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .btn-action.btn-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

    /* MODAL */
    .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; overflow-y: auto; }
    .modal-container { position: relative; margin: 30px auto; max-width: 600px; background: white; border-radius: 16px; overflow: hidden; }
    .modal-container.large { max-width: 800px; }
    .modal-header { padding: 20px 25px; display: flex; justify-content: space-between; align-items: center; }
    .modal-header.primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
    .modal-header.warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; }
    .modal-header h3 { margin: 0; font-size: 1.2rem; display: flex; align-items: center; gap: 10px; }
    .modal-close { background: none; border: none; font-size: 28px; color: inherit; cursor: pointer; opacity: 0.8; }
    .modal-close:hover { opacity: 1; }
    .modal-body { padding: 25px; }
    .modal-footer { padding: 15px 25px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 10px; }

    /* FORM */
    .form-group { margin-bottom: 20px; }
    .form-label { font-weight: 600; display: flex; align-items: center; gap: 8px; margin-bottom: 8px; color: #374151; }
    .form-control { width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 0.95rem; }
    .form-control:focus { border-color: #667eea; outline: none; }
    .btn { padding: 10px 20px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
    .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
    .btn-secondary { background: #6b7280; color: white; }
    .btn-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; }

    /* EMPTY STATE */
    .empty-state { padding: 60px 30px; text-align: center; }
    .empty-icon { width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
    .empty-icon i { font-size: 32px; color: white; }

    /* ANIMATIONS */
    @keyframes pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.7; transform: scale(1.1); } }

    /* DETAIL GRID */
    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .detail-item { padding: 12px; background: #f9fafb; border-radius: 8px; }
    .detail-label { font-size: 0.75rem; color: #6b7280; text-transform: uppercase; margin-bottom: 4px; }
    .detail-value { font-weight: 600; color: #1f2937; }
    .detail-full { grid-column: span 2; }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .filter-form { flex-direction: column; }
        .filter-group { min-width: 100%; }
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .detail-grid { grid-template-columns: 1fr; }
        .detail-full { grid-column: span 1; }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <!-- HEADER -->
        <div class="pengaduan-header">
            <div class="header-content">
                <div class="header-icon"><i class="fas fa-bullhorn"></i></div>
                <div>
                    <h1 class="header-title">Kelola Pengaduan</h1>
                    <p class="header-subtitle">{{ $tahunAktif }} - Semester {{ $semesterAktif }}</p>
                </div>
            </div>
        </div>

        <!-- STATISTICS -->
        <div class="stats-grid">
            <a href="{{ route('admin.pengaduan.index') }}" class="stat-card primary {{ empty($statusFilter) ? 'active' : '' }}">
                <div class="stat-icon"><i class="fas fa-clipboard-list"></i></div>
                <div class="stat-value">{{ $totalPengaduan }}</div>
                <div class="stat-label">Total Pengaduan</div>
            </a>
            <a href="{{ route('admin.pengaduan.index', ['status' => 'Menunggu']) }}" class="stat-card warning {{ $statusFilter === 'Menunggu' ? 'active' : '' }}">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-value">{{ $statusStats['Menunggu'] }}</div>
                <div class="stat-label">Menunggu</div>
            </a>
            <a href="{{ route('admin.pengaduan.index', ['status' => 'Diproses']) }}" class="stat-card info {{ $statusFilter === 'Diproses' ? 'active' : '' }}">
                <div class="stat-icon"><i class="fas fa-spinner"></i></div>
                <div class="stat-value">{{ $statusStats['Diproses'] }}</div>
                <div class="stat-label">Diproses</div>
            </a>
            <a href="{{ route('admin.pengaduan.index', ['status' => 'Ditangani']) }}" class="stat-card success {{ $statusFilter === 'Ditangani' ? 'active' : '' }}">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-value">{{ $statusStats['Ditangani'] }}</div>
                <div class="stat-label">Ditangani</div>
            </a>
            <a href="{{ route('admin.pengaduan.index', ['status' => 'Ditutup']) }}" class="stat-card secondary {{ $statusFilter === 'Ditutup' ? 'active' : '' }}">
                <div class="stat-icon"><i class="fas fa-archive"></i></div>
                <div class="stat-value">{{ $statusStats['Ditutup'] }}</div>
                <div class="stat-label">Ditutup</div>
            </a>
        </div>

        <!-- FILTER BAR -->
        <div class="filter-bar">
            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <label class="filter-label"><i class="fas fa-search"></i> Cari</label>
                    <input type="text" name="search" class="filter-input" value="{{ $search }}" placeholder="Cari pelapor, terlapor, deskripsi...">
                </div>
                <div class="filter-group" style="flex: 0 0 180px;">
                    <label class="filter-label"><i class="fas fa-filter"></i> Kategori</label>
                    <select name="kategori" class="filter-select">
                        <option value="">Semua Kategori</option>
                        <option value="Sarana Prasarana" {{ $kategoriFilter == 'Sarana Prasarana' ? 'selected' : '' }}>Sarana Prasarana</option>
                        <option value="Kekerasan" {{ $kategoriFilter == 'Kekerasan' ? 'selected' : '' }}>Kekerasan</option>
                        <option value="Bullying" {{ $kategoriFilter == 'Bullying' ? 'selected' : '' }}>Bullying</option>
                        <option value="Pelanggaran Aturan" {{ $kategoriFilter == 'Pelanggaran Aturan' ? 'selected' : '' }}>Pelanggaran Aturan</option>
                        <option value="Kegiatan Pembelajaran" {{ $kategoriFilter == 'Kegiatan Pembelajaran' ? 'selected' : '' }}>Kegiatan Pembelajaran</option>
                        <option value="Pelayanan Sekolah" {{ $kategoriFilter == 'Pelayanan Sekolah' ? 'selected' : '' }}>Pelayanan Sekolah</option>
                        <option value="Lainnya" {{ $kategoriFilter == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                @if(!empty($statusFilter))
                <input type="hidden" name="status" value="{{ $statusFilter }}">
                @endif
                <button type="submit" class="filter-btn primary"><i class="fas fa-search"></i> Cari</button>
                <a href="{{ route('admin.pengaduan.index') }}" class="filter-btn secondary"><i class="fas fa-refresh"></i> Reset</a>
            </form>
        </div>

        <!-- DATA TABLE -->
        <div class="data-card">
            <div class="data-header">
                <h3><i class="fas fa-list"></i> Daftar Pengaduan</h3>
                <span class="data-badge">{{ count($pengaduanList) }} Data</span>
            </div>

            @if(count($pengaduanList) > 0)
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pelapor</th>
                            <th>Kategori</th>
                            <th>Terlapor</th>
                            <th>Tanggal</th>
                            <th class="center">Status</th>
                            <th>Diteruskan Ke</th>
                            <th class="center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pengaduanList as $index => $item)
                        @php
                            // Status styling
                            $statusColors = [
                                'Menunggu' => ['bg' => '#fef3c7', 'color' => '#f59e0b'],
                                'Diproses' => ['bg' => '#dbeafe', 'color' => '#3b82f6'],
                                'Ditangani' => ['bg' => '#d1fae5', 'color' => '#10b981'],
                                'Ditutup' => ['bg' => '#e5e7eb', 'color' => '#6b7280'],
                            ];
                            $sc = $statusColors[$item->status] ?? ['bg' => '#f3f4f6', 'color' => '#6b7280'];
                            
                            // Kategori styling
                            $kategoriStyles = [
                                'Sarana Prasarana' => ['icon' => 'fa-building', 'color' => '#667eea'],
                                'Kekerasan' => ['icon' => 'fa-hand-rock', 'color' => '#ef4444'],
                                'Bullying' => ['icon' => 'fa-user-slash', 'color' => '#f59e0b'],
                                'Pelanggaran Aturan' => ['icon' => 'fa-gavel', 'color' => '#8b5cf6'],
                                'Kegiatan Pembelajaran' => ['icon' => 'fa-chalkboard-teacher', 'color' => '#3b82f6'],
                                'Pelayanan Sekolah' => ['icon' => 'fa-concierge-bell', 'color' => '#10b981'],
                            ];
                            $ks = $kategoriStyles[$item->kategori] ?? ['icon' => 'fa-file-alt', 'color' => '#6b7280'];
                        @endphp
                        <tr class="{{ $item->is_new && $item->status === 'Menunggu' ? 'new-row' : '' }}">
                            <td style="color: #6b7280;">
                                {{ $index + 1 }}
                                @if($item->is_new && $item->status === 'Menunggu')
                                <span class="badge-new">BARU</span>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #1f2937;">{{ $item->nama_pelapor }}</div>
                                <div style="font-size: 0.8rem; color: #6b7280;">{{ $item->rombel_pelapor ?? '-' }}</div>
                            </td>
                            <td>
                                <span class="badge-kategori" style="background: {{ $ks['color'] }}15; color: {{ $ks['color'] }};">
                                    <i class="fas {{ $ks['icon'] }}"></i> {{ $item->kategori }}
                                </span>
                            </td>
                            <td style="color: #374151;">{{ $item->subyek_terlapor }}</td>
                            <td style="color: #6b7280;">
                                {{ $item->tanggal_kejadian ? $item->tanggal_kejadian->format('d M Y') : '-' }}
                                @if($item->waktu_kejadian)
                                <br><small>{{ \Carbon\Carbon::parse($item->waktu_kejadian)->format('H:i') }}</small>
                                @endif
                            </td>
                            <td class="center">
                                <span class="badge-status" style="background: {{ $sc['bg'] }}; color: {{ $sc['color'] }};">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td>
                                @if($item->diteruskan_ke)
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <i class="fas fa-share" style="color: #f59e0b; font-size: 12px;"></i>
                                    <span style="font-size: 0.85rem; color: #374151;">{{ $item->diteruskan_ke }}</span>
                                </div>
                                @else
                                <span style="color: #9ca3af; font-size: 0.85rem;">-</span>
                                @endif
                            </td>
                            <td class="center">
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    <button onclick="showDetail({{ $item->id }})" class="btn-action btn-info" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="kelolaPengaduan({{ $item->id }}, '{{ $item->status }}', '{{ addslashes($item->tanggapan ?? '') }}')" class="btn-action btn-success" title="Kelola">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="teruskanPengaduan({{ $item->id }}, '{{ addslashes($item->nama_pelapor) }}', '{{ $item->rombel_aktif ?? $item->rombel_pelapor }}')" class="btn-action btn-warning" title="Teruskan">
                                        <i class="fas fa-share"></i>
                                    </button>
                                    <button onclick="hapusPengaduan({{ $item->id }})" class="btn-action btn-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                <h3 style="margin: 0 0 10px; color: #1f2937; font-weight: 600;">Tidak Ada Pengaduan</h3>
                <p style="margin: 0; color: #6b7280;">
                    @if(!empty($statusFilter) || !empty($kategoriFilter) || !empty($search))
                    Tidak ada pengaduan yang sesuai dengan filter.
                    @else
                    Belum ada pengaduan untuk periode ini.
                    @endif
                </p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- MODAL DETAIL -->
<div class="modal-overlay" id="modalDetail">
    <div class="modal-container large">
        <div class="modal-header primary">
            <h3><i class="fas fa-file-alt"></i> Detail Pengaduan</h3>
            <button class="modal-close" onclick="closeModal('modalDetail')">&times;</button>
        </div>
        <div class="modal-body" id="detailContent">
            <div style="text-align: center; padding: 40px;">
                <i class="fas fa-spinner fa-spin" style="font-size: 40px; color: #667eea;"></i>
                <p style="margin-top: 15px; color: #6b7280;">Memuat data...</p>
            </div>
        </div>
    </div>
</div>

<!-- MODAL KELOLA -->
<div class="modal-overlay" id="modalKelola">
    <div class="modal-container">
        <div class="modal-header primary">
            <h3><i class="fas fa-edit"></i> Kelola Pengaduan</h3>
            <button class="modal-close" onclick="closeModal('modalKelola')">&times;</button>
        </div>
        <form id="formKelola" onsubmit="submitKelola(event)">
            <input type="hidden" name="id" id="kelola_id">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-tasks" style="color: #667eea;"></i> Status Pengaduan</label>
                    <select name="status" id="kelola_status" class="form-control">
                        <option value="Menunggu">⏳ Menunggu</option>
                        <option value="Diproses">🔄 Diproses</option>
                        <option value="Ditangani">✅ Ditangani</option>
                        <option value="Ditutup">📁 Ditutup</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-reply" style="color: #10b981;"></i> Tanggapan</label>
                    <textarea name="tanggapan" id="kelola_tanggapan" class="form-control" rows="5" placeholder="Tulis tanggapan untuk pengaduan ini..."></textarea>
                    <small style="color: #6b7280;">Tanggapan akan ditampilkan kepada pelapor</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalKelola')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TERUSKAN -->
<div class="modal-overlay" id="modalTeruskan">
    <div class="modal-container">
        <div class="modal-header warning">
            <h3><i class="fas fa-share"></i> Teruskan Pengaduan</h3>
            <button class="modal-close" onclick="closeModal('modalTeruskan')">&times;</button>
        </div>
        <form id="formTeruskan" onsubmit="submitTeruskan(event)">
            <input type="hidden" name="id" id="teruskan_id">
            <input type="hidden" name="tujuan_nip" id="teruskan_nip">
            <div class="modal-body">
                <div style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-user" style="color: #3b82f6;"></i>
                        <div>
                            <strong>Pelapor:</strong> <span id="teruskan_pelapor">-</span>
                            <br><small id="teruskan_rombel" style="color: #6b7280;">-</small>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-user-check" style="color: #f59e0b;"></i> Teruskan Kepada</label>
                    <select name="tujuan" id="teruskan_tujuan" class="form-control" required onchange="updateNIP()">
                        <option value="">-- Pilih Tujuan --</option>
                        <optgroup label="📋 Wakil Kepala Sekolah">
                            @foreach($wakaData as $waka)
                                @if(!empty($waka['nama']))
                                <option value="{{ $waka['nama'] }}" data-nip="" data-role="{{ $waka['role'] }}">{{ $waka['role'] }}: {{ $waka['nama'] }}</option>
                                @endif
                            @endforeach
                        </optgroup>
                        <optgroup label="👨‍💼 Guru BK">
                            @foreach($guruBkList as $gbk)
                            <option value="{{ $gbk->nama }}" data-nip="{{ $gbk->nip }}" data-role="Guru BK">{{ $gbk->nama }} ({{ $gbk->nip }})</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="👨‍🏫 Guru / Wali Kelas">
                            @foreach($guruList as $guru)
                            <option value="{{ $guru->nama }}" data-nip="{{ $guru->nip }}" data-role="Guru">{{ $guru->nama }} ({{ $guru->nip }})</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>


                <div style="background: #fef3c7; padding: 12px; border: 1px solid #fbbf24; border-radius: 10px;">
                    <i class="fas fa-info-circle"></i>
                    <small>Pengaduan akan diteruskan dan penerima akan mendapat notifikasi.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalTeruskan')">Batal</button>
                <button type="submit" class="btn btn-warning"><i class="fas fa-share"></i> Teruskan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const pengaduanData = @json($pengaduanList);

function openModal(id) {
    document.getElementById(id).style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
    document.body.style.overflow = '';
}

// Close modal on overlay click
document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) closeModal(this.id);
    });
});

function showDetail(id) {
    openModal('modalDetail');
    
    fetch('{{ route("admin.pengaduan.detail") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ id: id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const p = data.data;
            const buktiUrl = p.bukti_pendukung ? '{{ asset("storage/pengaduan") }}/' + p.bukti_pendukung : null;
            
            document.getElementById('detailContent').innerHTML = `
                <div class="detail-grid">
                    <div class="detail-item"><div class="detail-label">Nama Pelapor</div><div class="detail-value">${p.nama_pelapor}</div></div>
                    <div class="detail-item"><div class="detail-label">Rombel</div><div class="detail-value">${p.rombel_pelapor || '-'}</div></div>
                    <div class="detail-item"><div class="detail-label">Kategori</div><div class="detail-value">${p.kategori}</div></div>
                    <div class="detail-item"><div class="detail-label">Status</div><div class="detail-value">${p.status}</div></div>
                    <div class="detail-item"><div class="detail-label">Subyek Terlapor</div><div class="detail-value">${p.subyek_terlapor}</div></div>
                    <div class="detail-item"><div class="detail-label">Tanggal Kejadian</div><div class="detail-value">${p.tanggal_kejadian || '-'}</div></div>
                    <div class="detail-item"><div class="detail-label">Waktu Kejadian</div><div class="detail-value">${p.waktu_kejadian || '-'}</div></div>
                    <div class="detail-item"><div class="detail-label">Lokasi</div><div class="detail-value">${p.lokasi_kejadian || '-'}</div></div>
                    <div class="detail-item detail-full"><div class="detail-label">Deskripsi</div><div class="detail-value">${p.deskripsi || '-'}</div></div>
                    ${p.tanggapan ? '<div class="detail-item detail-full"><div class="detail-label">Tanggapan</div><div class="detail-value">' + p.tanggapan + '</div></div>' : ''}
                    ${buktiUrl ? '<div class="detail-item detail-full"><div class="detail-label">Bukti Pendukung</div><div class="detail-value"><a href="' + buktiUrl + '" target="_blank"><i class="fas fa-file"></i> Lihat File</a></div></div>' : ''}
                </div>
            `;
        }
    });
}

function kelolaPengaduan(id, status, tanggapan) {
    document.getElementById('kelola_id').value = id;
    document.getElementById('kelola_status').value = status;
    document.getElementById('kelola_tanggapan').value = tanggapan;
    openModal('modalKelola');
}

function submitKelola(e) {
    e.preventDefault();
    const formData = new FormData(document.getElementById('formKelola'));
    
    fetch('{{ route("admin.pengaduan.update") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'Gagal menyimpan');
        }
    });
}

function teruskanPengaduan(id, nama, rombel) {
    document.getElementById('teruskan_id').value = id;
    document.getElementById('teruskan_pelapor').textContent = nama;
    document.getElementById('teruskan_rombel').textContent = rombel || '-';
    document.getElementById('teruskan_tujuan').value = '';
    document.getElementById('teruskan_nip').value = '';
    
    openModal('modalTeruskan');
}



function updateNIP() {
    const sel = document.getElementById('teruskan_tujuan');
    const opt = sel.options[sel.selectedIndex];
    document.getElementById('teruskan_nip').value = opt.dataset.nip || '';
}

function submitTeruskan(e) {
    e.preventDefault();
    
    const tujuan = document.getElementById('teruskan_tujuan').value;
    if (!tujuan || tujuan.trim() === '') {
        alert('Silakan pilih tujuan penerusan');
        return;
    }
    
    const formData = new FormData(document.getElementById('formTeruskan'));
    
    fetch('{{ route("admin.pengaduan.teruskan") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'Gagal meneruskan');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan jaringan');
    });
}

function hapusPengaduan(id) {
    if (!confirm('Yakin ingin menghapus pengaduan ini?')) return;
    
    fetch('{{ route("admin.pengaduan.destroy") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ id: id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'Gagal menghapus');
        }
    });
}
</script>
@endpush
@endsection
