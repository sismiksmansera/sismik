@extends('layouts.app-guru-bk')

@section('title', 'Catatan Guru Wali - ' . $siswa->nama)

@section('content')
<div class="main-content catatan-page">
    <!-- HEADER -->
    <div class="page-header">
        <div class="header-content">
            <div class="student-avatar">
                @if($siswa->foto)
                    <img src="{{ asset('storage/siswa/' . $siswa->foto) }}" alt="{{ $siswa->nama }}">
                @else
                    {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                @endif
            </div>
            <div class="header-info">
                <h1>Catatan Guru Wali</h1>
                <div class="header-meta">
                    <span class="meta-badge"><i class="fas fa-user"></i> {{ $siswa->nama }}</span>
                    <span class="meta-badge"><i class="fas fa-users"></i> {{ $rombel }}</span>
                    <span class="meta-badge"><i class="fas fa-calendar"></i> {{ $periodeAktif->tahun_pelajaran }} - {{ $periodeAktif->semester }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ACTION BAR -->
    <div class="action-bar">
        <a href="{{ route('guru_bk.siswa-wali') }}" class="btn-modern btn-back">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <a href="{{ route('guru_bk.catatan-guru-wali.create', $siswa->id) }}" class="btn-modern btn-primary">
            <i class="fas fa-plus"></i> Tambah Catatan
        </a>
    </div>

    <!-- ALERT -->
    @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- CATATAN LIST -->
    @if($catatanList->count() > 0)
        <div class="catatan-list">
            @foreach($catatanList as $catatan)
                @php
                    $typeClass = match($catatan->jenis_bimbingan) {
                        'Bimbingan Akademik' => 'type-akademik',
                        'Bimbingan Karakter' => 'type-karakter',
                        'Sosial Emosional' => 'type-sosial',
                        'Kedisiplinan' => 'type-disiplin',
                        'Potensi dan Minat' => 'type-potensi',
                        'Bimbingan Ibadah' => 'type-ibadah',
                        default => ''
                    };
                @endphp
                <div class="catatan-card">
                    <div class="catatan-header">
                        <span class="catatan-date">
                            <i class="fas fa-calendar-alt"></i>
                            {{ \Carbon\Carbon::parse($catatan->tanggal_pencatatan)->format('d F Y') }}
                        </span>
                        <span class="catatan-type {{ $typeClass }}">
                            {{ $catatan->jenis_bimbingan }}
                        </span>
                    </div>
                    <div class="catatan-body">
                        <p class="catatan-text">{{ $catatan->catatan }}</p>
                        <div class="catatan-badges">
                            @if($catatan->nilai_praktik_ibadah)
                                <span class="badge-info">
                                    <strong>Nilai Ibadah:</strong> {{ $catatan->nilai_praktik_ibadah }}
                                </span>
                            @endif
                            @if($catatan->perkembangan)
                                <span class="badge-info">
                                    <strong>Perkembangan:</strong> {{ $catatan->perkembangan }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="catatan-actions">
                        <a href="{{ route('guru_bk.catatan-guru-wali.edit', $catatan->id) }}" class="btn-modern btn-sm btn-edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('guru_bk.catatan-guru-wali.destroy', $catatan->id) }}" method="POST" 
                              onsubmit="return confirm('Yakin ingin menghapus catatan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-modern btn-sm btn-delete">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-sticky-note"></i>
            </div>
            <h3>Belum Ada Catatan</h3>
            <p>Belum ada catatan guru wali untuk siswa ini. Klik tombol "Tambah Catatan" untuk membuat catatan baru.</p>
        </div>
    @endif
</div>

<style>
/* PAGE STYLES */
.catatan-page {
    padding: 30px;
    background: #f8f9fa;
    min-height: calc(100vh - 60px);
}

/* HEADER */
.page-header {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 25px;
    color: white;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 20px;
}

.student-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    font-weight: 700;
    overflow: hidden;
    flex-shrink: 0;
}

.student-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.header-info h1 {
    margin: 0 0 8px 0;
    font-size: 24px;
    font-weight: 700;
}

.header-meta {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.meta-badge {
    background: rgba(255,255,255,0.2);
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

/* ACTION BAR */
.action-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    flex-wrap: wrap;
    gap: 15px;
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

.btn-primary {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
}

/* ALERT */
.alert-success {
    background: #d1fae5;
    border: 1px solid #10b981;
    color: #065f46;
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* CATATAN CARDS */
.catatan-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.catatan-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb;
}

.catatan-card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.12);
}

.catatan-header {
    padding: 15px 20px;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.catatan-date {
    font-weight: 600;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 8px;
}

.catatan-type {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.type-akademik { background: #dbeafe; color: #1d4ed8; }
.type-karakter { background: #fef3c7; color: #d97706; }
.type-sosial { background: #fce7f3; color: #db2777; }
.type-disiplin { background: #fee2e2; color: #dc2626; }
.type-potensi { background: #d1fae5; color: #059669; }
.type-ibadah { background: #e0e7ff; color: #4338ca; }

.catatan-body {
    padding: 20px;
}

.catatan-text {
    color: #374151;
    line-height: 1.6;
    margin-bottom: 15px;
}

.catatan-badges {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.badge-info {
    background: #f3f4f6;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    color: #374151;
}

.badge-info strong {
    color: #1f2937;
}

.catatan-actions {
    padding: 15px 20px;
    border-top: 1px solid #f3f4f6;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.btn-sm {
    padding: 8px 15px;
    font-size: 13px;
    border-radius: 8px;
}

.btn-edit {
    background: #f3f4f6;
    color: #374151;
}

.btn-edit:hover {
    background: #e5e7eb;
}

.btn-delete {
    background: #fee2e2;
    color: #dc2626;
}

.btn-delete:hover {
    background: #fecaca;
}

/* EMPTY STATE */
.empty-state {
    background: white;
    border-radius: 16px;
    padding: 60px 30px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.empty-icon {
    width: 100px;
    height: 100px;
    background: #f3f4f6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 25px;
}

.empty-icon i {
    font-size: 40px;
    color: #9ca3af;
}

.empty-state h3 {
    margin: 0 0 10px;
    color: #1f2937;
}

.empty-state p {
    color: #6b7280;
    margin: 0;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .catatan-page { padding: 20px; }
    .header-content { flex-direction: column; text-align: center; }
    .header-meta { justify-content: center; }
    .action-bar { flex-direction: column; align-items: stretch; }
    .btn-modern { justify-content: center; }
}
</style>
@endsection
