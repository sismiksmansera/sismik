@extends('layouts.app')

@section('title', 'Data Prestasi - ' . $sumberInfo['title'] . ' | SISMIK')

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border-radius: 16px;
        padding: 25px 30px;
        margin-bottom: 25px;
        color: white;
        box-shadow: 0 10px 40px rgba(245, 158, 11, 0.3);
    }
    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    .header-left {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .back-btn {
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: white;
        transition: all 0.2s ease;
    }
    .back-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.05);
        color: white;
    }
    .header-info h1 {
        margin: 0 0 8px 0;
        font-size: 1.5rem;
        font-weight: 700;
    }
    .header-info p {
        margin: 0;
        opacity: 0.9;
        font-size: 0.9rem;
    }
    .header-stats {
        display: flex;
        gap: 10px;
    }
    .stat-box {
        background: rgba(255, 255, 255, 0.15);
        padding: 12px 20px;
        border-radius: 12px;
        text-align: center;
    }
    .stat-box .value {
        font-size: 1.5rem;
        font-weight: 700;
    }
    .stat-box .label {
        font-size: 0.8rem;
        opacity: 0.9;
    }
    
    .stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 25px;
    }
    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
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
    }
    
    .prestasi-grid {
        display: grid;
        gap: 20px;
    }
    .prestasi-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        border-left: 5px solid;
        transition: all 0.3s ease;
    }
    .prestasi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }
    .card-header {
        padding: 20px;
        border-bottom: 1px solid #f3f4f6;
    }
    .card-header-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 15px;
        flex-wrap: wrap;
    }
    .card-title {
        flex: 1;
    }
    .title-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
    }
    .medal-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .title-row h3 {
        margin: 0 0 4px 0;
        font-size: 1.1rem;
        color: #1f2937;
    }
    .title-row p {
        margin: 0;
        font-size: 0.85rem;
        color: #6b7280;
    }
    .card-badges {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
    }
    .badge-prestasi {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .badge-juara {
        background: #fef3c7;
        color: #92400e;
    }
    .card-meta {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        font-size: 0.85rem;
        color: #6b7280;
        margin-top: 10px;
    }
    .card-meta span {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .card-meta i {
        color: #9ca3af;
    }
    .card-body {
        padding: 15px 20px;
        background: #f8fafc;
    }
    .peserta-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .peserta-label {
        font-size: 0.8rem;
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .peserta-type {
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    .peserta-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .peserta-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }
    .peserta-chip:hover {
        background: #f0f9ff;
        border-color: #3b82f6;
    }
    .peserta-chip .nis {
        color: #9ca3af;
        font-size: 0.75rem;
    }
    
    .empty-state {
        background: white;
        border-radius: 16px;
        padding: 60px 30px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }
    .empty-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 32px;
        color: white;
    }
    .empty-state h3 {
        margin: 0 0 10px 0;
        color: #1f2937;
        font-size: 1.1rem;
    }
    .empty-state p {
        margin: 0;
        color: #6b7280;
        max-width: 400px;
        margin: 0 auto;
    }
    
    @media (max-width: 768px) {
        .stats-row { grid-template-columns: 1fr; }
        .header-content { flex-direction: column; align-items: flex-start; }
    }
</style>
@endpush

@php
    use App\Http\Controllers\Admin\PrestasiController;
@endphp

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <!-- Header -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <a href="{{ $backUrl }}" class="back-btn">
                        <i class="fas fa-arrow-left" style="font-size: 18px;"></i>
                    </a>
                    <div class="header-info">
                        <h1><i class="fas fa-trophy"></i> Data Prestasi</h1>
                        <p>
                            {{ $type == 'ekstra' ? 'Ekstrakurikuler' : 'Rombel' }}: <strong>{{ $sumberInfo['title'] }}</strong>
                            · {{ $sumberInfo['tahun_pelajaran'] }} - {{ $sumberInfo['semester'] }}
                        </p>
                    </div>
                </div>
                <div class="header-stats">
                    <a href="{{ url('admin/prestasi/input?type=' . $type . '&id=' . $id) }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; background: rgba(255,255,255,0.2); color: white; border-radius: 12px; text-decoration: none; font-weight: 600; transition: all 0.2s;">
                        <i class="fas fa-plus"></i> Tambah Prestasi
                    </a>
                    <div class="stat-box">
                        <div class="value">{{ count($prestasiList) }}</div>
                        <div class="label">Total Prestasi</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white;">
                    <i class="fas fa-trophy"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 24px; font-weight: 700; color: #1f2937;">
                        {{ count($prestasiList) }}
                    </h3>
                    <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 12px;">Total Prestasi</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669); color: white;">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #1f2937;">
                        {{ $sumberInfo['tahun_pelajaran'] }}
                    </h3>
                    <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 12px;">Tahun Pelajaran</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white;">
                    <i class="fas fa-book"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #1f2937;">
                        {{ $sumberInfo['semester'] }}
                    </h3>
                    <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 12px;">Semester</p>
                </div>
            </div>
        </div>

        <!-- Prestasi List -->
        @if(count($prestasiList) > 0)
            <div class="prestasi-grid">
                @foreach($prestasiList as $prestasi)
                    @php
                        $jenjangColor = PrestasiController::getJenjangColor($prestasi['jenjang']);
                    @endphp
                    <div class="prestasi-card" style="border-left-color: {{ $jenjangColor }};">
                        <!-- Card Header -->
                        <div class="card-header">
                            <div class="card-header-top">
                                <div class="card-title">
                                    <div class="title-row">
                                        <div class="medal-icon" style="background: {{ $jenjangColor }}15;">
                                            <i class="fas fa-medal" style="font-size: 22px; color: {{ $jenjangColor }};"></i>
                                        </div>
                                        <div>
                                            <h3>{{ $prestasi['nama_kompetisi'] }}</h3>
                                            <p><i class="fas fa-building"></i> {{ $prestasi['penyelenggara'] ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-badges">
                                    <span class="badge-prestasi" style="background: {{ $jenjangColor }}; color: white;">
                                        {{ $prestasi['jenjang'] }}
                                    </span>
                                    <span class="badge-prestasi badge-juara">
                                        <i class="fas fa-trophy"></i> Juara {{ $prestasi['juara'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="card-meta">
                                <span><i class="fas fa-calendar"></i> {{ date('d M Y', strtotime($prestasi['tanggal_pelaksanaan'])) }}</span>
                                <span><i class="fas fa-users"></i> {{ $prestasi['jumlah_siswa'] }} peserta</span>
                            </div>
                        </div>

                        <!-- Card Body - Siswa List -->
                        <div class="card-body">
                            <div class="peserta-header">
                                <div class="peserta-label">
                                    <i class="fas fa-user-graduate"></i> Peserta yang Berprestasi
                                    @if(!empty($prestasi['tipe_peserta']))
                                        <span class="peserta-type" 
                                            style="background: {{ $prestasi['tipe_peserta'] == 'Tim' ? '#dcfce7' : '#dbeafe' }}; color: {{ $prestasi['tipe_peserta'] == 'Tim' ? '#166534' : '#1e40af' }};">
                                            <i class="fas {{ $prestasi['tipe_peserta'] == 'Tim' ? 'fa-users' : 'fa-user' }}"></i>
                                            {{ $prestasi['tipe_peserta'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="peserta-list">
                                @for($i = 0; $i < count($prestasi['siswa_array']); $i++)
                                    <span class="peserta-chip">
                                        <i class="fas fa-user" style="color: {{ $jenjangColor }};"></i>
                                        {{ $prestasi['siswa_array'][$i] }}
                                        <span class="nis">({{ $prestasi['nis_array'][$i] ?? '' }})</span>
                                    </span>
                                @endfor
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <h3>Belum Ada Prestasi</h3>
                <p>Belum ada data prestasi yang tercatat untuk {{ $type == 'ekstra' ? 'ekstrakurikuler' : 'kelas' }} ini pada periode aktif.</p>
            </div>
        @endif
    </div>
</div>
@endsection
