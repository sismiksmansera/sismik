@extends('layouts.app')

@section('title', 'Anggota Rombel - ' . $rombel->nama_rombel)

@push('styles')
<style>
/* HEADER SECTION */
.rombel-header {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 25px;
    color: white;
    text-align: center;
}

.rombel-header .header-icon-large {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    margin: 0 auto 20px;
}

.rombel-header .page-title {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 10px 0;
    text-transform: uppercase;
}

.rombel-header .header-subtitle {
    font-size: 14px;
    opacity: 0.9;
}

.header-badge {
    display: inline-block;
    background: rgba(255,255,255,0.25);
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    margin-top: 10px;
}

/* ACTION BUTTONS */
.action-buttons-header {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 25px;
    flex-wrap: wrap;
}

.btn-modern {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-back {
    background: white;
    color: #374151;
    border: 2px solid #d1d5db;
}

.btn-back:hover {
    background: #f3f4f6;
    transform: translateY(-2px);
    color: #1f2937;
}

.btn-prestasi {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.btn-prestasi:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    color: white;
}

.btn-leger {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.btn-leger:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    color: white;
}

.btn-print-all {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-print-all:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    color: white;
}

.btn-raport-all {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.btn-raport-all:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    color: white;
}

.btn-katrol-leger {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: white;
}

.btn-katrol-leger:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
    color: white;
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
    border: 1px solid #e5e7eb;
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
}

.stat-icon.male { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.stat-icon.female { background: linear-gradient(135deg, #ec4899, #db2777); }
.stat-icon.total { background: linear-gradient(135deg, #10b981, #059669); }

.stat-info h3 { margin: 0; font-size: 22px; font-weight: 700; color: #1f2937; }
.stat-info p { margin: 4px 0 0 0; color: #6b7280; font-size: 13px; }

/* MEMBERS SECTION */
.members-section {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    overflow: hidden;
}

.section-header {
    padding: 20px 25px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.section-title { display: flex; align-items: center; gap: 10px; }
.section-title h2 { margin: 0; font-size: 1.1rem; color: #1f2937; }

.member-count-badge {
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

/* MEMBER CARDS GRID */
.members-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 15px;
    padding: 20px;
}

/* MEMBER CARD */
.member-card {
    background: white;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    transition: all 0.3s ease;
}

.member-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-color: #10b981;
}

.member-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background: #e6f7f5;
    color: #0f766e;
    cursor: pointer;
}

.member-header-left {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
}

.member-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    overflow: hidden;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 16px;
}

.member-avatar img { width: 100%; height: 100%; object-fit: cover; }

.member-name-info h4 { margin: 0 0 3px 0; font-size: 15px; font-weight: 600; color: #1f2937; }
.member-name-info .member-nis { font-size: 12px; color: #6b7280; }

.badge-jk {
    padding: 3px 8px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 600;
}

.badge-laki { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.badge-perempuan { background: rgba(236, 72, 153, 0.1); color: #ec4899; }

.expand-icon { transition: transform 0.3s ease; }
.member-card.expanded .expand-icon { transform: rotate(180deg); }

/* MEMBER CARD BODY */
.member-card-body {
    max-height: 0;
    overflow: hidden;
    opacity: 0;
    transition: all 0.4s ease;
    padding: 0 15px;
    background: #fafafa;
}

.member-card.expanded .member-card-body {
    max-height: 400px;
    opacity: 1;
    padding: 15px;
}

.member-details-grid {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #e5e7eb;
}

.detail-label {
    font-size: 11px;
    color: #6b7280;
    font-weight: 600;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 5px;
}

.detail-label i { font-size: 10px; color: #10b981; }
.detail-value { font-size: 14px; color: #1f2937; font-weight: 500; }

/* ACTION BUTTONS CARD */
.member-actions-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding-top: 15px;
    margin-top: 15px;
    border-top: 1px solid #e5e7eb;
    flex-wrap: wrap;
}

.btn-action-card {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-cetak {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-cetak:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    color: white;
}

.btn-raport-card {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.btn-raport-card:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    color: white;
}

.btn-catatan {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.btn-catatan:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
    color: white;
}

/* EMPTY STATE */
.empty-state {
    padding: 60px 30px;
    text-align: center;
}

.empty-icon {
    width: 80px;
    height: 80px;
    background: #f3f4f6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.empty-icon i { font-size: 32px; color: #9ca3af; }
.empty-state h3 { margin: 0 0 10px 0; color: #1f2937; }
.empty-state p { margin: 0; color: #6b7280; }

/* RESPONSIVE */
@media (max-width: 768px) {
    .rombel-header { padding: 20px 15px; }
    .rombel-header .header-icon-large { width: 60px; height: 60px; font-size: 28px; }
    .rombel-header .page-title { font-size: 20px; }
    
    .stats-grid { grid-template-columns: repeat(3, 1fr); gap: 8px; }
    .stat-card { flex-direction: column; text-align: center; padding: 12px 8px; gap: 8px; }
    .stat-icon { width: 35px; height: 35px; font-size: 14px; }
    .stat-info h3 { font-size: 18px; }
    .stat-info p { font-size: 11px; }
    
    .action-buttons-header { flex-direction: column; }
    .btn-modern { width: 100%; justify-content: center; }
    
    .members-cards-grid { grid-template-columns: 1fr; padding: 15px; }
}
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-guru')
    
    <div class="main-content">
        <div class="anggota-rombel-page">
            <!-- HEADER -->
            <div class="rombel-header">
                <div class="header-icon-large">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <h1 class="page-title">{{ $rombel->nama_rombel }}</h1>
                <p class="header-subtitle">{{ $tahunPelajaran }} - {{ ucfirst($semester) }}</p>
                <span class="header-badge">
                    <i class="fas fa-user-tie"></i> Wali Kelas
                </span>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="action-buttons-header">
                <a href="{{ route('guru.tugas-tambahan') }}" class="btn-modern btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                @if($lockPrintLegerNilai != 'Ya')
                <a href="{{ route('guru.leger.print-nilai', ['rombel_id' => $idRombel, 'tahun' => $tahunPelajaran, 'semester' => $semester]) }}" target="_blank" class="btn-modern btn-leger">
                    <i class="fas fa-table"></i> Cetak Leger Akademik
                </a>
                @endif
                @if($lockPrintLegerKatrol != 'Ya')
                <a href="{{ route('guru.leger.print-katrol', ['rombel_id' => $idRombel, 'tahun' => $tahunPelajaran, 'semester' => $semester]) }}" target="_blank" class="btn-modern btn-katrol-leger">
                    <i class="fas fa-chart-line"></i> Cetak Leger Katrol
                </a>
                @endif
                @if($lockPrintRiwayatAll != 'Ya')
                <a href="{{ route('guru.riwayat-akademik.print-all', ['rombel_id' => $idRombel, 'tahun' => $tahunPelajaran, 'semester' => $semester]) }}" target="_blank" class="btn-modern btn-print-all">
                    <i class="fas fa-print"></i> Cetak Semua Riwayat
                </a>
                @endif
                @if($lockPrintRaportAll != 'Ya')
                <a href="{{ route('guru.raport.print-all', ['rombel_id' => $idRombel, 'tahun' => $tahunPelajaran, 'semester' => $semester]) }}" target="_blank" class="btn-modern btn-raport-all">
                    <i class="fas fa-file-alt"></i> Cetak Semua Raport
                </a>
                @endif
                <a href="{{ route('guru.lihat-prestasi', ['type' => 'rombel', 'id' => $idRombel]) }}" class="btn-modern btn-prestasi">
                    <i class="fas fa-trophy"></i> Lihat Prestasi
                </a>
            </div>

            <!-- STATS -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon male">
                        <i class="fas fa-male"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $totalLaki }}</h3>
                        <p>Laki-laki</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon female">
                        <i class="fas fa-female"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $totalPerempuan }}</h3>
                        <p>Perempuan</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon total">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $totalSiswa }}</h3>
                        <p>Total Siswa</p>
                    </div>
                </div>
            </div>

            <!-- MEMBERS SECTION -->
            <div class="members-section">
                <div class="section-header">
                    <div class="section-title">
                        <i class="fas fa-user-graduate"></i>
                        <h2>Daftar Siswa</h2>
                    </div>
                    <span class="member-count-badge">{{ $totalSiswa }} Siswa</span>
                </div>

                @if(count($siswaList) > 0)
                    <div class="members-cards-grid">
                        @foreach($siswaList as $siswa)
                            @php
                                $initials = collect(explode(' ', $siswa->nama))
                                    ->map(fn($p) => strtoupper(substr($p, 0, 1)))
                                    ->take(2)
                                    ->join('');
                                $hasFoto = $siswa->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/' . $siswa->foto);
                            @endphp
                            <div class="member-card" id="card-{{ $siswa->id }}">
                                <div class="member-card-header" onclick="toggleCard({{ $siswa->id }})">
                                    <div class="member-header-left">
                                        <div class="member-avatar">
                                            @if($hasFoto)
                                                <img src="{{ asset('storage/siswa/' . $siswa->foto) }}" alt="{{ $siswa->nama }}">
                                            @else
                                                {{ $initials ?: 'S' }}
                                            @endif
                                        </div>
                                        <div class="member-name-info">
                                            <h4>{{ $siswa->nama }}</h4>
                                            <span class="member-nis">NIS: {{ $siswa->nis }}</span>
                                        </div>
                                    </div>
                                    <div class="member-header-right">
                                        <span class="badge-jk {{ $siswa->jk == 'Laki-laki' ? 'badge-laki' : 'badge-perempuan' }}">
                                            <i class="fas {{ $siswa->jk == 'Laki-laki' ? 'fa-male' : 'fa-female' }}"></i>
                                            {{ $siswa->jk == 'Laki-laki' ? 'L' : 'P' }}
                                        </span>
                                        <i class="fas fa-chevron-down expand-icon"></i>
                                    </div>
                                </div>
                                <div class="member-card-body">
                                    <div class="member-details-grid">
                                        <div class="detail-item">
                                            <span class="detail-label"><i class="fas fa-id-card"></i> NIS</span>
                                            <span class="detail-value">{{ $siswa->nis }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label"><i class="fas fa-fingerprint"></i> NISN</span>
                                            <span class="detail-value">{{ $siswa->nisn }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label"><i class="fas fa-venus-mars"></i> Jenis Kelamin</span>
                                            <span class="detail-value">{{ $siswa->jk }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label"><i class="fas fa-pray"></i> Agama</span>
                                            <span class="detail-value">{{ $siswa->agama }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label"><i class="fas fa-calendar"></i> Angkatan</span>
                                            <span class="detail-value">{{ $siswa->angkatan_masuk }}</span>
                                        </div>
                                    </div>
                                    <div class="member-actions-row">
                                        @if($lockPrintRiwayatGuru != 'Ya')
                                        <a href="{{ route('guru.riwayat-akademik', ['nisn' => $siswa->nisn]) }}" 
                                           class="btn-action-card btn-cetak" title="Lihat Riwayat" target="_blank">
                                            <i class="fas fa-history"></i> Riwayat
                                        </a>
                                        @endif
                                        @if($lockPrintRaport != 'Ya')
                                        <a href="{{ route('guru.raport.print', ['nisn' => $siswa->nisn, 'rombel_id' => $idRombel, 'tahun' => $tahunPelajaran, 'semester' => $semester]) }}" 
                                           class="btn-action-card btn-raport-card" title="Cetak Raport" target="_blank">
                                            <i class="fas fa-file-alt"></i> Raport
                                        </a>
                                        @endif
                                        <a href="{{ route('guru.catatan-wali-kelas', ['siswa_id' => $siswa->id, 'rombel_id' => $idRombel, 'tahun' => $tahunPelajaran, 'semester' => $semester]) }}" class="btn-action-card btn-catatan" title="Catatan Wali Kelas">
                                            <i class="fas fa-sticky-note"></i> Catatan
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
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
        </div>
    </div>
</div>

<script>
function toggleCard(id) {
    const card = document.getElementById('card-' + id);
    card.classList.toggle('expanded');
}
</script>
@endsection
