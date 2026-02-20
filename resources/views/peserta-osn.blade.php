<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peserta OSN 2026 | SISMIK</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh; color: #e2e8f0;
        }

        .bg-pattern {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-image: radial-gradient(circle at 20% 50%, rgba(59, 130, 246, 0.08) 0%, transparent 50%),
                              radial-gradient(circle at 80% 20%, rgba(139, 92, 246, 0.08) 0%, transparent 50%),
                              radial-gradient(circle at 40% 80%, rgba(16, 185, 129, 0.06) 0%, transparent 50%);
            z-index: 0;
        }

        .page-wrapper {
            position: relative; z-index: 1;
            max-width: 900px; margin: 0 auto;
            padding: 30px 20px 60px;
        }

        /* HEADER */
        .osn-header {
            text-align: center; margin-bottom: 35px;
            animation: fadeInDown 0.6s ease;
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .osn-logo {
            width: 90px; height: 90px;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            border-radius: 24px;
            display: flex; align-items: center; justify-content: center;
            font-size: 42px; color: white;
            margin: 0 auto 20px;
            box-shadow: 0 15px 40px rgba(59, 130, 246, 0.3);
            position: relative;
        }
        .osn-logo::after {
            content: '';
            position: absolute; inset: -3px;
            border-radius: 27px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6, #ec4899);
            z-index: -1; opacity: 0.5; filter: blur(10px);
        }

        .osn-header h1 {
            font-size: 28px; font-weight: 800;
            background: linear-gradient(135deg, #60a5fa, #a78bfa);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }
        .osn-header p { color: #94a3b8; font-size: 15px; }
        .osn-header .school-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            padding: 8px 20px; border-radius: 25px;
            font-size: 13px; color: #60a5fa; font-weight: 600;
            margin-top: 15px;
        }

        /* ACTION BUTTONS */
        .action-bar {
            display: flex; justify-content: center; gap: 12px;
            margin-bottom: 25px; flex-wrap: wrap;
        }
        .btn-link {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 22px; border-radius: 12px;
            font-size: 13px; font-weight: 600; text-decoration: none;
            transition: all 0.3s; border: none; cursor: pointer;
        }
        .btn-link.primary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }
        .btn-link.primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4); }
        .btn-link.ghost {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #94a3b8;
        }
        .btn-link.ghost:hover { background: rgba(255, 255, 255, 0.1); color: #e2e8f0; }

        /* STATS */
        .stats-row {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 12px; margin-bottom: 30px;
        }
        .stat-item {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px; padding: 18px 16px;
            text-align: center; transition: all 0.3s;
        }
        .stat-item:hover { background: rgba(255, 255, 255, 0.07); transform: translateY(-2px); }
        .stat-number {
            font-size: 28px; font-weight: 800;
            background: linear-gradient(135deg, #60a5fa, #a78bfa);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .stat-label { font-size: 11px; color: #64748b; margin-top: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }

        /* FILTER TABS */
        .filter-bar {
            display: flex; gap: 8px; margin-bottom: 25px;
            overflow-x: auto; padding-bottom: 4px;
            -webkit-overflow-scrolling: touch;
        }
        .filter-tab {
            padding: 8px 18px; border-radius: 10px;
            font-size: 12px; font-weight: 600;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #94a3b8; cursor: pointer;
            white-space: nowrap; transition: all 0.2s;
        }
        .filter-tab:hover { background: rgba(255, 255, 255, 0.1); color: #e2e8f0; }
        .filter-tab.active {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white; border-color: transparent;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }
        .filter-tab .tab-count {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 20px; height: 20px; border-radius: 10px;
            font-size: 10px; font-weight: 700;
            background: rgba(255, 255, 255, 0.15);
            margin-left: 6px; padding: 0 6px;
        }
        .filter-tab.active .tab-count {
            background: rgba(255, 255, 255, 0.25);
        }

        /* SEARCH */
        .search-container {
            position: relative; margin-bottom: 25px;
        }
        .search-container input {
            width: 100%; padding: 14px 18px 14px 45px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px; color: #e2e8f0;
            font-size: 14px; font-family: 'Inter', sans-serif;
            transition: all 0.3s;
        }
        .search-container input::placeholder { color: #475569; }
        .search-container input:focus {
            outline: none; border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .search-container .search-icon {
            position: absolute; left: 16px; top: 50%; transform: translateY(-50%);
            color: #475569; font-size: 16px;
        }

        /* PESERTA GRID */
        .peserta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 14px;
        }

        .peserta-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px; padding: 18px;
            transition: all 0.3s; animation: fadeInUp 0.4s ease;
        }
        .peserta-card:hover {
            background: rgba(255, 255, 255, 0.07);
            border-color: rgba(59, 130, 246, 0.3);
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .peserta-top {
            display: flex; align-items: center; gap: 14px; margin-bottom: 14px;
        }
        .peserta-avatar {
            width: 50px; height: 50px; border-radius: 14px;
            overflow: hidden; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; font-weight: 700; color: white;
        }
        .peserta-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .peserta-avatar.laki { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .peserta-avatar.perempuan { background: linear-gradient(135deg, #ec4899, #db2777); }

        .peserta-nama { font-size: 14px; font-weight: 700; color: #f1f5f9; margin-bottom: 2px; }
        .peserta-rombel { font-size: 11px; color: #64748b; }

        .peserta-meta {
            display: flex; flex-direction: column; gap: 8px;
        }
        .meta-row {
            display: flex; align-items: center; gap: 8px;
            font-size: 12px; color: #94a3b8;
        }
        .meta-row i { width: 14px; text-align: center; color: #475569; font-size: 11px; }

        .mapel-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 12px; border-radius: 8px;
            font-size: 11px; font-weight: 700;
            margin-top: 10px;
        }

        .mapel-badge.matematika { background: rgba(59, 130, 246, 0.15); color: #60a5fa; }
        .mapel-badge.fisika { background: rgba(139, 92, 246, 0.15); color: #a78bfa; }
        .mapel-badge.kimia { background: rgba(236, 72, 153, 0.15); color: #f472b6; }
        .mapel-badge.biologi { background: rgba(16, 185, 129, 0.15); color: #34d399; }
        .mapel-badge.geografi { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }
        .mapel-badge.astronomi { background: rgba(99, 102, 241, 0.15); color: #818cf8; }
        .mapel-badge.informatika { background: rgba(6, 182, 212, 0.15); color: #22d3ee; }
        .mapel-badge.ekonomi { background: rgba(244, 63, 94, 0.15); color: #fb7185; }
        .mapel-badge.kebumian { background: rgba(168, 85, 247, 0.15); color: #c084fc; }

        /* EMPTY */
        .empty-state {
            text-align: center; padding: 60px 20px;
            color: #475569;
        }
        .empty-state i { font-size: 50px; margin-bottom: 15px; display: block; }
        .empty-state h3 { font-size: 18px; color: #64748b; margin-bottom: 8px; }
        .empty-state p { font-size: 13px; }

        /* RESPONSIVE */
        @media (max-width: 600px) {
            .osn-header h1 { font-size: 22px; }
            .peserta-grid { grid-template-columns: 1fr; }
            .stats-row { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>
<div class="bg-pattern"></div>

<div class="page-wrapper">
    <div class="osn-header">
        <div class="osn-logo"><i class="fas fa-trophy"></i></div>
        <h1>Peserta OSN 2026</h1>
        <p>Daftar Siswa Terdaftar Olimpiade Sains Nasional</p>
        <div class="school-badge">
            <i class="fas fa-school"></i> SMA Negeri 1 Seram Bagian Timur
        </div>
    </div>

    <div class="action-bar">
        <a href="{{ route('pendaftaran-osn') }}" class="btn-link primary">
            <i class="fas fa-edit"></i> Daftar OSN
        </a>
        <a href="{{ url('/') }}" class="btn-link ghost">
            <i class="fas fa-home"></i> Beranda
        </a>
    </div>

    {{-- Stats --}}
    @php
        $mapelIcons = [
            'Matematika' => 'fa-calculator',
            'Fisika' => 'fa-atom',
            'Kimia' => 'fa-flask',
            'Biologi' => 'fa-dna',
            'Geografi' => 'fa-globe-americas',
            'Astronomi' => 'fa-star',
            'Informatika' => 'fa-laptop-code',
            'Ekonomi' => 'fa-chart-line',
            'Kebumian' => 'fa-mountain',
        ];
    @endphp

    <div class="stats-row">
        <div class="stat-item">
            <div class="stat-number">{{ count($pesertaAll) }}</div>
            <div class="stat-label">Total Peserta</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ count($grouped) }}</div>
            <div class="stat-label">Mata Pelajaran</div>
        </div>
        @php
            $lakiCount = $pesertaAll->filter(fn($s) => in_array($s->jk, ['Laki-laki', 'L']))->count();
            $perempuanCount = count($pesertaAll) - $lakiCount;
        @endphp
        <div class="stat-item">
            <div class="stat-number">{{ $lakiCount }}</div>
            <div class="stat-label">Laki-laki</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $perempuanCount }}</div>
            <div class="stat-label">Perempuan</div>
        </div>
    </div>

    {{-- Search --}}
    <div class="search-container">
        <i class="fas fa-search search-icon"></i>
        <input type="text" id="searchInput" placeholder="Cari nama peserta..." onkeyup="filterPeserta()">
    </div>

    {{-- Filter Tabs --}}
    <div class="filter-bar">
        <div class="filter-tab active" onclick="filterByMapel('all', this)">
            Semua <span class="tab-count">{{ count($pesertaAll) }}</span>
        </div>
        @foreach($grouped as $mapel => $list)
        <div class="filter-tab" onclick="filterByMapel('{{ strtolower($mapel) }}', this)">
            <i class="fas {{ $mapelIcons[$mapel] ?? 'fa-book' }}"></i>
            {{ $mapel }} <span class="tab-count">{{ count($list) }}</span>
        </div>
        @endforeach
    </div>

    {{-- Peserta Grid --}}
    @if(count($pesertaAll) == 0)
    <div class="empty-state">
        <i class="fas fa-users-slash"></i>
        <h3>Belum Ada Peserta</h3>
        <p>Belum ada siswa yang mendaftar OSN 2026.</p>
    </div>
    @else
    <div class="peserta-grid" id="pesertaGrid">
        @foreach($pesertaAll as $index => $siswa)
        @php
            $isLaki = in_array($siswa->jk, ['Laki-laki', 'L']);
            $mapelLower = strtolower($siswa->mapel_osn_2026);
        @endphp
        <div class="peserta-card" data-nama="{{ strtolower($siswa->nama) }}" data-mapel="{{ $mapelLower }}" style="animation-delay: {{ $index * 0.03 }}s">
            <div class="peserta-top">
                <div class="peserta-avatar {{ $isLaki ? 'laki' : 'perempuan' }}">
                    @if($siswa->foto_url)
                        <img src="{{ $siswa->foto_url }}" alt="{{ $siswa->nama }}">
                    @else
                        {{ $siswa->initials }}
                    @endif
                </div>
                <div>
                    <div class="peserta-nama">{{ $siswa->nama }}</div>
                    <div class="peserta-rombel">{{ $siswa->rombel_aktif }}</div>
                </div>
            </div>
            <div class="peserta-meta">
                <div class="meta-row"><i class="fas fa-fingerprint"></i> {{ $siswa->nisn }}</div>
                <div class="meta-row"><i class="fas fa-id-card"></i> {{ $siswa->nis }}</div>
                @if($siswa->ikut_osn_2025 == 'Ya')
                <div class="meta-row"><i class="fas fa-check-circle" style="color: #34d399;"></i> Pernah ikut OSN 2025</div>
                @endif
            </div>
            <div class="mapel-badge {{ $mapelLower }}">
                <i class="fas {{ $mapelIcons[$siswa->mapel_osn_2026] ?? 'fa-book' }}"></i>
                {{ $siswa->mapel_osn_2026 }}
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<script>
function filterPeserta() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('.peserta-card').forEach(card => {
        const nama = card.dataset.nama;
        card.style.display = nama.includes(q) ? '' : 'none';
    });
}

function filterByMapel(mapel, el) {
    // Update active tab
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');

    // Filter cards
    document.querySelectorAll('.peserta-card').forEach(card => {
        if (mapel === 'all' || card.dataset.mapel === mapel) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>
</body>
</html>
