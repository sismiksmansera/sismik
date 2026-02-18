@extends('layouts.app')

@section('title', 'Anggota Ekstrakurikuler - ' . $ekstra->nama_ekstrakurikuler)

@push('styles')
<style>
/* HEADER SECTION */
.ekstra-header {
    background: linear-gradient(135deg, {{ $ekstraColor }} 0%, {{ $ekstraColor }}dd 100%);
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 25px;
    color: white;
    text-align: center;
}

.ekstra-header .header-icon-large {
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

.ekstra-header .page-title {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 10px 0;
    text-transform: uppercase;
}

.ekstra-header .header-subtitle {
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
    gap: 10px;
    padding: 12px 24px;
    border-radius: 12px;
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

.btn-add {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.btn-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
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

.stat-icon.primary { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.stat-icon.success { background: linear-gradient(135deg, #10b981, #059669); }
.stat-icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }

.stat-info h3 { margin: 0; font-size: 18px; font-weight: 700; color: #1f2937; }
.stat-info p { margin: 4px 0 0 0; color: #6b7280; font-size: 12px; }

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
    background: {{ $ekstraColor }}15;
    color: {{ $ekstraColor }};
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
    border-color: {{ $ekstraColor }};
}

.member-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background: {{ $ekstraColor }}15;
    color: {{ $ekstraColor }};
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
.member-name-info .member-rombel { font-size: 12px; color: #6b7280; }

.nilai-badge {
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 600;
}

.nilai-badge.has-nilai { background: #d1fae5; color: #059669; }
.nilai-badge.no-nilai { background: #fef3c7; color: #d97706; }

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
    margin-bottom: 15px;
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

.detail-label i { font-size: 10px; color: {{ $ekstraColor }}; }
.detail-value { font-size: 14px; color: #1f2937; font-weight: 500; }

.member-actions-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding-top: 15px;
    border-top: 1px solid #e5e7eb;
}

.nilai-input-group { display: flex; align-items: center; gap: 8px; }
.nilai-input-group label { font-size: 11px; color: #6b7280; font-weight: 600; }

.nilai-select {
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 13px;
    min-width: 80px;
}

.btn-save-nilai {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 12px;
}

.btn-hapus-anggota {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    border: none;
    padding: 8px 14px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 5px;
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
    .ekstra-header { padding: 20px 15px; }
    .ekstra-header .header-icon-large { width: 60px; height: 60px; font-size: 28px; }
    .ekstra-header .page-title { font-size: 20px; }
    
    .stats-grid { grid-template-columns: repeat(3, 1fr); gap: 8px; }
    .stat-card { flex-direction: column; text-align: center; padding: 12px 8px; gap: 8px; }
    .stat-icon { width: 35px; height: 35px; font-size: 14px; }
    .stat-info h3 { font-size: 16px; }
    .stat-info p { font-size: 10px; }
    
    .action-buttons-header { flex-direction: row; gap: 10px; }
    .btn-modern { flex: 1; justify-content: center; padding: 10px 12px; font-size: 13px; }
    
    .members-cards-grid { grid-template-columns: 1fr; padding: 15px; }
    
    .member-actions-row { flex-direction: column; align-items: stretch; }
}
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-guru')
    
    <div class="main-content">
        <div class="anggota-ekstra-page">
            <!-- HEADER -->
            <div class="ekstra-header">
                <div class="header-icon-large">
                    <i class="fas {{ $ekstraIcon }}"></i>
                </div>
                <h1 class="page-title">{{ $ekstra->nama_ekstrakurikuler }}</h1>
                <p class="header-subtitle">
                    {{ $ekstra->tahun_pelajaran }} - {{ ucfirst($ekstra->semester) }}
                </p>
                <span class="header-badge">
                    <i class="fas fa-medal"></i> {{ $posisiPembina }}
                </span>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="action-buttons-header">
                <a href="{{ url()->previous() }}" class="btn-modern btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="button" class="btn-modern btn-add" onclick="openModalTambah()">
                    <i class="fas fa-user-plus"></i> Tambah Anggota
                </button>
            </div>

            <!-- STATS -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ count($anggotaList) }}</h3>
                        <p>Total Anggota</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $anggotaList->whereNotNull('nilai')->count() }}</h3>
                        <p>Sudah Dinilai</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $anggotaList->whereNull('nilai')->count() }}</h3>
                        <p>Belum Dinilai</p>
                    </div>
                </div>
            </div>

            <!-- MEMBERS SECTION -->
            <div class="members-section">
                <div class="section-header">
                    <div class="section-title">
                        <i class="fas fa-user-graduate"></i>
                        <h2>Daftar Anggota</h2>
                    </div>
                    <span class="member-count-badge">{{ count($anggotaList) }} Anggota</span>
                </div>

                @if(count($anggotaList) > 0)
                    <div class="members-cards-grid">
                        @foreach($anggotaList as $anggota)
                            @php
                                $initials = collect(explode(' ', $anggota->nama_siswa))
                                    ->map(fn($p) => strtoupper(substr($p, 0, 1)))
                                    ->take(2)
                                    ->join('');
                                $hasFoto = $anggota->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/' . $anggota->foto);
                            @endphp
                            <div class="member-card" id="card-{{ $anggota->id }}">
                                <div class="member-card-header" onclick="toggleCard({{ $anggota->id }})">
                                    <div class="member-header-left">
                                        <div class="member-avatar">
                                            @if($hasFoto)
                                                <img src="{{ asset('storage/siswa/' . $anggota->foto) }}" alt="{{ $anggota->nama_siswa }}">
                                            @else
                                                {{ $initials ?: 'S' }}
                                            @endif
                                        </div>
                                        <div class="member-name-info">
                                            <h4>{{ $anggota->nama_siswa }}</h4>
                                            <span class="member-rombel">{{ $anggota->rombel_aktif }}</span>
                                        </div>
                                    </div>
                                    <div class="member-header-right">
                                        <span class="nilai-badge {{ $anggota->nilai ? 'has-nilai' : 'no-nilai' }}">
                                            @if($anggota->nilai)
                                                <i class="fas fa-star"></i> {{ $anggota->nilai }}
                                            @else
                                                <i class="fas fa-clock"></i> Belum
                                            @endif
                                        </span>
                                        <i class="fas fa-chevron-down expand-icon"></i>
                                    </div>
                                </div>
                                <div class="member-card-body">
                                    <div class="member-details-grid">
                                        <div class="detail-item">
                                            <span class="detail-label"><i class="fas fa-id-card"></i> NIS</span>
                                            <span class="detail-value">{{ $anggota->nis }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label"><i class="fas fa-fingerprint"></i> NISN</span>
                                            <span class="detail-value">{{ $anggota->nisn }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label"><i class="fas fa-venus-mars"></i> JK</span>
                                            <span class="detail-value">{{ $anggota->jk }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label"><i class="fas fa-calendar"></i> Bergabung</span>
                                            <span class="detail-value">{{ \Carbon\Carbon::parse($anggota->tanggal_bergabung)->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="member-actions-row">
                                        <div class="nilai-input-group">
                                            <label>Nilai:</label>
                                            <select class="nilai-select" id="nilai-{{ $anggota->id }}">
                                                <option value="">Pilih</option>
                                                @foreach(['A', 'B', 'C', 'D', 'E'] as $n)
                                                    <option value="{{ $n }}" {{ $anggota->nilai == $n ? 'selected' : '' }}>{{ $n }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button" class="btn-save-nilai" onclick="simpanNilai({{ $anggota->id }})">
                                                <i class="fas fa-save"></i>
                                            </button>
                                        </div>
                                        <button type="button" class="btn-hapus-anggota" onclick="hapusAnggota({{ $anggota->id }}, '{{ $anggota->nama_siswa }}')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
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
                        <h3>Belum Ada Anggota</h3>
                        <p>Klik tombol "Tambah Anggota" untuk menambahkan siswa ke ekstrakurikuler ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH ANGGOTA -->
<div id="modalTambah" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
    <div style="background: white; border-radius: 16px; width: 90%; max-width: 800px; max-height: 85vh; overflow: hidden;">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid #e5e7eb; background: {{ $ekstraColor }}15;">
            <h3 style="margin: 0; font-size: 1.1rem; color: {{ $ekstraColor }};"><i class="fas fa-user-plus"></i> Tambah Anggota Baru</h3>
            <button type="button" onclick="closeModalTambah()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #6b7280;">&times;</button>
        </div>
        <form action="{{ route('guru.anggota-ekstrakurikuler.tambah') }}" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{ $ekstra->id }}">
            <div style="padding: 20px; max-height: 60vh; overflow-y: auto;">
                <div style="margin-bottom: 15px;">
                    <input type="text" id="searchSiswa" placeholder="Cari siswa berdasarkan nama, NIS, atau NISN..." 
                           style="width: 100%; padding: 12px 15px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 14px; box-sizing: border-box;">
                </div>
                <div id="siswaListContainer" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 10px; max-height: 400px; overflow-y: auto;">
                    <div style="text-align: center; padding: 40px; color: #6b7280;">
                        <i class="fas fa-search" style="font-size: 32px; margin-bottom: 10px;"></i>
                        <p>Ketik untuk mencari siswa...</p>
                    </div>
                </div>
                <div id="selectedCount" style="margin-top: 15px; padding: 10px; background: #f3f4f6; border-radius: 8px; font-size: 13px; color: #6b7280;">
                    <i class="fas fa-check-circle"></i> <span id="countText">0 siswa dipilih</span>
                </div>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeModalTambah()" style="padding: 10px 20px; background: #f3f4f6; color: #4b5563; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">Batal</button>
                <button type="submit" style="padding: 10px 20px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                    <i class="fas fa-plus"></i> Tambah Anggota
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleCard(id) {
    const card = document.getElementById('card-' + id);
    card.classList.toggle('expanded');
}

function openModalTambah() {
    document.getElementById('modalTambah').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeModalTambah() {
    document.getElementById('modalTambah').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Search siswa
let searchTimeout;
document.getElementById('searchSiswa').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const search = this.value;
        if (search.length < 2) return;
        
        fetch(`{{ route('guru.anggota-ekstrakurikuler.cari-siswa') }}?id={{ $ekstra->id }}&search=${encodeURIComponent(search)}`)
            .then(r => r.json())
            .then(data => {
                const container = document.getElementById('siswaListContainer');
                if (data.siswa.length === 0) {
                    container.innerHTML = '<div style="text-align: center; padding: 40px; color: #6b7280;"><i class="fas fa-user-slash" style="font-size: 32px; margin-bottom: 10px;"></i><p>Tidak ada siswa ditemukan</p></div>';
                    return;
                }
                
                container.innerHTML = data.siswa.map(s => `
                    <label style="display: flex; align-items: center; gap: 10px; padding: 12px; background: #f8fafc; border-radius: 10px; cursor: pointer; border: 1px solid #e5e7eb;">
                        <input type="checkbox" name="siswa_ids[]" value="${s.id}" class="siswa-checkbox" style="width: 18px; height: 18px; accent-color: {{ $ekstraColor }};" onchange="updateCount()">
                        <div>
                            <div style="font-weight: 600; color: #1f2937;">${s.nama}</div>
                            <div style="font-size: 12px; color: #6b7280;">${s.nis} · ${s.rombel_aktif}</div>
                        </div>
                    </label>
                `).join('');
            });
    }, 300);
});

function updateCount() {
    const count = document.querySelectorAll('.siswa-checkbox:checked').length;
    document.getElementById('countText').textContent = count + ' siswa dipilih';
}

function simpanNilai(id) {
    const nilai = document.getElementById('nilai-' + id).value;
    fetch('{{ route("guru.anggota-ekstrakurikuler.update-nilai") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({anggota_id: id, nilai: nilai})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message, 'error');
        }
    });
}

function hapusAnggota(id, nama) {
    if (!confirm('Hapus ' + nama + ' dari ekstrakurikuler ini?')) return;
    
    fetch('{{ route("guru.anggota-ekstrakurikuler.hapus") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({anggota_id: id, ekstra_id: {{ $ekstra->id }}})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            document.getElementById('card-' + id).remove();
        } else {
            showToast(data.message, 'error');
        }
    });
}

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.style.cssText = `position: fixed; top: 20px; right: 20px; background: ${type === 'success' ? '#10b981' : '#ef4444'}; color: white; padding: 15px 25px; border-radius: 10px; z-index: 9999; box-shadow: 0 10px 30px rgba(0,0,0,0.2);`;
    toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i> ${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Close modal on outside click
document.getElementById('modalTambah').addEventListener('click', function(e) {
    if (e.target === this) closeModalTambah();
});
</script>
@endsection
