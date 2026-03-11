@extends('layouts.app')

@section('title', 'Data Prestasi - ' . $sumberInfo['title'] . ' | SISMIK')

@section('content')
@php
    use App\Http\Controllers\Admin\PrestasiController;
@endphp

<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content prestasi-page">
        {{-- Header --}}
        <div class="page-header-center">
            <div class="header-icon-large">
                <i class="fas fa-trophy"></i>
            </div>
            <h1>Prestasi {{ $sumberInfo['title'] }}</h1>
            <p>Daftar Prestasi {{ $type == 'ekstra' ? 'Ekstrakurikuler' : ($type == 'ajang_talenta' ? 'Ajang Talenta' : 'Rombel') }} · {{ $sumberInfo['tahun_pelajaran'] }} - {{ $sumberInfo['semester'] }}</p>
        </div>

        {{-- Action Buttons --}}
        <div class="action-buttons-center">
            <a href="{{ $backUrl }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="{{ url('admin/prestasi/input?type=' . $type . '&id=' . $id . (isset($defaultKompetisi) && $defaultKompetisi ? '&default_kompetisi=' . urlencode($defaultKompetisi) : '') . (isset($defaultPenyelenggara) && $defaultPenyelenggara ? '&default_penyelenggara=' . urlencode($defaultPenyelenggara) : '')) }}" class="btn-add">
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
                    <h3>{{ count($prestasiList) }}</h3>
                    <p>Total Prestasi</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $sumberInfo['tahun_pelajaran'] }}</h3>
                    <p>Tahun Pelajaran</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $sumberInfo['semester'] }}</h3>
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
                    {{ count($prestasiList) }} Prestasi
                </span>
            </div>

            @if(count($prestasiList) == 0)
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <h3>Belum Ada Prestasi</h3>
                <p>Belum ada prestasi yang tercatat untuk periode ini.</p>
            </div>
            @else
            <div class="prestasi-cards-grid">
                @foreach($prestasiList as $prestasi)
                @php
                    $jenjangColor = PrestasiController::getJenjangColor($prestasi['jenjang']);
                @endphp
                <div class="prestasi-card">
                    <div class="prestasi-card-content">
                        <div class="medal-icon" style="background: {{ $jenjangColor }}20;">
                            <i class="fas fa-medal" style="color: {{ $jenjangColor }};"></i>
                        </div>
                        <div class="prestasi-info">
                            <h4>{{ $prestasi['nama_kompetisi'] }}</h4>
                            <div class="prestasi-badges">
                                <span class="badge-juara" style="background: {{ $jenjangColor }}20; color: {{ $jenjangColor }};">
                                    Juara {{ $prestasi['juara'] }}
                                </span>
                                <span class="badge-jenjang">
                                    {{ $prestasi['jenjang'] }}
                                </span>
                                @if(!empty($prestasi['tipe_peserta']))
                                <span class="badge-tipe {{ $prestasi['tipe_peserta'] == 'Tim' ? 'badge-tim' : 'badge-individu' }}">
                                    <i class="fas {{ $prestasi['tipe_peserta'] == 'Tim' ? 'fa-users' : 'fa-user' }}"></i>
                                    {{ $prestasi['tipe_peserta'] }}
                                </span>
                                @endif
                            </div>
                            <p class="prestasi-date">
                                <i class="fas fa-calendar"></i>
                                {{ date('d M Y', strtotime($prestasi['tanggal_pelaksanaan'])) }}
                            </p>
                            <p class="prestasi-peserta">
                                <i class="fas fa-users"></i>
                                @for($i = 0; $i < count($prestasi['siswa_array']); $i++)
                                    {{ $prestasi['siswa_array'][$i] }}{{ $i < count($prestasi['siswa_array']) - 1 ? ', ' : '' }}
                                @endfor
                            </p>
                            <div class="prestasi-actions">
                                <button type="button" class="btn-edit"
                                        onclick="openEditModal('{{ addslashes($prestasi['nama_kompetisi']) }}', '{{ $prestasi['juara'] }}', '{{ $prestasi['jenjang'] }}', '{{ $prestasi['penyelenggara'] ?? '' }}', '{{ $prestasi['tanggal_pelaksanaan'] }}', '{{ $prestasi['tipe_peserta'] ?? 'Single' }}')">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button type="button" class="btn-hapus"
                                        onclick="hapusPrestasi('{{ addslashes($prestasi['nama_kompetisi']) }}', '{{ $prestasi['juara'] }}', '{{ $prestasi['jenjang'] }}', '{{ $prestasi['tanggal_pelaksanaan'] }}')">
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

{{-- Edit Modal --}}
<div class="modal-overlay" id="editModal" style="display:none;">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Prestasi</h3>
            <button type="button" class="modal-close" onclick="closeEditModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Nama Kompetisi</label>
                <input type="text" id="edit_nama_kompetisi" class="form-input" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Juara</label>
                    <select id="edit_juara" class="form-input">
                        <option value="Juara 1">Juara 1</option>
                        <option value="Juara 2">Juara 2</option>
                        <option value="Juara 3">Juara 3</option>
                        <option value="Harapan 1">Harapan 1</option>
                        <option value="Harapan 2">Harapan 2</option>
                        <option value="Harapan 3">Harapan 3</option>
                        <option value="Finalis">Finalis</option>
                        <option value="Semifinalis">Semifinalis</option>
                        <option value="Medali Emas">Medali Emas</option>
                        <option value="Medali Perak">Medali Perak</option>
                        <option value="Medali Perunggu">Medali Perunggu</option>
                        <option value="Best Speaker">Best Speaker</option>
                        <option value="Best Delegate">Best Delegate</option>
                        <option value="Honorable Mention">Honorable Mention</option>
                        <option value="Peserta">Peserta</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jenjang</label>
                    <select id="edit_jenjang" class="form-input">
                        <option value="Kelas">Kelas</option>
                        <option value="Sekolah">Sekolah</option>
                        <option value="Kecamatan">Kecamatan</option>
                        <option value="Kabupaten">Kabupaten</option>
                        <option value="Provinsi">Provinsi</option>
                        <option value="Nasional">Nasional</option>
                        <option value="Internasional">Internasional</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Penyelenggara</label>
                    <input type="text" id="edit_penyelenggara" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" id="edit_tanggal" class="form-input" required>
                </div>
            </div>
            <div class="form-group">
                <label>Tipe Peserta</label>
                <select id="edit_tipe_peserta" class="form-input">
                    <option value="Single">Individu</option>
                    <option value="Tim">Tim</option>
                </select>
            </div>
            <input type="hidden" id="edit_orig_nama">
            <input type="hidden" id="edit_orig_juara">
            <input type="hidden" id="edit_orig_jenjang">
            <input type="hidden" id="edit_orig_tanggal">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeEditModal()">Batal</button>
            <button type="button" class="btn-save" onclick="simpanEdit()">
                <i class="fas fa-save"></i> Simpan
            </button>
        </div>
    </div>
</div>

<style>
.prestasi-page {
    padding: 30px;
    background: #f8f9fa;
    min-height: calc(100vh - 60px);
}

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

.prestasi-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 15px;
    padding: 20px;
}

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

/* Action Buttons on Cards */
.prestasi-actions {
    display: flex;
    gap: 8px;
    margin-top: 10px;
}

.btn-edit {
    padding: 5px 12px;
    background: #eff6ff;
    color: #2563eb;
    border: 1px solid #bfdbfe;
    border-radius: 8px;
    font-size: 0.75rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.2s ease;
}

.btn-edit:hover {
    background: #2563eb;
    color: white;
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

/* Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 9990;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(4px);
}

.modal-container {
    background: white;
    border-radius: 16px;
    width: 90%;
    max-width: 550px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 25px 60px rgba(0,0,0,0.3);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 25px;
    border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 { margin: 0; font-size: 18px; color: #1f2937; }
.modal-header h3 i { color: #3b82f6; margin-right: 8px; }

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    color: #9ca3af;
    cursor: pointer;
    padding: 5px;
}

.modal-body {
    padding: 25px;
}

.modal-body .form-group {
    margin-bottom: 15px;
}

.modal-body label {
    display: block;
    font-weight: 600;
    font-size: 13px;
    color: #374151;
    margin-bottom: 6px;
}

.modal-body .form-input {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    font-size: 14px;
    transition: border-color 0.2s;
    box-sizing: border-box;
}

.modal-body .form-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 15px 25px;
    border-top: 1px solid #e5e7eb;
}

.btn-cancel {
    padding: 10px 20px;
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
}

.btn-save {
    padding: 10px 20px;
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-save:hover {
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
}

@media (max-width: 768px) {
    .prestasi-page { padding: 20px; }
    .stats-grid { grid-template-columns: 1fr; }
    .prestasi-cards-grid { grid-template-columns: 1fr; }
    .form-row { grid-template-columns: 1fr; }
}
</style>

<script>
function openEditModal(nama, juara, jenjang, penyelenggara, tanggal, tipePeserta) {
    document.getElementById('edit_nama_kompetisi').value = nama;
    document.getElementById('edit_juara').value = juara;
    document.getElementById('edit_jenjang').value = jenjang;
    document.getElementById('edit_penyelenggara').value = penyelenggara;
    document.getElementById('edit_tanggal').value = tanggal;
    document.getElementById('edit_tipe_peserta').value = tipePeserta;

    document.getElementById('edit_orig_nama').value = nama;
    document.getElementById('edit_orig_juara').value = juara;
    document.getElementById('edit_orig_jenjang').value = jenjang;
    document.getElementById('edit_orig_tanggal').value = tanggal;

    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

function simpanEdit() {
    const data = {
        type: '{{ $type }}',
        source_id: {{ $id }},
        orig_nama_kompetisi: document.getElementById('edit_orig_nama').value,
        orig_juara: document.getElementById('edit_orig_juara').value,
        orig_jenjang: document.getElementById('edit_orig_jenjang').value,
        orig_tanggal: document.getElementById('edit_orig_tanggal').value,
        nama_kompetisi: document.getElementById('edit_nama_kompetisi').value,
        juara: document.getElementById('edit_juara').value,
        jenjang: document.getElementById('edit_jenjang').value,
        penyelenggara: document.getElementById('edit_penyelenggara').value,
        tanggal_pelaksanaan: document.getElementById('edit_tanggal').value,
        tipe_peserta: document.getElementById('edit_tipe_peserta').value,
    };

    fetch('{{ route("admin.prestasi.update") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            showToast(res.message, 'success');
            closeEditModal();
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(res.message, 'error');
        }
    });
}

function hapusPrestasi(nama, juara, jenjang, tanggal) {
    if (!confirm('Hapus prestasi "' + nama + ' - ' + juara + '"? Tindakan ini akan menghapus semua siswa terkait.')) return;

    fetch('{{ route("admin.prestasi.hapus") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({
            type: '{{ $type }}',
            source_id: {{ $id }},
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
