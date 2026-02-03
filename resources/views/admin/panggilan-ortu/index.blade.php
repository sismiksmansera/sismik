@extends('layouts.app')

@section('title', 'Panggilan Orang Tua - ' . $siswa->nama . ' | SISMIK')

@push('styles')
<style>
    .content-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }
    .header-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .siswa-foto {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid #10b981;
        flex-shrink: 0;
    }
    .siswa-foto img { width: 100%; height: 100%; object-fit: cover; }
    .siswa-foto-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 32px;
    }
    .header-text h1 { margin: 0; color: #065f46; font-size: 24px; font-weight: 700; }
    .header-text .nama { margin: 5px 0 0 0; color: #1f2937; font-size: 16px; font-weight: 600; }
    .header-text .info { margin: 3px 0 0 0; color: #6b7280; font-size: 13px; }
    .header-actions { display: flex; gap: 10px; align-items: center; }
    
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
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
        gap: 12px;
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
    .stat-info h3 { margin: 0; font-size: 24px; font-weight: 700; color: #1f2937; }
    .stat-info p { margin: 2px 0 0 0; color: #6b7280; font-size: 12px; }
    
    /* Content Section */
    .content-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .section-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        padding: 15px 20px;
    }
    .section-header h2 {
        margin: 0;
        color: white;
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    /* Table */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }
    .data-table th {
        padding: 12px 15px;
        text-align: left;
        color: #64748b;
        font-weight: 600;
        background: #f8fafc;
    }
    .data-table th.text-center { text-align: center; }
    .data-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #f1f5f9;
    }
    .data-table td.text-center { text-align: center; }
    .data-table tbody tr:hover { background: #f8fafc; }
    
    /* Status Badges */
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .status-menunggu { background: rgba(245,158,11,0.1); color: #f59e0b; }
    .status-hadir { background: rgba(34,197,94,0.1); color: #22c55e; }
    .status-tidak-hadir { background: rgba(239,68,68,0.1); color: #ef4444; }
    .status-dijadwalkan-ulang { background: rgba(59,130,246,0.1); color: #3b82f6; }
    
    /* Action Buttons */
    .btn-action {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }
    .btn-print { background: rgba(16,185,129,0.1); color: #10b981; }
    .btn-print:hover { background: #10b981; color: white; }
    .btn-delete { background: rgba(239,68,68,0.1); color: #ef4444; }
    .btn-delete:hover { background: #ef4444; color: white; }
    .btn-edit { background: rgba(59,130,246,0.1); color: #3b82f6; }
    .btn-edit:hover { background: #3b82f6; color: white; }
    
    /* Empty State */
    .empty-state {
        padding: 60px 20px;
        text-align: center;
    }
    .empty-state i { font-size: 64px; color: #d1d5db; margin-bottom: 20px; }
    .empty-state h3 { margin: 0 0 10px 0; color: #374151; font-size: 18px; }
    .empty-state p { margin: 0 0 20px 0; color: #6b7280; font-size: 14px; }
    
    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    .modal-content {
        background: white;
        padding: 30px;
        border-radius: 12px;
        width: 400px;
        text-align: center;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <!-- Header -->
        <div class="content-header">
            <div class="header-left">
                <div class="siswa-foto">
                    @if($hasFoto)
                        <img src="{{ asset('storage/siswa/' . $siswa->foto) }}" alt="">
                    @else
                        <div class="siswa-foto-placeholder" style="background: {{ $siswa->jk == 'Laki-laki' ? 'linear-gradient(135deg, #3b82f6, #1d4ed8)' : 'linear-gradient(135deg, #ec4899, #db2777)' }};">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif
                </div>
                <div class="header-text">
                    <h1>Panggilan Orang Tua</h1>
                    <p class="nama">{{ $siswa->nama }}</p>
                    <p class="info">NISN: {{ $siswa->nisn }} | Kelas: {{ $siswa->nama_rombel ?? '-' }}</p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.panggilan-ortu.create', ['nisn' => $siswa->nisn, 'guru_bk_id' => $guruBkId]) }}" class="btn btn-primary" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="fas fa-plus"></i> Buat Surat Panggilan
                </a>
                <a href="{{ route('admin.guru-bk.siswa-bimbingan', $guruBkId) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> KEMBALI
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card" style="border-left: 4px solid #10b981;">
                <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $totalPanggilan }}</h3>
                    <p>Total Panggilan</p>
                </div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #f59e0b;">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['Menunggu'] }}</h3>
                    <p>Menunggu</p>
                </div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #22c55e;">
                <div class="stat-icon" style="background: linear-gradient(135deg, #22c55e, #16a34a);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['Hadir'] }}</h3>
                    <p>Hadir</p>
                </div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #ef4444;">
                <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['Tidak Hadir'] }}</h3>
                    <p>Tidak Hadir</p>
                </div>
            </div>
        </div>

        <!-- Daftar Panggilan -->
        <div class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-list"></i> Riwayat Panggilan Orang Tua</h2>
            </div>

            @if(count($panggilanList) == 0)
                <div class="empty-state">
                    <i class="fas fa-envelope-open"></i>
                    <h3>Belum Ada Panggilan</h3>
                    <p>Belum ada surat panggilan orang tua untuk siswa ini.</p>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="text-center" width="50">No</th>
                                <th>No. Surat</th>
                                <th>Perihal</th>
                                <th class="text-center">Tgl Panggilan</th>
                                <th>Menghadap</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($panggilanList as $index => $p)
                                @php
                                    $statusClass = 'status-menunggu';
                                    if ($p->status == 'Hadir') $statusClass = 'status-hadir';
                                    elseif ($p->status == 'Tidak Hadir') $statusClass = 'status-tidak-hadir';
                                    elseif ($p->status == 'Dijadwalkan Ulang') $statusClass = 'status-dijadwalkan-ulang';
                                @endphp
                                <tr>
                                    <td class="text-center" style="color: #64748b;">{{ $index + 1 }}</td>
                                    <td>
                                        <span style="font-family: monospace; color: #1f2937; font-weight: 500;">
                                            {{ $p->no_surat ?? '-' }}
                                        </span>
                                        <div style="font-size: 11px; color: #9ca3af; margin-top: 2px;">
                                            {{ date('d/m/Y', strtotime($p->tanggal_surat)) }}
                                        </div>
                                    </td>
                                    <td style="color: #1f2937;">{{ $p->perihal }}</td>
                                    <td class="text-center">
                                        <div style="font-weight: 600; color: #1f2937;">
                                            {{ date('d M Y', strtotime($p->tanggal_panggilan)) }}
                                        </div>
                                        <div style="font-size: 11px; color: #6b7280;">
                                            {{ $p->jam_panggilan ? date('H:i', strtotime($p->jam_panggilan)) . ' WIB' : '-' }}
                                        </div>
                                    </td>
                                    <td style="color: #4b5563;">{{ $p->menghadap_ke ?? 'Guru BK' }}</td>
                                    <td class="text-center">
                                        <span class="status-badge {{ $statusClass }}">{{ $p->status }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div style="display: flex; justify-content: center; gap: 6px;">
                                            <a href="{{ route('admin.panggilan-ortu.edit', ['id' => $p->id, 'guru_bk_id' => $guruBkId]) }}" class="btn-action btn-edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.panggilan-ortu.print', $p->id) }}" class="btn-action btn-print" title="Cetak" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <button class="btn-action btn-delete" onclick="confirmDelete({{ $p->id }})" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-content">
        <div style="font-size: 48px; color: #f59e0b; margin-bottom: 15px;">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3 style="margin: 0 0 10px 0; color: #1f2937; font-size: 20px; font-weight: 600;">Konfirmasi Hapus</h3>
        <p style="margin: 0 0 20px 0; color: #6b7280; font-size: 14px;">Apakah yakin Anda akan menghapus data panggilan ini?</p>
        <div style="display: flex; gap: 12px; justify-content: center;">
            <button onclick="closeDeleteModal()" style="background: #6b7280; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Batal</button>
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <input type="hidden" name="nisn" value="{{ $siswa->nisn }}">
                <input type="hidden" name="guru_bk_id" value="{{ $guruBkId }}">
                <button type="submit" style="background: #ef4444; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Hapus</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(id) {
        document.getElementById('deleteForm').action = '{{ url("admin/panggilan-ortu") }}/' + id;
        document.getElementById('deleteModal').style.display = 'flex';
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }
    
    document.getElementById('deleteModal').addEventListener('click', (e) => {
        if (e.target.id === 'deleteModal') closeDeleteModal();
    });
</script>
@endpush
