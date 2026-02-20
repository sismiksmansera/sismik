@extends('layouts.app')

@section('title', 'Peserta ' . $ajang->nama_ajang . ' | SISMIK')

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-guru')

<div class="main-content peserta-ajang-page">

    {{-- Header --}}
    <div class="page-header-center">
        <div class="header-icon-large">
            <i class="fas fa-trophy"></i>
        </div>
        <h1>{{ $ajang->nama_ajang }}</h1>
        <p>Daftar Peserta Ajang Talenta</p>
        @if($ajang->jenis_ajang)
        <span class="header-badge">{{ $ajang->jenis_ajang }}</span>
        @endif
    </div>

    {{-- Action Buttons --}}
    <div class="action-buttons-center">
        <a href="{{ route('guru.koordinator-osn.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon primary"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <h3>{{ count($pesertaList) }}</h3>
                <p>Total Peserta</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success"><i class="fas fa-calendar-alt"></i></div>
            <div class="stat-info">
                <h3>{{ $ajang->tahun ?? '-' }}</h3>
                <p>Tahun</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon warning"><i class="fas fa-user-tie"></i></div>
            <div class="stat-info">
                <h3 style="font-size: 14px;">{{ $ajang->pembina ?? '-' }}</h3>
                <p>Pembina</p>
            </div>
        </div>
    </div>

    {{-- Members List --}}
    <div class="members-container">
        <div class="members-header">
            <div class="members-title">
                <i class="fas fa-users" style="color: #3b82f6;"></i>
                <h2>Daftar Peserta</h2>
            </div>
            <span class="members-count">{{ count($pesertaList) }} Peserta</span>
        </div>

        @if(count($pesertaList) == 0)
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-users"></i></div>
            <h3>Belum Ada Peserta</h3>
            <p>Belum ada siswa yang terdaftar sebagai peserta ajang ini.</p>
        </div>
        @else
        <div class="members-cards-grid">
            @foreach($pesertaList as $peserta)
            @php
                $cardId = 'peserta_' . $peserta->id;
                $gender_gradient = ($peserta->jk == 'Laki-laki' || $peserta->jk == 'L')
                    ? 'linear-gradient(135deg, #3b82f6, #1d4ed8)'
                    : 'linear-gradient(135deg, #ec4899, #db2777)';
            @endphp
            <div class="member-card" data-card-id="{{ $cardId }}">
                <div class="member-card-header" onclick="toggleCard('{{ $cardId }}')">
                    <div class="member-header-left">
                        <div class="member-avatar" style="background: {{ $gender_gradient }};">
                            @if($peserta->foto_url)
                                <img src="{{ $peserta->foto_url }}" alt="{{ $peserta->nama }}">
                            @else
                                <span class="avatar-initial">{{ strtoupper(substr($peserta->nama, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="member-name-info">
                            <h4>{{ $peserta->nama }}</h4>
                            <span class="member-rombel">{{ $peserta->rombel_aktif }}</span>
                        </div>
                    </div>
                    <i class="fas fa-chevron-down expand-icon"></i>
                </div>

                <div class="member-card-body" id="{{ $cardId }}">
                    <div class="member-details-grid">
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-id-card"></i> NIS</span>
                            <span class="detail-value">{{ $peserta->nis }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-fingerprint"></i> NISN</span>
                            <span class="detail-value">{{ $peserta->nisn ?? '-' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-chalkboard"></i> Rombel</span>
                            <span class="detail-value">{{ $peserta->rombel_aktif }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-venus-mars"></i> JK</span>
                            <span class="detail-value">
                                @if($peserta->jk == 'Laki-laki' || $peserta->jk == 'L')
                                    <span class="badge-jk badge-laki">Laki-laki</span>
                                @else
                                    <span class="badge-jk badge-perempuan">Perempuan</span>
                                @endif
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fas fa-calendar-plus"></i> Terdaftar</span>
                            <span class="detail-value">{{ \Carbon\Carbon::parse($peserta->tanggal_bergabung)->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
</div>

<style>
.peserta-ajang-page { padding: 30px; background: #f8f9fa; min-height: calc(100vh - 60px); }

/* Header */
.page-header-center { text-align: center; margin-bottom: 25px; }
.header-icon-large {
    width: 80px; height: 80px; border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    font-size: 36px; color: white; margin: 0 auto 20px;
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    box-shadow: 0 8px 25px rgba(124,58,237,0.4);
}
.page-header-center h1 { font-size: 24px; font-weight: 700; margin: 0 0 5px; color: #1f2937; }
.page-header-center p { color: #6b7280; margin: 0 0 10px; }
.header-badge {
    display: inline-block; padding: 4px 14px; border-radius: 20px;
    background: #f5f3ff; color: #7c3aed; font-size: 12px; font-weight: 600;
    border: 1px solid #ddd6fe;
}

/* Buttons */
.action-buttons-center { display: flex; justify-content: center; gap: 15px; margin-bottom: 25px; flex-wrap: wrap; }
.btn-back {
    display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px;
    background: white; color: #374151; border: 2px solid #d1d5db;
    border-radius: 10px; text-decoration: none; font-weight: 600; transition: all 0.3s;
}
.btn-back:hover { border-color: #7c3aed; color: #7c3aed; }

/* Stats */
.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 25px; }
.stat-card {
    background: white; padding: 20px; border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08); display: flex;
    align-items: center; gap: 15px; border: 1px solid #e5e7eb;
}
.stat-icon {
    width: 50px; height: 50px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; color: white;
}
.stat-icon.primary { background: linear-gradient(135deg, #7c3aed, #6d28d9); }
.stat-icon.success { background: linear-gradient(135deg, #10b981, #059669); }
.stat-icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
.stat-info h3 { margin: 0; font-size: 20px; font-weight: 700; color: #1f2937; }
.stat-info p { margin: 4px 0 0; color: #6b7280; font-size: 12px; }

/* Members Container */
.members-container { background: white; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow: hidden; }
.members-header {
    padding: 20px 25px; border-bottom: 1px solid #e5e7eb;
    display: flex; justify-content: space-between; align-items: center;
}
.members-title { display: flex; align-items: center; gap: 10px; }
.members-title h2 { margin: 0; font-size: 1.1rem; color: #1f2937; }
.members-count { padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; background: rgba(124,58,237,0.1); color: #7c3aed; }

/* Empty */
.empty-state { padding: 60px 30px; text-align: center; }
.empty-icon { width: 80px; height: 80px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
.empty-icon i { font-size: 30px; color: #9ca3af; }
.empty-state h3 { margin: 0 0 10px; color: #1f2937; }
.empty-state p { margin: 0; color: #6b7280; }

/* Cards */
.members-cards-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 15px; padding: 20px;
}
.member-card {
    background: white; border-radius: 12px; border: 1px solid #e5e7eb;
    overflow: hidden; transition: all 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.member-card:hover { box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-color: #c4b5fd; }
.member-card.expanded { border-color: #7c3aed; }
.member-card-header {
    display: flex; justify-content: space-between; align-items: center;
    padding: 15px; cursor: pointer; transition: all 0.2s; background: rgba(124,58,237,0.04);
}
.member-header-left { display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0; }
.member-avatar {
    width: 45px; height: 45px; border-radius: 50%; overflow: hidden; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    border: 2px solid rgba(255,255,255,0.3); box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
.member-avatar img { width: 100%; height: 100%; object-fit: cover; }
.member-avatar .avatar-initial { color: white; font-weight: 700; font-size: 16px; }
.member-name-info { flex: 1; min-width: 0; }
.member-name-info h4 { margin: 0 0 3px; font-size: 15px; font-weight: 600; color: #1f2937; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.member-name-info .member-rombel { font-size: 12px; color: #6b7280; display: block; }
.expand-icon { transition: transform 0.3s; color: #7c3aed; }
.member-card.expanded .expand-icon { transform: rotate(180deg); }

/* Card Body */
.member-card-body {
    max-height: 0; overflow: hidden; opacity: 0;
    transition: max-height 0.4s ease, opacity 0.3s ease, padding 0.3s ease;
    padding: 0 15px; background: #fafafa;
}
.member-card.expanded .member-card-body { max-height: 500px; opacity: 1; padding: 15px; }
.member-details-grid { display: flex; flex-direction: column; gap: 12px; }
.detail-item { display: flex; align-items: center; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
.detail-label { font-size: 11px; color: #6b7280; font-weight: 600; text-transform: uppercase; display: flex; align-items: center; gap: 5px; }
.detail-label i { font-size: 10px; color: #7c3aed; }
.detail-value { font-size: 14px; color: #1f2937; font-weight: 500; }

.badge-jk { padding: 3px 8px; border-radius: 15px; font-size: 11px; font-weight: 600; }
.badge-laki { background: rgba(59,130,246,0.1); color: #3b82f6; }
.badge-perempuan { background: rgba(236,72,153,0.1); color: #ec4899; }

/* Responsive */
@media (max-width: 768px) {
    .peserta-ajang-page { padding: 15px; }
    .stats-grid { grid-template-columns: 1fr; }
    .members-cards-grid { grid-template-columns: 1fr; }
}
</style>

<script>
function toggleCard(cardId) {
    const card = document.querySelector(`[data-card-id="${cardId}"]`);
    if (card) card.classList.toggle('expanded');
}
</script>
@endsection
