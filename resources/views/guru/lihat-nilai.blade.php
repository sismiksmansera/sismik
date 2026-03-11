@extends('layouts.app')

@section('title', 'Lihat Nilai')

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
    margin: 0;
    color: white;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* STATS GRID */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 25px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #e5e7eb;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.12);
    border-color: #10b981;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
    flex-shrink: 0;
}

.stat-icon.primary { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.stat-icon.success { background: linear-gradient(135deg, #10b981, #059669); }
.stat-icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }

.stat-info { flex: 1; min-width: 0; }
.stat-info h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: #1f2937;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.stat-info p { margin: 4px 0 0 0; color: #6b7280; font-size: 12px; font-weight: 500; }

/* ACTION BUTTONS */
.action-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 20px;
}

.btn-back {
    background: linear-gradient(135deg, #6b7280, #4b5563);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-back:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
    color: white;
}

.btn-add {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
    color: white;
}

/* SEARCH SECTION */
.search-card {
    background: white;
    border-radius: 12px;
    padding: 15px 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}

.search-form {
    display: flex;
    gap: 15px;
    align-items: center;
}

.search-input-group {
    flex: 1;
    position: relative;
}

.search-input {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
}

.search-input:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.search-results-info {
    margin-top: 10px;
    font-size: 13px;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 8px;
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
.student-card-body {
    padding: 15px;
}

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

.nilai-date {
    font-size: 12px;
    font-weight: 600;
    color: #059669;
}

.nilai-score {
    font-size: 12px;
    color: #1f2937;
}

.score-label { color: #6b7280; }
.score-value { font-weight: 700; }

.nilai-materi {
    font-size: 11px;
    color: #6b7280;
}

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
    margin-right: 8px;
    transition: all 0.3s ease;
    font-size: 10px;
}

.btn-edit-nilai:hover {
    background: linear-gradient(135deg, #d97706, #b45309);
    transform: scale(1.1);
    color: white;
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

.section-header .badge {
    background: #d1fae5;
    color: #059669;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

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
    
    .action-bar { flex-direction: column; align-items: stretch; }
    
    .students-cards-grid { grid-template-columns: 1fr; gap: 15px; }
    
    .student-avatar { width: 40px; height: 40px; font-size: 14px; }
    .student-name { font-size: 13px; }
    .student-nisn { font-size: 10px; }
    
    .statistics-row { flex-direction: row; gap: 6px; }
    .stat-item-small { flex-direction: column; text-align: center; padding: 6px; }
    .stat-icon-small { width: 22px; height: 22px; font-size: 10px; }
    .stat-value-small { font-size: 12px; }
    .stat-label-small { font-size: 8px; }
    
    .student-card-footer { padding: 10px; }
    .btn-tambah-nilai { font-size: 11px; padding: 8px 12px; }
}

/* STUDENT CARD FOOTER */
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
            </div>

            <!-- STATS CARDS -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-school"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $namaRombel }}</h3>
                        <p>Rombel</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $mapel }}</h3>
                        <p>Mata Pelajaran</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon warning">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $tahunPelajaran }}</h3>
                        <p>{{ ucfirst($semesterAktif) }}</p>
                    </div>
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="action-bar">
                <a href="{{ route('guru.tugas-mengajar') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali ke Penugasan
                </a>
                <a href="{{ route('guru.penilaian.index', ['id_rombel' => $idRombel, 'mapel' => $mapel, 'from' => 'lihat-nilai']) }}" class="btn-add">
                    <i class="fas fa-plus"></i> Tambah Penilaian
                </a>
            </div>

            <!-- SEARCH SECTION -->
            <div class="search-card">
                <form method="GET" class="search-form">
                    <input type="hidden" name="id_rombel" value="{{ $idRombel }}">
                    <input type="hidden" name="mapel" value="{{ $mapel }}">
                    <div class="search-input-group">
                        <input type="text" name="search" class="search-input" 
                               placeholder="Cari berdasarkan nama, NIS, atau NISN..." 
                               value="{{ $searchQuery }}">
                    </div>
                </form>
                @if(!empty($searchQuery))
                    <div class="search-results-info">
                        <i class="fas fa-info-circle"></i>
                        Menampilkan {{ $filteredTotal }} dari {{ $totalSiswa }} siswa untuk pencarian "<strong>{{ $searchQuery }}</strong>"
                    </div>
                @endif
            </div>

            <!-- SECTION HEADER -->
            <div class="section-header">
                <h2><i class="fas fa-user-graduate"></i> Daftar Nilai Siswa</h2>
                <span class="badge">
                    @if(!empty($searchQuery))
                        {{ $filteredTotal }} dari {{ $totalSiswa }} Siswa
                    @else
                        {{ $totalSiswa }} Siswa
                    @endif
                </span>
            </div>

            <!-- STUDENT CARDS -->
            @if(count($filteredData) > 0)
                <div class="students-cards-grid">
                    @foreach($filteredData as $data)
                        @php
                            $status = !empty($data['nilai']) ? 'success' : 'warning';
                            $statusText = !empty($data['nilai']) ? 'Sudah Dinilai' : 'Belum Dinilai';
                            $statusIcon = !empty($data['nilai']) ? 'fa-check-circle' : 'fa-clock';
                            $nilaiCount = count($data['nilai']);
                            
                            // Calculate statistics
                            $nilaiValues = [];
                            if (!empty($data['nilai'])) {
                                foreach ($data['nilai'] as $nilaiDetail) {
                                    if (is_numeric($nilaiDetail['nilai'])) {
                                        $nilaiValues[] = floatval($nilaiDetail['nilai']);
                                    }
                                }
                            }
                            
                            $minNilai = !empty($nilaiValues) ? min($nilaiValues) : 0;
                            $maxNilai = !empty($nilaiValues) ? max($nilaiValues) : 0;
                            $avgNilai = !empty($nilaiValues) ? array_sum($nilaiValues) / count($nilaiValues) : 0;
                            
                            $initials = collect(explode(' ', $data['nama']))
                                ->map(fn($p) => strtoupper(substr($p, 0, 1)))
                                ->take(2)
                                ->join('');
                            
                            $hasFoto = $data['foto'] && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/' . $data['foto']);
                        @endphp
                        
                        <div class="student-card">
                            <div class="student-card-header">
                                <div class="student-avatar">
                                    @if($hasFoto)
                                        <img src="{{ asset('storage/siswa/' . $data['foto']) }}" alt="{{ $data['nama'] }}">
                                    @else
                                        {{ $initials ?: 'S' }}
                                    @endif
                                </div>
                                <div class="student-info">
                                    <h4 class="student-name">{{ $data['nama'] }}</h4>
                                    <p class="student-nisn">NISN: {{ $data['nisn'] }}</p>
                                </div>
                                <div class="student-status {{ $status }}">
                                    <i class="fas {{ $statusIcon }}"></i>
                                    {{ $statusText }}
                                </div>
                            </div>

                            @if(!empty($data['nilai']))
                                <div class="statistics-row">
                                    <div class="stat-item-small">
                                        <div class="stat-icon-small">
                                            <i class="fas fa-chart-line"></i>
                                        </div>
                                        <div class="stat-info-small">
                                            <div class="stat-value-small">{{ number_format($avgNilai, 1) }}</div>
                                            <div class="stat-label-small">Rata-rata</div>
                                        </div>
                                    </div>
                                    <div class="stat-item-small">
                                        <div class="stat-icon-small">
                                            <i class="fas fa-arrow-up"></i>
                                        </div>
                                        <div class="stat-info-small">
                                            <div class="stat-value-small">{{ number_format($maxNilai, 1) }}</div>
                                            <div class="stat-label-small">Tertinggi</div>
                                        </div>
                                    </div>
                                    <div class="stat-item-small">
                                        <div class="stat-icon-small">
                                            <i class="fas fa-arrow-down"></i>
                                        </div>
                                        <div class="stat-info-small">
                                            <div class="stat-value-small">{{ number_format($minNilai, 1) }}</div>
                                            <div class="stat-label-small">Terendah</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="student-card-body">
                                    <div class="nilai-summary-header">
                                        <i class="fas fa-history"></i>
                                        <span>Riwayat Penilaian ({{ $nilaiCount }} sesi)</span>
                                    </div>
                                    <div class="nilai-list">
                                        @foreach($data['nilai'] as $tanggal => $nilaiDetail)
                                            <div class="nilai-item">
                                                <div class="nilai-header">
                                                    <span class="nilai-date">
                                                        <a href="{{ route('guru.edit-nilai-siswa', ['id_rombel' => $idRombel, 'mapel' => $mapel, 'tanggal' => $tanggal, 'nisn' => $data['nisn']]) }}" 
                                                           class="btn-edit-nilai" title="Edit nilai tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}
                                                    </span>
                                                    <span class="nilai-score">
                                                        <span class="score-label">Nilai:</span>
                                                        <span class="score-value">{{ $nilaiDetail['nilai'] }}</span>
                                                    </span>
                                                </div>
                                                @if(!empty($nilaiDetail['materi']))
                                                    <div class="nilai-materi">
                                                        Materi: {{ Str::limit($nilaiDetail['materi'], 40) }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="no-statistics">
                                    <i class="fas fa-chart-bar"></i>
                                    <p>Belum ada data nilai</p>
                                </div>
                            @endif

                            <!-- TAMBAH NILAI BUTTON -->
                            <div class="student-card-footer">
                                <a href="{{ route('guru.tambah-nilai', ['id_rombel' => $idRombel, 'mapel' => $mapel, 'nisn' => $data['nisn'], 'nama_siswa' => $data['nama']]) }}" 
                                   class="btn-tambah-nilai">
                                    <i class="fas fa-plus"></i> Tambah Nilai
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        @if(!empty($searchQuery))
                            <i class="fas fa-search"></i>
                        @else
                            <i class="fas fa-clipboard-list"></i>
                        @endif
                    </div>
                    <h3>
                        @if(!empty($searchQuery))
                            Siswa Tidak Ditemukan
                        @else
                            Belum Ada Data Siswa
                        @endif
                    </h3>
                    <p>
                        @if(!empty($searchQuery))
                            Tidak ada siswa yang cocok dengan pencarian "{{ $searchQuery }}".
                        @else
                            Tidak ada siswa yang terdaftar di rombel {{ $namaRombel }}.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
