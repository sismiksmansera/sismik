@extends($layout ?? 'layouts.app')

@section('title', 'Manajemen Talenta | SISMIK')

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        {{-- Header --}}
        <div class="mt-header">
            <div class="mt-header-content">
                <div class="mt-header-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="mt-header-text">
                    <h1>Manajemen Talenta</h1>
                    <p>Data peserta Olimpiade Sains Nasional 2026</p>
                </div>
            </div>
            <div class="mt-header-stats">
                <div class="mt-stat-badge">
                    <i class="fas fa-users"></i>
                    <span>{{ count($siswaList) }} Peserta Terdaftar</span>
                </div>
                <div class="mt-stat-badge gold">
                    <i class="fas fa-trophy"></i>
                    <span>{{ count($mapelStats) }} Mata Pelajaran</span>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        @if(count($mapelStats) > 0)
        <div class="mt-stats-grid">
            @php
                $mapelIcons = [
                    'Matematika' => 'fa-calculator',
                    'Fisika' => 'fa-atom',
                    'Kimia' => 'fa-flask',
                    'Biologi' => 'fa-dna',
                    'Geografi' => 'fa-globe-asia',
                    'Astronomi' => 'fa-star',
                    'Informatika' => 'fa-laptop-code',
                    'Ekonomi' => 'fa-chart-line',
                    'Kebumian' => 'fa-mountain',
                ];
                $mapelColors = [
                    'Matematika' => '#3b82f6',
                    'Fisika' => '#8b5cf6',
                    'Kimia' => '#10b981',
                    'Biologi' => '#f59e0b',
                    'Geografi' => '#06b6d4',
                    'Astronomi' => '#ec4899',
                    'Informatika' => '#6366f1',
                    'Ekonomi' => '#f97316',
                    'Kebumian' => '#84cc16',
                ];
            @endphp
            @foreach($mapelStats as $mapel => $count)
            <div class="mt-stat-card" style="--card-color: {{ $mapelColors[$mapel] ?? '#64748b' }}">
                <div class="mt-stat-icon">
                    <i class="fas {{ $mapelIcons[$mapel] ?? 'fa-book' }}"></i>
                </div>
                <div class="mt-stat-info">
                    <span class="mt-stat-count">{{ $count }}</span>
                    <span class="mt-stat-label">{{ $mapel }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Filter --}}
        <div class="mt-filter-bar">
            <form method="GET" action="{{ route('admin.manajemen-talenta.index') }}" class="mt-filter-form">
                <div class="mt-filter-group">
                    <label><i class="fas fa-book"></i> Mata Pelajaran</label>
                    <select name="mapel" onchange="this.form.submit()">
                        <option value="">Semua Mapel</option>
                        @foreach($mapelList as $mapel)
                            <option value="{{ $mapel }}" {{ $filterMapel == $mapel ? 'selected' : '' }}>{{ $mapel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-filter-group">
                    <label><i class="fas fa-calendar"></i> Angkatan</label>
                    <select name="angkatan" onchange="this.form.submit()">
                        <option value="">Semua Angkatan</option>
                        @foreach($angkatanList as $angkatan)
                            <option value="{{ $angkatan }}" {{ $filterAngkatan == $angkatan ? 'selected' : '' }}>{{ $angkatan }}</option>
                        @endforeach
                    </select>
                </div>
                @if($filterMapel || $filterAngkatan)
                <a href="{{ route('admin.manajemen-talenta.index') }}" class="mt-filter-reset">
                    <i class="fas fa-times"></i> Reset
                </a>
                @endif
            </form>
        </div>

        {{-- Student List --}}
        <div class="mt-content-section">
            <div class="mt-section-header">
                <div class="mt-section-title">
                    <i class="fas fa-medal"></i>
                    <h2>Daftar Peserta OSN 2026</h2>
                </div>
                <span class="mt-section-count">{{ count($siswaList) }} Siswa</span>
            </div>

            @if(count($siswaList) == 0)
            <div class="mt-empty-state">
                <div class="mt-empty-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <h3>Belum Ada Peserta Terdaftar</h3>
                <p>Siswa dapat mendaftar melalui halaman Pendaftaran OSN.</p>
            </div>
            @else
            <div class="mt-table-wrapper">
                <table class="mt-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>Nama Siswa</th>
                            <th>NISN</th>
                            <th>Rombel</th>
                            <th>Mapel OSN</th>
                            <th>Ikut OSN 2025</th>
                            <th>Email</th>
                            <th>No HP</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswaList as $index => $siswa)
                        <tr id="row-{{ $siswa->id }}">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="mt-avatar">
                                    @if($siswa->foto_url)
                                        <img src="{{ $siswa->foto_url }}" alt="{{ $siswa->nama }}">
                                    @else
                                        <div class="mt-avatar-initials">
                                            {{ collect(explode(' ', $siswa->nama))->map(fn($p) => strtoupper(substr($p, 0, 1)))->take(2)->join('') }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="mt-name-cell">
                                    <strong>{{ $siswa->nama }}</strong>
                                    <span>Angkatan {{ $siswa->angkatan_masuk ?? '-' }}</span>
                                </div>
                            </td>
                            <td><span class="mt-badge blue">{{ $siswa->nisn }}</span></td>
                            <td>{{ $siswa->rombel_aktif }}</td>
                            <td>
                                <span class="mt-badge mapel" style="--badge-color: {{ $mapelColors[$siswa->mapel_osn_2026] ?? '#64748b' }}">
                                    <i class="fas {{ $mapelIcons[$siswa->mapel_osn_2026] ?? 'fa-book' }}"></i>
                                    {{ $siswa->mapel_osn_2026 }}
                                </span>
                            </td>
                            <td>
                                @if($siswa->ikut_osn_2025 == 'Ya')
                                    <span class="mt-badge green"><i class="fas fa-check"></i> Ya</span>
                                @else
                                    <span class="mt-badge gray">Tidak</span>
                                @endif
                            </td>
                            <td>{{ $siswa->email ?? '-' }}</td>
                            <td>{{ $siswa->nohp_siswa ?? '-' }}</td>
                            <td>
                                <button class="mt-btn-remove" onclick="removeOsn({{ $siswa->id }}, '{{ addslashes($siswa->nama) }}', '{{ $siswa->mapel_osn_2026 }}')" title="Hapus Pendaftaran">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    /* HEADER */
    .mt-header {
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.15), rgba(59, 130, 246, 0.1));
        border: 1px solid rgba(139, 92, 246, 0.2);
        border-radius: 16px;
        padding: 24px 28px;
        margin-bottom: 24px;
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 16px;
    }
    .mt-header-content { display: flex; align-items: center; gap: 16px; }
    .mt-header-icon {
        width: 52px; height: 52px; border-radius: 14px;
        background: linear-gradient(135deg, #8b5cf6, #6366f1);
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 22px;
    }
    .mt-header-text h1 { font-size: 22px; font-weight: 800; color: #f1f5f9; margin: 0; }
    .mt-header-text p { font-size: 13px; color: #94a3b8; margin: 4px 0 0; }
    .mt-header-stats { display: flex; gap: 12px; }
    .mt-stat-badge {
        display: flex; align-items: center; gap: 8px;
        padding: 8px 16px; border-radius: 10px;
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.2);
        color: #60a5fa; font-size: 13px; font-weight: 600;
    }
    .mt-stat-badge.gold {
        background: rgba(245, 158, 11, 0.1);
        border-color: rgba(245, 158, 11, 0.2);
        color: #fbbf24;
    }

    /* STATS GRID */
    .mt-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 14px; margin-bottom: 24px;
    }
    .mt-stat-card {
        background: rgba(30, 41, 59, 0.6);
        border: 1px solid rgba(148, 163, 184, 0.08);
        border-radius: 14px;
        padding: 18px;
        display: flex; align-items: center; gap: 14px;
        transition: all 0.3s;
        border-left: 3px solid var(--card-color);
    }
    .mt-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    }
    .mt-stat-icon {
        width: 40px; height: 40px; border-radius: 10px;
        background: color-mix(in srgb, var(--card-color) 15%, transparent);
        display: flex; align-items: center; justify-content: center;
        color: var(--card-color); font-size: 16px;
    }
    .mt-stat-count {
        font-size: 22px; font-weight: 800; color: #f1f5f9;
        display: block; line-height: 1;
    }
    .mt-stat-label {
        font-size: 11px; color: #94a3b8; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.3px;
    }

    /* FILTER */
    .mt-filter-bar {
        background: rgba(30, 41, 59, 0.6);
        border: 1px solid rgba(148, 163, 184, 0.08);
        border-radius: 14px;
        padding: 16px 20px;
        margin-bottom: 24px;
    }
    .mt-filter-form { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
    .mt-filter-group { display: flex; flex-direction: column; gap: 6px; }
    .mt-filter-group label {
        font-size: 11px; font-weight: 600; color: #94a3b8;
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .mt-filter-group label i { margin-right: 4px; font-size: 10px; }
    .mt-filter-group select {
        padding: 10px 14px; border-radius: 10px;
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(148, 163, 184, 0.15);
        color: #e2e8f0; font-size: 13px; font-family: 'Inter', sans-serif;
        min-width: 180px; cursor: pointer;
    }
    .mt-filter-group select:focus {
        outline: none;
        border-color: rgba(139, 92, 246, 0.5);
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
    }
    .mt-filter-reset {
        padding: 10px 16px; border-radius: 10px;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.2);
        color: #f87171; font-size: 13px; font-weight: 600;
        text-decoration: none; align-self: flex-end;
        transition: all 0.2s;
    }
    .mt-filter-reset:hover {
        background: rgba(239, 68, 68, 0.2);
        color: #fca5a5;
    }

    /* CONTENT */
    .mt-content-section {
        background: rgba(30, 41, 59, 0.6);
        border: 1px solid rgba(148, 163, 184, 0.08);
        border-radius: 16px;
        padding: 24px;
    }
    .mt-section-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 20px; padding-bottom: 16px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.08);
    }
    .mt-section-title { display: flex; align-items: center; gap: 10px; }
    .mt-section-title i { color: #fbbf24; font-size: 18px; }
    .mt-section-title h2 { font-size: 17px; font-weight: 700; color: #f1f5f9; margin: 0; }
    .mt-section-count {
        padding: 6px 14px; border-radius: 8px;
        background: rgba(139, 92, 246, 0.1);
        border: 1px solid rgba(139, 92, 246, 0.2);
        color: #a78bfa; font-size: 12px; font-weight: 600;
    }

    /* EMPTY STATE */
    .mt-empty-state {
        text-align: center; padding: 50px 20px;
    }
    .mt-empty-icon {
        width: 70px; height: 70px; border-radius: 50%;
        background: rgba(139, 92, 246, 0.1);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 16px; font-size: 28px; color: #8b5cf6;
    }
    .mt-empty-state h3 { font-size: 16px; color: #e2e8f0; margin-bottom: 8px; }
    .mt-empty-state p { color: #94a3b8; font-size: 13px; }

    /* TABLE */
    .mt-table-wrapper { overflow-x: auto; }
    .mt-table {
        width: 100%; border-collapse: collapse;
        font-size: 13px;
    }
    .mt-table thead th {
        padding: 12px 14px;
        text-align: left;
        font-size: 11px; font-weight: 700;
        color: #94a3b8; text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.1);
        white-space: nowrap;
    }
    .mt-table tbody tr {
        border-bottom: 1px solid rgba(148, 163, 184, 0.05);
        transition: background 0.2s;
    }
    .mt-table tbody tr:hover {
        background: rgba(139, 92, 246, 0.05);
    }
    .mt-table tbody td {
        padding: 12px 14px;
        color: #e2e8f0;
        vertical-align: middle;
    }

    /* AVATAR */
    .mt-avatar {
        width: 38px; height: 38px; border-radius: 10px;
        overflow: hidden; flex-shrink: 0;
    }
    .mt-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .mt-avatar-initials {
        width: 100%; height: 100%;
        background: linear-gradient(135deg, #8b5cf6, #6366f1);
        display: flex; align-items: center; justify-content: center;
        color: white; font-weight: 700; font-size: 13px;
        border-radius: 10px;
    }

    /* NAME CELL */
    .mt-name-cell strong { display: block; color: #f1f5f9; font-weight: 600; }
    .mt-name-cell span { font-size: 11px; color: #94a3b8; }

    /* BADGES */
    .mt-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 10px; border-radius: 8px;
        font-size: 11px; font-weight: 600;
        white-space: nowrap;
    }
    .mt-badge.blue {
        background: rgba(59, 130, 246, 0.12);
        color: #60a5fa;
    }
    .mt-badge.green {
        background: rgba(16, 185, 129, 0.12);
        color: #34d399;
    }
    .mt-badge.gray {
        background: rgba(148, 163, 184, 0.1);
        color: #94a3b8;
    }
    .mt-badge.mapel {
        background: color-mix(in srgb, var(--badge-color) 12%, transparent);
        color: var(--badge-color);
    }

    /* REMOVE BUTTON */
    .mt-btn-remove {
        width: 32px; height: 32px; border-radius: 8px;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.2);
        color: #f87171; font-size: 13px;
        cursor: pointer; display: flex;
        align-items: center; justify-content: center;
        transition: all 0.2s;
    }
    .mt-btn-remove:hover {
        background: rgba(239, 68, 68, 0.2);
        transform: scale(1.1);
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .mt-header { flex-direction: column; align-items: flex-start; }
        .mt-stats-grid { grid-template-columns: repeat(2, 1fr); }
        .mt-filter-form { flex-direction: column; }
        .mt-filter-group select { min-width: 100%; }
        .mt-filter-reset { align-self: flex-start; }
    }
</style>

<script>
function removeOsn(siswaId, nama, mapel) {
    if (!confirm(`Yakin ingin menghapus pendaftaran OSN ${mapel} untuk ${nama}?`)) return;

    fetch('{{ route("admin.manajemen-talenta.remove-osn") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ siswa_id: siswaId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const row = document.getElementById('row-' + siswaId);
            if (row) {
                row.style.transition = 'opacity 0.3s, transform 0.3s';
                row.style.opacity = '0';
                row.style.transform = 'translateX(20px)';
                setTimeout(() => row.remove(), 300);
            }
            alert(data.message);
        } else {
            alert(data.message || 'Gagal menghapus pendaftaran');
        }
    })
    .catch(() => alert('Gagal menghubungi server'));
}
</script>
@endsection
