@extends('layouts.app')
@php use App\Http\Controllers\Admin\PrestasiController; @endphp
@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
    <div class="prestasi-overview">
    {{-- Header --}}
    <div class="page-header">
        <div class="header-icon">
            <i class="fas fa-trophy"></i>
        </div>
        <h1>Prestasi Sekolah</h1>
        <p>Data prestasi seluruh siswa dari semua sumber</p>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-row">
        <div class="stat-card stat-total">
            <div class="stat-icon-wrap"><i class="fas fa-trophy"></i></div>
            <div class="stat-data">
                <h3>{{ $totalPrestasi }}</h3>
                <p>Total Prestasi</p>
            </div>
        </div>
        <div class="stat-card stat-siswa">
            <div class="stat-icon-wrap"><i class="fas fa-users"></i></div>
            <div class="stat-data">
                <h3>{{ $totalSiswa }}</h3>
                <p>Siswa Berprestasi</p>
            </div>
        </div>
        <div class="stat-card stat-tahun">
            <div class="stat-icon-wrap"><i class="fas fa-calendar"></i></div>
            <div class="stat-data">
                <h3>{{ $tahunList->count() }}</h3>
                <p>Tahun Pelajaran</p>
            </div>
        </div>
    </div>

    {{-- Jenjang Badge Bar --}}
    <div class="jenjang-bar">
        <a href="{{ route('admin.prestasi.index') }}" class="jenjang-chip {{ empty($filterJenjang) ? 'active' : '' }}">
            <i class="fas fa-layer-group"></i> Semua
        </a>
        @foreach($jenjangStats as $js)
        @php $jColor = PrestasiController::getJenjangColor($js->jenjang); @endphp
        <a href="{{ route('admin.prestasi.index', ['jenjang' => $js->jenjang, 'tahun' => $filterTahun]) }}"
           class="jenjang-chip {{ $filterJenjang == $js->jenjang ? 'active' : '' }}"
           style="{{ $filterJenjang == $js->jenjang ? 'background:'.$jColor.'; color:white;' : 'border-color:'.$jColor.'; color:'.$jColor }}">
            {{ $js->jenjang }} <span class="chip-count">{{ $js->total }}</span>
        </a>
        @endforeach
    </div>

    {{-- Filter --}}
    <div class="filter-bar">
        <div class="filter-left">
            <i class="fas fa-filter"></i>
            <select id="filterTahun" onchange="applyFilter()">
                <option value="">Semua Tahun</option>
                @foreach($tahunList as $t)
                <option value="{{ $t }}" {{ $filterTahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-right">
            <span class="result-count">{{ $totalPrestasi }} prestasi ditemukan</span>
            <a href="{{ route('admin.prestasi.export-excel', ['tahun' => $filterTahun, 'jenjang' => $filterJenjang]) }}" class="btn-export">
                <i class="fas fa-file-excel"></i> Cetak Excel
            </a>
        </div>
    </div>

    {{-- Prestasi Grouped by Year --}}
    @if($groupedByTahun->isEmpty())
    <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-trophy"></i></div>
        <h3>Belum Ada Prestasi</h3>
        <p>Belum ada data prestasi yang tercatat.</p>
    </div>
    @else
    @foreach($groupedByTahun as $tahun => $prestasiItems)
    <div class="year-section">
        <div class="year-header" onclick="toggleYear(this)">
            <div class="year-title">
                <i class="fas fa-calendar-alt"></i>
                <h2>{{ $tahun }}</h2>
                <span class="year-count">{{ $prestasiItems->count() }} Prestasi</span>
            </div>
            <i class="fas fa-chevron-down year-toggle"></i>
        </div>

        {{-- Group by jenjang within year --}}
        @php
            $byJenjang = $prestasiItems->groupBy('jenjang');
            $jenjangOrder = ['Internasional','Nasional','Provinsi','Kabupaten','Kecamatan','Sekolah','Kelas'];
            $sortedJenjang = collect($jenjangOrder)->filter(fn($j) => $byJenjang->has($j));
        @endphp

        <div class="year-content">
            @foreach($sortedJenjang as $jenjang)
            @php $jColor = PrestasiController::getJenjangColor($jenjang); @endphp
            <div class="jenjang-section">
                <div class="jenjang-label" style="border-left: 4px solid {{ $jColor }};">
                    <span class="jenjang-badge" style="background: {{ $jColor }}20; color: {{ $jColor }};">
                        {{ $jenjang }}
                    </span>
                    <span class="jenjang-count">{{ $byJenjang[$jenjang]->count() }} prestasi</span>
                </div>
                <div class="prestasi-grid">
                    @foreach($byJenjang[$jenjang] as $prestasi)
                    <div class="p-card">
                        <div class="p-card-left" style="background: {{ $jColor }}15;">
                            <i class="fas fa-medal" style="color: {{ $jColor }};"></i>
                        </div>
                        <div class="p-card-body">
                            <h4>{{ $prestasi->nama_kompetisi }}</h4>
                            <div class="p-badges">
                                <span class="p-badge" style="background: {{ $jColor }}20; color: {{ $jColor }};">
                                    {{ $prestasi->juara }}
                                </span>
                                @if($prestasi->tipe_peserta)
                                <span class="p-badge {{ $prestasi->tipe_peserta == 'Tim' ? 'badge-tim' : 'badge-individu' }}">
                                    <i class="fas {{ $prestasi->tipe_peserta == 'Tim' ? 'fa-users' : 'fa-user' }}"></i>
                                    {{ $prestasi->tipe_peserta == 'Single' ? 'Individu' : $prestasi->tipe_peserta }}
                                </span>
                                @endif
                                <span class="p-badge badge-sumber">
                                    {{ $prestasi->sumber_prestasi == 'ekstrakurikuler' ? 'Ekskul' : ($prestasi->sumber_prestasi == 'ajang_talenta' ? 'Ajang Talenta' : 'Rombel') }}
                                </span>
                            </div>
                            <div class="p-meta">
                                @if($prestasi->penyelenggara)
                                <span><i class="fas fa-building"></i> {{ $prestasi->penyelenggara }}</span>
                                @endif
                                <span><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($prestasi->tanggal_pelaksanaan)->format('d M Y') }}</span>
                            </div>
                            <div class="p-siswa">
                                <i class="fas fa-user-graduate"></i>
                                @for($i = 0; $i < min(count($prestasi->siswa_array), 5); $i++)
                                    {{ $prestasi->siswa_array[$i] }}{{ $i < min(count($prestasi->siswa_array), 5) - 1 ? ', ' : '' }}
                                @endfor
                                @if(count($prestasi->siswa_array) > 5)
                                    <span class="more-siswa">+{{ count($prestasi->siswa_array) - 5 }} lainnya</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
    @endif
</div>
    </div>
</div>

<style>
.prestasi-overview {
    padding: 30px;
    background: #f0f2f5;
    min-height: calc(100vh - 60px);
}

/* Header */
.page-header {
    text-align: center;
    margin-bottom: 30px;
}

.header-icon {
    width: 80px; height: 80px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    font-size: 36px; color: white;
    margin: 0 auto 15px;
    box-shadow: 0 8px 25px rgba(245,158,11,0.3);
}

.page-header h1 {
    font-size: 28px; font-weight: 700;
    margin: 0 0 5px; color: #1f2937;
}

.page-header p {
    color: #6b7280; margin: 0; font-size: 15px;
}

/* Stats Row */
.stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 20px;
}

.stat-card {
    background: white;
    border-radius: 14px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    border-left: 4px solid transparent;
}

.stat-total { border-left-color: #f59e0b; }
.stat-siswa { border-left-color: #3b82f6; }
.stat-tahun { border-left-color: #10b981; }

.stat-icon-wrap {
    width: 50px; height: 50px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
}

.stat-total .stat-icon-wrap { background: #fef3c7; color: #f59e0b; }
.stat-siswa .stat-icon-wrap { background: #dbeafe; color: #3b82f6; }
.stat-tahun .stat-icon-wrap { background: #d1fae5; color: #10b981; }

.stat-data h3 { margin: 0; font-size: 24px; font-weight: 700; color: #1f2937; }
.stat-data p { margin: 0; font-size: 12px; color: #6b7280; }

/* Jenjang Bar */
.jenjang-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 15px;
    padding: 15px 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

.jenjang-chip {
    display: inline-flex;
    align-items: center; gap: 6px;
    padding: 7px 16px;
    border-radius: 20px;
    font-size: 13px; font-weight: 600;
    text-decoration: none;
    border: 2px solid #e5e7eb;
    color: #6b7280;
    transition: all 0.2s;
}

.jenjang-chip:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.jenjang-chip.active {
    background: #1f2937;
    color: white;
    border-color: #1f2937;
}

.chip-count {
    background: rgba(255,255,255,0.3);
    padding: 1px 7px;
    border-radius: 10px;
    font-size: 11px;
}

.jenjang-chip.active .chip-count {
    background: rgba(255,255,255,0.2);
}

/* Filter Bar */
.filter-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 12px 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

.filter-left {
    display: flex; align-items: center; gap: 10px;
}

.filter-left i { color: #9ca3af; }

.filter-left select {
    padding: 8px 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    color: #374151;
    background: white;
    cursor: pointer;
}

.result-count {
    font-size: 13px; color: #6b7280; font-weight: 500;
}

.filter-right {
    display: flex; align-items: center; gap: 12px;
}

.btn-export {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 18px;
    background: linear-gradient(135deg, #059669, #047857);
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.2s;
    box-shadow: 0 2px 8px rgba(5,150,105,0.3);
}

.btn-export:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(5,150,105,0.4);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 80px 30px;
    background: white;
    border-radius: 16px;
}

.empty-icon { font-size: 48px; color: #d1d5db; margin-bottom: 15px; }
.empty-state h3 { color: #6b7280; margin: 0 0 5px; }
.empty-state p { color: #9ca3af; margin: 0; }

/* Year Section */
.year-section {
    background: white;
    border-radius: 16px;
    margin-bottom: 20px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
}

.year-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 25px;
    cursor: pointer;
    transition: background 0.2s;
    border-bottom: 1px solid #f3f4f6;
}

.year-header:hover {
    background: #f9fafb;
}

.year-title {
    display: flex;
    align-items: center;
    gap: 12px;
}

.year-title i { color: #f59e0b; font-size: 20px; }
.year-title h2 { margin: 0; font-size: 20px; font-weight: 700; color: #1f2937; }

.year-count {
    background: #fef3c7;
    color: #92400e;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
}

.year-toggle {
    color: #9ca3af;
    transition: transform 0.3s;
}

.year-header.collapsed .year-toggle {
    transform: rotate(-90deg);
}

.year-content {
    padding: 0 25px 25px;
}

.year-content.hidden {
    display: none;
}

/* Jenjang Section */
.jenjang-section {
    margin-top: 20px;
}

.jenjang-label {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 15px;
    background: #fafafa;
    border-radius: 8px;
    margin-bottom: 12px;
}

.jenjang-badge {
    padding: 4px 14px;
    border-radius: 15px;
    font-size: 13px;
    font-weight: 600;
}

.jenjang-count {
    font-size: 12px;
    color: #9ca3af;
}

/* Prestasi Grid */
.prestasi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 12px;
}

.p-card {
    display: flex;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.2s;
}

.p-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
}

.p-card-left {
    width: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 18px;
}

.p-card-body {
    flex: 1;
    padding: 14px 16px;
    min-width: 0;
}

.p-card-body h4 {
    margin: 0 0 8px;
    font-size: 14px;
    font-weight: 600;
    color: #1f2937;
}

.p-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 8px;
}

.p-badge {
    padding: 2px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

.badge-tim { background: #dcfce7; color: #166534; }
.badge-individu { background: #dbeafe; color: #1e40af; }
.badge-sumber { background: #f3f4f6; color: #6b7280; }

.p-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 6px;
}

.p-meta span {
    font-size: 12px;
    color: #6b7280;
}

.p-meta i {
    margin-right: 4px;
    font-size: 11px;
}

.p-siswa {
    font-size: 12px;
    color: #374151;
}

.p-siswa i {
    margin-right: 5px;
    color: #9ca3af;
}

.more-siswa {
    color: #3b82f6;
    font-weight: 600;
}

/* Responsive */
@media (max-width: 768px) {
    .prestasi-overview { padding: 20px; }
    .stats-row { grid-template-columns: 1fr; }
    .prestasi-grid { grid-template-columns: 1fr; }
    .filter-bar { flex-direction: column; gap: 10px; }
    .jenjang-bar { justify-content: center; }
}
</style>

<script>
function applyFilter() {
    const tahun = document.getElementById('filterTahun').value;
    const params = new URLSearchParams(window.location.search);
    if (tahun) params.set('tahun', tahun); else params.delete('tahun');
    window.location.href = '{{ route("admin.prestasi.index") }}?' + params.toString();
}

function toggleYear(header) {
    header.classList.toggle('collapsed');
    const content = header.nextElementSibling;
    content.classList.toggle('hidden');
}
</script>
@endsection
