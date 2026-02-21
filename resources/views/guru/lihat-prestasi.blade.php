@extends('layouts.app')

@section('title', 'Data Prestasi - ' . $sourceNama)

@section('content')
@php
    use App\Http\Controllers\Guru\LihatPrestasiController;
@endphp

<div class="layout">
    @include('layouts.partials.sidebar-guru')

    <div class="main-content prestasi-page">
        {{-- Header --}}
        <div class="page-header-center">
            <div class="header-icon-large">
                <i class="fas fa-trophy"></i>
            </div>
            <h1>Prestasi {{ $sourceNama }}</h1>
            <p>Daftar Prestasi {{ $type == 'ekstra' ? 'Ekstrakurikuler' : 'Wali Kelas' }} · {{ $tahunPelajaran }} - {{ ucfirst($semesterAktif) }}</p>
        </div>

        {{-- Action Buttons --}}
        <div class="action-buttons-center">
            <a href="{{ route('guru.tugas-tambahan') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="{{ url('guru/input-prestasi?type=' . $type . '&id=' . $sourceId) }}" class="btn-add">
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
                    <h3>{{ count($prestasiGrouped) }}</h3>
                    <p>Total Prestasi</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $tahunPelajaran }}</h3>
                    <p>Tahun Pelajaran</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ ucfirst($semesterAktif) }}</h3>
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
                    {{ count($prestasiGrouped) }} Prestasi
                </span>
            </div>

            @if(count($prestasiGrouped) == 0)
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <h3>Belum Ada Prestasi</h3>
                <p>Belum ada prestasi yang tercatat untuk periode ini.</p>
            </div>
            @else
            <div class="prestasi-cards-grid">
                @foreach($prestasiGrouped as $prestasi)
                @php
                    $jenjangColor = LihatPrestasiController::getJenjangColor($prestasi->jenjang);
                @endphp
                <div class="prestasi-card" data-jenjang-color="{{ $jenjangColor }}">
                    <div class="prestasi-card-content">
                        <div class="medal-icon" style="background: {{ $jenjangColor }}20;">
                            <i class="fas fa-medal" style="color: {{ $jenjangColor }};"></i>
                        </div>
                        <div class="prestasi-info">
                            <h4>{{ $prestasi->nama_kompetisi }}</h4>
                            <div class="prestasi-badges">
                                <span class="badge-juara" style="background: {{ $jenjangColor }}20; color: {{ $jenjangColor }};">
                                    Juara {{ $prestasi->juara }}
                                </span>
                                <span class="badge-jenjang">
                                    {{ $prestasi->jenjang }}
                                </span>
                                @if($prestasi->tipe_peserta)
                                <span class="badge-tipe {{ $prestasi->tipe_peserta == 'Tim' ? 'badge-tim' : 'badge-individu' }}">
                                    <i class="fas {{ $prestasi->tipe_peserta == 'Tim' ? 'fa-users' : 'fa-user' }}"></i>
                                    {{ $prestasi->tipe_peserta }}
                                </span>
                                @endif
                            </div>
                            <p class="prestasi-meta">
                                <span><i class="fas fa-building"></i> {{ $prestasi->penyelenggara }}</span>
                            </p>
                            <p class="prestasi-date">
                                <i class="fas fa-calendar"></i>
                                {{ \Carbon\Carbon::parse($prestasi->tanggal_pelaksanaan)->format('d M Y') }}
                            </p>
                            <p class="prestasi-peserta">
                                <i class="fas fa-users"></i>
                                @for($i = 0; $i < count($prestasi->siswa_array); $i++)
                                    {{ $prestasi->siswa_array[$i] }}{{ $i < count($prestasi->siswa_array) - 1 ? ', ' : '' }}
                                @endfor
                            </p>
                            <div class="prestasi-actions">
                                <button type="button" class="btn-hapus"
                                        onclick="hapusPrestasi('{{ $prestasi->nama_kompetisi }}', '{{ $prestasi->juara }}', '{{ $prestasi->jenjang }}', '{{ $prestasi->tanggal_pelaksanaan }}')">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
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
.prestasi-page {
    padding: 30px;
    background: #f8f9fa;
    min-height: calc(100vh - 60px);
}

/* Header */
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

/* Action Buttons */
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

/* Stats Grid */
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

/* Prestasi Container */
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

/* Empty State */
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

/* Prestasi Cards Grid */
.prestasi-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 15px;
    padding: 20px;
}

/* Prestasi Card */
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

.prestasi-meta {
    margin: 0 0 5px 0;
    font-size: 12px;
    color: #6b7280;
}

.prestasi-meta i {
    margin-right: 5px;
}

.prestasi-date {
    margin: 0 0 5px 0;
    font-size: 12px;
    color: #6b7280;
}

.prestasi-date i {
    margin-right: 5px;
}

.prestasi-peserta {
    margin: 0 0 10px 0;
    font-size: 12px;
    color: #374151;
}

.prestasi-peserta i {
    margin-right: 5px;
    color: #9ca3af;
}

.prestasi-actions {
    display: flex;
    gap: 8px;
}

.btn-hapus {
    padding: 5px 12px;
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
    border-radius: 8px;
    font-size: 0.75rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.2s ease;
}

.btn-hapus:hover {
    background: #dc2626;
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .prestasi-page {
        padding: 20px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .prestasi-cards-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function hapusPrestasi(nama, juara, jenjang, tanggal) {
    if (!confirm('Hapus prestasi "' + nama + ' - Juara ' + juara + '"? Tindakan ini akan menghapus semua siswa terkait.')) return;

    fetch('{{ route("guru.lihat-prestasi.hapus") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({
            type: '{{ $type }}',
            source_id: {{ $sourceId }},
            nama_kompetisi: nama,
            juara: juara,
            jenjang: jenjang,
            tanggal_pelaksanaan: tanggal
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
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
</script>
@endsection
