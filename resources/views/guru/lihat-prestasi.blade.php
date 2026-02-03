@extends('layouts.app')

@section('title', 'Data Prestasi - ' . $sourceNama)

@push('styles')
<style>
/* HEADER */
.prestasi-header {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    border-radius: 16px;
    padding: 25px 30px;
    margin-bottom: 25px;
    color: white;
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

.btn-back-icon {
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    color: white;
    font-size: 18px;
    transition: all 0.2s ease;
}

.btn-back-icon:hover {
    background: rgba(255,255,255,0.3);
    color: white;
}

.header-text h1 {
    margin: 0 0 8px 0;
    font-size: 1.5rem;
    font-weight: 700;
}

.header-text p {
    margin: 0;
    opacity: 0.9;
    font-size: 0.9rem;
}

.header-right {
    display: flex;
    gap: 10px;
}

.stat-box {
    background: rgba(255,255,255,0.15);
    padding: 12px 20px;
    border-radius: 12px;
    text-align: center;
}

.stat-box .value { font-size: 1.5rem; font-weight: 700; }
.stat-box .label { font-size: 0.8rem; opacity: 0.9; }

.btn-add-prestasi {
    background: rgba(255,255,255,0.2);
    padding: 12px 20px;
    border-radius: 12px;
    text-decoration: none;
    color: white;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-add-prestasi:hover {
    background: rgba(255,255,255,0.3);
    color: white;
}

/* PRESTASI CARDS */
.prestasi-grid {
    display: grid;
    gap: 20px;
}

.prestasi-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
}

.prestasi-card-header {
    padding: 20px;
    border-bottom: 1px solid #f3f4f6;
}

.card-header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 15px;
    flex-wrap: wrap;
}

.card-header-left { flex: 1; }

.card-title-row {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 10px;
}

.card-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}

.card-title-row h3 {
    margin: 0 0 4px 0;
    font-size: 1.1rem;
    color: #1f2937;
}

.card-title-row p {
    margin: 0;
    font-size: 0.85rem;
    color: #6b7280;
}

.card-header-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 8px;
}

.badge-jenjang {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    color: white;
}

.badge-juara {
    padding: 6px 14px;
    background: #fef3c7;
    color: #92400e;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 700;
}

.card-meta {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    font-size: 0.85rem;
    color: #6b7280;
    margin-top: 10px;
}

.card-meta i { color: #9ca3af; }

/* PRESTASI CARD BODY */
.prestasi-card-body {
    padding: 15px 20px;
    background: #f8fafc;
}

.body-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.body-title {
    font-size: 0.8rem;
    color: #6b7280;
    font-weight: 600;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 10px;
}

.badge-tipe {
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
}

.badge-tim { background: #dcfce7; color: #166534; }
.badge-individu { background: #dbeafe; color: #1e40af; }

.action-buttons {
    display: flex;
    gap: 8px;
}

.btn-edit, .btn-hapus {
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 0.8rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    text-decoration: none;
    transition: all 0.2s ease;
    border: 1px solid;
}

.btn-edit {
    background: #eff6ff;
    color: #2563eb;
    border-color: #bfdbfe;
}

.btn-edit:hover {
    background: #2563eb;
    color: white;
}

.btn-hapus {
    background: #fef2f2;
    color: #dc2626;
    border-color: #fecaca;
}

.btn-hapus:hover {
    background: #dc2626;
    color: white;
}

.siswa-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.siswa-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    font-size: 0.85rem;
}

.siswa-badge .nis {
    color: #9ca3af;
    font-size: 0.75rem;
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
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.empty-icon i { font-size: 32px; color: white; }
.empty-state h3 { margin: 0 0 10px 0; color: #1f2937; font-size: 1.1rem; }
.empty-state p { margin: 0 0 20px 0; color: #6b7280; max-width: 400px; margin-left: auto; margin-right: auto; }

.btn-add-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    padding: 12px 25px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-add-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    color: white;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .prestasi-header { padding: 20px 15px; }
    .header-content { flex-direction: column; align-items: stretch; }
    .header-left { flex-direction: column; text-align: center; }
    .header-text h1 { font-size: 1.2rem; }
    .header-right { justify-content: center; }
    
    .card-header-content { flex-direction: column; }
    .card-header-right { align-items: flex-start; flex-direction: row; flex-wrap: wrap; }
    
    .body-header { flex-direction: column; align-items: flex-start; gap: 10px; }
}
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-guru')
    
    <div class="main-content">
        <div class="prestasi-page">
            <!-- HEADER -->
            <div class="prestasi-header">
                <div class="header-content">
                    <div class="header-left">
                        <a href="{{ route('guru.tugas-tambahan') }}" class="btn-back-icon">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div class="header-text">
                            <h1><i class="fas fa-trophy"></i> Data Prestasi</h1>
                            <p>
                                {{ $type == 'ekstra' ? 'Ekstrakurikuler' : 'Wali Kelas' }}: 
                                <strong>{{ $sourceNama }}</strong>
                                · {{ $tahunPelajaran }} - {{ ucfirst($semesterAktif) }}
                            </p>
                        </div>
                    </div>
                    <div class="header-right">
                        <div class="stat-box">
                            <div class="value">{{ count($prestasiGrouped) }}</div>
                            <div class="label">Kategori Prestasi</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PRESTASI LIST -->
            @if(count($prestasiGrouped) > 0)
                <div class="prestasi-grid">
                    @foreach($prestasiGrouped as $prestasi)
                        @php
                            $jenjangColor = \App\Http\Controllers\Guru\LihatPrestasiController::getJenjangColor($prestasi->jenjang);
                        @endphp
                        <div class="prestasi-card" style="border-left: 5px solid {{ $jenjangColor }};">
                            <div class="prestasi-card-header">
                                <div class="card-header-content">
                                    <div class="card-header-left">
                                        <div class="card-title-row">
                                            <div class="card-icon" style="background: {{ $jenjangColor }}15;">
                                                <i class="fas fa-medal" style="color: {{ $jenjangColor }};"></i>
                                            </div>
                                            <div>
                                                <h3>{{ $prestasi->nama_kompetisi }}</h3>
                                                <p><i class="fas fa-building"></i> {{ $prestasi->penyelenggara }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-header-right">
                                        <span class="badge-jenjang" style="background: {{ $jenjangColor }};">
                                            {{ $prestasi->jenjang }}
                                        </span>
                                        <span class="badge-juara">
                                            <i class="fas fa-trophy"></i> Juara {{ $prestasi->juara }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-meta">
                                    <span><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($prestasi->tanggal_pelaksanaan)->format('d M Y') }}</span>
                                    <span><i class="fas fa-users"></i> {{ $prestasi->jumlah_siswa }} peserta</span>
                                </div>
                            </div>
                            <div class="prestasi-card-body">
                                <div class="body-header">
                                    <div class="body-title">
                                        <i class="fas fa-user-graduate"></i> Peserta yang Berprestasi
                                        @if($prestasi->tipe_peserta)
                                            <span class="badge-tipe {{ $prestasi->tipe_peserta == 'Tim' ? 'badge-tim' : 'badge-individu' }}">
                                                <i class="fas {{ $prestasi->tipe_peserta == 'Tim' ? 'fa-users' : 'fa-user' }}"></i>
                                                {{ $prestasi->tipe_peserta }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="action-buttons">
                                        <button type="button" class="btn-hapus" 
                                                onclick="hapusPrestasi('{{ $prestasi->nama_kompetisi }}', '{{ $prestasi->juara }}', '{{ $prestasi->jenjang }}', '{{ $prestasi->tanggal_pelaksanaan }}')">
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                                <div class="siswa-list">
                                    @for($i = 0; $i < count($prestasi->siswa_array); $i++)
                                        <span class="siswa-badge">
                                            <i class="fas fa-user" style="color: {{ $jenjangColor }};"></i>
                                            {{ $prestasi->siswa_array[$i] }}
                                            <span class="nis">({{ $prestasi->nis_array[$i] }})</span>
                                        </span>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
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
</div>

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
