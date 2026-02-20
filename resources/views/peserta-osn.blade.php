<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            cursor: pointer; transition: all 0.2s;
        }
        .peserta-avatar:hover { transform: scale(1.1); box-shadow: 0 4px 15px rgba(59,130,246,0.4); }
        .peserta-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .peserta-avatar.laki { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .peserta-avatar.perempuan { background: linear-gradient(135deg, #ec4899, #db2777); }

        /* EXPANDABLE CARD */
        .peserta-card-header { cursor: pointer; }
        .expand-toggle {
            display: flex; align-items: center; justify-content: center;
            margin-top: 10px; padding: 6px;
            background: rgba(255,255,255,0.04); border-radius: 8px;
            cursor: pointer; transition: all 0.2s;
            color: #64748b; font-size: 11px; gap: 5px;
        }
        .expand-toggle:hover { background: rgba(255,255,255,0.08); color: #94a3b8; }
        .expand-toggle i { transition: transform 0.3s; font-size: 10px; }
        .peserta-card.expanded .expand-toggle i { transform: rotate(180deg); }
        .peserta-card.expanded .expand-toggle .toggle-text { }

        .peserta-detail-body {
            max-height: 0; overflow: hidden; opacity: 0;
            transition: max-height 0.4s ease, opacity 0.3s ease, margin 0.3s ease;
            margin-top: 0;
        }
        .peserta-card.expanded .peserta-detail-body {
            max-height: 2000px; opacity: 1; margin-top: 12px;
        }

        .detail-section-title {
            font-size: 10px; font-weight: 700; color: #60a5fa; text-transform: uppercase;
            margin: 10px 0 6px; display: flex; align-items: center; gap: 5px;
            padding-bottom: 4px; border-bottom: 1px solid rgba(96,165,250,0.2);
        }
        .detail-section-title:first-child { margin-top: 0; }
        .detail-section-title i { font-size: 9px; }

        .detail-grid {
            display: flex; flex-direction: column; gap: 2px; margin-bottom: 6px;
        }
        .detail-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 5px 0; border-bottom: 1px solid rgba(255,255,255,0.04);
        }
        .detail-row-label {
            font-size: 10px; color: #64748b; font-weight: 600; text-transform: uppercase;
            display: flex; align-items: center; gap: 4px;
        }
        .detail-row-label i { font-size: 9px; color: #3b82f6; }
        .detail-row-value { font-size: 12px; color: #e2e8f0; font-weight: 500; text-align: right; max-width: 55%; }

        .badge-jk-dark { padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 600; }
        .badge-laki-dark { background: rgba(59,130,246,0.2); color: #60a5fa; }
        .badge-perempuan-dark { background: rgba(236,72,153,0.2); color: #f472b6; }

        .btn-download-berkas-dark {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 6px 14px; border-radius: 8px;
            font-size: 11px; font-weight: 600;
            background: rgba(59,130,246,0.1); color: #60a5fa;
            border: 1px solid rgba(59,130,246,0.2);
            cursor: pointer; transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }
        .btn-download-berkas-dark:hover { background: rgba(59,130,246,0.2); transform: translateY(-1px); }

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

        /* CARD FOOTER */
        .peserta-card-footer {
            display: flex; justify-content: space-between; align-items: center;
            margin-top: 12px; padding-top: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }
        .btn-hapus {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 6px 14px; border-radius: 8px;
            font-size: 11px; font-weight: 600;
            background: rgba(239, 68, 68, 0.1); color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.2);
            cursor: pointer; transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }
        .btn-hapus:hover { background: rgba(239, 68, 68, 0.2); transform: translateY(-1px); }

        .btn-buka-akses {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 6px 14px; border-radius: 8px;
            font-size: 11px; font-weight: 600;
            background: rgba(16, 185, 129, 0.1); color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.2);
            cursor: pointer; transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }
        .btn-buka-akses:hover { background: rgba(16, 185, 129, 0.2); transform: translateY(-1px); }

        .peserta-card-actions {
            display: flex; gap: 6px;
        }

        /* EMPTY */
        .empty-state {
            text-align: center; padding: 60px 20px;
            color: #475569;
        }
        .empty-state i { font-size: 50px; margin-bottom: 15px; display: block; }
        .empty-state h3 { font-size: 18px; color: #64748b; margin-bottom: 8px; }
        .empty-state p { font-size: 13px; }

        /* Photo Modal (dark) */
        .photo-modal-dark {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.8); backdrop-filter: blur(10px);
            z-index: 20000; align-items: center; justify-content: center; padding: 20px;
        }
        .photo-modal-dark.active { display: flex; }
        .photo-modal-dark-content {
            background: #1e293b; border: 1px solid rgba(255,255,255,0.1);
            border-radius: 18px; width: 100%; max-width: 400px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            animation: fadeInUp 0.3s ease;
            padding: 24px; position: relative; text-align: center;
        }
        .photo-modal-dark-close {
            position: absolute; top: 12px; right: 12px; width: 32px; height: 32px;
            border-radius: 50%; background: rgba(255,255,255,0.1); border: none; color: #94a3b8;
            cursor: pointer; font-size: 18px; display: flex; align-items: center; justify-content: center;
            transition: all 0.2s;
        }
        .photo-modal-dark-close:hover { background: rgba(239,68,68,0.2); color: #f87171; }
        .photo-modal-dark-img {
            width: 200px; height: 200px; border-radius: 16px; object-fit: cover;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3); margin: 0 auto 15px;
        }
        .photo-modal-dark-placeholder {
            width: 200px; height: 200px; border-radius: 16px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 64px; font-weight: 800; margin: 0 auto 15px;
        }
        .photo-modal-dark-name { font-size: 16px; color: #f1f5f9; margin-bottom: 15px; font-weight: 600; }
        .photo-modal-dark-actions { display: flex; gap: 10px; justify-content: center; }
        .btn-photo-dl {
            padding: 10px 20px; border-radius: 10px; border: none; cursor: pointer;
            font-size: 13px; font-weight: 600; font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white;
            display: flex; align-items: center; gap: 6px; transition: all 0.2s;
        }
        .btn-photo-dl:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(59,130,246,0.4); }
        .btn-photo-cl {
            padding: 10px 20px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.15);
            background: transparent; color: #94a3b8; cursor: pointer;
            font-size: 13px; font-weight: 600; font-family: 'Inter', sans-serif;
            display: flex; align-items: center; gap: 6px; justify-content: center; transition: all 0.2s;
        }
        .btn-photo-cl:hover { background: rgba(255,255,255,0.05); color: #e2e8f0; }

        /* Berkas Modal (dark) */
        .berkas-modal-dark-content { max-width: 440px; text-align: left; }
        .berkas-header-dark { text-align: center; margin-bottom: 20px; }
        .berkas-icon-dark {
            width: 56px; height: 56px; border-radius: 14px; margin: 0 auto 12px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 22px;
        }
        .berkas-header-dark h3 { font-size: 17px; color: #f1f5f9; margin: 0 0 4px; }
        .berkas-header-dark p { font-size: 12px; color: #64748b; margin: 0; }
        .berkas-list-dark { display: flex; flex-direction: column; gap: 10px; }
        .berkas-item-dark {
            display: flex; align-items: center; gap: 14px; padding: 14px;
            border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; cursor: pointer;
            transition: all 0.2s; background: rgba(255,255,255,0.04);
        }
        .berkas-item-dark:hover { border-color: rgba(59,130,246,0.3); background: rgba(59,130,246,0.08); transform: translateY(-1px); }
        .berkas-item-dark-icon {
            width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 16px; flex-shrink: 0;
        }
        .berkas-item-dark-info { flex: 1; }
        .berkas-item-dark-info h4 { font-size: 13px; color: #e2e8f0; margin: 0 0 2px; }
        .berkas-item-dark-info p { font-size: 10px; color: #64748b; margin: 0; }
        .berkas-dl-icon-dark { color: #60a5fa; font-size: 14px; }

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
            <i class="fas fa-school"></i> SMAN 1 Seputih Raman
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
                <div class="peserta-avatar {{ $isLaki ? 'laki' : 'perempuan' }}" onclick="openPhotoModal('{{ $siswa->foto_url ?? '' }}', '{{ $siswa->initials }}', '{{ addslashes($siswa->nama) }}', '{{ $siswa->mapel_osn_2026 }}')">
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

            {{-- Expand toggle --}}
            <div class="expand-toggle" onclick="toggleCardDetail(this)">
                <span class="toggle-text">Detail</span>
                <i class="fas fa-chevron-down"></i>
            </div>

            {{-- Expandable detail body --}}
            <div class="peserta-detail-body">
                <div class="detail-section-title"><i class="fas fa-id-badge"></i> Identitas</div>
                <div class="detail-grid">
                    <div class="detail-row">
                        <span class="detail-row-label"><i class="fas fa-venus-mars"></i> JK</span>
                        <span class="detail-row-value">
                            @if($isLaki)
                                <span class="badge-jk-dark badge-laki-dark">Laki-laki</span>
                            @else
                                <span class="badge-jk-dark badge-perempuan-dark">Perempuan</span>
                            @endif
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-row-label"><i class="fas fa-pray"></i> Agama</span>
                        <span class="detail-row-value">{{ $siswa->agama ?? '-' }}</span>
                    </div>
                </div>

                <div class="detail-section-title"><i class="fas fa-map-marker-alt"></i> Tempat & Tanggal Lahir</div>
                <div class="detail-grid">
                    <div class="detail-row">
                        <span class="detail-row-label"><i class="fas fa-city"></i> Tempat Lahir</span>
                        <span class="detail-row-value">{{ $siswa->tempat_lahir ?? '-' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-row-label"><i class="fas fa-calendar-alt"></i> Tgl Lahir</span>
                        <span class="detail-row-value">{{ $siswa->tgl_lahir ? \Carbon\Carbon::parse($siswa->tgl_lahir)->format('d/m/Y') : '-' }}</span>
                    </div>
                </div>

                <div class="detail-section-title"><i class="fas fa-home"></i> Alamat</div>
                <div class="detail-grid">
                    <div class="detail-row">
                        <span class="detail-row-label"><i class="fas fa-globe-asia"></i> Provinsi</span>
                        <span class="detail-row-value">{{ $siswa->provinsi ?? '-' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-row-label"><i class="fas fa-building"></i> Kota/Kab</span>
                        <span class="detail-row-value">{{ $siswa->kota ?? '-' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-row-label"><i class="fas fa-map"></i> Kecamatan</span>
                        <span class="detail-row-value">{{ $siswa->kecamatan ?? '-' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-row-label"><i class="fas fa-map-pin"></i> Kampung</span>
                        <span class="detail-row-value">{{ $siswa->kelurahan ?? '-' }}</span>
                    </div>
                    @if($siswa->dusun)
                    <div class="detail-row">
                        <span class="detail-row-label"><i class="fas fa-tree"></i> Dusun</span>
                        <span class="detail-row-value">{{ $siswa->dusun }}</span>
                    </div>
                    @endif
                    @if($siswa->rt_rw)
                    <div class="detail-row">
                        <span class="detail-row-label"><i class="fas fa-hashtag"></i> RT/RW</span>
                        <span class="detail-row-value">{{ $siswa->rt_rw }}</span>
                    </div>
                    @endif
                </div>

                <div class="detail-section-title"><i class="fas fa-address-book"></i> Kontak</div>
                <div class="detail-grid">
                    <div class="detail-row">
                        <span class="detail-row-label"><i class="fas fa-envelope"></i> Email</span>
                        <span class="detail-row-value">{{ $siswa->email ?? '-' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-row-label"><i class="fas fa-phone"></i> No HP</span>
                        <span class="detail-row-value">{{ $siswa->nohp_siswa ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="peserta-card-footer">
                <div class="mapel-badge {{ $mapelLower }}">
                    <i class="fas {{ $mapelIcons[$siswa->mapel_osn_2026] ?? 'fa-book' }}"></i>
                    {{ $siswa->mapel_osn_2026 }}
                </div>
                <div class="peserta-card-actions">
                    <button class="btn-download-berkas-dark" onclick="openBerkasModal('{{ addslashes($siswa->nama) }}', '{{ $siswa->mapel_osn_2026 }}')">
                        <i class="fas fa-file-download"></i> Berkas
                    </button>
                    <button class="btn-buka-akses" onclick="bukaAkses({{ $siswa->id }}, '{{ addslashes($siswa->nama) }}')">
                        <i class="fas fa-unlock-alt"></i> Buka Akses
                    </button>
                    <button class="btn-hapus" onclick="hapusPeserta({{ $siswa->id }}, '{{ addslashes($siswa->nama) }}')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- PHOTO MODAL --}}
<div id="photoModal" class="photo-modal-dark" onclick="if(event.target===this)closePhotoModal()">
    <div class="photo-modal-dark-content">
        <button class="photo-modal-dark-close" onclick="closePhotoModal()">&times;</button>
        <div id="photoModalBody">
            <img id="photoModalImg" src="" alt="Foto" class="photo-modal-dark-img">
            <div id="photoModalPlaceholder" class="photo-modal-dark-placeholder"></div>
        </div>
        <div class="photo-modal-dark-name" id="photoModalName"></div>
        <div class="photo-modal-dark-actions">
            <button class="btn-photo-dl" id="btnPhotoDownload" onclick="downloadPhoto()">
                <i class="fas fa-download"></i> Download Foto
            </button>
            <button class="btn-photo-cl" onclick="closePhotoModal()">
                <i class="fas fa-times"></i> Tutup
            </button>
        </div>
    </div>
</div>

{{-- BERKAS MODAL --}}
<div id="berkasModal" class="photo-modal-dark" onclick="if(event.target===this)closeBerkasModal()">
    <div class="photo-modal-dark-content berkas-modal-dark-content">
        <button class="photo-modal-dark-close" onclick="closeBerkasModal()">&times;</button>
        <div class="berkas-header-dark">
            <div class="berkas-icon-dark"><i class="fas fa-file-alt"></i></div>
            <h3>Download Berkas</h3>
            <p id="berkasNama"></p>
        </div>
        <div class="berkas-list-dark">
            <div class="berkas-item-dark" onclick="alert('Format Pakta Integritas belum disusun.')">
                <div class="berkas-item-dark-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                    <i class="fas fa-file-signature"></i>
                </div>
                <div class="berkas-item-dark-info">
                    <h4>Pakta Integritas</h4>
                    <p>Surat pernyataan integritas peserta OSN</p>
                </div>
                <i class="fas fa-download berkas-dl-icon-dark"></i>
            </div>
            <div class="berkas-item-dark" onclick="alert('Format Surat Keterangan belum disusun.')">
                <div class="berkas-item-dark-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="fas fa-file-contract"></i>
                </div>
                <div class="berkas-item-dark-info">
                    <h4>Surat Keterangan Kepala Sekolah</h4>
                    <p>Surat keterangan dari kepala sekolah</p>
                </div>
                <i class="fas fa-download berkas-dl-icon-dark"></i>
            </div>
        </div>
        <button class="btn-photo-cl" onclick="closeBerkasModal()" style="width:100%;margin-top:15px;">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>
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
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    document.querySelectorAll('.peserta-card').forEach(card => {
        if (mapel === 'all' || card.dataset.mapel === mapel) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

function toggleCardDetail(el) {
    const card = el.closest('.peserta-card');
    card.classList.toggle('expanded');
    el.querySelector('.toggle-text').textContent = card.classList.contains('expanded') ? 'Sembunyikan' : 'Detail';
}

// Photo Modal
let currentPhotoUrl = '';
let currentPhotoFilename = '';

function openPhotoModal(fotoUrl, initials, nama, mapel) {
    const modal = document.getElementById('photoModal');
    const img = document.getElementById('photoModalImg');
    const placeholder = document.getElementById('photoModalPlaceholder');
    const nameEl = document.getElementById('photoModalName');
    const dlBtn = document.getElementById('btnPhotoDownload');

    nameEl.textContent = nama;
    currentPhotoFilename = mapel + ' - ' + nama;

    if (fotoUrl) {
        img.src = fotoUrl;
        img.style.display = 'block';
        placeholder.style.display = 'none';
        currentPhotoUrl = fotoUrl;
        dlBtn.style.display = 'flex';
    } else {
        img.style.display = 'none';
        placeholder.style.display = 'flex';
        placeholder.textContent = initials;
        currentPhotoUrl = '';
        dlBtn.style.display = 'none';
    }
    modal.classList.add('active');
}

function closePhotoModal() {
    document.getElementById('photoModal').classList.remove('active');
}

function downloadPhoto() {
    if (!currentPhotoUrl) return;
    const a = document.createElement('a');
    a.href = currentPhotoUrl;
    a.download = currentPhotoFilename + '.jpg';
    a.target = '_blank';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

// Berkas Modal
function openBerkasModal(nama, mapel) {
    document.getElementById('berkasNama').textContent = mapel + ' - ' + nama;
    document.getElementById('berkasModal').classList.add('active');
}

function closeBerkasModal() {
    document.getElementById('berkasModal').classList.remove('active');
}

function hapusPeserta(siswaId, nama) {
    if (!confirm(`Yakin ingin menghapus pendaftaran OSN untuk ${nama}?`)) return;

    fetch('{{ route("peserta-osn") }}/hapus', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ siswa_id: siswaId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'Gagal menghapus.');
        }
    })
    .catch(() => alert('Terjadi kesalahan.'));
}

function bukaAkses(siswaId, nama) {
    if (!confirm(`Buka akses edit data dan foto profil untuk ${nama}?\nSiswa akan dapat mengisi ulang formulir pendaftaran OSN.`)) return;

    fetch('{{ route("peserta-osn") }}/buka-akses', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ siswa_id: siswaId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'Gagal membuka akses.');
        }
    })
    .catch(() => alert('Terjadi kesalahan.'));
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closePhotoModal();
        closeBerkasModal();
    }
});
</script>
</body>
</html>
