@extends('layouts.app-guru-bk')

@section('title', 'Siswa Wali')

@section('content')
<div class="main-content siswa-wali-page">
    <!-- HEADER SECTION -->
    <div class="header-section">
        <div class="header-icon-large">
            <i class="fas fa-user-graduate"></i>
        </div>
        <h1 class="page-title">Siswa Wali</h1>
        <p class="header-subtitle">Daftar siswa yang Anda bimbing sebagai Guru Wali</p>
        <span class="header-badge">
            <i class="fas fa-calendar-alt"></i> {{ $tahunPelajaran }} - {{ ucfirst($semesterAktif) }}
        </span>
    </div>

    <!-- STATS CARDS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $totalSiswa }}</h3>
                <p>Total Siswa</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-mars"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $totalLaki }}</h3>
                <p>Laki-laki</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon pink">
                <i class="fas fa-venus"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $totalPerempuan }}</h3>
                <p>Perempuan</p>
            </div>
        </div>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="action-buttons-header">
        <a href="{{ route('guru_bk.tugas-tambahan') }}" class="btn-modern btn-back">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if($totalSiswa > 0)
        @foreach($siswaByTingkat as $tingkat => $siswas)
            <div class="tingkat-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h2>Kelas {{ $tingkat }} ({{ count($siswas) }} Siswa)</h2>
                </div>
                
                <div class="student-cards-grid">
                    @foreach($siswas as $siswa)
                        <div class="student-card">
                            <div class="student-card-body">
                                <div class="student-photo {{ $siswa->foto ? 'clickable' : '' }}"
                                     @if($siswa->foto) onclick="openPhotoModal('{{ asset('storage/siswa/' . $siswa->foto) }}', '{{ $siswa->nama }}')" @endif>
                                    @if($siswa->foto)
                                        <img src="{{ asset('storage/siswa/' . $siswa->foto) }}" alt="{{ $siswa->nama }}">
                                    @else
                                        {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                                    @endif
                                </div>
                                <div class="student-info">
                                    <h4>{{ $siswa->nama }}</h4>
                                    <div class="student-nisn">NISN: {{ $siswa->nisn }}</div>
                                    <div class="student-meta">
                                        <span class="student-meta-item rombel">{{ $siswa->rombel ?? '-' }}</span>
                                        <span class="student-meta-item {{ $siswa->jk == 'Laki-laki' ? 'gender-l' : 'gender-p' }}">
                                            {{ $siswa->jk }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @else
        <div class="empty-state">
            <div class="empty-icon-wrapper">
                <i class="fas fa-user-graduate"></i>
            </div>
            <h3>Belum Ada Siswa Wali</h3>
            <p>Anda belum memiliki siswa wali untuk periode {{ $tahunPelajaran }} - {{ ucfirst($semesterAktif) }}</p>
        </div>
    @endif
</div>

<!-- Photo Modal -->
<div id="photoModal" class="photo-modal" onclick="closePhotoModal()">
    <div class="photo-modal-content" onclick="event.stopPropagation()">
        <button onclick="closePhotoModal()" class="photo-modal-close">
            <i class="fas fa-times"></i>
        </button>
        <img id="modalPhoto" src="" alt="Foto Siswa">
        <div class="photo-modal-info">
            <h4 id="modalName"></h4>
            <p>Foto Profil Siswa</p>
        </div>
    </div>
</div>

<style>
/* HEADER SECTION */
.siswa-wali-page {
    padding: 30px;
    background: #f8f9fa;
    min-height: calc(100vh - 60px);
}

.header-section {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 25px;
    color: white;
    text-align: center;
}

.header-section .header-icon-large {
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

.header-section .page-title {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 10px 0;
    text-transform: uppercase;
}

.header-section .header-subtitle {
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

.stat-icon.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.stat-icon.blue { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.stat-icon.pink { background: linear-gradient(135deg, #ec4899, #db2777); }

.stat-info { flex: 1; min-width: 0; }
.stat-info h3 { margin: 0; font-size: 20px; font-weight: 700; color: #1f2937; }
.stat-info p { margin: 4px 0 0 0; color: #6b7280; font-size: 12px; font-weight: 500; }

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

/* SECTION */
.tingkat-section {
    margin-bottom: 30px;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.section-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
}

.section-header h2 {
    margin: 0;
    color: #1f2937;
    font-size: 1.25rem;
}

/* STUDENT CARDS GRID */
.student-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}

/* STUDENT CARD */
.student-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb;
}

.student-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}

.student-card-body {
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.student-photo {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    font-weight: 700;
    flex-shrink: 0;
    overflow: hidden;
}

.student-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.student-photo.clickable {
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}

.student-photo.clickable:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}

.student-info {
    flex: 1;
    min-width: 0;
}

.student-info h4 {
    margin: 0 0 4px 0;
    font-size: 15px;
    font-weight: 600;
    color: #1f2937;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.student-nisn {
    font-size: 12px;
    color: #6b7280;
    margin-bottom: 6px;
}

.student-meta {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.student-meta-item {
    font-size: 11px;
    padding: 4px 10px;
    border-radius: 20px;
    background: #f3f4f6;
    color: #374151;
}

.student-meta-item.rombel {
    background: rgba(139, 92, 246, 0.1);
    color: #7c3aed;
}

.student-meta-item.gender-l {
    background: rgba(59, 130, 246, 0.1);
    color: #2563eb;
}

.student-meta-item.gender-p {
    background: rgba(236, 72, 153, 0.1);
    color: #db2777;
}

/* EMPTY STATE */
.empty-state {
    background: white;
    border-radius: 16px;
    padding: 60px 30px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.empty-icon-wrapper {
    width: 100px;
    height: 100px;
    background: #f3f4f6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 25px;
}

.empty-icon-wrapper i {
    font-size: 40px;
    color: #9ca3af;
}

.empty-state h3 {
    margin: 0 0 10px 0;
    color: #1f2937;
    font-size: 1.25rem;
}

.empty-state p {
    margin: 0;
    color: #6b7280;
}

/* PHOTO MODAL */
.photo-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    z-index: 9999;
    justify-content: center;
    align-items: center;
}

.photo-modal-content {
    position: relative;
    max-width: 90%;
    max-height: 90%;
    text-align: center;
}

.photo-modal-content img {
    max-width: 400px;
    max-height: 70vh;
    border-radius: 12px;
    box-shadow: 0 0 30px rgba(0, 0, 0, 0.5);
}

.photo-modal-close {
    position: absolute;
    top: -40px;
    right: 0;
    background: none;
    border: none;
    font-size: 28px;
    color: white;
    cursor: pointer;
}

.photo-modal-info {
    margin-top: 15px;
    color: white;
}

.photo-modal-info h4 {
    margin: 0;
    font-size: 1.2rem;
}

.photo-modal-info p {
    margin: 5px 0 0;
    opacity: 0.8;
    font-size: 0.9rem;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .siswa-wali-page { padding: 20px; }
    .header-section { padding: 20px 15px; }
    .header-section .header-icon-large {
        width: 60px; height: 60px; font-size: 28px; margin-bottom: 15px;
    }
    .header-section .page-title { font-size: 20px; }
    
    .stats-grid { grid-template-columns: repeat(3, 1fr); gap: 8px; }
    .stat-card {
        flex-direction: column; text-align: center; padding: 12px 8px; gap: 8px;
    }
    .stat-icon { width: 35px; height: 35px; font-size: 14px; }
    .stat-info h3 { font-size: 16px; }
    .stat-info p { font-size: 10px; }
    
    .student-cards-grid { grid-template-columns: 1fr; gap: 12px; }
    .student-card-body { padding: 15px; }
    .student-photo { width: 50px; height: 50px; font-size: 20px; }
}
</style>

<script>
function openPhotoModal(photoUrl, name) {
    document.getElementById('modalPhoto').src = photoUrl;
    document.getElementById('modalName').textContent = name;
    document.getElementById('photoModal').style.display = 'flex';
}

function closePhotoModal() {
    document.getElementById('photoModal').style.display = 'none';
}

// ESC to close modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePhotoModal();
    }
});
</script>
@endsection
