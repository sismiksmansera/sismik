@extends('layouts.app')

@section('title', 'Riwayat Pengaduan | SISMIK')

@push('styles')
<style>
    /* Header Card */
    .pengaduan-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 30px;
        color: white;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    .pengaduan-header-icon {
        width: 80px;
        height: 80px;
        background: rgba(255,255,255,0.2);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
    }
    .pengaduan-header-text h1 { margin: 0 0 10px 0; font-size: 1.75rem; font-weight: 700; }
    .pengaduan-header-text p { margin: 0; opacity: 0.9; font-size: 0.95rem; }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }
    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: white;
    }
    .stat-icon.total { background: linear-gradient(135deg, #667eea, #764ba2); }
    .stat-icon.menunggu { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .stat-icon.diproses { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .stat-icon.selesai { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-info h3 { margin: 0; font-size: 1.5rem; font-weight: 700; color: #1f2937; }
    .stat-info p { margin: 3px 0 0 0; font-size: 0.85rem; color: #6b7280; }

    /* Action Buttons */
    .action-bar {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 20px;
    }
    .btn-add {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        text-decoration: none;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-add:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(102,126,234,0.3); }

    /* Pengaduan Section */
    .pengaduan-section {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .section-header {
        padding: 20px 25px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .section-header h2 { margin: 0; font-size: 1.1rem; color: #1f2937; display: flex; align-items: center; gap: 10px; }

    /* Pengaduan List */
    .pengaduan-list { padding: 20px; display: flex; flex-direction: column; gap: 15px; }
    .pengaduan-card {
        background: #f8fafc;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }
    .pengaduan-card:hover { transform: translateY(-2px); box-shadow: 0 6px 25px rgba(0,0,0,0.1); }
    .pengaduan-card::before { content: ''; display: block; height: 4px; }
    .pengaduan-card.status-menunggu::before { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .pengaduan-card.status-diproses::before { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .pengaduan-card.status-ditangani::before { background: linear-gradient(135deg, #10b981, #059669); }
    .pengaduan-card.status-ditutup::before { background: linear-gradient(135deg, #6b7280, #4b5563); }
    
    .pengaduan-card-header {
        padding: 18px 20px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 15px;
        cursor: pointer;
    }
    .pengaduan-info { flex: 1; }
    .pengaduan-info h4 { margin: 0 0 8px 0; color: #1f2937; font-size: 1rem; font-weight: 600; }
    .pengaduan-meta { display: flex; flex-wrap: wrap; gap: 12px; font-size: 0.8rem; color: #6b7280; }
    .pengaduan-meta span { display: flex; align-items: center; gap: 5px; }
    
    .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .status-badge.menunggu { background: #fef3c7; color: #92400e; }
    .status-badge.diproses { background: #dbeafe; color: #1e40af; }
    .status-badge.ditangani { background: #dcfce7; color: #166534; }
    .status-badge.ditutup { background: #f3f4f6; color: #4b5563; }

    .pengaduan-card-body { padding: 0 20px 20px; display: none; }
    .pengaduan-card.expanded .pengaduan-card-body { display: block; }
    .pengaduan-card.expanded .toggle-icon { transform: rotate(180deg); }
    
    .pengaduan-detail { margin-bottom: 12px; }
    .pengaduan-detail-label { font-weight: 600; color: #374151; font-size: 0.85rem; margin-bottom: 5px; }
    .pengaduan-detail-value { color: #4b5563; font-size: 0.9rem; line-height: 1.6; }
    
    .btn-delete {
        padding: 6px 12px;
        background: #fee2e2;
        color: #dc2626;
        border: none;
        border-radius: 6px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn-delete:hover { background: #fecaca; }

    .toggle-icon { transition: transform 0.3s ease; color: #6b7280; }

    /* Empty State */
    .empty-state { text-align: center; padding: 50px 30px; }
    .empty-icon { width: 80px; height: 80px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
    .empty-icon i { font-size: 32px; color: white; }

    /* Alert */
    .alert { padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; }
    .alert-success { background: #dcfce7; border: 1px solid #10b981; color: #166534; }
    .alert i { font-size: 20px; }

    @media (max-width: 768px) {
        /* Stats grid mobile - 4 in a row */
        .stats-grid { 
            grid-template-columns: repeat(4, 1fr) !important; 
            gap: 6px;
        }
        .stat-card {
            flex-direction: column !important;
            padding: 10px 4px !important;
            gap: 4px !important;
            text-align: center;
        }
        .stat-icon {
            width: 32px !important;
            height: 32px !important;
            font-size: 12px !important;
            margin: 0 auto;
        }
        .stat-info h3 {
            font-size: 14px !important;
        }
        .stat-info p {
            font-size: 8px !important;
        }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-siswa')

    <div class="main-content">
        <!-- Header -->
        <div class="pengaduan-header">
            <div class="pengaduan-header-icon">
                <i class="fas fa-bullhorn"></i>
            </div>
            <div class="pengaduan-header-text">
                <h1>Riwayat Pengaduan</h1>
                <p>Pantau status dan perkembangan pengaduan yang telah Anda sampaikan.</p>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon total"><i class="fas fa-clipboard-list"></i></div>
                <div class="stat-info">
                    <h3>{{ $totalPengaduan }}</h3>
                    <p>Total Pengaduan</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon menunggu"><i class="fas fa-clock"></i></div>
                <div class="stat-info">
                    <h3>{{ $statusStats['Menunggu'] }}</h3>
                    <p>Menunggu</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon diproses"><i class="fas fa-spinner"></i></div>
                <div class="stat-info">
                    <h3>{{ $statusStats['Diproses'] }}</h3>
                    <p>Diproses</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon selesai"><i class="fas fa-check-circle"></i></div>
                <div class="stat-info">
                    <h3>{{ $statusStats['Ditangani'] + $statusStats['Ditutup'] }}</h3>
                    <p>Selesai</p>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="action-bar">
            <a href="{{ route('siswa.pengaduan.create') }}" class="btn-add">
                <i class="fas fa-plus"></i> Buat Pengaduan Baru
            </a>
        </div>

        <!-- Pengaduan Section -->
        <div class="pengaduan-section">
            <div class="section-header">
                <h2><i class="fas fa-history" style="color: #667eea;"></i> Daftar Pengaduan</h2>
            </div>

            @if($pengaduanList->count() > 0)
            <div class="pengaduan-list">
                @foreach($pengaduanList as $pengaduan)
                    @php
                        $statusClass = strtolower($pengaduan->status);
                    @endphp
                    <div class="pengaduan-card status-{{ $statusClass }}" id="pengaduan-{{ $pengaduan->id }}">
                        <div class="pengaduan-card-header" onclick="toggleCard(this.parentElement)">
                            <div class="pengaduan-info">
                                <h4>{{ $pengaduan->kategori }}</h4>
                                <div class="pengaduan-meta">
                                    <span><i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($pengaduan->tanggal_kejadian)->format('d M Y') }}</span>
                                    <span><i class="fas fa-user-tag"></i> {{ $pengaduan->subyek_terlapor }}</span>
                                    @if($pengaduan->lokasi_kejadian)
                                    <span><i class="fas fa-map-marker-alt"></i> {{ $pengaduan->lokasi_kejadian }}</span>
                                    @endif
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span class="status-badge {{ $statusClass }}">{{ $pengaduan->status }}</span>
                                <i class="fas fa-chevron-down toggle-icon"></i>
                            </div>
                        </div>
                        <div class="pengaduan-card-body">
                            <div class="pengaduan-detail">
                                <div class="pengaduan-detail-label">Deskripsi</div>
                                <div class="pengaduan-detail-value">{!! nl2br(e($pengaduan->deskripsi)) !!}</div>
                            </div>
                            @if($pengaduan->tanggapan)
                            <div class="pengaduan-detail">
                                <div class="pengaduan-detail-label">Tanggapan</div>
                                <div class="pengaduan-detail-value">{!! nl2br(e($pengaduan->tanggapan)) !!}</div>
                            </div>
                            @endif
                            @if($pengaduan->status === 'Menunggu')
                            <div style="text-align: right; margin-top: 15px;">
                                <button class="btn-delete" onclick="hapusPengaduan({{ $pengaduan->id }}, '{{ $pengaduan->kategori }}')">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @else
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-clipboard-list"></i></div>
                <h3>Belum Ada Pengaduan</h3>
                <p>Anda belum pernah mengajukan pengaduan.</p>
                <a href="{{ route('siswa.pengaduan.create') }}" class="btn-add" style="margin-top: 15px;">
                    <i class="fas fa-plus"></i> Buat Pengaduan Baru
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleCard(card) {
    card.classList.toggle('expanded');
}

function hapusPengaduan(id, kategori) {
    if (confirm('Yakin ingin menghapus pengaduan "' + kategori + '"?')) {
        fetch(`{{ url('siswa/pengaduan') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('pengaduan-' + id).remove();
                alert(data.message);
            } else {
                alert(data.message);
            }
        })
        .catch(() => alert('Terjadi kesalahan.'));
    }
}
</script>
@endpush
