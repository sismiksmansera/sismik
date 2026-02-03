@extends('layouts.app-guru-bk')

@section('content')
@php
    use App\Http\Controllers\GuruBK\PengaduanController;
@endphp

<div class="main-content pengaduan-page">
    {{-- Toast Notifications --}}
    @if(session('success'))
    <div id="toastNotification" class="toast-notification">
        <div class="toast-content">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button class="toast-close" onclick="hideToast()">×</button>
    </div>
    @endif
    
    @if(session('error'))
    <div id="toastNotification" class="toast-notification toast-error">
        <div class="toast-content">
            <i class="fas fa-times-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button class="toast-close" onclick="hideToast()">×</button>
    </div>
    @endif

    {{-- Header --}}
    <div class="page-header">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-comment-dots"></i>
            </div>
            <div class="header-text">
                <h2>Pengaduan Siswa</h2>
                <p>Pengaduan yang diteruskan kepada Anda</p>
            </div>
        </div>
        <div class="header-period">
            <i class="fas fa-calendar-alt"></i>
            {{ $tahun_pelajaran }} - {{ $semester }}
        </div>
    </div>

    {{-- Statistics --}}
    <div class="stats-grid">
        <div class="stat-card purple">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Total</div>
        </div>
        <div class="stat-card orange">
            <div class="stat-value">{{ $stats['menunggu'] }}</div>
            <div class="stat-label">Menunggu</div>
        </div>
        <div class="stat-card blue">
            <div class="stat-value">{{ $stats['diproses'] }}</div>
            <div class="stat-label">Diproses</div>
        </div>
        <div class="stat-card green">
            <div class="stat-value">{{ $stats['ditangani'] }}</div>
            <div class="stat-label">Ditangani</div>
        </div>
    </div>

    {{-- Pengaduan List - Collapsible Cards --}}
    <div class="pengaduan-container">
        <div class="container-header">
            <div class="header-title">
                <i class="fas fa-list"></i>
                <h5>Daftar Pengaduan</h5>
            </div>
            <span class="count-badge">{{ $stats['total'] }} Pengaduan</span>
        </div>

        @if(count($pengaduan_list) == 0)
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-inbox"></i>
            </div>
            <h4>Belum Ada Pengaduan</h4>
            <p>Tidak ada pengaduan yang diteruskan kepada Anda saat ini.</p>
        </div>
        @else
        <div class="pengaduan-cards">
            @foreach($pengaduan_list as $index => $item)
            @php
                $statusColors = PengaduanController::getStatusColor($item->status);
                $cardId = 'pengaduan_' . $item->id;
            @endphp
            <div class="pengaduan-card {{ $item->is_new ? 'is-new' : '' }}" data-card-id="{{ $cardId }}" data-id="{{ $item->id }}">
                {{-- Card Header - Collapsible Trigger --}}
                <div class="card-header" onclick="toggleCard('{{ $cardId }}')">
                    <div class="header-left">
                        <div class="number-badge {{ $item->is_new ? 'new' : '' }}">
                            {{ $index + 1 }}
                            @if($item->is_new)
                            <span class="new-badge">BARU</span>
                            @endif
                        </div>
                        <div class="pelapor-info">
                            <h4>{{ $item->nama_pelapor }}</h4>
                            <span class="rombel">{{ $item->rombel_pelapor ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="header-right">
                        <span class="kategori-badge">{{ $item->kategori }}</span>
                        <span class="status-badge" style="background: {{ $statusColors['bg'] }}; color: {{ $statusColors['color'] }};">
                            {{ $item->status }}
                        </span>
                        <i class="fas fa-chevron-down expand-icon"></i>
                    </div>
                </div>

                {{-- Card Body - Expandable Content --}}
                <div class="card-body" id="{{ $cardId }}">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label"><i class="fas fa-user"></i> Pelapor</span>
                            <span class="info-value">{{ $item->nama_pelapor }}</span>
                            <span class="info-sub">{{ $item->rombel_pelapor ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="fas fa-user-times"></i> Terlapor</span>
                            <span class="info-value">{{ $item->subyek_terlapor }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="fas fa-tag"></i> Kategori</span>
                            <span class="info-value">{{ $item->kategori }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="fas fa-calendar"></i> Tanggal</span>
                            <span class="info-value">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') }}</span>
                        </div>
                    </div>

                    <div class="deskripsi-section">
                        <div class="info-label"><i class="fas fa-align-left"></i> Deskripsi</div>
                        <p class="deskripsi-text">{{ $item->deskripsi }}</p>
                    </div>

                    @if($item->tanggapan)
                    <div class="tanggapan-section">
                        <div class="tanggapan-header">
                            <i class="fas fa-reply"></i> Tanggapan
                            @if($item->ditanggapi_oleh)
                            <span class="ditanggapi-oleh">oleh {{ $item->ditanggapi_oleh }}</span>
                            @endif
                        </div>
                        <p class="tanggapan-text">{{ $item->tanggapan }}</p>
                    </div>
                    @endif

                    <div class="card-actions">
                        <button class="btn-tanggapi" onclick="openTanggapiModal({{ $item->id }}, '{{ $item->status }}', '{{ addslashes($item->tanggapan ?? '') }}')">
                            <i class="fas fa-reply"></i> Tanggapi
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- Modal Tanggapi --}}
<div id="modalTanggapi" class="modal">
    <div class="modal-content">
        <div class="modal-header green">
            <h3><i class="fas fa-reply"></i> Tanggapi Pengaduan</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form id="formTanggapi">
            <input type="hidden" name="id" id="tanggapi_id">
            <div class="modal-body">
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="tanggapi_status" class="form-select">
                        <option value="Diproses">🔄 Diproses</option>
                        <option value="Ditangani">✅ Ditangani</option>
                        <option value="Ditutup">📁 Ditutup</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tanggapan</label>
                    <textarea name="tanggapan" id="tanggapi_tanggapan" rows="5" placeholder="Tulis tanggapan Anda..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<style>
.pengaduan-page {
    padding: 30px;
    background: #f8f9fa;
    min-height: calc(100vh - 60px);
}

/* Toast Notification */
.toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 300px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 16px 20px;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(16, 185, 129, 0.4);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    z-index: 9999;
    animation: slideIn 0.3s ease;
}

.toast-notification.toast-error {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    box-shadow: 0 10px 40px rgba(239, 68, 68, 0.4);
}

@keyframes slideIn {
    from { transform: translateX(120%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

.toast-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.toast-content i {
    font-size: 20px;
}

.toast-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

/* Header */
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 16px;
    padding: 25px 30px;
    margin-bottom: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 15px;
}

.header-icon {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.2);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
}

.header-text h2 {
    margin: 0;
    font-size: 1.5rem;
}

.header-text p {
    margin: 5px 0 0;
    opacity: 0.9;
}

.header-period {
    background: rgba(255,255,255,0.15);
    padding: 10px 20px;
    border-radius: 10px;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-bottom: 25px;
}

.stat-card {
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    color: white;
}

.stat-card.purple { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.stat-card.orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.stat-card.blue { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
.stat-card.green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }

.stat-value {
    font-size: 28px;
    font-weight: 700;
}

.stat-label {
    font-size: 0.85rem;
    opacity: 0.9;
}

/* Pengaduan Container */
.pengaduan-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
}

.container-header {
    padding: 20px 25px;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-title {
    display: flex;
    align-items: center;
    gap: 10px;
}

.header-title i {
    color: #667eea;
}

.header-title h5 {
    margin: 0;
    color: #1f2937;
}

.count-badge {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.empty-icon i {
    font-size: 32px;
    color: white;
}

.empty-state h4 {
    color: #6b7280;
    margin-bottom: 10px;
}

.empty-state p {
    color: #9ca3af;
}

/* Pengaduan Cards */
.pengaduan-cards {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.pengaduan-card {
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    transition: all 0.3s ease;
}

.pengaduan-card:hover {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-color: #667eea;
}

.pengaduan-card.is-new {
    background: #fffbeb;
    border-color: #fcd34d;
}

.pengaduan-card.expanded {
    border-color: #667eea;
}

/* Card Header */
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    cursor: pointer;
    transition: background 0.2s ease;
}

.card-header:hover {
    background: rgba(102, 126, 234, 0.05);
}

.header-left {
    display: flex;
    align-items: center;
    gap: 15px;
}

.number-badge {
    width: 35px;
    height: 35px;
    background: #667eea;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
    position: relative;
}

.number-badge.new {
    background: #ef4444;
}

.new-badge {
    position: absolute;
    top: -8px;
    right: -25px;
    background: #ef4444;
    color: white;
    font-size: 9px;
    padding: 2px 6px;
    border-radius: 10px;
    font-weight: 600;
}

.pelapor-info h4 {
    margin: 0;
    font-size: 15px;
    font-weight: 600;
    color: #1f2937;
}

.pelapor-info .rombel {
    font-size: 12px;
    color: #6b7280;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 10px;
}

.kategori-badge {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 500;
}

.status-badge {
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 600;
}

.expand-icon {
    color: #667eea;
    transition: transform 0.3s ease;
}

.pengaduan-card.expanded .expand-icon {
    transform: rotate(180deg);
}

/* Card Body - Collapsible */
.card-body {
    max-height: 0;
    overflow: hidden;
    opacity: 0;
    transition: max-height 0.4s ease, opacity 0.3s ease, padding 0.3s ease;
    padding: 0 20px;
    background: white;
}

.pengaduan-card.expanded .card-body {
    max-height: 800px;
    opacity: 1;
    padding: 20px;
    border-top: 1px solid #e5e7eb;
}

/* Info Grid */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.info-item {
    background: #f8fafc;
    padding: 12px 15px;
    border-radius: 10px;
}

.info-label {
    font-size: 11px;
    color: #6b7280;
    text-transform: uppercase;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
    margin-bottom: 5px;
}

.info-label i {
    color: #667eea;
    font-size: 10px;
}

.info-value {
    font-size: 14px;
    color: #1f2937;
    font-weight: 600;
    display: block;
}

.info-sub {
    font-size: 12px;
    color: #6b7280;
    display: block;
}

/* Deskripsi Section */
.deskripsi-section {
    background: #f8fafc;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 15px;
}

.deskripsi-text {
    margin: 10px 0 0;
    color: #4b5563;
    line-height: 1.7;
    white-space: pre-line;
}

/* Tanggapan Section */
.tanggapan-section {
    background: #d1fae5;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 15px;
}

.tanggapan-header {
    color: #065f46;
    font-weight: 600;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.ditanggapi-oleh {
    font-weight: 400;
    font-size: 12px;
    color: #047857;
}

.tanggapan-text {
    margin: 0;
    color: #047857;
    line-height: 1.6;
}

/* Card Actions */
.card-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding-top: 15px;
    border-top: 1px solid #e5e7eb;
}

.btn-tanggapi {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-tanggapi:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    border-radius: 16px;
    width: 90%;
    max-width: 500px;
    max-height: 85vh;
    overflow: hidden;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    color: white;
}

.modal-header.green {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.modal-header h3 {
    margin: 0;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.modal-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 18px;
}

.modal-body {
    padding: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #374151;
}

.form-select, .form-group textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    transition: border-color 0.2s ease;
    box-sizing: border-box;
}

.form-select:focus, .form-group textarea:focus {
    border-color: #10b981;
    outline: none;
}

.modal-footer {
    padding: 20px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.btn-secondary {
    background: #6b7280;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
}

.btn-primary {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Responsive */
@media (max-width: 768px) {
    .pengaduan-page {
        padding: 20px;
    }

    .page-header {
        flex-direction: column;
        text-align: center;
    }

    .header-content {
        flex-direction: column;
    }

    .card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }

    .header-right {
        width: 100%;
        justify-content: space-between;
    }

    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
const pengaduanData = @json($pengaduan_list);

// Toast
function hideToast() {
    const toast = document.getElementById('toastNotification');
    if (toast) {
        toast.style.transform = 'translateX(120%)';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }
}

setTimeout(() => hideToast(), 5000);

// Toggle Card
function toggleCard(cardId) {
    const card = document.querySelector(`[data-card-id="${cardId}"]`);
    card.classList.toggle('expanded');
    
    // Mark as read if new
    if (card.classList.contains('is-new') && card.classList.contains('expanded')) {
        const id = card.dataset.id;
        markAsRead(id);
        card.classList.remove('is-new');
        card.querySelector('.number-badge').classList.remove('new');
        const newBadge = card.querySelector('.new-badge');
        if (newBadge) newBadge.remove();
    }
}

// Mark as read
function markAsRead(id) {
    fetch('{{ route("guru_bk.pengaduan.mark-read") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ id: id })
    });
}

// Modal
function openTanggapiModal(id, status, tanggapan) {
    document.getElementById('tanggapi_id').value = id;
    document.getElementById('tanggapi_status').value = status;
    document.getElementById('tanggapi_tanggapan').value = tanggapan || '';
    document.getElementById('modalTanggapi').style.display = 'flex';
}

function closeModal() {
    document.getElementById('modalTanggapi').style.display = 'none';
}

// Form Submit
document.getElementById('formTanggapi').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const id = document.getElementById('tanggapi_id').value;
    const status = document.getElementById('tanggapi_status').value;
    const tanggapan = document.getElementById('tanggapi_tanggapan').value;
    
    fetch('{{ route("guru_bk.pengaduan.update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ id, status, tanggapan })
    })
    .then(r => r.json())
    .then(data => {
        closeModal();
        if (data.success) {
            showNotification('Berhasil!', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('Gagal!', data.message, true);
        }
    })
    .catch(() => showNotification('Error!', 'Terjadi kesalahan jaringan', true));
});

// Dynamic Notification
function showNotification(title, message, isError = false) {
    const existingNotif = document.querySelector('.dynamic-notif');
    if (existingNotif) existingNotif.remove();
    
    const notif = document.createElement('div');
    notif.className = 'dynamic-notif toast-notification' + (isError ? ' toast-error' : '');
    notif.innerHTML = `
        <div class="toast-content">
            <i class="fas ${isError ? 'fa-times-circle' : 'fa-check-circle'}"></i>
            <span>${message}</span>
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()">×</button>
    `;
    document.body.appendChild(notif);
    
    setTimeout(() => {
        notif.style.transform = 'translateX(120%)';
        notif.style.opacity = '0';
        setTimeout(() => notif.remove(), 300);
    }, 4000);
}

// Close modal on outside click
document.getElementById('modalTanggapi').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endsection
