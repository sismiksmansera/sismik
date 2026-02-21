@extends('layouts.app')

@section('title', 'Data Prestasi - ' . $sumberInfo['title'] . ' | SISMIK')

@section('content')
@php
    use App\Http\Controllers\Admin\PrestasiController;
@endphp

<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content prestasi-page">
        {{-- Header --}}
        <div class="page-header-center">
            <div class="header-icon-large">
                <i class="fas fa-trophy"></i>
            </div>
            <h1>Prestasi {{ $sumberInfo['title'] }}</h1>
            <p>Daftar Prestasi {{ $type == 'ekstra' ? 'Ekstrakurikuler' : ($type == 'ajang_talenta' ? 'Ajang Talenta' : 'Rombel') }} · {{ $sumberInfo['tahun_pelajaran'] }} - {{ $sumberInfo['semester'] }}</p>
        </div>

        {{-- Action Buttons --}}
        <div class="action-buttons-center">
            <a href="{{ $backUrl }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="{{ url('admin/prestasi/input?type=' . $type . '&id=' . $id . (isset($defaultKompetisi) && $defaultKompetisi ? '&default_kompetisi=' . urlencode($defaultKompetisi) : '') . (isset($defaultPenyelenggara) && $defaultPenyelenggara ? '&default_penyelenggara=' . urlencode($defaultPenyelenggara) : '')) }}" class="btn-add">
                <i class="fas fa-plus"></i> Tambah Prestasi
            </a>
        </div>

        {{-- Stats --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ count($prestasiList) }}</h3>
                    <p>Total Prestasi</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $sumberInfo['tahun_pelajaran'] }}</h3>
                    <p>Tahun Pelajaran</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $sumberInfo['semester'] }}</h3>
                    <p>Semester</p>
                </div>
            </div>
        </div>

        {{-- Prestasi List --}}
        <div class="prestasi-container">
            <div class="prestasi-header">
                <div class="prestasi-title">
                    <i class="fas fa-trophy"></i>
                    <h2>Daftar Prestasi</h2>
                </div>
                <span class="prestasi-count">
                    {{ count($prestasiList) }} Prestasi
                </span>
            </div>

            @if(count($prestasiList) == 0)
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <h3>Belum Ada Prestasi</h3>
                <p>Belum ada prestasi yang tercatat untuk periode ini.</p>
            </div>
            @else
            <div class="prestasi-cards-grid">
                @foreach($prestasiList as $prestasi)
                @php
                    $jenjangColor = PrestasiController::getJenjangColor($prestasi['jenjang']);
                @endphp
                <div class="prestasi-card">
                    <div class="prestasi-card-content">
                        <div class="medal-icon" style="background: {{ $jenjangColor }}20;">
                            <i class="fas fa-medal" style="color: {{ $jenjangColor }};"></i>
                        </div>
                        <div class="prestasi-info">
                            <h4>{{ $prestasi['nama_kompetisi'] }}</h4>
                            <div class="prestasi-badges">
                                <span class="badge-juara" style="background: {{ $jenjangColor }}20; color: {{ $jenjangColor }};">
                                    Juara {{ $prestasi['juara'] }}
                                </span>
                                <span class="badge-jenjang">
                                    {{ $prestasi['jenjang'] }}
                                </span>
                                @if(!empty($prestasi['tipe_peserta']))
                                <span class="badge-tipe {{ $prestasi['tipe_peserta'] == 'Tim' ? 'badge-tim' : 'badge-individu' }}">
                                    <i class="fas {{ $prestasi['tipe_peserta'] == 'Tim' ? 'fa-users' : 'fa-user' }}"></i>
                                    {{ $prestasi['tipe_peserta'] }}
                                </span>
                                @endif
                            </div>
                            <p class="prestasi-date">
                                <i class="fas fa-calendar"></i>
                                {{ date('d M Y', strtotime($prestasi['tanggal_pelaksanaan'])) }}
                            </p>
                            <p class="prestasi-peserta">
                                <i class="fas fa-users"></i>
                                @for($i = 0; $i < count($prestasi['siswa_array']); $i++)
                                    {{ $prestasi['siswa_array'][$i] }}{{ $i < count($prestasi['siswa_array']) - 1 ? ', ' : '' }}
                                @endfor
                            </p>
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
.prestasi-page {
    padding: 30px;
    background: #f8f9fa;
    min-height: calc(100vh - 60px);
}

.page-header-center {
    text-align: center;
    margin-bottom: 25px;
}

.header-icon-large {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    color: white;
    margin: 0 auto 20px;
    box-shadow: 0 8px 25px rgba(245, 158, 11, 0.3);
}

.page-header-center h1 {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 5px 0;
    color: #1f2937;
    text-transform: uppercase;
}

.page-header-center p {
    color: #6b7280;
    margin: 0;
}

.action-buttons-center {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 25px;
    flex-wrap: wrap;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: white;
    color: #374151;
    border: 2px solid #d1d5db;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-back:hover {
    border-color: #f59e0b;
    color: #f59e0b;
}

.btn-add {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35);
    transition: all 0.3s ease;
}

.btn-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    color: white;
}

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

.stat-icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
.stat-icon.success { background: linear-gradient(135deg, #10b981, #059669); }
.stat-icon.primary { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }

.stat-info h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: #1f2937;
}

.stat-info p {
    margin: 4px 0 0 0;
    color: #6b7280;
    font-size: 12px;
}

.prestasi-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    overflow: hidden;
}

.prestasi-header {
    padding: 20px 25px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.prestasi-title {
    display: flex;
    align-items: center;
    gap: 10px;
}

.prestasi-title i {
    color: #f59e0b;
}

.prestasi-title h2 {
    margin: 0;
    font-size: 1.1rem;
    color: #1f2937;
}

.prestasi-count {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

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

.empty-icon i {
    font-size: 30px;
    color: #9ca3af;
}

.empty-state h3 {
    margin: 0 0 10px 0;
    color: #1f2937;
}

.empty-state p {
    margin: 0;
    color: #6b7280;
}

.prestasi-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 15px;
    padding: 20px;
}

.prestasi-card {
    background: #f8fafc;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
}

.prestasi-card:hover {
    border-color: #f59e0b;
}

.prestasi-card-content {
    display: flex;
    align-items: flex-start;
    gap: 15px;
}

.medal-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.medal-icon i {
    font-size: 24px;
}

.prestasi-info {
    flex: 1;
    min-width: 0;
}

.prestasi-info h4 {
    margin: 0 0 8px 0;
    font-size: 15px;
    font-weight: 600;
    color: #1f2937;
}

.prestasi-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 8px;
}

.badge-juara {
    padding: 3px 10px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 600;
}

.badge-jenjang {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    padding: 3px 10px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 500;
}

.badge-tipe {
    padding: 3px 10px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 600;
}

.badge-tim { background: #dcfce7; color: #166534; }
.badge-individu { background: #dbeafe; color: #1e40af; }

.prestasi-date {
    margin: 0 0 5px 0;
    font-size: 12px;
    color: #6b7280;
}

.prestasi-date i {
    margin-right: 5px;
}

.prestasi-peserta {
    margin: 0;
    font-size: 12px;
    color: #374151;
}

.prestasi-peserta i {
    margin-right: 5px;
    color: #9ca3af;
}

@media (max-width: 768px) {
    .prestasi-page { padding: 20px; }
    .stats-grid { grid-template-columns: 1fr; }
    .prestasi-cards-grid { grid-template-columns: 1fr; }
}
</style>
@endsection
