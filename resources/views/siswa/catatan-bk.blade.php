@extends('layouts.app')

@section('title', 'Catatan Bimbingan | SISMIK')

@push('styles')
<style>
    /* Header Card */
    .bk-header-card {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border-left: 4px solid #10b981;
        margin-bottom: 20px;
    }
    .bk-header-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
    }
    .bk-header-details h3 { margin: 0; color: #1f2937; font-size: 18px; font-weight: 600; }
    .bk-header-details p { margin: 5px 0 0 0; color: #6b7280; font-size: 14px; }

    /* Stats Grid */
    .stats-grid-horizontal {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: nowrap;
        overflow-x: auto;
    }
    .stat-item-h {
        flex: 1;
        min-width: 90px;
        background: white;
        border-radius: 12px;
        padding: 15px 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }
    .stat-icon-h {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: white;
    }
    .stat-icon-h.total { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-icon-h.pending { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .stat-icon-h.process { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .stat-icon-h.done { background: linear-gradient(135deg, #22c55e, #16a34a); }
    .stat-value-h { font-size: 1.25rem; font-weight: 700; color: #1f2937; }
    .stat-label-h { font-size: 0.75rem; color: #6b7280; white-space: nowrap; }

    /* Catatan Section */
    .catatan-section {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    .section-header {
        padding: 20px 25px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
    }
    .section-header h2 { margin: 0; font-size: 1.1rem; color: #065f46; display: flex; align-items: center; gap: 10px; }
    .badge { background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }

    /* Catatan Card */
    .catatan-list { padding: 20px; display: flex; flex-direction: column; gap: 15px; }
    .catatan-card {
        background: #f8fafc;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }
    .catatan-card:hover { transform: translateY(-2px); box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1); }
    .catatan-card::before { content: ''; display: block; height: 4px; }
    .catatan-card.status-pending::before { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .catatan-card.status-process::before { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .catatan-card.status-done::before { background: linear-gradient(135deg, #22c55e, #16a34a); }
    
    .catatan-card-header {
        padding: 18px 20px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 15px;
        cursor: pointer;
    }
    .catatan-card.status-pending .catatan-card-header { background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); }
    .catatan-card.status-process .catatan-card-header { background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); }
    .catatan-card.status-done .catatan-card-header { background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); }
    
    .catatan-info { flex: 1; }
    .catatan-info h4 { margin: 0 0 8px 0; color: #1f2937; font-size: 1rem; font-weight: 600; display: flex; align-items: center; gap: 8px; }
    .catatan-meta { display: flex; flex-wrap: wrap; gap: 12px; font-size: 0.8rem; color: #6b7280; }
    .catatan-meta span { display: flex; align-items: center; gap: 5px; }
    
    .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 5px; }
    .status-badge.pending { background: #fee2e2; color: #991b1b; }
    .status-badge.process { background: #fef3c7; color: #92400e; }
    .status-badge.done { background: #dcfce7; color: #166534; }

    .catatan-card-body {
        padding: 20px;
        background: white;
        display: none;
    }
    .catatan-card.expanded .catatan-card-body { display: block; }
    .catatan-card.expanded .toggle-icon { transform: rotate(180deg); }
    
    .catatan-detail { margin-bottom: 15px; }
    .catatan-detail-label { font-weight: 600; color: #374151; font-size: 0.85rem; margin-bottom: 5px; display: flex; align-items: center; gap: 6px; }
    .catatan-detail-value { color: #4b5563; font-size: 0.9rem; line-height: 1.6; padding-left: 22px; }

    .toggle-icon { transition: transform 0.3s ease; color: #6b7280; }

    /* Empty State */
    .empty-state { text-align: center; padding: 50px 30px; }
    .empty-icon { width: 80px; height: 80px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
    .empty-icon i { font-size: 32px; color: white; }

    @media (max-width: 768px) {
        .stats-grid-horizontal { gap: 8px; }
        .stat-item-h { min-width: 75px; padding: 12px 8px; }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-siswa')

    <div class="main-content">
        <!-- Header -->
        <div class="bk-header-card">
            <div class="bk-header-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="bk-header-details">
                <h3>{{ $siswa->nama }}</h3>
                <p>Riwayat Catatan Bimbingan</p>
                @if($periodik)
                <p style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                    Periode: {{ $tahunFilter }} - {{ $semesterFilter }}
                </p>
                @endif
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid-horizontal">
            <div class="stat-item-h">
                <div class="stat-icon-h total"><i class="fas fa-clipboard-check"></i></div>
                <div class="stat-value-h">{{ $totalCatatan }}</div>
                <div class="stat-label-h">Total</div>
            </div>
            <div class="stat-item-h">
                <div class="stat-icon-h pending"><i class="fas fa-clock"></i></div>
                <div class="stat-value-h">{{ $statusStats['Belum Ditangani'] }}</div>
                <div class="stat-label-h">Pending</div>
            </div>
            <div class="stat-item-h">
                <div class="stat-icon-h process"><i class="fas fa-spinner"></i></div>
                <div class="stat-value-h">{{ $statusStats['Dalam Proses'] }}</div>
                <div class="stat-label-h">Proses</div>
            </div>
            <div class="stat-item-h">
                <div class="stat-icon-h done"><i class="fas fa-check-circle"></i></div>
                <div class="stat-value-h">{{ $statusStats['Selesai'] }}</div>
                <div class="stat-label-h">Selesai</div>
            </div>
        </div>

        <!-- Catatan Section -->
        <div class="catatan-section">
            <div class="section-header">
                <h2><i class="fas fa-clipboard-list" style="color: #10b981;"></i> Daftar Catatan Bimbingan</h2>
                <span class="badge">{{ $totalCatatan }} Catatan</span>
            </div>

            @if($catatanList->count() > 0)
            <div class="catatan-list">
                @foreach($catatanList as $catatan)
                    @php
                        $status = strtolower(trim($catatan->status ?? 'Belum Ditangani'));
                        if ($status === 'proses' || $status === 'dalam proses') {
                            $statusClass = 'process';
                            $statusLabel = 'Dalam Proses';
                        } elseif ($status === 'selesai') {
                            $statusClass = 'done';
                            $statusLabel = 'Selesai';
                        } else {
                            $statusClass = 'pending';
                            $statusLabel = 'Belum Ditangani';
                        }
                        
                        $jenisIcon = match(strtolower($catatan->jenis_bimbingan ?? '')) {
                            'pribadi' => 'fa-user',
                            'sosial' => 'fa-users',
                            'belajar' => 'fa-book',
                            'karir' => 'fa-briefcase',
                            default => 'fa-clipboard'
                        };
                    @endphp
                    <div class="catatan-card status-{{ $statusClass }}" onclick="toggleCard(this)">
                        <div class="catatan-card-header">
                            <div class="catatan-info">
                                <h4>
                                    <i class="fas {{ $jenisIcon }}" style="color: #10b981;"></i>
                                    {{ $catatan->jenis_bimbingan ?? 'Bimbingan' }}
                                </h4>
                                <div class="catatan-meta">
                                    <span><i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($catatan->tanggal)->format('d M Y') }}</span>
                                    <span><i class="fas fa-user-tie"></i> {{ $catatan->nama_guru ?? 'Guru BK' }}</span>
                                    <span><i class="fas fa-graduation-cap"></i> {{ $catatan->semester }} {{ $catatan->tahun_pelajaran }}</span>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span class="status-badge {{ $statusClass }}">
                                    <i class="fas {{ $statusClass == 'pending' ? 'fa-clock' : ($statusClass == 'process' ? 'fa-spinner' : 'fa-check-circle') }}"></i>
                                    {{ $statusLabel }}
                                </span>
                                <i class="fas fa-chevron-down toggle-icon"></i>
                            </div>
                        </div>
                        <div class="catatan-card-body">
                            <div class="catatan-detail">
                                <div class="catatan-detail-label"><i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i> Masalah</div>
                                <div class="catatan-detail-value">{!! nl2br(e($catatan->masalah ?? '-')) !!}</div>
                            </div>
                            <div class="catatan-detail">
                                <div class="catatan-detail-label"><i class="fas fa-lightbulb" style="color: #f59e0b;"></i> Penyelesaian</div>
                                <div class="catatan-detail-value">{!! nl2br(e($catatan->penyelesaian ?? '-')) !!}</div>
                            </div>
                            <div class="catatan-detail">
                                <div class="catatan-detail-label"><i class="fas fa-tasks" style="color: #10b981;"></i> Tindak Lanjut</div>
                                <div class="catatan-detail-value">{!! nl2br(e($catatan->tindak_lanjut ?? '-')) !!}</div>
                            </div>
                            @if($catatan->keterangan)
                            <div class="catatan-detail">
                                <div class="catatan-detail-label"><i class="fas fa-sticky-note" style="color: #6b7280;"></i> Keterangan</div>
                                <div class="catatan-detail-value">{!! nl2br(e($catatan->keterangan)) !!}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @else
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-clipboard-check"></i></div>
                <h3>Belum Ada Catatan Bimbingan</h3>
                <p>Saat ini belum ada catatan bimbingan yang tercatat untuk Anda<br>pada periode {{ $tahunFilter }} - {{ $semesterFilter }}.</p>
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
</script>
@endpush
