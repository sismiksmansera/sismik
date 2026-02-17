@extends($layout ?? 'layouts.app')

@section('title', 'Tambah Presensi | SISMIK')

<style>
    /* HEADER */
    .tp-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 20px;
        text-align: center;
        color: white;
        box-shadow: 0 10px 40px rgba(16, 185, 129, 0.3);
    }
    .tp-header .header-icon { font-size: 50px; margin-bottom: 10px; opacity: 0.9; }
    .tp-header h1 { font-size: 24px; font-weight: 700; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.1); }

    /* INFO CARDS */
    .tp-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }
    .tp-stat {
        background: #fff;
        padding: 16px;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .tp-stat-icon {
        width: 42px; height: 42px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; color: #fff; flex-shrink: 0;
    }
    .tp-stat-icon.rombel { background: linear-gradient(135deg, #059669, #047857); }
    .tp-stat-icon.tanggal { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .tp-stat-icon.hari { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .tp-stat-icon.periode { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
    .tp-stat-info h3 { margin: 0; font-size: 14px; font-weight: 700; color: #1f2937; }
    .tp-stat-info p { margin: 2px 0 0; font-size: 11px; color: #6b7280; }

    /* BUTTONS */
    .tp-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .btn-back-tp {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 20px;
        background: linear-gradient(135deg, #6b7280, #4b5563);
        color: white; border: none; border-radius: 10px;
        font-weight: 600; font-size: 13px; text-decoration: none;
        transition: all 0.3s ease;
    }
    .btn-back-tp:hover { background: linear-gradient(135deg, #4b5563, #374151); transform: translateY(-2px); color: white; }

    .btn-set-semua {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 20px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white; border: none; border-radius: 10px;
        font-weight: 600; font-size: 13px; cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn-set-semua:hover { background: linear-gradient(135deg, #2563eb, #1d4ed8); transform: translateY(-2px); }
    .btn-hapus-presensi {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 20px;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white; border: none; border-radius: 10px;
        font-weight: 600; font-size: 13px; cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn-hapus-presensi:hover { background: linear-gradient(135deg, #dc2626, #b91c1c); transform: translateY(-2px); }

    /* STUDENT CARDS */
    .tp-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 16px;
    }
    .tp-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        border: 2px solid #e5e7eb;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .tp-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.1); }

    .tp-card-header {
        display: flex; align-items: center; gap: 12px;
        padding: 14px 16px;
        border-bottom: 1px solid #f3f4f6;
    }
    .tp-avatar {
        width: 44px; height: 44px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; overflow: hidden;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border: 2px solid #e5e7eb;
    }
    .tp-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .tp-avatar-initial {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-weight: 700; font-size: 16px;
    }
    .tp-student-info { flex: 1; min-width: 0; }
    .tp-student-name { margin: 0; font-size: 13px; font-weight: 700; color: #1f2937; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .tp-student-nisn { margin: 2px 0 0; font-size: 11px; color: #6b7280; }

    /* JP BUTTONS GRID */
    .tp-jp-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 6px;
        padding: 12px 16px 14px;
    }
    .tp-jp-btn {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        padding: 6px 4px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        background: #f9fafb;
        cursor: pointer;
        transition: all 0.2s ease;
        min-height: 48px;
    }
    .tp-jp-btn:hover { transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .tp-jp-label { font-size: 9px; color: #9ca3af; font-weight: 600; margin-bottom: 2px; }
    .tp-jp-status { font-size: 14px; font-weight: 800; }
    .tp-jp-mapel { font-size: 7px; color: #9ca3af; margin-top: 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; text-align: center; }

    /* JP Status Colors */
    .tp-jp-btn.H { border-color: #22c55e; background: #f0fdf4; }
    .tp-jp-btn.H .tp-jp-status { color: #16a34a; }
    .tp-jp-btn.S { border-color: #f97316; background: #fff7ed; }
    .tp-jp-btn.S .tp-jp-status { color: #ea580c; }
    .tp-jp-btn.I { border-color: #eab308; background: #fefce8; }
    .tp-jp-btn.I .tp-jp-status { color: #ca8a04; }
    .tp-jp-btn.A { border-color: #ef4444; background: #fef2f2; }
    .tp-jp-btn.A .tp-jp-status { color: #dc2626; }
    .tp-jp-btn.D { border-color: #06b6d4; background: #ecfeff; }
    .tp-jp-btn.D .tp-jp-status { color: #0891b2; }
    .tp-jp-btn.B { border-color: #6b7280; background: #f9fafb; }
    .tp-jp-btn.B .tp-jp-status { color: #4b5563; }
    .tp-jp-btn.none { border-color: #e5e7eb; background: #f9fafb; }
    .tp-jp-btn.none .tp-jp-status { color: #d1d5db; }

    /* MODAL */
    .tp-modal-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.5); z-index: 9999;
        align-items: center; justify-content: center;
        padding: 20px;
    }
    .tp-modal-overlay.show { display: flex; }
    .tp-modal {
        background: #fff; border-radius: 16px;
        max-width: 420px; width: 100%;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        animation: modalSlideIn 0.3s ease;
    }
    @keyframes modalSlideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .tp-modal-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e5e7eb;
        display: flex; justify-content: space-between; align-items: center;
    }
    .tp-modal-header h3 { margin: 0; font-size: 16px; font-weight: 700; color: #1f2937; }
    .tp-modal-close {
        width: 32px; height: 32px; border-radius: 50%;
        border: none; background: #f3f4f6; cursor: pointer;
        font-size: 18px; display: flex; align-items: center; justify-content: center;
        transition: all 0.2s;
    }
    .tp-modal-close:hover { background: #ef4444; color: #fff; }
    .tp-modal-body { padding: 20px; }
    .tp-modal-student {
        text-align: center; margin-bottom: 16px;
        padding: 12px; background: #f8fafc; border-radius: 10px;
    }
    .tp-modal-student h4 { margin: 0 0 4px; font-size: 15px; font-weight: 700; color: #1f2937; }
    .tp-modal-student p { margin: 0; font-size: 12px; color: #6b7280; }

    .tp-status-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }
    .tp-status-option {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        padding: 14px 8px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        background: #fff;
        cursor: pointer;
        transition: all 0.2s ease;
        gap: 4px;
    }
    .tp-status-option:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .tp-status-option i { font-size: 22px; }
    .tp-status-option span { font-size: 11px; font-weight: 700; }

    .tp-status-option.hadir { color: #16a34a; border-color: rgba(34,197,94,0.3); }
    .tp-status-option.hadir:hover { background: #f0fdf4; border-color: #22c55e; }
    .tp-status-option.sakit { color: #ea580c; border-color: rgba(249,115,22,0.3); }
    .tp-status-option.sakit:hover { background: #fff7ed; border-color: #f97316; }
    .tp-status-option.izin { color: #ca8a04; border-color: rgba(234,179,8,0.3); }
    .tp-status-option.izin:hover { background: #fefce8; border-color: #eab308; }
    .tp-status-option.alfa { color: #dc2626; border-color: rgba(239,68,68,0.3); }
    .tp-status-option.alfa:hover { background: #fef2f2; border-color: #ef4444; }
    .tp-status-option.dispen { color: #0891b2; border-color: rgba(6,182,212,0.3); }
    .tp-status-option.dispen:hover { background: #ecfeff; border-color: #06b6d4; }
    .tp-status-option.bolos { color: #4b5563; border-color: rgba(107,114,128,0.3); }
    .tp-status-option.bolos:hover { background: #f9fafb; border-color: #6b7280; }

    /* TOAST */
    .tp-toast {
        position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%) translateY(100px);
        padding: 12px 24px; border-radius: 10px;
        color: #fff; font-weight: 600; font-size: 13px;
        z-index: 99999; transition: transform 0.3s ease;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }
    .tp-toast.show { transform: translateX(-50%) translateY(0); }
    .tp-toast.success { background: linear-gradient(135deg, #10b981, #059669); }
    .tp-toast.error { background: linear-gradient(135deg, #ef4444, #dc2626); }

    /* LOADING OVERLAY */
    .tp-loading-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.6); z-index: 99998;
        align-items: center; justify-content: center;
        flex-direction: column; gap: 16px;
    }
    .tp-loading-overlay.show { display: flex; }
    .tp-loading-spinner {
        width: 48px; height: 48px;
        border: 4px solid rgba(255,255,255,0.3);
        border-top-color: #fff;
        border-radius: 50%;
        animation: tpSpin 0.8s linear infinite;
    }
    @keyframes tpSpin { to { transform: rotate(360deg); } }
    .tp-loading-text {
        color: #fff; font-size: 16px; font-weight: 700;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    .tp-loading-progress {
        color: rgba(255,255,255,0.8); font-size: 13px;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .tp-header { padding: 15px; }
        .tp-header .header-icon { font-size: 36px; }
        .tp-header h1 { font-size: 18px; }
        .tp-stats { grid-template-columns: repeat(2, 1fr); gap: 8px; }
        .tp-stat { padding: 10px; gap: 8px; }
        .tp-stat-icon { width: 34px; height: 34px; font-size: 14px; }
        .tp-stat-info h3 { font-size: 11px; }
        .tp-stat-info p { font-size: 9px; }
        .tp-cards { grid-template-columns: 1fr; gap: 12px; }
        .tp-card-header { padding: 10px 12px; }
        .tp-avatar { width: 36px; height: 36px; }
        .tp-student-name { font-size: 12px; }
        .tp-jp-grid { padding: 8px 12px 10px; gap: 4px; }
        .tp-jp-btn { padding: 4px 2px; min-height: 40px; }
        .tp-jp-label { font-size: 8px; }
        .tp-jp-status { font-size: 12px; }
        .tp-jp-mapel { font-size: 6px; }
        .tp-actions { flex-direction: column; gap: 8px; }
        .btn-back-tp, .btn-set-semua, .btn-hapus-presensi { width: 100%; justify-content: center; }
    }
</style>

@section('content')
<div class="layout">
    @if(($routePrefix ?? 'admin') === 'guru_bk')
        @include('layouts.partials.sidebar-guru-bk')
    @else
        @include('layouts.partials.sidebar-admin')
    @endif

    <div class="main-content">
        <!-- Header -->
        <div class="tp-header">
            <div class="header-icon"><i class="fas fa-clipboard-check"></i></div>
            <h1>{{ count($presensiMap) > 0 ? 'EDIT PRESENSI' : 'TAMBAH PRESENSI' }}</h1>
        </div>

        <!-- Info Cards -->
        @php
            $tglObj = \Carbon\Carbon::parse($tanggal);
            $tglFormatted = $tglObj->translatedFormat('d F Y');
        @endphp
        <div class="tp-stats">
            <div class="tp-stat">
                <div class="tp-stat-icon rombel"><i class="fas fa-school"></i></div>
                <div class="tp-stat-info">
                    <h3>{{ $rombel->nama_rombel }}</h3>
                    <p>Rombel</p>
                </div>
            </div>
            <div class="tp-stat">
                <div class="tp-stat-icon tanggal"><i class="fas fa-calendar-day"></i></div>
                <div class="tp-stat-info">
                    <h3>{{ $tglFormatted }}</h3>
                    <p>Tanggal</p>
                </div>
            </div>
            <div class="tp-stat">
                <div class="tp-stat-icon hari"><i class="fas fa-sun"></i></div>
                <div class="tp-stat-info">
                    <h3>{{ $dayName }}</h3>
                    <p>Hari</p>
                </div>
            </div>
            <div class="tp-stat">
                <div class="tp-stat-icon periode"><i class="fas fa-graduation-cap"></i></div>
                <div class="tp-stat-info">
                    <h3>{{ $tahunPelajaran }}</h3>
                    <p>{{ $semesterAktif }}</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="tp-actions">
            <a href="{{ route($routePrefix . '.cek-presensi.index', ['method' => 'tanggal', 'id_rombel' => $idRombel, 'nama_rombel' => $rombel->nama_rombel, 'tanggal' => $tanggal]) }}" class="btn-back-tp">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <button class="btn-set-semua" onclick="openSetSemuaModal()">
                    <i class="fas fa-check-double"></i> Set Semua
                </button>
                @if(($routePrefix ?? 'admin') === 'admin')
                <button class="btn-hapus-presensi" onclick="hapusPresensi()">
                    <i class="fas fa-trash-alt"></i> Hapus Presensi
                </button>
                @endif
            </div>
        </div>

        <!-- Student Cards -->
        <div class="tp-cards">
            @foreach($siswaList as $s)
                @php
                    $fotoExists = !empty($s->foto) && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/' . $s->foto);
                    $fotoPath = $fotoExists ? asset('storage/siswa/' . $s->foto) : '';
                    $namaParts = explode(' ', $s->nama);
                    $initials = '';
                    foreach ($namaParts as $part) {
                        if (!empty($part)) {
                            $initials .= strtoupper(substr($part, 0, 1));
                            if (strlen($initials) >= 2) break;
                        }
                    }
                    $initials = $initials ?: strtoupper(substr($s->nama, 0, 1));
                    $existingJp = $presensiMap[$s->nisn] ?? [];
                @endphp
                <div class="tp-card" data-nisn="{{ $s->nisn }}" data-nama="{{ $s->nama }}">
                    <div class="tp-card-header">
                        <div class="tp-avatar">
                            @if($fotoExists)
                                <img src="{{ $fotoPath }}" alt="{{ $s->nama }}"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="tp-avatar-initial" style="display:none;">{{ $initials }}</div>
                            @else
                                <div class="tp-avatar-initial">{{ $initials }}</div>
                            @endif
                        </div>
                        <div class="tp-student-info">
                            <p class="tp-student-name">{{ $s->nama }}</p>
                            <p class="tp-student-nisn">NISN: {{ $s->nisn }}</p>
                        </div>
                    </div>
                    <div class="tp-jp-grid">
                        @for($jp = 1; $jp <= 10; $jp++)
                            @php
                                $jpStatus = $existingJp["jp_$jp"] ?? null;
                                $jadwal = $jadwalMap[$jp] ?? null;
                                $mapelName = $jadwal['mapel'] ?? '';
                                $guruName = $jadwal['guru'] ?? '';
                                $statusClass = $jpStatus ?: 'none';
                                $statusLabel = $jpStatus ?: '-';
                            @endphp
                            <div class="tp-jp-btn {{ $statusClass }}"
                                 id="jp-{{ $s->nisn }}-{{ $jp }}"
                                 data-jp="{{ $jp }}"
                                 data-mapel="{{ $mapelName }}"
                                 data-guru="{{ $guruName }}"
                                 onclick="openStatusModal('{{ $s->nisn }}', '{{ addslashes($s->nama) }}', {{ $jp }}, '{{ $mapelName }}', '{{ $guruName }}')">
                                <span class="tp-jp-label">JP {{ $jp }}</span>
                                <span class="tp-jp-status">{{ $statusLabel }}</span>
                                @if($mapelName)
                                    <span class="tp-jp-mapel">{{ Str::limit($mapelName, 12) }}</span>
                                @endif
                            </div>
                        @endfor
                    </div>
                </div>
            @endforeach
        </div>

        @if($siswaList->count() === 0)
            <div style="text-align:center; padding:60px 20px;">
                <i class="fas fa-users-slash" style="font-size:48px; color:#d1d5db; margin-bottom:15px;"></i>
                <h4 style="color:#4b5563;">Tidak Ada Siswa</h4>
                <p style="color:#9ca3af;">Tidak ditemukan siswa dalam rombel ini.</p>
            </div>
        @endif
    </div>
</div>

<!-- Status Modal -->
<div class="tp-modal-overlay" id="statusModal">
    <div class="tp-modal">
        <div class="tp-modal-header">
            <h3><i class="fas fa-clipboard-check" style="color:#10b981; margin-right:6px;"></i> Ubah Status Presensi</h3>
            <button class="tp-modal-close" onclick="closeStatusModal()">&times;</button>
        </div>
        <div class="tp-modal-body">
            <div class="tp-modal-student">
                <h4 id="modalStudentName">-</h4>
                <p id="modalJpInfo">JP 1 — Matematika</p>
            </div>
            <div class="tp-status-grid">
                <div class="tp-status-option hadir" onclick="selectStatus('H')">
                    <i class="fas fa-check-circle"></i>
                    <span>Hadir</span>
                </div>
                <div class="tp-status-option sakit" onclick="selectStatus('S')">
                    <i class="fas fa-thermometer-half"></i>
                    <span>Sakit</span>
                </div>
                <div class="tp-status-option izin" onclick="selectStatus('I')">
                    <i class="fas fa-envelope"></i>
                    <span>Izin</span>
                </div>
                <div class="tp-status-option alfa" onclick="selectStatus('A')">
                    <i class="fas fa-times-circle"></i>
                    <span>Alpha</span>
                </div>
                <div class="tp-status-option dispen" onclick="selectStatus('D')">
                    <i class="fas fa-shield-alt"></i>
                    <span>Dispensasi</span>
                </div>
                <div class="tp-status-option bolos" onclick="selectStatus('B')">
                    <i class="fas fa-running"></i>
                    <span>Bolos</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Set Semua Modal -->
<div class="tp-modal-overlay" id="setSemuaModal">
    <div class="tp-modal">
        <div class="tp-modal-header">
            <h3><i class="fas fa-check-double" style="color:#3b82f6; margin-right:6px;"></i> Set Semua Presensi</h3>
            <button class="tp-modal-close" onclick="closeSetSemuaModal()">&times;</button>
        </div>
        <div class="tp-modal-body">
            <div class="tp-modal-student">
                <h4>Semua Siswa & Jam Pelajaran</h4>
                <p>Pilih status yang akan diterapkan ke seluruh siswa pada semua JP yang terjadwal</p>
            </div>
            <div class="tp-status-grid">
                <div class="tp-status-option hadir" onclick="setSemuaStatus('H')">
                    <i class="fas fa-check-circle"></i>
                    <span>Hadir</span>
                </div>
                <div class="tp-status-option sakit" onclick="setSemuaStatus('S')">
                    <i class="fas fa-thermometer-half"></i>
                    <span>Sakit</span>
                </div>
                <div class="tp-status-option izin" onclick="setSemuaStatus('I')">
                    <i class="fas fa-envelope"></i>
                    <span>Izin</span>
                </div>
                <div class="tp-status-option alfa" onclick="setSemuaStatus('A')">
                    <i class="fas fa-times-circle"></i>
                    <span>Alpha</span>
                </div>
                <div class="tp-status-option dispen" onclick="setSemuaStatus('D')">
                    <i class="fas fa-shield-alt"></i>
                    <span>Dispensasi</span>
                </div>
                <div class="tp-status-option bolos" onclick="setSemuaStatus('B')">
                    <i class="fas fa-running"></i>
                    <span>Bolos</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="tp-loading-overlay" id="loadingOverlay">
    <div class="tp-loading-spinner"></div>
    <div class="tp-loading-text" id="loadingText">Menyimpan data...</div>
    <div class="tp-loading-progress" id="loadingProgress"></div>
</div>

<!-- Toast -->
<div class="tp-toast" id="tpToast"></div>

<script>
    // State
    let activeNisn = '';
    let activeNama = '';
    let activeJp = 0;
    let activeMapel = '';
    let activeGuru = '';

    @php $backUrl = route($routePrefix . '.cek-presensi.index', ['method' => 'tanggal', 'id_rombel' => $idRombel, 'nama_rombel' => $rombel->nama_rombel, 'tanggal' => $tanggal]); @endphp
    const storeUrl = @json(route("{$routePrefix}.cek-presensi.store-presensi"));
    const bulkStoreUrl = @json(route("{$routePrefix}.cek-presensi.bulk-store-presensi"));
    const deleteUrl = @json(route("{$routePrefix}.cek-presensi.delete-presensi-by-date"));
    const backUrl = @json($backUrl);
    const csrfToken = '{{ csrf_token() }}';
    const idRombel = '{{ $idRombel }}';
    const tanggal = '{{ $tanggal }}';

    const statusNames = {
        'H': 'Hadir', 'S': 'Sakit', 'I': 'Izin',
        'A': 'Alpha', 'D': 'Dispensasi', 'B': 'Bolos'
    };

    function openStatusModal(nisn, nama, jp, mapel, guru) {
        activeNisn = nisn;
        activeNama = nama;
        activeJp = jp;
        activeMapel = mapel;
        activeGuru = guru;

        document.getElementById('modalStudentName').textContent = nama;
        document.getElementById('modalJpInfo').textContent =
            `JP ${jp}` + (mapel ? ` — ${mapel}` : '') + (guru ? ` (${guru})` : '');
        document.getElementById('statusModal').classList.add('show');
    }

    function closeStatusModal() {
        document.getElementById('statusModal').classList.remove('show');
    }

    // Click overlay to close
    document.getElementById('statusModal').addEventListener('click', function(e) {
        if (e.target === this) closeStatusModal();
    });

    function selectStatus(status) {
        closeStatusModal();
        savePresensi(activeNisn, activeNama, activeJp, status, activeMapel, activeGuru);
    }

    let bulkInProgress = false;
    let bulkTotal = 0;
    let bulkDone = 0;

    function showLoading(text) {
        bulkInProgress = true;
        document.getElementById('loadingText').textContent = text;
        document.getElementById('loadingProgress').textContent = '';
        document.getElementById('loadingOverlay').classList.add('show');
    }
    function updateLoadingProgress() {
        bulkDone++;
        document.getElementById('loadingProgress').textContent = `${bulkDone} / ${bulkTotal}`;
    }
    function hideLoading() {
        bulkInProgress = false;
        document.getElementById('loadingOverlay').classList.remove('show');
    }

    // Prevent navigation during bulk save
    window.addEventListener('beforeunload', function(e) {
        if (bulkInProgress) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    function savePresensi(nisn, nama, jp, status, mapel, guru, silent = false) {
        // Update UI immediately
        const btn = document.getElementById(`jp-${nisn}-${jp}`);
        if (btn) {
            btn.className = `tp-jp-btn ${status}`;
            btn.querySelector('.tp-jp-status').textContent = status;
        }

        // AJAX save — returns Promise
        return fetch(storeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                nisn: nisn,
                nama_siswa: nama,
                tanggal: tanggal,
                id_rombel: idRombel,
                jam_ke: jp,
                status: status,
                mapel: mapel,
                guru: guru
            })
        })
        .then(r => r.json())
        .then(data => {
            if (bulkInProgress) updateLoadingProgress();
            if (!silent) {
                if (data.success) {
                    showToast(`JP ${jp}: ${statusNames[status]} ✓`, 'success');
                } else {
                    showToast(data.message || 'Gagal menyimpan', 'error');
                }
            }
        })
        .catch(() => {
            if (bulkInProgress) updateLoadingProgress();
            if (!silent) showToast('Terjadi kesalahan koneksi', 'error');
        });
    }

    // Auto-set all scheduled JPs to Hadir on page load ONLY when adding new presensi (no existing data)
    const hasExistingData = {{ count($presensiMap) > 0 ? 'true' : 'false' }};
    if (!hasExistingData) {
        document.addEventListener('DOMContentLoaded', function() {
            bulkSaveAll('H', 'Menyimpan presensi awal...', true);
        });
    }

    /**
     * Bulk save: collect all scheduled JPs, update UI, send one request
     * @param {string} status - status to set
     * @param {string} loadingMsg - loading overlay message
     * @param {boolean} onlyEmpty - if true, only set JPs without existing status
     */
    function bulkSaveAll(status, loadingMsg, onlyEmpty = false) {
        const entries = [];
        const cards = document.querySelectorAll('.tp-card');
        cards.forEach(card => {
            const nisn = card.dataset.nisn;
            const nama = card.dataset.nama;
            const btns = card.querySelectorAll('.tp-jp-btn');
            btns.forEach(btn => {
                const jp = parseInt(btn.dataset.jp);
                const mapel = btn.dataset.mapel;
                const guru = btn.dataset.guru;
                if (!mapel) return;
                if (onlyEmpty) {
                    const cur = btn.querySelector('.tp-jp-status').textContent.trim();
                    if (cur !== '-' && cur !== '') return;
                }
                entries.push({ nisn, nama, jp, status, mapel, guru });
                // Update UI immediately
                btn.className = `tp-jp-btn ${status}`;
                btn.querySelector('.tp-jp-status').textContent = status;
            });
        });

        if (entries.length === 0) return;

        showLoading(loadingMsg);
        bulkTotal = entries.length;
        bulkDone = 0;

        fetch(bulkStoreUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                entries: entries,
                tanggal: tanggal,
                id_rombel: idRombel
            })
        })
        .then(r => r.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showToast(`${data.saved} presensi berhasil disimpan ✓`, 'success');
            } else {
                showToast(data.message || 'Gagal menyimpan', 'error');
            }
        })
        .catch(() => {
            hideLoading();
            showToast('Terjadi kesalahan koneksi', 'error');
        });
    }

    // Set Semua Modal
    function openSetSemuaModal() {
        document.getElementById('setSemuaModal').classList.add('show');
    }
    function closeSetSemuaModal() {
        document.getElementById('setSemuaModal').classList.remove('show');
    }
    document.getElementById('setSemuaModal').addEventListener('click', function(e) {
        if (e.target === this) closeSetSemuaModal();
    });

    function setSemuaStatus(status) {
        closeSetSemuaModal();
        bulkSaveAll(status, `Menyimpan ${statusNames[status]} ke semua siswa...`);
    }

    // Hapus Presensi
    function hapusPresensi() {
        if (!confirm('Yakin ingin menghapus SEMUA data presensi pada tanggal ini?\n\nData yang sudah dihapus tidak dapat dikembalikan.')) return;

        fetch(deleteUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                id_rombel: idRombel,
                tanggal: tanggal
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.href = backUrl, 1000);
            } else {
                showToast(data.message || 'Gagal menghapus', 'error');
            }
        })
        .catch(() => showToast('Terjadi kesalahan koneksi', 'error'));
    }

    function showToast(msg, type) {
        const toast = document.getElementById('tpToast');
        toast.textContent = msg;
        toast.className = `tp-toast ${type} show`;
        setTimeout(() => toast.classList.remove('show'), 2500);
    }
</script>
@endsection
