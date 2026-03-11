@extends('layouts.app-guru-bk')

@section('content')
<div class="main-content siswa-bimbingan-bk">
    {{-- HEADER SECTION --}}
    <div class="page-header-center">
        <div class="header-icon-large">
            <i class="fas fa-user-graduate"></i>
        </div>
        <h1>Siswa Bimbingan</h1>
        <div class="header-badges">
            <span class="header-periode-badge"><i class="fas fa-user-tie"></i> {{ $nama_guru_bk }}</span>
            <span class="header-periode-badge"><i class="fas fa-calendar-alt"></i> {{ $selected_tahun }} - {{ ucfirst($selected_semester) }}</span>
        </div>
    </div>

    {{-- STATISTICS SECTION - Dashboard Style --}}
    <div class="chart-card">
        <div class="chart-header">
            <i class="fas fa-chart-bar"></i>
            <h3>Statistik Siswa Bimbingan</h3>
        </div>
        <div class="quick-stats-grid">
            <div class="stat-card-mini primary">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div>
                    <h3>{{ $total_siswa }}</h3>
                    <p>Siswa</p>
                </div>
            </div>
            <div class="stat-card-mini secondary">
                <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <div>
                    <h3>{{ count($rombel_list) }}</h3>
                    <p>Rombel</p>
                </div>
            </div>
            <a href="{{ route('guru_bk.rekap-status', ['status' => 'Belum Ditangani', 'tahun' => $selected_tahun, 'semester' => $selected_semester]) }}" class="stat-card-mini danger clickable-stat">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div>
                    <h3>{{ $status_bimbingan_stats['Belum Ditangani'] ?? 0 }}</h3>
                    <p>Belum</p>
                </div>
            </a>
            <a href="{{ route('guru_bk.rekap-status', ['status' => 'Dalam Proses', 'tahun' => $selected_tahun, 'semester' => $selected_semester]) }}" class="stat-card-mini warning clickable-stat">
                <div class="stat-icon"><i class="fas fa-spinner"></i></div>
                <div>
                    <h3>{{ $status_bimbingan_stats['Dalam Proses'] ?? 0 }}</h3>
                    <p>Proses</p>
                </div>
            </a>
            <a href="{{ route('guru_bk.rekap-status', ['status' => 'Selesai', 'tahun' => $selected_tahun, 'semester' => $selected_semester]) }}" class="stat-card-mini success clickable-stat">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div>
                    <h3>{{ $status_bimbingan_stats['Selesai'] ?? 0 }}</h3>
                    <p>Selesai</p>
                </div>
            </a>
            <a href="{{ route('guru_bk.rekap-status', ['status' => 'Belum Ada Catatan', 'tahun' => $selected_tahun, 'semester' => $selected_semester]) }}" class="stat-card-mini info clickable-stat">
                <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                <div>
                    <h3>{{ $status_bimbingan_stats['Belum Ada Catatan'] ?? 0 }}</h3>
                    <p>No Data</p>
                </div>
            </a>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="filter-bar">
        <form method="POST" class="filter-form-horizontal">
            @csrf
            <input type="hidden" name="filter_tahun_semester" value="1">
            <div class="filter-row">
                <div class="filter-item">
                    <label class="filter-label">Tahun</label>
                    <select name="tahun_pelajaran" class="form-select modern-select">
                        @foreach($tahun_pelajaran_list as $tahun)
                        <option value="{{ $tahun }}" {{ $tahun == $selected_tahun ? 'selected' : '' }}>
                            {{ $tahun }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-item">
                    <label class="filter-label">Semester</label>
                    <select name="semester" class="form-select modern-select">
                        <option value="Ganjil" {{ $selected_semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap" {{ $selected_semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>

                <div class="filter-actions-horizontal">
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('guru_bk.siswa-bimbingan') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-refresh"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- ROMBEL CARDS SECTION --}}
    @if(count($siswa_per_rombel) > 0)
        @foreach($siswa_per_rombel as $rombel_nama => $siswa_list)
            @php
                $siswa_count = count($siswa_list);
                $laki_count = collect($siswa_list)->where('jk', 'Laki-laki')->count();
                $perempuan_count = collect($siswa_list)->where('jk', 'Perempuan')->count();
                
                // Count agama
                $agama_counts = [];
                foreach($siswa_list as $s) {
                    $agama = $s->agama ?? 'Tidak diketahui';
                    if (!isset($agama_counts[$agama])) {
                        $agama_counts[$agama] = 0;
                    }
                    $agama_counts[$agama]++;
                }
                arsort($agama_counts);
            @endphp

            <div class="rombel-card" data-rombel="{{ $rombel_nama }}">
                <div class="rombel-card-header" onclick="toggleRombelCard('{{ $rombel_nama }}')">
                    <div class="rombel-header-left">
                        <div class="rombel-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="rombel-info">
                            <h3>{{ $rombel_nama }}</h3>
                            <div class="rombel-stats-inline">
                                <span><i class="fas fa-users"></i> {{ $siswa_count }} siswa</span>
                            </div>
                        </div>
                    </div>
                    <div class="rombel-toggle">
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                </div>

                <div class="rombel-card-content">
                    @foreach($siswa_list as $siswa)
                        @php
                            $initials = collect(explode(' ', $siswa->nama))
                                ->map(fn($p) => strtoupper(substr($p, 0, 1)))
                                ->take(2)
                                ->join('');
                            $hasFoto = $siswa->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/' . $siswa->foto);
                        @endphp

                        <div class="siswa-card" data-siswa="{{ $siswa->id }}">
                            <div class="siswa-card-header" onclick="toggleSiswaCard('{{ $siswa->id }}')">
                                <div class="siswa-header-left">
                                    <div class="siswa-avatar clickable-avatar" onclick="event.stopPropagation(); showPhotoModal('{{ $siswa->nama }}', '{{ $hasFoto ? asset('storage/siswa/' . $siswa->foto) : '' }}', '{{ $initials ?: 'S' }}', '{{ $siswa->jk }}')" title="Lihat Foto">
                                        @if($hasFoto)
                                            <img src="{{ asset('storage/siswa/' . $siswa->foto) }}" alt="{{ $siswa->nama }}">
                                        @else
                                            <div class="siswa-avatar-initial {{ $siswa->jk == 'L' ? 'male' : 'female' }}">{{ $initials ?: 'S' }}</div>
                                        @endif
                                        <div class="avatar-overlay"><i class="fas fa-search-plus"></i></div>
                                    </div>
                                    <div class="siswa-info">
                                        <h4>{{ $siswa->nama }}</h4>
                                        <div class="siswa-meta">
                                            <span><i class="fas fa-id-card"></i> NIS: {{ $siswa->nis }}</span>
                                            <span><i class="fas fa-barcode"></i> NISN: {{ $siswa->nisn }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="siswa-header-right">
                                    <div class="siswa-toggle">
                                        <i class="fas fa-chevron-down toggle-icon"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="siswa-card-content">
                                <div class="siswa-details-grid">
                                    <div class="detail-row">
                                        <i class="fas fa-venus-mars detail-icon"></i>
                                        <div class="detail-content">
                                            <span class="detail-label">Jenis Kelamin</span>
                                            <span class="detail-value">{{ $siswa->jk }}</span>
                                        </div>
                                    </div>
                                    <div class="detail-row">
                                        <i class="fas fa-pray detail-icon"></i>
                                        <div class="detail-content">
                                            <span class="detail-label">Agama</span>
                                            <span class="detail-value">{{ $siswa->agama }}</span>
                                        </div>
                                    </div>
                                    <div class="detail-row">
                                        <i class="fas fa-calendar-alt detail-icon"></i>
                                        <div class="detail-content">
                                            <span class="detail-label">Angkatan Masuk</span>
                                            <span class="detail-value">{{ $siswa->angkatan_masuk }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Parent Information --}}
                                <div class="parent-info-section">
                                    <h5><i class="fas fa-users"></i> Informasi Orang Tua</h5>
                                    <div class="parent-cards">
                                        <div class="parent-card">
                                            <div class="parent-header">
                                                <i class="fas fa-male"></i>
                                                <strong>Ayah</strong>
                                            </div>
                                            <div class="parent-details">
                                                <div class="parent-detail-row">
                                                    <span class="parent-label">Nama:</span>
                                                    <span class="parent-value">{{ $siswa->nama_bapak ?? '-' }}</span>
                                                </div>
                                                <div class="parent-detail-row">
                                                    <span class="parent-label">Pekerjaan:</span>
                                                    <span class="parent-value">{{ $siswa->pekerjaan_bapak ?? '-' }}</span>
                                                </div>
                                                <div class="parent-detail-row">
                                                    <span class="parent-label">No. HP:</span>
                                                    <span class="parent-value">{{ $siswa->nohp_bapak ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="parent-card">
                                            <div class="parent-header">
                                                <i class="fas fa-female"></i>
                                                <strong>Ibu</strong>
                                            </div>
                                            <div class="parent-details">
                                                <div class="parent-detail-row">
                                                    <span class="parent-label">Nama:</span>
                                                    <span class="parent-value">{{ $siswa->nama_ibu ?? '-' }}</span>
                                                </div>
                                                <div class="parent-detail-row">
                                                    <span class="parent-label">Pekerjaan:</span>
                                                    <span class="parent-value">{{ $siswa->pekerjaan_ibu ?? '-' }}</span>
                                                </div>
                                                <div class="parent-detail-row">
                                                    <span class="parent-label">No. HP:</span>
                                                    <span class="parent-value">{{ $siswa->nohp_ibu ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="siswa-actions">
                                    <a href="{{ url('guru_bk/detail_siswa.php?nisn=' . urlencode($siswa->nisn)) }}" 
                                       class="btn-action btn-info">
                                        <i class="fas fa-eye"></i> Detail Lengkap
                                    </a>
                                    <a href="{{ route('guru_bk.catatan-bimbingan', ['nisn' => $siswa->nisn]) }}" 
                                       class="btn-action btn-primary">
                                        <i class="fas fa-clipboard-list"></i> Catatan Bimbingan
                                    </a>
                                    <a href="{{ route('guru_bk.panggilan-ortu', ['nisn' => $siswa->nisn]) }}" 
                                       class="btn-action btn-warning">
                                        <i class="fas fa-phone"></i> Panggilan Ortu
                                    </a>
                                    <a href="{{ route('guru_bk.riwayat-akademik', ['nisn' => $siswa->nisn]) }}" 
                                       class="btn-action btn-success">
                                        <i class="fas fa-chart-line"></i> Riwayat Akademik
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @else
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-users"></i>
            </div>
            <h3>Tidak Ada Siswa</h3>
            <p>Tidak ada siswa yang terdaftar di rombel ini pada periode yang dipilih.</p>
        </div>
    @endif
</div>

{{-- PHOTO MODAL --}}
<div id="photoModal" class="photo-modal-overlay" style="display: none;">
    <div class="photo-modal-content">
        <button type="button" class="photo-modal-close" onclick="closePhotoModal()">
            <i class="fas fa-times"></i>
        </button>
        <div class="photo-modal-body">
            <div id="photoModalImage" class="photo-modal-image"></div>
            <div class="photo-modal-name" id="photoModalName"></div>
        </div>
    </div>
</div>

<style>
/* Main Content */
.main-content.siswa-bimbingan-bk {
    padding: 25px;
    background-color: #f8f9fa;
    min-height: calc(100vh - 60px);
}

/* ============== HEADER ============== */
.siswa-bimbingan-bk .page-header-center { text-align: center; margin-bottom: 25px; }
.siswa-bimbingan-bk .header-icon-large {
    width: 70px; height: 70px; border-radius: 18px;
    display: flex; align-items: center; justify-content: center;
    font-size: 32px; color: white; margin: 0 auto 16px;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    box-shadow: 0 8px 25px rgba(139,92,246,0.4);
}
.siswa-bimbingan-bk .page-header-center h1 { font-size: 24px; font-weight: 700; margin: 0 0 8px 0; color: #1f2937; }
.header-badges { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
.header-periode-badge {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(139,92,246,0.1); color: #7c3aed;
    padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 600;
    border: 1px solid rgba(139,92,246,0.2);
}

/* ============== STATS CHART CARD ============== */
.siswa-bimbingan-bk .chart-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.siswa-bimbingan-bk .chart-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f3f4f6;
    margin-bottom: 15px;
}

.siswa-bimbingan-bk .chart-header i {
    color: #2e7d32;
    font-size: 18px;
}

.siswa-bimbingan-bk .chart-header h3 {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.siswa-bimbingan-bk .quick-stats-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.siswa-bimbingan-bk .stat-card-mini {
    flex: 1;
    min-width: 90px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.siswa-bimbingan-bk .stat-card-mini.primary {
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
}

.siswa-bimbingan-bk .stat-card-mini.secondary {
    background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
}

.siswa-bimbingan-bk .stat-card-mini.danger {
    background: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
}

.siswa-bimbingan-bk .stat-card-mini.warning {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
}

.siswa-bimbingan-bk .stat-card-mini.success {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
}

.siswa-bimbingan-bk .stat-card-mini.info {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
}

.siswa-bimbingan-bk .stat-card-mini.clickable-stat:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.siswa-bimbingan-bk .stat-card-mini .stat-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}

.siswa-bimbingan-bk .stat-card-mini.primary .stat-icon {
    background: #0ea5e9;
    color: white;
}

.siswa-bimbingan-bk .stat-card-mini.secondary .stat-icon {
    background: #a855f7;
    color: white;
}

.siswa-bimbingan-bk .stat-card-mini.danger .stat-icon {
    background: #ef4444;
    color: white;
}

.siswa-bimbingan-bk .stat-card-mini.warning .stat-icon {
    background: #f59e0b;
    color: white;
}

.siswa-bimbingan-bk .stat-card-mini.success .stat-icon {
    background: #10b981;
    color: white;
}

.siswa-bimbingan-bk .stat-card-mini.info .stat-icon {
    background: #3b82f6;
    color: white;
}

.siswa-bimbingan-bk .stat-card-mini h3 {
    font-size: 18px;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.siswa-bimbingan-bk .stat-card-mini p {
    font-size: 11px;
    color: #6b7280;
    margin: 0;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.stat-card-link {
    text-decoration: none;
    color: inherit;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card.clickable:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    cursor: pointer;
}

.stat-icon {
    width: 55px;
    height: 55px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.stat-icon.primary { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }
.stat-icon.secondary { background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%); }
.stat-icon.success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.stat-icon.warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.stat-icon.danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
.stat-icon.info { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }

.stat-info h3 {
    margin: 0;
    font-size: 1.8rem;
    font-weight: 700;
    color: #1f2937;
}

.stat-info p {
    margin: 4px 0 0;
    color: #6b7280;
    font-size: 0.85rem;
}

.stat-subtitle {
    display: block;
    font-size: 10px;
    color: #9ca3af;
    margin-top: 2px;
    font-weight: 500;
}

/* Filter Bar */
.filter-bar {
    background: white;
    border-radius: 16px;
    padding: 20px 25px;
    margin-bottom: 25px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.filter-row {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: flex-end;
}

.filter-item {
    flex: 1;
    min-width: 150px;
}

.filter-label {
    display: block;
    font-size: 0.8rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
}

.form-select {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.form-select:focus {
    outline: none;
    border-color: #2e7d32;
    box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.1);
}

.filter-actions-horizontal {
    display: flex;
    gap: 10px;
}

.btn {
    padding: 10px 18px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    border: none;
}

.btn-secondary {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    color: white;
}

.btn-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
}

/* Rombel Card */
.rombel-card {
    background: white;
    border-radius: 16px;
    margin-bottom: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    overflow: hidden;
}

.rombel-card-header {
    padding: 20px 25px;
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.rombel-card-header:hover {
    background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
}

.rombel-header-left {
    display: flex;
    align-items: center;
    gap: 15px;
    flex: 1;
}

.rombel-icon {
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.rombel-info h3 {
    margin: 0 0 8px 0;
    font-size: 1.3rem;
    font-weight: 700;
}

.rombel-stats-inline {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    font-size: 0.85rem;
    opacity: 0.95;
}

.rombel-stats-inline span {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.rombel-toggle {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(255,255,255,0.2);
    transition: all 0.3s ease;
}

.rombel-card.expanded .rombel-toggle .toggle-icon {
    transform: rotate(180deg);
}

.rombel-card-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
    opacity: 0;
    padding: 0 20px;
}

.rombel-card.expanded .rombel-card-content {
    max-height: none;
    opacity: 1;
    padding: 20px;
}

/* Siswa Card */
.siswa-card {
    background: #f9fafb;
    border-radius: 12px;
    margin-bottom: 15px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.siswa-card-header {
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.siswa-card-header:hover {
    background: #f3f4f6;
}

.siswa-header-left {
    display: flex;
    align-items: center;
    gap: 15px;
    flex: 1;
}

.siswa-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    overflow: hidden;
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.siswa-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.siswa-avatar-initial {
    color: white;
    font-weight: 700;
    font-size: 18px;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.siswa-avatar-initial.male {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.siswa-avatar-initial.female {
    background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
}

/* Clickable Avatar */
.siswa-avatar.clickable-avatar {
    position: relative;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.siswa-avatar.clickable-avatar:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4);
}

.avatar-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.avatar-overlay i {
    color: white;
    font-size: 16px;
}

.siswa-avatar.clickable-avatar:hover .avatar-overlay {
    opacity: 1;
}

/* Photo Modal */
.photo-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(8px);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.photo-modal-content {
    position: relative;
    max-width: 90%;
    max-height: 90%;
    animation: zoomIn 0.3s ease;
}

@keyframes zoomIn {
    from { transform: scale(0.8); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

.photo-modal-close {
    position: absolute;
    top: -15px;
    right: -15px;
    width: 40px;
    height: 40px;
    background: white;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 18px;
    color: #374151;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
    z-index: 10001;
}

.photo-modal-close:hover {
    background: #ef4444;
    color: white;
    transform: scale(1.1);
}

.photo-modal-body {
    text-align: center;
}

.photo-modal-image {
    width: 280px;
    height: 280px;
    border-radius: 50%;
    overflow: hidden;
    border: 5px solid white;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
    margin: 0 auto 20px;
}

.photo-modal-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.photo-modal-image .modal-avatar-initial {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 100px;
    font-weight: 700;
    color: white;
}

.photo-modal-image .modal-avatar-initial.male {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.photo-modal-image .modal-avatar-initial.female {
    background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
}

.photo-modal-name {
    color: white;
    font-size: 1.5rem;
    font-weight: 600;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
}

.siswa-info h4 {
    margin: 0 0 5px 0;
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
}

.siswa-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    font-size: 0.75rem;
    color: #6b7280;
}

.siswa-meta span {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.siswa-toggle {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 6px;
    background: rgba(59, 130, 246, 0.1);
    border: 1px solid rgba(59, 130, 246, 0.2);
    transition: all 0.3s ease;
}

.siswa-card.expanded .siswa-toggle .toggle-icon {
    transform: rotate(180deg);
}

.siswa-card-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
    opacity: 0;
}

.siswa-card.expanded .siswa-card-content {
    max-height: 2000px;
    opacity: 1;
}

.siswa-details-grid {
    padding: 20px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 12px;
    border-bottom: 1px solid #e5e7eb;
}

.detail-row {
    display: flex;
    align-items: center;
    gap: 12px;
}

.detail-icon {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
}

.detail-content {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.detail-label {
    font-size: 0.7rem;
    color: #9ca3af;
    font-weight: 500;
}

.detail-value {
    font-size: 0.9rem;
    color: #1f2937;
    font-weight: 600;
}

/* Parent Info */
.parent-info-section {
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
}

.parent-info-section h5 {
    margin: 0 0 15px 0;
    font-size: 0.95rem;
    font-weight: 600;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 8px;
}

.parent-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
}

.parent-card {
    background: white;
    border-radius: 10px;
    padding: 15px;
    border: 1px solid #e5e7eb;
}

.parent-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f3f4f6;
    color: #2e7d32;
    font-size: 0.9rem;
}

.parent-details {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.parent-detail-row {
    display: flex;
    justify-content: space-between;
    font-size: 0.85rem;
}

.parent-label {
    color: #6b7280;
    font-weight: 500;
}

.parent-value {
    color: #1f2937;
    font-weight: 600;
    text-align: right;
}

/* Action Buttons */
.siswa-actions {
    padding: 20px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 10px;
}

.btn-action {
    padding: 10px 16px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none;
    border: none;
    color: white;
}

.btn-action.btn-info {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.btn-action.btn-primary {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
}

.btn-action.btn-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.btn-action.btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Empty State */
.empty-state {
    background: white;
    border-radius: 16px;
    padding: 60px 30px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.empty-state .empty-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #2e7d32 0%, #43a047 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.empty-state .empty-icon i {
    font-size: 32px;
    color: white;
}

.empty-state h3 {
    margin: 0 0 10px;
    color: #1f2937;
    font-weight: 600;
}

.empty-state p {
    margin: 0;
    color: #6b7280;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .main-content.siswa-bimbingan-bk {
        padding: 15px;
    }
    
    /* Header Mobile - Centered */
    .siswa-bimbingan-bk .bk-page-header {
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 15px;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .siswa-bimbingan-bk .header-content-wrapper {
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }
    
    .siswa-bimbingan-bk .header-icon-box {
        width: 60px;
        height: 60px;
        font-size: 24px;
        border-radius: 50%;
    }
    
    .siswa-bimbingan-bk .header-info {
        text-align: center;
    }
    
    .siswa-bimbingan-bk .header-greeting .greeting-text {
        font-size: 11px;
    }
    
    .siswa-bimbingan-bk .header-greeting h1 {
        font-size: 16px;
    }
    
    .siswa-bimbingan-bk .header-details {
        justify-content: center;
        gap: 8px;
        margin-top: 8px;
    }
    
    .siswa-bimbingan-bk .detail-badge {
        font-size: 10px;
        padding: 4px 8px;
    }
    
    /* Stats Card Mobile - Horizontal Single Row */
    .siswa-bimbingan-bk .chart-card {
        padding: 12px;
        border-radius: 12px;
        margin-bottom: 15px;
    }
    
    .siswa-bimbingan-bk .chart-header {
        padding-bottom: 10px;
        margin-bottom: 10px;
    }
    
    .siswa-bimbingan-bk .chart-header i {
        font-size: 14px;
    }
    
    .siswa-bimbingan-bk .chart-header h3 {
        font-size: 13px;
    }
    
    .siswa-bimbingan-bk .quick-stats-grid {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        gap: 6px;
        padding-bottom: 5px;
        -webkit-overflow-scrolling: touch;
    }
    
    .siswa-bimbingan-bk .stat-card-mini {
        flex: 0 0 auto;
        min-width: 70px;
        padding: 8px;
        border-radius: 8px;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 4px;
    }
    
    .siswa-bimbingan-bk .stat-card-mini .stat-icon {
        width: 28px;
        height: 28px;
        font-size: 11px;
        border-radius: 8px;
    }
    
    .siswa-bimbingan-bk .stat-card-mini h3 {
        font-size: 14px;
    }
    
    .siswa-bimbingan-bk .stat-card-mini p {
        font-size: 8px;
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    /* Filter Bar Mobile - Inline Layout */
    .filter-bar {
        padding: 12px;
        border-radius: 10px;
    }
    
    .filter-row {
        flex-direction: column;
        gap: 10px;
    }

    .filter-item {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 10px;
        width: 100%;
    }
    
    .filter-label {
        white-space: nowrap;
        min-width: 70px;
        font-size: 11px;
        margin-bottom: 0;
    }
    
    .filter-item .form-select {
        flex: 1;
        font-size: 12px;
        padding: 8px 10px;
    }

    .filter-actions-horizontal {
        width: 100%;
        justify-content: flex-end;
        gap: 8px;
    }

    .filter-actions-horizontal .btn {
        flex: 1;
        font-size: 11px;
        padding: 8px 12px;
    }

    .rombel-stats-inline {
        flex-direction: column;
        gap: 8px;
    }

    .siswa-actions {
        grid-template-columns: 1fr;
    }

    .parent-cards {
        grid-template-columns: 1fr;
    }

    .siswa-details-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function toggleRombelCard(rombelId) {
    const card = document.querySelector(`.rombel-card[data-rombel="${rombelId}"]`);
    if (!card) return;

    const content = card.querySelector('.rombel-card-content');
    const isExpanding = !card.classList.contains('expanded');

    if (isExpanding) {
        card.classList.add('expanded');
        const contentHeight = content.scrollHeight;
        content.style.maxHeight = contentHeight + 'px';
        content.style.opacity = '1';
        setTimeout(() => {
            if (card.classList.contains('expanded')) {
                content.style.maxHeight = 'none';
            }
        }, 400);
    } else {
        const contentHeight = content.scrollHeight;
        content.style.maxHeight = contentHeight + 'px';
        content.offsetHeight;
        content.style.maxHeight = '0';
        content.style.opacity = '0';
        setTimeout(() => {
            card.classList.remove('expanded');
        }, 300);
    }
}

function toggleSiswaCard(siswaId) {
    const card = document.querySelector(`.siswa-card[data-siswa="${siswaId}"]`);
    if (!card) return;

    const content = card.querySelector('.siswa-card-content');
    const isExpanding = !card.classList.contains('expanded');

    if (isExpanding) {
        card.classList.add('expanded');
        const contentHeight = content.scrollHeight;
        content.style.maxHeight = contentHeight + 'px';
        content.style.opacity = '1';
        setTimeout(() => {
            if (card.classList.contains('expanded')) {
                content.style.maxHeight = 'none';
            }
        }, 400);
    } else {
        const contentHeight = content.scrollHeight;
        content.style.maxHeight = contentHeight + 'px';
        content.offsetHeight;
        content.style.maxHeight = '0';
        content.style.opacity = '0';
        setTimeout(() => {
            card.classList.remove('expanded');
        }, 300);
    }
}

// Photo Modal Functions
function showPhotoModal(nama, fotoUrl, initials, jk) {
    const modal = document.getElementById('photoModal');
    const imageContainer = document.getElementById('photoModalImage');
    const nameElement = document.getElementById('photoModalName');
    
    // Set name
    nameElement.textContent = nama;
    
    // Set image or initials
    if (fotoUrl && fotoUrl.trim() !== '') {
        imageContainer.innerHTML = `<img src="${fotoUrl}" alt="${nama}">`;
    } else {
        const genderClass = jk === 'L' ? 'male' : 'female';
        imageContainer.innerHTML = `<div class="modal-avatar-initial ${genderClass}">${initials}</div>`;
    }
    
    // Show modal
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closePhotoModal() {
    const modal = document.getElementById('photoModal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

// Close modal on overlay click
document.addEventListener('click', function(e) {
    const modal = document.getElementById('photoModal');
    if (e.target === modal) {
        closePhotoModal();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePhotoModal();
    }
});
</script>
@endsection

