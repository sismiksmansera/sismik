@extends('layouts.app')

@section('title', 'Pengaduan Siswa | SISMIK')

@push('styles')
<style>
    /* HEADER */
    .pengaduan-header {
        text-align: center;
        margin-bottom: 25px;
    }
    .header-icon-large {
        width: 80px; height: 80px;
        background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
        border-radius: 20px;
        display: flex; align-items: center; justify-content: center;
        font-size: 36px; color: white;
        margin: 0 auto 20px;
        box-shadow: 0 8px 25px rgba(13, 148, 136, 0.3);
    }
    .page-title { font-size: 32px; font-weight: 700; margin: 0 0 10px 0; color: #0f766e; text-transform: uppercase; letter-spacing: 1px; }
    .page-subtitle { color: #6b7280; font-size: 16px; margin: 0; }

    /* STATS GRID */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .stat-card {
        background: white; padding: 25px; border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        display: flex; align-items: center; gap: 15px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 20px rgba(0,0,0,0.12); }
    .stat-icon { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; color: white; }
    .stat-icon.primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .stat-icon.success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .stat-icon.warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .stat-icon.info { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
    .stat-info h3 { margin: 0; font-size: 28px; font-weight: 700; color: #1f2937; }
    .stat-info p { margin: 5px 0 0 0; color: #6b7280; font-size: 14px; }

    /* DATA TABLE */
    .data-card { background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden; }
    .data-header { padding: 20px 25px; border-bottom: 1px solid #f3f4f6; }
    .data-header h5 { margin: 0; color: #1f2937; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead tr { background: #f8fafc; }
    .data-table th { padding: 15px; text-align: left; font-weight: 600; color: #6b7280; }
    .data-table td { padding: 15px; border-bottom: 1px solid #f3f4f6; }
    .data-table tbody tr { transition: background 0.2s; }
    .data-table tbody tr:hover { background: #f8fafc; }
    .data-table tbody tr.new-row { background: #fffbeb; }
    .data-table tbody tr.new-row:hover { background: #fef3c7; }

    /* BADGES */
    .badge-new { display: inline-block; background: #ef4444; color: white; font-size: 0.65rem; padding: 2px 6px; border-radius: 10px; margin-left: 5px; }
    .badge-status { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }

    /* BUTTONS */
    .btn-action { border: none; padding: 8px 12px; border-radius: 8px; cursor: pointer; color: white; margin-left: 5px; }
    .btn-action.info { background: #3b82f6; }
    .btn-action.success { background: #10b981; }

    /* MODAL */
    .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; overflow-y: auto; }
    .modal-container { position: relative; margin: 30px auto; max-width: 600px; background: white; border-radius: 16px; overflow: hidden; }
    .modal-container.large { max-width: 800px; }
    .modal-header { padding: 20px 25px; display: flex; justify-content: space-between; align-items: center; color: white; }
    .modal-header.primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .modal-header.success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .modal-header h3 { margin: 0; font-size: 1.2rem; }
    .modal-close { background: none; border: none; font-size: 28px; color: inherit; cursor: pointer; opacity: 0.8; }
    .modal-close:hover { opacity: 1; }
    .modal-body { padding: 25px; }
    .modal-footer { padding: 15px 25px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 10px; }

    /* FORM */
    .form-group { margin-bottom: 20px; }
    .form-label { font-weight: 600; display: block; margin-bottom: 8px; color: #374151; }
    .form-control { width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 0.95rem; }
    .form-control:focus { border-color: #10b981; outline: none; }
    .btn { padding: 10px 20px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
    .btn-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
    .btn-secondary { background: #6b7280; color: white; }

    /* EMPTY STATE */
    .empty-state { padding: 60px 30px; text-align: center; }
    .empty-icon { width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
    .empty-icon i { font-size: 32px; color: white; }

    /* DETAIL GRID */
    .detail-grid { display: grid; gap: 15px; }
    .detail-item { padding: 15px; background: #f8fafc; border-radius: 10px; }
    .detail-label { font-size: 0.75rem; color: #9ca3af; text-transform: uppercase; margin-bottom: 4px; }
    .detail-value { font-weight: 600; color: #1f2937; }
    .detail-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .tanggapan-box { margin-top: 20px; padding: 15px; background: #d1fae5; border-radius: 10px; }
    .tanggapan-box h6 { margin: 0 0 10px; color: #065f46; }
    .tanggapan-box p { margin: 0; color: #047857; }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .stats-grid { display: flex; justify-content: space-between; gap: 10px; }
        .stat-card { flex: 1; padding: 12px 8px; flex-direction: column; text-align: center; gap: 8px; min-width: 0; }
        .stat-icon { width: 35px; height: 35px; font-size: 14px; }
        .stat-info h3 { font-size: 16px; }
        .stat-info p { font-size: 10px; }
        .page-title { font-size: 24px; }
        .header-icon-large { width: 70px; height: 70px; font-size: 30px; }
        .detail-row { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-guru')

    <div class="main-content">
        <!-- HEADER -->
        <div class="pengaduan-header">
            <div class="header-icon-large"><i class="fas fa-comment-dots"></i></div>
            <h1 class="page-title">Pengaduan Siswa</h1>
            <p class="page-subtitle">Pengaduan yang diteruskan kepada Anda</p>
        </div>

        <!-- STATS CARDS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon primary"><i class="fas fa-inbox"></i></div>
                <div class="stat-info">
                    <h3>{{ $totalPengaduan }}</h3>
                    <p>Total Pengaduan</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon warning"><i class="fas fa-clock"></i></div>
                <div class="stat-info">
                    <h3>{{ $statusStats['Menunggu'] }}</h3>
                    <p>Menunggu</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon info"><i class="fas fa-spinner"></i></div>
                <div class="stat-info">
                    <h3>{{ $statusStats['Diproses'] }}</h3>
                    <p>Diproses</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon success"><i class="fas fa-check-circle"></i></div>
                <div class="stat-info">
                    <h3>{{ $statusStats['Ditangani'] }}</h3>
                    <p>Ditangani</p>
                </div>
            </div>
        </div>

        <!-- DATA TABLE -->
        <div class="data-card">
            <div class="data-header">
                <h5><i class="fas fa-list"></i> Daftar Pengaduan</h5>
            </div>

            @if(count($pengaduanList) > 0)
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pelapor</th>
                            <th>Kategori</th>
                            <th>Subyek</th>
                            <th style="text-align: center;">Status</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pengaduanList as $index => $item)
                        @php
                            $statusColors = [
                                'Menunggu' => ['bg' => '#fef3c7', 'color' => '#f59e0b'],
                                'Diproses' => ['bg' => '#dbeafe', 'color' => '#3b82f6'],
                                'Ditangani' => ['bg' => '#d1fae5', 'color' => '#10b981'],
                                'Ditutup' => ['bg' => '#e5e7eb', 'color' => '#6b7280'],
                            ];
                            $sc = $statusColors[$item->status] ?? ['bg' => '#f3f4f6', 'color' => '#6b7280'];
                        @endphp
                        <tr class="{{ $item->is_new ? 'new-row' : '' }}">
                            <td>
                                {{ $index + 1 }}
                                @if($item->is_new)
                                <span class="badge-new">BARU</span>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #1f2937;">{{ $item->nama_pelapor }}</div>
                                <div style="font-size: 0.8rem; color: #6b7280;">{{ $item->rombel_pelapor ?? '-' }}</div>
                            </td>
                            <td style="color: #374151;">{{ $item->kategori }}</td>
                            <td style="color: #374151;">{{ $item->subyek_terlapor }}</td>
                            <td style="text-align: center;">
                                <span class="badge-status" style="background: {{ $sc['bg'] }}; color: {{ $sc['color'] }};">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <button onclick="showDetail({{ $item->id }})" class="btn-action info" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="kelolaPengaduan({{ $item->id }}, '{{ $item->status }}', '{{ addslashes($item->tanggapan ?? '') }}')" class="btn-action success" title="Tanggapi">
                                    <i class="fas fa-reply"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                <h4 style="color: #6b7280; margin-bottom: 10px;">Belum Ada Pengaduan</h4>
                <p style="color: #9ca3af;">Tidak ada pengaduan yang diteruskan kepada Anda saat ini.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- MODAL DETAIL -->
<div class="modal-overlay" id="modalDetail">
    <div class="modal-container large">
        <div class="modal-header primary">
            <h3><i class="fas fa-file-alt"></i> Detail Pengaduan</h3>
            <button class="modal-close" onclick="closeModal('modalDetail')">&times;</button>
        </div>
        <div class="modal-body" id="detailContent">
            <div style="text-align: center; padding: 40px;">
                <i class="fas fa-spinner fa-spin" style="font-size: 40px; color: #667eea;"></i>
            </div>
        </div>
    </div>
</div>

<!-- MODAL KELOLA -->
<div class="modal-overlay" id="modalKelola">
    <div class="modal-container">
        <div class="modal-header success">
            <h3><i class="fas fa-reply"></i> Tanggapi Pengaduan</h3>
            <button class="modal-close" onclick="closeModal('modalKelola')">&times;</button>
        </div>
        <form id="formKelola" onsubmit="submitKelola(event)">
            <input type="hidden" name="id" id="kelola_id">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" id="kelola_status" class="form-control">
                        <option value="Diproses">🔄 Diproses</option>
                        <option value="Ditangani">✅ Ditangani</option>
                        <option value="Ditutup">📁 Ditutup</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggapan</label>
                    <textarea name="tanggapan" id="kelola_tanggapan" class="form-control" rows="5" placeholder="Tulis tanggapan Anda..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalKelola')">Batal</button>
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const pengaduanData = @json($pengaduanList);

function openModal(id) {
    document.getElementById(id).style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
    document.body.style.overflow = '';
}

document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) closeModal(this.id);
    });
});

function showDetail(id) {
    openModal('modalDetail');
    
    fetch('{{ route("guru.pengaduan.detail") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ id: id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const p = data.data;
            let tanggapanHtml = '';
            if (p.tanggapan) {
                tanggapanHtml = `
                    <div class="tanggapan-box">
                        <h6><i class="fas fa-reply"></i> Tanggapan</h6>
                        <p>${p.tanggapan}</p>
                    </div>`;
            }
            
            document.getElementById('detailContent').innerHTML = `
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Kategori</div>
                        <div class="detail-value">${p.kategori}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-item">
                            <div class="detail-label">Pelapor</div>
                            <div class="detail-value">${p.nama_pelapor}</div>
                            <div style="font-size: 0.85rem; color: #6b7280;">${p.rombel_pelapor || '-'}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Terlapor</div>
                            <div class="detail-value">${p.subyek_terlapor}</div>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Deskripsi</div>
                        <p style="margin: 10px 0 0; color: #4b5563; line-height: 1.7; white-space: pre-line;">${p.deskripsi || '-'}</p>
                    </div>
                    ${tanggapanHtml}
                </div>
            `;
        }
    });
}

function kelolaPengaduan(id, status, tanggapan) {
    document.getElementById('kelola_id').value = id;
    document.getElementById('kelola_status').value = status;
    document.getElementById('kelola_tanggapan').value = tanggapan || '';
    openModal('modalKelola');
}

function submitKelola(e) {
    e.preventDefault();
    const formData = new FormData(document.getElementById('formKelola'));
    
    fetch('{{ route("guru.pengaduan.update") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'Gagal menyimpan');
        }
    });
}
</script>
@endpush
@endsection
