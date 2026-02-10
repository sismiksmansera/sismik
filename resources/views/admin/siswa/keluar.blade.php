@extends('layouts.admin')

@section('title', 'Data Siswa Keluar')

@push('styles')
<style>
    :root {
        --gray-50: #f9fafb; --gray-100: #f3f4f6; --gray-200: #e5e7eb;
        --gray-300: #d1d5db; --gray-500: #6b7280; --gray-600: #4b5563;
        --gray-700: #374151; --gray-800: #1f2937;
    }
    .page-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 24px; flex-wrap: wrap; gap: 15px;
    }
    .page-header h1 {
        font-size: 1.8rem; font-weight: 700; color: var(--gray-800); margin: 0;
    }
    .page-header p { color: var(--gray-500); margin: 4px 0 0 0; font-size: 14px; }
    .btn-back {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 20px; background: var(--gray-600); color: white;
        border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 14px;
        transition: all 0.2s;
    }
    .btn-back:hover { background: var(--gray-700); transform: translateY(-1px); color: white; text-decoration: none; }

    /* Stats */
    .stats-grid {
        display: grid; grid-template-columns: repeat(4, 1fr);
        gap: 16px; margin-bottom: 24px;
    }
    .stat-card {
        background: white; border-radius: 14px; padding: 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06); display: flex;
        align-items: center; gap: 16px; transition: transform 0.2s;
    }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-icon {
        width: 52px; height: 52px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center; font-size: 22px;
    }
    .stat-icon.total { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; }
    .stat-icon.lulus { background: linear-gradient(135deg, #10b981, #059669); color: white; }
    .stat-icon.mutasi { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
    .stat-icon.dikeluarkan { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
    .stat-info h3 { font-size: 1.6rem; font-weight: 700; color: var(--gray-800); margin: 0 0 2px; }
    .stat-info p { font-size: 13px; color: var(--gray-500); margin: 0; }

    /* Filter */
    .filter-bar {
        background: white; border-radius: 14px; padding: 18px 20px;
        margin-bottom: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        display: flex; gap: 12px; flex-wrap: wrap; align-items: center;
    }
    .filter-bar select, .filter-bar input[type="text"] {
        padding: 10px 14px; border: 2px solid var(--gray-200);
        border-radius: 10px; font-size: 14px; transition: border-color 0.2s;
    }
    .filter-bar select:focus, .filter-bar input:focus { outline: none; border-color: #6366f1; }
    .filter-bar input[type="text"] { flex: 1; min-width: 200px; }
    .btn-search {
        padding: 10px 20px; background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white; border: none; border-radius: 10px; cursor: pointer; font-weight: 600; font-size: 14px;
    }
    .btn-search:hover { transform: translateY(-1px); }
    .btn-reset {
        padding: 10px 20px; background: var(--gray-200); color: var(--gray-700);
        border-radius: 10px; text-decoration: none; font-size: 14px; font-weight: 500;
    }

    /* Table */
    .table-card {
        background: white; border-radius: 14px; padding: 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06); overflow-x: auto;
    }
    .table-card .table-info { color: var(--gray-500); font-size: 13px; margin-bottom: 14px; }
    table { width: 100%; border-collapse: collapse; }
    th {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9); padding: 12px 14px;
        text-align: left; font-weight: 600; color: var(--gray-700); font-size: 13px;
        border-bottom: 2px solid var(--gray-200);
    }
    td { padding: 12px 14px; border-bottom: 1px solid var(--gray-100); font-size: 14px; color: var(--gray-700); }
    tr:hover { background: #f9fafb; }

    /* Badges */
    .badge {
        padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
        display: inline-block;
    }
    .badge-lulus { background: #d1fae5; color: #059669; }
    .badge-mutasi { background: #fef3c7; color: #d97706; }
    .badge-dikeluarkan { background: #fee2e2; color: #dc2626; }

    /* Action buttons */
    .btn-action {
        padding: 6px 12px; border: none; border-radius: 8px; cursor: pointer;
        font-size: 12px; font-weight: 600; color: white; display: inline-flex;
        align-items: center; gap: 4px; text-decoration: none; transition: all 0.2s;
    }
    .btn-action:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
    .btn-detail { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .btn-restore { background: linear-gradient(135deg, #10b981, #059669); }

    /* Empty state */
    .empty-state {
        text-align: center; padding: 60px 20px; color: var(--gray-500);
    }
    .empty-state i { font-size: 48px; margin-bottom: 15px; display: block; opacity: 0.4; }
    .empty-state h3 { font-size: 1.2rem; color: var(--gray-600); margin-bottom: 8px; }

    /* Modal */
    .modal {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); z-index: 9999;
        justify-content: center; align-items: center;
    }
    .modal.show { display: flex; }
    .modal-dialog {
        background: white; border-radius: 16px; width: 90%;
        max-width: 700px; max-height: 90vh; overflow-y: auto;
        box-shadow: 0 25px 50px rgba(0,0,0,0.15);
    }
    .modal-header {
        padding: 20px 24px; border-radius: 16px 16px 0 0; color: white;
        display: flex; justify-content: space-between; align-items: center;
    }
    .modal-header h3 { margin: 0; font-size: 1.1rem; }
    .modal-close {
        background: rgba(255,255,255,0.2); border: none; color: white;
        width: 32px; height: 32px; border-radius: 50%; font-size: 18px;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
    }
    .modal-body { padding: 24px; }
    .modal-footer {
        padding: 16px 24px; border-top: 1px solid var(--gray-200);
        display: flex; gap: 10px; justify-content: flex-end;
    }
    .btn { padding: 10px 20px; border: none; border-radius: 10px; cursor: pointer; font-weight: 600; font-size: 14px; }
    .btn-secondary { background: var(--gray-200); color: var(--gray-700); }
    .btn-success { background: linear-gradient(135deg, #10b981, #059669); color: white; }

    /* Detail grid */
    .detail-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
    .detail-section h4 {
        margin-bottom: 12px; padding-bottom: 8px; font-size: 14px;
        border-bottom: 2px solid var(--gray-200);
    }
    .detail-section p { margin: 6px 0; font-size: 13px; color: var(--gray-600); }
    .detail-section p strong { color: var(--gray-800); }

    /* Form group */
    .form-group { margin-bottom: 16px; }
    .form-group label {
        display: block; font-weight: 600; color: var(--gray-700);
        margin-bottom: 8px; font-size: 14px;
    }
    .form-group select {
        width: 100%; padding: 12px 14px; border: 2px solid var(--gray-200);
        border-radius: 10px; font-size: 14px;
    }
    .form-group select:focus { outline: none; border-color: #10b981; }

    /* Toast */
    .toast {
        position: fixed; top: 20px; right: 20px; padding: 14px 24px;
        border-radius: 12px; color: white; font-weight: 600; z-index: 99999;
        transform: translateX(120%); transition: transform 0.3s;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .toast.show { transform: translateX(0); }
    .toast.success { background: linear-gradient(135deg, #10b981, #059669); }
    .toast.error { background: linear-gradient(135deg, #ef4444, #dc2626); }

    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .detail-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div style="padding: 20px; max-width: 1400px; margin: 0 auto;">
    <!-- Header -->
    <div class="page-header">
        <div>
            <h1><i class="fas fa-user-graduate"></i> Data Siswa Keluar</h1>
            <p>Arsip data siswa yang telah keluar dari sekolah</p>
        </div>
        <a href="{{ route('admin.siswa.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali ke Data Siswa
        </a>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon total"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <h3>{{ number_format($totalKeluar) }}</h3>
                <p>Total Siswa Keluar</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon lulus"><i class="fas fa-graduation-cap"></i></div>
            <div class="stat-info">
                <h3>{{ number_format($stats['Lulus']) }}</h3>
                <p>Lulus</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon mutasi"><i class="fas fa-exchange-alt"></i></div>
            <div class="stat-info">
                <h3>{{ number_format($stats['Mutasi']) }}</h3>
                <p>Mutasi</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon dikeluarkan"><i class="fas fa-user-times"></i></div>
            <div class="stat-info">
                <h3>{{ number_format($stats['Dikeluarkan']) }}</h3>
                <p>Dikeluarkan</p>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="filter-bar">
        <form method="GET" action="{{ route('admin.siswa-keluar.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center; width: 100%;">
            <select name="jenis">
                <option value="">Semua Jenis</option>
                <option value="Lulus" {{ $filterJenis == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                <option value="Mutasi" {{ $filterJenis == 'Mutasi' ? 'selected' : '' }}>Mutasi</option>
                <option value="Dikeluarkan" {{ $filterJenis == 'Dikeluarkan' ? 'selected' : '' }}>Dikeluarkan</option>
            </select>
            <select name="tahun">
                <option value="">Semua Tahun</option>
                @foreach($tahunList as $th)
                <option value="{{ $th }}" {{ $filterTahun == $th ? 'selected' : '' }}>{{ $th }}</option>
                @endforeach
            </select>
            <input type="text" name="search" placeholder="Cari nama, NISN, atau NIS..." value="{{ $search }}">
            <button type="submit" class="btn-search">
                <i class="fas fa-search"></i> Cari
            </button>
            @if(!empty($filterJenis) || !empty($filterTahun) || !empty($search))
            <a href="{{ route('admin.siswa-keluar.index') }}" class="btn-reset">
                <i class="fas fa-times"></i> Reset
            </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="table-card">
        <p class="table-info">Menampilkan {{ $siswaKeluarList->count() }} data</p>

        @if($siswaKeluarList->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NISN</th>
                    <th>Angkatan</th>
                    <th>Tanggal Keluar</th>
                    <th>Jenis</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($siswaKeluarList as $index => $sk)
                <tr id="row-{{ $sk->id }}">
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $sk->nama }}</strong></td>
                    <td>{{ $sk->nisn }}</td>
                    <td>{{ $sk->angkatan_masuk }}</td>
                    <td>{{ $sk->tanggal_keluar ? \Carbon\Carbon::parse($sk->tanggal_keluar)->format('d/m/Y') : '-' }}</td>
                    <td>
                        <span class="badge badge-{{ strtolower($sk->jenis_keluar) }}">
                            {{ $sk->jenis_keluar }}
                        </span>
                    </td>
                    <td>{{ $sk->keterangan ?: '-' }}</td>
                    <td>
                        <button class="btn-action btn-detail" onclick="showDetail({{ $sk->id }})">
                            <i class="fas fa-eye"></i> Detail
                        </button>
                        <button class="btn-action btn-restore" onclick="showRestoreModal({{ $sk->id }}, '{{ addslashes($sk->nama) }}')">
                            <i class="fas fa-undo"></i> Kembalikan
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>Tidak Ada Data</h3>
            <p>Belum ada siswa yang keluar atau tidak ada data yang sesuai filter.</p>
        </div>
        @endif
    </div>
</div>

<!-- Modal Detail -->
<div class="modal" id="modalDetail">
    <div class="modal-dialog">
        <div class="modal-header" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
            <h3><i class="fas fa-user"></i> Detail Siswa Keluar</h3>
            <button class="modal-close" onclick="closeModal('modalDetail')">&times;</button>
        </div>
        <div class="modal-body" id="modalDetailContent"></div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modalDetail')">
                <i class="fas fa-times"></i> Tutup
            </button>
        </div>
    </div>
</div>

<!-- Modal Kembalikan -->
<div class="modal" id="modalRestore">
    <div class="modal-dialog" style="max-width: 480px;">
        <div class="modal-header" style="background: linear-gradient(135deg, #10b981, #059669);">
            <h3><i class="fas fa-undo"></i> Kembalikan Siswa</h3>
            <button class="modal-close" onclick="closeModal('modalRestore')">&times;</button>
        </div>
        <div class="modal-body">
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 15px; margin-bottom: 20px;">
                <p style="margin: 0; font-weight: 600; color: #166534;">
                    <i class="fas fa-user"></i> <span id="restoreNama"></span>
                </p>
            </div>

            <div class="form-group">
                <label><i class="fas fa-users-class"></i> Pilih Rombel Kembali</label>
                <select id="rombelKembali">
                    <option value="">-- Tidak mengubah rombel --</option>
                    @foreach($rombelList as $rombel)
                    <option value="{{ $rombel }}">{{ $rombel }}</option>
                    @endforeach
                </select>
                <p style="color: var(--gray-500); font-size: 12px; margin-top: 8px;">Opsional: Pilih rombel jika siswa akan ditempatkan di kelas tertentu.</p>
            </div>

            <div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 10px; padding: 12px;">
                <p style="margin: 0; color: #92400e; font-size: 13px;">
                    <i class="fas fa-info-circle"></i>
                    Data siswa akan dipindahkan kembali ke tabel siswa aktif.
                </p>
            </div>

            <input type="hidden" id="restoreId">
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modalRestore')">Batal</button>
            <button class="btn btn-success" id="confirmRestore">
                <i class="fas fa-check"></i> Kembalikan Siswa
            </button>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>
@endsection

@push('scripts')
<script>
const csrfToken = '{{ csrf_token() }}';

// All siswa data for detail modal
const siswaData = @json($siswaKeluarList->keyBy('id'));

// Modal functions
function openModal(id) { document.getElementById(id).classList.add('show'); }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = 'toast show ' + type;
    setTimeout(() => { toast.classList.remove('show'); }, 3000);
}

// Detail Modal
function showDetail(id) {
    const siswa = siswaData[id];
    if (!siswa) return;

    const formatDate = (dateStr) => {
        if (!dateStr || dateStr === '0000-00-00') return '-';
        const d = new Date(dateStr);
        return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
    };

    const badgeClass = siswa.jenis_keluar ? 'badge-' + siswa.jenis_keluar.toLowerCase() : '';

    const html = `
        <div class="detail-grid">
            <div class="detail-section">
                <h4 style="color: #6366f1;"><i class="fas fa-user"></i> Data Pribadi</h4>
                <p><strong>Nama:</strong> ${siswa.nama || '-'}</p>
                <p><strong>NISN:</strong> ${siswa.nisn || '-'}</p>
                <p><strong>NIS:</strong> ${siswa.nis || '-'}</p>
                <p><strong>Jenis Kelamin:</strong> ${siswa.jk || '-'}</p>
                <p><strong>Agama:</strong> ${siswa.agama || '-'}</p>
                <p><strong>Tempat Lahir:</strong> ${siswa.tempat_lahir || '-'}</p>
                <p><strong>Tanggal Lahir:</strong> ${formatDate(siswa.tgl_lahir)}</p>
            </div>
            <div class="detail-section">
                <h4 style="color: #10b981;"><i class="fas fa-sign-out-alt"></i> Info Keluar</h4>
                <p><strong>Tanggal Keluar:</strong> ${formatDate(siswa.tanggal_keluar)}</p>
                <p><strong>Jenis Keluar:</strong> <span class="badge ${badgeClass}">${siswa.jenis_keluar || '-'}</span></p>
                <p><strong>Keterangan:</strong> ${siswa.keterangan || '-'}</p>
                <p><strong>Angkatan:</strong> ${siswa.angkatan_masuk || '-'}</p>
            </div>
            <div class="detail-section">
                <h4 style="color: #f59e0b;"><i class="fas fa-users"></i> Data Orang Tua</h4>
                <p><strong>Nama Ayah:</strong> ${siswa.nama_bapak || '-'}</p>
                <p><strong>Pekerjaan Ayah:</strong> ${siswa.pekerjaan_bapak || '-'}</p>
                <p><strong>Nama Ibu:</strong> ${siswa.nama_ibu || '-'}</p>
                <p><strong>Pekerjaan Ibu:</strong> ${siswa.pekerjaan_ibu || '-'}</p>
            </div>
            <div class="detail-section">
                <h4 style="color: #3b82f6;"><i class="fas fa-map-marker-alt"></i> Alamat</h4>
                <p><strong>Provinsi:</strong> ${siswa.provinsi || '-'}</p>
                <p><strong>Kota:</strong> ${siswa.kota || '-'}</p>
                <p><strong>Kecamatan:</strong> ${siswa.kecamatan || '-'}</p>
                <p><strong>Kelurahan:</strong> ${siswa.kelurahan || '-'}</p>
            </div>
        </div>
    `;

    document.getElementById('modalDetailContent').innerHTML = html;
    openModal('modalDetail');
}

// Restore Modal
function showRestoreModal(id, nama) {
    document.getElementById('restoreId').value = id;
    document.getElementById('restoreNama').textContent = nama;
    document.getElementById('rombelKembali').value = '';
    openModal('modalRestore');
}

// Confirm Restore
document.getElementById('confirmRestore')?.addEventListener('click', async function() {
    const id = document.getElementById('restoreId').value;
    const rombel = document.getElementById('rombelKembali').value;
    const nama = document.getElementById('restoreNama').textContent;

    if (!confirm(`Kembalikan "${nama}" ke data siswa aktif?`)) return;

    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    this.disabled = true;

    try {
        const response = await fetch('{{ route("admin.siswa-keluar.kembalikan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                siswa_keluar_id: parseInt(id),
                rombel_kembali: rombel
            })
        });

        const data = await response.json();

        if (data.success) {
            showToast(data.message);
            closeModal('modalRestore');
            // Remove row with animation
            const row = document.getElementById('row-' + id);
            if (row) {
                row.style.transition = 'all 0.3s ease';
                row.style.opacity = '0';
                row.style.transform = 'translateX(50px)';
                setTimeout(() => row.remove(), 300);
            }
        } else {
            showToast(data.message || 'Gagal mengembalikan siswa', 'error');
        }
    } catch (err) {
        showToast('Terjadi kesalahan: ' + err.message, 'error');
    }

    this.innerHTML = '<i class="fas fa-check"></i> Kembalikan Siswa';
    this.disabled = false;
});

// Close modals on backdrop click
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('mousedown', function(e) {
        if (e.target === this) closeModal(this.id);
    });
});
</script>
@endpush
