@extends('layouts.app')

@section('title', 'Prestasi | SISMIK')

@push('styles')
<style>
    /* Header Card */
    .prestasi-header {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border-left: 4px solid #f59e0b;
        margin-bottom: 25px;
    }
    .prestasi-header-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
    }
    .prestasi-header-details h3 { margin: 0; color: #1f2937; font-size: 18px; font-weight: 600; }
    .prestasi-header-details p { margin: 5px 0 0 0; color: #6b7280; font-size: 14px; }

    /* Year Section */
    .year-section { margin-bottom: 30px; }
    .year-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }
    .year-header h2 { margin: 0; font-size: 1.3rem; color: #1f2937; font-weight: 700; }
    .year-badge {
        padding: 4px 12px;
        background: #fef3c7;
        color: #92400e;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* Prestasi Cards */
    .prestasi-list { display: flex; flex-direction: column; gap: 15px; }
    .prestasi-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
        display: flex;
    }
    .prestasi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }

    .prestasi-accent { width: 4px; flex-shrink: 0; }
    .prestasi-content {
        flex: 1;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .trophy-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .prestasi-info { flex: 1; min-width: 200px; }
    .prestasi-info h4 { margin: 0 0 8px 0; font-size: 1rem; color: #1f2937; font-weight: 600; }

    .prestasi-badges { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 10px; }
    .badge-jenjang, .badge-juara, .badge-tim {
        padding: 4px 10px;
        border-radius: 15px;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .badge-juara { background: #fef3c7; color: #92400e; }
    .badge-tim {
        background: #dcfce7;
        color: #166534;
        cursor: pointer;
        border: 1px solid #bbf7d0;
        transition: all 0.2s ease;
    }
    .badge-tim:hover { background: #166534; color: white; }
    .badge-single { background: #dbeafe; color: #1e40af; }

    .prestasi-meta { display: flex; flex-wrap: wrap; gap: 12px; font-size: 0.75rem; color: #6b7280; }
    .prestasi-meta span { display: flex; align-items: center; gap: 4px; }
    .prestasi-meta i { font-size: 10px; color: #9ca3af; }

    .prestasi-source {
        padding: 8px 12px;
        background: #f3f4f6;
        border-radius: 8px;
        font-size: 0.7rem;
        color: #6b7280;
        text-align: center;
        flex-shrink: 0;
    }
    .prestasi-source i { margin-right: 4px; font-size: 10px; }

    /* Empty State */
    .empty-state {
        background: white;
        border-radius: 16px;
        padding: 60px 30px;
        text-align: center;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .empty-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
    }
    .empty-icon i { font-size: 40px; color: white; }

    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    .modal-container {
        background: white;
        border-radius: 16px;
        width: 90%;
        max-width: 450px;
        overflow: hidden;
        box-shadow: 0 25px 50px rgba(0,0,0,0.25);
    }
    .modal-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        padding: 20px 25px;
        color: white;
    }
    .modal-header h3 { margin: 0; font-size: 1.1rem; display: flex; align-items: center; gap: 10px; }
    .modal-body { padding: 25px; }
    .modal-footer {
        padding: 15px 25px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        background: #f8fafc;
    }

    .team-list { max-height: 300px; overflow-y: auto; }
    .team-member {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        border-radius: 10px;
        margin-bottom: 8px;
    }
    .team-member:nth-child(odd) { background: #f8fafc; }
    .team-avatar {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
    }
    .team-info { flex: 1; }
    .team-info .name { font-weight: 600; color: #1f2937; }
    .team-info .meta { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; margin-top: 4px; }
    .team-info .meta span { font-size: 0.8rem; color: #6b7280; }
    .team-info .rombel-badge {
        padding: 2px 8px;
        background: #dbeafe;
        color: #1e40af;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .prestasi-content { flex-direction: column; align-items: flex-start; }
        .prestasi-source { align-self: flex-start; }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-siswa')

    <div class="main-content">
        <!-- Header -->
        <div class="prestasi-header">
            <div class="prestasi-header-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="prestasi-header-details">
                <h3>{{ $siswa->nama }}</h3>
                <p>Prestasi Saya</p>
                <p style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                    Periode: {{ $tahunAktif }} - {{ $semesterAktif }}
                </p>
            </div>
        </div>

        @if($totalPrestasi > 0)
            @foreach($prestasiByYear as $year => $yearPrestasi)
            <div class="year-section">
                <div class="year-header">
                    <h2><i class="fas fa-calendar-alt" style="color: #f59e0b;"></i> Tahun {{ $year }}</h2>
                    <span class="year-badge">{{ $yearPrestasi->count() }} prestasi</span>
                </div>

                <div class="prestasi-list">
                    @foreach($yearPrestasi as $prestasi)
                    <div class="prestasi-card">
                        <div class="prestasi-accent" style="background: linear-gradient(180deg, {{ $prestasi->jenjang_colors['gradient'] }} 0%, {{ $prestasi->jenjang_colors['gradient'] }}88 100%);"></div>
                        <div class="prestasi-content">
                            <div class="trophy-icon" style="background: {{ $prestasi->juara_info['color'] }}22;">
                                <i class="fas {{ $prestasi->juara_info['icon'] }}" style="color: {{ $prestasi->juara_info['color'] }};"></i>
                            </div>
                            <div class="prestasi-info">
                                <h4>{{ $prestasi->nama_kompetisi }}</h4>
                                <div class="prestasi-badges">
                                    <span class="badge-jenjang" style="background: {{ $prestasi->jenjang_colors['bg'] }}; color: {{ $prestasi->jenjang_colors['text'] }};">
                                        {{ $prestasi->jenjang }}
                                    </span>
                                    <span class="badge-juara">
                                        <i class="fas fa-medal"></i> Juara {{ $prestasi->juara }}
                                    </span>
                                    @if(($prestasi->tipe_peserta ?? 'Single') === 'Tim' && isset($prestasi->team_members))
                                    <button type="button" class="badge-tim btn-lihat-tim" 
                                            data-team='@json($prestasi->team_members)'
                                            data-kompetisi="{{ $prestasi->nama_kompetisi }}">
                                        <i class="fas fa-users"></i> Tim ({{ count($prestasi->team_members) }})
                                    </button>
                                    @else
                                    <span class="badge-single">
                                        <i class="fas fa-user"></i> {{ $prestasi->tipe_peserta ?? 'Single' }}
                                    </span>
                                    @endif
                                </div>
                                <div class="prestasi-meta">
                                    <span><i class="fas fa-building"></i> {{ $prestasi->penyelenggara }}</span>
                                    <span><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($prestasi->tanggal_pelaksanaan)->format('d M Y') }}</span>
                                </div>
                            </div>
                            <div class="prestasi-source">
                                <i class="fas {{ $prestasi->sumber_prestasi === 'ekstrakurikuler' ? 'fa-futbol' : 'fa-chalkboard' }}"></i>
                                <span style="font-weight: 600;">{{ $prestasi->sumber_nama }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        @else
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-trophy"></i></div>
            <h3>Belum Ada Prestasi</h3>
            <p>Kamu belum memiliki data prestasi yang tercatat. Terus semangat dan raih prestasimu!</p>
        </div>
        @endif
    </div>
</div>

<!-- Modal Tim -->
<div class="modal-overlay" id="modalTim">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-users"></i> Anggota Tim</h3>
        </div>
        <div class="modal-body">
            <p style="margin: 0 0 15px 0; color: #6b7280; font-size: 0.9rem;">
                <i class="fas fa-trophy" style="color: #f59e0b;"></i> <span id="modalKompetisi"></span>
            </p>
            <div class="team-list" id="modalTimList"></div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btnTutupTim" style="padding: 10px 20px; background: #f3f4f6; color: #4b5563; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                Tutup
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.btn-lihat-tim').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const teamData = JSON.parse(this.dataset.team);
        const kompetisi = this.dataset.kompetisi;
        
        document.getElementById('modalKompetisi').textContent = kompetisi;
        
        let html = '';
        teamData.forEach((member, index) => {
            html += `
            <div class="team-member">
                <div class="team-avatar">${member.nama.charAt(0).toUpperCase()}</div>
                <div class="team-info">
                    <div class="name">${member.nama}</div>
                    <div class="meta">
                        <span>NIS: ${member.nis}</span>
                        <span class="rombel-badge"><i class="fas fa-chalkboard"></i> ${member.rombel_aktif || '-'}</span>
                    </div>
                </div>
            </div>`;
        });
        
        document.getElementById('modalTimList').innerHTML = html;
        document.getElementById('modalTim').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    });
});

function closeModalTim() {
    document.getElementById('modalTim').style.display = 'none';
    document.body.style.overflow = 'auto';
}

document.getElementById('btnTutupTim').addEventListener('click', closeModalTim);
document.getElementById('modalTim').addEventListener('click', function(e) {
    if (e.target === this) closeModalTim();
});
</script>
@endpush
